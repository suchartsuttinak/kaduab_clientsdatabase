<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientHouseTransfer;
use App\Models\House;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientHouseTransferController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $clients = Client::forUser($user)
            ->with(['house', 'project'])
            ->orderBy('house_id')
            ->orderBy('first_name')
            ->get();

        $houses = House::query()
            ->whereIn('id', $user->accessibleHouseIds())
            ->orderByRaw('CAST(REGEXP_REPLACE(house_name, "[^0-9]", "") AS UNSIGNED)')
            ->orderBy('house_name')
            ->get();

        $houseIds = $houses->pluck('id');

        $caregivers = User::query()
            ->with(['houses' => fn ($q) => $q->whereIn('houses.id', $houseIds)])
            ->select('id', 'name', 'project_id')
            ->whereHas('houses', fn ($q) => $q->whereIn('houses.id', $houseIds))
            ->orderBy('name')
            ->get()
            ->flatMap(function ($caregiver) {
                return $caregiver->houses->map(function ($house) use ($caregiver) {
                    return [
                        'house_id' => $house->id,
                        'name' => $caregiver->name,
                    ];
                });
            })
            ->groupBy('house_id')
            ->map(fn ($items) => $items->pluck('name')->implode(', '));

        return view('frontend.client_house_transfer.index', compact(
            'clients',
            'houses',
            'caregivers'
        ));
    }

    public function update(Request $request, Client $client)
    {
        $user = auth()->user();
        $client = Client::forUser($user)->findOrFail($client->id);

        $validated = $request->validate([
            'house_id' => ['required', 'exists:houses,id'],
            'remark' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_unless(
            $user->canAccessHouse((int) $validated['house_id']),
            403,
            'คุณไม่มีสิทธิ์ย้ายผู้รับบริการไปยังบ้านนี้'
        );

        if ((int) $client->house_id === (int) $validated['house_id']) {
            return back()->with('info', 'เด็กอยู่บ้านนี้อยู่แล้ว ไม่มีการเปลี่ยนแปลงข้อมูล');
        }

        DB::transaction(function () use ($client, $validated) {
            $oldHouseId = $client->house_id;
            $newHouseId = $validated['house_id'];

            $caregiver = User::whereHas('houses', function ($query) use ($newHouseId) {
                $query->where('houses.id', $newHouseId);
            })->first();

            ClientHouseTransfer::create([
                'client_id' => $client->id,
                'old_house_id' => $oldHouseId,
                'new_house_id' => $newHouseId,
                'project_id' => $client->project_id,
                'caregiver_id' => $caregiver?->id,
                'changed_by' => auth()->id(),
                'transfer_date' => now()->toDateString(),
                'remark' => $validated['remark'] ?? null,
            ]);

            $client->update([
                'house_id' => $newHouseId,
            ]);
        });

        return back()->with('success', 'ย้ายบ้านและอัปเดตข้อมูลเด็กเรียบร้อยแล้ว');
    }
}
