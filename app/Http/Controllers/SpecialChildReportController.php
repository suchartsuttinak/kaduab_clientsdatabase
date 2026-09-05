<?php

namespace App\Http\Controllers;

// SPECIAL_CHILDREN_REPORT_V1

use App\Models\CheckBody;
use App\Models\Client;
use App\Models\House;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SpecialChildReportController extends Controller
{
    private const SUPPORT_TYPES = [
        'ต้องการการสนับสนุนด้านการเรียนรู้ (อ่าน เขียน คำนวณ)',
        'ต้องการการสนับสนุนด้านพฤติกรรมและอารมณ์ (การควบคุมอารมณ์, สมาธิ)',
        'ต้องการการสนับสนุนด้านสังคม (การเข้าสังคม, ทำงานร่วมกับเพื่อน)',
        'ต้องการการสนับสนุนด้านร่างกาย (การเคลื่อนไหว, สุขภาพ)',
        'มีศักยภาพพิเศษที่ควรส่งเสริม (ดนตรี, กีฬา, ศิลปะ)',
        'อื่น ๆ',
    ];

    private const SUPPORT_LABELS = [
        'ต้องการการสนับสนุนด้านการเรียนรู้ (อ่าน เขียน คำนวณ)' => 'ด้านการเรียนรู้',
        'ต้องการการสนับสนุนด้านพฤติกรรมและอารมณ์ (การควบคุมอารมณ์, สมาธิ)' => 'พฤติกรรมและอารมณ์',
        'ต้องการการสนับสนุนด้านสังคม (การเข้าสังคม, ทำงานร่วมกับเพื่อน)' => 'ด้านสังคม',
        'ต้องการการสนับสนุนด้านร่างกาย (การเคลื่อนไหว, สุขภาพ)' => 'ด้านร่างกาย',
        'มีศักยภาพพิเศษที่ควรส่งเสริม (ดนตรี, กีฬา, ศิลปะ)' => 'ศักยภาพพิเศษ',
        'อื่น ๆ' => 'อื่น ๆ',
    ];

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user, 401);

        // Defense-in-depth: แม้ config route permission ยังถูก cache อยู่ ก็ไม่เปิดข้อมูลให้ผู้ไม่มีสิทธิ์
        if (method_exists($user, 'canViewForm')) {
            abort_unless(
                $user->canViewForm('report_special_children'),
                403,
                'บัญชีนี้ไม่มีสิทธิ์ดูรายงานเด็กกลุ่มพิเศษ'
            );
        }

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'house_id' => ['nullable', 'integer', 'exists:houses,id'],
            'support_type' => ['nullable', Rule::in(self::SUPPORT_TYPES)],
            'development' => ['nullable', Rule::in(['สมวัย', 'ไม่สมวัย'])],
            'per_page' => ['nullable', Rule::in(['10', '25', '50', '100', 10, 25, 50, 100])],
        ], [
            'search.max' => 'คำค้นหาต้องไม่เกิน 100 ตัวอักษร',
            'house_id.integer' => 'ข้อมูลบ้านพักไม่ถูกต้อง',
            'house_id.exists' => 'ไม่พบบ้านพักที่เลือก',
            'support_type.in' => 'ประเภทการสนับสนุนไม่ถูกต้อง',
            'development.in' => 'ข้อมูลพัฒนาการไม่ถูกต้อง',
            'per_page.in' => 'จำนวนรายการต่อหน้าไม่ถูกต้อง',
        ]);

        $filters = [
            'search' => Str::substr(trim((string) ($validated['search'] ?? '')), 0, 100),
            'house_id' => isset($validated['house_id']) ? (int) $validated['house_id'] : null,
            'support_type' => $validated['support_type'] ?? null,
            'development' => $validated['development'] ?? null,
            'per_page' => (int) ($validated['per_page'] ?? 25),
        ];

        if (!in_array($filters['per_page'], [10, 25, 50, 100], true)) {
            $filters['per_page'] = 25;
        }

        $baseQuery = $this->latestSpecialChildrenQuery($user);

        $this->applyFilters($baseQuery, $filters, includeSupportType: false);

        // Summary ใช้ตัวกรองอื่นทั้งหมด แต่ยังไม่บังคับ support_type
        // เพื่อให้ผู้ใช้เห็นสัดส่วนทุกประเภทและใช้การ์ดเป็น Quick Filter ได้
        $summaryCounts = (clone $baseQuery)
            ->select('check_bodies.special_support_type')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('check_bodies.special_support_type')
            ->pluck('total', 'special_support_type');

        $supportSummary = collect(self::SUPPORT_TYPES)->map(function (string $type) use ($summaryCounts): array {
            return [
                'type' => $type,
                'label' => self::SUPPORT_LABELS[$type] ?? $type,
                'count' => (int) ($summaryCounts[$type] ?? 0),
            ];
        });

        $unclassifiedCount = (int) ($summaryCounts[''] ?? 0);
        $summaryTotal = (int) $supportSummary->sum('count') + $unclassifiedCount;

        $recordsQuery = clone $baseQuery;
        $this->applySupportFilter($recordsQuery, $filters['support_type']);

        $records = $recordsQuery
            ->with([
                'client.title',
                'client.house',
            ])
            ->orderBy('check_bodies.special_support_type')
            ->orderByDesc('check_bodies.assessor_date')
            ->orderByDesc('check_bodies.id')
            ->paginate($filters['per_page'])
            ->withQueryString();

        $houses = $this->accessibleHouses($user);

        $canPrint = method_exists($user, 'canPrintForm')
            ? (bool) $user->canPrintForm('report_special_children')
            : false;

        $supportTypes = collect(self::SUPPORT_TYPES)->mapWithKeys(
            fn (string $type): array => [$type => self::SUPPORT_LABELS[$type] ?? $type]
        );

        $selectedSupportLabel = $filters['support_type']
            ? (self::SUPPORT_LABELS[$filters['support_type']] ?? $filters['support_type'])
            : null;

        return view('admin.reports.special_children.index', compact(
            'records',
            'houses',
            'filters',
            'supportTypes',
            'supportSummary',
            'summaryTotal',
            'unclassifiedCount',
            'selectedSupportLabel',
            'canPrint'
        ));
    }

    private function latestSpecialChildrenQuery($user): Builder
    {
        return CheckBody::query()
            ->where('check_bodies.development_type', 'เด็กกลุ่มพิเศษ')
            ->whereHas('client', function (Builder $query) use ($user): void {
                $query->forUser($user);
                $this->applyCurrentClientStatus($query);
            })
            // เลือกเฉพาะผลตรวจล่าสุดของเด็กแต่ละคน
            // หากผลตรวจใหม่เปลี่ยนกลับเป็น "เด็กทั่วไป" เด็กจะไม่ค้างในรายงานนี้
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('check_bodies as newer_cb')
                    ->whereColumn('newer_cb.client_id', 'check_bodies.client_id')
                    ->where(function ($newer): void {
                        $newer->whereColumn('newer_cb.assessor_date', '>', 'check_bodies.assessor_date')
                            ->orWhere(function ($sameDate): void {
                                $sameDate->whereColumn('newer_cb.assessor_date', '=', 'check_bodies.assessor_date')
                                    ->whereColumn('newer_cb.id', '>', 'check_bodies.id');
                            });
                    });
            });
    }

    private function applyFilters(Builder $query, array $filters, bool $includeSupportType = true): void
    {
        if ($filters['search'] !== '') {
            $terms = preg_split('/\\s+/u', $filters['search'], -1, PREG_SPLIT_NO_EMPTY) ?: [];

            foreach ($terms as $term) {
                $query->whereHas('client', function (Builder $clientQuery) use ($term): void {
                    $keyword = '%' . $term . '%';
                    $clientQuery->where(function (Builder $searchQuery) use ($keyword): void {
                        $searchQuery
                            ->where('register_number', 'like', $keyword)
                            ->orWhere('first_name', 'like', $keyword)
                            ->orWhere('last_name', 'like', $keyword)
                            ->orWhere('nick_name', 'like', $keyword);
                    });
                });
            }
        }

        if (!empty($filters['house_id'])) {
            $query->whereHas('client', function (Builder $clientQuery) use ($filters): void {
                $clientQuery->where('house_id', (int) $filters['house_id']);
            });
        }

        if (!empty($filters['development'])) {
            $query->where('check_bodies.development', $filters['development']);
        }

        if ($includeSupportType) {
            $this->applySupportFilter($query, $filters['support_type'] ?? null);
        }
    }

    private function applySupportFilter(Builder $query, ?string $supportType): void
    {
        if (!empty($supportType)) {
            $query->where('check_bodies.special_support_type', $supportType);
        }
    }

    private function applyCurrentClientStatus(Builder $query): void
    {
        // ให้ผลลัพธ์สอดคล้องกับ "ผู้รับบริการปัจจุบัน" ของระบบ
        // รวม pending_refer เฉพาะรายที่ยังมีคำขอส่งต่อรอดำเนินการ
        $query->where(function (Builder $statusQuery): void {
            $statusQuery
                ->where('release_status', 'show')
                ->orWhere(function (Builder $pendingQuery): void {
                    $pendingQuery
                        ->where('release_status', 'pending_refer')
                        ->whereHas('refers', function (Builder $referQuery): void {
                            $referQuery->where('approve_status', 'pending');
                        });
                });
        });
    }

    private function accessibleHouses($user)
    {
        $clients = Client::forUser($user)
            ->select(['clients.house_id', 'clients.release_status']);

        $this->applyCurrentClientStatus($clients);

        $houseIds = $clients
            ->whereNotNull('clients.house_id')
            ->distinct()
            ->pluck('clients.house_id');

        if ($houseIds->isEmpty()) {
            return collect();
        }

        return House::query()
            ->select(['id', 'house_name'])
            ->whereIn('id', $houseIds)
            ->orderBy('house_name')
            ->get();
    }
}