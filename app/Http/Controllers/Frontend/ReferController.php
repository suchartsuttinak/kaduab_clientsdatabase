<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Refer;
use App\Models\Translate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class ReferController extends Controller
{
    /**
     * แสดงรายการจำหน่ายของผู้รับบริการ
     */
    public function index($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $translates = Translate::query()
            ->orderBy('translate_name')
            ->get();

        $refers = Refer::with(['client', 'translate'])
            ->where('client_id', $client->id)
            ->latest('refer_date')
            ->latest('id')
            ->get();

        $hasActiveRefer = $refers->contains(function ($refer) {
            return in_array($refer->approve_status, ['pending', 'approved'], true);
        });

        $canCreateRefer = !$hasActiveRefer;

        return view('frontend.client.refer.refer_index', compact(
            'translates',
            'refers',
            'client',
            'canCreateRefer'
        ));
    }

    /**
     * บันทึกข้อมูลจำหน่ายใหม่
     */
    public function store(Request $request)
    {
        $request->merge([
            'destination' => trim((string) $request->input('destination')),
            'address'     => trim((string) $request->input('address')),
            'parent_name' => trim((string) $request->input('parent_name')),
            'parent_tel'  => trim((string) $request->input('parent_tel')),
            'member'      => trim((string) $request->input('member')),
            'teacher'     => trim((string) $request->input('teacher')),
            'remark'      => trim((string) $request->input('remark')),
        ]);

        $validated = $request->validate([
            'refer_date'          => ['required', 'date', 'before_or_equal:' . now('Asia/Bangkok')->toDateString()],
            'translate_id'        => ['required', 'integer', 'exists:translates,id'],
            'destination'         => ['required', 'string', 'max:255'],
            'address'             => ['required', 'string', 'max:2000'],
            'guardian'            => ['required', Rule::in(['มี', 'ไม่มี'])],
            'parent_name'         => ['nullable', 'string', 'max:255', 'required_if:guardian,มี'],
            'parent_tel'          => ['nullable', 'string', 'max:50'],
            'member'              => ['nullable', 'string', 'max:255'],
            'teacher'             => ['required', 'string', 'max:255'],
            'committee_result'    => ['required', Rule::in(['ผ่าน', 'ไม่ผ่าน'])],
            'meeting_report_file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:10240',
                'required_if:committee_result,ผ่าน',
                'prohibited_unless:committee_result,ผ่าน',
            ],
            'remark'              => ['nullable', 'string', 'max:3000'],
            'client_id'           => ['required', 'integer', 'exists:clients,id'],
        ], [
            'refer_date.required'                => 'กรุณาระบุวันที่นำส่ง',
            'refer_date.date'                    => 'รูปแบบวันที่นำส่งไม่ถูกต้อง',
            'refer_date.before_or_equal'         => 'วันที่นำส่งต้องไม่เกินวันที่ปัจจุบัน',
            'translate_id.required'              => 'กรุณาเลือกสาเหตุการจำหน่าย',
            'translate_id.integer'               => 'ข้อมูลสาเหตุการจำหน่ายไม่ถูกต้อง',
            'translate_id.exists'                => 'ไม่พบข้อมูลสาเหตุการจำหน่ายที่เลือก',
            'destination.required'               => 'กรุณาระบุชื่อสถานที่นำส่ง',
            'destination.max'                    => 'ชื่อสถานที่นำส่งต้องไม่เกิน 255 ตัวอักษร',
            'address.required'                   => 'กรุณาระบุที่อยู่ของสถานที่นำส่ง',
            'address.max'                        => 'ที่อยู่ต้องไม่เกิน 2,000 ตัวอักษร',
            'guardian.required'                  => 'กรุณาเลือกว่ามีผู้ดูแลหรือไม่',
            'guardian.in'                        => 'ข้อมูลผู้ดูแลที่เลือกไม่ถูกต้อง',
            'parent_name.required_if'            => 'กรณีมีผู้ดูแล กรุณาระบุชื่อผู้รับตัว',
            'parent_name.max'                    => 'ชื่อผู้รับตัวต้องไม่เกิน 255 ตัวอักษร',
            'parent_tel.max'                     => 'หมายเลขโทรศัพท์ต้องไม่เกิน 50 ตัวอักษร',
            'member.max'                         => 'ความสัมพันธ์ต้องไม่เกิน 255 ตัวอักษร',
            'teacher.required'                   => 'กรุณาระบุชื่อผู้นำส่ง',
            'teacher.max'                        => 'ชื่อผู้นำส่งต้องไม่เกิน 255 ตัวอักษร',
            'committee_result.required'          => 'กรุณาเลือกผลคณะกรรมการฯ',
            'committee_result.in'                => 'ค่าผลคณะกรรมการฯ ไม่ถูกต้อง',
            'meeting_report_file.required_if'    => 'กรณีคณะกรรมการฯ มีมติ “ผ่าน” กรุณาแนบรายงานการประชุม PDF',
            'meeting_report_file.prohibited_unless' => 'สามารถแนบรายงานการประชุมได้เฉพาะกรณีผลคณะกรรมการฯ เป็น “ผ่าน”',
            'meeting_report_file.file'           => 'ไฟล์รายงานการประชุมไม่ถูกต้อง',
            'meeting_report_file.mimes'          => 'แนบรายงานการประชุมได้เฉพาะไฟล์ PDF เท่านั้น',
            'meeting_report_file.max'            => 'ไฟล์ PDF ต้องมีขนาดไม่เกิน 10 MB',
            'remark.max'                         => 'หมายเหตุต้องไม่เกิน 3,000 ตัวอักษร',
            'client_id.required'                 => 'กรุณาเลือกผู้รับบริการ',
            'client_id.integer'                  => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',
            'client_id.exists'                   => 'ไม่พบข้อมูลผู้รับบริการ',
        ]);

        // ตรวจสิทธิ์ก่อนเริ่ม Transaction เพื่อไม่เปิดเผยหรือแก้ไขข้อมูลข้ามสิทธิ์
        $authorizedClient = Client::forUser(auth()->user())
            ->findOrFail($validated['client_id']);

        $canAutoApprove = auth()->check()
            && in_array(auth()->user()->role, ['admin', 'executive'], true);

        $uploadedAbsolutePath = null;

        try {
            $result = DB::transaction(function () use (
                $request,
                $validated,
                $authorizedClient,
                $canAutoApprove,
                &$uploadedAbsolutePath
            ) {
                // ล็อกแถว client เพื่อกันการบันทึกซ้ำพร้อมกันจากหลาย request
                $client = Client::query()
                    ->whereKey($authorizedClient->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $activeRefer = Refer::query()
                    ->where('client_id', $client->id)
                    ->whereIn('approve_status', ['pending', 'approved'])
                    ->latest('id')
                    ->first();

                if ($activeRefer) {
                    $expectedReleaseStatus = $activeRefer->approve_status === 'approved'
                        ? 'refer'
                        : 'pending_refer';

                    if ($client->release_status !== $expectedReleaseStatus) {
                        $client->update(['release_status' => $expectedReleaseStatus]);
                    }

                    return [
                        'duplicate' => true,
                        'message'   => $activeRefer->approve_status === 'approved'
                            ? 'มีรายการจำหน่ายที่อนุมัติแล้วอยู่แล้ว ไม่สามารถบันทึกซ้ำได้'
                            : 'มีรายการจำหน่ายรออนุมัติอยู่แล้ว ไม่สามารถบันทึกซ้ำได้',
                    ];
                }

                $payload = $validated;

                if ($payload['guardian'] === 'ไม่มี') {
                    $payload['parent_name'] = null;
                    $payload['parent_tel'] = null;
                    $payload['member'] = null;
                }

                if ($request->hasFile('meeting_report_file')) {
                    $destinationPath = public_path('uploads/refer_meeting_reports');
                    File::ensureDirectoryExists($destinationPath, 0775, true);

                    $filename = sprintf(
                        'refer_meeting_%d_%s_%s.pdf',
                        $client->id,
                        now('Asia/Bangkok')->format('Ymd_His'),
                        Str::lower(Str::random(12))
                    );

                    $request->file('meeting_report_file')->move($destinationPath, $filename);
                    $uploadedAbsolutePath = $destinationPath . DIRECTORY_SEPARATOR . $filename;
                    $payload['meeting_report_file'] = $filename;
                } else {
                    $payload['meeting_report_file'] = null;
                }

                $payload['approve_status'] = $canAutoApprove ? 'approved' : 'pending';
                $payload['created_by'] = auth()->id();
                $payload['approved_by'] = $canAutoApprove ? auth()->id() : null;
                $payload['approved_at'] = $canAutoApprove ? now('Asia/Bangkok') : null;

                $refer = Refer::create($payload);

                $client->update([
                    'release_status' => $canAutoApprove ? 'refer' : 'pending_refer',
                ]);

                CaseActivity::where('client_id', $client->id)
                    ->where('module', 'refer')
                    ->delete();

                CaseActivity::record([
                    'client_id'   => $client->id,
                    'module'      => 'refer',
                    'type'        => $canAutoApprove ? 'danger' : 'warning',
                    'title'       => $canAutoApprove
                        ? 'อนุมัติการจำหน่ายผู้รับบริการ'
                        : 'ส่งคำขอจำหน่ายผู้รับบริการ',
                    'description' => 'ผลคณะกรรมการ: ' . ($refer->committee_result ?: '-')
                        . ' | ปลายทาง: ' . ($refer->destination ?: '-')
                        . ' | ผู้นำส่ง: ' . ($refer->teacher ?: '-'),
                    'occurred_at' => now('Asia/Bangkok'),
                    'icon'        => $canAutoApprove
                        ? 'bi-box-arrow-right'
                        : 'bi-hourglass-split',
                    'url'         => route('refers.index', $client->id),
                ]);

                return [
                    'duplicate' => false,
                    'client_id' => $client->id,
                ];
            });
        } catch (Throwable $exception) {
            if ($uploadedAbsolutePath && File::exists($uploadedAbsolutePath)) {
                File::delete($uploadedAbsolutePath);
            }

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'system' => 'ไม่สามารถบันทึกข้อมูลการจำหน่ายได้ กรุณาลองใหม่อีกครั้ง',
                ]);
        }

        if ($result['duplicate']) {
            return redirect()->route('refers.index', $authorizedClient->id)->with([
                'message'    => $result['message'],
                'alert-type' => 'warning',
            ]);
        }

        return redirect()->route('refers.index', $result['client_id'])->with([
            'message'    => $canAutoApprove
                ? 'บันทึกและอนุมัติการจำหน่ายเรียบร้อยแล้ว'
                : 'บันทึกการจำหน่ายเรียบร้อยแล้ว และส่งรออนุมัติแล้ว',
            'alert-type' => 'success',
        ]);
    }

    /**
     * อนุมัติการจำหน่าย
     */
    public function approve($id)
    {
        abort_unless(
            auth()->check() && in_array(auth()->user()->role, ['admin', 'executive'], true),
            403,
            'คุณไม่มีสิทธิ์อนุมัติการจำหน่าย'
        );

        $accessibleRefer = Refer::query()
            ->whereKey($id)
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->firstOrFail();

        $result = DB::transaction(function () use ($accessibleRefer) {
            $refer = Refer::query()
                ->whereKey($accessibleRefer->id)
                ->lockForUpdate()
                ->firstOrFail();

            $client = Client::query()
                ->whereKey($refer->client_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($refer->approve_status === 'approved') {
                return ['ok' => false, 'client_id' => $client->id, 'message' => 'รายการนี้ถูกอนุมัติแล้ว'];
            }

            if ($refer->approve_status !== 'pending') {
                return ['ok' => false, 'client_id' => $client->id, 'message' => 'ไม่สามารถอนุมัติรายการนี้ได้'];
            }

            if ($refer->committee_result === 'ผ่าน' && empty($refer->meeting_report_file)) {
                return [
                    'ok' => false,
                    'client_id' => $client->id,
                    'message' => 'รายการนี้มีผลคณะกรรมการ “ผ่าน” แต่ยังไม่มีไฟล์รายงานการประชุม จึงยังอนุมัติไม่ได้',
                ];
            }

            $anotherApproved = Refer::query()
                ->where('client_id', $client->id)
                ->where('approve_status', 'approved')
                ->where('id', '!=', $refer->id)
                ->exists();

            if ($anotherApproved) {
                return [
                    'ok' => false,
                    'client_id' => $client->id,
                    'message' => 'ผู้รับบริการรายนี้มีรายการจำหน่ายที่อนุมัติแล้วอยู่แล้ว',
                ];
            }

            $refer->update([
                'approve_status' => 'approved',
                'approved_by'    => auth()->id(),
                'approved_at'    => now('Asia/Bangkok'),
            ]);

            $client->update([
                'release_status' => 'refer',
            ]);

            CaseActivity::where('client_id', $client->id)
                ->where('module', 'refer')
                ->delete();

            CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'refer',
                'type'        => 'danger',
                'title'       => 'อนุมัติการจำหน่ายผู้รับบริการ',
                'description' => 'ผู้อนุมัติ: ' . (auth()->user()->name ?? '-')
                    . ' | วันที่อนุมัติ: ' . now('Asia/Bangkok')->format('d/m/Y H:i'),
                'occurred_at' => now('Asia/Bangkok'),
                'icon'        => 'bi-check-circle',
                'url'         => route('refers.index', $client->id),
            ]);

            return ['ok' => true, 'client_id' => $client->id];
        }, 3);

        return redirect()->route('refers.index', $result['client_id'])->with([
            'message'    => $result['ok'] ? 'อนุมัติการจำหน่ายเรียบร้อยแล้ว' : $result['message'],
            'alert-type' => $result['ok'] ? 'success' : 'warning',
        ]);
    }

    /**
     * ยกเลิกรายการจำหน่ายและคืนสถานะผู้รับบริการ
     */
    public function restore($id)
    {
        abort_unless(
            auth()->check() && in_array(auth()->user()->role, ['admin', 'executive'], true),
            403,
            'คุณไม่มีสิทธิ์คืนสถานะ'
        );

        $accessibleRefer = Refer::query()
            ->whereKey($id)
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->firstOrFail();

        $result = DB::transaction(function () use ($accessibleRefer) {
            $refer = Refer::query()
                ->whereKey($accessibleRefer->id)
                ->lockForUpdate()
                ->firstOrFail();

            $client = Client::query()
                ->whereKey($refer->client_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($refer->approve_status, ['pending', 'approved'], true)) {
                return [
                    'ok' => false,
                    'client_id' => $client->id,
                    'message' => 'รายการนี้ถูกยกเลิกหรือคืนสถานะแล้ว',
                ];
            }

            $previousStatus = $refer->approve_status;

            $refer->update([
                'approve_status' => 'cancelled',
                'approved_by'    => null,
                'approved_at'    => null,
            ]);

            // รองรับข้อมูลเก่าที่อาจมีหลายรายการ โดยกำหนดสถานะจากรายการที่ยัง active จริง
            $remainingActive = Refer::query()
                ->where('client_id', $client->id)
                ->whereIn('approve_status', ['pending', 'approved'])
                ->latest('id')
                ->first();

            $newReleaseStatus = match ($remainingActive?->approve_status) {
                'approved' => 'refer',
                'pending'  => 'pending_refer',
                default    => 'show',
            };

            $client->update([
                'release_status' => $newReleaseStatus,
            ]);

            CaseActivity::where('client_id', $client->id)
                ->where('module', 'refer')
                ->delete();

            CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'refer',
                'type'        => 'success',
                'title'       => $previousStatus === 'pending'
                    ? 'ยกเลิกคำขอจำหน่ายผู้รับบริการ'
                    : 'คืนสถานะผู้รับบริการกลับเข้าสู่ระบบ',
                'description' => $newReleaseStatus === 'show'
                    ? 'ยกเลิกสถานะการจำหน่าย และคืนผู้รับบริการกลับมาแสดงในระบบ'
                    : 'ยกเลิกรายการนี้แล้ว และปรับสถานะตามรายการจำหน่ายที่ยังมีผลอยู่',
                'occurred_at' => now('Asia/Bangkok'),
                'icon'        => 'bi-arrow-counterclockwise',
                'url'         => route('refers.index', $client->id),
            ]);

            return [
                'ok' => true,
                'client_id' => $client->id,
                'client_name' => $client->fullname ?? $client->name ?? '-',
            ];
        }, 3);

        if (request()->ajax()) {
            return response()->json([
                'message' => $result['ok']
                    ? 'คืนสถานะเรียบร้อยแล้ว'
                    : $result['message'],
            ], $result['ok'] ? 200 : 422);
        }

        return redirect()->route('refers.index', $result['client_id'])->with([
            'message'    => $result['ok']
                ? 'คืนสถานะ ' . $result['client_name'] . ' เรียบร้อยแล้ว'
                : $result['message'],
            'alert-type' => $result['ok'] ? 'success' : 'warning',
        ]);
    }

    /**
     * รายงานรายบุคคล
     */
    public function report($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $refers = Refer::with(['client', 'translate'])
            ->where('client_id', $client->id)
            ->latest('refer_date')
            ->latest('id')
            ->get();

        return view('frontend.client.refer.refer_report', compact('client', 'refers'));
    }

    /**
     * ตารางการจำหน่ายรวม
     */
    public function allRefers(Request $request)
    {
        abort_unless(
            auth()->check() && in_array(auth()->user()->role, ['admin', 'executive'], true),
            403,
            'คุณไม่มีสิทธิ์เข้าถึงตารางการจำหน่ายรวม'
        );

        $validator = Validator::make($request->all(), [
            'date_from'        => ['nullable', 'date', 'before_or_equal:' . now('Asia/Bangkok')->toDateString()],
            'date_to'          => ['nullable', 'date', 'after_or_equal:date_from', 'before_or_equal:' . now('Asia/Bangkok')->toDateString()],
            'year'             => ['nullable', 'integer', 'min:2000', 'max:' . now('Asia/Bangkok')->year],
            'month'            => ['nullable', 'integer', 'between:1,12'],
            'approve_status'   => ['nullable', Rule::in(['approved', 'pending', 'cancelled'])],
            'committee_result' => ['nullable', Rule::in(['ผ่าน', 'ไม่ผ่าน'])],
            'keyword'          => ['nullable', 'string', 'max:255'],
        ], [
            'date_from.date'         => 'รูปแบบวันที่เริ่มต้นไม่ถูกต้อง',
            'date_from.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันที่ปัจจุบัน',
            'date_to.date'           => 'รูปแบบวันที่สิ้นสุดไม่ถูกต้อง',
            'date_to.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
            'date_to.before_or_equal'   => 'วันที่สิ้นสุดต้องไม่เกินวันที่ปัจจุบัน',
            'year.integer'           => 'รูปแบบปีไม่ถูกต้อง',
            'year.min'               => 'ปีที่เลือกไม่ถูกต้อง',
            'year.max'               => 'ปีต้องไม่เกินปีปัจจุบัน',
            'month.integer'          => 'รูปแบบเดือนไม่ถูกต้อง',
            'month.between'          => 'เดือนต้องอยู่ระหว่าง 1 ถึง 12',
            'approve_status.in'      => 'สถานะอนุมัติไม่ถูกต้อง',
            'committee_result.in'    => 'ผลคณะกรรมการฯ ไม่ถูกต้อง',
            'keyword.max'            => 'คำค้นหาต้องไม่เกิน 255 ตัวอักษร',
        ]);

        if ($validator->fails()) {
            return redirect()->route('refers.all')
                ->withErrors($validator)
                ->withInput();
        }

        $accessibleQuery = Refer::query()
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            });

        $hasAnyRefers = (clone $accessibleQuery)->exists();
        $baseQuery = $this->applyAllReferFilters($accessibleQuery, $request);

        $refers = (clone $baseQuery)
            ->with(['client', 'translate'])
            ->latest('refer_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $summaryRow = (clone $baseQuery)
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("SUM(CASE WHEN approve_status = 'approved' THEN 1 ELSE 0 END) AS approved")
            ->selectRaw("SUM(CASE WHEN approve_status = 'pending' THEN 1 ELSE 0 END) AS pending")
            ->selectRaw("SUM(CASE WHEN approve_status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled")
            ->first();

        $summary = [
            'total'     => (int) ($summaryRow->total ?? 0),
            'approved'  => (int) ($summaryRow->approved ?? 0),
            'pending'   => (int) ($summaryRow->pending ?? 0),
            'cancelled' => (int) ($summaryRow->cancelled ?? 0),
        ];

        return view('frontend.client.refer.refer_all', compact('refers', 'summary', 'hasAnyRefers'));
    }

    /**
     * ใช้ filter ชุดเดียวกันทั้งรายการและสรุปผล ลดโค้ดซ้ำและป้องกันยอดไม่ตรงกัน
     */
    private function applyAllReferFilters($query, Request $request)
    {
        $query
            ->when($request->filled('date_from'), function ($builder) use ($request) {
                $builder->whereDate('refer_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($builder) use ($request) {
                $builder->whereDate('refer_date', '<=', $request->date_to);
            })
            ->when($request->filled('year'), function ($builder) use ($request) {
                $builder->whereYear('refer_date', (int) $request->year);
            })
            ->when($request->filled('month'), function ($builder) use ($request) {
                $builder->whereMonth('refer_date', (int) $request->month);
            })
            ->when($request->filled('approve_status'), function ($builder) use ($request) {
                $builder->where('approve_status', $request->approve_status);
            })
            ->when($request->filled('committee_result'), function ($builder) use ($request) {
                $builder->where('committee_result', $request->committee_result);
            });

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->keyword);

            $query->where(function ($builder) use ($keyword) {
                $builder->where('destination', 'like', "%{$keyword}%")
                    ->orWhere('teacher', 'like', "%{$keyword}%")
                    ->orWhere('parent_name', 'like', "%{$keyword}%")
                    ->orWhere('parent_tel', 'like', "%{$keyword}%")
                    ->orWhere('member', 'like', "%{$keyword}%")
                    ->orWhere('remark', 'like', "%{$keyword}%")
                    ->orWhereHas('client', function ($clientQuery) use ($keyword) {
                        $clientQuery->where('first_name', 'like', "%{$keyword}%")
                            ->orWhere('last_name', 'like', "%{$keyword}%")
                            ->orWhereRaw(
                                "CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')) LIKE ?",
                                ["%{$keyword}%"]
                            );

                        if (ctype_digit($keyword)) {
                            $clientQuery->orWhereKey((int) $keyword);
                        }
                    })
                    ->orWhereHas('translate', function ($translateQuery) use ($keyword) {
                        $translateQuery->where('translate_name', 'like', "%{$keyword}%");
                    });
            });
        }

        return $query;
    }
}
