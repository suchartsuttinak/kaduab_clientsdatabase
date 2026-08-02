<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\DepressionScreening;
use App\Models\EducationRecord;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DepressionScreeningController extends Controller
{
    private const MODULE = 'depression_screening';
    private const RISK_SCORE = 22;

    /**
     * แสดงรายการแบบคัดกรองของผู้รับบริการ
     */
    public function index(Client $client): View
    {
        $client = $this->accessibleClient($client);

        $screenings = DepressionScreening::query()
            ->where('client_id', $client->id)
            ->orderByDesc('screening_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'frontend.depression_screenings.index',
            compact('client', 'screenings')
        );
    }

    /**
     * แสดงหน้าเพิ่มข้อมูล โดยคงรูปแบบและเส้นทางเดิม
     * /depression-screenings/client/{client}/create
     */
    public function create(Client $client): View
    {
        $client = $this->accessibleClient($client);
        $questions = $this->questions();
        $latestEducationRecord = $this->latestEducationRecord($client->id);
        $ageText = $this->resolveAgeText(
            $client,
            now('Asia/Bangkok')->toDateString()
        );
        $classLevel = data_get(
            $latestEducationRecord,
            'education.education_name'
        );

        return view(
            'frontend.depression_screenings.create',
            compact(
                'client',
                'questions',
                'latestEducationRecord',
                'ageText',
                'classLevel'
            )
        );
    }

    /**
     * บันทึกแบบคัดกรอง
     */
    public function store(Request $request, Client $client): RedirectResponse
    {
        $client = $this->accessibleClient($client);
        $this->normalizeRequest($request);

        $questions = $this->questions();
        $validated = $request->validate(
            $this->validationRules($client, $questions),
            $this->validationMessages($questions)
        );

        DB::transaction(function () use ($validated, $client, $questions): void {
            // ล็อกผู้รับบริการเพื่อป้องกันการบันทึกวันเดียวกันพร้อมกันหลายคำขอ
            Client::query()
                ->whereKey($client->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureScreeningDateIsAvailable(
                $client->id,
                $validated['screening_date']
            );

            $answers = $this->normalizeAnswers(
                $validated['answers'],
                $questions
            );

            $totalScore = $this->calculateTotalScore($answers);
            $latestEducationRecord = $this->latestEducationRecord($client->id);

            $screening = DepressionScreening::create([
                'client_id' => $client->id,
                'created_by' => auth()->id(),
                'screening_date' => $validated['screening_date'],
                'observer_name' => $validated['observer_name'] ?? null,
                // คำนวณจากข้อมูลจริงฝั่งเซิร์ฟเวอร์ ไม่เชื่อค่าจาก hidden input
                'age_text' => $this->resolveAgeText(
                    $client,
                    $validated['screening_date']
                ),
                // เรียงตามปีการศึกษาและภาคเรียน ไม่เรียงตามวันที่บันทึก
                'class_level' => data_get(
                    $latestEducationRecord,
                    'education.education_name'
                ),
                'total_score' => $totalScore,
                'result_level' => $this->buildResultLevel($totalScore),
                'summary' => $this->buildSummary($totalScore),
                'recommendation' => $this->buildRecommendation($totalScore),
                'remark' => $validated['remark'] ?? null,
            ]);

            $this->replaceItems($screening, $questions, $answers);
            $this->syncLatestCaseActivity($client->id);
        });

        return redirect()
            ->route('depression-screenings.index', $client->id)
            ->with('success', 'บันทึกแบบคัดกรองภาวะซึมเศร้าเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายละเอียด
     */
    public function show(DepressionScreening $screening): View
    {
        $client = $this->accessibleClientByScreening($screening);

        $screening->load([
            'items' => fn ($query) => $query->orderBy('item_no'),
        ]);

        return view(
            'frontend.depression_screenings.show',
            compact('screening', 'client')
        );
    }

    /**
     * ลบข้อมูลพร้อมรายการคำตอบ และปรับกิจกรรมล่าสุดให้ถูกต้อง
     */
    public function destroy(DepressionScreening $screening): RedirectResponse
    {
        $client = $this->accessibleClientByScreening($screening);

        DB::transaction(function () use ($screening, $client): void {
            // รองรับฐานข้อมูลเดิมที่อาจยังไม่ได้กำหนด cascade delete
            $screening->items()->delete();
            $screening->delete();

            $this->syncLatestCaseActivity($client->id);
        });

        return redirect()
            ->route('depression-screenings.index', $client->id)
            ->with('success', 'ลบแบบคัดกรองเรียบร้อยแล้ว');
    }

    /**
     * รายงานรูปแบบราชการ
     */
    public function officialReport(DepressionScreening $screening): View
    {
        $client = $this->accessibleClientByScreening($screening);

        $screening->load([
            'items' => fn ($query) => $query->orderBy('item_no'),
        ]);

        return view(
            'frontend.depression_screenings.official_report',
            compact('screening', 'client')
        );
    }

    private function accessibleClient(Client $client): Client
    {
        return Client::forUser(auth()->user())
            ->findOrFail($client->id);
    }

    private function accessibleClientByScreening(
        DepressionScreening $screening
    ): Client {
        return Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);
    }

    /**
     * ดึงชั้นเรียนล่าสุดตามลำดับปีการศึกษาและภาคเรียน
     * เช่น 2/2569 ต้องมาก่อน 1/2569 แม้ record_date จะเก่ากว่า
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
            ->orderByRaw(
                "CAST(SUBSTRING_INDEX(semesters.semester_name, '/', -1) AS UNSIGNED) DESC"
            )
            ->orderByRaw(
                "CAST(SUBSTRING_INDEX(semesters.semester_name, '/', 1) AS UNSIGNED) DESC"
            )
            ->orderByDesc('education_records.id')
            ->select([
                'education_records.*',
                'semesters.semester_name as semester_label',
            ])
            ->first();
    }

    private function normalizeRequest(Request $request): void
    {
        $request->merge([
            'observer_name' => $this->nullableTrim(
                $request->input('observer_name')
            ),
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

    private function validationRules(Client $client, array $questions): array
    {
        $dateRules = [
            'required',
            'date_format:Y-m-d',
            'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
        ];

        if ($client->birth_date) {
            $dateRules[] = 'after_or_equal:'
                . Carbon::parse($client->birth_date)->toDateString();
        }

        $dateRules[] = Rule::unique(
            'depression_screenings',
            'screening_date'
        )->where(
            fn ($query) => $query->where('client_id', $client->id)
        );

        $rules = [
            'screening_date' => $dateRules,
            'observer_name' => ['nullable', 'string', 'max:255'],
            'answers' => [
                'required',
                'array',
                'size:' . count($questions),
            ],
            'remark' => ['nullable', 'string', 'max:5000'],
        ];

        foreach (array_keys($questions) as $itemNo) {
            $rules["answers.{$itemNo}"] = [
                'required',
                'integer',
                Rule::in([0, 1, 2, 3]),
            ];
        }

        return $rules;
    }

    private function validationMessages(array $questions): array
    {
        $messages = [
            'screening_date.required' => 'กรุณาเลือกวันที่คัดกรอง',
            'screening_date.date_format' => 'รูปแบบวันที่คัดกรองไม่ถูกต้อง',
            'screening_date.before_or_equal' => 'วันที่คัดกรองต้องไม่เกินวันปัจจุบัน',
            'screening_date.after_or_equal' => 'วันที่คัดกรองต้องไม่ก่อนวันเกิดของผู้รับบริการ',
            'screening_date.unique' => 'ผู้รับบริการรายนี้มีการคัดกรองในวันที่ดังกล่าวแล้ว',
            'observer_name.max' => 'ชื่อผู้ประเมินต้องไม่เกิน 255 ตัวอักษร',
            'answers.required' => 'กรุณาตอบแบบคัดกรองให้ครบทั้ง 20 ข้อ',
            'answers.array' => 'ข้อมูลคำตอบไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง',
            'answers.size' => 'กรุณาตอบแบบคัดกรองให้ครบทั้ง 20 ข้อ',
            'remark.max' => 'หมายเหตุต้องไม่เกิน 5,000 ตัวอักษร',
        ];

        foreach (array_keys($questions) as $itemNo) {
            $messages["answers.{$itemNo}.required"] = "กรุณาตอบแบบคัดกรองข้อ {$itemNo}";
            $messages["answers.{$itemNo}.integer"] = "คะแนนข้อ {$itemNo} ไม่ถูกต้อง";
            $messages["answers.{$itemNo}.in"] = "คะแนนข้อ {$itemNo} ต้องอยู่ระหว่าง 0–3";
        }

        return $messages;
    }

    private function ensureScreeningDateIsAvailable(
        int $clientId,
        string $screeningDate
    ): void {
        $exists = DepressionScreening::query()
            ->where('client_id', $clientId)
            ->whereDate('screening_date', $screeningDate)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'screening_date' => 'ผู้รับบริการรายนี้มีการคัดกรองในวันที่ดังกล่าวแล้ว',
            ]);
        }
    }

    private function normalizeAnswers(
        array $answers,
        array $questions
    ): array {
        $normalized = [];

        foreach (array_keys($questions) as $itemNo) {
            $normalized[$itemNo] = (int) $answers[$itemNo];
        }

        return $normalized;
    }

    private function resolveAgeText(
        Client $client,
        string $screeningDate
    ): ?string {
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

    /**
     * บันทึกรายการคำตอบแบบ bulk insert ลดจำนวน query จาก 20 ครั้งเหลือ 1 ครั้ง
     */
    private function replaceItems(
        DepressionScreening $screening,
        array $questions,
        array $answers
    ): void {
        $relation = $screening->items();
        $itemModel = $relation->getRelated();
        $foreignKey = $relation->getForeignKeyName();
        $timestamp = now('Asia/Bangkok');
        $rows = [];

        foreach ($questions as $itemNo => $question) {
            $rawScore = $answers[$itemNo];
            $isReverse = $this->isReverseItem($itemNo);

            $row = [
                $foreignKey => $screening->id,
                'item_no' => $itemNo,
                'question' => $question,
                'score' => $isReverse ? 3 - $rawScore : $rawScore,
                'choice_text' => $this->choiceText($rawScore),
                'is_reverse' => $isReverse,
            ];

            if ($itemModel->usesTimestamps()) {
                $row[$itemModel->getCreatedAtColumn()] = $timestamp;
                $row[$itemModel->getUpdatedAtColumn()] = $timestamp;
            }

            $rows[] = $row;
        }

        DB::table($itemModel->getTable())->insert($rows);
    }

    /**
     * ให้หน้า Dashboard แสดงเฉพาะผลคัดกรองล่าสุด และไม่ค้างหลังลบข้อมูล
     */
    private function syncLatestCaseActivity(int $clientId): void
    {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', self::MODULE)
            ->delete();

        $screening = DepressionScreening::query()
            ->where('client_id', $clientId)
            ->orderByDesc('screening_date')
            ->orderByDesc('id')
            ->first();

        if (! $screening) {
            return;
        }

        $screeningDate = Carbon::parse(
            $screening->screening_date,
            'Asia/Bangkok'
        )->toDateString();

        CaseActivity::record([
            'client_id' => $clientId,
            'module' => self::MODULE,
            'type' => $screening->total_score >= self::RISK_SCORE
                ? 'warning'
                : 'success',
            'title' => 'บันทึกแบบคัดกรองภาวะซึมเศร้าในวัยรุ่น',
            'description' => 'วันที่คัดกรอง: ' . $screeningDate
                . ' | คะแนนรวม: ' . $screening->total_score
                . ' | ผล: ' . $screening->result_level,
            'occurred_at' => Carbon::parse(
                $screeningDate,
                'Asia/Bangkok'
            )->startOfDay(),
            'icon' => 'bi-emoji-frown',
            'url' => route('depression-screenings.show', $screening->id),
        ]);
    }

    private function calculateTotalScore(array $answers): int
    {
        $total = 0;

        foreach ($answers as $itemNo => $score) {
            $total += $this->isReverseItem((int) $itemNo)
                ? 3 - (int) $score
                : (int) $score;
        }

        return $total;
    }

    private function isReverseItem(int $itemNo): bool
    {
        return in_array($itemNo, [4, 8, 12, 16], true);
    }

    private function choiceText(int $score): string
    {
        return match ($score) {
            0 => 'ไม่เลย (น้อยกว่า 1 วัน)',
            1 => 'นานๆ ครั้ง (1-2 วัน)',
            2 => 'บ่อยๆ (3-4 วัน)',
            3 => 'ตลอดเวลา (5-7 วัน)',
            default => '-',
        };
    }

    private function buildResultLevel(int $totalScore): string
    {
        return $totalScore >= self::RISK_SCORE
            ? 'มีแนวโน้มภาวะซึมเศร้า'
            : 'ไม่พบภาวะซึมเศร้าตามเกณฑ์คัดกรอง';
    }

    private function buildSummary(int $totalScore): string
    {
        if ($totalScore >= self::RISK_SCORE) {
            return 'คะแนนรวม ' . $totalScore
                . ' คะแนน อยู่ในเกณฑ์ที่ควรเฝ้าระวังภาวะซึมเศร้าในวัยรุ่น';
        }

        return 'คะแนนรวม ' . $totalScore
            . ' คะแนน ยังไม่ถึงเกณฑ์คัดกรองภาวะซึมเศร้า';
    }

    private function buildRecommendation(int $totalScore): string
    {
        if ($totalScore >= self::RISK_SCORE) {
            return 'ควรพูดคุยให้คำปรึกษา ติดตามอารมณ์และพฤติกรรมอย่างใกล้ชิด '
                . 'และพิจารณาส่งต่อบุคลากรด้านสุขภาพจิตเพื่อประเมินเพิ่มเติม '
                . 'ทั้งนี้ผลแบบคัดกรองไม่ใช่การวินิจฉัยโรค';
        }

        return 'ควรส่งเสริมสุขภาพจิต ติดตามอารมณ์และพฤติกรรมตามปกติ '
            . 'และให้การดูแลสนับสนุนอย่างต่อเนื่อง';
    }

    /**
     * คำถาม CES-D ฉบับภาษาไทย
     */
    private function questions(): array
    {
        return [
            1 => 'ฉันรู้สึกหงุดหงิดง่าย',
            2 => 'ฉันรู้สึกเบื่ออาหาร',
            3 => 'ฉันไม่สามารถขจัดความเศร้าออกจากใจได้แม้จะมีคนคอยช่วยเหลือก็ตาม',
            4 => 'ฉันรู้สึกว่าตนเองดีพอๆ กับคนอื่น',
            5 => 'ฉันไม่มีสมาธิ',
            6 => 'ฉันรู้สึกหดหู่',
            7 => 'ทุกๆ สิ่งที่ฉันกระทำจะต้องฝืนใจ',
            8 => 'ฉันมีความหวังเกี่ยวกับอนาคต',
            9 => 'ฉันรู้สึกว่าชีวิตมีแต่สิ่งล้มเหลว',
            10 => 'ฉันรู้สึกหวาดกลัว',
            11 => 'ฉันนอนไม่ค่อยหลับ',
            12 => 'ฉันมีความสุข',
            13 => 'ฉันไม่ค่อยอยากคุยกับใคร',
            14 => 'ฉันรู้สึกเหงา',
            15 => 'ผู้คนทั่วไปไม่ค่อยเป็นมิตรกับฉัน',
            16 => 'ฉันรู้สึกว่าชีวิตนี้สนุกสนาน',
            17 => 'ฉันร้องไห้',
            18 => 'ฉันรู้สึกเศร้า',
            19 => 'ผู้คนรอบข้างไม่ชอบฉัน',
            20 => 'ฉันรู้สึกท้อถอยในชีวิต',
        ];
    }
}