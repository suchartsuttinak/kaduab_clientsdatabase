<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\NutritionAssessment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class NutritionAssessmentController extends Controller
{
    public function index(Client $client)
    {
        $client = Client::forUser(auth()->user())
            ->findOrFail($client->id);

      $assessments = NutritionAssessment::where('client_id', $client->id)
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
    $client = Client::forUser(auth()->user())
        ->findOrFail($client->id);

    return view(
        'frontend.nutrition_assessments.create',
        compact('client')
    );
}

public function store(Request $request, Client $client)
{
    $client = Client::forUser(auth()->user())
        ->findOrFail($client->id);

    $validated = $request->validate([
       'assessment_date' => [
        'required',
        'date',
        'before_or_equal:today',
        Rule::unique('nutrition_assessments', 'assessment_date')
            ->where(fn ($query) => $query->where('client_id', $client->id)),
    ],
        'height_cm' => 'required|numeric|min:30|max:250',
        'weight_kg' => 'required|numeric|min:1|max:300',
        'note' => 'nullable|string',
    ], [
         'assessment_date.unique' => 'เด็กคนนี้มีผลประเมินในวันที่เลือกแล้ว กรุณาแก้ไขรายการเดิม',
         'assessment_date.before_or_equal' => 'ไม่สามารถบันทึกวันที่ในอนาคตได้',
    ]);

    $birthDate = $client->birth_date
        ? Carbon::parse($client->birth_date)
        : null;

    $assessmentDate = Carbon::parse($validated['assessment_date']);

    $ageYear = null;
    $ageMonth = null;

    if ($birthDate) {
        $age = $birthDate->diff($assessmentDate);
        $ageYear = $age->y;
        $ageMonth = $age->m;
    }

    $heightCm = (float) $validated['height_cm'];
    $weightKg = (float) $validated['weight_kg'];

    $heightMeter = $heightCm / 100;

    $bmi = $heightMeter > 0
        ? round($weightKg / ($heightMeter * $heightMeter), 2)
        : null;

    $bmiResult = null;

    if ($bmi !== null) {
        if ($bmi < 18.5) {
            $bmiResult = 'น้ำหนักน้อย';
        } elseif ($bmi < 23) {
            $bmiResult = 'สมส่วน';
        } elseif ($bmi < 25) {
            $bmiResult = 'เริ่มอ้วน';
        } elseif ($bmi < 30) {
            $bmiResult = 'อ้วน';
        } else {
            $bmiResult = 'อ้วนมาก';
        }
    }

    NutritionAssessment::create([
        'client_id'          => $client->id,
        'assessment_date'    => $validated['assessment_date'],
        'birth_date'         => $client->birth_date,
        'age_year'           => $ageYear,
        'age_month'          => $ageMonth,
        'gender'             => $client->gender,
        'height_cm'          => $heightCm,
        'weight_kg'          => $weightKg,
        'bmi'                => $bmi,
        'bmi_result'         => $bmiResult,
        'nutrition_status'   => $bmiResult,
        'summary_result'     => $bmiResult,
        'note'               => $validated['note'] ?? null,
        'created_by'         => auth()->id(),
        'updated_by'         => auth()->id(),
    ]);

    return redirect()
        ->route('nutrition_assessments.index', $client->id)
        ->with('success', 'บันทึกผลประเมินภาวะโภชนาการเรียบร้อยแล้ว');
}

public function edit(
    Client $client,
    NutritionAssessment $assessment
)
{
    $client = Client::forUser(auth()->user())
        ->findOrFail($client->id);

    if ($assessment->client_id !== $client->id) {
        abort(403);
    }

    return view(
        'frontend.nutrition_assessments.edit',
        compact('client', 'assessment')
    );
}

public function update(Request $request, Client $client, NutritionAssessment $assessment)
{
    $client = Client::forUser(auth()->user())
        ->findOrFail($client->id);

    abort_if($assessment->client_id !== $client->id, 403);

    $validated = $request->validate([
        'assessment_date' => [
            'required',
            'date',
            Rule::unique('nutrition_assessments', 'assessment_date')
                ->where(fn ($query) => $query->where('client_id', $client->id))
                ->ignore($assessment->id),
        ],
        'height_cm' => 'required|numeric|min:30|max:250',
        'weight_kg' => 'required|numeric|min:1|max:300',
        'note' => 'nullable|string',
    ], [
        'assessment_date.unique' => 'เด็กคนนี้มีผลประเมินในวันที่เลือกแล้ว กรุณาเลือกวันอื่น หรือแก้ไขรายการเดิม',
        'assessment_date.before_or_equal' => 'ไม่สามารถบันทึกวันที่ในอนาคตได้',
    ]);

    $birthDate = $client->birth_date
        ? Carbon::parse($client->birth_date)
        : null;

    $assessmentDate = Carbon::parse($validated['assessment_date']);

    $ageYear = null;
    $ageMonth = null;

    if ($birthDate) {
        $age = $birthDate->diff($assessmentDate);
        $ageYear = $age->y;
        $ageMonth = $age->m;
    }

    $heightCm = (float) $validated['height_cm'];
    $weightKg = (float) $validated['weight_kg'];

    $heightMeter = $heightCm / 100;

    $bmi = $heightMeter > 0
        ? round($weightKg / ($heightMeter * $heightMeter), 2)
        : null;

    $bmiResult = null;

    if ($bmi !== null) {
        if ($bmi < 18.5) {
            $bmiResult = 'น้ำหนักน้อย';
        } elseif ($bmi < 23) {
            $bmiResult = 'สมส่วน';
        } elseif ($bmi < 25) {
            $bmiResult = 'เริ่มอ้วน';
        } elseif ($bmi < 30) {
            $bmiResult = 'อ้วน';
        } else {
            $bmiResult = 'อ้วนมาก';
        }
    }

    $assessment->update([
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
        'updated_by'       => auth()->id(),
    ]);

    return redirect()
        ->route('nutrition_assessments.show', [$client->id, $assessment->id])
        ->with('success', 'แก้ไขผลประเมินภาวะโภชนาการเรียบร้อยแล้ว');
}

public function show(Client $client, NutritionAssessment $assessment)
{
    $client = Client::forUser(auth()->user())
        ->findOrFail($client->id);

    abort_if($assessment->client_id !== $client->id, 403);

    $growthRecords = NutritionAssessment::query()
        ->where('client_id', $client->id)
        ->whereNotNull('assessment_date')
        ->orderBy('assessment_date')
        ->select([
            'id',
            'assessment_date',
            'height_cm',
            'weight_kg',
            'bmi',
        ])
        ->get();

    return view('frontend.nutrition_assessments.show', [
        'client'        => $client,
        'assessment'    => $assessment,
        'growthRecords' => $growthRecords,
    ]);
}
public function destroy(Client $client, NutritionAssessment $assessment)
{
    $client = Client::forUser(auth()->user())
        ->findOrFail($client->id);

    if ($assessment->client_id !== $client->id) {
        abort(403);
    }

    if (auth()->user()->role !== 'admin') {
        abort(403, 'คุณไม่มีสิทธิ์ลบข้อมูลนี้');
    }

    $assessment->delete();

   return redirect()
    ->route('nutrition_assessments.index', $client->id)
    ->with('success', 'ลบผลประเมินภาวะโภชนาการเรียบร้อยแล้ว');
}
}