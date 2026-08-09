<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipChild;
use App\Models\ScholarshipExpense;
use App\Models\ScholarshipExpenseAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class ScholarshipChildController extends Controller
{
    private const EXPENSE_TYPES = [
        'ทุนการศึกษา',
        'ค่าเทอม',
        'ค่าหอพัก',
        'ค่าอุปกรณ์การเรียน',
        'ค่าเดินทาง',
        'ค่าอาหาร',
        'ค่าใช้จ่ายอื่น ๆ',
    ];

    /** ข้อมูลประจำตัวที่ใช้ร่วมกันทุกคำขอของบุคคลเดียวกัน */
    private const PROFILE_FIELDS = [
        'first_name',
        'last_name',
        'gender',
        'current_address',
        'guardian_name',
        'phone',
    ];

    /** ข้อมูลที่เปลี่ยนได้ในแต่ละปีการศึกษา/ภาคเรียน */
    private const APPLICATION_FIELDS = [
        'age',
        'education_level',
        'school_name',
        'reason',
        'help_needed',
        'more_detail',
    ];

    protected function saveChildPhoto($file): string
    {
        $destinationPath = storage_path('app/private/scholarship_children');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $filename = Str::uuid()->toString() . '.jpg';
        $relativePath = 'scholarship_children/' . $filename;
        $fullPath = storage_path('app/private/' . $relativePath);

        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());
        $image = $image->orient();
        $image->scaleDown(width: 1000);

        $image->toJpeg(
            quality: 70,
            progressive: true
        )->save($fullPath);

        return $relativePath;
    }

    protected function deleteChildPhoto(?string $path): void
    {
        if (!$path) {
            return;
        }

        $normalized = ltrim(str_replace('\\', '/', trim($path)), '/');
        if ($normalized === '' || Str::contains($normalized, '..')) {
            return;
        }

        $candidates = [];

        if (Str::startsWith($normalized, 'scholarship_children/')) {
            $candidates[] = storage_path('app/private/' . $normalized);
        }

        // รองรับข้อมูลเดิมก่อนย้ายไฟล์รูปออกจาก public
        if (Str::startsWith($normalized, 'upload/scholarship_children/')) {
            $candidates[] = public_path($normalized);
        } elseif (!Str::contains($normalized, '/')) {
            $candidates[] = public_path('upload/scholarship_children/' . basename($normalized));
        }

        foreach ($candidates as $candidate) {
            if (File::isFile($candidate)) {
                File::delete($candidate);
            }
        }
    }

    private function scholarshipChildPhotoPath(ScholarshipChild $child): ?string
    {
        if (empty($child->photo)) {
            return null;
        }

        $relative = ltrim(str_replace('\\', '/', trim((string) $child->photo)), '/');
        if ($relative === '' || Str::contains($relative, '..')) {
            return null;
        }

        $candidates = [];

        if (Str::startsWith($relative, 'scholarship_children/')) {
            $candidates[] = storage_path('app/private/' . $relative);
        }

        if (Str::startsWith($relative, 'upload/scholarship_children/')) {
            $candidates[] = public_path($relative);
        } elseif (!Str::contains($relative, '/')) {
            $candidates[] = public_path('upload/scholarship_children/' . basename($relative));
        }

        return collect($candidates)->first(static fn ($candidate) => File::isFile($candidate));
    }

    public function viewPhoto(ScholarshipChild $child)
    {
        $path = $this->scholarshipChildPhotoPath($child);

        if (!$path) {
            $fallback = public_path('upload/no_image.jpg');
            abort_unless(File::isFile($fallback), 404);
            $path = $fallback;
        }

        $mime = File::mimeType($path) ?: 'image/jpeg';
        abort_unless(in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true), 404);

        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    protected function savePdfFiles(
        ScholarshipExpense $expense,
        array $files,
        string $category,
        array &$storedPaths
    ): void {
        if (empty($files)) {
            return;
        }

        $relativeDirectory = 'scholarship_expenses/'
            . $expense->scholarship_child_id
            . '/'
            . $expense->id;

        $destinationPath = storage_path('app/private/' . $relativeDirectory);
        File::ensureDirectoryExists($destinationPath);

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }

            if (!$this->hasPdfSignature($file->getRealPath())) {
                throw new \RuntimeException('พบไฟล์แนบที่ไม่ใช่ PDF ที่ถูกต้อง');
            }

            $originalName = Str::limit(
                basename((string) $file->getClientOriginalName()),
                255,
                ''
            );
            $fileSize = $file->getSize();
            $filename = Str::uuid()->toString() . '.pdf';
            $relativePath = $relativeDirectory . '/' . $filename;

            $file->move($destinationPath, $filename);
            $storedPaths[] = storage_path('app/private/' . $relativePath);

            $expense->attachments()->create([
                'category'      => $category,
                'file_path'     => $relativePath,
                'original_name' => $originalName,
                'mime_type'     => 'application/pdf',
                'file_size'     => $fileSize,
                'uploaded_by'   => auth()->id(),
            ]);
        }
    }

    private function hasPdfSignature(?string $path): bool
    {
        if (!$path || !File::isFile($path)) {
            return false;
        }

        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return false;
        }

        try {
            return fread($handle, 5) === '%PDF-';
        } finally {
            fclose($handle);
        }
    }

    private function attachmentAbsolutePath(ScholarshipExpenseAttachment $attachment): ?string
    {
        $relative = ltrim(str_replace('\\', '/', trim((string) $attachment->file_path)), '/');

        if ($relative === '' || Str::contains($relative, '..')) {
            return null;
        }

        if (Str::startsWith($relative, 'scholarship_expenses/')) {
            $path = storage_path('app/private/' . $relative);
            return File::isFile($path) ? $path : null;
        }

        // รองรับข้อมูลเดิมก่อนย้ายไฟล์ออกจาก public
        if (Str::startsWith($relative, 'upload/scholarship_expenses/')) {
            $path = public_path($relative);
            return File::isFile($path) ? $path : null;
        }

        return null;
    }

    /**
     * เปิดเอกสารค่าใช้จ่าย/ผลการเรียนผ่าน route ที่มี auth + form permission
     */
    public function viewAttachment(
        ScholarshipChild $child,
        ScholarshipExpense $expense,
        ScholarshipExpenseAttachment $attachment
    ) {
        abort_unless((int) $expense->scholarship_child_id === (int) $child->id, 404);
        abort_unless((int) $attachment->scholarship_expense_id === (int) $expense->id, 404);

        $path = $this->attachmentAbsolutePath($attachment);
        abort_unless($path && $this->hasPdfSignature($path), 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="scholarship-document-' . $attachment->id . '.pdf"',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function applicationRules(bool $includeProfile = true): array
    {
        $rules = [
            'age'             => ['nullable', 'integer', 'min:1', 'max:120'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'school_name'     => ['nullable', 'string', 'max:255'],
            'academic_year'   => ['required', 'regex:/^[0-9]{4}$/'],
            'semester'        => ['required', Rule::in(['1', '2', 1, 2])],
            'reason'          => ['nullable', 'string'],
            'help_needed'     => ['nullable', 'string'],
            'more_detail'     => ['nullable', 'string'],
        ];

        if ($includeProfile) {
            $rules = array_merge([
                'first_name'      => ['required', 'string', 'max:255'],
                'last_name'       => ['required', 'string', 'max:255'],
                'gender'          => ['nullable', Rule::in(['male', 'female'])],
                'current_address' => ['nullable', 'string'],
                'guardian_name'   => ['nullable', 'string', 'max:255'],
                'phone'           => ['nullable', 'string', 'max:30'],
                'photo'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            ], $rules);
        }

        return $rules;
    }

    private function applicationMessages(): array
    {
        return [
            'first_name.required'    => 'กรุณากรอกชื่อผู้ขอรับทุน',
            'last_name.required'     => 'กรุณากรอกนามสกุลผู้ขอรับทุน',
            'gender.in'              => 'กรุณาเลือกเพศให้ถูกต้อง',
            'academic_year.required' => 'กรุณากรอกปีการศึกษาที่ขอรับทุน',
            'academic_year.regex'    => 'ปีการศึกษาต้องเป็นตัวเลข พ.ศ. 4 หลัก เช่น 2569',
            'semester.required'      => 'กรุณาเลือกภาคเรียนที่ยื่นขอทุน',
            'semester.in'            => 'ภาคเรียนต้องเป็น 1 หรือ 2 เท่านั้น',
            'photo.image'            => 'ไฟล์ภาพถ่ายต้องเป็นรูปภาพเท่านั้น',
            'photo.max'              => 'ไฟล์ภาพถ่ายต้องมีขนาดไม่เกิน 10 MB',
        ];
    }

    private function applyChildFilters(
        $query,
        ?string $keyword,
        ?string $academicYear,
        ?int $semester,
        ?string $status,
        bool $includeStatus = true
    ) {
        return $query
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('first_name', 'like', '%' . $keyword . '%')
                        ->orWhere('last_name', 'like', '%' . $keyword . '%')
                        ->orWhere('school_name', 'like', '%' . $keyword . '%')
                        ->orWhere('guardian_name', 'like', '%' . $keyword . '%')
                        ->orWhere('phone', 'like', '%' . $keyword . '%');
                });
            })
            ->when($academicYear, fn ($query) => $query->where('academic_year', $academicYear))
            ->when($semester, fn ($query) => $query->where('semester', $semester))
            ->when(
                $includeStatus && $status,
                fn ($query) => $query->where('scholarship_status', $status)
            );
    }

    private function ensurePersonUuid(ScholarshipChild $child): string
    {
        if ($child->person_uuid) {
            return $child->person_uuid;
        }

        $personUuid = Str::uuid()->toString();
        $child->forceFill(['person_uuid' => $personUuid])->save();

        return $personUuid;
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'academic_year' => ['nullable', 'regex:/^[0-9]{4}$/'],
            'semester' => ['nullable', Rule::in(['1', '2', 1, 2])],
            'keyword' => ['nullable', 'string', 'max:100'],
            'scholarship_status' => [
                'nullable',
                Rule::in([
                    ScholarshipChild::STATUS_PENDING,
                    ScholarshipChild::STATUS_APPROVED,
                    ScholarshipChild::STATUS_REJECTED,
                ]),
            ],
        ], [
            'academic_year.regex' => 'ปีการศึกษาต้องเป็นตัวเลข พ.ศ. 4 หลัก',
            'semester.in' => 'ภาคเรียนต้องเป็น 1 หรือ 2 เท่านั้น',
            'keyword.max' => 'คำค้นหาต้องไม่เกิน 100 ตัวอักษร',
            'scholarship_status.in' => 'สถานะการพิจารณาทุนไม่ถูกต้อง',
        ]);

        $academicYear = !empty($filters['academic_year'])
            ? trim((string) $filters['academic_year'])
            : null;
        $semester = isset($filters['semester']) && $filters['semester'] !== ''
            ? (int) $filters['semester']
            : null;
        $keyword = !empty($filters['keyword'])
            ? Str::squish((string) $filters['keyword'])
            : null;
        $status = !empty($filters['scholarship_status'])
            ? (string) $filters['scholarship_status']
            : null;

        $years = ScholarshipChild::query()
            ->whereNotNull('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year');

        $childrenQuery = ScholarshipChild::query()
            ->with([
                'expenses' => function ($query) {
                    $query->with(['items', 'attachments'])
                        ->orderByDesc('record_date')
                        ->orderByDesc('id');
                },
                'personApplications' => function ($query) {
                    $query->with([
                        'expenses' => function ($expenseQuery) {
                            $expenseQuery->with(['items', 'attachments'])
                                ->orderByDesc('record_date')
                                ->orderByDesc('id');
                        },
                    ])
                        ->orderByDesc('academic_year')
                        ->orderByDesc('semester')
                        ->orderByDesc('id');
                },
            ]);

        $children = $this->applyChildFilters(
            $childrenQuery,
            $keyword,
            $academicYear,
            $semester,
            $status
        )
            ->orderByDesc('academic_year')
            ->orderByDesc('semester')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $summaryQuery = $this->applyChildFilters(
            ScholarshipChild::query(),
            $keyword,
            $academicYear,
            $semester,
            $status,
            false
        );

        $statusSummary = (clone $summaryQuery)
            ->selectRaw("\n                COUNT(*) AS total_count,\n                SUM(CASE WHEN scholarship_status = 'pending' THEN 1 ELSE 0 END) AS pending_count,\n                SUM(CASE WHEN scholarship_status = 'approved' THEN 1 ELSE 0 END) AS approved_count,\n                SUM(CASE WHEN scholarship_status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count\n            ")
            ->first();

        $distinctPersonCount = (int) (clone $summaryQuery)
            ->selectRaw("COUNT(DISTINCT COALESCE(person_uuid, CONCAT('legacy-', id))) AS person_count")
            ->value('person_count');

        $expenseBaseQuery = DB::table('scholarship_expenses as expenses')
            ->join(
                'scholarship_children as children',
                'children.id',
                '=',
                'expenses.scholarship_child_id'
            )
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('children.first_name', 'like', '%' . $keyword . '%')
                        ->orWhere('children.last_name', 'like', '%' . $keyword . '%')
                        ->orWhere('children.school_name', 'like', '%' . $keyword . '%')
                        ->orWhere('children.guardian_name', 'like', '%' . $keyword . '%')
                        ->orWhere('children.phone', 'like', '%' . $keyword . '%');
                });
            })
            ->when($academicYear, fn ($query) => $query->where('expenses.academic_year', $academicYear))
            ->when($semester, fn ($query) => $query->where('expenses.semester', $semester))
            ->when($status, fn ($query) => $query->where('children.scholarship_status', $status));

        $expenseYearSummary = (clone $expenseBaseQuery)
            ->select('expenses.academic_year')
            ->selectRaw('SUM(expenses.total_amount) AS total_amount')
            ->selectRaw('SUM(CASE WHEN expenses.semester = 1 THEN expenses.total_amount ELSE 0 END) AS semester_1_total')
            ->selectRaw('SUM(CASE WHEN expenses.semester = 2 THEN expenses.total_amount ELSE 0 END) AS semester_2_total')
            ->selectRaw('COUNT(*) AS expense_count')
            ->selectRaw("COUNT(DISTINCT COALESCE(children.person_uuid, CONCAT('legacy-', children.id))) AS recipient_count")
            ->groupBy('expenses.academic_year')
            ->orderByDesc('expenses.academic_year')
            ->get();

        $expenseGrandTotal = (float) $expenseYearSummary->sum('total_amount');
        $expenseGrandRecordCount = (int) $expenseYearSummary->sum('expense_count');

        $expenseGrandRecipientCount = (int) (clone $expenseBaseQuery)
            ->selectRaw("COUNT(DISTINCT COALESCE(children.person_uuid, CONCAT('legacy-', children.id))) AS recipient_count")
            ->value('recipient_count');

        return view('landing.scholarship.children.index', compact(
            'children',
            'years',
            'academicYear',
            'semester',
            'keyword',
            'status',
            'statusSummary',
            'distinctPersonCount',
            'expenseYearSummary',
            'expenseGrandTotal',
            'expenseGrandRecordCount',
            'expenseGrandRecipientCount'
        ));
    }

    /** เพิ่มบุคคลใหม่และสร้างคำขอครั้งแรก */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->applicationRules(true),
            $this->applicationMessages()
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_create_child_modal', true);
        }

        $validated = $validator->validated();
        $data = collect($validated)
            ->only(array_merge(self::PROFILE_FIELDS, self::APPLICATION_FIELDS, [
                'academic_year',
                'semester',
            ]))
            ->all();

        $data['person_uuid'] = Str::uuid()->toString();
        $data['semester'] = (int) $validated['semester'];
        $data['scholarship_status'] = ScholarshipChild::STATUS_PENDING;
        $data['scholarship_status_updated_at'] = now();

        $newPhotoPath = null;

        try {
            if ($request->hasFile('photo')) {
                $newPhotoPath = $this->saveChildPhoto($request->file('photo'));
                $data['photo'] = $newPhotoPath;
            }

            DB::transaction(fn () => ScholarshipChild::query()->create($data), 3);
        } catch (Throwable $exception) {
            if ($newPhotoPath) {
                $this->deleteChildPhoto($newPhotoPath);
            }

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'child_store' => 'ไม่สามารถบันทึกผู้ขอรับทุนได้ กรุณาลองใหม่อีกครั้ง',
                ])
                ->with('open_create_child_modal', true);
        }

        return redirect()
            ->route('scholarship.children.index', [
                'academic_year' => $data['academic_year'],
                'semester' => $data['semester'],
            ])
            ->with(
                'success',
                'บันทึกผู้ขอรับทุนรายใหม่ ปีการศึกษา '
                . $data['academic_year']
                . ' ภาคเรียนที่ '
                . $data['semester']
                . ' เรียบร้อยแล้ว'
            );
    }

    /** ยื่นคำขอรอบใหม่ โดยใช้ข้อมูลประจำตัวของบุคคลเดิม */
    public function storeApplication(Request $request, ScholarshipChild $child)
    {
        $validator = Validator::make(
            $request->all(),
            array_merge(
                $this->applicationRules(false),
                ['reapply_child_id' => ['nullable', 'integer']]
            ),
            $this->applicationMessages()
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_reapply_modal', $child->id);
        }

        $validated = $validator->validated();
        $academicYear = (string) $validated['academic_year'];
        $semester = (int) $validated['semester'];
        $personUuid = $this->ensurePersonUuid($child);

        $alreadyExists = ScholarshipChild::query()
            ->where('person_uuid', $personUuid)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->exists();

        if ($alreadyExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'application_period' =>
                        'บุคคลนี้มีคำขอทุนปีการศึกษา '
                        . $academicYear
                        . ' ภาคเรียนที่ '
                        . $semester
                        . ' อยู่แล้ว ไม่สามารถบันทึกซ้ำได้',
                ])
                ->with('open_reapply_modal', $child->id);
        }

        $data = collect($child->only(array_merge(self::PROFILE_FIELDS, ['photo'])))
            ->merge(collect($validated)->only(self::APPLICATION_FIELDS))
            ->merge([
                'person_uuid' => $personUuid,
                'academic_year' => $academicYear,
                'semester' => $semester,
                'scholarship_status' => ScholarshipChild::STATUS_PENDING,
                'scholarship_status_updated_at' => now(),
            ])
            ->all();

        try {
            DB::transaction(fn () => ScholarshipChild::query()->create($data), 3);
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'application_store' => 'ไม่สามารถสร้างคำขอทุนรอบใหม่ได้ กรุณาลองใหม่อีกครั้ง',
                ])
                ->with('open_reapply_modal', $child->id);
        }

        return redirect()
            ->route('scholarship.children.index', [
                'academic_year' => $academicYear,
                'semester' => $semester,
            ])
            ->with(
                'success',
                'สร้างคำขอทุนรอบใหม่ของ '
                . $child->first_name
                . ' '
                . $child->last_name
                . ' ปีการศึกษา '
                . $academicYear
                . ' ภาคเรียนที่ '
                . $semester
                . ' โดยใช้ข้อมูลประจำตัวเดิมเรียบร้อยแล้ว'
            );
    }

    /** แก้ไขข้อมูลประจำตัวร่วมกัน และแก้ข้อมูลเฉพาะคำขอปัจจุบัน */
    public function update(Request $request, ScholarshipChild $child)
    {
        $rules = $this->applicationRules(true);
        unset($rules['academic_year'], $rules['semester']);

        $validator = Validator::make(
            $request->all(),
            $rules,
            $this->applicationMessages()
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_edit_child_modal', $child->id);
        }

        $validated = $validator->validated();
        $personUuid = $this->ensurePersonUuid($child);
        $profileData = collect($validated)->only(self::PROFILE_FIELDS)->all();
        $applicationData = collect($validated)->only(self::APPLICATION_FIELDS)->all();

        $newPhotoPath = null;
        $oldPhotoPaths = ScholarshipChild::query()
            ->where('person_uuid', $personUuid)
            ->whereNotNull('photo')
            ->pluck('photo')
            ->filter()
            ->unique()
            ->values();

        if ($request->hasFile('photo')) {
            $newPhotoPath = $this->saveChildPhoto($request->file('photo'));
            $profileData['photo'] = $newPhotoPath;
        }

        try {
            DB::transaction(function () use (
                $personUuid,
                $profileData,
                $applicationData,
                $child
            ) {
                ScholarshipChild::query()
                    ->where('person_uuid', $personUuid)
                    ->update($profileData);

                $child->update($applicationData);
            });
        } catch (Throwable $exception) {
            if ($newPhotoPath) {
                $this->deleteChildPhoto($newPhotoPath);
            }

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'child_update' => 'ไม่สามารถแก้ไขข้อมูลผู้ขอรับทุนได้ กรุณาลองใหม่อีกครั้ง',
                ])
                ->with('open_edit_child_modal', $child->id);
        }

        if ($newPhotoPath) {
            foreach ($oldPhotoPaths as $oldPhotoPath) {
                if ($oldPhotoPath !== $newPhotoPath) {
                    $this->deleteChildPhoto($oldPhotoPath);
                }
            }
        }

        return redirect()
            ->route('scholarship.children.index', [
                'academic_year' => $child->academic_year,
                'semester' => $child->semester,
            ])
            ->with(
                'success',
                'แก้ไขข้อมูลของ '
                . $child->first_name
                . ' '
                . $child->last_name
                . ' เรียบร้อยแล้ว ข้อมูลประจำตัวถูกปรับใช้กับทุกคำขอของบุคคลนี้'
            );
    }

    public function updateStatus(Request $request, ScholarshipChild $child)
    {
        $validated = $request->validate([
            'scholarship_status' => [
                'required',
                Rule::in([
                    ScholarshipChild::STATUS_PENDING,
                    ScholarshipChild::STATUS_APPROVED,
                    ScholarshipChild::STATUS_REJECTED,
                ]),
            ],
        ], [
            'scholarship_status.required' => 'กรุณาเลือกสถานะการพิจารณาทุน',
            'scholarship_status.in' => 'สถานะการพิจารณาทุนไม่ถูกต้อง',
        ]);

        $child->update([
            'scholarship_status' => $validated['scholarship_status'],
            'scholarship_status_updated_at' => now(),
        ]);

        return back()->with(
            'success',
            'ปรับสถานะของ '
            . $child->first_name
            . ' '
            . $child->last_name
            . ' ปีการศึกษา '
            . $child->academic_year
            . ' ภาคเรียนที่ '
            . $child->semester
            . ' เป็น “'
            . $child->fresh()->status_label
            . '” เรียบร้อยแล้ว'
        );
    }

    public function storeExpense(Request $request, ScholarshipChild $child)
    {
        if (!$child->isApproved()) {
            return back()
                ->withInput()
                ->withErrors([
                    'expense_items' =>
                        'บันทึกค่าใช้จ่ายได้เฉพาะคำขอทุนที่อนุมัติแล้วในปีและภาคเรียนนี้เท่านั้น',
                ])
                ->with('open_expense_modal', $child->id);
        }

        // ปีและภาคเรียนยึดจากคำขอทุนเสมอ ป้องกันบันทึกข้ามรอบ
        $request->merge([
            'semester' => (string) $child->semester,
        ]);

        $validated = $request->validate([
            'expense_child_id' => ['nullable', 'integer'],
            'record_date' => ['required', 'date', 'before_or_equal:' . now('Asia/Bangkok')->toDateString()],
            'semester' => ['required', Rule::in([(string) $child->semester, (int) $child->semester])],
            'note' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.expense_type' => ['required', 'distinct', Rule::in(self::EXPENSE_TYPES)],
            'items.*.amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'expense_documents' => ['nullable', 'array', 'max:10'],
            'expense_documents.*' => ['file', 'mimes:pdf', 'max:10240'],
            'grade_reports' => ['nullable', 'array', 'max:10'],
            'grade_reports.*' => ['file', 'mimes:pdf', 'max:10240'],
        ], $this->expenseMessages());

        $itemTypes = collect($validated['items'])
            ->pluck('expense_type')
            ->filter()
            ->unique()
            ->values();

        $existingItemTypes = DB::table('scholarship_expense_items as expense_items')
            ->join(
                'scholarship_expenses as expenses',
                'expenses.id',
                '=',
                'expense_items.scholarship_expense_id'
            )
            ->where('expenses.scholarship_child_id', $child->id)
            ->where('expenses.academic_year', $child->academic_year)
            ->where('expenses.semester', $child->semester)
            ->whereIn('expense_items.expense_type', $itemTypes->all())
            ->distinct()
            ->orderBy('expense_items.expense_type')
            ->pluck('expense_items.expense_type');

        if ($existingItemTypes->isNotEmpty()) {
            return back()
                ->withInput()
                ->withErrors([
                    'expense_items' =>
                        'ไม่สามารถบันทึกได้ เนื่องจากรายการ “'
                        . $existingItemTypes->implode(', ')
                        . '” ถูกบันทึกแล้วสำหรับปีการศึกษา '
                        . $child->academic_year
                        . ' ภาคเรียนที่ '
                        . $child->semester,
                ])
                ->with('open_expense_modal', $child->id);
        }

        $totalAmount = collect($validated['items'])
            ->sum(fn ($item) => (float) $item['amount']);

        $storedPaths = [];
        DB::beginTransaction();

        try {
            $expense = $child->expenses()->create([
                'record_date'   => $validated['record_date'],
                'academic_year' => $child->academic_year,
                'semester'      => $child->semester,
                'total_amount'  => round($totalAmount, 2),
                'note'          => $validated['note'] ?? null,
                'created_by'    => auth()->id(),
                'updated_by'    => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $expense->items()->create([
                    'expense_type' => $item['expense_type'],
                    'amount' => round((float) $item['amount'], 2),
                ]);
            }

            $this->savePdfFiles(
                $expense,
                $request->file('expense_documents', []),
                'expense_document',
                $storedPaths
            );

            $this->savePdfFiles(
                $expense,
                $request->file('grade_reports', []),
                'grade_report',
                $storedPaths
            );

            DB::commit();

            return back()->with(
                'success',
                'บันทึกค่าใช้จ่ายของ '
                . $child->first_name
                . ' '
                . $child->last_name
                . ' ปีการศึกษา '
                . $child->academic_year
                . ' ภาคเรียนที่ '
                . $child->semester
                . ' รวม '
                . number_format($totalAmount, 2)
                . ' บาท เรียบร้อยแล้ว'
            );
        } catch (Throwable $exception) {
            DB::rollBack();

            foreach ($storedPaths as $fullPath) {
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'expense' => 'ไม่สามารถบันทึกรายการค่าใช้จ่ายได้ กรุณาลองใหม่อีกครั้ง',
                ])
                ->with('open_expense_modal', $child->id);
        }
    }

    private function expenseMessages(): array
    {
        return [
            'record_date.required' => 'กรุณาระบุวันที่บันทึก',
            'record_date.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันที่ปัจจุบัน',
            'semester.required' => 'กรุณาเลือกภาคเรียน',
            'semester.in' => 'ภาคเรียนไม่ตรงกับคำขอทุนที่เลือก',
            'items.required' => 'กรุณาเพิ่มรายการค่าใช้จ่ายอย่างน้อย 1 รายการ',
            'items.min' => 'กรุณาเพิ่มรายการค่าใช้จ่ายอย่างน้อย 1 รายการ',
            'items.*.expense_type.required' => 'กรุณาเลือกรายการค่าใช้จ่าย',
            'items.*.expense_type.distinct' => 'ไม่สามารถเลือกรายการค่าใช้จ่ายซ้ำกันภายในฟอร์มเดียวได้',
            'items.*.expense_type.in' => 'รายการค่าใช้จ่ายไม่ถูกต้อง',
            'items.*.amount.required' => 'กรุณาระบุจำนวนเงิน',
            'items.*.amount.numeric' => 'จำนวนเงินต้องเป็นตัวเลข',
            'items.*.amount.min' => 'จำนวนเงินต้องมากกว่า 0 บาท',
            'expense_documents.*.mimes' => 'เอกสารรายการค่าใช้จ่ายต้องเป็นไฟล์ PDF เท่านั้น',
            'expense_documents.*.max' => 'เอกสารรายการค่าใช้จ่ายแต่ละไฟล์ต้องไม่เกิน 10 MB',
            'grade_reports.*.mimes' => 'ไฟล์ผลการเรียนต้องเป็นไฟล์ PDF เท่านั้น',
            'grade_reports.*.max' => 'ไฟล์ผลการเรียนแต่ละไฟล์ต้องไม่เกิน 10 MB',
        ];
    }

    public function updateExpense(
        Request $request,
        ScholarshipChild $child,
        ScholarshipExpense $expense
    ) {
        if ((int) $expense->scholarship_child_id !== (int) $child->id) {
            abort(404, 'ไม่พบรายการค่าใช้จ่ายของผู้รับทุนรายนี้');
        }

        if (!$child->isApproved()) {
            return back()
                ->withErrors([
                    'edit_expense_items' =>
                        'แก้ไขค่าใช้จ่ายได้เฉพาะคำขอทุนที่มีสถานะได้รับทุนเท่านั้น',
                ])
                ->with('open_edit_expense_modal', $expense->id);
        }

        $request->merge([
            'semester' => (string) $child->semester,
        ]);

        $validator = Validator::make(
            $request->all(),
            [
                'edit_expense_id' => ['nullable', 'integer'],
                'record_date' => ['required', 'date', 'before_or_equal:' . now('Asia/Bangkok')->toDateString()],
                'semester' => ['required', Rule::in([(string) $child->semester, (int) $child->semester])],
                'note' => ['nullable', 'string', 'max:2000'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.expense_type' => ['required', 'distinct', Rule::in(self::EXPENSE_TYPES)],
                'items.*.amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
                'expense_documents' => ['nullable', 'array', 'max:10'],
                'expense_documents.*' => ['file', 'mimes:pdf', 'max:10240'],
                'grade_reports' => ['nullable', 'array', 'max:10'],
                'grade_reports.*' => ['file', 'mimes:pdf', 'max:10240'],
                'remove_attachment_ids' => ['nullable', 'array'],
                'remove_attachment_ids.*' => ['integer', 'distinct'],
            ],
            $this->expenseMessages()
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_edit_expense_modal', $expense->id);
        }

        $validated = $validator->validated();
        $itemTypes = collect($validated['items'])
            ->pluck('expense_type')
            ->filter()
            ->unique()
            ->values();

        $existingItemTypes = DB::table('scholarship_expense_items as expense_items')
            ->join(
                'scholarship_expenses as expenses',
                'expenses.id',
                '=',
                'expense_items.scholarship_expense_id'
            )
            ->where('expenses.scholarship_child_id', $child->id)
            ->where('expenses.academic_year', $child->academic_year)
            ->where('expenses.semester', $child->semester)
            ->where('expenses.id', '<>', $expense->id)
            ->whereIn('expense_items.expense_type', $itemTypes->all())
            ->distinct()
            ->orderBy('expense_items.expense_type')
            ->pluck('expense_items.expense_type');

        if ($existingItemTypes->isNotEmpty()) {
            return back()
                ->withInput()
                ->withErrors([
                    'edit_expense_items' =>
                        'ไม่สามารถแก้ไขได้ เนื่องจากรายการ “'
                        . $existingItemTypes->implode(', ')
                        . '” ถูกบันทึกแล้วสำหรับปีการศึกษา '
                        . $child->academic_year
                        . ' ภาคเรียนที่ '
                        . $child->semester,
                ])
                ->with('open_edit_expense_modal', $expense->id);
        }

        $totalAmount = collect($validated['items'])
            ->sum(fn ($item) => (float) $item['amount']);

        $removeAttachmentIds = collect($validated['remove_attachment_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $attachmentsToRemove = $expense->attachments()
            ->whereIn('id', $removeAttachmentIds->all())
            ->get();

        $oldFilePathsToDelete = $attachmentsToRemove
            ->map(fn ($attachment) => $this->attachmentAbsolutePath($attachment))
            ->filter()
            ->values();

        $newStoredPaths = [];
        DB::beginTransaction();

        try {
            $expense->update([
                'record_date' => $validated['record_date'],
                'academic_year' => $child->academic_year,
                'semester' => $child->semester,
                'total_amount' => round($totalAmount, 2),
                'note' => $validated['note'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            $expense->items()->delete();

            foreach ($validated['items'] as $item) {
                $expense->items()->create([
                    'expense_type' => $item['expense_type'],
                    'amount' => round((float) $item['amount'], 2),
                ]);
            }

            if ($removeAttachmentIds->isNotEmpty()) {
                $expense->attachments()
                    ->whereIn('id', $removeAttachmentIds->all())
                    ->delete();
            }

            $this->savePdfFiles(
                $expense,
                $request->file('expense_documents', []),
                'expense_document',
                $newStoredPaths
            );

            $this->savePdfFiles(
                $expense,
                $request->file('grade_reports', []),
                'grade_report',
                $newStoredPaths
            );

            DB::commit();

            foreach ($oldFilePathsToDelete as $fullPath) {
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }

            return back()->with(
                'success',
                'แก้ไขค่าใช้จ่ายของ '
                . $child->first_name
                . ' '
                . $child->last_name
                . ' ปีการศึกษา '
                . $child->academic_year
                . ' ภาคเรียนที่ '
                . $child->semester
                . ' ยอดรวม '
                . number_format($totalAmount, 2)
                . ' บาท เรียบร้อยแล้ว'
            );
        } catch (Throwable $exception) {
            DB::rollBack();

            foreach ($newStoredPaths as $fullPath) {
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'edit_expense' => 'ไม่สามารถแก้ไขรายการค่าใช้จ่ายได้ กรุณาลองใหม่อีกครั้ง',
                ])
                ->with('open_edit_expense_modal', $expense->id);
        }
    }

    public function destroyExpense(
        ScholarshipChild $child,
        ScholarshipExpense $expense
    ) {
        if ((int) $expense->scholarship_child_id !== (int) $child->id) {
            abort(404, 'ไม่พบรายการค่าใช้จ่ายของผู้รับทุนรายนี้');
        }

        $expenseDirectories = [
            storage_path('app/private/scholarship_expenses/' . $child->id . '/' . $expense->id),
            public_path('upload/scholarship_expenses/' . $child->id . '/' . $expense->id),
        ];

        try {
            DB::transaction(fn () => $expense->delete());

            foreach ($expenseDirectories as $expenseDirectory) {
                if (File::isDirectory($expenseDirectory)) {
                    File::deleteDirectory($expenseDirectory);
                }
            }

            return back()->with(
                'success',
                'ลบรายการค่าใช้จ่ายของ '
                . $child->first_name
                . ' '
                . $child->last_name
                . ' เรียบร้อยแล้ว'
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'delete_expense' => 'ไม่สามารถลบรายการค่าใช้จ่ายได้ กรุณาลองใหม่อีกครั้ง',
            ]);
        }
    }

    /** ลบเฉพาะคำขอในปี/ภาคเรียนนี้ ไม่ลบประวัติรอบอื่นของบุคคลเดียวกัน */
    public function destroy(ScholarshipChild $child)
    {
        $personUuid = $child->person_uuid;
        $hasOtherApplications = $personUuid
            ? ScholarshipChild::query()
                ->where('person_uuid', $personUuid)
                ->where('id', '<>', $child->id)
                ->exists()
            : false;

        $photoPath = $child->photo;
        $expenseDirectories = [
            storage_path('app/private/scholarship_expenses/' . $child->id),
            public_path('upload/scholarship_expenses/' . $child->id),
        ];

        try {
            DB::transaction(fn () => $child->delete());

            foreach ($expenseDirectories as $expenseDirectory) {
                if (File::isDirectory($expenseDirectory)) {
                    File::deleteDirectory($expenseDirectory);
                }
            }

            if (!$hasOtherApplications) {
                $this->deleteChildPhoto($photoPath);
            }

            return redirect()
                ->route('scholarship.children.index')
                ->with(
                    'success',
                    'ลบคำขอทุนปีการศึกษา '
                    . $child->academic_year
                    . ' ภาคเรียนที่ '
                    . $child->semester
                    . ' เรียบร้อยแล้ว โดยไม่กระทบประวัติรอบอื่น'
                );
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'delete_child' => 'ไม่สามารถลบคำขอทุนได้ กรุณาลองใหม่อีกครั้ง',
            ]);
        }
    }

    public function report(Request $request)
    {
        $filters = $request->validate([
            'academic_year' => ['nullable', 'regex:/^[0-9]{4}$/'],
            'semester' => ['nullable', Rule::in(['1', '2', 1, 2])],
            'keyword' => ['nullable', 'string', 'max:100'],
            'scholarship_status' => [
                'nullable',
                Rule::in([
                    ScholarshipChild::STATUS_PENDING,
                    ScholarshipChild::STATUS_APPROVED,
                    ScholarshipChild::STATUS_REJECTED,
                ]),
            ],
        ]);

        $academicYear = !empty($filters['academic_year'])
            ? (string) $filters['academic_year']
            : null;
        $semester = isset($filters['semester']) && $filters['semester'] !== ''
            ? (int) $filters['semester']
            : null;
        $keyword = !empty($filters['keyword'])
            ? Str::squish((string) $filters['keyword'])
            : null;
        $status = !empty($filters['scholarship_status'])
            ? (string) $filters['scholarship_status']
            : null;

        $years = ScholarshipChild::query()
            ->whereNotNull('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year');

        /* โหลดค่าใช้จ่ายล่วงหน้า ป้องกัน N+1 Query ในหน้ารายงาน */
        $children = $this->applyChildFilters(
            ScholarshipChild::query()->with([
                'expenses' => function ($query) {
                    $query->with(['items', 'attachments'])
                        ->orderByDesc('record_date')
                        ->orderByDesc('id');
                },
            ]),
            $keyword,
            $academicYear,
            $semester,
            $status
        )
            ->orderByDesc('academic_year')
            ->orderByDesc('semester')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('landing.scholarship.children.report', compact(
            'children',
            'years',
            'academicYear',
            'semester',
            'keyword',
            'status'
        ));
    }

    public function publicReport()
    {
        $latestApplication = ScholarshipChild::query()
            ->where('scholarship_status', ScholarshipChild::STATUS_PENDING)
            ->whereNotNull('academic_year')
            ->orderByDesc('academic_year')
            ->orderByDesc('semester')
            ->first(['academic_year', 'semester']);

        $latestYear = $latestApplication?->academic_year;
        $latestSemester = $latestApplication?->semester;

        $children = ScholarshipChild::query()
            ->where('scholarship_status', ScholarshipChild::STATUS_PENDING)
            ->when($latestYear, fn ($query) => $query->where('academic_year', $latestYear))
            ->when($latestSemester, fn ($query) => $query->where('semester', $latestSemester))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('landing.scholarship.children.public_report', [
            'children' => $children,
            'latestYear' => $latestYear,
            'latestSemester' => $latestSemester,
        ]);
    }
}