<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\House;
use App\Models\Idstation;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Citizen;

class IdstationCentralController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $today = now('Asia/Bangkok')->toDateString();

        $clientsQuery = Client::forUser($user)
            ->with(['target', 'house', 'project'])
            ->whereHas('target', function ($query) {
                $query->where('target_name', 'บุคคลไม่มีสถานะทางทะเบียน');
            });

        $idstationsQuery = Idstation::with([
                'client.target',
                'client.house',
                'client.project',
                'citizenships',
                'citizens',
                'creator',
                'updater',
            ])
            ->whereIn('client_id', (clone $clientsQuery)->pluck('id'));

        if ($request->filled('house_id')) {
            abort_unless($user->canAccessHouse((int) $request->house_id), 403, 'คุณไม่มีสิทธิ์ดูบ้านนี้');
        }
        if ($request->filled('project_id')) {
            abort_unless($user->canAccessProject((int) $request->project_id), 403, 'คุณไม่มีสิทธิ์ดูหน่วยงาน/โครงการนี้');
        }

        if ($request->filled('date_from')) {
            $idstationsQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $idstationsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('house_id')) {
            $idstationsQuery->whereHas('client', function ($query) use ($request) {
                $query->where('house_id', $request->house_id);
            });
        }

        if ($request->filled('project_id')) {
            $idstationsQuery->whereHas('client', function ($query) use ($request) {
                $query->where('project_id', $request->project_id);
            });
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);

            $idstationsQuery->whereHas('client', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"]);
                });
            });
        }

        $allForSummary = (clone $idstationsQuery)->get();

        $summary = [
            'total' => $allForSummary->count(),

            'pending' => $allForSummary->filter(function ($item) {
                return $item->citizens->count() === 0;
            })->count(),

            'completed' => $allForSummary->filter(function ($item) {
                return $item->citizens->count() > 0;
            })->count(),

            'over_90' => $allForSummary->filter(function ($item) {
                return $item->created_at
                    && Carbon::parse($item->created_at)->startOfDay()
                        ->diffInDays(now('Asia/Bangkok')->startOfDay()) > 90
                    && $item->citizens->count() === 0;
            })->count(),

            'over_180' => $allForSummary->filter(function ($item) {
                return $item->created_at
                    && Carbon::parse($item->created_at)->startOfDay()
                        ->diffInDays(now('Asia/Bangkok')->startOfDay()) > 180
                    && $item->citizens->count() === 0;
            })->count(),

            'citizenship' => $allForSummary->filter(function ($item) {
                return $item->citizens->count() > 0;
            })->count(),

            'birth_registration' => 0,

            'id_card' => $allForSummary->filter(function ($item) {
                return $item->citizens->count() > 0;
            })->count(),

            'other' => 0,
        ];

        if ($request->filled('process_status')) {
            if ($request->process_status === 'pending') {
                $idstationsQuery->whereDoesntHave('citizens');
            }

            if ($request->process_status === 'completed') {
                $idstationsQuery->whereHas('citizens');
            }
        }

        if ($request->filled('duration_status')) {
            if ($request->duration_status === 'over_90') {
                $idstationsQuery->whereDate('created_at', '<=', now('Asia/Bangkok')->subDays(90)->toDateString())
                    ->whereDoesntHave('citizens');
            }

            if ($request->duration_status === 'over_180') {
                $idstationsQuery->whereDate('created_at', '<=', now('Asia/Bangkok')->subDays(180)->toDateString())
                    ->whereDoesntHave('citizens');
            }
        }

            if ($request->filled('result_status')) {
            $idstationsQuery->whereHas('citizens', function ($query) use ($request) {
                $query->where('citizens.id', $request->result_status);
            });
        }

        $idstations = $idstationsQuery
            ->latest()
            ->paginate(15)
            ->withQueryString();

       $houses = House::query()->whereIn('id', $user->accessibleHouseIds())->orderBy('house_name')->get();

            $projects = Project::query()->whereIn('id', $user->accessibleProjectIds())->orderBy('project_name')->get();

            $citizens = Citizen::orderBy('id')->get();

            /*
            |--------------------------------------------------------------------------
            | สรุปสถานะที่ได้รับ (Dynamic)
            |--------------------------------------------------------------------------
            |
            | ดึงข้อมูลจากตาราง citizens ทั้งหมด
            | ไม่ต้องเขียนชื่อสถานะแบบตายตัว
            |
            */

            $citizenSummary = $citizens->map(function ($citizen) use ($allForSummary) {

                return [

                    'id' => $citizen->id,

                    'name' => $citizen->citizen_name,

                    'count' => $allForSummary->filter(function ($item) use ($citizen) {

                        return $item->citizens->contains('id', $citizen->id);

                    })->count(),

                ];

            });

            return view('backend.idstation_central.index', compact(

                'summary',

                'citizenSummary',

                'idstations',

                'houses',

                'projects',

                'citizens',

                'today'

            ));
    }

   public function report(Request $request)
{
    $clientsQuery = Client::forUser(auth()->user())
        ->with(['target', 'house', 'project'])
        ->whereHas('target', function ($query) {
            $query->where('target_name', 'บุคคลไม่มีสถานะทางทะเบียน');
        });

    $idstationsQuery = Idstation::with([
            'client.target',
            'client.house',
            'client.project',
            'citizenships',
            'citizens',
            'creator',
            'updater',
        ])
        ->whereIn('client_id', (clone $clientsQuery)->pluck('id'));

    if ($request->filled('date_from')) {
        $idstationsQuery->whereDate('receive_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $idstationsQuery->whereDate('receive_date', '<=', $request->date_to);
    }

    if ($request->filled('house_id')) {
        $idstationsQuery->whereHas('client', function ($query) use ($request) {
            $query->where('house_id', $request->house_id);
        });
    }

    if ($request->filled('project_id')) {
        $idstationsQuery->whereHas('client', function ($query) use ($request) {
            $query->where('project_id', $request->project_id);
        });
    }

    if ($request->filled('keyword')) {
        $keyword = trim($request->keyword);

        $idstationsQuery->whereHas('client', function ($query) use ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"]);
            });
        });
    }

    if ($request->filled('process_status')) {
        if ($request->process_status === 'pending') {
            $idstationsQuery->whereDoesntHave('citizens');
        }

        if ($request->process_status === 'completed') {
            $idstationsQuery->whereHas('citizens');
        }
    }

    if ($request->filled('duration_status')) {
        if ($request->duration_status === 'over_90') {
            $idstationsQuery->whereDate('receive_date', '<=', now('Asia/Bangkok')->subDays(90)->toDateString())
                ->whereDoesntHave('citizens');
        }

        if ($request->duration_status === 'over_180') {
            $idstationsQuery->whereDate('receive_date', '<=', now('Asia/Bangkok')->subDays(180)->toDateString())
                ->whereDoesntHave('citizens');
        }
    }

    if ($request->filled('result_status')) {
        $idstationsQuery->whereHas('citizens', function ($query) use ($request) {
            $query->where('citizens.id', $request->result_status);
        });
    }

    $idstations = $idstationsQuery
        ->latest('receive_date')
        ->get();

    $summary = [
        'total' => $idstations->count(),

        'pending' => $idstations->filter(function ($item) {
            return $item->citizens->count() === 0;
        })->count(),

        'completed' => $idstations->filter(function ($item) {
            return $item->citizens->count() > 0;
        })->count(),

        'over_90' => $idstations->filter(function ($item) {
            return $item->receive_date
                && Carbon::parse($item->receive_date)->startOfDay()
                    ->diffInDays(now('Asia/Bangkok')->startOfDay()) > 90
                && $item->citizens->count() === 0;
        })->count(),

        'over_180' => $idstations->filter(function ($item) {
            return $item->receive_date
                && Carbon::parse($item->receive_date)->startOfDay()
                    ->diffInDays(now('Asia/Bangkok')->startOfDay()) > 180
                && $item->citizens->count() === 0;
        })->count(),
    ];

    $citizenSummary = $idstations
        ->flatMap(function ($item) {
            return $item->citizens;
        })
        ->groupBy('id')
        ->map(function ($items) {
            $first = $items->first();

            return [
                'name' => $first->citizen_name ?? '-',
                'count' => $items->count(),
            ];
        })
        ->values();

    return view('backend.idstation_central.report', compact(
        'idstations',
        'summary',
        'citizenSummary'
    ));
}
}