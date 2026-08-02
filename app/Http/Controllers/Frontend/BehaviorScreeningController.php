<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BehaviorScreening;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\EducationRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BehaviorScreeningController extends Controller
{
    public function index(Client $client)
    {
        $client = $this->accessibleClient($client);

        $screenings = BehaviorScreening::query()
            ->with('creator')
            ->where('client_id', $client->id)
            ->orderByDesc('screening_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('frontend.behavior_screenings.index', compact('client', 'screenings'));
    }

    public function create(Client $client)
    {
        $client = $this->accessibleClient($client);
        $questions = $this->questions();
        $defaultDate = now('Asia/Bangkok')->toDateString();
        $ageText = $this->resolveAgeText($client, $defaultDate);
        $classLevel = $this->resolveClassLevel($client);

        return view(
            'frontend.behavior_screenings.create',
            compact('client', 'questions', 'defaultDate', 'ageText', 'classLevel')
        );
    }

    public function store(Request $request, Client $client)
    {
        $client = $this->accessibleClient($client);
        $questions = $this->questions();

        $request->merge([
            'observer_name' => $this->nullableTrim($request->input('observer_name')),
            'remark' => $this->nullableTrim($request->input('remark')),
        ]);

        $validated = $request->validate(
            $this->validationRules($client, $questions),
            $this->validationMessages($questions)
        );

        DB::transaction(function () use ($validated, $client, $questions) {
            $answers = $validated['answers'];

            $learningScore = $this->calculateScore($answers['learning']);
            $ldScore = $this->calculateScore($answers['ld']);
            $adhdScore = $this->calculateScore($answers['adhd']);
            $autismScore = $this->calculateScore($answers['autism']);

            $learningRisk = $learningScore >= 5;
            $ldRisk = $ldScore >= 6;
            $adhdRisk = $adhdScore >= 6;
            $autismRisk = $autismScore >= 5;

            $summary = $this->buildSummary(
                $learningRisk,
                $ldRisk,
                $adhdRisk,
                $autismRisk
            );

            $recommendation = $this->buildRecommendation(
                $learningRisk,
                $ldRisk,
                $adhdRisk,
                $autismRisk
            );

            $screeningDate = $validated['screening_date'];

            $screening = BehaviorScreening::create([
                'client_id' => $client->id,
                'created_by' => auth()->id(),
                'screening_date' => $screeningDate,
                'observer_name' => $validated['observer_name'] ?? null,
                'age_text' => $this->resolveAgeText($client, $screeningDate),
                'class_level' => $this->resolveClassLevel($client),
                'learning_score' => $learningScore,
                'ld_score' => $ldScore,
                'adhd_score' => $adhdScore,
                'autism_score' => $autismScore,
                'learning_risk' => $learningRisk,
                'ld_risk' => $ldRisk,
                'adhd_risk' => $adhdRisk,
                'autism_risk' => $autismRisk,
                'summary' => $summary,
                'recommendation' => $recommendation,
                'remark' => $validated['remark'] ?? null,
            ]);

            $this->insertScreeningItems($screening, $questions, $answers);
            $this->syncCaseActivity($client->id, $screening);

        });

        return redirect()
            ->route('behavior-screenings.index', $client->id)
            ->with('success', 'บันทึกแบบสังเกตพฤติกรรมเรียบร้อยแล้ว');
    }

    public function show(BehaviorScreening $screening)
    {
        $client = Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);

        $screening->load([
            'creator',
            'items' => fn ($query) => $query->orderBy('item_no'),
        ]);

        return view('frontend.behavior_screenings.show', compact('screening', 'client'));
    }

    public function destroy(BehaviorScreening $screening)
    {
        $client = Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);

        DB::transaction(function () use ($screening, $client) {
            // ลบรายการย่อยโดยตรงเพื่อรองรับฐานข้อมูลเดิมที่อาจยังไม่ได้ตั้ง cascade delete
            $screening->items()->delete();
            $screening->delete();

            $latestScreening = BehaviorScreening::query()
                ->where('client_id', $client->id)
                ->orderByDesc('screening_date')
                ->orderByDesc('id')
                ->first();

            $this->syncCaseActivity($client->id, $latestScreening);
        });

        return redirect()
            ->route('behavior-screenings.index', $client->id)
            ->with('success', 'ลบแบบสังเกตพฤติกรรมเรียบร้อยแล้ว');
    }

    public function officialReport(BehaviorScreening $screening)
    {
        $client = Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);

        $screening->load([
            'items' => fn ($query) => $query->orderBy('item_no'),
        ]);

        return view(
            'frontend.behavior_screenings.official_report',
            compact('screening', 'client')
        );
    }

    private function accessibleClient(Client $client): Client
    {
        return Client::forUser(auth()->user())->findOrFail($client->id);
    }

    private function validationRules(Client $client, array $questions): array
    {
        $rules = [
            'screening_date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
                Rule::unique('behavior_screenings', 'screening_date')
                    ->where(fn ($query) => $query->where('client_id', $client->id)),
            ],
            'observer_name' => ['nullable', 'string', 'max:255'],
            'answers' => ['required', 'array:learning,ld,adhd,autism'],
            'remark' => ['nullable', 'string', 'max:5000'],
        ];

        foreach ($questions as $category => $items) {
            $rules["answers.{$category}"] = ['required', 'array', 'size:' . count($items)];

            foreach (array_keys($items) as $itemNo) {
                $rules["answers.{$category}.{$itemNo}"] = [
                    'required',
                    Rule::in([0, 1, '0', '1']),
                ];
            }
        }

        return $rules;
    }

    private function validationMessages(array $questions): array
    {
        $messages = [
            'screening_date.required' => 'กรุณาเลือกวันที่ประเมิน',
            'screening_date.date_format' => 'รูปแบบวันที่ประเมินไม่ถูกต้อง',
            'screening_date.before_or_equal' => 'วันที่ประเมินต้องไม่เกินวันปัจจุบัน',
            'screening_date.unique' => 'ผู้รับบริการรายนี้มีการประเมินในวันที่ดังกล่าวแล้ว',
            'observer_name.string' => 'ชื่อผู้ประเมินต้องเป็นข้อความ',
            'observer_name.max' => 'ชื่อผู้ประเมินต้องไม่เกิน 255 ตัวอักษร',
            'answers.required' => 'กรุณาตอบแบบสังเกตพฤติกรรมให้ครบทุกข้อ',
            'answers.array' => 'ข้อมูลคำตอบไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง',
            'remark.string' => 'หมายเหตุต้องเป็นข้อความ',
            'remark.max' => 'หมายเหตุต้องไม่เกิน 5,000 ตัวอักษร',
        ];

        foreach ($questions as $category => $items) {
            $messages["answers.{$category}.required"] = 'กรุณาตอบแบบสังเกตพฤติกรรมให้ครบทุกข้อ';
            $messages["answers.{$category}.size"] = 'กรุณาตอบแบบสังเกตพฤติกรรมให้ครบทุกข้อ';

            foreach (array_keys($items) as $itemNo) {
                $messages["answers.{$category}.{$itemNo}.required"] = "กรุณาตอบข้อ {$itemNo}";
                $messages["answers.{$category}.{$itemNo}.in"] = "คำตอบข้อ {$itemNo} ไม่ถูกต้อง";
            }
        }

        return $messages;
    }

    private function insertScreeningItems(
        BehaviorScreening $screening,
        array $questions,
        array $answers
    ): void {
        $relation = $screening->items();
        $itemModel = $relation->getRelated();
        $foreignKey = $relation->getForeignKeyName();
        $timestamp = now('Asia/Bangkok');
        $rows = [];

        foreach ($questions as $category => $items) {
            foreach ($items as $itemNo => $question) {
                $row = [
                    $foreignKey => $screening->id,
                    'category' => $category,
                    'item_no' => $itemNo,
                    'question' => $question,
                    'answer' => (int) $answers[$category][$itemNo],
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

    private function syncCaseActivity(
        int $clientId,
        ?BehaviorScreening $screening
    ): void {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'behavior_screening')
            ->delete();

        if (! $screening) {
            return;
        }

        $hasRisk = $screening->learning_risk
            || $screening->ld_risk
            || $screening->adhd_risk
            || $screening->autism_risk;

        $screeningDate = Carbon::parse(
            $screening->screening_date,
            'Asia/Bangkok'
        )->startOfDay();

        CaseActivity::record([
            'client_id' => $clientId,
            'module' => 'behavior_screening',
            'type' => $hasRisk ? 'warning' : 'success',
            'title' => 'บันทึกแบบสังเกตพฤติกรรม 4 โรค',
            'description' => 'วันที่ประเมิน: ' . $screeningDate->format('d/m/') . ($screeningDate->year + 543)
                . ' | ผลสรุป: ' . ($screening->summary ?: '-')
                . ' | คะแนน Learning: ' . $screening->learning_score
                . ', LD: ' . $screening->ld_score
                . ', ADHD: ' . $screening->adhd_score
                . ', Autism: ' . $screening->autism_score,
            'occurred_at' => $screeningDate,
            'icon' => 'bi-clipboard2-heart',
            'url' => route('behavior-screenings.show', $screening->id),
        ]);
    }

    private function calculateScore(array $items): int
    {
        return collect($items)
            ->filter(fn ($value) => (int) $value === 1)
            ->count();
    }

    private function resolveAgeText(Client $client, string $screeningDate): ?string
    {
        if (! $client->birth_date) {
            return null;
        }

        $birthDate = Carbon::parse($client->birth_date, 'Asia/Bangkok')->startOfDay();
        $assessmentDate = Carbon::createFromFormat(
            'Y-m-d',
            $screeningDate,
            'Asia/Bangkok'
        )->startOfDay();

        if ($birthDate->greaterThan($assessmentDate)) {
            return null;
        }

        $age = $birthDate->diff($assessmentDate);

        if ($age->y === 0) {
            return $age->m . ' เดือน';
        }

        return $age->m > 0
            ? $age->y . ' ปี ' . $age->m . ' เดือน'
            : $age->y . ' ปี';
    }

    /**
     * ดึงชั้นเรียนจากผลการเรียนที่มีปีการศึกษาและภาคเรียนสูงสุด
     * ไม่ใช้ record_date เป็นตัวตัดสิน เพราะวันที่บันทึกอาจไม่ตรงลำดับภาคเรียน
     * ตัวอย่าง: 2/2569 ต้องมาก่อน 1/2569 แม้รายการ 1/2569 จะบันทึกภายหลัง
     */
    private function resolveClassLevel(Client $client): ?string
    {
        return data_get(
            $this->latestEducationRecord($client->id),
            'education.education_name'
        );
    }

    /**
     * ใช้หลักเดียวกับ SNAP-IV:
     * 1) ปีการศึกษาสูงสุด
     * 2) ภาคเรียนสูงสุด
     * 3) id ล่าสุด เฉพาะเมื่อปีและภาคเรียนเท่ากัน
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

    private function nullableTrim(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function buildSummary(
        bool $learningRisk,
        bool $ldRisk,
        bool $adhdRisk,
        bool $autismRisk
    ): string {
        $summary = [];

        if ($learningRisk) {
            $summary[] = 'มีแนวโน้มภาวะเรียนรู้ช้า';
        }

        if ($ldRisk) {
            $summary[] = 'มีแนวโน้มภาวะแอลดี';
        }

        if ($adhdRisk) {
            $summary[] = 'มีแนวโน้มภาวะสมาธิสั้น';
        }

        if ($autismRisk) {
            $summary[] = 'มีแนวโน้มภาวะออทิสติก';
        }

        if ($summary === []) {
            $summary[] = 'ไม่พบความเสี่ยงตามเกณฑ์แบบสังเกตเบื้องต้น';
        }

        return implode("\n", $summary);
    }

    private function buildRecommendation(
        bool $learningRisk,
        bool $ldRisk,
        bool $adhdRisk,
        bool $autismRisk
    ): string {
        if (! $learningRisk && ! $ldRisk && ! $adhdRisk && ! $autismRisk) {
            return 'ควรส่งเสริมและติดตามพัฒนาการ การเรียนรู้ และพฤติกรรมตามปกติอย่างต่อเนื่อง';
        }

        return 'ควรเฝ้าระวัง ติดตามพฤติกรรมอย่างใกล้ชิด ประสานครู ผู้ปกครอง และพิจารณาส่งต่อหน่วยบริการสาธารณสุขหรือผู้เชี่ยวชาญ เพื่อประเมินเพิ่มเติม ทั้งนี้ผลแบบสังเกตนี้เป็นการคัดกรองเบื้องต้น ไม่ใช่การวินิจฉัยโรค';
    }

    private function questions(): array
    {
        return [
            'learning' => [
                1 => 'เรียนรู้บทเรียนได้ช้ากว่าเด็กคนอื่นในห้อง',
                2 => 'ลืมง่าย จำสิ่งที่เคยเรียนไปไม่ได้ ต้องเรียนซ้ำ ๆ',
                3 => 'กลัวเมื่อต้องไปแข่งขัน ทำงานที่ตนเองไม่ถนัด',
                4 => 'สรุปใจความสำคัญของเนื้อหาที่เรียนไม่ได้',
                5 => 'ใช้ท่าทีในการแก้ปัญหาเฉพาะหน้าไม่สมวัย',
                6 => 'ตอบสนองต่อสิ่งต่าง ๆ ช้า',
                7 => 'ชอบเล่นกับเด็กที่มีอายุน้อยกว่า',
                8 => 'ช่วยเหลือตนเองในกิจวัตรประจำวันได้น้อย',
                9 => 'การใช้ภาษาไม่สมวัย',
                10 => 'เล่นไม่ค่อยเหมือนเพื่อน',
            ],
            'ld' => [
                11 => 'ดูคล้ายหรือโตเกินกว่าเพื่อน ๆ ยกเว้นเรื่องเรียน',
                12 => 'อ่านผิดบ่อย ๆ อ่านไม่คล่อง หรืออ่านไม่ได้',
                13 => 'อ่านช้ามาก อ่านไม่ออก',
                14 => 'อ่านแล้วจับใจความสำคัญไม่ได้',
                15 => 'เขียนพยัญชนะหรือตัวเลขที่คล้ายกันสลับกัน',
                16 => 'เขียนผิด เขียนไม่คล่องบ่อย ๆ',
                17 => 'สะกดคำตามการผสมเสียงไม่ได้ ชอบทำคำขาด ๆ หาย ๆ',
                18 => 'ไม่เข้าใจค่าของจำนวน เช่น หน่วย สิบ ร้อย พัน หมื่น',
                19 => 'คำนวณ บวก ลบ คูณ หาร ไม่ได้',
                20 => 'ไม่เข้าใจหลักการพื้นฐานทางคณิตศาสตร์ เช่น การเข้าใจความหมายของสัญลักษณ์ เวลา ทิศทาง ขนาด ระยะทาง การจัดลำดับ การเปรียบเทียบ',
            ],
            'adhd' => [
                21 => 'ซนมาก อยู่ไม่นิ่ง ยุกยิกตลอดเวลา',
                22 => 'พูดมาก',
                23 => 'ชอบลุกจากที่นั่งขณะอยู่ในห้องเรียน',
                24 => 'เหม่อบ่อย ๆ ใจลอย ต้องคอยเรียก',
                25 => 'ตื่นตัวไวต่อเสียงหรือสิ่งเร้าภายนอก',
                26 => 'ทำงานไม่เรียบร้อย ไม่รอบคอบ',
                27 => 'ไม่สนใจในการทำงาน หรือทำได้เพียงช่วงสั้น ๆ',
                28 => 'ทำงานช้า ทำงานไม่เสร็จ แต่ถ้าตนเองชอบจะทำได้เร็วขึ้น',
                29 => 'รอคอยไม่ได้',
                30 => 'ใจร้อน ควบคุมอารมณ์ไม่ค่อยได้',
            ],
            'autism' => [
                31 => 'ชอบแยกตัวอยู่คนเดียว',
                32 => 'สบตาเพียงช่วงสั้น ๆ หรือไม่สบตาเวลาพูดกับเพื่อน',
                33 => 'เมื่อต้องเกี่ยวข้องกับผู้อื่น ไม่เข้าใจกติกา ไม่เข้าใจวิธีการเล่น',
                34 => 'ไม่เล่นกับเพื่อน หรือมีเพื่อนน้อย',
                35 => 'ไม่สามารถร่วมสนทนากับเพื่อน หรือไม่สามารถตอบคำถามคนอื่นได้',
                36 => 'มีระดับการใช้ภาษาที่ต่ำกว่าวัย',
                37 => 'มักพูดเล่าเรื่องของตนเอง ไม่สนใจเรื่องเพื่อนพูดหรืออื่น ๆ',
                38 => 'ไม่เข้าใจมุกตลก คำพังเพย คำประชด',
                39 => 'ไม่ชอบการเปลี่ยนแปลง ชอบทำกิจวัตรเดิม ๆ ซ้ำ ๆ',
                40 => 'มีพฤติกรรมกระตุ้นตนเอง สะบัดมือ เล่นมือ',
            ],
        ];
    }
}