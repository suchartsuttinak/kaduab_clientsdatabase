<?php

namespace App\Http\Controllers\ClientAdmin;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Client;
use App\Models\Medical;
use App\Models\Observe;
// use App\Models\Problem;
use App\Models\Psychiatric;
use App\Models\Vaccination;
use App\Models\Absent;
use App\Models\SchoolFollowup;
use App\Models\CaseOutside;
use App\Models\CaseActivity;
use App\Models\Followup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;

class AdminClientController extends Controller
{
    /**
     * ดึง client ที่ user มีสิทธิ์เท่านั้น
     */
  protected function findAuthorizedClient(int $id, array $with = []): Client
{
    $query = Client::forUser(auth()->user());

    if (!empty($with)) {
        $query->with($with);
    }

    return $query->findOrFail($id);
}

    public function Index($id)
    {
        $today = Carbon::today();

        $client = $this->findAuthorizedClient((int) $id, [
            'educationRecords',
            'educationRecords.education',
            'educationRecords.institution',
            'problems',
            'house',
            'title',
            'national',
            'religion',
            'marital',
            'occupation',
            'income',
            'education',
            'contact',
            'status',
            'project',
            'target',
            'province',
            'district',
            'sub_district',
            'originProvince',
            'originDistrict',
            'originSubDistrict',
            'father',
            'mother',
            'spouse',
            'relative',
            'members',
            'files',
            'vaccinations',
            'refers',
        ]);

        // =========================
        // นัดหมาย
        // =========================
        $appointments = collect();

        $medicalRecords = Medical::where('client_id', $client->id)
            ->whereDate('appt_date', '>=', $today)
            ->get(['appt_date']);

        foreach ($medicalRecords as $m) {
            $appointments->push([
                'type' => 'พบแพทย์',
                'date' => $m->appt_date,
            ]);
        }

        $psychiatricRecords = Psychiatric::where('client_id', $client->id)
            ->whereDate('appoin_date', '>=', $today)
            ->get(['appoin_date']);

        foreach ($psychiatricRecords as $p) {
            $appointments->push([
                'type' => 'พบจิตแพทย์',
                'date' => $p->appoin_date,
            ]);
        }

        $accidentAppointments = Accident::where('client_id', $client->id)
            ->whereNotNull('appointment')
            ->whereDate('appointment', '>=', $today)
            ->get(['appointment', 'treat_no']);

        foreach ($accidentAppointments as $a) {
            $appointments->push([
                'type' => $a->treat_no ?: 'นัดหมายการรักษา',
                'date' => $a->appointment,
            ]);
        }

        $appointments = $appointments->sortBy('date')->values();
        $appointmentCount = $appointments->count();

        // =========================
        // พฤติกรรมล่าสุด
        // =========================
        $observeLatest = Observe::where('client_id', $client->id)
            ->orderBy('date', 'desc')
            ->first();

        $observeDate = $observeLatest ? $observeLatest->date : null;

        // =========================
        // การบาดเจ็บวันนี้
        // =========================
        $accidents = Accident::where('client_id', $client->id)
            ->whereDate('incident_date', $today)
            ->get();

        $accidentCount = $accidents->count();

        $day = $today->locale('th')->translatedFormat('d');
        $month = $today->locale('th')->translatedFormat('F');
        $year = $today->year + 543;

        // =========================
        // ข้อมูลเบื้องต้นที่ยังขาด
        // =========================
        $requiredSummary = $this->getClientRequiredDataSummary($client);

        // คงชื่อตัวแปรเดิมไว้ เพื่อไม่ให้ส่วนอื่นกระทบ
        $serviceLogCount = $requiredSummary['missingCount'];
        $serviceLogLatestType = $requiredSummary['firstMissingType'];
        $serviceLogLatestDate = null;

        // =========================
        // Health
        // =========================
        $healthSummary = $this->getClientHealthSummary($client->id);

        $healthNextAppointment = $healthSummary['nextAppointment'];
        $healthAppointmentCount = $healthSummary['appointmentCount'];
        $healthMedicalCount = $healthSummary['medicalCount'];
        $healthPsychiatricCount = $healthSummary['psychiatricCount'];
        $healthVaccinationCount = $healthSummary['vaccinationCount'];
        $healthAccidentCount = $healthSummary['accidentCount'];
        $healthLatestTreatmentType = $healthSummary['latestTreatmentType'];
        $healthLatestTreatmentDate = $healthSummary['latestTreatmentDate'];

        // =========================

            // =========================
// Case Activities
// =========================
$latestActivity = CaseActivity::where('client_id', $client->id)
    ->latest('occurred_at')
    ->latest('id')
    ->first();

$activitiesCount = CaseActivity::where('client_id', $client->id)
    ->count();

$moduleLabels = [
    'client'           => 'ผู้รับบริการ',
    'education_record' => 'บันทึกการศึกษา',
    'psychiatric'      => 'พบจิตแพทย์',
    'medical'          => 'พบแพทย์',
    'vaccine'          => 'วัคซีน',
    'observe'          => 'สังเกตพฤติกรรม',
    'addictive'        => 'พฤติกรรมเสพติด',
    'escape'           => 'หนีออกจากสถานสงเคราะห์',
    'school_followup'  => 'ติดตามการเรียน',
    'help_session'     => 'การช่วยเหลือ',
    'job_agency'       => 'จัดหางาน',
    'refer'            => 'จำหน่าย / ส่งต่อ',
    'absent'           => 'ขาดเรียน',
    'operation'        => 'กิจกรรมประจำวัน',
    'estimate'         => 'แบบประเมิน',
    'health_checkup'   => 'ตรวจสุขภาพ',
    'accident'         => 'อุบัติเหตุ',
];

$latestActivityType = !empty($latestActivity?->module)
    ? ($moduleLabels[$latestActivity->module] ?? $latestActivity->module)
    : 'ไม่มีข้อมูล';

$latestActivityDate = $latestActivity?->occurred_at;


        // Publicizes


        // =========================
        $publicizeQuery = \App\Models\Publicize::query();

        $publicizeCount = (clone $publicizeQuery)->count();

        $publicizeLatest = (clone $publicizeQuery)
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->first();

        $publicizeLatestDate = $publicizeLatest->recorded_at ?? null;
        $publicizeLatestTitle = $publicizeLatest->title ?? 'ไม่มีข้อมูล';
        $publicizeLatestCategory = $publicizeLatest->category ?? 'ไม่มีข้อมูล';

        // =========================
        // Files
        // =========================
        $filesQuery = \App\Models\ClientFile::where('client_id', $client->id);

        $filesCount = (clone $filesQuery)->count();

        $latestFile = (clone $filesQuery)
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->first();

        $fileLatestType = $latestFile->file_type ?? 'ไม่มีข้อมูล';
        $fileLatestDate = $latestFile->uploaded_at ?? null;

        return view('admin_client.index.client_index', compact(
            'client',
            'appointmentCount',
            'appointments',
            'observeLatest',
            'observeDate',
            'accidentCount',
            'accidents',
            'day',
            'month',
            'year',
            'serviceLogCount',
            'serviceLogLatestType',
            'serviceLogLatestDate',
            'healthNextAppointment',
            'healthAppointmentCount',
            'healthMedicalCount',
            'healthPsychiatricCount',
            'healthVaccinationCount',
            'healthAccidentCount',
            'healthLatestTreatmentType',
            'healthLatestTreatmentDate',
            'publicizeCount',
            'publicizeLatestDate',
            'publicizeLatestTitle',
            'publicizeLatestCategory',
            'filesCount',
            'fileLatestType',
            'fileLatestDate',
            'activitiesCount',
            'latestActivityType',
            'latestActivityDate'
        ));
    }

    public function ClientReport($id)
    {
        $client = $this->findAuthorizedClient((int) $id, [
            'problems',
            'province',
            'district',
            'sub_district',
            'national',
            'religion',
            'marital',
            'occupation',
            'income',
            'education',
            'contact',
            'project',
            'status',
            'house',
            'target',
            'title',
            'originProvince',
            'originDistrict',
            'originSubDistrict',
            'members.education',
            'members.occupation',
            'members.income',
            'father',
            'mother',
            'relative',
            'spouse',
        ]);

        $factFinding = \App\Models\Factfinding::with([
            'marital',
            'documents',
        ])->where('client_id', $client->id)->first();

        // สภาพปัญหาของผู้รับบริการรายนี้เท่านั้น
        $clientProblems = $client->problems ?? collect();

        // ข้อ 18 การติดตามผลการช่วยเหลือ
        // เรียงจากวันที่เก่าไปใหม่ เพื่อให้อ่านลำดับการดำเนินงานต่อเนื่องในรายงาน
        $followups = Followup::where('client_id', $client->id)
            ->orderBy('followup_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin_client.index.client_report', compact(
            'client',
            'clientProblems',
            'factFinding',
            'followups'
        ));
    }

    /**
     * [NEW]
     * หน้าข้อมูลภาพรวมผู้รับบริการ
     */
   public function Overview($id)
{
    $client = $this->findAuthorizedClient((int) $id, [
        'educationRecords',
        'educationRecords.education',
        'educationRecords.institution',
        'house',
        'title',
        'national',
        'religion',
        'marital',
        'occupation',
        'income',
        'education',
        'contact',
        'status',
        'project',
        'target',
        'province',
        'district',
        'sub_district',
        'originProvince',
        'originDistrict',
        'originSubDistrict',
        'father',
        'mother',
        'spouse',
        'relative',
        'members',
        'files',
        'vaccinations',
        'refers',
    ]);

    $educationRecord = optional($client->educationRecords)->sortByDesc('id')->first();

    $memberCount = method_exists($client, 'members') ? $client->members()->count() : 0;
    $fileCount = method_exists($client, 'files') ? $client->files()->count() : 0;
    $vaccineCount = method_exists($client, 'vaccinations') ? $client->vaccinations()->count() : 0;
    $referCount = method_exists($client, 'refers') ? $client->refers()->count() : 0;

    $familyStatusCount = 0;
    $familyStatusCount += !empty($client->father) ? 1 : 0;
    $familyStatusCount += !empty($client->mother) ? 1 : 0;
    $familyStatusCount += !empty($client->spouse) ? 1 : 0;
    $familyStatusCount += !empty($client->relative) ? 1 : 0;

    $latestObserve = Observe::where('client_id', $client->id)
        ->orderBy('date', 'desc')
        ->first();

    $latestAccident = Accident::where('client_id', $client->id)
        ->orderBy('incident_date', 'desc')
        ->first();

    $nextMedical = Medical::where('client_id', $client->id)
        ->whereNotNull('appt_date')
        ->whereDate('appt_date', '>=', Carbon::today())
        ->orderBy('appt_date')
        ->first();

    $nextPsychiatric = Psychiatric::where('client_id', $client->id)
        ->whereNotNull('appoin_date')
        ->whereDate('appoin_date', '>=', Carbon::today())
        ->orderBy('appoin_date')
        ->first();

    $currentAcademicYear = $this->getCurrentAcademicYear();

    $filterMode = request('filter_mode', 'single');
    if (!in_array($filterMode, ['single', 'range'], true)) {
        $filterMode = 'single';
    }

    $selectedAcademicYear = (int) request('academic_year', $currentAcademicYear);
    if ($selectedAcademicYear < 2500 || $selectedAcademicYear > 2600) {
        $selectedAcademicYear = $currentAcademicYear;
    }

    $startAcademicYear = (int) request('start_academic_year', $selectedAcademicYear);
    $endAcademicYear = (int) request('end_academic_year', $selectedAcademicYear);

    if ($startAcademicYear < 2500 || $startAcademicYear > 2600) {
        $startAcademicYear = $currentAcademicYear;
    }

    if ($endAcademicYear < 2500 || $endAcademicYear > 2600) {
        $endAcademicYear = $currentAcademicYear;
    }

    if ($startAcademicYear > $endAcademicYear) {
        [$startAcademicYear, $endAcademicYear] = [$endAcademicYear, $startAcademicYear];
    }

    $academicYearOptions = collect(range(
        $currentAcademicYear - 5,
        $currentAcademicYear + 1
    ))->sortDesc()->values();

    if ($filterMode === 'range') {
        $overviewSummary = $this->buildOverviewAcademicRangeSummary(
            $client->id,
            $startAcademicYear,
            $endAcademicYear
        );
    } else {
        $overviewSummary = $this->buildOverviewAcademicSummary(
            $client->id,
            $selectedAcademicYear
        );
    }

    return view('admin_client.index.client_overview', compact(
        'client',
        'educationRecord',
        'memberCount',
        'fileCount',
        'vaccineCount',
        'referCount',
        'familyStatusCount',
        'latestObserve',
        'latestAccident',
        'nextMedical',
        'nextPsychiatric',
        'filterMode',
        'selectedAcademicYear',
        'startAcademicYear',
        'endAcademicYear',
        'academicYearOptions',
        'overviewSummary'
    ));
}

/**
 * [NEW]
 * คืนค่า route ถ้ามีจริง ถ้าไม่มีให้ fallback เพื่อไม่ให้หน้า overview พัง
 */
protected function safeRoute(string $routeName, $parameter = null): string
{
    if (Route::has($routeName)) {
        return route($routeName, $parameter);
    }

    return 'javascript:void(0)';
}

/**
 * [NEW]
 * สร้าง URL พร้อม query string ถ้า route มีจริง
 * ถ้า route ไม่มี ให้ fallback เพื่อไม่ให้หน้า overview พัง
 */
protected function safeRouteWithQuery(string $routeName, $parameter = null, array $query = []): string
{
    if (!Route::has($routeName)) {
        return 'javascript:void(0)';
    }

    $url = route($routeName, $parameter);

    if (!empty($query)) {
        $url .= '?' . http_build_query($query);
    }

    return $url;
}


/**
 * [NEW]
 * เตรียมข้อมูล service cards สำหรับหน้า overview
 * ใช้เฉพาะหน้านี้ เพื่อไม่กระทบกับ flow เดิมของส่วนอื่น
 */
/**
 * [NEW]
 * เตรียมข้อมูล service cards สำหรับหน้า overview
 * ใช้เฉพาะหน้านี้ เพื่อไม่กระทบกับ flow เดิมของส่วนอื่น
 */
/**
 * [NEW]
 * เตรียมข้อมูล service cards สำหรับหน้า overview
 * ใช้เฉพาะหน้านี้ เพื่อไม่กระทบกับ flow เดิมของส่วนอื่น
 */
/**
 * [NEW]
 * เตรียมข้อมูล service cards สำหรับหน้า overview
 * ใช้เฉพาะหน้านี้ เพื่อไม่กระทบกับ flow เดิมของส่วนอื่น
 */
protected function buildOverviewServiceCards(int $clientId): array
{
    $today = Carbon::today();

    // ภาคเรียน/ปีการศึกษาปัจจุบันแบบไทย
    // พ.ค.-ต.ค. = ภาคเรียนที่ 1, พ.ย.-เม.ย. = ภาคเรียนที่ 2
    $academicYear = $today->month >= 5 ? $today->year + 543 : $today->copy()->subYear()->year + 543;
    $semester = ($today->month >= 5 && $today->month <= 10) ? 1 : 2;

    // helper ย่อย: หา column วันที่จริงของ model แบบอัตโนมัติ
    $latestDateRange = function (?string $modelClass, array $candidateColumns = ['date']) use ($clientId, $today) {
        if (!$modelClass || !class_exists($modelClass)) {
            return [
                'from' => $today->format('Y-m-d'),
                'to'   => $today->format('Y-m-d'),
                'count' => 0,
            ];
        }

        $model = new $modelClass();
        $table = $model->getTable();

        $query = $modelClass::where('client_id', $clientId);
        $count = (clone $query)->count();

        $dateColumn = null;
        foreach ($candidateColumns as $column) {
            if (Schema::hasColumn($table, $column)) {
                $dateColumn = $column;
                break;
            }
        }

        if (!$dateColumn) {
            return [
                'from' => $today->format('Y-m-d'),
                'to'   => $today->format('Y-m-d'),
                'count' => $count,
            ];
        }

        $latest = (clone $query)
            ->whereNotNull($dateColumn)
            ->orderByDesc($dateColumn)
            ->orderByDesc('id')
            ->first();

        $defaultDate = !empty($latest?->{$dateColumn})
            ? Carbon::parse($latest->{$dateColumn})->format('Y-m-d')
            : $today->format('Y-m-d');

        return [
            'from' => $defaultDate,
            'to'   => $defaultDate,
            'count' => $count,
        ];
    };

    $absent = $latestDateRange(
        class_exists(Absent::class) ? Absent::class : null,
        ['absent_date', 'date', 'record_date', 'created_at']
    );

    $schoolFollowup = $latestDateRange(
        class_exists(SchoolFollowup::class) ? SchoolFollowup::class : null,
        ['followup_date', 'date', 'record_date', 'created_at']
    );

    $caseOutside = $latestDateRange(
        class_exists(CaseOutside::class) ? CaseOutside::class : null,
        ['date', 'outside_date', 'record_date', 'created_at']
    );

    $medical = $latestDateRange(
        Medical::class,
        ['date', 'appt_date', 'record_date', 'created_at']
    );

    return [
        [
            'title' => 'บันทึกการขาดเรียน',
            'description' => 'เปิดดูข้อมูลการขาดเรียนของผู้รับบริการตามปีการศึกษาและภาคเรียนปัจจุบัน',
            'icon' => 'bi-person-x-fill',
            'icon_class' => 'bg-primary-subtle text-primary',
            'count' => $absent['count'],
            'route' => $this->safeRouteWithQuery('absent.report.all', $clientId, [
                'academic_year' => $academicYear,
                'semester' => $semester,
            ]),
            'academic_year' => $academicYear,
            'semester' => $semester,
            'mode' => 'education',
        ],
        [
            'title' => 'บันทึกติดตามการศึกษาเด็กในโรงเรียน',
            'description' => 'เปิดดูบันทึกการติดตามในโรงเรียน โดยใช้ปีการศึกษาและภาคเรียนปัจจุบัน',
            'icon' => 'bi-building-check',
            'icon_class' => 'bg-success-subtle text-success',
            'count' => $schoolFollowup['count'],
            'route' => $this->safeRouteWithQuery('school_followup.report.all', $clientId, [
                'academic_year' => $academicYear,
                'semester' => $semester,
            ]),
            'academic_year' => $academicYear,
            'semester' => $semester,
            'mode' => 'education',
        ],
        [
            'title' => 'บันทึกติดตามเด็กที่อยู่ภายนอก',
            'description' => 'ดูรายการติดตามเด็กที่อยู่ภายนอก โดยกำหนดช่วงวันที่เริ่มต้นจากข้อมูลล่าสุด',
            'icon' => 'bi-house-door-fill',
            'icon_class' => 'bg-danger-subtle text-danger',
            'count' => $caseOutside['count'],
            'route' => $this->safeRouteWithQuery('case_outside.report.all', $clientId, [
                'from_date' => $caseOutside['from'],
                'to_date' => $caseOutside['to'],
            ]),
            'from' => $caseOutside['from'],
            'to' => $caseOutside['to'],
            'mode' => 'date',
        ],
        [
            'title' => 'บันทึกการเจ็บป่วย',
            'description' => 'รวมการเจ็บป่วย อุบัติเหตุ และจิตเวช (เฉพาะจิตเวชที่มีนัดพบแพทย์)',
            'icon' => 'bi-heart-pulse-fill',
            'icon_class' => 'bg-warning-subtle text-warning',
            'count' => $medical['count'],
            'route' => $this->safeRouteWithQuery('medical.report.all', $clientId, [
                'from_date' => $medical['from'],
                'to_date' => $medical['to'],
            ]),
            'from' => $medical['from'],
            'to' => $medical['to'],
            'mode' => 'date',
        ],
    ];
}

    /**
     * [UPDATED]
     * หน้ารวมข้อมูลที่ยังไม่ได้บันทึก / ควรบันทึก
     */
    public function ServiceLogs($id)
    {
        $client = $this->findAuthorizedClient((int) $id, [
            'educationRecords',
            'educationRecords.education',
            'educationRecords.institution',
            'house',
            'status',
            'project',
            'father',
            'mother',
            'spouse',
            'relative',
            'members',
            'files',
        ]);

        $requiredSummary = $this->getClientRequiredDataSummary($client);

        $services = $requiredSummary['items']; // คงชื่อตัวแปรเดิมไว้
        $totalCount = $requiredSummary['missingCount'];
        $latestType = $requiredSummary['firstMissingType'];
        $latestDate = null;

        $completedCount = $requiredSummary['completedCount'];
        $requiredTotal = $requiredSummary['totalRequired'];

        return view('admin_client.index.client_service_logs', compact(
            'client',
            'services',
            'totalCount',
            'latestType',
            'latestDate',
            'completedCount',
            'requiredTotal'
        ));
    }


    /**
 * [NEW]
 * ปีการศึกษาปัจจุบันแบบไทย (พ.ศ.)
 * พ.ค. - เม.ย. ของปีถัดไป
 */
protected function getCurrentAcademicYear(): int
{
    $today = Carbon::today();

    return $today->month >= 5
        ? $today->year + 543
        : $today->year + 542;
}

/**
 * [NEW]
 * ช่วงวันที่ของปีการศึกษา
 * เช่น 2568 = 1 พ.ค. 2025 - 30 เม.ย. 2026
 */
protected function getAcademicYearRange(int $academicYear): array
{
    $startYearAd = $academicYear - 543;

    $startDate = Carbon::create($startYearAd, 5, 1)->startOfDay();
    $endDate = Carbon::create($startYearAd + 1, 4, 30)->endOfDay();

    return [$startDate, $endDate];
}

/**
 * [NEW]
 * คืนชื่อคอลัมน์วันที่จริงของ model
 */
protected function resolveDateColumn(string $modelClass, array $candidateColumns): ?string
{
    if (!class_exists($modelClass)) {
        return null;
    }

    $model = new $modelClass();
    $table = $model->getTable();

    foreach ($candidateColumns as $column) {
        if (Schema::hasColumn($table, $column)) {
            return $column;
        }
    }

    return null;
}

/**
 * [NEW]
 * นับจำนวนข้อมูลของแต่ละโมดูลในช่วงปีการศึกษา
 */
protected function countRecordsInAcademicYear(
    ?string $modelClass,
    int $clientId,
    int $academicYear,
    array $candidateColumns
): int {
    if (!$modelClass || !class_exists($modelClass)) {
        return 0;
    }

    $dateColumn = $this->resolveDateColumn($modelClass, $candidateColumns);

    if (!$dateColumn) {
        return 0;
    }

    [$startDate, $endDate] = $this->getAcademicYearRange($academicYear);

    return $modelClass::where('client_id', $clientId)
        ->whereNotNull($dateColumn)
        ->whereDate($dateColumn, '>=', $startDate->toDateString())
        ->whereDate($dateColumn, '<=', $endDate->toDateString())
        ->count();
}

/**
 * [NEW]
 * สรุปข้อมูล overview ตามปีการศึกษา
 */
protected function buildOverviewAcademicSummary(int $clientId, int $academicYear): array
{
    [$startDate, $endDate] = $this->getAcademicYearRange($academicYear);

    $absentCount = $this->countRecordsInAcademicYear(
        class_exists(Absent::class) ? Absent::class : null,
        $clientId,
        $academicYear,
        ['absent_date', 'date', 'record_date', 'created_at']
    );

    $schoolFollowupCount = $this->countRecordsInAcademicYear(
        class_exists(SchoolFollowup::class) ? SchoolFollowup::class : null,
        $clientId,
        $academicYear,
        ['followup_date', 'date', 'record_date', 'created_at']
    );

    $caseOutsideCount = $this->countRecordsInAcademicYear(
        class_exists(CaseOutside::class) ? CaseOutside::class : null,
        $clientId,
        $academicYear,
        ['outside_date', 'date', 'record_date', 'created_at']
    );

    $medicalOnlyCount = $this->countRecordsInAcademicYear(
        Medical::class,
        $clientId,
        $academicYear,
        ['date', 'record_date', 'created_at', 'appt_date']
    );

    $accidentOnlyCount = $this->countRecordsInAcademicYear(
        Accident::class,
        $clientId,
        $academicYear,
        ['incident_date', 'date', 'record_date', 'created_at']
    );

    $psychiatricOnlyCount = $this->countRecordsInAcademicYear(
        Psychiatric::class,
        $clientId,
        $academicYear,
        ['appoin_date']
    );

    $medicalCount = $medicalOnlyCount + $accidentOnlyCount + $psychiatricOnlyCount;

    return [
        'mode' => 'single',
        'academic_year' => $academicYear,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'items' => [
            [
                'title' => 'การขาดเรียน',
                'count' => $absentCount,
                'icon' => 'bi-person-x-fill',
                'icon_class' => 'bg-primary-subtle text-primary',
                'description' => 'จำนวนครั้งที่บันทึกการขาดเรียนในปีการศึกษาที่เลือก',
            ],
            [
                'title' => 'ติดตามที่โรงเรียน',
                'count' => $schoolFollowupCount,
                'icon' => 'bi-building-check',
                'icon_class' => 'bg-success-subtle text-success',
                'description' => 'จำนวนครั้งที่ติดตามเด็กในสถานศึกษา',
            ],
            [
                'title' => 'ติดตามเด็กที่อยู่ภายนอก',
                'count' => $caseOutsideCount,
                'icon' => 'bi-house-door-fill',
                'icon_class' => 'bg-danger-subtle text-danger',
                'description' => 'จำนวนครั้งที่ติดตามเด็กที่อยู่นอกสถานศึกษา/ภายนอก',
            ],
            [
                'title' => 'การเจ็บป่วย',
                'count' => $medicalCount,
                'icon' => 'bi-heart-pulse-fill',
                'icon_class' => 'bg-warning-subtle text-warning',
                'description' => 'รวมการเจ็บป่วย อุบัติเหตุ และจิตเวชตามปีการศึกษาที่เลือก',
            ],
        ],
        'total_count' => $absentCount + $schoolFollowupCount + $caseOutsideCount + $medicalCount,
        'rows' => [
            [
                'academic_year' => $academicYear,
                'absent_count' => $absentCount,
                'school_followup_count' => $schoolFollowupCount,
                'case_outside_count' => $caseOutsideCount,
                'medical_count' => $medicalCount,
                'total_count' => $absentCount + $schoolFollowupCount + $caseOutsideCount + $medicalCount,
            ],
        ],
    ];
}

protected function buildOverviewAcademicRangeSummary(int $clientId, int $startAcademicYear, int $endAcademicYear): array
{
    $rows = collect();

    for ($year = $startAcademicYear; $year <= $endAcademicYear; $year++) {
        $yearSummary = $this->buildOverviewAcademicSummary($clientId, $year);

        $row = $yearSummary['rows'][0] ?? null;
        if ($row) {
            $rows->push($row);
        }
    }

    $absentCount = $rows->sum('absent_count');
    $schoolFollowupCount = $rows->sum('school_followup_count');
    $caseOutsideCount = $rows->sum('case_outside_count');
    $medicalCount = $rows->sum('medical_count');

    [$startDate] = $this->getAcademicYearRange($startAcademicYear);
    [, $endDate] = $this->getAcademicYearRange($endAcademicYear);

    return [
        'mode' => 'range',
        'start_academic_year' => $startAcademicYear,
        'end_academic_year' => $endAcademicYear,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'items' => [
            [
                'title' => 'การขาดเรียน',
                'count' => $absentCount,
                'icon' => 'bi-person-x-fill',
                'icon_class' => 'bg-primary-subtle text-primary',
                'description' => 'รวมจำนวนครั้งการขาดเรียนในช่วงปีการศึกษาที่เลือก',
            ],
            [
                'title' => 'ติดตามที่โรงเรียน',
                'count' => $schoolFollowupCount,
                'icon' => 'bi-building-check',
                'icon_class' => 'bg-success-subtle text-success',
                'description' => 'รวมจำนวนครั้งที่ติดตามเด็กในสถานศึกษา',
            ],
            [
                'title' => 'ติดตามเด็กที่อยู่ภายนอก',
                'count' => $caseOutsideCount,
                'icon' => 'bi-house-door-fill',
                'icon_class' => 'bg-danger-subtle text-danger',
                'description' => 'รวมจำนวนครั้งที่ติดตามเด็กที่อยู่ภายนอก',
            ],
            [
                'title' => 'การเจ็บป่วย',
                'count' => $medicalCount,
                'icon' => 'bi-heart-pulse-fill',
                'icon_class' => 'bg-warning-subtle text-warning',
                'description' => 'รวมการเจ็บป่วย อุบัติเหตุ และจิตเวชในช่วงปีการศึกษา',
            ],
        ],
        'total_count' => $absentCount + $schoolFollowupCount + $caseOutsideCount + $medicalCount,
        'rows' => $rows->values()->toArray(),
    ];
}
    /**
     * [NEW]
     * สรุปรายการข้อมูลเบื้องต้นที่จำเป็น/ควรบันทึก
     */
    protected function getClientRequiredDataSummary(Client $client): array
    {
        $items = collect();

        $factfindingCount = class_exists(\App\Models\Factfinding::class)
            ? \App\Models\Factfinding::where('client_id', $client->id)->count()
            : 0;

        $familyCount = 0;
        $familyCount += !empty($client->father) ? 1 : 0;
        $familyCount += !empty($client->mother) ? 1 : 0;
        $familyCount += !empty($client->spouse) ? 1 : 0;
        $familyCount += !empty($client->relative) ? 1 : 0;

        $memberCount = method_exists($client, 'members') ? $client->members()->count() : 0;
        $fileCount = method_exists($client, 'files') ? $client->files()->count() : 0;

        $checkBodyCount = class_exists(\App\Models\CheckBody::class)
            ? \App\Models\CheckBody::where('client_id', $client->id)->count()
            : 0;

        $educationRecordCount = class_exists(\App\Models\EducationRecord::class)
            ? \App\Models\EducationRecord::where('client_id', $client->id)->count()
            : 0;

        $followupCount = class_exists(\App\Models\Followup::class)
            ? \App\Models\Followup::where('client_id', $client->id)->count()
            : 0;

        $items->push([
            'type' => 'สอบข้อเท็จจริง',
            'count' => $factfindingCount,
            'status' => $factfindingCount > 0 ? 'complete' : 'missing',
            'description' => 'ควรมีข้อมูลการสอบข้อเท็จจริงเบื้องต้นของผู้รับบริการ',
            'model' => 'Factfinding',
        ]);

        $items->push([
            'type' => 'ข้อมูลครอบครัว',
            'count' => $familyCount,
            'status' => $familyCount > 0 ? 'complete' : 'missing',
            'description' => 'ควรมีข้อมูลบิดา มารดา คู่สมรส หรือญาติ อย่างน้อย 1 ส่วน',
            'model' => 'Family',
        ]);

        $items->push([
            'type' => 'สมาชิกครอบครัว',
            'count' => $memberCount,
            'status' => $memberCount > 0 ? 'complete' : 'missing',
            'description' => 'ควรมีรายการสมาชิกในครอบครัวอย่างน้อย 1 รายการ',
            'model' => 'Member',
        ]);

        $items->push([
            'type' => 'ไฟล์เอกสารผู้รับบริการ',
            'count' => $fileCount,
            'status' => $fileCount > 0 ? 'complete' : 'missing',
            'description' => 'ควรมีไฟล์หรือเอกสารสำคัญแนบไว้ในระบบ',
            'model' => 'ClientFile',
        ]);

        $items->push([
            'type' => 'ตรวจร่างกาย',
            'count' => $checkBodyCount,
            'status' => $checkBodyCount > 0 ? 'complete' : 'missing',
            'description' => 'ควรมีข้อมูลการตรวจร่างกายเบื้องต้น',
            'model' => 'CheckBody',
        ]);

        $items->push([
            'type' => 'ประวัติการศึกษา',
            'count' => $educationRecordCount,
            'status' => $educationRecordCount > 0 ? 'complete' : 'missing',
            'description' => 'ควรมีข้อมูลประวัติการศึกษาอย่างน้อย 1 รายการ',
            'model' => 'EducationRecord',
        ]);

        $items->push([
            'type' => 'ติดตามผล',
            'count' => $followupCount,
            'status' => $followupCount > 0 ? 'complete' : 'missing',
            'description' => 'ควรมีข้อมูลการติดตามผลการช่วยเหลือ',
            'model' => 'Followup',
        ]);

        $missingItems = $items->where('status', 'missing')->values();
        $completedItems = $items->where('status', 'complete')->values();

        return [
            'items' => $items->values(),
            'missingCount' => $missingItems->count(),
            'completedCount' => $completedItems->count(),
            'totalRequired' => $items->count(),
            'firstMissingType' => $missingItems->first()['type'] ?? 'ข้อมูลเบื้องต้นครบถ้วน',
        ];
    }

    /**
     * [NEW]
     * สรุปข้อมูลสุขภาพและการรักษา
     * หน้า dashboard ดึงเฉพาะสรุป ไม่ดึงรายการเต็ม
     */
        protected function getClientHealthSummary(int $clientId): array
    {
        $today = Carbon::today();

        // =========================
        // นัดหมายล่วงหน้า
        // =========================
        $appointments = collect();

        $medicalAppointments = Medical::where('client_id', $clientId)
            ->whereNotNull('appt_date')
            ->whereDate('appt_date', '>=', $today)
            ->orderBy('appt_date')
            ->get(['id', 'appt_date']);

        foreach ($medicalAppointments as $item) {
            $appointments->push([
                'type' => 'พบแพทย์',
                'date' => $item->appt_date,
                'source' => 'medical',
                'id' => $item->id,
            ]);
        }

        $psychiatricAppointments = Psychiatric::where('client_id', $clientId)
            ->whereNotNull('appoin_date')
            ->whereDate('appoin_date', '>=', $today)
            ->orderBy('appoin_date')
            ->get(['id', 'appoin_date']);

        foreach ($psychiatricAppointments as $item) {
            $appointments->push([
                'type' => 'พบจิตแพทย์',
                'date' => $item->appoin_date,
                'source' => 'psychiatric',
                'id' => $item->id,
            ]);
        }

        $accidentAppointments = Accident::where('client_id', $clientId)
            ->whereNotNull('appointment')
            ->whereDate('appointment', '>=', $today)
            ->orderBy('appointment')
            ->get(['id', 'appointment', 'treat_no']);

        foreach ($accidentAppointments as $item) {
            $appointments->push([
                'type' => !empty($item->treat_no) ? trim($item->treat_no) : 'นัดหมายการรักษา',
                'date' => $item->appointment,
                'source' => 'accident',
                'id' => $item->id,
                'treat_no' => $item->treat_no,
            ]);
        }

        $appointments = $appointments
            ->sortBy('date')
            ->values();

        $nextAppointment = $appointments->first();
        $appointmentCount = $appointments->count();

        // =========================
        // นัดหมายแยกตามหมวด
        // =========================
        $medicalAppointmentsList = $appointments
            ->where('source', 'medical')
            ->values();

        $psychiatricAppointmentsList = $appointments
            ->where('source', 'psychiatric')
            ->values();

        $accidentAppointmentsList = $appointments
            ->where('source', 'accident')
            ->values();

        $appointmentDates = $appointments
            ->pluck('date')
            ->filter()
            ->map(function ($date) {
                return Carbon::parse($date)->locale('th')->translatedFormat('d F') . ' ' . (Carbon::parse($date)->year + 543);
            })
            ->unique()
            ->values()
            ->toArray();

        // =========================
        // นับจำนวนแต่ละหมวด
        // =========================
        $medicalCount = $medicalAppointmentsList->count();
        $psychiatricCount = $psychiatricAppointmentsList->count();
        $vaccinationCount = class_exists(Vaccination::class)
            ? Vaccination::where('client_id', $clientId)->count()
            : 0;
        $accidentCount = $accidentAppointmentsList->count();

        // =========================
        // วันที่รักษาล่าสุด
        // =========================
        $latestHealthRecords = collect();

        $latestMedical = Medical::where('client_id', $clientId)
            ->orderByDesc('appt_date')
            ->orderByDesc('id')
            ->first();

        if ($latestMedical && !empty($latestMedical->appt_date)) {
            $latestHealthRecords->push([
                'type' => 'พบแพทย์',
                'date' => $latestMedical->appt_date,
            ]);
        }

        $latestPsychiatric = Psychiatric::where('client_id', $clientId)
            ->orderByDesc('appoin_date')
            ->orderByDesc('id')
            ->first();

        if ($latestPsychiatric && !empty($latestPsychiatric->appoin_date)) {
            $latestHealthRecords->push([
                'type' => 'พบจิตแพทย์',
                'date' => $latestPsychiatric->appoin_date,
            ]);
        }

        $latestAccident = Accident::where('client_id', $clientId)
            ->orderByDesc('incident_date')
            ->orderByDesc('id')
            ->first();

        if ($latestAccident && !empty($latestAccident->incident_date)) {
            $latestHealthRecords->push([
                'type' => 'อุบัติเหตุ/การบาดเจ็บ',
                'date' => $latestAccident->incident_date,
            ]);
        }

        if (class_exists(Vaccination::class)) {
            $latestVaccination = Vaccination::where('client_id', $clientId)
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->first();

            if ($latestVaccination && !empty($latestVaccination->date)) {
                $latestHealthRecords->push([
                    'type' => 'วัคซีน',
                    'date' => $latestVaccination->date,
                ]);
            }
        }

        $latestHealthRecords = $latestHealthRecords
            ->sortByDesc('date')
            ->values();

        $latestTreatment = $latestHealthRecords->first();

        return [
            'nextAppointment' => $nextAppointment,
            'appointmentCount' => $appointmentCount,
            'appointmentDates' => $appointmentDates,

            'medicalCount' => $medicalCount,
            'medicalAppointmentsList' => $medicalAppointmentsList,

            'psychiatricCount' => $psychiatricCount,
            'psychiatricAppointmentsList' => $psychiatricAppointmentsList,

            'vaccinationCount' => $vaccinationCount,

            'accidentCount' => $accidentCount,
            'accidentAppointmentsList' => $accidentAppointmentsList,

            'latestTreatmentType' => $latestTreatment['type'] ?? 'ไม่มีข้อมูล',
            'latestTreatmentDate' => $latestTreatment['date'] ?? null,
        ];
    }
        /**
         * [NEW]
         * หน้าสุขภาพและการรักษา
         */
        public function HealthDashboard($id)
        {
            $client = $this->findAuthorizedClient((int) $id, [
                'educationRecords',
                'educationRecords.education',
                'educationRecords.institution',
                'house',
                'status',
                'project',
                'vaccinations',
            ]);

            $healthSummary = $this->getClientHealthSummary($client->id);

            return view('admin_client.index.client_health_dashboard', compact(
                'client',
                'healthSummary'
            ));
        }
    }