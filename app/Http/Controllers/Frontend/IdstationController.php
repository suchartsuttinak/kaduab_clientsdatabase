<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Citizen;
use App\Models\Citizenship;
use App\Models\Client;
use App\Models\Idstation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IdstationController extends Controller
{
    private function ensureIdstationTarget(Client $client): void
    {
        if (($client->target->target_name ?? '') !== 'บุคคลไม่มีสถานะทางทะเบียน') {
            abort(403, 'ผู้รับบริการรายนี้ไม่ใช่กลุ่มเป้าหมายบุคคลไม่มีสถานะทางทะเบียน');
        }
    }

    public function index($clientId)
    {
        $client = Client::forUser(auth()->user())
            ->with('target')
            ->findOrFail($clientId);

        $this->ensureIdstationTarget($client);

        $idstations = Idstation::with(['citizenships', 'citizens', 'creator', 'updater'])
            ->where('client_id', $client->id)
            ->latest('receive_date')
            ->latest('id')
            ->get();

        $citizenships = Citizenship::orderBy('id')->get();
        $citizens = Citizen::orderBy('id')->get();

        return view('frontend.client.idstation.index', compact(
            'client',
            'idstations',
            'citizenships',
            'citizens'
        ));
    }

    public function store(Request $request, $clientId)
    {
        $client = Client::forUser(auth()->user())
            ->with('target')
            ->findOrFail($clientId);

        $this->ensureIdstationTarget($client);

        if (Idstation::where('client_id', $client->id)->exists()) {
            return redirect()
                ->route('idstation.index', $client->id)
                ->with('error', 'ผู้รับบริการรายนี้มีการรับเรื่องด้านสถานะบุคคลแล้ว ไม่สามารถรับเรื่องซ้ำได้');
        }

        $validated = $request->validate([
            'receive_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],

            'citizenship_ids' => ['required', 'array', 'min:1'],
            'citizenship_ids.*' => ['exists:citizenships,id'],

            'detail' => ['nullable', 'string'],
        ], [
            'receive_date.required' => 'กรุณาระบุวันที่รับเรื่อง',
            'receive_date.before_or_equal' => 'วันที่รับเรื่องต้องไม่เกินวันที่ปัจจุบัน',

            'citizenship_ids.required' => 'กรุณาเลือกรายการทางทะเบียนอย่างน้อย 1 รายการ',
            'citizenship_ids.min' => 'กรุณาเลือกรายการทางทะเบียนอย่างน้อย 1 รายการ',
        ]);

        DB::transaction(function () use ($validated, $client) {
            $idstation = Idstation::create([
                'client_id' => $client->id,
                'receive_date' => $validated['receive_date'],
                'detail' => $validated['detail'] ?? null,
                'process_status' => 'processing',
                'received_status_date' => null,
                'remark' => null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $idstation->citizenships()->sync($validated['citizenship_ids']);
            $idstation->citizens()->sync([]);
        });

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

        $validated = $request->validate([
            'receive_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],

            'citizenship_ids' => ['required', 'array', 'min:1'],
            'citizenship_ids.*' => ['exists:citizenships,id'],

            'detail' => ['nullable', 'string'],

            'process_status' => ['required', 'in:processing,received_status'],

            'received_status_date' => [
                'nullable',
                'required_if:process_status,received_status',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],

            'citizen_ids' => [
                'nullable',
                'required_if:process_status,received_status',
                'array',
                'min:1',
            ],
            'citizen_ids.*' => ['exists:citizens,id'],

            'remark' => ['nullable', 'string'],
        ], [
            'receive_date.required' => 'กรุณาระบุวันที่รับเรื่อง',
            'receive_date.before_or_equal' => 'วันที่รับเรื่องต้องไม่เกินวันที่ปัจจุบัน',

            'citizenship_ids.required' => 'กรุณาเลือกรายการทางทะเบียนอย่างน้อย 1 รายการ',
            'citizenship_ids.min' => 'กรุณาเลือกรายการทางทะเบียนอย่างน้อย 1 รายการ',

            'process_status.required' => 'กรุณาเลือกการดำเนินการ',

            'received_status_date.required_if' => 'กรุณาระบุวันที่รับสถานะทางทะเบียน',
            'received_status_date.before_or_equal' => 'วันที่รับสถานะทางทะเบียนต้องไม่เกินวันที่ปัจจุบัน',

            'citizen_ids.required_if' => 'กรุณาเลือกสถานะทางทะเบียนที่ได้รับอย่างน้อย 1 รายการ',
            'citizen_ids.min' => 'กรุณาเลือกสถานะทางทะเบียนที่ได้รับอย่างน้อย 1 รายการ',
        ]);

        DB::transaction(function () use ($validated, $client, $idstation) {
            $processStatus = $validated['process_status'];

            $idstation->update([
                'receive_date' => $validated['receive_date'],
                'detail' => $validated['detail'] ?? null,
                'process_status' => $processStatus,
                'received_status_date' => $processStatus === 'received_status'
                    ? ($validated['received_status_date'] ?? null)
                    : null,
                'remark' => $validated['remark'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            $idstation->citizenships()->sync($validated['citizenship_ids']);

            if ($processStatus === 'received_status') {
                $idstation->citizens()->sync($validated['citizen_ids'] ?? []);

                $client->update([
                    'release_status' => 'refer',
                ]);

                $this->recordReceivedStatusActivity($client);
            } else {
                $idstation->citizens()->sync([]);

                CaseActivity::where('client_id', $client->id)
                    ->where('module', 'idstation')
                    ->delete();
            }
        });

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

        DB::transaction(function () use ($idstation, $client) {
            $idstation->citizenships()->detach();
            $idstation->citizens()->detach();
            $idstation->delete();

            CaseActivity::where('client_id', $client->id)
                ->where('module', 'idstation')
                ->delete();
        });

        return redirect()
            ->route('idstation.index', $client->id)
            ->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    private function recordReceivedStatusActivity(Client $client): void
    {
        CaseActivity::where('client_id', $client->id)
            ->where('module', 'idstation')
            ->delete();

        CaseActivity::record([
            'client_id' => $client->id,
            'module' => 'idstation',
            'type' => 'success',
            'title' => 'ได้รับสถานะทางทะเบียน',
            'description' => 'ได้รับสถานะทางทะเบียนและจำหน่ายผู้รับบริการออกจากระบบ',
            'occurred_at' => now('Asia/Bangkok'),
            'icon' => 'bi-person-check',
            'url' => route('idstation.index', $client->id),
        ]);
    }
}