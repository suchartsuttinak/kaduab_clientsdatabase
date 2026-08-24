<?php

namespace App\Services\IndividualDevelopment;

use App\Models\IndividualDevelopment\DevelopmentPlan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class IndividualDevelopmentTimelineService
{
    public function forClient(int $clientId): Collection
    {
        $items = collect();

        $this->appendDevelopmentEvents($items, $clientId);

        // ข้อมูลจากโมดูลเดิม: อ่านอย่างเดียวและตรวจ schema ก่อนทุกครั้ง
        $this->appendTableEvents($items, $clientId, 'education_records', 'record_date', 'education', 'การศึกษา', ['grade_average'], 'บันทึกข้อมูลการศึกษา');
        $this->appendTableEvents($items, $clientId, 'medicals', 'medical_date', 'health', 'สุขภาพ', ['disease_name','diagnosis','treatment','remark'], 'บันทึกการรักษาพยาบาล');
        $this->appendTableEvents($items, $clientId, 'check_bodies', 'assessor_date', 'health', 'สุขภาพ', ['health','development','detail','remark'], 'ตรวจสุขภาพเบื้องต้น');
        $this->appendTableEvents($items, $clientId, 'accidents', 'incident_date', 'health', 'อุบัติเหตุ', ['detail','cause','diagnosis','treatment'], 'บันทึกอุบัติเหตุ');
        $this->appendTableEvents($items, $clientId, 'school_followups', 'follow_date', 'education', 'การศึกษา', ['follow_type','result','remark'], 'ติดตามสถานศึกษา');
        $this->appendTableEvents($items, $clientId, 'followups', 'followup_date', 'social', 'สังคมสงเคราะห์', ['assistance_detail','note'], 'ติดตามการช่วยเหลือ');
        $this->appendTableEvents($items, $clientId, 'counselings', 'session_date', 'mental', 'การให้คำปรึกษา', ['presenting_problem','outcome','next_steps'], 'ให้คำปรึกษา');
        $this->appendTableEvents($items, $clientId, 'observes', 'date', 'behavior', 'พฤติกรรม', ['behavior','result','action','obstacles'], 'บันทึกพฤติกรรม');
        $this->appendTableEvents($items, $clientId, 'psychiatrics', 'sent_date', 'mental', 'จิตใจ/จิตเวช', ['diagnose','drug_name'], 'ส่ง/ติดตามการรักษาจิตเวช');
        $this->appendTableEvents($items, $clientId, 'visit_families', 'visit_date', 'family', 'ครอบครัว', ['problem','need','assistance','comment'], 'เยี่ยมครอบครัว');
        $this->appendTableEvents($items, $clientId, 'refers', 'refer_date', 'social', 'ส่งต่อ', ['destination','committee_result','remark'], 'ส่งต่อ/ประสานหน่วยงาน');
        $this->appendTableEvents($items, $clientId, 'individual_development_coordinations', 'coordination_date', 'social', 'ประสานหน่วยงาน', ['agency_name','subject','result','next_appointment_date','document_note','status'], 'ประสานหน่วยงาน');

        return $items
            ->filter(fn (array $item) => !empty($item['date']))
            ->sortByDesc(fn (array $item) => (string) $item['date'])
            ->values();
    }

    private function appendDevelopmentEvents(Collection $items, int $clientId): void
    {
        $plans = DevelopmentPlan::query()
            ->where('client_id', $clientId)
            ->with(['goals.activities', 'followups', 'assessments'])
            ->orderBy('plan_no')
            ->get();

        foreach ($plans as $plan) {
            $items->push($this->item($plan->start_date, 'development', 'แผนพัฒนา', 'เริ่มแผนพัฒนารายบุคคล', 'แผนครั้งที่ ' . $plan->plan_no));

            foreach ($plan->assessments as $assessment) {
                $type = match ($assessment->assessment_type) {
                    'baseline' => 'Baseline',
                    'review' => 'ประเมินทบทวน',
                    'final' => 'ประเมินก่อนปิดแผน',
                    'post_discharge' => 'ประเมินหลังจำหน่าย',
                    default => 'ประเมินพัฒนาการ',
                };
                $items->push($this->item($assessment->assessment_date, 'development', 'การประเมิน', $type, 'รอบที่ ' . $assessment->round_no));
            }

            foreach ($plan->goals as $goal) {
                $items->push($this->item($goal->created_at, 'development', 'เป้าหมาย', 'กำหนดเป้าหมาย', $goal->title));
                foreach ($goal->activities as $activity) {
                    $detail = $this->joinDetails([$activity->detail, $activity->result, $activity->next_action]);
                    $items->push($this->item($activity->activity_date, 'development', 'กิจกรรม', 'กิจกรรมพัฒนา', $detail ?: $activity->activity_type));
                }
            }

            foreach ($plan->followups as $followup) {
                $detail = $this->joinDetails([$followup->current_situation, $followup->positive_changes, $followup->result, $followup->next_action]);
                $items->push($this->item($followup->followup_date, 'development', 'การติดตาม', 'ติดตามผล ครั้งที่ ' . $followup->followup_no, $detail));
            }

            if ($plan->closed_at) {
                $items->push($this->item($plan->closed_at, 'development', 'แผนพัฒนา', 'ปิดแผนพัฒนา', $plan->final_outcome));
            }
        }
    }

    private function appendTableEvents(
        Collection $items,
        int $clientId,
        string $table,
        string $dateColumn,
        string $group,
        string $category,
        array $detailColumns,
        string $title
    ): void {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'client_id') || !Schema::hasColumn($table, $dateColumn)) {
            return;
        }

        $columns = ['id', 'client_id', $dateColumn];
        foreach ($detailColumns as $column) {
            if (Schema::hasColumn($table, $column)) $columns[] = $column;
        }
        if (Schema::hasColumn($table, 'deleted_at')) $columns[] = 'deleted_at';

        $query = DB::table($table)->select(array_values(array_unique($columns)))->where('client_id', $clientId);
        if (Schema::hasColumn($table, 'deleted_at')) $query->whereNull('deleted_at');

        foreach ($query->orderBy($dateColumn)->orderBy('id')->get() as $row) {
            $detail = [];
            foreach ($detailColumns as $column) {
                if (property_exists($row, $column) && filled($row->{$column})) {
                    $detail[] = trim((string) $row->{$column});
                }
            }
            $items->push($this->item(
                $row->{$dateColumn} ?? null,
                $group,
                $category,
                $title,
                $this->joinDetails($detail),
                $table,
                (int) ($row->id ?? 0)
            ));
        }
    }

    private function item($date, string $group, string $category, string $title, ?string $detail, ?string $source = null, ?int $sourceId = null): array
    {
        return [
            'date' => $date,
            'group' => $group,
            'category' => $category,
            'title' => $title,
            'detail' => $detail ? Str::limit(trim($detail), 1000) : null,
            'source' => $source,
            'source_id' => $sourceId,
        ];
    }

    private function joinDetails(array $parts): ?string
    {
        $parts = collect($parts)
            ->map(fn ($value) => trim((string) ($value ?? '')))
            ->filter()
            ->unique()
            ->values();

        return $parts->isEmpty() ? null : $parts->implode(' • ');
    }
}
