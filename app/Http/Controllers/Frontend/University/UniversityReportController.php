<?php

namespace App\Http\Controllers\Frontend\University;

use App\Models\SchoolFollowup;
use App\Support\UniversityCurrentEducationSource;
use Illuminate\View\View;

class UniversityReportController extends UniversityBaseController
{
    public function semester(int $id): View
    {
        $this->requireUniversityPermission('print');

        $record = $this->scopedSemesterRecord($id);
        $record->load([
            'educationRecord.education',
            'educationRecord.semester',
            'subjects',
            'documents',
            'followups' => fn ($query) => $query
                ->with('issues')
                ->orderBy('sequence_no')
                ->orderBy('followup_date')
                ->orderBy('id'),
        ]);

        $enrollment = $record->enrollment;
        $enrollment->load('client');
        $client = $enrollment->client;

        $schoolFollowups = collect();

        if ($record->education_record_id) {
            $schoolFollowups = SchoolFollowup::query()
                ->where('client_id', $client->id)
                ->where('education_record_id', $record->education_record_id)
                ->orderBy('follow_date')
                ->orderBy('id')
                ->get();
        }

        return view('university.reports.semester', [
            'record' => $record,
            'enrollment' => $enrollment,
            'client' => $client,
            'schoolFollowups' => $schoolFollowups,
            'currentEducationRecord' => UniversityCurrentEducationSource::latestForClient($client->id),
        ]);
    }

    public function enrollment(int $id): View
    {
        $this->requireUniversityPermission('print');

        $enrollment = $this->scopedEnrollment($id);
        $enrollment->load([
            'client',
            'semesterRecords' => fn ($query) => $query
                ->with([
                    'educationRecord.education',
                    'educationRecord.semester',
                    'subjects',
                    'documents',
                    'followups' => fn ($followups) => $followups
                        ->with('issues')
                        ->orderBy('sequence_no')
                        ->orderBy('followup_date')
                        ->orderBy('id'),
                ])
                ->orderBy('academic_year')
                ->orderBy('term'),
            'outcome.reasons',
        ]);

        $educationRecordIds = $enrollment->semesterRecords
            ->pluck('education_record_id')
            ->filter()
            ->unique()
            ->values();

        $schoolFollowupsByEducationRecord = collect();

        if ($educationRecordIds->isNotEmpty()) {
            $schoolFollowupsByEducationRecord = SchoolFollowup::query()
                ->where('client_id', $enrollment->client_id)
                ->whereIn('education_record_id', $educationRecordIds)
                ->orderBy('follow_date')
                ->orderBy('id')
                ->get()
                ->groupBy('education_record_id');
        }

        return view('university.reports.enrollment', [
            'enrollment' => $enrollment,
            'client' => $enrollment->client,
            'schoolFollowupsByEducationRecord' => $schoolFollowupsByEducationRecord,
            'currentEducationRecord' => UniversityCurrentEducationSource::latestForClient($enrollment->client_id),
        ]);
    }
}
