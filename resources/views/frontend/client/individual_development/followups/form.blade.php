@extends('admin_client.admin_client')

@section('content')
@php
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $isEdit = $mode === 'edit';
    $thaiDate = static function ($value): string {
        if (!$value) return '-';
        try { $d = $value instanceof \Carbon\CarbonInterface ? $value : \Carbon\Carbon::parse($value); return $d->format('d/m/') . ($d->year + 543); } catch (\Throwable $e) { return '-'; }
    };
    $domainIcons = ['physical'=>'bi-heart-pulse','emotional'=>'bi-emoji-smile','social'=>'bi-people','intellectual'=>'bi-lightbulb'];
    $existingItems = $followup?->items?->keyBy('indicator_id') ?? collect();
    $selectedType = old('followup_type', $followup?->followup_type ?? 'routine');
    $selectedOverall = old('overall_result', $followup?->overall_result ?? 'stable');
@endphp
<style>
.idpf{--bd:#e3eaf2;--txt:#203249;--muted:#6d7d91;--blue:#2869ad;padding-bottom:1.5rem}.idpf .head,.idpf .card{background:#fff;border:1px solid var(--bd);border-radius:16px;box-shadow:0 6px 20px rgba(30,50,75,.045)}.idpf .head{padding:1rem 1.15rem;margin-bottom:1rem;display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap}.idpf h4{font-size:1.12rem;font-weight:800;margin:0;color:var(--txt)}.idpf .sub{font-size:.84rem;color:var(--muted);margin-top:.28rem}.idpf .actions{display:flex;gap:.5rem;flex-wrap:wrap}.idpf .btnx{display:inline-flex;align-items:center;justify-content:center;gap:.38rem;min-height:40px;padding:.5rem .85rem;border-radius:10px;font-size:.82rem;font-weight:800;text-decoration:none;border:1px solid #d4dee9;background:#fff;color:#4b6076}.idpf .btnx.primary{border:0;background:linear-gradient(135deg,#3479bf,#245f9f);color:#fff;box-shadow:0 5px 14px rgba(36,95,159,.18)}.idpf .card{overflow:hidden;margin-bottom:1rem}.idpf .card-head{padding:.9rem 1rem;background:#fbfcfe;border-bottom:1px solid var(--bd);display:flex;justify-content:space-between;gap:.75rem;align-items:center;flex-wrap:wrap}.idpf .card-title{font-size:.94rem;font-weight:800;color:var(--txt)}.idpf .card-body{padding:1rem}.idpf label.form-label{font-size:.79rem;font-weight:800;color:#40556d}.idpf .form-control,.idpf .form-select{border-radius:10px;border-color:#dbe4ee;min-height:42px}.idpf textarea.form-control{min-height:92px}.idpf .prevbox{border:1px solid #d5e6f7;background:#f7fbff;border-radius:14px;padding:.85rem 1rem;margin-bottom:1rem}.idpf .prevbox-title{font-size:.82rem;font-weight:800;color:#245f9f}.idpf .prevbox-text{font-size:.8rem;color:#556b82;white-space:pre-line;margin-top:.25rem}.idpf .domain-head{display:flex;justify-content:space-between;gap:.8rem;align-items:center;flex-wrap:wrap}.idpf .domain-title{display:flex;gap:.55rem;align-items:center;font-size:.95rem;font-weight:800}.idpf .domain-icon{display:inline-flex;width:34px;height:34px;border-radius:10px;background:#edf5ff;color:#2f70b4;align-items:center;justify-content:center}.idpf .avg{font-size:.78rem;color:#60758c;background:#f5f8fc;border:1px solid #e0e8f1;padding:.35rem .65rem;border-radius:999px}.idpf .indicator{border:1px solid #e5ebf2;border-radius:13px;padding:.85rem;margin-bottom:.7rem;background:#fff}.idpf .indicator-top{display:grid;grid-template-columns:minmax(220px,1fr) auto;gap:.8rem;align-items:start}.idpf .indicator-name{font-size:.84rem;font-weight:800;color:#2d425a}.idpf .indicator-desc{font-size:.75rem;color:#7a899a;margin-top:.2rem}.idpf .prev-score{display:inline-flex;align-items:center;gap:.28rem;padding:.3rem .55rem;border-radius:999px;background:#f1f4f8;color:#536578;font-size:.74rem;font-weight:800;white-space:nowrap}.idpf .score-row{display:flex;gap:.42rem;flex-wrap:wrap;margin-top:.7rem}.idpf .score-choice input{position:absolute;opacity:0;pointer-events:none}.idpf .score-choice label{width:38px;height:38px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #cfdbe7;border-radius:10px;background:#fff;color:#50647a;font-weight:900;cursor:pointer;transition:.15s}.idpf .score-choice input:checked+label{background:#2f72b5;border-color:#2f72b5;color:#fff;box-shadow:0 4px 10px rgba(47,114,181,.18)}.idpf .rubric{margin-top:.6rem;background:#f8fbff;border-left:3px solid #79a9d7;border-radius:8px;padding:.55rem .7rem;font-size:.76rem;color:#50667d;line-height:1.5}.idpf .rubric strong{color:#2d6196}.idpf .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:.65rem;margin-top:.7rem}.idpf .detail-grid textarea{min-height:70px;font-size:.78rem}.idpf .section-grid{display:grid;grid-template-columns:1fr 1fr;gap:.8rem}.idpf .sticky-actions{display:flex;justify-content:flex-end;gap:.6rem;flex-wrap:wrap;padding:1rem;background:#fff;border:1px solid var(--bd);border-radius:14px}.idpf .readonly{display:inline-flex;padding:.32rem .65rem;border-radius:999px;background:#f2f4f7;color:#687586;font-size:.75rem;font-weight:800}
@media(max-width:991.98px){.idpf .indicator-top{grid-template-columns:1fr}.idpf .section-grid{grid-template-columns:1fr}}@media(max-width:575.98px){.idpf .actions,.idpf .sticky-actions{display:grid;grid-template-columns:1fr;width:100%}.idpf .actions .btnx,.idpf .sticky-actions .btnx,.idpf .sticky-actions button{width:100%}.idpf .detail-grid{grid-template-columns:1fr}.idpf .score-row{justify-content:space-between}.idpf .score-choice{flex:1}.idpf .score-choice label{width:100%}}

/* IDP_PHASE6_SAFE_SCOPE */
.idpf{width:100%;min-width:0}
.idpf *{min-width:0}
.idpf .indicator-name,.idpf .indicator-desc,.idpf .rubric,.idpf textarea{overflow-wrap:anywhere;word-break:break-word}
</style>
<div class="container-fluid px-2 px-lg-3 idpf">
    <div class="head">
        <div>
            <h4><i class="bi bi-activity me-2 text-primary"></i>{{ $isEdit ? 'แก้ไขการติดตาม' : 'บันทึกการติดตาม' }} ครั้งที่ {{ $followupNo }}</h4>
            <div class="sub">ผู้รับบริการ: <strong>{{ $clientName }}</strong> • อายุ {{ $ageText }} • แผนครั้งที่ {{ $plan->plan_no }}</div>
        </div>
        <div class="actions">
            @if($readOnly)<span class="readonly"><i class="bi bi-eye me-1"></i>อ่านอย่างเดียว</span>@endif
            <a class="btnx" href="{{ route('individual-development.index',$client->id) }}"><i class="bi bi-arrow-left"></i>กลับหน้าหลัก</a>
        </div>
    </div>

    @if($previousFollowup?->next_action)
        <div class="prevbox">
            <div class="prevbox-title"><i class="bi bi-pin-angle me-1"></i>สิ่งที่กำหนดให้ดำเนินการจากครั้งก่อน</div>
            <div class="prevbox-text">{{ $previousFollowup->next_action }}</div>
            @if($previousFollowup->next_followup_date)<div class="small text-muted mt-2">กำหนดติดตามเดิม: {{ $thaiDate($previousFollowup->next_followup_date) }}</div>@endif
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-3"><div class="fw-bold mb-1">กรุณาตรวจสอบข้อมูล</div><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('individual-development.followups.update',[$client->id,$followup->id]) : route('individual-development.followups.store',$client->id) }}">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <div class="card">
            <div class="card-head"><div class="card-title">ข้อมูลการติดตาม</div></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3"><label class="form-label">วันที่ติดตาม <span class="text-danger">*</span></label><input type="date" name="followup_date" class="form-control" max="{{ now('Asia/Bangkok')->format('Y-m-d') }}" value="{{ old('followup_date', optional($followup?->followup_date)->format('Y-m-d') ?? now('Asia/Bangkok')->format('Y-m-d')) }}" {{ $readOnly?'disabled':'' }} required></div>
                    <div class="col-md-3"><label class="form-label">ประเภทการติดตาม</label><select name="followup_type" class="form-select" {{ $readOnly?'disabled':'' }}>@foreach($followupTypes as $key=>$label)<option value="{{ $key }}" @selected($selectedType===$key)>{{ $label }}</option>@endforeach</select></div>
                    <div class="col-md-3"><label class="form-label">ผู้ติดตาม</label><input type="text" name="follower_name" class="form-control" value="{{ old('follower_name',$followup?->follower_name ?? auth()->user()->name ?? '') }}" {{ $readOnly?'disabled':'' }}></div>
                    <div class="col-md-3"><label class="form-label">ผลโดยรวม <span class="text-danger">*</span></label><select name="overall_result" class="form-select" {{ $readOnly?'disabled':'' }} required>@foreach($resultLabels as $key=>$label)<option value="{{ $key }}" @selected($selectedOverall===$key)>{{ $label }}</option>@endforeach</select></div>
                </div>
                <div class="section-grid mt-3">
                    <div><label class="form-label">สถานการณ์ปัจจุบัน</label><textarea name="current_situation" class="form-control" {{ $readOnly?'disabled':'' }}>{{ old('current_situation',$followup?->current_situation) }}</textarea></div>
                    <div><label class="form-label">การเปลี่ยนแปลงที่พบ</label><textarea name="changes" class="form-control" {{ $readOnly?'disabled':'' }}>{{ old('changes',$followup?->changes) }}</textarea></div>
                    <div><label class="form-label">สิ่งที่ดีขึ้น</label><textarea name="positive_changes" class="form-control" {{ $readOnly?'disabled':'' }}>{{ old('positive_changes',$followup?->positive_changes) }}</textarea></div>
                    <div><label class="form-label">การช่วยเหลือ/กิจกรรมที่ดำเนินการ</label><textarea name="actions_taken" class="form-control" {{ $readOnly?'disabled':'' }}>{{ old('actions_taken',$followup?->actions_taken) }}</textarea></div>
                    <div><label class="form-label">ผลที่เกิดขึ้น</label><textarea name="result" class="form-control" {{ $readOnly?'disabled':'' }}>{{ old('result',$followup?->result) }}</textarea></div>
                    <div><label class="form-label">ปัญหา/อุปสรรค</label><textarea name="problem" class="form-control" {{ $readOnly?'disabled':'' }}>{{ old('problem',$followup?->problem) }}</textarea></div>
                </div>
            </div>
        </div>

        @foreach($domains as $domain)
            @php
                $prevVals = $domain->indicators->map(fn($i)=>(float)($previousScores[$i->id] ?? 0))->filter(fn($v)=>$v>0);
                $curVals = $domain->indicators->map(fn($i)=>(float)old('items.'.$i->id.'.score',$currentScores[$i->id] ?? $previousScores[$i->id] ?? 0))->filter(fn($v)=>$v>0);
                $prevAvg = $prevVals->isNotEmpty() ? $prevVals->avg() : null;
                $curAvg = $curVals->isNotEmpty() ? $curVals->avg() : null;
            @endphp
            <div class="card domain-card" data-domain="{{ $domain->id }}">
                <div class="card-head domain-head">
                    <div class="domain-title"><span class="domain-icon"><i class="bi {{ $domainIcons[$domain->code] ?? 'bi-clipboard-data' }}"></i></span>{{ $domain->name }}</div>
                    <div class="avg">ครั้งก่อน <strong class="prev-avg">{{ $prevAvg!==null?number_format($prevAvg,2):'-' }}</strong> → ครั้งนี้ <strong class="cur-avg">{{ $curAvg!==null?number_format($curAvg,2):'-' }}</strong></div>
                </div>
                <div class="card-body">
                    @foreach($domain->indicators as $indicator)
                        @php
                            $id=(int)$indicator->id;
                            $prev=$previousScores[$id] ?? null;
                            $current=(int)old('items.'.$id.'.score',$currentScores[$id] ?? $prev ?? 3);
                            $oldItem=$existingItems->get($id);
                            $rubricMap=$indicator->rubrics->mapWithKeys(fn($r)=>[(string)$r->level=>['title'=>$r->title,'description'=>$r->description]])->all();
                        @endphp
                        <div class="indicator" data-indicator="{{ $id }}" data-rubrics='@json($rubricMap)'>
                            <div class="indicator-top">
                                <div><div class="indicator-name">{{ $indicator->code }} • {{ $indicator->name }}</div><div class="indicator-desc">{{ $indicator->description }}</div></div>
                                <span class="prev-score">ครั้งก่อน {{ $prev ?? '-' }}</span>
                            </div>
                            <div class="score-row">
                                @for($level=1;$level<=5;$level++)
                                    <span class="score-choice"><input type="radio" id="score_{{ $id }}_{{ $level }}" name="items[{{ $id }}][score]" value="{{ $level }}" @checked($current===$level) {{ $readOnly?'disabled':'' }}><label for="score_{{ $id }}_{{ $level }}">{{ $level }}</label></span>
                                @endfor
                            </div>
                            <div class="rubric"><strong>เกณฑ์ระดับ <span class="rubric-level">{{ $current }}</span>:</strong> <span class="rubric-text">{{ data_get($rubricMap,(string)$current.'.description','-') }}</span></div>
                            <div class="detail-grid">
                                <div><label class="form-label">หลักฐาน/พฤติกรรมที่พบ</label><textarea name="items[{{ $id }}][evidence]" class="form-control" {{ $readOnly?'disabled':'' }}>{{ old('items.'.$id.'.evidence',$oldItem?->evidence) }}</textarea></div>
                                <div><label class="form-label">ข้อสังเกตพัฒนาการ</label><textarea name="items[{{ $id }}][development_note]" class="form-control" {{ $readOnly?'disabled':'' }}>{{ old('items.'.$id.'.development_note',$oldItem?->development_note) }}</textarea></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="card">
            <div class="card-head"><div class="card-title">เสียงสะท้อนและแผนดำเนินงานต่อ</div></div>
            <div class="card-body">
                <div class="section-grid">
                    <div><label class="form-label">ความคิดเห็นของผู้รับบริการ</label><textarea name="client_feedback" class="form-control" {{ $readOnly?'disabled':'' }}>{{ old('client_feedback',$followup?->client_feedback) }}</textarea></div>
                    <div><label class="form-label">ความคิดเห็นของผู้ดูแล/ครอบครัว</label><textarea name="caregiver_feedback" class="form-control" {{ $readOnly?'disabled':'' }}>{{ old('caregiver_feedback',$followup?->caregiver_feedback) }}</textarea></div>
                    <div><label class="form-label">ข้อเสนอแนะ</label><textarea name="suggestion" class="form-control" {{ $readOnly?'disabled':'' }}>{{ old('suggestion',$followup?->suggestion) }}</textarea></div>
                    <div><label class="form-label">สิ่งที่ต้องดำเนินการต่อ <span class="text-danger">*</span></label><textarea name="next_action" class="form-control" {{ $readOnly?'disabled':'' }} required>{{ old('next_action',$followup?->next_action) }}</textarea></div>
                </div>
                <div class="row g-3 mt-1"><div class="col-md-4"><label class="form-label">วันที่ติดตามครั้งถัดไป</label><input type="date" name="next_followup_date" class="form-control" value="{{ old('next_followup_date',optional($followup?->next_followup_date)->format('Y-m-d')) }}" {{ $readOnly?'disabled':'' }}></div></div>
            </div>
        </div>

        <div class="sticky-actions">
            <a href="{{ route('individual-development.index',$client->id) }}" class="btnx"><i class="bi bi-x-circle"></i>ปิด/กลับ</a>
            @unless($readOnly)<button type="submit" class="btnx primary"><i class="bi bi-check2-circle"></i>{{ $isEdit?'บันทึกการแก้ไข':'บันทึกการติดตามครั้งที่ '.$followupNo }}</button>@endunless
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
  function updateIndicator(box){var checked=box.querySelector('input[type=radio]:checked');if(!checked)return;var level=checked.value;var rubrics={};try{rubrics=JSON.parse(box.dataset.rubrics||'{}')}catch(e){}var data=rubrics[level]||{};var l=box.querySelector('.rubric-level'),t=box.querySelector('.rubric-text');if(l)l.textContent=level;if(t)t.textContent=data.description||'-';}
  function updateDomain(card){var values=[...card.querySelectorAll('.indicator input[type=radio]:checked')].map(x=>Number(x.value)).filter(Boolean);var avg=values.length?values.reduce((a,b)=>a+b,0)/values.length:null;var el=card.querySelector('.cur-avg');if(el)el.textContent=avg===null?'-':avg.toFixed(2);}
  document.querySelectorAll('.indicator').forEach(function(box){updateIndicator(box);box.querySelectorAll('input[type=radio]').forEach(function(r){r.addEventListener('change',function(){updateIndicator(box);updateDomain(box.closest('.domain-card'));});});});
  document.querySelectorAll('.domain-card').forEach(updateDomain);
});
</script>
@endsection
