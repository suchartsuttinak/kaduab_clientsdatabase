<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Models\ClientTransfer;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'register_number',
        'title_id',
        'nick_name',
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'id_card',
        'national_id',
        'religion_id',
        'marital_id',
        'occupation_id',
        'income_id',
        'education_id',
        'scholl',
        'address',
        'moo',
        'soi',
        'road',
        'village',
        'province_id',
        'district_id',
        'sub_district_id',
        'zipcode',
        'phone',
        'arrival_date',
        'target_id',
        'contact_id',
        'project_id',
        'house_id',
        'status_id',
        'case_resident',
        'image',
        'release_status',

        // ภูมิลำเนาเดิม
        'origin_address',
        'origin_moo',
        'origin_soi',
        'origin_road',
        'origin_village',
        'origin_province_id',
        'origin_district_id',
        'origin_sub_district_id',
        'origin_zipcode',
        'origin_phone',
    ];

    protected $casts = [
        'birth_date'   => 'date',
        'arrival_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function sub_district()
    {
        return $this->belongsTo(SubDistrict::class, 'sub_district_id');
    }

    // บ้านปัจจุบัน
    public function house()
    {
        return $this->belongsTo(House::class, 'house_id');
    }

    // ประวัติการย้ายบ้าน
    public function houseTransfers()
    {
        return $this->hasMany(ClientHouseTransfer::class, 'client_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function target()
    {
        return $this->belongsTo(Target::class, 'target_id');
    }

    public function education()
    {
        return $this->belongsTo(Education::class, 'education_id');
    }

    public function title()
    {
        return $this->belongsTo(Title::class, 'title_id');
    }

    public function national()
    {
        return $this->belongsTo(National::class, 'national_id');
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    public function marital()
    {
        return $this->belongsTo(Marital::class, 'marital_id');
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id');
    }

    public function income()
    {
        return $this->belongsTo(Income::class, 'income_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    // ภูมิลำเนาเดิม
    public function originProvince()
    {
        return $this->belongsTo(Province::class, 'origin_province_id');
    }

    public function originDistrict()
    {
        return $this->belongsTo(District::class, 'origin_district_id');
    }

    public function originSubDistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'origin_sub_district_id');
    }

    public function problems()
    {
        return $this->belongsToMany(Problem::class, 'client_problem', 'client_id', 'problem_id')
            ->withTimestamps();
    }

    public function vaccinations()
    {
        return $this->hasMany(Vaccination::class, 'client_id');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'client_id');
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class, 'client_id');
    }

    public function refers()
    {
        return $this->hasMany(Refer::class, 'client_id');
    }

    public function father()
    {
        return $this->hasOne(Father::class, 'client_id');
    }

    public function mother()
    {
        return $this->hasOne(Mother::class, 'client_id');
    }

    public function spouse()
    {
        return $this->hasOne(Spouse::class, 'client_id');
    }

    public function relative()
    {
        return $this->hasOne(Relative::class, 'client_id');
    }

    public function educationRecords()
    {
        return $this->hasMany(EducationRecord::class, 'client_id');
    }

    public function files()
    {
        return $this->hasMany(ClientFile::class, 'client_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAgeAttribute()
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : null;
    }

    public function getAdjustedTitleAttribute()
    {
        $age = $this->age;
        $title = $this->title->title_name ?? $this->title->name ?? '';

        if ($age >= 15) {
            if (in_array($title, ['ด.ช.', 'เด็กชาย'])) {
                $title = 'นาย';
            } elseif (in_array($title, ['ด.ญ.', 'เด็กหญิง'])) {
                $title = 'นางสาว';
            }
        }

        return $title;
    }

    public function getResidenceDurationAttribute()
    {
        if (!$this->arrival_date) {
            return null;
        }

        try {
            $start = Carbon::parse($this->arrival_date);
            $now = now();
            $diff = $start->diff($now);

            return "{$diff->y} ปี {$diff->m} เดือน";
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getFullNameAttribute()
    {
        $title = $this->adjusted_title ?: ($this->title->title_name ?? $this->title->name ?? '');

        return trim($title . ' ' . $this->first_name . ' ' . $this->last_name);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

  public function scopeForUser(Builder $query, $user): Builder
{
    if (!$user) {
        return $query->whereRaw('1 = 0');
    }

    // admin / executive เห็นทั้งหมด
    if (
        (method_exists($user, 'isAdmin') && $user->isAdmin()) ||
        (method_exists($user, 'isExecutive') && $user->isExecutive()) ||
        in_array(($user->role ?? null), ['admin', 'executive'], true)
    ) {
        return $query;
    }

    if (method_exists($user, 'loadMissing')) {
        $user->loadMissing('houses');
    }

    // มีสิทธิ์ตามบ้าน
    if (isset($user->houses) && $user->houses && $user->houses->isNotEmpty()) {

        $houseIds = $user->houses
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();

        return $query->whereIn('house_id', $houseIds);
    }

    // fallback บ้านเดี่ยว
    if (!empty($user->house_id)) {
        return $query->where('house_id', (int) $user->house_id);
    }

    // กรณีมี project_id แต่ไม่มีบ้าน
    if (!empty($user->project_id)) {
        return $query->where('project_id', (int) $user->project_id);
    }

    // ไม่มีสิทธิ์ใดเลย
    return $query->whereRaw('1 = 0');
}

        // ความสัมพันธ์กับ Followup (ติดตามผล)
    public function followups()
{
    return $this->hasMany(\App\Models\Followup::class, 'client_id');
}

public function healthcHeckups()
{
    return $this->hasMany(\App\Models\HealthcHeckup::class, 'client_id');
}

public function transfers()
{
    return $this->hasMany(ClientTransfer::class);
}

  // ความสัมพันธ์กับ BehaviorScreening (คัดกรองพฤติกรรม)
public function behaviorScreenings()
{
    return $this->hasMany(\App\Models\BehaviorScreening::class);
}


  // ความสัมพันธ์กับ NutritionAssessment (ประเมินโภชนาการ)
public function nutritionAssessments()
{
    return $this->hasMany(NutritionAssessment::class);
}


public function idstations()
{
    return $this->hasMany(Idstation::class);
}

/**
 * ประวัติการให้คำปรึกษาของผู้รับบริการ
 */
public function counselings(): HasMany
{
    return $this->hasMany(Counseling::class)
        ->orderByDesc('session_date')
        ->orderByDesc('session_no');
}
}