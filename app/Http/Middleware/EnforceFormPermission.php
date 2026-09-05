<?php

namespace App\Http\Middleware;

use App\Services\AuditLogger;
use App\Services\AuditMutationContext;
use App\Support\FormPermissionUi;
use App\Support\FormPermissionMenu;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class EnforceFormPermission
{
    /**
     * ตรวจสอบสิทธิ์รายฟอร์มของ Route
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        /*
        |--------------------------------------------------------------------------
        | Route ไม่มีชื่อ
        |--------------------------------------------------------------------------
        */

        if (!$routeName) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | ค้นหากฎ Permission
        |--------------------------------------------------------------------------
        */

        $rule = FormPermissionUi::findRule($routeName);

        if ($rule === null) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | ตรวจผู้ใช้งาน
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Route ที่มี Permission Rule ต้องมีผู้ใช้งานเสมอ
        |--------------------------------------------------------------------------
        |
        | เดิม middleware นี้ปล่อย request ผ่านเมื่อยังไม่ได้ login โดยคาดว่า
        | แต่ละ route จะมี auth middleware ของตัวเองทั้งหมด ซึ่งในโปรเจกต์จริง
        | มี route บางไฟล์ที่อาศัย Form Permission เป็นด่านหลัก
        |
        | การบังคับ authentication ตรงนี้ปิดช่องว่างดังกล่าวโดยไม่เปลี่ยน
        | logic สิทธิ์ของผู้ใช้ที่ login แล้ว
        |
        */
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return redirect()->guest(route('login'));
        }

        if (!method_exists($user, 'hasFormPermission')) {
            abort(403, 'ไม่สามารถตรวจสอบสิทธิ์ของบัญชีนี้ได้');
        }

        /*
        |--------------------------------------------------------------------------
        | Permission Key และ Action
        |--------------------------------------------------------------------------
        */

        $permissionKeys = $this->permissionKeys($rule);

        $action = (string) (
            $rule['action'] ?? 'view'
        );

        $allowed = $this->isAllowed(
            $user,
            $permissionKeys,
            $action
        );

        // UNIFIED_ACCESS_SCOPE_V5:
        // รายการย่อยใน “ประเภท / หมวดหมู่” ต้องเปิดสิทธิ์เมนูหลักด้วย
        // ป้องกันกรณีซ่อน Sidebar แล้วผู้ใช้เข้ารายการย่อยตรงจาก URL ได้
        $usesMasterChildPermission = collect($permissionKeys)->contains(
            static fn (string $key): bool => str_starts_with($key, 'master_')
                && $key !== 'master_data_menu'
        );

        if (
            $allowed
            && $usesMasterChildPermission
            && !$user->canViewForm('master_data_menu')
        ) {
            $allowed = false;
        }

        /*
        |--------------------------------------------------------------------------
        | มีสิทธิ์ใช้งาน
        |--------------------------------------------------------------------------
        |
        | ปล่อย Request ไปยัง Controller ตามปกติ
        | หลัง Controller ทำงานแล้วจึงบันทึก CREATE / UPDATE / DELETE
        |
        */

        if ($allowed) {
            try {
                $response = $next($request);

                $this->auditMutation(
                    request: $request,
                    response: $response,
                    user: $user,
                    permissionKeys: $permissionKeys,
                    action: $action
                );

                return $response;

            } catch (Throwable $e) {

                /*
                |--------------------------------------------------------------------------
                | Operation เกิด Exception
                |--------------------------------------------------------------------------
                |
                | บันทึกว่าไม่สำเร็จ แต่ไม่เก็บ Exception message,
                | stack trace หรือ Request payload ลง Audit Log
                |
                */

                $this->auditMutation(
                    request: $request,
                    response: null,
                    user: $user,
                    permissionKeys: $permissionKeys,
                    action: $action,
                    failedByException: true,
                    exception: $e
                );

                throw $e;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Read-only mode
        |--------------------------------------------------------------------------
        |
        | Route บางหน้าถูกกำหนด action=update
        | แต่ GET/HEAD เป็นเพียงการเปิดดูแบบฟอร์ม
        |
        | หากผู้ใช้มี view แต่ไม่มี update
        | ยังสามารถเปิดดูข้อมูลเดิมได้ในโหมดอ่านอย่างเดียว
        |
        */

        if (
            $action === 'update'
            && $request->isMethodSafe()
            && $this->isAllowed(
                $user,
                $permissionKeys,
                'view'
            )
            && (
                !$usesMasterChildPermission
                || $user->canViewForm('master_data_menu')
            )
        ) {
            $request->attributes->set(
                'form_permission_readonly',
                true
            );

            $request->attributes->set(
                'form_permission_keys',
                $permissionKeys
            );

            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | ตรวจกรณี Dashboard
        |--------------------------------------------------------------------------
        */

        $isDashboardRedirect =
            $routeName === 'dashboard'
            && $request->isMethodSafe();

        /*
        |--------------------------------------------------------------------------
        | Audit Log : ACCESS_DENIED
        |--------------------------------------------------------------------------
        |
        | บันทึกเฉพาะข้อมูลที่จำเป็นต่อการตรวจสอบความปลอดภัย
        |
        | ไม่บันทึก:
        | - Request payload
        | - Password
        | - Token
        | - Cookie / Session
        | - ข้อมูลสุขภาพ
        | - เนื้อหาข้อมูลส่วนบุคคล
        |
        */

        AuditLogger::log(
            action: 'ACCESS_DENIED',
            module: 'authorization',
            result: 'denied',
            statusCode: $isDashboardRedirect
                ? 302
                : 403,
            metadata: [
                'required_action' => $action,

                'permission_keys' => implode(
                    ',',
                    $permissionKeys
                ),
            ],
            userId: (int) $user->getAuthIdentifier()
        );

        /*
        |--------------------------------------------------------------------------
        | Dashboard ไม่มีสิทธิ์
        |--------------------------------------------------------------------------
        |
        | ระบบเดิมกำหนดให้กลับไปหน้าทะเบียนผู้รับบริการ
        | แทนการแสดงหน้า 403
        |
        */

        if ($isDashboardRedirect) {
            $fallbackRoute = FormPermissionMenu::firstAccessibleRouteName($user);

            return redirect()
                ->route($fallbackRoute)
                ->with(
                    'info',
                    'บัญชีนี้ไม่ได้รับสิทธิ์เข้าหน้า Dashboard ระบบจึงนำไปยังหน้าที่ได้รับสิทธิ์'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Permission Denied
        |--------------------------------------------------------------------------
        */

        $message = $this->deniedMessage(
            $action
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'action' => $action,
            ], 403);
        }

        abort(
            403,
            $message
        );
    }


    /**
     * คืนค่า Permission Key ของ Rule
     *
     * @return list<string>
     */
    private function permissionKeys(array $rule): array
    {
        $keys =
            $rule['permissions']
            ??
            $rule['permission']
            ??
            [];

        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        static fn ($key): string =>
                            trim((string) $key),

                        (array) $keys
                    ),

                    static fn (string $key): bool =>
                        $key !== ''
                )
            )
        );
    }


    /**
     * ตรวจสอบว่าผู้ใช้งานได้รับสิทธิ์หรือไม่
     */
    private function isAllowed(
        mixed $user,
        array $permissionKeys,
        string $action
    ): bool {
        if ($permissionKeys === []) {
            return true;
        }

        try {
            /*
            |--------------------------------------------------------------------------
            | Permission เดียว
            |--------------------------------------------------------------------------
            */

            if (count($permissionKeys) === 1) {
                return (bool) $user->hasFormPermission(
                    $permissionKeys[0],
                    $action
                );
            }

            /*
            |--------------------------------------------------------------------------
            | หลาย Permission
            |--------------------------------------------------------------------------
            */

            if (
                method_exists(
                    $user,
                    'hasAnyFormPermission'
                )
            ) {
                return (bool) $user->hasAnyFormPermission(
                    $permissionKeys,
                    $action
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback
            |--------------------------------------------------------------------------
            */

            foreach ($permissionKeys as $permissionKey) {
                if (
                    $user->hasFormPermission(
                        $permissionKey,
                        $action
                    )
                ) {
                    return true;
                }
            }

        } catch (Throwable) {
            return false;
        }

        return false;
    }


    /**
     * บันทึก CREATE / UPDATE / DELETE
     *
     * ไม่บันทึก Request Payload
     * ไม่บันทึกค่าของ Field
     * ไม่บันทึกข้อมูลสุขภาพ
     * ไม่บันทึกข้อมูลส่วนบุคคล
     */
    private function auditMutation(
        Request $request,
        ?Response $response,
        mixed $user,
        array $permissionKeys,
        string $action,
        bool $failedByException = false,
        ?Throwable $exception = null
    ): void {
        /*
        |--------------------------------------------------------------------------
        | สนใจเฉพาะการเปลี่ยนแปลงข้อมูล
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $action,
                [
                    'create',
                    'update',
                    'delete',
                ],
                true
            )
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | GET / HEAD ไม่ถือว่าเป็น Mutation
        |--------------------------------------------------------------------------
        |
        | เช่น:
        | users.create
        | users.edit
        | accident.edit
        |
        | แม้ Permission Rule อาจเป็น create/update
        | แต่การเปิดหน้าเฉย ๆ ไม่ถือว่าได้เปลี่ยนข้อมูล
        |
        */

        if ($request->isMethodSafe()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | HTTP Status
        |--------------------------------------------------------------------------
        */

        $statusCode = $response
            ? $response->getStatusCode()
            : null;

        /*
        |--------------------------------------------------------------------------
        | Exception Status
        |--------------------------------------------------------------------------
        */

        if ($failedByException) {
            if ($exception instanceof ValidationException) {
                $statusCode = 422;

            } elseif ($exception instanceof HttpExceptionInterface) {
                $statusCode = $exception->getStatusCode();

            } else {
                $statusCode = 500;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ตรวจ Session Error
        |--------------------------------------------------------------------------
        |
        | Controller บางแห่งตอบ Redirect 302
        | แต่ส่ง session('error') หรือ validation errors กลับมา
        |
        | จึงไม่ควรนับ Redirect ทุกกรณีเป็น success
        |
        */

        $hasErrors = false;

        try {
            if ($request->hasSession()) {
                $hasErrors =
                    $request->session()->has('errors')
                    ||
                    $request->session()->has('error');
            }

        } catch (Throwable) {
            $hasErrors = false;
        }

        /*
        |--------------------------------------------------------------------------
        | Result
        |--------------------------------------------------------------------------
        */

        $result = (
            $failedByException
            ||
            ($statusCode !== null && $statusCode >= 400)
            ||
            $hasErrors
        )
            ? 'failed'
            : 'success';

        /*
        |--------------------------------------------------------------------------
        | Module
        |--------------------------------------------------------------------------
        |
        | ใช้ Permission Key เป็นชื่อ Module
        |
        | ตัวอย่าง:
        |
        | health_accident
        | health_medical
        | welfare_followup
        | education_followup
        | system_users
        |
        */

        $module =
            $permissionKeys[0]
            ??
            'system';

        /*
        |--------------------------------------------------------------------------
        | Client ID
        |--------------------------------------------------------------------------
        |
        | อ่านเฉพาะ Route Parameter ที่ระบุชัดว่า:
        |
        | client_id
        | client
        |
        | ไม่ใช้ {id} ทั่วไป เพราะ id อาจเป็น:
        |
        | accident id
        | medical id
        | user id
        | followup id
        |
        | จึงห้ามเดาว่า {id} คือ Client ID
        |
        */

        $context = AuditMutationContext::primary($request, $action);

        /*
         * AUDIT_USER_UPDATE_EMPTY_CONTEXT_V1
         * users.update may already write a dedicated PERMISSION_CHANGE audit row.
         * If the request succeeded but no Eloquent mutation context exists,
         * there was no model UPDATE to describe, so skip the empty generic UPDATE row.
         */
        if (
            $context === null
            && $result === 'success'
            && $action === 'update'
            && $request->route()?->getName() === 'users.update'
        ) {
            return;
        }

        $clientId = isset($context['client_id']) && is_numeric($context['client_id'])
            ? (int) $context['client_id']
            : null;

        $subjectType = filled($context['subject_type'] ?? null)
            ? (string) $context['subject_type']
            : null;

        $subjectId = isset($context['subject_id']) && is_numeric($context['subject_id'])
            ? (int) $context['subject_id']
            : null;

        $changedFields = is_array($context['changed_fields'] ?? null)
            ? $context['changed_fields']
            : [];

        /*
         * Fallback เดิม: Route ระบุ client_id/client โดยตรง
         * ใช้เมื่อ Request นั้นไม่มี Eloquent mutation context
         */
        if ($clientId === null) {
            foreach (['client_id', 'client'] as $parameterName) {
                $parameter = $request->route($parameterName);

                if ($parameter === null) {
                    continue;
                }

                if (is_numeric($parameter)) {
                    $clientId = (int) $parameter;
                    break;
                }

                if (is_object($parameter) && method_exists($parameter, 'getKey')) {
                    $key = $parameter->getKey();

                    if (is_numeric($key)) {
                        $clientId = (int) $key;
                    }

                    break;
                }
            }
        }

        /*
         * CREATE หลาย route ส่ง client_id ใน form แทน URL
         * ใช้เฉพาะกรณีที่ Controller ทำงานสำเร็จแล้วเท่านั้น
         */
        if ($clientId === null && $result === 'success') {
            $postedClientId = $request->input('client_id');

            if (is_numeric($postedClientId) && (int) $postedClientId > 0) {
                $clientId = (int) $postedClientId;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        |
        | เก็บเฉพาะ:
        |
        | - ใคร
        | - ทำอะไร
        | - Module ไหน
        | - ผู้รับบริการรายใด (ถ้าระบุได้อย่างปลอดภัย)
        | - Route
        | - HTTP Method
        | - IP
        | - ผลสำเร็จ/ล้มเหลว
        | - HTTP Status
        | - เวลา
        |
        | AuditLogger จะเติมข้อมูลทางเทคนิคส่วนอื่นให้อัตโนมัติ
        |--------------------------------------------------------------------------
        */

        AuditLogger::log(
            action: strtoupper($action),
            module: $module,
            clientId: $clientId,
            changedFields: $changedFields,
            result: $result,
            statusCode: $statusCode,
            userId: (int) $user->getAuthIdentifier(),
            subjectType: $subjectType,
            subjectId: $subjectId
        );
    }


    /**
     * ข้อความเมื่อไม่มีสิทธิ์
     */
    private function deniedMessage(
        string $action
    ): string {
        return match ($action) {
            'create' =>
                'บัญชีนี้อยู่ในโหมดอ่านอย่างเดียว และไม่มีสิทธิ์เพิ่มข้อมูล',

            'update' =>
                'บัญชีนี้อยู่ในโหมดอ่านอย่างเดียว และไม่มีสิทธิ์แก้ไขข้อมูล',

            'delete' =>
                'บัญชีนี้อยู่ในโหมดอ่านอย่างเดียว และไม่มีสิทธิ์ลบข้อมูล',

            'print' =>
                'บัญชีนี้ไม่มีสิทธิ์พิมพ์หรือเปิดรายงาน',

            default =>
                'คุณไม่มีสิทธิ์เข้าถึงหน้านี้หรือดำเนินการรายการนี้',
        };
    }
}