<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\NutritionAssessment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NutritionAssessmentController extends Controller
{
    public function index(Client $client)
    {
        $client = $this->findAccessibleClient($client);

        $assessments = NutritionAssessment::query()
            ->where('client_id', $client->id)
            ->latest('assessment_date')
            ->latest('id')
            ->get();

        return view('frontend.nutrition_assessments.index', compact(
            'client',
            'assessments'
        ));
    }

    public function create(Client $client)
    {
        $client = $this->findAccessibleClient($client);

        return view(
            'frontend.nutrition_assessments.create',
            compact('client')
        );
    }

    public function store(Request $request, Client $client)
    {
        $client = $this->findAccessibleClient($client);

        $validated = $request->validate(
            $this->validationRules($client),
            $this->validationMessages()
        );

        NutritionAssessment::create(
            $this->buildAssessmentData($validated, $client) + [
                'client_id'  => $client->id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );

        return redirect()
            ->route('nutrition_assessments.index', $client->id)
            ->with('success', 'บันทึกผลประเมินภาวะโภชนาการเรียบร้อยแล้ว');
    }

    public function edit(Client $client, NutritionAssessment $assessment)
    {
        $client = $this->findAccessibleClient($client);
        $this->ensureAssessmentBelongsToClient($assessment, $client);

        return view(
            'frontend.nutrition_assessments.edit',
            compact('client', 'assessment')
        );
    }

    public function update(
        Request $request,
        Client $client,
        NutritionAssessment $assessment
    ) {
        $client = $this->findAccessibleClient($client);
        $this->ensureAssessmentBelongsToClient($assessment, $client);

        $validated = $request->validate(
            $this->validationRules($client, $assessment->id),
            $this->validationMessages()
        );

        $assessment->update(
            $this->buildAssessmentData($validated, $client) + [
                'updated_by' => auth()->id(),
            ]
        );

        return redirect()
            ->route('nutrition_assessments.show', [$client->id, $assessment->id])
            ->with('success', 'แก้ไขผลประเมินภาวะโภชนาการเรียบร้อยแล้ว');
    }

    public function show(Client $client, NutritionAssessment $assessment)
    {
        $client = $this->findAccessibleClient($client);
        $this->ensureAssessmentBelongsToClient($assessment, $client);

        $growthRecords = NutritionAssessment::query()
            ->where('client_id', $client->id)
            ->whereNotNull('assessment_date')
            ->orderBy('assessment_date')
            ->orderBy('id')
            ->get([
                'id',
                'assessment_date',
                'height_cm',
                'weight_kg',
                'bmi',
            ]);

        return view('frontend.nutrition_assessments.show', [
            'client'        => $client,
            'assessment'    => $assessment,
            'growthRecords' => $growthRecords,
        ]);
    }

    public function destroy(Client $client, NutritionAssessment $assessment)
    {
        $client = $this->findAccessibleClient($client);
        $this->ensureAssessmentBelongsToClient($assessment, $client);

        abort_unless(
            auth()->user()?->role === 'admin',
            403,
            'คุณไม่มีสิทธิ์ลบข้อมูลนี้'
        );

        $assessment->delete();

        return redirect()
            ->route('nutrition_assessments.index', $client->id)
            ->with('success', 'ลบผลประเมินภาวะโภชนาการเรียบร้อยแล้ว');
    }

    private function findAccessibleClient(Client $client): Client
    {
        return Client::forUser(auth()->user())
            ->findOrFail($client->id);
    }

    private function ensureAssessmentBelongsToClient(
        NutritionAssessment $assessment,
        Client $client
    ): void {
        abort_unless((int) $assessment->client_id === (int) $client->id, 403);
    }

    private function validationRules(Client $client, ?int $ignoreId = null): array
    {
        $assessmentDateRules = [
            'required',
            'date',
            'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
        ];

        if ($client->birth_date) {
            $assessmentDateRules[] = 'after_or_equal:'
                . Carbon::parse($client->birth_date)->toDateString();
        }

        $uniqueDateRule = Rule::unique(
            'nutrition_assessments',
            'assessment_date'
        )->where(
            fn ($query) => $query->where('client_id', $client->id)
        );

        if ($ignoreId !== null) {
            $uniqueDateRule->ignore($ignoreId);
        }

        $assessmentDateRules[] = $uniqueDateRule;

        return [
            'assessment_date' => $assessmentDateRules,
            'height_cm'       => ['required', 'numeric', 'min:30', 'max:250'],
            'weight_kg'       => ['required', 'numeric', 'min:1', 'max:300'],
            'note'            => ['nullable', 'string', 'max:2000'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'assessment_date.required'        => 'กรุณาเลือกวันที่ชั่งวัด',
            'assessment_date.date'            => 'รูปแบบวันที่ชั่งวัดไม่ถูกต้อง',
            'assessment_date.before_or_equal' => 'ไม่สามารถบันทึกวันที่ในอนาคตได้',
            'assessment_date.after_or_equal'  => 'วันที่ชั่งวัดต้องไม่ก่อนวันเกิดของผู้รับบริการ',
            'assessment_date.unique'          => 'เด็กคนนี้มีผลประเมินในวันที่เลือกแล้ว กรุณาแก้ไขรายการเดิม',
            'height_cm.required'              => 'กรุณาระบุส่วนสูง',
            'height_cm.numeric'               => 'ส่วนสูงต้องเป็นตัวเลข',
            'height_cm.min'                   => 'ส่วนสูงต้องไม่น้อยกว่า 30 เซนติเมตร',
            'height_cm.max'                   => 'ส่วนสูงต้องไม่เกิน 250 เซนติเมตร',
            'weight_kg.required'              => 'กรุณาระบุน้ำหนัก',
            'weight_kg.numeric'               => 'น้ำหนักต้องเป็นตัวเลข',
            'weight_kg.min'                   => 'น้ำหนักต้องไม่น้อยกว่า 1 กิโลกรัม',
            'weight_kg.max'                   => 'น้ำหนักต้องไม่เกิน 300 กิโลกรัม',
            'note.string'                     => 'หมายเหตุต้องเป็นข้อความ',
            'note.max'                        => 'หมายเหตุต้องมีความยาวไม่เกิน 2,000 ตัวอักษร',
        ];
    }

    private function buildAssessmentData(array $validated, Client $client): array
    {
        $assessmentDate = Carbon::parse($validated['assessment_date'])->startOfDay();
        $birthDate = $client->birth_date
            ? Carbon::parse($client->birth_date)->startOfDay()
            : null;

        $ageYear = null;
        $ageMonth = null;

        if ($birthDate && $assessmentDate->greaterThanOrEqualTo($birthDate)) {
            $age = $birthDate->diff($assessmentDate);
            $ageYear = $age->y;
            $ageMonth = $age->m;
        }

        $heightCm = round((float) $validated['height_cm'], 2);
        $weightKg = round((float) $validated['weight_kg'], 2);
        $heightMeter = $heightCm / 100;
        $bmi = round($weightKg / ($heightMeter ** 2), 2);
        $bmiResult = $this->resolveBmiResult($bmi);

        return [
            'assessment_date'  => $validated['assessment_date'],
            'birth_date'       => $client->birth_date,
            'age_year'         => $ageYear,
            'age_month'        => $ageMonth,
            'gender'           => $client->gender,
            'height_cm'        => $heightCm,
            'weight_kg'        => $weightKg,
            'bmi'              => $bmi,
            'bmi_result'       => $bmiResult,
            'nutrition_status' => $bmiResult,
            'summary_result'   => $bmiResult,
            'note'             => $validated['note'] ?? null,
        ];
    }

    private function resolveBmiResult(float $bmi): string
    {
        return match (true) {
            $bmi < 18.5 => 'น้ำหนักน้อย',
            $bmi < 23   => 'สมส่วน',
            $bmi < 25   => 'เริ่มอ้วน',
            $bmi < 30   => 'อ้วน',
            default     => 'อ้วนมาก',
        };
    }
}
