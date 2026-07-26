<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BehaviorScreening;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\CaseActivity;

class BehaviorScreeningController extends Controller
{
    public function index(Client $client)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client->id);

        $screenings = BehaviorScreening::with('creator')
            ->where('client_id', $client->id)
            ->latest('screening_date')
            ->paginate(10);

        return view('frontend.behavior_screenings.index', compact('client', 'screenings'));
    }

    public function create(Client $client)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client->id);

        $questions = $this->questions();

        return view('frontend.behavior_screenings.create', compact('client', 'questions'));
    }

   public function store(Request $request, Client $client)
        {
            $client = Client::forUser(auth()->user())->findOrFail($client->id);

            $validated = $request->validate([
                'screening_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),

                Rule::unique('behavior_screenings', 'screening_date')
                    ->where(function ($query) use ($client) {
                        return $query->where('client_id', $client->id);
                    }),
            ],

                'observer_name'  => ['nullable', 'string', 'max:255'],
                'age_text'       => ['nullable', 'string', 'max:100'],
                'class_level'    => ['nullable', 'string', 'max:100'],
                'answers'        => ['required', 'array'],
                'remark'         => ['nullable', 'string'],

            ], [

                'screening_date.required'        => 'กรุณาเลือกวันที่ประเมิน',
                'screening_date.date'            => 'รูปแบบวันที่ไม่ถูกต้อง',
                'screening_date.before_or_equal' => 'วันที่ประเมินต้องไม่เกินวันปัจจุบัน',
                'screening_date.unique'          => 'ผู้รับบริการรายนี้มีการประเมินในวันที่ดังกล่าวแล้ว',

                'answers.required'              => 'กรุณาประเมินอย่างน้อย 1 รายการ',

            ]);

            DB::transaction(function () use ($validated, $client) {
                $questions = $this->questions();
                $answers = $validated['answers'] ?? [];

                $learningScore = $this->calculateScore($answers['learning'] ?? []);
                $ldScore       = $this->calculateScore($answers['ld'] ?? []);
                $adhdScore     = $this->calculateScore($answers['adhd'] ?? []);
                $autismScore   = $this->calculateScore($answers['autism'] ?? []);

                $learningRisk = $learningScore >= 5;
                $ldRisk       = $ldScore >= 6;
                $adhdRisk     = $adhdScore >= 6;
                $autismRisk   = $autismScore >= 5;

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

                $screening = BehaviorScreening::create([
                    'client_id'       => $client->id,
                    'created_by'      => auth()->id(),
                    'screening_date'  => $validated['screening_date'],
                    'observer_name'   => $validated['observer_name'] ?? null,
                    'age_text'        => $validated['age_text'] ?? null,
                    'class_level'     => $validated['class_level'] ?? null,

                    'learning_score'  => $learningScore,
                    'ld_score'        => $ldScore,
                    'adhd_score'      => $adhdScore,
                    'autism_score'    => $autismScore,

                    'learning_risk'   => $learningRisk,
                    'ld_risk'         => $ldRisk,
                    'adhd_risk'       => $adhdRisk,
                    'autism_risk'     => $autismRisk,

                    'summary'         => $summary,
                    'recommendation'  => $recommendation,
                    'remark'          => $validated['remark'] ?? null,
                ]);

                foreach ($questions as $category => $items) {
                    foreach ($items as $itemNo => $question) {
                        $answer = isset($answers[$category][$itemNo])
                            ? (bool) $answers[$category][$itemNo]
                            : false;

                        $screening->items()->create([
                            'category' => $category,
                            'item_no'  => $itemNo,
                            'question' => $question,
                            'answer'   => $answer,
                        ]);
                    }
                }

                $hasRisk = $learningRisk || $ldRisk || $adhdRisk || $autismRisk;

               CaseActivity::where('client_id', $client->id)
                    ->where('module', 'behavior_screening')
                    ->delete();

                CaseActivity::record([
                    'client_id'   => $client->id,
                    'module'      => 'behavior_screening',
                    'type'        => $hasRisk ? 'warning' : 'success',
                    'title'       => 'บันทึกแบบประเมินพัฒนาการ',
                    'description' => 'วันที่ประเมิน: ' . ($validated['screening_date'] ?? '-') .
                                    ' | ผลสรุป: ' . ($summary ?: '-') .
                                    ' | คะแนน Learning: ' . $learningScore .
                                    ', LD: ' . $ldScore .
                                    ', ADHD: ' . $adhdScore .
                                    ', Autism: ' . $autismScore,
                    'occurred_at' => now(),
                    'icon'        => 'bi-clipboard2-heart',
                    'url'         => route('behavior-screenings.show', $screening->id),
                ]);
            });

            return redirect()
                ->route('behavior-screenings.index', $client->id)
                ->with('success', 'บันทึกแบบสังเกตพฤติกรรมเรียบร้อยแล้ว');
        }

    public function show(BehaviorScreening $screening)
    {
        $screening->load(['client', 'items', 'creator']);

        $client = Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);

        return view('frontend.behavior_screenings.show', compact('screening', 'client'));
    }

    public function destroy(BehaviorScreening $screening)
    {
        $client = Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);

        $client = Client::forUser(auth()->user())
        ->findOrFail($screening->client_id);

        CaseActivity::where('client_id', $client->id)
            ->where('module', 'behavior_screening')
            ->delete();

        $screening->delete();

        return redirect()
            ->route('behavior-screenings.index', $client->id)
            ->with('success', 'ลบแบบสังเกตพฤติกรรมเรียบร้อยแล้ว');
    }


    public function officialReport(BehaviorScreening $screening)
        {
            $screening->load(['client', 'items']);

            $client = Client::forUser(auth()->user())
                ->findOrFail($screening->client_id);

            return view(
                'frontend.behavior_screenings.official_report',
                compact('screening', 'client')
            );
        }

    private function calculateScore(array $items): int
    {
        return collect($items)
            ->filter(fn ($value) => (int) $value === 1)
            ->count();
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

        if (empty($summary)) {
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
                1  => 'เรียนรู้บทเรียนได้ช้ากว่าเด็กคนอื่นในห้อง',
                2  => 'ลืมง่าย จำสิ่งที่เคยเรียนไปไม่ได้ ต้องเรียนซ้ำ ๆ',
                3  => 'กลัวเมื่อต้องไปแข่งขัน ทำงานที่ตนเองไม่ถนัด',
                4  => 'สรุปใจความสำคัญของเนื้อหาที่เรียนไม่ได้',
                5  => 'ใช้ท่าทีในการแก้ปัญหาเฉพาะหน้าไม่สมวัย',
                6  => 'ตอบสนองต่อสิ่งต่าง ๆ ช้า',
                7  => 'ชอบเล่นกับเด็กที่มีอายุน้อยกว่า',
                8  => 'ช่วยเหลือตนเองในกิจวัตรประจำวันได้น้อย',
                9  => 'การใช้ภาษาไม่สมวัย',
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