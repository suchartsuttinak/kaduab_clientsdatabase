<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;

use Carbon\Carbon;

use App\Models\Client;
use App\Models\Refer;
use App\Models\Absent;
use App\Models\Medical;
use App\Models\Psychiatric;
use App\Models\User;

use App\Services\AuditLogger;
use App\Services\AuditMutationContext;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Password policy
        |--------------------------------------------------------------------------
        | ใช้มาตรฐานเดียวกันกับการตั้ง/รีเซ็ตรหัสผ่านทุกจุดของระบบ
        | ไม่กระทบรหัสผ่านเดิมจนกว่าผู้ใช้จะเปลี่ยนรหัสผ่านครั้งถัดไป
        */
        Password::defaults(static fn () => Password::min(10)->letters()->numbers());

        /*
        |--------------------------------------------------------------------------
        | Mutation context for Audit Log
        |--------------------------------------------------------------------------
        | เก็บเฉพาะ Model/ID/client_id และชื่อ field ที่เปลี่ยนในหน่วยความจำ
        | ของ Request ปัจจุบัน เพื่อให้ Audit Log รู้เป้าหมายโดยไม่แก้ Controller
        | ของแต่ละฟอร์ม และไม่เก็บค่าข้อมูลเดิม/ใหม่
        */
        foreach ([
            'eloquent.created: *' => 'create',
            'eloquent.updated: *' => 'update',
            'eloquent.deleted: *' => 'delete',
        ] as $eventName => $auditAction) {
            Event::listen($eventName, function (string $event, array $payload) use ($auditAction): void {
                $model = $payload[0] ?? null;

                if ($model instanceof Model) {
                    AuditMutationContext::capture($auditAction, $model);
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Authentication Audit Log
        |--------------------------------------------------------------------------
        | บันทึกเหตุการณ์เกี่ยวกับการเข้าสู่ระบบ
        |
        | - LOGIN        : เข้าสู่ระบบสำเร็จ
        | - LOGIN_FAILED : เข้าสู่ระบบไม่สำเร็จ
        | - LOGOUT       : ออกจากระบบ
        |
        | ไม่บันทึก Password / Username / Email / Credentials
        |--------------------------------------------------------------------------
        */

        Event::listen(Login::class, function (Login $event) {
            AuditLogger::log(
                action: 'LOGIN',
                module: 'authentication',
                result: 'success',
                statusCode: 200,
                metadata: [
                    'guard' => $event->guard,
                ],
                userId: (int) $event->user->getAuthIdentifier()
            );
        });

        Event::listen(Failed::class, function (Failed $event) {
            AuditLogger::log(
                action: 'LOGIN_FAILED',
                module: 'authentication',
                result: 'failed',
                statusCode: 401,
                metadata: [
                    'guard' => $event->guard,
                ],
                userId: $event->user
                    ? (int) $event->user->getAuthIdentifier()
                    : null
            );
        });

        /*
         * เมื่อถูก Rate Limit จากการพยายาม Login หลายครั้ง
         * ไม่บันทึก email / password / credentials ใด ๆ
         */
        Event::listen(Lockout::class, function (Lockout $event) {
            /*
             * Lockout เกิดก่อนผู้ใช้ผ่านการยืนยันตัวตน จึงไม่มี auth()->user()
             * หากอีเมลที่พยายาม Login ตรงกับบัญชีจริง ให้ผูกเฉพาะ user_id
             * เพื่อให้ตรวจสอบเหตุการณ์ย้อนหลังได้ โดยไม่บันทึกอีเมลลง Audit Log
             */
            $attemptedEmail = trim((string) $event->request->input('email', ''));
            $lockedUserId = null;

            if ($attemptedEmail !== '') {
                $resolvedUserId = User::query()
                    ->where('email', $attemptedEmail)
                    ->value('id');

                $lockedUserId = $resolvedUserId !== null
                    ? (int) $resolvedUserId
                    : null;
            }

            AuditLogger::log(
                action: 'LOGIN_FAILED',
                module: 'authentication',
                result: 'failed',
                statusCode: 429,
                metadata: [
                    'reason' => 'rate_limited',
                ],
                userId: $lockedUserId
            );
        });

        /*
         * การรีเซ็ตรหัสผ่านผ่าน Forgot Password
         * เก็บเฉพาะว่าเกิดเหตุการณ์ด้านความปลอดภัยขึ้น
         * ไม่เก็บรหัสผ่าน / token / email
         */
        Event::listen(PasswordReset::class, function (PasswordReset $event) {
            AuditLogger::log(
                action: 'UPDATE',
                module: 'account_security',
                result: 'success',
                statusCode: 200,
                metadata: [
                    'security_event' => 'password_reset',
                ],
                userId: (int) $event->user->getAuthIdentifier()
            );
        });

        Event::listen(Logout::class, function (Logout $event) {
            AuditLogger::log(
                action: 'LOGOUT',
                module: 'authentication',
                result: 'success',
                statusCode: 200,
                metadata: [
                    'guard' => $event->guard,
                ],
                userId: $event->user
                    ? (int) $event->user->getAuthIdentifier()
                    : null
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Header Notifications
        |--------------------------------------------------------------------------
        | ระบบแจ้งเตือนเดิมของโครงการ
        |--------------------------------------------------------------------------
        */

        View::composer('admin.body.header', function ($view) {
            $today = Carbon::today();

            $pendingRefers = 0;
            $todayAbsents = 0;
            $todayIllnesses = 0;
            $upcomingAppointments = 0;
            $overdueAppointments = 0;

            $pendingReferItems = collect();
            $todayAbsentItems = collect();
            $todayIllnessItems = collect();
            $upcomingAppointmentItems = collect();

            if (auth()->check()) {
                $user = auth()->user();
                $authorizedClientIds = Client::forUser($user)->pluck('id');
                $canRefer = $user->canViewForm('welfare_discharge');
                $canAbsence = $user->canViewForm('education_absence');
                $canMedical = $user->canViewForm('health_medical');
                $canPsychiatric = $user->canViewForm('health_psychiatric');

                /*
                |--------------------------------------------------------------------------
                | รายการส่งต่อที่รออนุมัติ
                |--------------------------------------------------------------------------
                */
                if (
                    $canRefer
                    && class_exists(Refer::class)
                    && Schema::hasTable('refers')
                ) {
                    $pendingReferItems = Refer::with('client')
                        ->where('approve_status', 'pending')
                        ->whereIn('client_id', $authorizedClientIds)
                        ->whereHas('client', function ($query) {
                            $query->where(
                                'release_status',
                                'pending_refer'
                            );
                        })
                        ->latest()
                        ->limit(10)
                        ->get();

                    $pendingRefers = $pendingReferItems->count();
                }

                /*
                |--------------------------------------------------------------------------
                | ขาดเรียนวันนี้
                |--------------------------------------------------------------------------
                */
                if (
                    $canAbsence
                    && class_exists(Absent::class)
                    && Schema::hasTable('absents')
                    && Schema::hasColumn('absents', 'absent_date')
                ) {
                    $todayAbsentItems = Absent::with('client')
                        ->whereDate('absent_date', $today)
                        ->whereIn('client_id', $authorizedClientIds)
                        ->latest()
                        ->limit(10)
                        ->get();

                    $todayAbsents = $todayAbsentItems->count();
                }

                /*
                |--------------------------------------------------------------------------
                | เจ็บป่วยวันนี้
                |--------------------------------------------------------------------------
                */
                if (
                    $canMedical
                    && class_exists(Medical::class)
                    && Schema::hasTable('medicals')
                    && Schema::hasColumn('medicals', 'medical_date')
                ) {
                    $todayIllnessItems = Medical::with('client')
                        ->whereDate('medical_date', $today)
                        ->whereIn('client_id', $authorizedClientIds)
                        ->latest()
                        ->limit(10)
                        ->get();

                    $todayIllnesses = $todayIllnessItems->count();
                }

                /*
                |--------------------------------------------------------------------------
                | นัดพบแพทย์ภายใน 7 วัน
                |--------------------------------------------------------------------------
                */
                if (
                    $canMedical
                    && class_exists(Medical::class)
                    && Schema::hasTable('medicals')
                    && Schema::hasColumn('medicals', 'appointment_date')
                ) {
                    $medicalUpcomingItems = Medical::with('client')
                        ->whereDate('appointment_date', '>=', $today)
                        ->whereDate(
                            'appointment_date',
                            '<=',
                            $today->copy()->addDays(7)
                        )
                        ->whereIn('client_id', $authorizedClientIds)
                        ->orderBy('appointment_date')
                        ->limit(10)
                        ->get();

                    $upcomingAppointmentItems =
                        $upcomingAppointmentItems->merge(
                            $medicalUpcomingItems
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | นัดจิตเวชภายใน 7 วัน
                |--------------------------------------------------------------------------
                */
                if (
                    $canPsychiatric
                    && class_exists(Psychiatric::class)
                    && Schema::hasTable('psychiatrics')
                    && Schema::hasColumn('psychiatrics', 'appoin_date')
                ) {
                    $psychiatricUpcomingItems =
                        Psychiatric::with('client')
                            ->whereDate('appoin_date', '>=', $today)
                            ->whereDate(
                                'appoin_date',
                                '<=',
                                $today->copy()->addDays(7)
                            )
                            ->whereIn(
                                'client_id',
                                $authorizedClientIds
                            )
                            ->orderBy('appoin_date')
                            ->limit(10)
                            ->get();

                    $upcomingAppointmentItems =
                        $upcomingAppointmentItems->merge(
                            $psychiatricUpcomingItems
                        );
                }

                /*
                 * เรียงนัดพบแพทย์ทั้งหมด
                 * ทั้ง Medical และ Psychiatric
                 * ให้วันที่ใกล้สุดอยู่ด้านบน
                 */
                $upcomingAppointmentItems =
                    $upcomingAppointmentItems
                        ->sortBy(function ($item) {
                            return $item->appointment_date
                                ?? $item->appoin_date
                                ?? null;
                        })
                        ->values();

                $upcomingAppointments =
                    $upcomingAppointmentItems->count();
            }

            /*
            |--------------------------------------------------------------------------
            | จำนวนแจ้งเตือนทั้งหมด
            |--------------------------------------------------------------------------
            */

            $notificationCount =
                $pendingRefers
                + $todayAbsents
                + $todayIllnesses
                + $upcomingAppointments;

            $view->with(compact(
                'pendingRefers',
                'todayAbsents',
                'todayIllnesses',
                'upcomingAppointments',
                'overdueAppointments',
                'notificationCount',
                'pendingReferItems',
                'todayAbsentItems',
                'todayIllnessItems',
                'upcomingAppointmentItems'
            ));
        });
    }
}