<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Operation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Operation::with('user');

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

       if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);

            $query->where(function ($q) use ($keyword) {
                $q->where('subject', 'like', "%{$keyword}%")
                    ->orWhere('result', 'like', "%{$keyword}%")
                    ->orWhere('remark', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('operation_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('operation_date', '<=', $request->end_date);
        }

        $operations = $query
            ->orderByDesc('operation_date')
            ->orderBy('sequence_no')
            ->paginate(10)
            ->withQueryString();

        return view('backend.operations.index', compact('operations'));
    }

    public function create()
    {
        return view('backend.operations.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'operation_date' => ['required', 'date'],
            'subject'        => ['required', 'string', 'max:500'],
            'result'         => ['nullable', 'string'],
            'remark'         => ['nullable', 'string'],
        ], [
            'operation_date.required' => 'กรุณาระบุวันที่',
            'operation_date.date'     => 'รูปแบบวันที่ไม่ถูกต้อง',
            'subject.required'        => 'กรุณาระบุเรื่องที่ดำเนินงาน',
            'subject.max'             => 'เรื่องที่ดำเนินงานต้องไม่เกิน 500 ตัวอักษร',
        ]);

        $this->validateOperationDateByRole($validated['operation_date']);

        DB::transaction(function () use ($validated, $user) {
            $operation = Operation::create([
                'user_id'        => $user->id,
                'operation_date' => $validated['operation_date'],
                'sequence_no'    => 1,
                'subject'        => $validated['subject'],
                'result'         => $validated['result'] ?? null,
                'remark'         => $validated['remark'] ?? null,
            ]);

            $this->reorderSequenceByUserAndYear(
                $operation->user_id,
                Carbon::parse($operation->operation_date)->year
            );
        });

        return redirect()
            ->route('operations.index')
            ->with('success', 'บันทึกรายงานการปฏิบัติงานเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $operation = Operation::with('user')->findOrFail($id);

        $this->authorizeOperation($operation);

        return view('backend.operations.edit', compact('operation'));
    }

   public function update(Request $request, $id)
{
    $operation = Operation::findOrFail($id);

    $this->authorizeOperation($operation);

    $user = auth()->user();

    $oldUserId = $operation->user_id;
    $oldYear = Carbon::parse($operation->operation_date)->year;

    $validated = $request->validate([
        'operation_date' => $user->isAdmin()
            ? ['required', 'date']
            : ['nullable'],
        'subject' => ['required', 'string', 'max:500'],
        'result'  => ['nullable', 'string'],
        'remark'  => ['nullable', 'string'],
    ], [
        'operation_date.required' => 'กรุณาระบุวันที่',
        'operation_date.date'     => 'รูปแบบวันที่ไม่ถูกต้อง',
        'subject.required'        => 'กรุณาระบุเรื่องที่ดำเนินงาน',
        'subject.max'             => 'เรื่องที่ดำเนินงานต้องไม่เกิน 500 ตัวอักษร',
    ]);

    if ($user->isAdmin()) {
        $this->validateOperationDateByRole($validated['operation_date']);
    }

    DB::transaction(function () use ($operation, $validated, $oldUserId, $oldYear, $user) {
        $updateData = [
            'subject' => $validated['subject'],
            'result'  => $validated['result'] ?? null,
            'remark'  => $validated['remark'] ?? null,
        ];

        if ($user->isAdmin()) {
            $updateData['operation_date'] = $validated['operation_date'];
        }

        $operation->update($updateData);

        $newYear = Carbon::parse($operation->operation_date)->year;

        $this->reorderSequenceByUserAndYear($oldUserId, $oldYear);
        $this->reorderSequenceByUserAndYear($operation->user_id, $newYear);
    });

    return redirect()
        ->route('operations.index')
        ->with('success', 'อัปเดตรายงานการปฏิบัติงานเรียบร้อยแล้ว');
}

    public function destroy($id)
    {
        $operation = Operation::findOrFail($id);

        $this->authorizeOperation($operation);

        $userId = $operation->user_id;
        $year = Carbon::parse($operation->operation_date)->year;

        DB::transaction(function () use ($operation, $userId, $year) {
            $operation->delete();
            $this->reorderSequenceByUserAndYear($userId, $year);
        });

        return redirect()
            ->route('operations.index')
            ->with('success', 'ลบรายการเรียบร้อยแล้ว');
    }

   public function dailyReport(Request $request)
    {
        $authUser = auth()->user();

        $query = Operation::with('user');

        if (!$authUser->isAdmin()) {
            $query->where('user_id', $authUser->id);
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);

            $query->where(function ($q) use ($keyword) {
                $q->where('subject', 'like', "%{$keyword}%")
                    ->orWhere('result', 'like', "%{$keyword}%")
                    ->orWhere('remark', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('operation_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('operation_date', '<=', $request->end_date);
        }

        $operations = $query
            ->orderByDesc('operation_date')
            ->orderBy('sequence_no')
            ->get();

        return view('backend.operations.report_daily', compact('operations'));
    }

    private function authorizeOperation(Operation $operation): void
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $operation->user_id !== $user->id) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลนี้');
        }
    }

    private function reorderAllSequences(): void
    {
        $groups = Operation::selectRaw('user_id, YEAR(operation_date) as operation_year')
            ->groupBy('user_id', DB::raw('YEAR(operation_date)'))
            ->get();

        foreach ($groups as $group) {
            $this->reorderSequenceByUserAndYear(
                (int) $group->user_id,
                (int) $group->operation_year
            );
        }
    }

    private function reorderSequenceByUserAndYear(int $userId, int $year): void
    {
        $items = Operation::where('user_id', $userId)
            ->whereYear('operation_date', $year)
            ->orderBy('operation_date')
            ->orderBy('id')
            ->get();

        $sequence = 1;

        foreach ($items as $item) {
            if ((int) $item->sequence_no !== $sequence) {
                $item->update(['sequence_no' => $sequence]);
            }

            $sequence++;
        }
    }

    private function validateOperationDateByRole(string $date): void
    {
        $user = auth()->user();

        $selectedDate = Carbon::parse($date)->startOfDay();
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        if ($selectedDate->gt($today)) {
            abort(422, 'ไม่สามารถเลือกวันที่เกินกว่าวันปัจจุบันได้');
        }

        if ($user->isAdmin()) {
            return;
        }

        if ($selectedDate->lt($yesterday)) {
            abort(422, 'ไม่สามารถบันทึกย้อนหลังเกิน 1 วันได้');
        }
    }
}