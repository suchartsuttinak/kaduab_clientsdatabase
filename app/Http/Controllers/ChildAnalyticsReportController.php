<?php

namespace App\Http\Controllers;

use App\Models\Absent;
use App\Models\Client;
use App\Models\Education;
use App\Models\House;
use App\Models\Institution;
use App\Models\Medical;
use App\Models\Problem;
use App\Models\Project;
use App\Models\Target;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChildAnalyticsReportController extends Controller
{
    private const TIMEZONE = 'Asia/Bangkok';

    public function index(Request $request)
    {
        $user = auth()->user();
        $todayDate = now(self::TIMEZONE);
        $today = $todayDate->toDateString();

        $request->validate(
            [
                'start_date'     => ['nullable', 'date_format:Y-m-d', 'before_or_equal:' . $today],
                'end_date'       => ['nullable', 'date_format:Y-m-d', 'before_or_equal:' . $today],
                'gender'         => ['nullable', 'in:male,female'],
                'age_min'        => ['nullable', 'integer', 'min:0', 'max:120'],
                'age_max'        => ['nullable', 'integer', 'min:0', 'max:120'],
                'release_status' => ['nullable', 'in:show,hide,pending_refer,all'],
            ],
            [
                'start_date.date_format'     => 'รูปแบบวันที่เริ่มต้นไม่ถูกต้อง',
                'start_date.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันปัจจุบัน',
                'end_date.date_format'       => 'รูปแบบวันที่สิ้นสุดไม่ถูกต้อง',
                'end_date.before_or_equal'   => 'วันที่สิ้นสุดต้องไม่เกินวันปัจจุบัน',
                'gender.in'                  => 'ข้อมูลเพศไม่ถูกต้อง',
                'age_min.integer'            => 'อายุต่ำสุดต้องเป็นจำนวนเต็ม',
                'age_min.min'                => 'อายุต่ำสุดต้องไม่น้อยกว่า 0 ปี',
                'age_min.max'                => 'อายุต่ำสุดต้องไม่เกิน 120 ปี',
                'age_max.integer'            => 'อายุสูงสุดต้องเป็นจำนวนเต็ม',
                'age_max.min'                => 'อายุสูงสุดต้องไม่น้อยกว่า 0 ปี',
                'age_max.max'                => 'อายุสูงสุดต้องไม่เกิน 120 ปี',
                'release_status.in'          => 'สถานะที่เลือกไม่ถูกต้อง',
            ]
        );

        $startDate = $request->filled('start_date')
            ? Carbon::createFromFormat('Y-m-d', $request->input('start_date'), self::TIMEZONE)->startOfDay()
            : $todayDate->copy()->startOfYear()->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::createFromFormat('Y-m-d', $request->input('end_date'), self::TIMEZONE)->endOfDay()
            : $todayDate->copy()->endOfDay();

        if ($startDate->gt($endDate)) {
            $oldStartDate = $startDate->copy();
            $startDate = $endDate->copy()->startOfDay();
            $endDate = $oldStartDate->copy()->endOfDay();
        }

        $gender = $request->input('gender');
        $ageMin = $request->filled('age_min') ? (int) $request->input('age_min') : null;
        $ageMax = $request->filled('age_max') ? (int) $request->input('age_max') : null;

        if ($ageMin !== null && $ageMax !== null && $ageMin > $ageMax) {
            [$ageMin, $ageMax] = [$ageMax, $ageMin];
        }

        $educationStart = $this->selectedId($request, 'education_start');
        $educationEnd   = $this->selectedId($request, 'education_end');
        $institutionId  = $this->selectedId($request, 'institution_id');
        $problemId      = $this->selectedId($request, 'problem_id');
        $projectId      = $this->selectedId($request, 'project_id');
        $houseId        = $this->selectedId($request, 'house_id');
        $targetId       = $this->selectedId($request, 'target_id');
        $releaseStatus  = $request->input('release_status', 'show');

        if ($projectId !== null) {
            abort_unless($user->canAccessProject($projectId), 403, 'คุณไม่มีสิทธิ์ดูหน่วยงาน/โครงการนี้');
        }
        if ($houseId !== null) {
            abort_unless($user->canAccessHouse($houseId), 403, 'คุณไม่มีสิทธิ์ดูบ้านนี้');
        }

        if ($educationStart !== null && $educationEnd !== null && $educationStart > $educationEnd) {
            [$educationStart, $educationEnd] = [$educationEnd, $educationStart];
        }

        $periodStart = $startDate->toDateString();
        $periodEnd   = $endDate->toDateString();

        /*
        |--------------------------------------------------------------------------
        | ฐานผู้รับบริการตามตัวกรอง
        |--------------------------------------------------------------------------
        | ไม่ใช้ arrival_date >= วันเริ่มต้นในจุดนี้ เพราะจะทำให้เด็กที่รับเข้าก่อน
        | ช่วงรายงาน แต่มีรายการขาดเรียน/รักษาในช่วงรายงาน ถูกตัดออกโดยผิดพลาด
        |
        | ระดับชั้น/สถานศึกษา ใช้ข้อมูลล่าสุดที่มี record_date ไม่เกินวันสิ้นสุด
        | และอายุคำนวณ ณ วันสิ้นสุดรายงาน เพื่อให้รายงานย้อนหลังสอดคล้องกัน
        */
        $eligibleClientQuery = Client::forUser($user)
            ->with([
                'title',
                'educationRecords' => function ($query) use ($periodEnd) {
                    $query->whereDate('record_date', '<=', $periodEnd)
                        ->with(['education', 'semester', 'institution'])
                        ->orderByDesc('record_date')
                        ->orderByDesc('id');
                },
                'problems' => function ($query) use ($problemId) {
                    if ($problemId !== null) {
                        $query->where('problems.id', $problemId);
                    }
                },
                'project',
                'house',
                'target',
            ])
            ->where(function ($query) use ($periodEnd) {
                // ผู้รับบริการต้องเข้าระบบไม่เกินวันสิ้นสุดรายงาน
                // กรณีข้อมูลเก่าที่ไม่มี arrival_date ยังไม่ตัดออกจากฐานกิจกรรม
                $query->whereNull('arrival_date')
                    ->orWhereDate('arrival_date', '<=', $periodEnd);
            });

        if (!empty($releaseStatus) && $releaseStatus !== 'all') {
            $eligibleClientQuery->where('release_status', $releaseStatus);
        }

        if (!empty($gender)) {
            $eligibleClientQuery->where('gender', $gender);
        }

        $this->applyAgeFilter($eligibleClientQuery, $ageMin, $ageMax, $endDate);

        if ($projectId !== null) {
            $eligibleClientQuery->where('project_id', $projectId);
        }

        if ($houseId !== null) {
            $eligibleClientQuery->where('house_id', $houseId);
        }

        if ($targetId !== null) {
            $eligibleClientQuery->where('target_id', $targetId);
        }

        if ($problemId !== null) {
            $eligibleClientQuery->whereHas('problems', function ($query) use ($problemId) {
                $query->where('problems.id', $problemId);
            });
        }

        $eligibleClients = $eligibleClientQuery->get();

        // กรองระดับชั้นและสถานศึกษาจากประวัติ "ล่าสุด ณ วันสิ้นสุดรายงาน"
        $eligibleClients = $this->filterByLatestEducation(
            $eligibleClients,
            $educationStart,
            $educationEnd,
            $institutionId
        );

        /*
        |--------------------------------------------------------------------------
        | กลุ่มเด็กที่รับเข้าในช่วงรายงาน
        |--------------------------------------------------------------------------
        | ใช้สำหรับยอดเด็ก โรงเรียน ระดับชั้น และสภาพปัญหา
        */
        $clients = $eligibleClients
            ->filter(function (Client $client) use ($periodStart, $periodEnd) {
                if (!$client->arrival_date) {
                    return false;
                }

                $arrivalDate = $client->arrival_date instanceof Carbon
                    ? $client->arrival_date->toDateString()
                    : Carbon::parse($client->arrival_date, self::TIMEZONE)->toDateString();

                return $arrivalDate >= $periodStart && $arrivalDate <= $periodEnd;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | รายการกิจกรรมในช่วงรายงาน
        |--------------------------------------------------------------------------
        | ขาดเรียน/เจ็บป่วย/โรค ใช้วันที่เกิดรายการจริง และนับจากผู้รับบริการ
        | ทั้งหมดที่ตรงตามตัวกรอง ไม่จำกัดว่าต้องรับเข้าใหม่ภายในช่วงเดียวกัน
        */
        $activityClientIds = $eligibleClients->pluck('id')->values();
        $emptyActivityQuery = $activityClientIds->isEmpty();

        $absentQuery = Absent::query()
            ->when($emptyActivityQuery, fn ($query) => $query->whereRaw('1 = 0'))
            ->when(!$emptyActivityQuery, fn ($query) => $query->whereIn('client_id', $activityClientIds))
            ->whereBetween('absent_date', [$periodStart, $periodEnd]);

        $medicalQuery = Medical::query()
            ->when($emptyActivityQuery, fn ($query) => $query->whereRaw('1 = 0'))
            ->when(!$emptyActivityQuery, fn ($query) => $query->whereIn('client_id', $activityClientIds))
            ->whereBetween('medical_date', [$periodStart, $periodEnd]);

        $totalClients = $clients->count();
        $maleCount    = $clients->where('gender', 'male')->count();
        $femaleCount  = $clients->where('gender', 'female')->count();

        // จำนวนเด็กทั้งหมดที่เป็นฐานตรวจรายการขาดเรียน/เจ็บป่วย
        $activityPopulationCount = $eligibleClients->count();

        $absentTotalRecords  = (clone $absentQuery)->count();
        $absentTotalChildren = (clone $absentQuery)->distinct('client_id')->count('client_id');

        $medicalTotalRecords  = (clone $medicalQuery)->count();
        $medicalTotalChildren = (clone $medicalQuery)->distinct('client_id')->count('client_id');

        $absentByClient = Absent::query()
            ->select(
                'client_id',
                DB::raw('COUNT(*) as total_count'),
                DB::raw('MAX(absent_date) as latest_date')
            )
            ->when($emptyActivityQuery, fn ($query) => $query->whereRaw('1 = 0'))
            ->when(!$emptyActivityQuery, fn ($query) => $query->whereIn('client_id', $activityClientIds))
            ->whereBetween('absent_date', [$periodStart, $periodEnd])
            ->groupBy('client_id')
            ->orderByDesc('total_count')
            ->orderByDesc('latest_date')
            ->get()
            ->map(fn ($row) => $this->mapClientActivityRow($row, $eligibleClients, 'absent', $endDate));

        $medicalByClient = Medical::query()
            ->select(
                'client_id',
                DB::raw('COUNT(*) as total_count'),
                DB::raw('MAX(medical_date) as latest_date')
            )
            ->when($emptyActivityQuery, fn ($query) => $query->whereRaw('1 = 0'))
            ->when(!$emptyActivityQuery, fn ($query) => $query->whereIn('client_id', $activityClientIds))
            ->whereBetween('medical_date', [$periodStart, $periodEnd])
            ->groupBy('client_id')
            ->orderByDesc('total_count')
            ->orderByDesc('latest_date')
            ->get()
            ->map(fn ($row) => $this->mapClientActivityRow($row, $eligibleClients, 'medical', $endDate));

        // รวมโรคด้วย Collection เพื่อรองรับ MySQL ONLY_FULL_GROUP_BY
        $medicalDiseaseSummary = Medical::query()
            ->when($emptyActivityQuery, fn ($query) => $query->whereRaw('1 = 0'))
            ->when(!$emptyActivityQuery, fn ($query) => $query->whereIn('client_id', $activityClientIds))
            ->whereBetween('medical_date', [$periodStart, $periodEnd])
            ->pluck('disease_name')
            ->map(function ($diseaseName) {
                $name = preg_replace('/\s+/u', ' ', trim((string) $diseaseName));

                return $name !== '' ? $name : 'ไม่ระบุ';
            })
            ->countBy()
            ->map(function (int $totalCount, string $name) {
                return (object) [
                    'name' => $name,
                    'total_count' => $totalCount,
                ];
            })
            ->sort(function ($first, $second) {
                $countComparison = $second->total_count <=> $first->total_count;

                return $countComparison !== 0
                    ? $countComparison
                    : strnatcasecmp($first->name, $second->name);
            })
            ->values();

        [$educationSummary, $schoolSummary, $problemSummary] = $this->buildClientSummaries($clients);

        $educations   = Education::orderBy('id')->get();
        $institutions = Institution::orderBy('institution_name')->get();
        $problems     = Problem::orderBy('problem_name')->get();
        $projects = Project::query()->whereIn('id', $user->accessibleProjectIds())->orderBy('project_name')->get();
        $houses = House::query()->whereIn('id', $user->accessibleHouseIds())->orderBy('id')->get();
        $targets      = Target::orderBy('target_name')->get();

        return view('admin.reports.child_analytics.index', compact(
            'today',
            'startDate',
            'endDate',
            'gender',
            'ageMin',
            'ageMax',
            'educationStart',
            'educationEnd',
            'institutionId',
            'releaseStatus',
            'problemId',
            'projectId',
            'houseId',
            'targetId',
            'clients',
            'eligibleClients',
            'totalClients',
            'maleCount',
            'femaleCount',
            'activityPopulationCount',
            'absentTotalRecords',
            'absentTotalChildren',
            'medicalTotalRecords',
            'medicalTotalChildren',
            'absentByClient',
            'medicalByClient',
            'medicalDiseaseSummary',
            'educationSummary',
            'schoolSummary',
            'problemSummary',
            'educations',
            'institutions',
            'problems',
            'projects',
            'houses',
            'targets'
        ));
    }

    private function selectedId(Request $request, string $key): ?int
    {
        $value = $request->input($key);

        if ($value === null || $value === '' || $value === 'all') {
            return null;
        }

        return filter_var(
            $value,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        ) ?: null;
    }

    private function applyAgeFilter($query, ?int $ageMin, ?int $ageMax, Carbon $asOfDate): void
    {
        if ($ageMin === null && $ageMax === null) {
            return;
        }

        $minimumAge = $ageMin ?? 0;
        $maximumAge = $ageMax ?? 120;

        $oldestBirthDate = $asOfDate->copy()
            ->startOfDay()
            ->subYears($maximumAge + 1)
            ->addDay()
            ->toDateString();

        $youngestBirthDate = $asOfDate->copy()
            ->startOfDay()
            ->subYears($minimumAge)
            ->toDateString();

        $query->whereBetween('birth_date', [$oldestBirthDate, $youngestBirthDate]);
    }

    private function filterByLatestEducation(
        Collection $clients,
        ?int $educationStart,
        ?int $educationEnd,
        ?int $institutionId
    ): Collection {
        if ($educationStart === null && $educationEnd === null && $institutionId === null) {
            return $clients->values();
        }

        return $clients
            ->filter(function (Client $client) use ($educationStart, $educationEnd, $institutionId) {
                $latestEducation = $client->educationRecords->first();

                if (!$latestEducation) {
                    return false;
                }

                $latestEducationId = $latestEducation->education_id !== null
                    ? (int) $latestEducation->education_id
                    : null;

                if ($educationStart !== null
                    && ($latestEducationId === null || $latestEducationId < $educationStart)) {
                    return false;
                }

                if ($educationEnd !== null
                    && ($latestEducationId === null || $latestEducationId > $educationEnd)) {
                    return false;
                }

                if ($institutionId !== null
                    && (int) $latestEducation->institution_id !== $institutionId) {
                    return false;
                }

                return true;
            })
            ->values();
    }

    private function mapClientActivityRow(
        object $row,
        Collection $clients,
        string $module,
        Carbon $asOfDate
    ): array {
        $client = $clients->firstWhere('id', $row->client_id);

        $url = match ($module) {
            'absent'  => $client ? route('absent.add', $client->id) : null,
            'medical' => $client ? route('medical.add', $client->id) : null,
            default   => null,
        };

        return [
            'client_id'   => (int) $row->client_id,
            'fullname'    => $client?->full_name
                ?? $client?->fullname
                ?? $client?->name
                ?? '-',
            'gender'      => $client?->gender ?? '-',
            'age'         => $client?->birth_date
                ? (int) Carbon::parse($client->birth_date, self::TIMEZONE)
                    ->diffInYears($asOfDate)
                : null,
            'total_count' => (int) $row->total_count,
            'latest_date' => $row->latest_date,
            'url'         => $url,
        ];
    }

    private function buildClientSummaries(Collection $clients): array
    {
        $educationSummary = [];
        $schoolSummary = [];
        $problemSummary = [];

        foreach ($clients as $client) {
            $latestEducation = $client->educationRecords->first();

            $educationName = $latestEducation?->education?->education_name ?? 'ไม่ระบุ';
            $educationSummary[$educationName] = ($educationSummary[$educationName] ?? 0) + 1;

            $schoolName = $latestEducation?->institution?->institution_name
                ?? $latestEducation?->school_name
                ?? 'ไม่ระบุ';
            $schoolSummary[$schoolName] = ($schoolSummary[$schoolName] ?? 0) + 1;

            if ($client->problems && $client->problems->isNotEmpty()) {
                foreach ($client->problems as $problem) {
                    $problemName = trim((string) ($problem->problem_name ?? $problem->name ?? ''));

                    if ($problemName === '') {
                        $problemName = 'ไม่ระบุ';
                    }

                    $problemSummary[$problemName] = ($problemSummary[$problemName] ?? 0) + 1;
                }
            } else {
                $problemSummary['ไม่ระบุ'] = ($problemSummary['ไม่ระบุ'] ?? 0) + 1;
            }
        }

        $educationSummary = $this->sortEducationSummary($educationSummary);
        arsort($schoolSummary);
        arsort($problemSummary);

        return [$educationSummary, $schoolSummary, $problemSummary];
    }

    private function sortEducationSummary(array $summary): array
    {
        uksort($summary, function ($a, $b) {
            $score = function ($text) {
                $text = trim((string) $text);
                $base = 0;

                if (str_contains($text, 'มัธยม') || str_contains($text, 'ม.')) {
                    $base = 200;
                } elseif (str_contains($text, 'ประถม')
                    || str_contains($text, 'ประม')
                    || str_contains($text, 'ป.')) {
                    $base = 100;
                } elseif (str_contains($text, 'อนุบาล')) {
                    $base = 50;
                }

                preg_match('/(\d+)/u', $text, $match);
                $year = isset($match[1]) ? (int) $match[1] : 0;

                return $base + $year;
            };

            return $score($b) <=> $score($a);
        });

        return $summary;
    }
}