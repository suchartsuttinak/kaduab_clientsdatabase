<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\HelpSession;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class HelpSessionController extends Controller
{
    public function show(Request $request, Client $client): View|RedirectResponse
    {
        $client = $this->authorizedClient($client);

        $validator = $this->filterValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('help_sessions.show', $client->id)
                ->withErrors($validator, 'filter')
                ->withInput();
        }

        $filters = $validator->validated();

        $hasAnySessions = HelpSession::query()
            ->where('client_id', $client->id)
            ->exists();

        $totalSessionCount = HelpSession::query()
            ->where('client_id', $client->id)
            ->count();

        $query = HelpSession::query()
            ->where('client_id', $client->id)
            ->with([
                'items' => fn ($itemsQuery) => $itemsQuery->orderBy('id'),
            ])
            ->orderByDesc('help_date')
            ->orderByDesc('id');

        $this->applyDateFilters($query, $filters);

        $sessions = $query->get();
        $grandTotal = (float) $sessions->sum('total_amount');
        $totalItemCount = $sessions->sum(fn (HelpSession $session) => $session->items->count());

        return view('frontend.client.helping.help_sessions_show', compact(
            'client',
            'sessions',
            'grandTotal',
            'totalItemCount',
            'hasAnySessions',
            'totalSessionCount'
        ));
    }

    public function create(Client $client): View
    {
        $client = $this->authorizedClient($client);

        return view('frontend.client.helping.help_sessions_create', compact('client'));
    }

    public function store(Request $request, Client $client): RedirectResponse
    {
        $client = $this->authorizedClient($client);
        $this->normalizeItems($request);

        $validated = $request->validate(
            $this->rules($client->id),
            $this->messages()
        );

        DB::transaction(function () use ($validated, $client): void {
            Client::query()->whereKey($client->id)->lockForUpdate()->firstOrFail();

            $duplicate = HelpSession::query()
                ->where('client_id', $client->id)
                ->whereDate('help_date', $validated['help_date'])
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'help_date' => 'วันที่นี้มีการบันทึกการช่วยเหลือแล้ว กรุณาเลือกวันอื่น',
                ]);
            }

            $session = HelpSession::create([
                'client_id' => $client->id,
                'help_date' => $validated['help_date'],
                'total_amount' => 0,
            ]);

            [$itemRows, $total] = $this->prepareItemRows($validated['items']);

            $session->items()->createMany($itemRows);
            $session->update(['total_amount' => $total]);

            $this->syncLatestCaseActivity($client->id);
        }, 3);

        return redirect()
            ->route('help_sessions.show', $client->id)
            ->with([
                'message' => 'บันทึกข้อมูลการช่วยเหลือเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function edit(Client $client, HelpSession $session): View
    {
        $client = $this->authorizedClient($client);
        $session = $this->authorizedSession($client, $session)->loadMissing('items');

        return view('frontend.client.helping.edit', compact('client', 'session'));
    }

    public function update(Request $request, Client $client, HelpSession $session): RedirectResponse
    {
        $client = $this->authorizedClient($client);
        $session = $this->authorizedSession($client, $session);
        $this->normalizeItems($request);

        $validated = $request->validate(
            $this->rules($client->id, $session->id),
            $this->messages()
        );

        DB::transaction(function () use ($validated, $client, $session): void {
            Client::query()->whereKey($client->id)->lockForUpdate()->firstOrFail();

            $lockedSession = HelpSession::query()
                ->where('client_id', $client->id)
                ->lockForUpdate()
                ->findOrFail($session->id);

            $duplicate = HelpSession::query()
                ->where('client_id', $client->id)
                ->whereDate('help_date', $validated['help_date'])
                ->where('id', '!=', $lockedSession->id)
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'help_date' => 'วันที่นี้มีการบันทึกการช่วยเหลือแล้ว กรุณาเลือกวันอื่น',
                ]);
            }

            [$itemRows, $total] = $this->prepareItemRows($validated['items']);

            $lockedSession->update([
                'help_date' => $validated['help_date'],
                'total_amount' => $total,
            ]);

            $lockedSession->items()->delete();
            $lockedSession->items()->createMany($itemRows);

            $this->syncLatestCaseActivity($client->id);
        }, 3);

        return redirect()
            ->route('help_sessions.show', $client->id)
            ->with([
                'message' => 'แก้ไขข้อมูลการช่วยเหลือเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function destroy(Client $client, HelpSession $session): RedirectResponse
    {
        $client = $this->authorizedClient($client);
        $session = $this->authorizedSession($client, $session);

        DB::transaction(function () use ($client, $session): void {
            $lockedSession = HelpSession::query()
                ->where('client_id', $client->id)
                ->lockForUpdate()
                ->findOrFail($session->id);

            $lockedSession->items()->delete();
            $lockedSession->delete();

            $this->syncLatestCaseActivity($client->id);
        }, 3);

        return redirect()
            ->route('help_sessions.show', $client->id)
            ->with([
                'message' => 'ลบข้อมูลการช่วยเหลือเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function report(Client $client, HelpSession $session): View
    {
        $client = $this->authorizedClient($client);
        $session = $this->authorizedSession($client, $session)->loadMissing([
            'items' => fn ($itemsQuery) => $itemsQuery->orderBy('id'),
        ]);

        $grandTotal = (float) $session->items->sum(
            fn ($item) => round((float) $item->quantity * (float) $item->unit_price, 2)
        );

        return view('frontend.client.helping.report', compact(
            'client',
            'session',
            'grandTotal'
        ));
    }

    public function reportRange(Request $request, Client $client): View|RedirectResponse
    {
        $client = $this->authorizedClient($client);

        $validator = $this->filterValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('help_sessions.show', $client->id)
                ->withErrors($validator, 'filter')
                ->withInput();
        }

        $filters = $validator->validated();

        $query = HelpSession::query()
            ->where('client_id', $client->id)
            ->with([
                'items' => fn ($itemsQuery) => $itemsQuery->orderBy('id'),
            ])
            ->orderBy('help_date')
            ->orderBy('id');

        $this->applyDateFilters($query, $filters);

        $sessions = $query->get();
        $grandTotal = (float) $sessions->sum('total_amount');

        return view('frontend.client.helping.report_range', compact(
            'client',
            'sessions',
            'grandTotal'
        ));
    }

    private function authorizedClient(Client $client): Client
    {
        return Client::forUser(auth()->user())->findOrFail($client->id);
    }

    private function authorizedSession(Client $client, HelpSession $session): HelpSession
    {
        return HelpSession::query()
            ->where('client_id', $client->id)
            ->findOrFail($session->id);
    }

    private function filterValidator(Request $request)
    {
        return Validator::make($request->only(['from', 'to']), [
            'from' => ['nullable', 'date', 'before_or_equal:today'],
            'to' => ['nullable', 'date', 'before_or_equal:today', 'after_or_equal:from'],
        ], [
            'from.date' => 'วันที่เริ่มต้นไม่ถูกต้อง',
            'from.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันปัจจุบัน',
            'to.date' => 'วันที่สิ้นสุดไม่ถูกต้อง',
            'to.before_or_equal' => 'วันที่สิ้นสุดต้องไม่เกินวันปัจจุบัน',
            'to.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);
    }

    private function applyDateFilters($query, array $filters): void
    {
        if (!empty($filters['from'])) {
            $query->whereDate('help_date', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('help_date', '<=', $filters['to']);
        }
    }

    private function rules(int $clientId, ?int $ignoreSessionId = null): array
    {
        $dateRule = Rule::unique('help_sessions', 'help_date')
            ->where(fn ($query) => $query->where('client_id', $clientId));

        if ($ignoreSessionId !== null) {
            $dateRule->ignore($ignoreSessionId);
        }

        return [
            'help_date' => ['required', 'date', 'before_or_equal:today', $dateRule],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
        ];
    }

    private function messages(): array
    {
        return [
            'help_date.required' => 'กรุณาระบุวันที่ให้ความช่วยเหลือ',
            'help_date.date' => 'รูปแบบวันที่ให้ความช่วยเหลือไม่ถูกต้อง',
            'help_date.before_or_equal' => 'วันที่ให้ความช่วยเหลือต้องไม่เกินวันปัจจุบัน',
            'help_date.unique' => 'วันที่นี้มีการบันทึกการช่วยเหลือแล้ว กรุณาเลือกวันอื่น',

            'items.required' => 'กรุณาเพิ่มรายการช่วยเหลืออย่างน้อย 1 รายการ',
            'items.array' => 'รูปแบบรายการช่วยเหลือไม่ถูกต้อง',
            'items.min' => 'กรุณาเพิ่มรายการช่วยเหลืออย่างน้อย 1 รายการ',
            'items.max' => 'บันทึกรายการช่วยเหลือได้ไม่เกิน 50 รายการต่อครั้ง',

            'items.*.item_name.required' => 'กรุณาระบุชื่อรายการช่วยเหลือ',
            'items.*.item_name.string' => 'ชื่อรายการช่วยเหลือต้องเป็นข้อความ',
            'items.*.item_name.max' => 'ชื่อรายการช่วยเหลือต้องไม่เกิน 255 ตัวอักษร',
            'items.*.quantity.required' => 'กรุณาระบุจำนวน',
            'items.*.quantity.integer' => 'จำนวนต้องเป็นเลขจำนวนเต็ม',
            'items.*.quantity.min' => 'จำนวนต้องไม่น้อยกว่า 1',
            'items.*.quantity.max' => 'จำนวนมีค่ามากเกินกว่าที่ระบบรองรับ',
            'items.*.unit_price.required' => 'กรุณาระบุราคา/หน่วย',
            'items.*.unit_price.numeric' => 'ราคา/หน่วยต้องเป็นตัวเลข',
            'items.*.unit_price.min' => 'ราคา/หน่วยต้องไม่น้อยกว่า 0',
            'items.*.unit_price.max' => 'ราคา/หน่วยมีค่ามากเกินกว่าที่ระบบรองรับ',
        ];
    }

    private function normalizeItems(Request $request): void
    {
        $items = collect($request->input('items', []))
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item): array {
                return [
                    'item_name' => trim((string) ($item['item_name'] ?? '')),
                    'quantity' => $item['quantity'] ?? null,
                    'unit_price' => $item['unit_price'] ?? null,
                ];
            })
            ->values()
            ->all();

        $request->merge(['items' => $items]);
    }

    private function prepareItemRows(array $items): array
    {
        $rows = [];
        $total = 0.0;

        foreach ($items as $item) {
            $quantity = (int) $item['quantity'];
            $unitPrice = round((float) $item['unit_price'], 2);
            $rowTotal = round($quantity * $unitPrice, 2);

            $rows[] = [
                'item_name' => trim((string) $item['item_name']),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $rowTotal,
            ];

            $total = round($total + $rowTotal, 2);
        }

        return [$rows, $total];
    }

    private function syncLatestCaseActivity(int $clientId): void
    {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'help_session')
            ->delete();

        $latest = HelpSession::query()
            ->where('client_id', $clientId)
            ->with([
                'items' => fn ($itemsQuery) => $itemsQuery->orderBy('id'),
            ])
            ->orderByDesc('help_date')
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            return;
        }

        $itemNames = $latest->items
            ->pluck('item_name')
            ->filter()
            ->take(3)
            ->values()
            ->all();

        $description = 'วันที่ช่วยเหลือ: ' . Carbon::parse($latest->help_date)->format('d/m/Y')
            . ' | รายการ: ' . (count($itemNames) ? implode(', ', $itemNames) : '-')
            . ($latest->items->count() > 3 ? ' และรายการอื่น ๆ' : '')
            . ' | มูลค่ารวม: ' . number_format((float) $latest->total_amount, 2) . ' บาท';

        CaseActivity::record([
            'client_id' => $clientId,
            'module' => 'help_session',
            'type' => 'success',
            'title' => 'ข้อมูลการช่วยเหลือสิ่งของ/เครื่องใช้ล่าสุด',
            'description' => $description,
            'occurred_at' => Carbon::parse($latest->help_date, 'Asia/Bangkok')->startOfDay(),
            'icon' => 'bi-box-seam',
            'url' => route('help_sessions.show', $clientId),
        ]);
    }
}
