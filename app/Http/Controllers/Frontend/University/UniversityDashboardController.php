<?php

namespace App\Http\Controllers\Frontend\University;

use App\Models\UniversityEnrollment;
use App\Models\UniversityFollowup;
use App\Models\UniversityFollowupIssue;
use App\Models\UniversityOutcome;
use App\Models\UniversityOutcomeReason;
use App\Models\UniversitySemesterRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UniversityDashboardController extends UniversityBaseController
{
    public function index(Request $request): View
    {
        $this->requireUniversityPermission('view');

        $allowedEnrollmentIds = UniversityEnrollment::query()
            ->whereHas('client', fn ($q) => $q->forUser(auth()->user()))
            ->pluck('id');

        $years = UniversitySemesterRecord::query()
            ->whereIn('enrollment_id', $allowedEnrollmentIds)
            ->select('academic_year')
            ->distinct()
            ->pluck('academic_year')
            ->merge(
                UniversityEnrollment::query()
                    ->whereIn('id', $allowedEnrollmentIds)
                    ->pluck('admission_academic_year')
            )
            ->merge(
                UniversityOutcome::query()
                    ->whereIn('enrollment_id', $allowedEnrollmentIds)
                    ->pluck('academic_year')
            )
            ->filter()
            ->map(fn ($year) => (int) $year)
            ->unique()
            ->sortDesc()
            ->values();

        $defaultYear = $years->first() ?? (now('Asia/Bangkok')->year + 543);
        $academicYear = (int) $request->integer('academic_year', $defaultYear);

        $semesterQuery = UniversitySemesterRecord::query()
            ->whereIn('enrollment_id', $allowedEnrollmentIds)
            ->where('academic_year', $academicYear);

        $studentCount = (clone $semesterQuery)->distinct()->count('enrollment_id');
        $yearLevelCounts = (clone $semesterQuery)
            ->select('year_level', DB::raw('COUNT(DISTINCT enrollment_id) as total'))
            ->groupBy('year_level')
            ->pluck('total', 'year_level');

        $riskCounts = (clone $semesterQuery)
            ->select('risk_level', DB::raw('COUNT(DISTINCT enrollment_id) as total'))
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level');

        $atRiskStudentCount = (clone $semesterQuery)
            ->whereIn('risk_level', ['risk', 'high_risk'])
            ->distinct()
            ->count('enrollment_id');

        $gpaBelowTwo = (clone $semesterQuery)
            ->whereNotNull('semester_gpa')
            ->where('semester_gpa', '<', 2.00)
            ->distinct()
            ->count('enrollment_id');

        $followupCount = UniversityFollowup::query()
            ->whereIn('enrollment_id', $allowedEnrollmentIds)
            ->where('academic_year', $academicYear)
            ->count();

        $outcomeCounts = UniversityOutcome::query()
            ->whereIn('enrollment_id', $allowedEnrollmentIds)
            ->where('academic_year', $academicYear)
            ->select('outcome_type', DB::raw('COUNT(*) as total'))
            ->groupBy('outcome_type')
            ->pluck('total', 'outcome_type');

        $cohortEnrollmentIds = UniversityEnrollment::query()
            ->whereIn('id', $allowedEnrollmentIds)
            ->where('admission_academic_year', $academicYear)
            ->pluck('id');

        $cohortTotal = $cohortEnrollmentIds->count();
        $cohortStatusCounts = UniversityEnrollment::query()
            ->whereIn('id', $cohortEnrollmentIds)
            ->select('current_status', DB::raw('COUNT(*) as total'))
            ->groupBy('current_status')
            ->pluck('total', 'current_status');

        $issueCounts = UniversityFollowupIssue::query()
            ->join('university_followups as uf', 'university_followup_issues.followup_id', '=', 'uf.id')
            ->whereIn('uf.enrollment_id', $allowedEnrollmentIds)
            ->where('uf.academic_year', $academicYear)
            ->select('university_followup_issues.category', DB::raw('COUNT(DISTINCT uf.enrollment_id) as total'))
            ->groupBy('university_followup_issues.category')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $dropoutReasonCounts = UniversityOutcomeReason::query()
            ->whereHas('outcome', function ($q) use ($allowedEnrollmentIds, $academicYear) {
                $q->whereIn('enrollment_id', $allowedEnrollmentIds)
                    ->where('academic_year', $academicYear)
                    ->whereIn('outcome_type', ['dropout', 'dismissed']);
            })
            ->select('reason_code', DB::raw('COUNT(*) as total'))
            ->groupBy('reason_code')
            ->orderByDesc('total')
            ->get();

        $highRiskStudents = UniversitySemesterRecord::query()
            ->with(['enrollment.client', 'semester'])
            ->whereIn('enrollment_id', $allowedEnrollmentIds)
            ->where('academic_year', $academicYear)
            ->whereIn('risk_level', ['risk', 'high_risk'])
            ->orderByRaw("FIELD(risk_level, 'high_risk', 'risk')")
            ->orderBy('semester_gpa')
            ->limit(12)
            ->get();

        $recentFollowups = UniversityFollowup::query()
            ->with(['enrollment.client', 'semester'])
            ->whereIn('enrollment_id', $allowedEnrollmentIds)
            ->where('academic_year', $academicYear)
            ->latest('followup_date')
            ->limit(10)
            ->get();

        return view('university.dashboard.index', [
            'academicYear' => $academicYear,
            'years' => $years,
            'studentCount' => $studentCount,
            'yearLevelCounts' => $yearLevelCounts,
            'riskCounts' => $riskCounts,
            'atRiskStudentCount' => $atRiskStudentCount,
            'gpaBelowTwo' => $gpaBelowTwo,
            'followupCount' => $followupCount,
            'outcomeCounts' => $outcomeCounts,
            'cohortTotal' => $cohortTotal,
            'cohortStatusCounts' => $cohortStatusCounts,
            'issueCounts' => $issueCounts,
            'dropoutReasonCounts' => $dropoutReasonCounts,
            'highRiskStudents' => $highRiskStudents,
            'recentFollowups' => $recentFollowups,
            'universityPermissions' => $this->universityPermissionBag(),
        ]);
    }
}
