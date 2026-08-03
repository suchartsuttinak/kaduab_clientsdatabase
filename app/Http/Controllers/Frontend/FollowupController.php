<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Followup;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FollowupController extends Controller
{
    /**
     * แสดงรายการติดตามผลของผู้รับบริการ
     */
    public function index(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $filters = $this->validateDateFilters($request);

        $hasAnyFollowups = Followup::query()
            ->where('client_id', $client->id)
            ->exists();

        $followups = $this->followupQuery($client->id, $filters)
            ->latest('followup_date')
            ->latest('id')
            ->get();

        return view('frontend.client.followup.index', [
            'client'          => $client,
            'followups'       => $followups,
            'hasAnyFollowups' => $hasAnyFollowups,
            'dateFrom'        => $filters['date_from'] ?? null,
            'dateTo'          => $filters['date_to'] ?? null,
        ]);
    }

    /**
     * บันทึกข้อมูลติดตามผล
     */
    public function store(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $this->authorizeManage();

        $this->normalizeTextInputs($request);

        $validator = Validator::make(
            $request->all(),
            $this->followupRules(),
            $this->followupMessages()
        );

        if ($validator->fails()) {
            return redirect()
                ->route('followup.index', $client->id)
                ->withErrors($validator)
                ->withInput()
                ->with('followup_modal', 'create');
        }

        $data = $validator->validated();
        $data['note'] = filled($data['note'] ?? null) ? $data['note'] : null;

        try {
            DB::transaction(function () use ($client, $data) {
                $followup = Followup::create([
                    'client_id'         => $client->id,
                    'followup_date'     => $data['followup_date'],
                    'assistance_detail' => $data['assistance_detail'],
                    'note'              => $data['note'],
                ]);

                $this->replaceCaseActivity(
                    $client->id,
                    $followup,
                    'บันทึกการช่วยเหลือและติดตามผล'
                );
            }, 3);
        } catch (\Throwable $e) {
            Log::error('Unable to store followup.', [
                'client_id' => $client->id,
                'user_id'   => auth()->id(),
                'message'   => $e->getMessage(),
            ]);

            return redirect()
                ->route('followup.index', $client->id)
                ->withInput()
                ->with('followup_modal', 'create')
                ->with('error', 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
        }

        return redirect()
            ->route('followup.index', $client->id)
            ->with('success', 'บันทึกข้อมูลติดตามผลเรียบร้อยแล้ว');
    }

    /**
     * แก้ไขข้อมูลติดตามผล
     */
    public function update(Request $request, $id)
    {
        $followup = $this->scopedFollowupQuery()->findOrFail($id);
        $client = Client::forUser(auth()->user())->findOrFail($followup->client_id);

        $this->authorizeManage();
        $this->normalizeTextInputs($request);

        $validator = Validator::make(
            $request->all(),
            $this->followupRules(),
            $this->followupMessages()
        );

        if ($validator->fails()) {
            return redirect()
                ->route('followup.index', $client->id)
                ->withErrors($validator)
                ->withInput()
                ->with('followup_modal', 'edit')
                ->with('followup_edit_id', $followup->id);
        }

        $data = $validator->validated();
        $data['note'] = filled($data['note'] ?? null) ? $data['note'] : null;

        try {
            DB::transaction(function () use ($followup, $data) {
                $followup->update([
                    'followup_date'     => $data['followup_date'],
                    'assistance_detail' => $data['assistance_detail'],
                    'note'              => $data['note'],
                ]);

                $followup->refresh();

                $this->replaceCaseActivity(
                    $followup->client_id,
                    $followup,
                    'แก้ไขการช่วยเหลือและติดตามผล'
                );
            }, 3);
        } catch (\Throwable $e) {
            Log::error('Unable to update followup.', [
                'followup_id' => $followup->id,
                'client_id'   => $client->id,
                'user_id'     => auth()->id(),
                'message'     => $e->getMessage(),
            ]);

            return redirect()
                ->route('followup.index', $client->id)
                ->withInput()
                ->with('followup_modal', 'edit')
                ->with('followup_edit_id', $followup->id)
                ->with('error', 'ไม่สามารถแก้ไขข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
        }

        return redirect()
            ->route('followup.index', $client->id)
            ->with('success', 'แก้ไขข้อมูลติดตามผลเรียบร้อยแล้ว');
    }

    /**
     * ลบข้อมูลติดตามผล
     */
    public function destroy($id)
    {
        $followup = $this->scopedFollowupQuery()->findOrFail($id);
        $client = Client::forUser(auth()->user())->findOrFail($followup->client_id);

        $this->authorizeDelete();

        try {
            DB::transaction(function () use ($followup, $client) {
                $followup->delete();
                $this->syncCaseActivityAfterDelete($client->id);
            }, 3);
        } catch (\Throwable $e) {
            Log::error('Unable to delete followup.', [
                'followup_id' => $followup->id,
                'client_id'   => $client->id,
                'user_id'     => auth()->id(),
                'message'     => $e->getMessage(),
            ]);

            return redirect()
                ->route('followup.index', $client->id)
                ->with('error', 'ไม่สามารถลบข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
        }

        return redirect()
            ->route('followup.index', $client->id)
            ->with('success', 'ลบข้อมูลติดตามผลเรียบร้อยแล้ว');
    }

    /**
     * รายงานตามช่วงวันที่
     */
    public function report(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $filters = $this->validateDateFilters($request);

        $followups = $this->followupQuery($client->id, $filters)
            ->oldest('followup_date')
            ->oldest('id')
            ->get();

        return view('frontend.client.followup.report', [
            'client'       => $client,
            'followups'    => $followups,
            'dateFrom'     => $filters['date_from'] ?? null,
            'dateTo'       => $filters['date_to'] ?? null,
            'isSingleItem' => false,
        ]);
    }

    /**
     * ดาวน์โหลดรายงาน PDF
     */
    public function exportPdf(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $filters = $this->validateDateFilters($request);

        $followups = $this->followupQuery($client->id, $filters)
            ->oldest('followup_date')
            ->oldest('id')
            ->get();

        $dateFrom = $filters['date_from'] ?? null;
        $dateTo   = $filters['date_to'] ?? null;

        $pdf = Pdf::loadView('frontend.client.followup.pdf', compact(
            'client',
            'followups',
            'dateFrom',
            'dateTo'
        ))->setPaper('a4', 'portrait');

        $filename = 'followup-client-' . $client->id . '-' . now('Asia/Bangkok')->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * รายงานเฉพาะรายการ
     */
    public function reportItem($id)
    {
        $followup = $this->scopedFollowupQuery()->findOrFail($id);
        $client = Client::forUser(auth()->user())->findOrFail($followup->client_id);

        return view('frontend.client.followup.report', [
            'client'       => $client,
            'followups'    => collect([$followup]),
            'dateFrom'     => null,
            'dateTo'       => null,
            'isSingleItem' => true,
        ]);
    }

    /**
     * Query รายการติดตามผล พร้อมตรวจสิทธิ์ผ่าน client relationship
     */
    private function scopedFollowupQuery(): Builder
    {
        return Followup::query()
            ->whereHas('client', function (Builder $query) {
                $query->forUser(auth()->user());
            });
    }

    /**
     * Query รายการตามผู้รับบริการและตัวกรอง
     */
    private function followupQuery(int $clientId, array $filters = []): Builder
    {
        return Followup::query()
            ->select([
                'id',
                'client_id',
                'followup_date',
                'assistance_detail',
                'note',
                'created_at',
                'updated_at',
            ])
            ->where('client_id', $clientId)
            ->when(
                filled($filters['date_from'] ?? null),
                fn (Builder $query) => $query->whereDate('followup_date', '>=', $filters['date_from'])
            )
            ->when(
                filled($filters['date_to'] ?? null),
                fn (Builder $query) => $query->whereDate('followup_date', '<=', $filters['date_to'])
            );
    }

    /**
     * ตรวจสอบตัวกรองวันที่
     */
    private function validateDateFilters(Request $request): array
    {
        $today = now('Asia/Bangkok')->toDateString();

        return Validator::make($request->all(), [
            'date_from' => ['nullable', 'date', 'before_or_equal:' . $today],
            'date_to'   => ['nullable', 'date', 'before_or_equal:' . $today, 'after_or_equal:date_from'],
        ], [
            'date_from.date'            => 'รูปแบบวันที่เริ่มต้นไม่ถูกต้อง',
            'date_from.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันปัจจุบัน',
            'date_to.date'              => 'รูปแบบวันที่สิ้นสุดไม่ถูกต้อง',
            'date_to.before_or_equal'   => 'วันที่สิ้นสุดต้องไม่เกินวันปัจจุบัน',
            'date_to.after_or_equal'    => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ])->validate();
    }

    /**
     * กฎ Validation สำหรับเพิ่มและแก้ไข
     */
    private function followupRules(): array
    {
        return [
            'followup_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'assistance_detail' => ['required', 'string', 'max:10000'],
            'note'              => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * ข้อความ Validation ภาษาไทย
     */
    private function followupMessages(): array
    {
        return [
            'followup_date.required'        => 'กรุณาเลือกวันเดือนปี',
            'followup_date.date'            => 'รูปแบบวันเดือนไม่ถูกต้อง',
            'followup_date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันปัจจุบัน',
            'assistance_detail.required'    => 'กรุณากรอกการช่วยเหลือและติดตามผล',
            'assistance_detail.string'      => 'การช่วยเหลือและติดตามผลต้องเป็นข้อความ',
            'assistance_detail.max'         => 'การช่วยเหลือและติดตามผลต้องมีความยาวไม่เกิน 10,000 ตัวอักษร',
            'note.string'                   => 'หมายเหตุต้องเป็นข้อความ',
            'note.max'                      => 'หมายเหตุต้องมีความยาวไม่เกิน 5,000 ตัวอักษร',
        ];
    }

    /**
     * ตัดช่องว่างก่อน Validation
     */
    private function normalizeTextInputs(Request $request): void
    {
        $request->merge([
            'assistance_detail' => trim((string) $request->input('assistance_detail', '')),
            'note'              => filled($request->input('note'))
                ? trim((string) $request->input('note'))
                : null,
        ]);
    }

    /**
     * แทนที่กิจกรรมล่าสุดของโมดูล
     */
    private function replaceCaseActivity(int $clientId, Followup $followup, string $title): void
    {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'followup')
            ->delete();

        CaseActivity::record([
            'client_id'   => $clientId,
            'module'      => 'followup',
            'type'        => 'success',
            'title'       => $title,
            'description' => 'วันที่ติดตาม: ' .
                Carbon::parse($followup->followup_date)->format('d/m/Y') .
                ' | รายละเอียด: ' .
                Str::limit($followup->assistance_detail, 180),
            'occurred_at' => Carbon::parse($followup->followup_date)->startOfDay(),
            'icon'        => 'bi-journal-check',
            'url'         => route('followup.index', $clientId),
        ]);
    }

    /**
     * หลังลบ ให้กิจกรรมอ้างอิงรายการล่าสุดที่ยังเหลืออยู่
     */
    private function syncCaseActivityAfterDelete(int $clientId): void
    {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'followup')
            ->delete();

        $latest = Followup::query()
            ->where('client_id', $clientId)
            ->latest('followup_date')
            ->latest('id')
            ->first();

        if ($latest) {
            $this->replaceCaseActivity(
                $clientId,
                $latest,
                'ข้อมูลการช่วยเหลือและติดตามผลล่าสุด'
            );
        }
    }

    private function authorizeManage(): void
    {
        abort_unless(
            auth()->check()
                && in_array(auth()->user()->role, ['admin', 'executive', 'social_worker'], true),
            403,
            'คุณไม่มีสิทธิ์ดำเนินการนี้'
        );
    }

    private function authorizeDelete(): void
    {
        abort_unless(
            auth()->check() && auth()->user()->role === 'admin',
            403,
            'คุณไม่มีสิทธิ์ลบข้อมูลนี้'
        );
    }
}