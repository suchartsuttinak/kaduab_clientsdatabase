<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CaseActivityController extends Controller
{
    private const ACTIVITY_TYPES = [
        'info',
        'success',
        'warning',
        'danger',
    ];

    /**
     * แสดง Timeline ความเคลื่อนไหวของผู้รับบริการ
     */
    public function index(Request $request, $client)
    {
        $client = $this->findAccessibleClient($client);
        $filters = $this->validateFilters($request);

        /*
         * คงพฤติกรรมเดิมของระบบไว้สำหรับแฟ้มเก่าที่ยังไม่มีรายการแรกเข้า
         * ในระยะยาวควรสร้างรายการนี้พร้อมการสร้าง Client
         */
        $this->ensureInitialActivity($client);

        $activities = $this->activityQuery($client, $filters)
            ->paginate(20)
            ->withQueryString();

        $modules = CaseActivity::query()
            ->where('client_id', $client->id)
            ->whereNotNull('module')
            ->where('module', '<>', '')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('frontend.case_activities.index', compact(
            'client',
            'activities',
            'modules'
        ));
    }

    /**
     * แสดงรายงานสำหรับพิมพ์
     */
    public function report(Request $request, $client)
    {
        $client = $this->findAccessibleClient($client);
        $filters = $this->validateFilters($request);

        $this->ensureInitialActivity($client);

        $activities = $this->activityQuery($client, $filters)->get();

        return view('frontend.case_activities.report', compact(
            'client',
            'activities'
        ));
    }

    /**
     * จำกัดสิทธิ์ให้เห็นเฉพาะผู้รับบริการที่ผู้ใช้มีสิทธิ์เข้าถึง
     */
    private function findAccessibleClient($client): Client
    {
        $clientId = $client instanceof Client
            ? $client->getKey()
            : $client;

        return Client::forUser(auth()->user())->findOrFail($clientId);
    }

    /**
     * ตรวจสอบค่าตัวกรองก่อนนำไปใช้กับ Query
     */
    private function validateFilters(Request $request): array
    {
        return $request->validate(
            [
                'module' => ['nullable', 'string', 'max:100'],
                'type' => ['nullable', Rule::in(self::ACTIVITY_TYPES)],
                'date_from' => ['nullable', 'date', 'before_or_equal:today'],
                'date_to' => [
                    'nullable',
                    'date',
                    'after_or_equal:date_from',
                    'before_or_equal:today',
                ],
            ],
            [
                'module.string' => 'ประเภทโมดูลไม่ถูกต้อง',
                'module.max' => 'ประเภทโมดูลต้องมีความยาวไม่เกิน 100 ตัวอักษร',

                'type.in' => 'ระดับเหตุการณ์ไม่ถูกต้อง',

                'date_from.date' => 'รูปแบบวันที่เริ่มต้นไม่ถูกต้อง',
                'date_from.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันที่ปัจจุบัน',

                'date_to.date' => 'รูปแบบวันที่สิ้นสุดไม่ถูกต้อง',
                'date_to.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
                'date_to.before_or_equal' => 'วันที่สิ้นสุดต้องไม่เกินวันที่ปัจจุบัน',
            ]
        );
    }

    /**
     * Query กลางสำหรับหน้า Timeline และหน้ารายงาน
     */
    private function activityQuery(Client $client, array $filters): Builder
    {
        return CaseActivity::query()
            ->with(['user:id,name'])
            ->where('client_id', $client->id)
            ->when(
                !empty($filters['module']),
                fn (Builder $query) => $query->where('module', $filters['module'])
            )
            ->when(
                !empty($filters['type']),
                fn (Builder $query) => $query->where('type', $filters['type'])
            )
            ->when(
                !empty($filters['date_from']),
                fn (Builder $query) => $query->whereDate(
                    'occurred_at',
                    '>=',
                    $filters['date_from']
                )
            )
            ->when(
                !empty($filters['date_to']),
                fn (Builder $query) => $query->whereDate(
                    'occurred_at',
                    '<=',
                    $filters['date_to']
                )
            )
            ->orderByDesc('occurred_at')
            ->orderByDesc('id');
    }

    /**
     * เพิ่มรายการแรกเข้าสำหรับข้อมูลเดิมที่ยังไม่มี Timeline
     */
    private function ensureInitialActivity(Client $client): void
    {
        CaseActivity::firstOrCreate(
            [
                'client_id' => $client->id,
                'module' => 'client',
                'title' => 'บันทึกข้อมูลแรกเข้า',
            ],
            [
                'user_id' => auth()->id(),
                'type' => 'success',
                'description' => 'มีการสร้างแฟ้มทะเบียนประวัติผู้รับบริการเข้าสู่ระบบ',
                'occurred_at' => $client->created_at
                    ?? now(config('app.timezone', 'Asia/Bangkok')),
                'icon' => 'bi-person-plus',
                'url' => route('client.edit', $client->id),
            ]
        );
    }
}