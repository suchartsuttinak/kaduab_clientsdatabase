<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Escape;
use App\Models\Retire;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EscapeController extends Controller
{
    /**
     * แสดงรายการข้อมูลการออก/หลบหนีของผู้รับบริการ
     */
    public function IndexEscape($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $escapes = Escape::query()
            ->with([
                'retire',
                'follows' => function ($query) {
                    $query->orderByDesc('trace_date')->orderByDesc('id');
                },
            ])
            ->where('client_id', $client->id)
            ->orderByDesc('retire_date')
            ->orderByDesc('id')
            ->get();

        $retires = Retire::query()
            ->orderBy('retire_name')
            ->get();

        return view('frontend.client.escape.escape_index', compact('client', 'escapes', 'retires'));
    }

    /**
     * หน้าเพิ่มข้อมูลแบบเต็มหน้า
     */
    public function AddEscape($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $retires = Retire::query()
            ->orderBy('retire_name')
            ->get();

        $mode = 'create';

        return view('frontend.client.escape.escape_create', compact('client', 'retires', 'mode'));
    }

    /**
     * บันทึกข้อมูลใหม่
     */
    public function StoreEscape(Request $request)
    {
        $request->merge([
            'stories' => $request->filled('stories') ? trim((string) $request->stories) : null,
        ]);

        $data = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'retire_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
                Rule::unique('escapes')->where(
                    fn ($query) => $query->where('client_id', $request->integer('client_id'))
                ),
            ],
            'retire_id' => ['required', 'integer', 'exists:retires,id'],
            'stories' => ['nullable', 'string', 'max:5000'],
        ], [
            'client_id.required' => 'ไม่พบข้อมูลผู้รับบริการ',
            'client_id.integer' => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'client_id.exists' => 'ไม่พบข้อมูลผู้รับบริการในระบบ',
            'retire_date.required' => 'กรุณาระบุวันที่ออก/หลบหนี',
            'retire_date.date' => 'รูปแบบวันที่ออก/หลบหนีไม่ถูกต้อง',
            'retire_date.before_or_equal' => 'วันที่ออก/หลบหนีต้องไม่เกินวันปัจจุบัน',
            'retire_date.unique' => 'วันที่ออก/หลบหนีนี้ถูกบันทึกแล้วสำหรับผู้รับบริการรายนี้',
            'retire_id.required' => 'กรุณาเลือกประเภทการออก/หลบหนี',
            'retire_id.integer' => 'ประเภทการออก/หลบหนีไม่ถูกต้อง',
            'retire_id.exists' => 'ไม่พบประเภทการออก/หลบหนีที่เลือก',
            'stories.string' => 'พฤติการณ์หรือสาเหตุต้องเป็นข้อความ',
            'stories.max' => 'พฤติการณ์หรือสาเหตุต้องไม่เกิน 5,000 ตัวอักษร',
        ]);

        $client = Client::forUser(auth()->user())->findOrFail($data['client_id']);
        $data['client_id'] = $client->id;

        $escape = DB::transaction(function () use ($data, $client) {
            $escape = Escape::create($data);
            $this->syncEscapeActivity($client->id, $escape, 'บันทึกการออก/หลบหนีจากที่พักพิง');

            return $escape;
        });

        return redirect()
            ->route('escape.edit', $escape->id)
            ->with(['message' => 'บันทึกข้อมูลการออก/หลบหนีเรียบร้อย', 'alert-type' => 'success']);
    }

    /**
     * หน้าแก้ไขและติดตามผล
     */
    public function EditEscape($id)
    {
        $escape = Escape::query()
            ->with([
                'client',
                'retire',
                'follows' => function ($query) {
                    $query->orderBy('count')->orderBy('trace_date')->orderBy('id');
                },
            ])
            ->whereHas('client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);

        $client = $escape->client;

        $retires = Retire::query()
            ->orderBy('retire_name')
            ->get();

        return view('frontend.client.escape.escape_edit', compact('escape', 'retires', 'client'));
    }

    /**
     * อัปเดตข้อมูลการออก/หลบหนี
     */
    public function UpdateEscape(Request $request, $id)
    {
        $escape = Escape::query()
            ->whereHas('client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);

        $request->merge([
            'stories' => $request->filled('stories') ? trim((string) $request->stories) : null,
        ]);

        $data = $request->validate([
            'retire_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
                Rule::unique('escapes')
                    ->where(fn ($query) => $query->where('client_id', $escape->client_id))
                    ->ignore($escape->id),
            ],
            'retire_id' => ['required', 'integer', 'exists:retires,id'],
            'stories' => ['nullable', 'string', 'max:5000'],
        ], [
            'retire_date.required' => 'กรุณาระบุวันที่ออก/หลบหนี',
            'retire_date.date' => 'รูปแบบวันที่ออก/หลบหนีไม่ถูกต้อง',
            'retire_date.before_or_equal' => 'วันที่ออก/หลบหนีต้องไม่เกินวันปัจจุบัน',
            'retire_date.unique' => 'วันที่ออก/หลบหนีนี้ถูกบันทึกแล้วสำหรับผู้รับบริการรายนี้',
            'retire_id.required' => 'กรุณาเลือกประเภทการออก/หลบหนี',
            'retire_id.integer' => 'ประเภทการออก/หลบหนีไม่ถูกต้อง',
            'retire_id.exists' => 'ไม่พบประเภทการออก/หลบหนีที่เลือก',
            'stories.string' => 'พฤติการณ์หรือสาเหตุต้องเป็นข้อความ',
            'stories.max' => 'พฤติการณ์หรือสาเหตุต้องไม่เกิน 5,000 ตัวอักษร',
        ]);

        DB::transaction(function () use ($escape, $data) {
            $escape->update($data);
            $escape->refresh();
            $this->syncEscapeActivity($escape->client_id, $escape, 'แก้ไขข้อมูลการออก/หลบหนีจากที่พักพิง');
        });

        return redirect()
            ->route('escape.edit', $escape->id)
            ->with(['message' => 'แก้ไขข้อมูลการออก/หลบหนีเรียบร้อย', 'alert-type' => 'success']);
    }

    /**
     * ลบข้อมูล
     */
    public function DeleteEscape($id)
    {
        $escape = Escape::query()
            ->whereHas('client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);

        $clientId = $escape->client_id;

        DB::transaction(function () use ($escape, $clientId) {
            $escape->delete();

            $latestEscape = Escape::query()
                ->where('client_id', $clientId)
                ->orderByDesc('retire_date')
                ->orderByDesc('id')
                ->first();

            if ($latestEscape) {
                $this->syncEscapeActivity($clientId, $latestEscape, 'ข้อมูลการออก/หลบหนีล่าสุด');
            } else {
                CaseActivity::where('client_id', $clientId)
                    ->where('module', 'escape')
                    ->delete();
            }
        });

        return redirect()
            ->route('escape.index', $clientId)
            ->with(['message' => 'ลบข้อมูลการออก/หลบหนีเรียบร้อย', 'alert-type' => 'success']);
    }

    /**
     * คัดลอกข้อมูลเดิมมาเป็นต้นแบบสำหรับสร้างรายการใหม่
     */
    public function CopyEscape($id)
    {
        $escape = Escape::query()
            ->with(['client', 'retire'])
            ->whereHas('client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);

        $client = $escape->client;

        $retires = Retire::query()
            ->orderBy('retire_name')
            ->get();

        $mode = 'copy';

        return view('frontend.client.escape.escape_create', compact('client', 'retires', 'escape', 'mode'));
    }

    /**
     * รายงาน
     */
    public function ReportEscape($id)
    {
        $escape = Escape::query()
            ->with([
                'client',
                'retire',
                'follows' => function ($query) {
                    $query->orderBy('count')->orderBy('trace_date')->orderBy('id');
                },
            ])
            ->whereHas('client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);

        $client = $escape->client;

        return view('frontend.client.escape.escape_report', compact('escape', 'client'));
    }

    /**
     * ทำให้กิจกรรมล่าสุดของโมดูล Escape เหลือหนึ่งรายการเสมอ
     */
    private function syncEscapeActivity(int $clientId, Escape $escape, string $title): void
    {
        CaseActivity::where('client_id', $clientId)
            ->where('module', 'escape')
            ->delete();

        $occurredAt = $escape->retire_date
            ? Carbon::parse($escape->retire_date, 'Asia/Bangkok')->startOfDay()
            : now('Asia/Bangkok');

        CaseActivity::record([
            'client_id' => $clientId,
            'module' => 'escape',
            'type' => 'danger',
            'title' => $title,
            'description' => 'วันที่ออก/หลบหนี: ' . ($escape->retire_date?->format('Y-m-d') ?? '-')
                . ' | รายละเอียด: ' . ($escape->stories ?: '-'),
            'occurred_at' => $occurredAt,
            'icon' => 'bi-box-arrow-right',
            'url' => route('escape.edit', $escape->id),
        ]);
    }
}
