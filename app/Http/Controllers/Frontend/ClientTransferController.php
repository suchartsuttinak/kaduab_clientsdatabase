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
        $this->ensureTransferPermission('view');
        $user = auth()->user();

        $query = ClientTransfer::with([
            'client',
            'fromProject',
            'toProject',
            'requestedBy',
            'approvedBy',
        ])->latest();

        // UNIFIED_ACCESS_SCOPE_V5
        // Admin เห็นทั้งหมด ส่วนผู้ใช้อื่นถ้ามีการเลือกหน่วยงาน ให้เห็นเฉพาะรายการ
        // ที่ต้นทางหรือปลายทางอยู่ในหน่วยงานที่ได้รับมอบหมาย
        if (!$user->isAdmin() && $user->hasProjectRestriction()) {
            $projectIds = $user->assignedProjectIds();

            $query->where(function ($q) use ($projectIds) {
                $q->whereIn('from_project_id', $projectIds)
                    ->orWhereIn('to_project_id', $projectIds);
            });
        }

        $transfers = $query->paginate(20)->withQueryString();

        return view('frontend.client_transfer.index', compact('transfers'));
    }

    public function create(Client $client)
    {
        $this->ensureTransferPermission('create');
        $user = auth()->user();
        $client = Client::forUser($user)->findOrFail($client->id);

        $projects = Project::query()
            ->whereIn('id', $user->accessibleProjectIds())
            ->where('id', '!=', $client->project_id)
            ->orderBy('project_name')
            ->get();

        return view('frontend.client_transfer.create', compact('client', 'projects'));
    }

    public function store(Request $request)
    {
        $this->ensureTransferPermission('create');
        $user = auth()->user();

        $validated = $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'to_project_id' => 'required|exists:projects,id',
            'remark'        => 'nullable|string|max:1000',
        ]);

        $client = Client::forUser($user)->findOrFail($validated['client_id']);

        abort_unless(
            $user->canAccessProject((int) $validated['to_project_id']),
            403,
            'คุณไม่มีสิทธิ์ย้ายผู้รับบริการไปยังหน่วยงาน/โครงการนี้'
        );

        if ((int) $client->project_id === (int) $validated['to_project_id']) {
            return back()
                ->withInput()
                ->with('error', 'ไม่สามารถย้ายไปโครงการเดิมได้');
        }

        $canAutoApprove = $user->hasFormPermission('registration_project_transfer', 'update');

        DB::transaction(function () use ($client, $validated, $canAutoApprove) {
            ClientTransfer::create([
                'client_id'        => $client->id,
                'from_project_id'  => $client->project_id,
                'to_project_id'    => $validated['to_project_id'],
                'transfer_date'    => now()->toDateString(),
                'status'           => $canAutoApprove ? 'approved' : 'pending',
                'requested_by'     => auth()->id(),
                'approved_by'      => $canAutoApprove ? auth()->id() : null,
                'approved_at'      => $canAutoApprove ? now() : null,
                'remark'           => $validated['remark'] ?? null,
            ]);

            if ($canAutoApprove) {
                $client->update([
                    'project_id' => $validated['to_project_id'],
                    'release_status' => 'show',
                ]);
            }
        });

        return redirect()
            ->route('client.transfers')
            ->with(
                'success',
                $canAutoApprove
                    ? 'ย้ายเคสและอนุมัติเรียบร้อยแล้ว'
                    : 'ส่งคำขอย้ายเคสเรียบร้อยแล้ว รอผู้ที่ได้รับสิทธิ์อนุมัติดำเนินการ'
            );
    }

    public function approve($id)
    {
        $this->ensureTransferPermission('update');
        $user = auth()->user();

        DB::transaction(function () use ($id, $user) {
            $transfer = ClientTransfer::lockForUpdate()->findOrFail($id);
            $this->ensureTransferInProjectScope($transfer);

            if ($transfer->status !== 'pending') {
                abort(400, 'รายการนี้ไม่อยู่ในสถานะรออนุมัติ');
            }

            // รายการย้ายผ่าน ensureTransferInProjectScope แล้ว
            // จึงล็อก client โดยตรงเพื่อรองรับผู้อนุมัติฝั่งปลายทางที่ยังไม่ได้เห็น client ก่อนย้าย
            $client = Client::query()
                ->whereKey($transfer->client_id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $user->canAccessProject((int) $transfer->to_project_id),
                403,
                'คุณไม่มีสิทธิ์อนุมัติการย้ายไปยังหน่วยงาน/โครงการนี้'
            );

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
        $this->ensureTransferInProjectScope($transfer);

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

    private function ensureTransferPermission(string $action): void
    {
        $user = auth()->user();

        abort_unless($user, 403);
        abort_unless(
            $user->hasFormPermission('registration_project_transfer', $action),
            403,
            'บัญชีนี้ไม่ได้รับสิทธิ์สำหรับการดำเนินการย้ายโครงการ'
        );
    }

    private function ensureTransferInProjectScope(ClientTransfer $transfer): void
    {
        $user = auth()->user();

        if ($user->isAdmin() || !$user->hasProjectRestriction()) {
            return;
        }

        $allowed = $user->canAccessProject((int) $transfer->from_project_id)
            || $user->canAccessProject((int) $transfer->to_project_id);

        abort_unless($allowed, 403, 'รายการย้ายนี้อยู่นอกขอบเขตหน่วยงาน/โครงการที่ได้รับมอบหมาย');
    }
}
