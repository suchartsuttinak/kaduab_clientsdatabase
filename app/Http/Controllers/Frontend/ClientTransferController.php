<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientTransfer;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientTransferController extends Controller
{
    public function index(Request $request)
    {
        // EPC_PROJECT_TRANSFER_CONTROLLER_V1
        $this->ensureTransferPermission('view');
        $user = auth()->user();

        $query = ClientTransfer::with([
            'client',
            'fromProject',
            'toProject',
            'requestedBy',
            'approvedBy',
        ])->latest();

        // Admin/Executive เป็นบทบาทส่วนกลางและเห็นประวัติการย้ายทุกโครงการ
        // สำหรับบทบาทอื่นในอนาคต รองรับทั้ง relation projects() และ project_id เดี่ยว
        if (!$user->isAdmin() && !$user->isExecutive()) {
            $projectIds = collect();

            if (method_exists($user, 'projects')) {
                $projectIds = $user->projects()->pluck('projects.id');
            } elseif (!empty($user->project_id)) {
                $projectIds = collect([(int) $user->project_id]);
            }

            $query->where(function ($q) use ($projectIds) {
                $q->whereIn('from_project_id', $projectIds)
                    ->orWhereIn('to_project_id', $projectIds);
            });
        }

        $transfers = $query->paginate(20);

        return view('frontend.client_transfer.index', compact('transfers'));
    }

    public function create(Client $client)
    {
        $this->ensureTransferPermission('create');

        $projects = Project::where('id', '!=', $client->project_id)
            ->orderBy('project_name')
            ->get();

        return view('frontend.client_transfer.create', compact('client', 'projects'));
    }

   public function store(Request $request)
{
    $this->ensureTransferPermission('create');

    $validated = $request->validate([
        'client_id'     => 'required|exists:clients,id',
        'to_project_id' => 'required|exists:projects,id',
        'remark'        => 'nullable|string|max:1000',
    ]);

    $client = Client::findOrFail($validated['client_id']);

    if ((int) $client->project_id === (int) $validated['to_project_id']) {
        return back()
            ->withInput()
            ->with('error', 'ไม่สามารถย้ายไปโปรเจ็คเดิมได้');
    }

    DB::transaction(function () use ($client, $validated) {
        ClientTransfer::create([
            'client_id'        => $client->id,
            'from_project_id'  => $client->project_id,
            'to_project_id'    => $validated['to_project_id'],
            'transfer_date'    => now()->toDateString(),
            'status'           => 'approved',
            'requested_by'     => auth()->id(),
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'remark'           => $validated['remark'] ?? null,
        ]);

        $client->update([
            'project_id'      => $validated['to_project_id'],
            'release_status' => 'show',
        ]);
    });

    return redirect()
        ->route('client.transfers')
        ->with('success', 'ย้ายเคสเรียบร้อยแล้ว');
}

    public function approve($id)
    {
        $this->ensureTransferPermission('update');

        DB::transaction(function () use ($id) {
            $transfer = ClientTransfer::lockForUpdate()->findOrFail($id);

            if ($transfer->status !== 'pending') {
                abort(400, 'รายการนี้ไม่อยู่ในสถานะรออนุมัติ');
            }

            $client = Client::lockForUpdate()->findOrFail($transfer->client_id);

            $client->update([
                'project_id' => $transfer->to_project_id,
                'release_status' => 'show',
            ]);

            $transfer->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

            return redirect()
                ->route('client.transfers')
                ->with('success', 'อนุมัติการย้ายเคสเรียบร้อยแล้ว');
        }
        

    public function reject(Request $request, $id)
    {
        $this->ensureTransferPermission('update');

        $request->validate([
            'remark' => 'nullable|string|max:1000',
        ]);

        $transfer = ClientTransfer::findOrFail($id);

        if ($transfer->status !== 'pending') {
            return redirect()
                ->route('client.transfers')
                ->with('error', 'รายการนี้ไม่อยู่ในสถานะรออนุมัติ');
        }

        $transfer->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'remark' => $request->remark ?: $transfer->remark,
        ]);

            return redirect()
            ->route('client.transfers')
            ->with('success', 'ไม่อนุมัติการย้ายเคสเรียบร้อยแล้ว');
        }
    /**
     * ด่านใน Controller สำหรับย้ายโครงการ
     * - Admin ผ่านเสมอ
     * - Executive ต้องผ่าน permission key/action ที่ Admin กำหนด
     * - บทบาทอื่นยังไม่เปิดผ่าน Controller นี้
     */
    private function ensureTransferPermission(string $action): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return;
        }

        if (!$user->isExecutive()) {
            abort(403, 'บัญชีนี้ไม่มีสิทธิ์จัดการการย้ายโครงการ');
        }

        if (!$user->hasFormPermission('registration_project_transfer', $action)) {
            abort(403, 'บัญชีนี้ไม่ได้รับสิทธิ์สำหรับการดำเนินการย้ายโครงการ');
        }
    }
}