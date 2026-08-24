@extends('admin_client.admin_client')
@section('content')
@php
    $isEdit = ($mode ?? 'create') === 'edit' && $assessment;
    $action = $isEdit
        ? route('individual-development.outcomes.update', [$client->id, $assessment->id])
        : route('individual-development.outcomes.store', $client->id);
    $sourceValues = old('information_sources', $assessment?->information_sources ?? []);
@endphp
<style>
.idpof{max-width:1450px;margin:auto}.idpof-card{background:#fff;border:1px solid #dfe7f1;border-radius:16px;padding:18px}.idpof-domain{border:1px solid #e3eaf2;border-radius:14px;overflow:hidden;margin-bottom:14px}.idpof-domain h6{margin:0;padding:11px 14px;background:#f7faff}.idpof-item{padding:12px 14px;border-top:1px solid #edf1f5}.idpof-item:first-of-type{border-top:0}.idpof .form-control,.idpof .form-select{border-radius:10px;border-color:#d9e2ec}.idpof .score-help{font-size:.74rem;color:#64748b;line-height:1.5}@media(max-width:768px){.idpof-card{padding:12px}}
</style>
<div class="container-fluid py-3 idpof">
<form data-idp-th-validation="1" method="POST" action="{{ $action }}" id="outcomeForm">@csrf @if($isEdit) @method('PATCH') @endif
<input type="hidden" name="plan_id" value="{{ $plan->id }}"><input type="hidden" name="assessment_type" value="{{ $type }}">
<div class="idpof-card mb-3"><div class="d-flex justify-content-between gap-2 flex-wrap"><div><h4 class="fw-bold mb-1">{{ $isEdit ? 'แก้ไข: ' : '' }}{{ $typeLabels[$type] ?? 'ประเมินผลลัพธ์' }}</h4><div class="text-muted small">ประเมินตามตัวชี้วัดเดิมของแผน เพื่อเปรียบเทียบกับ Baseline อย่างต่อเนื่อง • คะแนนทุกข้อควรมีหลักฐาน/เหตุผลที่ตรวจสอบย้อนหลังได้</div></div><a class="btn btn-outline-secondary" href="{{ route('individual-development.outcomes.index',['client'=>$client->id,'plan'=>$plan->id]) }}">กลับ</a></div>
@if($errors->any())<div class="alert alert-danger mt-3 mb-0"><div class="fw-bold">กรุณาตรวจสอบข้อมูล</div><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="row g-3 mt-1"><div class="col-md-4"><label class="form-label fw-semibold">วันที่ประเมิน <span class="text-danger">*</span></label><input type="date" class="form-control" name="assessment_date" value="{{ old('assessment_date', optional($assessment?->assessment_date)->format('Y-m-d') ?? $today) }}" min="{{ $minimumDate }}" max="{{ $today }}" required></div><div class="col-md-8"><label class="form-label fw-semibold">แหล่งข้อมูล <span class="text-danger">*</span></label><div class="d-flex gap-3 flex-wrap pt-2">@foreach($informationSourceOptions as $value=>$label)<label><input type="checkbox" name="information_sources[]" value="{{ $value }}" @checked(in_array($value,$sourceValues,true))> {{ $label }}</label>@endforeach</div></div></div></div>
<div class="idpof-card mb-3"><h5 class="fw-bold mb-1">ประเมินรายด้าน</h5><div class="score-help mb-3">1 = ต้องส่งเสริมเร่งด่วน • 2 = ควรส่งเสริม • 3 = ตามเกณฑ์ • 4 = ดี • 5 = ดีมาก</div>@foreach($domains as $domain)<div class="idpof-domain"><h6 class="fw-bold">{{ $domain->name }}</h6>@foreach($domain->indicators as $indicator)@php($saved=$assessmentItems->get($indicator->id))<div class="idpof-item"><div class="fw-semibold mb-2">{{ $indicator->name }}</div><div class="row g-2"><div class="col-md-2"><select class="form-select" name="items[{{ $indicator->id }}][score]" required><option value="">ระดับ 1–5</option>@for($i=1;$i<=5;$i++)<option value="{{ $i }}" @selected((string)old('items.'.$indicator->id.'.score',$saved?->score)===(string)$i)>{{ $i }}</option>@endfor</select></div><div class="col-md-5"><input class="form-control" name="items[{{ $indicator->id }}][evidence]" required value="{{ old('items.'.$indicator->id.'.evidence',$saved?->evidence) }}" placeholder="หลักฐาน/เหตุผลประกอบ *"></div><div class="col-md-5"><input class="form-control" name="items[{{ $indicator->id }}][development_note]" value="{{ old('items.'.$indicator->id.'.development_note',$saved?->development_note) }}" placeholder="สิ่งที่เปลี่ยนแปลง/ควรพัฒนาต่อ"></div></div></div>@endforeach</div>@endforeach</div>
<div class="idpof-card"><div class="row g-3"><div class="col-md-6"><label class="form-label fw-semibold">ความเห็นของผู้รับบริการ/ผู้มีส่วนร่วม</label><textarea class="form-control" name="participant_note" rows="4">{{ old('participant_note',$assessment?->participant_note) }}</textarea></div><div class="col-md-6"><label class="form-label fw-semibold">สรุปผลโดยผู้ประเมิน</label><textarea class="form-control" name="overall_note" rows="4">{{ old('overall_note',$assessment?->overall_note) }}</textarea></div></div><div class="text-end mt-3"><button class="btn btn-primary px-4" id="saveOutcomeButton"><i class="bi bi-save me-1"></i>{{ $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกผลประเมิน' }}</button></div></div>
</form></div>
<script>
document.addEventListener('DOMContentLoaded',function(){
 const f=document.getElementById('outcomeForm'),b=document.getElementById('saveOutcomeButton'); if(!f||!b)return;
 f.addEventListener('submit',function(e){
   if(!f.checkValidity()){return;}
   const sources=f.querySelectorAll('input[name="information_sources[]"]:checked');
   if(sources.length===0){
     e.preventDefault();
     const firstSource=f.querySelector('input[name="information_sources[]"]');
     if(firstSource){firstSource.focus();firstSource.scrollIntoView({behavior:'smooth',block:'center'});}
     if(window.Swal){Swal.fire({icon:'warning',title:'กรุณาเลือกแหล่งข้อมูล',text:'เลือกแหล่งข้อมูลหรือผู้ร่วมให้ข้อมูลอย่างน้อย 1 รายการ',confirmButtonText:'OK'});}else{alert('กรุณาเลือกแหล่งข้อมูลอย่างน้อย 1 รายการ');}
     return;
   }
   if(f.dataset.confirmed==='1'){b.disabled=true;b.innerHTML='<span class="spinner-border spinner-border-sm me-1"></span>กำลังบันทึก...';return;}
   if(!window.Swal){b.disabled=true;return;}
   e.preventDefault();
   Swal.fire({icon:'question',title:'ยืนยันบันทึกผลประเมิน?',text:'กรุณาตรวจสอบคะแนน หลักฐาน และแหล่งข้อมูลให้ถูกต้องก่อนบันทึก',showCancelButton:true,confirmButtonText:'บันทึกผลประเมิน',cancelButtonText:'กลับไปตรวจ',reverseButtons:true}).then(function(r){if(!r.isConfirmed)return;f.dataset.confirmed='1';b.disabled=true;b.innerHTML='<span class="spinner-border spinner-border-sm me-1"></span>กำลังบันทึก...';f.requestSubmit();});
 });
});
</script>
@include('frontend.client.individual_development.partials._thai_validation')
@endsection
