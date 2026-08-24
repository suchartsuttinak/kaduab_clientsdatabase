<?php

namespace App\Http\Controllers\Frontend\University;

use App\Models\EducationRecord;
use App\Models\Institution;
use App\Models\UniversityEnrollment;
use App\Support\UniversityCurrentEducationSource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UniversityEnrollmentController extends UniversityBaseController
{
    public function index(Request $request): View
    {
        $this->requireUniversityPermission('view');

        $year = $request->integer('academic_year');
        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());

        $query = UniversityEnrollment::query()
            ->with(['client', 'semesterRecords' => fn ($q) => $q->orderByDesc('academic_year')->orderByDesc('term')])
            ->whereHas('client', fn ($q) => $q->forUser(auth()->user()));

        if ($year) {
            $query->where(function ($q) use ($year) {
                $q->where('admission_academic_year', '<=', $year)
                    ->where(function ($inner) use ($year) {
                        $inner->whereHas('semesterRecords', fn ($s) => $s->where('academic_year', $year))
                            ->orWhere('current_status', 'studying');
                    });
            });
        }

        if ($status !== '') {
            $query->where('current_status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('university_name', 'like', "%{$search}%")
                    ->orWhere('faculty', 'like', "%{$search}%")
                    ->orWhere('major', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where(function ($nameQuery) use ($search) {
                            $nameQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('register_number', 'like', "%{$search}%");

                            if (ctype_digit($search)) {
                                $nameQuery->orWhereKey((int) $search);
                            }
                        });
                    });
            });
        }

        $enrollments = $query->latest('id')->paginate(20)->withQueryString();

        return view('university.enrollments.index', [
            'enrollments' => $enrollments,
            'universityPermissions' => $this->universityPermissionBag(),
        ]);
    }

    public function client(int $clientId): RedirectResponse|View
    {
        $this->requireUniversityPermission('view');
        $client = $this->scopedClient($clientId);

        $enrollment = UniversityEnrollment::query()
            ->where('client_id', $client->id)
            ->latest('id')
            ->first();

        if ($enrollment) {
            return redirect()->route('university.enrollments.show', $enrollment);
        }

        return view('university.enrollments.empty', [
            'client' => $client,
            'universityPermissions' => $this->universityPermissionBag(),
        ]);
    }

    public function create(int $clientId): RedirectResponse|View
    {
        $this->requireUniversityPermission('create');
        $client = $this->scopedClient($clientId);

        $latestEducationRecord = UniversityCurrentEducationSource::latestForClient($client->id);

        if (!$latestEducationRecord) {
            return redirect()
                ->route('education_record_add', $client->id)
                ->with('info', 'กรุณาบันทึกภาคเรียน ระดับการศึกษา และสถานศึกษาปัจจุบันก่อนเข้าเมนูเด็กมหาวิทยาลัย');
        }

        return view('university.enrollments.form', [
            'client' => $client,
            'enrollment' => new UniversityEnrollment(),
            'latestEducationRecord' => $latestEducationRecord,
            'isEdit' => false,
            'universityPermissions' => $this->universityPermissionBag(),
        ]);
    }

    public function store(Request $request, int $clientId): RedirectResponse
    {
        $this->requireUniversityPermission('create');
        $client = $this->scopedClient($clientId);
        $latestEducationRecord = UniversityCurrentEducationSource::latestForClient($client->id);

        if (!$latestEducationRecord) {
            return redirect()
                ->route('education_record_add', $client->id)
                ->with('info', 'กรุณาบันทึกภาคเรียน ระดับการศึกษา และสถานศึกษาปัจจุบันก่อนเข้าเมนูเด็กมหาวิทยาลัย');
        }

        $validated = $this->validateEnrollment($request, null);

        $validated['client_id'] = $client->id;
        $validated['university_name'] = $this->normalizeText((string) $latestEducationRecord->school_name);

        $institution = Institution::firstOrCreate(['institution_name' => $validated['university_name']]);
        $validated['institution_id'] = $institution->id;

        $duplicate = UniversityEnrollment::query()
            ->where('client_id', $client->id)
            ->where('institution_id', $institution->id)
            ->where('admission_academic_year', $validated['admission_academic_year'])
            ->exists();

        if ($duplicate) {
            return back()->withInput()->with('error', 'มีข้อมูลการเข้าศึกษาของผู้รับบริการ มหาวิทยาลัย และปีที่เข้าเรียนนี้แล้ว');
        }

        $enrollment = UniversityEnrollment::create($validated);

        return redirect()->route('university.enrollments.show', $enrollment)
            ->with('success', 'สร้างประวัติการศึกษาระดับมหาวิทยาลัยเรียบร้อยแล้ว');
    }

    public function show(int $id): View
    {
        $this->requireUniversityPermission('view');
        $enrollment = $this->scopedEnrollment($id);
        $enrollment->load([
            'semesterRecords' => fn ($q) => $q
                ->with(['semester', 'educationRecord.education', 'subjects', 'documents'])
                ->withCount('followups')
                ->orderByDesc('academic_year')
                ->orderByDesc('term'),
            'followups' => fn ($q) => $q->with(['semester', 'issues'])->latest('followup_date'),
            'outcome.reasons',
        ]);

        return view('university.enrollments.show', [
            'enrollment' => $enrollment,
            'client' => $enrollment->client,
            'currentEducationRecord' => UniversityCurrentEducationSource::latestForClient($enrollment->client_id),
            'universityPermissions' => $this->universityPermissionBag(),
        ]);
    }

    public function edit(int $id): View
    {
        $this->requireUniversityPermission('update');
        $enrollment = $this->scopedEnrollment($id);

        return view('university.enrollments.form', [
            'client' => $enrollment->client,
            'enrollment' => $enrollment,
            'latestEducationRecord' => null,
            'isEdit' => true,
            'universityPermissions' => $this->universityPermissionBag(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->requireUniversityPermission('update');
        $enrollment = $this->scopedEnrollment($id);
        $validated = $this->validateEnrollment($request, $enrollment);
        $validated['university_name'] = $this->normalizeText($validated['university_name']);

        $institution = Institution::firstOrCreate(['institution_name' => $validated['university_name']]);
        $validated['institution_id'] = $institution->id;

        $duplicate = UniversityEnrollment::query()
            ->where('client_id', $enrollment->client_id)
            ->where('institution_id', $institution->id)
            ->where('admission_academic_year', $validated['admission_academic_year'])
            ->where('id', '!=', $enrollment->id)
            ->exists();

        if ($duplicate) {
            return back()->withInput()->with('error', 'มีข้อมูลการเข้าศึกษาของผู้รับบริการ มหาวิทยาลัย และปีที่เข้าเรียนนี้แล้ว');
        }

        $enrollment->update($validated);

        return redirect()->route('university.enrollments.show', $enrollment)
            ->with('success', 'แก้ไขข้อมูลการศึกษาระดับมหาวิทยาลัยเรียบร้อยแล้ว');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->requireUniversityPermission('delete');
        $enrollment = $this->scopedEnrollment($id);
        $clientId = $enrollment->client_id;
        $enrollment->load('semesterRecords.documents');
        foreach ($enrollment->semesterRecords as $semesterRecord) {
            foreach ($semesterRecord->documents as $document) {
                if ($document->file_path) {
                    Storage::disk('local')->delete($document->file_path);
                }
            }
        }
        $enrollment->delete();

        return redirect()->route('university.client', $clientId)
            ->with('success', 'ลบข้อมูลการศึกษาระดับมหาวิทยาลัยเรียบร้อยแล้ว');
    }

    private function validateEnrollment(Request $request, ?UniversityEnrollment $existing = null): array
    {
        // สถานะปลายทางต้องบันทึกผ่านหน้าผลสิ้นสุด เพื่อให้เหตุผลและสถิติครบถ้วน
        $statuses = ['studying', 'leave', 'lost_contact', 'other'];
        if ($existing && in_array($existing->current_status, ['transferred', 'graduated', 'dropout', 'dismissed'], true)) {
            $statuses[] = $existing->current_status;
        }
        $today = now('Asia/Bangkok')->toDateString();

        return $request->validate([
            'university_name' => ['required', 'string', 'max:255'],
            'student_code' => ['nullable', 'string', 'max:100'],
            'faculty' => ['required', 'string', 'max:255'],
            'major' => ['required', 'string', 'max:255'],
            'degree_name' => ['nullable', 'string', 'max:255'],
            'program_type' => ['nullable', 'string', 'max:100'],
            'admission_academic_year' => ['required', 'integer', 'between:2400,2800'],
            'admission_term' => ['nullable', 'integer', 'between:1,3'],
            'admission_date' => ['nullable', 'date', 'before_or_equal:' . $today],
            'curriculum_years' => ['nullable', 'integer', 'between:1,8'],
            'expected_graduation_year' => ['nullable', 'integer', 'between:2400,2800'],
            'current_status' => ['required', Rule::in($statuses)],
            'funding_type' => ['nullable', 'string', 'max:120'],
            'scholarship_name' => ['nullable', 'string', 'max:255'],
            'scholarship_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'note' => ['nullable', 'string', 'max:5000'],
        ], [
            'university_name.required' => 'กรุณากรอกชื่อมหาวิทยาลัย',
            'faculty.required' => 'กรุณากรอกคณะ',
            'major.required' => 'กรุณากรอกสาขาวิชา/วิชาเอก',
            'admission_academic_year.required' => 'กรุณากรอกปีการศึกษาที่เข้าศึกษา',
            'admission_date.before_or_equal' => 'วันที่เข้าศึกษาต้องไม่เกินวันปัจจุบัน',
        ]);
    }

    private function normalizeText(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);
    }
}
