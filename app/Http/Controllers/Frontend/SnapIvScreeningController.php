<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\SnapIvScreening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SnapIvScreeningController extends Controller
{
    public function index(Client $client)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client->id);

        $screenings = SnapIvScreening::with('creator')
            ->where('client_id', $client->id)
            ->latest('screening_date')
            ->latest('id')
            ->paginate(10);

        return view('frontend.snap_iv_screenings.index', compact('client', 'screenings'));
    }

    public function create(Client $client)
    {
        $client = Client::forUser(auth()->user())
            ->with(['educationRecords.education'])
            ->findOrFail($client->id);

        $questions = $this->questions();

        return view('frontend.snap_iv_screenings.create', compact('client', 'questions'));
    }

    public function store(Request $request, Client $client)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client->id);

        $validated = $request->validate([
           'screening_date' => [
            'required',
            'date',
            'before_or_equal:' . now('Asia/Bangkok')->toDateString(),

            Rule::unique('snap_iv_screenings', 'screening_date')
                ->where(fn ($query) => $query->where('client_id', $client->id)),
        ],


            'observer_name' => ['nullable', 'string', 'max:255'],
            'relationship'  => ['nullable', 'string', 'max:255'],
            'age_text'      => ['nullable', 'string', 'max:100'],
            'class_level'   => ['nullable', 'string', 'max:100'],
            'term'          => ['nullable', 'string', 'max:100'],
            'grade_average' => ['nullable', 'string', 'max:100'],
            'answers'       => ['required', 'array'],
            'remark'        => ['nullable', 'string'],
        ], [
           'screening_date.required'        => 'กรุณาเลือกวันที่ประเมิน',
            'screening_date.date'            => 'รูปแบบวันที่ประเมินไม่ถูกต้อง',
            'screening_date.before_or_equal' => 'วันที่ประเมินต้องไม่เกินวันปัจจุบัน',
            'screening_date.unique'          => 'ผู้รับบริการรายนี้มีแบบประเมิน SNAP-IV ในวันที่ดังกล่าวแล้ว',
            'answers.required'        => 'กรุณาประเมินอย่างน้อย 1 รายการ',
        ]);

        DB::transaction(function () use ($validated, $client) {
            $questions = $this->questions();
            $answers = $validated['answers'] ?? [];

            $inattentionScore = $this->calculateScore($answers['inattention'] ?? []);
            $hyperactivityScore = $this->calculateScore($answers['hyperactivity'] ?? []);
            $oppositionalScore = $this->calculateScore($answers['oppositional'] ?? []);

            $totalScore = $inattentionScore + $hyperactivityScore + $oppositionalScore;

            $inattentionLevel = $this->interpretMainSymptom($inattentionScore);
            $hyperactivityLevel = $this->interpretMainSymptom($hyperactivityScore);
            $oppositionalLevel = $this->interpretOppositional($oppositionalScore);

            $summary = $this->buildSummary(
                $inattentionLevel,
                $hyperactivityLevel,
                $oppositionalLevel
            );

            $recommendation = $this->buildRecommendation(
                $inattentionLevel,
                $hyperactivityLevel,
                $oppositionalLevel
            );

            $screening = SnapIvScreening::create([
                'client_id' => $client->id,
                'created_by' => auth()->id(),

                'screening_date' => $validated['screening_date'],
                'observer_name' => $validated['observer_name'] ?? null,
                'relationship' => $validated['relationship'] ?? null,
                'age_text' => $validated['age_text'] ?? null,
                'class_level' => $validated['class_level'] ?? null,
                'term' => $validated['term'] ?? null,
                'grade_average' => $validated['grade_average'] ?? null,

                'inattention_score' => $inattentionScore,
                'hyperactivity_score' => $hyperactivityScore,
                'oppositional_score' => $oppositionalScore,
                'total_score' => $totalScore,

                'inattention_level' => $inattentionLevel,
                'hyperactivity_level' => $hyperactivityLevel,
                'oppositional_level' => $oppositionalLevel,

                'summary' => $summary,
                'recommendation' => $recommendation,
                'remark' => $validated['remark'] ?? null,
            ]);

            foreach ($questions as $category => $items) {
                foreach ($items as $itemNo => $question) {
                    $score = isset($answers[$category][$itemNo])
                        ? (int) $answers[$category][$itemNo]
                        : 0;

                    $screening->items()->create([
                        'category' => $category,
                        'item_no' => $itemNo,
                        'question' => $question,
                        'score' => $score,
                    ]);
                }
            }

            $hasConcern = $inattentionLevel !== 'ปกติ'
                || $hyperactivityLevel !== 'ปกติ'
                || $oppositionalLevel !== 'ปกติ';

            CaseActivity::record([
                'client_id' => $client->id,
                'module' => 'snap_iv',
                'type' => $hasConcern ? 'warning' : 'success',
                'title' => 'บันทึกแบบประเมินพฤติกรรม SNAP-IV',
                'description' => 'วันที่ประเมิน: ' . ($validated['screening_date'] ?? '-') .
                    ' | ขาดสมาธิ: ' . $inattentionScore . ' คะแนน (' . $inattentionLevel . ')' .
                    ' | ซน/หุนหันพลันแล่น: ' . $hyperactivityScore . ' คะแนน (' . $hyperactivityLevel . ')' .
                    ' | ดื้อ/ต่อต้าน: ' . $oppositionalScore . ' คะแนน (' . $oppositionalLevel . ')',
                'occurred_at' => now(),
                'icon' => 'bi-clipboard2-pulse',
                'url' => route('snap-iv.show', $screening->id),
            ]);
        });

        return redirect()
            ->route('snap-iv.index', $client->id)
            ->with('success', 'บันทึกแบบประเมิน SNAP-IV เรียบร้อยแล้ว');
    }

    public function show(SnapIvScreening $screening)
    {
        $screening->load(['client', 'items', 'creator']);

        $client = Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);

        return view('frontend.snap_iv_screenings.show', compact('screening', 'client'));
    }

    public function officialReport(SnapIvScreening $screening)
    {
        $screening->load(['client', 'items', 'creator']);

        $client = Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);

        return view('frontend.snap_iv_screenings.official_report', compact('screening', 'client'));
    }

    public function destroy(SnapIvScreening $screening)
    {
        $client = Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);

        $screening->delete();

        return redirect()
            ->route('snap-iv.index', $client->id)
            ->with('success', 'ลบแบบประเมิน SNAP-IV เรียบร้อยแล้ว');
    }

    private function calculateScore(array $items): int
    {
        return collect($items)
            ->map(fn ($value) => (int) $value)
            ->sum();
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
        return
            "อาการขาดสมาธิ: {$inattentionLevel}\n" .
            "อาการซน อยู่ไม่นิ่ง และหุนหันพลันแล่น: {$hyperactivityLevel}\n" .
            "อาการดื้อและต่อต้าน: {$oppositionalLevel}";
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