<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Citizen;
use App\Models\Citizenship;
use App\Models\Client;
use App\Models\Idstation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IdstationController extends Controller
{
    private const TIMEZONE = 'Asia/Bangkok';

    private const TARGET_NAME = 'บุคคลไม่มีสถานะทางทะเบียน';

    private const STATUS_PROCESSING = 'processing';

    private const STATUS_RECEIVED = 'received_status';

    private function ensureIdstationTarget(Client $client): void
    {
        if (($client->target->target_name ?? '') !== self::TARGET_NAME) {
            abort(403, 'ผู้รับบริการรายนี้ไม่ใช่กลุ่มเป้าหมายบุคคลไม่มีสถานะทางทะเบียน');
        }
    }

    public function index(int $clientId)
    {
        $client = Client::forUser(auth()->user())
            ->with('target')
            ->findOrFail($clientId);

        $this->ensureIdstationTarget($client);

        $idstations = Idstation::query()
            ->with([
                'citizenships',
                'citizens',
                'creator:id,name',
                'updater:id,name',
            ])
            ->where('client_id', $client->id)
            ->latest('receive_date')
            ->latest('id')
            ->get();

        $citizenships = Citizenship::query()
            ->orderBy('id')
            ->get();

        // ใช้เฉพาะหน้าแก้ไข จึงไม่ต้อง query เมื่อยังไม่มีรายการ
        $citizens = $idstations->isNotEmpty()
            ? Citizen::query()->orderBy('id')->get()
            : collect();

        return view('frontend.client.idstation.index', compact(
            'client',
            'idstations',
            'citizenships',
            'citizens'
        ));
    }

    public function store(Request $request, int $clientId)
    {
        $client = Client::forUser(auth()->user())
            ->with('target')
            ->findOrFail($clientId);

        $this->ensureIdstationTarget($client);
        $this->normalizeNullableText($request, ['detail']);

        $validated = $request->validate([
            'receive_date' => [
                'bail',
                'required',
                'date_format:Y-m-d',
                'before_or_equal:' . now(self::TIMEZONE)->toDateString(),
            ],
            'citizenship_ids' => ['required', 'array', 'min:1'],
            'citizenship_ids.*' => [
                'bail',
                'integer',
                'distinct',
                'exists:citizenships,id',
            ],
            'detail' => ['nullable', 'string'],
            '_form_context' => ['nullable', 'string'],
        ], [
            'receive_date.required' => 'กรุณาระบุวันที่รับเรื่อง',
            'receive_date.date_format' => 'รูปแบบวันที่รับเรื่องไม่ถูกต้อง',
            'receive_date.before_or_equal' => 'วันที่รับเรื่องต้องไม่เกินวันที่ปัจจุบัน',
            'citizenship_ids.required' => 'กรุณาเลือกรายการทางทะเบียนอย่างน้อย 1 รายการ',
            'citizenship_ids.array' => 'รูปแบบรายการทางทะเบียนไม่ถูกต้อง',
            'citizenship_ids.min' => 'กรุณาเลือกรายการทางทะเบียนอย่างน้อย 1 รายการ',
            'citizenship_ids.*.integer' => 'รายการทางทะเบียนไม่ถูกต้อง',
            'citizenship_ids.*.distinct' => 'พบรายการทางทะเบียนซ้ำกัน',
            'citizenship_ids.*.exists' => 'ไม่พบรายการทางทะเบียนที่เลือกในระบบ',
            'detail.string' => 'รายละเอียดต้องเป็นข้อความ',
        ]);

        DB::transaction(function () use ($validated, $client): void {
            // ล็อกผู้รับบริการ ป้องกันการกดบันทึกพร้อมกันจนเกิดข้อมูลซ้ำ
            Client::query()
                ->whereKey($client->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (Idstation::query()->where('client_id', $client->id)->exists()) {
                throw ValidationException::withMessages([
                    'receive_date' => 'ผู้รับบริการรายนี้มีการรับเรื่องด้านสถานะบุคคลแล้ว ไม่สามารถรับเรื่องซ้ำได้',
                ]);
            }

            $idstation = Idstation::create([
                'client_id' => $client->id,
                'receive_date' => $validated['receive_date'],
                'detail' => $validated['detail'] ?? null,
                'process_status' => self::STATUS_PROCESSING,
                'received_status_date' => null,
                'remark' => null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $idstation->citizenships()->sync($validated['citizenship_ids']);
            $idstation->citizens()->sync([]);
        }, 3);

        return redirect()
            ->route('idstation.index', $client->id)
            ->with('success', 'บันทึกรับเรื่องด้านสถานะบุคคลเรียบร้อยแล้ว');
    }

    public function update(Request $request, Idstation $idstation)
    {
        $client = Client::forUser(auth()->user())
            ->with('target')
            ->findOrFail($idstation->client_id);

        $this->ensureIdstationTarget($client);
        $this->normalizeNullableText($request, ['detail', 'remark']);

        $validated = $request->validate([
            'receive_date' => [
                'bail',
                'required',
                'date_format:Y-m-d',
                'before_or_equal:' . now(self::TIMEZONE)->toDateString(),
            ],
            'citizenship_ids' => ['required', 'array', 'min:1'],
            'citizenship_ids.*' => [
                'bail',
                'integer',
                'distinct',
                'exists:citizenships,id',
            ],
            'detail' => ['nullable', 'string'],
            'process_status' => [
                'required',
                'in:' . self::STATUS_PROCESSING . ',' . self::STATUS_RECEIVED,
            ],
            'received_status_date' => [
                'exclude_unless:process_status,' . self::STATUS_RECEIVED,
                'required',
                'date_format:Y-m-d',
                'after_or_equal:receive_date',
                'before_or_equal:' . now(self::TIMEZONE)->toDateString(),
            ],
            'citizen_ids' => [
                'exclude_unless:process_status,' . self::STATUS_RECEIVED,
                'required',
                'array',
                'min:1',
            ],
            'citizen_ids.*' => [
                'bail',
                'integer',
                'distinct',
                'exists:citizens,id',
            ],
            'remark' => [
                'exclude_unless:process_status,' . self::STATUS_RECEIVED,
                'nullable',
                'string',
            ],
            '_form_context' => ['nullable', 'string'],
        ], [
            'receive_date.required' => 'กรุณาระบุวันที่รับเรื่อง',
            'receive_date.date_format' => 'รูปแบบวันที่รับเรื่องไม่ถูกต้อง',
            'receive_date.before_or_equal' => 'วันที่รับเรื่องต้องไม่เกินวันที่ปัจจุบัน',
            'citizenship_ids.required' => 'กรุณาเลือกรายการทางทะเบียนอย่างน้อย 1 รายการ',
            'citizenship_ids.array' => 'รูปแบบรายการทางทะเบียนไม่ถูกต้อง',
            'citizenship_ids.min' => 'กรุณาเลือกรายการทางทะเบียนอย่างน้อย 1 รายการ',
            'citizenship_ids.*.integer' => 'รายการทางทะเบียนไม่ถูกต้อง',
            'citizenship_ids.*.distinct' => 'พบรายการทางทะเบียนซ้ำกัน',
            'citizenship_ids.*.exists' => 'ไม่พบรายการทางทะเบียนที่เลือกในระบบ',
            'detail.string' => 'รายละเอียดต้องเป็นข้อความ',
            'process_status.required' => 'กรุณาเลือกการดำเนินการ',
            'process_status.in' => 'สถานะการดำเนินการไม่ถูกต้อง',
            'received_status_date.required' => 'กรุณาระบุวันที่รับสถานะทางทะเบียน',
            'received_status_date.date_format' => 'รูปแบบวันที่รับสถานะทางทะเบียนไม่ถูกต้อง',
            'received_status_date.after_or_equal' => 'วันที่รับสถานะทางทะเบียนต้องไม่น้อยกว่าวันที่รับเรื่อง',
            'received_status_date.before_or_equal' => 'วันที่รับสถานะทางทะเบียนต้องไม่เกินวันที่ปัจจุบัน',
            'citizen_ids.required' => 'กรุณาเลือกสถานะทางทะเบียนที่ได้รับอย่างน้อย 1 รายการ',
            'citizen_ids.array' => 'รูปแบบสถานะทางทะเบียนที่ได้รับไม่ถูกต้อง',
            'citizen_ids.min' => 'กรุณาเลือกสถานะทางทะเบียนที่ได้รับอย่างน้อย 1 รายการ',
            'citizen_ids.*.integer' => 'สถานะทางทะเบียนที่ได้รับไม่ถูกต้อง',
            'citizen_ids.*.distinct' => 'พบสถานะทางทะเบียนที่ได้รับซ้ำกัน',
            'citizen_ids.*.exists' => 'ไม่พบสถานะทางทะเบียนที่เลือกในระบบ',
            'remark.string' => 'รายละเอียดเพิ่มเติมต้องเป็นข้อความ',
        ]);

        DB::transaction(function () use ($validated, $client, $idstation): void {
            $lockedIdstation = Idstation::query()
                ->whereKey($idstation->id)
                ->where('client_id', $client->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedClient = Client::query()
                ->whereKey($client->id)
                ->lockForUpdate()
                ->firstOrFail();

            $wasReceived = $lockedIdstation->process_status === self::STATUS_RECEIVED;
            $processStatus = $validated['process_status'];
            $isReceived = $processStatus === self::STATUS_RECEIVED;

            $lockedIdstation->update([
                'receive_date' => $validated['receive_date'],
                'detail' => $validated['detail'] ?? null,
                'process_status' => $processStatus,
                'received_status_date' => $isReceived
                    ? $validated['received_status_date']
                    : null,
                // remark เป็นข้อมูลหลังได้รับสถานะ จึงต้องล้างเมื่อกลับเป็นระหว่างดำเนินการ
                'remark' => $isReceived
                    ? ($validated['remark'] ?? null)
                    : null,
                'updated_by' => auth()->id(),
            ]);

            $lockedIdstation->citizenships()->sync($validated['citizenship_ids']);

            if ($isReceived) {
                $lockedIdstation->citizens()->sync($validated['citizen_ids']);
                $lockedClient->update(['release_status' => 'refer']);

                $lockedIdstation->load('citizens');
                $this->recordReceivedStatusActivity($lockedClient, $lockedIdstation);
            } else {
                $lockedIdstation->citizens()->sync([]);

                CaseActivity::query()
                    ->where('client_id', $lockedClient->id)
                    ->where('module', 'idstation')
                    ->delete();

                // คืนสถานะเฉพาะกรณีที่เปลี่ยนกลับจาก “ได้รับสถานะแล้ว”
                if ($wasReceived && $lockedClient->release_status === 'refer') {
                    $lockedClient->update(['release_status' => 'show']);
                }
            }
        }, 3);

        return redirect()
            ->route('idstation.index', $client->id)
            ->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
    }

    public function destroy(Idstation $idstation)
    {
        $client = Client::forUser(auth()->user())
            ->with('target')
            ->findOrFail($idstation->client_id);

        $this->ensureIdstationTarget($client);

        DB::transaction(function () use ($idstation, $client): void {
            $lockedIdstation = Idstation::query()
                ->whereKey($idstation->id)
                ->where('client_id', $client->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedClient = Client::query()
                ->whereKey($client->id)
                ->lockForUpdate()
                ->firstOrFail();

            $wasReceived = $lockedIdstation->process_status === self::STATUS_RECEIVED;

            $lockedIdstation->citizenships()->detach();
            $lockedIdstation->citizens()->detach();
            $lockedIdstation->delete();

            CaseActivity::query()
                ->where('client_id', $lockedClient->id)
                ->where('module', 'idstation')
                ->delete();

            if ($wasReceived && $lockedClient->release_status === 'refer') {
                $lockedClient->update(['release_status' => 'show']);
            }
        }, 3);

        return redirect()
            ->route('idstation.index', $client->id)
            ->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    private function recordReceivedStatusActivity(Client $client, Idstation $idstation): void
    {
        CaseActivity::query()
            ->where('client_id', $client->id)
            ->where('module', 'idstation')
            ->delete();

        $receivedNames = $idstation->citizens
            ->map(fn ($citizen) => $citizen->citizen_name ?? $citizen->name ?? null)
            ->filter()
            ->implode(', ');

        $description = 'ได้รับสถานะทางทะเบียนและจำหน่ายผู้รับบริการออกจากระบบ';

        if ($receivedNames !== '') {
            $description .= ' | สถานะที่ได้รับ: ' . $receivedNames;
        }

        $occurredAt = $idstation->received_status_date
            ? Carbon::parse($idstation->received_status_date, self::TIMEZONE)->startOfDay()
            : now(self::TIMEZONE);

        CaseActivity::record([
            'client_id' => $client->id,
            'module' => 'idstation',
            'type' => 'success',
            'title' => 'ได้รับสถานะทางทะเบียน',
            'description' => $description,
            'occurred_at' => $occurredAt,
            'icon' => 'bi-person-check',
            'url' => route('idstation.index', $client->id),
        ]);
    }

    private function normalizeNullableText(Request $request, array $fields): void
    {
        $normalized = [];

        foreach ($fields as $field) {
            if (!$request->exists($field)) {
                continue;
            }

            $value = trim((string) $request->input($field));
            $normalized[$field] = $value !== '' ? $value : null;
        }

        if ($normalized !== []) {
            $request->merge($normalized);
        }
    }
}
