<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\DepressionScreening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\CaseActivity;

class DepressionScreeningController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | หน้าแสดงรายการ
    |--------------------------------------------------------------------------
    */

    public function index(Client $client)
    {
        $client = Client::forUser(auth()->user())
            ->findOrFail($client->id);

        $screenings = DepressionScreening::with('creator')
            ->where('client_id', $client->id)
            ->latest('screening_date')
            ->paginate(10);

        return view(
            'frontend.depression_screenings.index',
            compact('client', 'screenings')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | หน้าเพิ่มข้อมูล
    |--------------------------------------------------------------------------
    */

    public function create(Client $client)
    {
        $client = Client::forUser(auth()->user())
            ->findOrFail($client->id);

        $questions = $this->questions();

        return view(
            'frontend.depression_screenings.create',
            compact('client', 'questions')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | บันทึกข้อมูล
    |--------------------------------------------------------------------------
    */

   public function store(Request $request, Client $client)
{
    $client = Client::forUser(auth()->user())
        ->findOrFail($client->id);

    $validated = $request->validate([
       'screening_date' => [
        'required',
        'date',
        'before_or_equal:' . now('Asia/Bangkok')->toDateString(),

        Rule::unique('depression_screenings', 'screening_date')
            ->where(fn ($query) => $query->where('client_id', $client->id)),
    ],

        'observer_name' => ['nullable', 'string', 'max:255'],
        'age_text'      => ['nullable', 'string', 'max:100'],
        'class_level'   => ['nullable', 'string', 'max:100'],
        'answers'       => ['required', 'array'],
        'answers.*'     => ['required', 'integer', 'min:0', 'max:3'],
        'remark'        => ['nullable', 'string'],
    ], [
       'screening_date.required'        => 'กรุณาเลือกวันที่คัดกรอง',
        'screening_date.date'            => 'รูปแบบวันที่ไม่ถูกต้อง',
        'screening_date.before_or_equal' => 'วันที่คัดกรองต้องไม่เกินวันปัจจุบัน',
        'screening_date.unique'          => 'ผู้รับบริการรายนี้มีการคัดกรองในวันที่ดังกล่าวแล้ว',

        'answers.required'        => 'กรุณาตอบแบบคัดกรองให้ครบถ้วน',
        'answers.*.required'      => 'กรุณาตอบแบบคัดกรองให้ครบทุกข้อ',
    ]);

    DB::transaction(function () use ($validated, $client) {

        $questions = $this->questions();
        $answers   = $validated['answers'] ?? [];

        $totalScore = $this->calculateTotalScore($answers);

        $resultLevel = $this->buildResultLevel($totalScore);
        $summary = $this->buildSummary($totalScore);
        $recommendation = $this->buildRecommendation($totalScore);

        $screening = DepressionScreening::create([
            'client_id'      => $client->id,
            'created_by'     => auth()->id(),
            'screening_date' => $validated['screening_date'],
            'observer_name'  => $validated['observer_name'] ?? null,
            'age_text'       => $validated['age_text'] ?? null,
            'class_level'    => $validated['class_level'] ?? null,
            'total_score'    => $totalScore,
            'result_level'   => $resultLevel,
            'summary'        => $summary,
            'recommendation' => $recommendation,
            'remark'         => $validated['remark'] ?? null,
        ]);

        foreach ($questions as $itemNo => $question) {

            $rawScore = (int) ($answers[$itemNo] ?? 0);
            $isReverse = $this->isReverseItem($itemNo);
            $finalScore = $isReverse ? (3 - $rawScore) : $rawScore;

            $screening->items()->create([
                'item_no'     => $itemNo,
                'question'    => $question,
                'score'       => $finalScore,
                'choice_text' => $this->choiceText($rawScore),
                'is_reverse'  => $isReverse,
            ]);
        }

        CaseActivity::where('client_id', $client->id)
            ->where('module', 'depression_screening')
            ->delete();

        CaseActivity::record([
            'client_id'   => $client->id,
            'module'      => 'depression_screening',
            'type'        => $totalScore >= 22 ? 'warning' : 'success',
            'title'       => 'บันทึกแบบคัดกรองภาวะซึมเศร้าในวัยรุ่น',
            'description' => 'วันที่คัดกรอง: ' . ($validated['screening_date'] ?? '-') .
                            ' | คะแนนรวม: ' . $totalScore .
                            ' | ผล: ' . $resultLevel,
            'occurred_at' => now(),
            'icon'        => 'bi-emoji-frown',
            'url'         => route('depression-screenings.show', $screening->id),
        ]);
    });

    return redirect()
        ->route('depression-screenings.index', $client->id)
        ->with('success', 'บันทึกแบบคัดกรองภาวะซึมเศร้าเรียบร้อยแล้ว');
}
    /*
    |--------------------------------------------------------------------------
    | ดูรายละเอียด
    |--------------------------------------------------------------------------
    */

        private function calculateTotalScore(array $answers): int
    {
        $total = 0;

        foreach ($answers as $itemNo => $score) {
            $itemNo = (int) $itemNo;
            $score = (int) $score;

            $total += $this->isReverseItem($itemNo)
                ? (3 - $score)
                : $score;
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
        return $totalScore >= 22
            ? 'มีแนวโน้มภาวะซึมเศร้า'
            : 'ไม่พบภาวะซึมเศร้าตามเกณฑ์คัดกรอง';
    }

    private function buildSummary(int $totalScore): string
    {
        if ($totalScore >= 22) {
            return 'คะแนนรวม ' . $totalScore . ' คะแนน อยู่ในเกณฑ์ที่ควรเฝ้าระวังภาวะซึมเศร้าในวัยรุ่น';
        }

        return 'คะแนนรวม ' . $totalScore . ' คะแนน ยังไม่ถึงเกณฑ์คัดกรองภาวะซึมเศร้า';
    }

    private function buildRecommendation(int $totalScore): string
    {
        if ($totalScore >= 22) {
            return 'ควรพูดคุยให้คำปรึกษา ติดตามอารมณ์และพฤติกรรมอย่างใกล้ชิด และพิจารณาส่งต่อบุคลากรด้านสุขภาพจิตเพื่อประเมินเพิ่มเติม ทั้งนี้ผลแบบคัดกรองไม่ใช่การวินิจฉัยโรค';
        }

        return 'ควรส่งเสริมสุขภาพจิต ติดตามอารมณ์และพฤติกรรมตามปกติ และให้การดูแลสนับสนุนอย่างต่อเนื่อง';
    }

        public function show(DepressionScreening $screening)
        {
            $screening->load([
                'client',
                'items',
                'creator'
            ]);

            $client = Client::forUser(auth()->user())
                ->findOrFail($screening->client_id);

            return view(
                'frontend.depression_screenings.show',
                compact('screening', 'client')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ลบข้อมูล
        |--------------------------------------------------------------------------
        */

    public function destroy(DepressionScreening $screening)
    {
        $client = Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);

        $screening->delete();

        return redirect()
            ->route('depression-screenings.index', $client->id)
            ->with('success', 'ลบแบบคัดกรองเรียบร้อยแล้ว');
    }

    /*
    |--------------------------------------------------------------------------
    | รายงานราชการ
    |--------------------------------------------------------------------------
    */

    public function officialReport(DepressionScreening $screening)
    {
        $screening->load([
            'client',
            'items',
            'creator'
        ]);

        $client = Client::forUser(auth()->user())
            ->findOrFail($screening->client_id);

        return view(
            'frontend.depression_screenings.official_report',
            compact('screening', 'client')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | คำถาม CES-D
    |--------------------------------------------------------------------------
    */

    private function questions(): array
    {
        return [

            1  => 'ฉันรู้สึกหงุดหงิดง่าย',
            2  => 'ฉันรู้สึกเบื่ออาหาร',
            3  => 'ฉันไม่สามารถขจัดความเศร้าออกจากใจได้แม้จะมีคนคอยช่วยเหลือก็ตาม',
            4  => 'ฉันรู้สึกว่าตนเองดีพอๆ กับคนอื่น',

            5  => 'ฉันไม่มีสมาธิ',
            6  => 'ฉันรู้สึกหดหู่',
            7  => 'ทุกๆ สิ่งที่ฉันกระทำจะต้องฝืนใจ',
            8  => 'ฉันมีความหวังเกี่ยวกับอนาคต',

            9  => 'ฉันรู้สึกว่าชีวิตมีแต่สิ่งล้มเหลว',
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