<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\EducationRecord;
use App\Models\SnapIvScreening;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SnapIvScreeningController extends Controller
{
    private const MODULE = 'snap_iv';

    public function index(Client $client): View
    {
        $client = $this->accessibleClient($client);

        $screenings = SnapIvScreening::query()
            ->with('creator')
            ->where('client_id', $client->id)
            ->orderByDesc('screening_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('frontend.snap_iv_screenings.index', compact('client', 'screenings'));
    }

    public function create(Client $client): View
    {
        $client = $this->accessibleClient($client);
        $questions = $this->questions();
        $latestEducationRecord = $this->latestEducationRecord($client->id);

        return view(
            'frontend.snap_iv_screenings.create',
            compact('client', 'questions', 'latestEducationRecord')
        );
    }

    public function store(Request $request, Client $client): RedirectResponse
    {
        $client = $this->accessibleClient($client);
        $this->normalizeRequest($request);

        $questions = $this->questions();
        $validated = $request->validate(
            $this->validationRules($client, $questions),
            $this->validationMessages($questions)
        );

        $screening = DB::transaction(function () use ($validated, $client, $questions) {
            Client::query()->whereKey($client->id)->lockForUpdate()->firstOrFail();

            $this->ensureScreeningDateIsAvailable(
                $client->id,
                $validated['screening_date']
            );

            $answers = $this->normalizeAnswers($validated['answers'], $questions);
            $result = $this->calculateResult($answers);
            $latestEducationRecord = $this->latestEducationRecord($client->id);

            $screening = SnapIvScreening::create(array_merge(
                $this->screeningPayload(
                    $validated,
                    $client,
                    $latestEducationRecord
                ),
                $result
            ));

            $this->replaceItems($screening, $questions, $answers);
            $this->syncLatestCaseActivity($client->id);

            return $screening;
        });

        return redirect()
            ->route('snap-iv.index', $client->id)
            ->with('success', 'บันทึกแบบประเมิน SNAP-IV เรียบร้อยแล้ว');
    }

    public function edit(SnapIvScreening $screening): View
    {
        $client = $this->accessibleClientByScreening($screening);

        $screening->load([
            'items' => fn ($query) => $query->orderBy('item_no'),
        ]);

        $questions = $this->questions();
        $answers = $screening->items
            ->groupBy('category')
            ->map(fn ($items) => $items->pluck('score', 'item_no')->all())
            ->all();

        $latestEducationRecord = $this->latestEducationRecord($client->id);

        return view(
            'frontend.snap_iv_screenings.edit',
            compact(
                'screening',
                'client',
                'questions',
                'answers',
                'latestEducationRecord'
            )
        );
    }

    public function update(Request $request, SnapIvScreening $screening): RedirectResponse
    {
        $client = $this->accessibleClientByScreening($screening);
        $this->normalizeRequest($request);

        $questions = $this->questions();
        $validated = $request->validate(
            $this->validationRules($client, $questions, $screening),
            $this->validationMessages($questions)
        );

        DB::transaction(function () use ($validated, $client, $questions, $screening) {
            Client::query()->whereKey($client->id)->lockForUpdate()->firstOrFail();

            $this->ensureScreeningDateIsAvailable(
                $client->id,
                $validated['screening_date'],
                $screening->id
            );

            $answers = $this->normalizeAnswers($validated['answers'], $questions);
            $result = $this->calculateResult($answers);
            $latestEducationRecord = $this->latestEducationRecord($client->id);

            $payload = $this->screeningPayload(
                $validated,
                $client,
                $latestEducationRecord
            );

            // คงผู้สร้างรายการเดิมไว้เมื่อแก้ไข
            unset($payload['created_by']);

            $screening->update(array_merge($payload, $result));

            $this->replaceItems($screening, $questions, $answers);
            $this->syncLatestCaseActivity($client->id);
        });

        return redirect()
            ->route('snap-iv.index', $client->id)
            ->with('success', 'แก้ไขแบบประเมิน SNAP-IV เรียบร้อยแล้ว');
    }

    public function show(SnapIvScreening $screening): View
    {
        $client = $this->accessibleClientByScreening($screening);

        $screening->load([
            'creator',
            'items' => fn ($query) => $query->orderBy('item_no'),
        ]);

        $latestEducationRecord = $this->latestEducationRecord($client->id);

        return view(
            'frontend.snap_iv_screenings.show',
            compact('screening', 'client', 'latestEducationRecord')
        );
    }

    public function officialReport(SnapIvScreening $screening): View
    {
        $client = $this->accessibleClientByScreening($screening);

        $screening->load([
            'creator',
            'items' => fn ($query) => $query->orderBy('item_no'),
        ]);

        $latestEducationRecord = $this->latestEducationRecord($client->id);

        return view(
            'frontend.snap_iv_screenings.official_report',
            compact('screening', 'client', 'latestEducationRecord')
        );
    }

    public function destroy(SnapIvScreening $screening): RedirectResponse
    {
        $client = $this->accessibleClientByScreening($screening);

        DB::transaction(function () use ($screening, $client) {
            // รองรับฐานข้อมูลเดิมที่อาจยังไม่ได้กำหนด cascade delete
            $screening->items()->delete();
            $screening->delete();

            $this->syncLatestCaseActivity($client->id);
        });

        return redirect()
            ->route('snap-iv.index', $client->id)
            ->with('success', 'ลบแบบประเมิน SNAP-IV เรียบร้อยแล้ว');
    }

    private function accessibleClient(Client $client): Client
    {
        return Client::forUser(auth()->user())->findOrFail($client->id);
    }

    private function accessibleClientByScreening(SnapIvScreening $screening): Client
    {
        return Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);
    }

    /**
     * ดึงผลการเรียนล่าสุดตามลำดับปีการศึกษาและภาคเรียน
     * ไม่ใช้ record_date เป็นตัวตัดสิน เพราะวันที่บันทึกอาจไม่ตรงลำดับภาคเรียน
     * ตัวอย่าง: 2/2569 ต้องมาก่อน 1/2569 แม้วันที่บันทึกของภาคเรียนที่ 1 จะใหม่กว่า
     */
    private function latestEducationRecord(int $clientId): ?EducationRecord
    {
        return EducationRecord::query()
            ->with(['education', 'semester'])
            ->leftJoin(
                'semesters',
                'education_records.semester_id',
                '=',
                'semesters.id'
            )
            ->where('education_records.client_id', $clientId)
            // รูปแบบชื่อภาคเรียนของระบบ เช่น 1/2569 และ 2/2569
            // เรียงปีการศึกษาก่อน แล้วจึงเรียงเลขภาคเรียน
            ->orderByRaw(
                "CAST(SUBSTRING_INDEX(semesters.semester_name, '/', -1) AS UNSIGNED) DESC"
            )
            ->orderByRaw(
                "CAST(SUBSTRING_INDEX(semesters.semester_name, '/', 1) AS UNSIGNED) DESC"
            )
            // ใช้ id เฉพาะกรณีปีและภาคเรียนเท่ากัน
            ->orderByDesc('education_records.id')
            // ดึงชื่อภาคเรียนเป็นคอลัมน์ตรงด้วย เพื่อไม่พึ่ง relation เพียงอย่างเดียว
            ->select([
                'education_records.*',
                'semesters.semester_name as semester_label',
            ])
            ->first();
    }

    private function normalizeRequest(Request $request): void
    {
        $request->merge([
            'observer_name' => $this->nullableTrim($request->input('observer_name')),
            'relationship' => $this->nullableTrim($request->input('relationship')),
            'term' => $this->nullableTrim($request->input('term')),
            'grade_average' => $request->input('grade_average') === ''
                ? null
                : $request->input('grade_average'),
            'remark' => $this->nullableTrim($request->input('remark')),
        ]);
    }

    private function nullableTrim(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function validationRules(
        Client $client,
        array $questions,
        ?SnapIvScreening $screening = null
    ): array {
        $dateRules = [
            'required',
            'date',
            'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
        ];

        if ($client->birth_date) {
            $dateRules[] = 'after_or_equal:' . Carbon::parse($client->birth_date)->toDateString();
        }

        $uniqueDateRule = Rule::unique('snap_iv_screenings', 'screening_date')
            ->where(fn ($query) => $query->where('client_id', $client->id));

        if ($screening) {
            $uniqueDateRule->ignore($screening->id);
        }

        $dateRules[] = $uniqueDateRule;

        $rules = [
            'screening_date' => $dateRules,
            'observer_name' => ['nullable', 'string', 'max:255'],
            'relationship' => ['nullable', 'string', 'max:255'],
            'term' => ['nullable', 'string', 'max:100'],
            'grade_average' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'answers' => ['required', 'array', 'size:' . count($questions)],
            'remark' => ['nullable', 'string', 'max:5000'],
        ];

        foreach ($questions as $category => $items) {
            $rules["answers.{$category}"] = [
                'required',
                'array',
                'size:' . count($items),
            ];

            foreach (array_keys($items) as $itemNo) {
                $rules["answers.{$category}.{$itemNo}"] = [
                    'required',
                    'integer',
                    Rule::in([0, 1, 2, 3]),
                ];
            }
        }

        return $rules;
    }

    private function validationMessages(array $questions): array
    {
        $messages = [
            'screening_date.required' => 'กรุณาเลือกวันที่ประเมิน',
            'screening_date.date' => 'รูปแบบวันที่ประเมินไม่ถูกต้อง',
            'screening_date.before_or_equal' => 'วันที่ประเมินต้องไม่เกินวันปัจจุบัน',
            'screening_date.after_or_equal' => 'วันที่ประเมินต้องไม่ก่อนวันเกิดของผู้รับบริการ',
            'screening_date.unique' => 'ผู้รับบริการรายนี้มีแบบประเมิน SNAP-IV ในวันที่ดังกล่าวแล้ว',
            'observer_name.max' => 'ชื่อผู้ประเมินต้องไม่เกิน 255 ตัวอักษร',
            'relationship.max' => 'ความสัมพันธ์กับเด็กต้องไม่เกิน 255 ตัวอักษร',
            'term.max' => 'ข้อมูลภาคเรียนต้องไม่เกิน 100 ตัวอักษร',
            'grade_average.numeric' => 'ผลการเรียนเฉลี่ยต้องเป็นตัวเลข',
            'grade_average.min' => 'ผลการเรียนเฉลี่ยต้องไม่น้อยกว่า 0',
            'grade_average.max' => 'ผลการเรียนเฉลี่ยต้องไม่เกิน 4.00',
            'answers.required' => 'กรุณาตอบแบบประเมินให้ครบทุกข้อ',
            'answers.array' => 'ข้อมูลคำตอบไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง',
            'answers.size' => 'กรุณาตอบแบบประเมินให้ครบทุกด้าน',
            'remark.max' => 'หมายเหตุต้องไม่เกิน 5,000 ตัวอักษร',
        ];

        foreach ($questions as $category => $items) {
            $messages["answers.{$category}.required"] = 'กรุณาตอบแบบประเมินให้ครบทุกข้อ';
            $messages["answers.{$category}.size"] = 'กรุณาตอบแบบประเมินให้ครบทุกข้อ';

            foreach (array_keys($items) as $itemNo) {
                $messages["answers.{$category}.{$itemNo}.required"] = "กรุณาตอบข้อ {$itemNo}";
                $messages["answers.{$category}.{$itemNo}.integer"] = "คะแนนข้อ {$itemNo} ไม่ถูกต้อง";
                $messages["answers.{$category}.{$itemNo}.in"] = "คะแนนข้อ {$itemNo} ต้องอยู่ระหว่าง 0–3";
            }
        }

        return $messages;
    }

    private function ensureScreeningDateIsAvailable(
        int $clientId,
        string $screeningDate,
        ?int $ignoreId = null
    ): void {
        $exists = SnapIvScreening::query()
            ->where('client_id', $clientId)
            ->whereDate('screening_date', $screeningDate)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'screening_date' => 'ผู้รับบริการรายนี้มีแบบประเมิน SNAP-IV ในวันที่ดังกล่าวแล้ว',
            ]);
        }
    }

    private function normalizeAnswers(array $answers, array $questions): array
    {
        $normalized = [];

        foreach ($questions as $category => $items) {
            foreach (array_keys($items) as $itemNo) {
                $normalized[$category][$itemNo] = (int) $answers[$category][$itemNo];
            }
        }

        return $normalized;
    }

    private function screeningPayload(
        array $validated,
        Client $client,
        ?EducationRecord $educationRecord
    ): array {
        return [
            'client_id' => $client->id,
            'created_by' => auth()->id(),
            'screening_date' => $validated['screening_date'],
            'observer_name' => $validated['observer_name'] ?? null,
            'relationship' => $validated['relationship'] ?? null,
            'age_text' => $this->resolveAgeText($client, $validated['screening_date']),
            'class_level' => data_get($educationRecord, 'education.education_name'),
            'term' => $validated['term']
                ?? data_get($educationRecord, 'semester_label')
                ?? data_get($educationRecord, 'semester.semester_name'),
            'grade_average' => $this->normalizeGradeAverage(
                $validated['grade_average'] ?? data_get($educationRecord, 'grade_average')
            ),
            'remark' => $validated['remark'] ?? null,
        ];
    }

    private function resolveAgeText(Client $client, string $screeningDate): ?string
    {
        if (! $client->birth_date) {
            return null;
        }

        $birthDate = Carbon::parse($client->birth_date)->startOfDay();
        $date = Carbon::parse($screeningDate)->startOfDay();
        $difference = $birthDate->diff($date);

        if ($difference->y > 0 && $difference->m > 0) {
            return $difference->y . ' ปี ' . $difference->m . ' เดือน';
        }

        if ($difference->y > 0) {
            return $difference->y . ' ปี';
        }

        return $difference->m . ' เดือน';
    }

    private function normalizeGradeAverage(mixed $gradeAverage): ?string
    {
        if ($gradeAverage === null || $gradeAverage === '') {
            return null;
        }

        return number_format((float) $gradeAverage, 2, '.', '');
    }

    private function calculateResult(array $answers): array
    {
        $inattentionScore = $this->calculateScore($answers['inattention']);
        $hyperactivityScore = $this->calculateScore($answers['hyperactivity']);
        $oppositionalScore = $this->calculateScore($answers['oppositional']);

        $inattentionLevel = $this->interpretMainSymptom($inattentionScore);
        $hyperactivityLevel = $this->interpretMainSymptom($hyperactivityScore);
        $oppositionalLevel = $this->interpretOppositional($oppositionalScore);

        return [
            'inattention_score' => $inattentionScore,
            'hyperactivity_score' => $hyperactivityScore,
            'oppositional_score' => $oppositionalScore,
            'total_score' => $inattentionScore + $hyperactivityScore + $oppositionalScore,
            'inattention_level' => $inattentionLevel,
            'hyperactivity_level' => $hyperactivityLevel,
            'oppositional_level' => $oppositionalLevel,
            'summary' => $this->buildSummary(
                $inattentionLevel,
                $hyperactivityLevel,
                $oppositionalLevel
            ),
            'recommendation' => $this->buildRecommendation(
                $inattentionLevel,
                $hyperactivityLevel,
                $oppositionalLevel
            ),
        ];
    }

    private function replaceItems(
        SnapIvScreening $screening,
        array $questions,
        array $answers
    ): void {
        $relation = $screening->items();
        $itemModel = $relation->getRelated();
        $foreignKey = $relation->getForeignKeyName();
        $timestamp = now('Asia/Bangkok');

        $relation->delete();

        $rows = [];

        foreach ($questions as $category => $items) {
            foreach ($items as $itemNo => $question) {
                $row = [
                    $foreignKey => $screening->id,
                    'category' => $category,
                    'item_no' => $itemNo,
                    'question' => $question,
                    'score' => $answers[$category][$itemNo],
                ];

                if ($itemModel->usesTimestamps()) {
                    $row[$itemModel->getCreatedAtColumn()] = $timestamp;
                    $row[$itemModel->getUpdatedAtColumn()] = $timestamp;
                }

                $rows[] = $row;
            }
        }

        DB::table($itemModel->getTable())->insert($rows);
    }

    private function syncLatestCaseActivity(int $clientId): void
    {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', self::MODULE)
            ->delete();

        $screening = SnapIvScreening::query()
            ->where('client_id', $clientId)
            ->orderByDesc('screening_date')
            ->orderByDesc('id')
            ->first();

        if (! $screening) {
            return;
        }

        $hasConcern = $screening->inattention_level !== 'ปกติ'
            || $screening->hyperactivity_level !== 'ปกติ'
            || $screening->oppositional_level !== 'ปกติ';

        CaseActivity::record([
            'client_id' => $clientId,
            'module' => self::MODULE,
            'type' => $hasConcern ? 'warning' : 'success',
            'title' => 'บันทึกแบบประเมินพฤติกรรม SNAP-IV',
            'description' => 'วันที่ประเมิน: ' . $screening->screening_date
                . ' | ขาดสมาธิ: ' . $screening->inattention_score
                . ' คะแนน (' . $screening->inattention_level . ')'
                . ' | ซน/หุนหันพลันแล่น: ' . $screening->hyperactivity_score
                . ' คะแนน (' . $screening->hyperactivity_level . ')'
                . ' | ดื้อ/ต่อต้าน: ' . $screening->oppositional_score
                . ' คะแนน (' . $screening->oppositional_level . ')',
            'occurred_at' => Carbon::parse(
                $screening->screening_date,
                'Asia/Bangkok'
            )->startOfDay(),
            'icon' => 'bi-clipboard2-pulse',
            'url' => route('snap-iv.show', $screening->id),
        ]);
    }

    private function calculateScore(array $items): int
    {
        return array_sum(array_map('intval', $items));
    }

    private function interpretMainSymptom(int $score): string
    {
        if ($score < 13) {
            return 'ปกติ';
        }

        if ($score <= 17) {
            return 'มีอาการเล็กน้อย';
        }

        if ($score <= 26) {
            return 'มีอาการปานกลาง';
        }

        return 'มีอาการรุนแรงมาก';
    }

    private function interpretOppositional(int $score): string
    {
        if ($score < 11) {
            return 'ปกติ';
        }

        if ($score <= 13) {
            return 'มีอาการเล็กน้อย';
        }

        if ($score <= 19) {
            return 'มีอาการปานกลาง';
        }

        return 'มีอาการรุนแรงมาก';
    }

    private function buildSummary(
        string $inattentionLevel,
        string $hyperactivityLevel,
        string $oppositionalLevel
    ): string {
        return "อาการขาดสมาธิ: {$inattentionLevel}\n"
            . "อาการซน อยู่ไม่นิ่ง และหุนหันพลันแล่น: {$hyperactivityLevel}\n"
            . "อาการดื้อและต่อต้าน: {$oppositionalLevel}";
    }

    private function buildRecommendation(
        string $inattentionLevel,
        string $hyperactivityLevel,
        string $oppositionalLevel
    ): string {
        $hasConcern = $inattentionLevel !== 'ปกติ'
            || $hyperactivityLevel !== 'ปกติ'
            || $oppositionalLevel !== 'ปกติ';

        if (! $hasConcern) {
            return 'ควรส่งเสริมพฤติกรรมเชิงบวก ติดตามพฤติกรรม การเรียน และการปรับตัวตามปกติอย่างต่อเนื่อง';
        }

        return 'ควรติดตามพฤติกรรมอย่างใกล้ชิด ประสานครู ผู้ดูแล และทีมสหวิชาชีพ เพื่อประเมินบริบทเพิ่มเติม ทั้งนี้ผลแบบประเมิน SNAP-IV เป็นการคัดกรองเบื้องต้น ไม่ใช่การวินิจฉัยโรค';
    }

    private function questions(): array
    {
        return [
            'inattention' => [
                1 => 'มักไม่ละเอียดรอบคอบหรือสะเพร่าในการทำงานต่าง ๆ เช่น การบ้าน',
                2 => 'ทำอะไรนาน ๆ ไม่ได้',
                3 => 'ดูเหมือนไม่ค่อยฟังเวลามีคนพูดด้วย',
                4 => 'มักทำการบ้านไม่เสร็จ หรือทำงานที่ได้รับมอบหมายไม่สำเร็จ',
                5 => 'จัดระเบียบงานและกิจกรรมต่าง ๆ ไม่เป็น',
                6 => 'มักหลีกเลี่ยงกิจกรรมที่ต้องใช้ความอดทนในการทำให้สำเร็จ',
                7 => 'ทำของหายบ่อย ๆ เช่น ของเล่น สมุดจดงาน เครื่องเขียน',
                8 => 'วอกแวกง่าย',
                9 => 'ขี้ลืม',
            ],
            'hyperactivity' => [
                10 => 'มือเท้ายุกยิก นั่งบิดไปมา',
                11 => 'นั่งไม่ติดที่ ชอบลุกจากที่นั่งในชั้นเรียน หรือจากที่ที่ควรจะนั่งเรียบร้อย',
                12 => 'วิ่งหรือปีนป่ายมากเกินควรอย่างไม่รู้กาลเทศะ',
                13 => 'เล่นหรือทำกิจกรรมเงียบ ๆ ไม่เป็น',
                14 => 'พร้อมจะเคลื่อนไหวอยู่เสมอ เหมือนติดเครื่องอยู่ตลอดเวลา',
                15 => 'พูดมาก',
                16 => 'มักโพล่งคำตอบออกมาก่อนจะฟังคำถามจบ',
                17 => 'ไม่ชอบรอคิว',
                18 => 'ชอบสอดแทรกผู้อื่น เช่น ชอบพูดแทรกขณะผู้ใหญ่กำลังสนทนากัน',
            ],
            'oppositional' => [
                19 => 'อารมณ์เสียง่าย',
                20 => 'ชอบโต้เถียงกับผู้ใหญ่',
                21 => 'ไม่ยอมทำตามสิ่งที่ผู้ใหญ่สั่งหรือวางกฎเกณฑ์ไว้',
                22 => 'จงใจก่อกวนผู้อื่น',
                23 => 'มักตำหนิผู้อื่นในสิ่งที่ตนเองทำผิด',
                24 => 'ขี้รำคาญ',
                25 => 'โกรธบึ้งตึงเป็นประจำ',
                26 => 'เจ้าคิดเจ้าแค้น',
            ],
        ];
    }
}