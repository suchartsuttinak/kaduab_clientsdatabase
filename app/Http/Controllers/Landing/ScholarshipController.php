<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipDonation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ScholarshipController extends Controller
{
    private const SUPPORT_TYPES = [
        'ทุนการศึกษา',
        'ชุดนักเรียน',
        'อุปกรณ์การเรียน',
    ];

    private const DONATION_TYPES = [
        'เงินสด',
        'โอนเงิน',
        'ทุนการศึกษา',
        'ชุดนักเรียน',
        'อุปกรณ์การเรียน',
        'อื่น ๆ',
    ];

    public function index(Request $request): View
    {
        // EPC_SCHOLARSHIP_CONTROLLER_V1
        $this->ensureScholarshipPermission('view');

        if (auth()->check() && (auth()->user()->role ?? null) === 'admin') {
            Scholarship::query()
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now('Asia/Bangkok'),
                ]);
        }

        $endDateRules = [
            'nullable',
            'date',
            'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
        ];

        if ($request->filled('start_date')) {
            $endDateRules[] = 'after_or_equal:start_date';
        }

        $validated = $request->validate([
            'start_date' => [
                'nullable',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'end_date' => $endDateRules,
        ], [
            'start_date.date' => 'รูปแบบวันที่เริ่มต้นไม่ถูกต้อง',
            'start_date.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันที่ปัจจุบัน',
            'end_date.date' => 'รูปแบบวันที่สิ้นสุดไม่ถูกต้อง',
            'end_date.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
            'end_date.before_or_equal' => 'วันที่สิ้นสุดต้องไม่เกินวันที่ปัจจุบัน',
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;

        $applyDateFilter = static function ($query) use ($startDate, $endDate) {
            return $query
                ->when($startDate, fn ($q) => $q->where('donation_date', '>=', $startDate))
                ->when($endDate, fn ($q) => $q->where('donation_date', '<=', $endDate));
        };

        $scholarships = Scholarship::query()
            ->select([
                'id',
                'fullname',
                'support_types',
                'phone',
                'email',
                'detail',
                'created_at',
            ])
            ->when(
                $startDate || $endDate,
                fn (Builder $query) => $query->whereHas('donations', $applyDateFilter)
            )
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $donationQuery = ScholarshipDonation::query();
        $applyDateFilter($donationQuery);

        $totalDonationAmount = (clone $donationQuery)->sum('amount');

        /*
         * ใช้ตัวกรองช่วงวันที่เดียวกับยอดรวม
         * และส่ง total_items ให้ตรงกับที่หน้า Blade แสดงผล
         */
        $donationYearSummary = (clone $donationQuery)
            ->selectRaw('YEAR(donation_date) AS year')
            ->selectRaw('SUM(amount) AS total_amount')
            ->selectRaw('COUNT(*) AS total_items')
            ->whereNotNull('donation_date')
            ->groupByRaw('YEAR(donation_date)')
            ->orderByDesc('year')
            ->get();

        return view('landing.scholarship.scholarship.index', compact(
            'scholarships',
            'totalDonationAmount',
            'donationYearSummary',
            'startDate',
            'endDate'
        ));
    }

    public function create(): View
    {
        return view('landing.scholarship.scholarship.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'fullname' => Str::squish((string) $request->input('fullname')),
            'phone' => trim((string) $request->input('phone')),
            'email' => trim((string) $request->input('email')),
            'detail' => trim((string) $request->input('detail')),
        ]);

        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'support_types' => ['required', 'array', 'min:1'],
            'support_types.*' => [
                'required',
                'string',
                'distinct',
                Rule::in(self::SUPPORT_TYPES),
            ],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'detail' => ['nullable', 'string', 'max:5000'],
        ], [
            'fullname.required' => 'กรุณากรอกชื่อ-สกุลผู้สนับสนุน',
            'support_types.required' => 'กรุณาเลือกรูปแบบการสนับสนุนอย่างน้อย 1 รายการ',
            'support_types.min' => 'กรุณาเลือกรูปแบบการสนับสนุนอย่างน้อย 1 รายการ',
            'support_types.*.in' => 'มีรูปแบบการสนับสนุนบางรายการไม่ถูกต้อง',
            'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'detail.max' => 'รายละเอียดเพิ่มเติมต้องไม่เกิน 5,000 ตัวอักษร',
        ]);

        Scholarship::query()->create([
            'fullname' => $validated['fullname'],
            'support_types' => array_values(array_unique($validated['support_types'])),
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?: null,
            'detail' => $validated['detail'] ?: null,
            'is_read' => false,
        ]);

        return redirect()
            ->route('scholarship.create')
            ->with('success', 'ส่งข้อมูลการสนับสนุนเรียบร้อยแล้ว เจ้าหน้าที่จะติดต่อกลับภายหลัง');
    }

    public function createDonation(Scholarship $scholarship): View
    {
        $this->ensureScholarshipPermission('create');

        return view(
            'landing.scholarship.scholarship.donation_create',
            compact('scholarship')
        );
    }

    public function storeDonation(
        Request $request,
        Scholarship $scholarship
    ): RedirectResponse {
        $this->ensureScholarshipPermission('create');

        $request->merge([
            'description' => trim((string) $request->input('description')),
        ]);

        $validated = $request->validate([
            'donation_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'donation_type' => ['required', Rule::in(self::DONATION_TYPES)],
            'description' => ['nullable', 'string', 'max:5000'],
        ], [
            'donation_date.required' => 'กรุณาระบุวันที่บริจาค',
            'donation_date.before_or_equal' => 'วันที่บริจาคต้องไม่เกินวันที่ปัจจุบัน',
            'amount.numeric' => 'จำนวนเงินต้องเป็นตัวเลข',
            'amount.min' => 'จำนวนเงินต้องไม่น้อยกว่า 0 บาท',
            'donation_type.required' => 'กรุณาเลือกประเภทการบริจาค',
            'donation_type.in' => 'ประเภทการบริจาคไม่ถูกต้อง',
            'description.max' => 'รายละเอียดต้องไม่เกิน 5,000 ตัวอักษร',
        ]);

        $scholarship->donations()->create([
            'donation_date' => $validated['donation_date'],
            'amount' => $validated['amount'] ?? null,
            'donation_type' => $validated['donation_type'],
            'description' => $validated['description'] ?: null,
            'recorded_by' => auth()->user()->name ?? null,
        ]);

        return redirect()
            ->route('scholarship.donation.index', $scholarship->id)
            ->with('success', 'บันทึกข้อมูลการบริจาคเรียบร้อยแล้ว');
    }

    public function donationIndex($id): View
    {
        $this->ensureScholarshipPermission('view');

        $scholarship = Scholarship::query()->findOrFail($id);

        $donations = $scholarship->donations()
            ->select([
                'id',
                'scholarship_id',
                'donation_date',
                'amount',
                'donation_type',
                'description',
                'recorded_by',
                'created_at',
            ])
            ->latest('donation_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $totalDonationAmount = $scholarship->donations()->sum('amount');

        $donationYearSummary = $scholarship->donations()
            ->selectRaw('YEAR(donation_date) AS year')
            ->selectRaw('SUM(amount) AS total_amount')
            ->selectRaw('COUNT(*) AS total_items')
            ->whereNotNull('donation_date')
            ->groupByRaw('YEAR(donation_date)')
            ->orderByDesc('year')
            ->get();

        return view('landing.scholarship.scholarship.donation_index', compact(
            'scholarship',
            'donations',
            'totalDonationAmount',
            'donationYearSummary'
        ));
    }
    /**
     * ด่านสำหรับหน้าจัดการผู้สนับสนุนทุน
     * หมายเหตุ: create()/store() สำหรับแบบฟอร์มผู้สนับสนุนสาธารณะไม่ถูกแตะ
     */
    private function ensureScholarshipPermission(string $action): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return;
        }

        if (!$user->isExecutive()) {
            abort(403, 'บัญชีนี้ไม่มีสิทธิ์จัดการผู้สนับสนุนทุนการศึกษา');
        }

        if (!$user->hasFormPermission('dashboard_scholarship_sponsors', $action)) {
            abort(403, 'บัญชีนี้ไม่ได้รับสิทธิ์สำหรับการดำเนินการด้านผู้สนับสนุนทุนการศึกษา');
        }
    }
}
