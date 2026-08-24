@php
    $networkTypeLabels = [
        'parent'=>'ผู้ปกครอง','relative'=>'ญาติ','teacher'=>'ครู','home_staff'=>'บุคลากรบ้านพัก',
        'social_worker'=>'นักสังคมสงเคราะห์','agency'=>'หน่วยงานภายนอก','other'=>'อื่น ๆ',
    ];
    $supportLevelLabels = ['high'=>'มาก','medium'=>'ปานกลาง','low'=>'น้อย'];
    $coordStatusLabels = ['open'=>'กำลังดำเนินการ','waiting'=>'รอผล/รอตอบกลับ','completed'=>'เสร็จสิ้น','cancelled'=>'ยุติ'];
    $docStatusLabels = ['missing'=>'ยังไม่มี','in_progress'=>'อยู่ระหว่างดำเนินการ','available'=>'มีแล้ว','expired'=>'หมดอายุ','not_applicable'=>'ไม่เกี่ยวข้อง'];
    $docStatusClass = ['missing'=>'danger','in_progress'=>'warning','available'=>'success','expired'=>'danger','not_applicable'=>'secondary'];
    $networkRows = collect($currentPlan?->support_network_profile ?? [])->filter(fn($r)=>is_array($r));
    $discharge = $currentPlan?->discharge_plan_profile ?? [];
    $dischargeHasData = is_array($discharge) && collect($discharge)->filter(fn($v)=>filled($v))->isNotEmpty();
@endphp

<style>
/* V8: modal guard เฉพาะ Individual Development — ไม่กระทบ modal หน้าอื่น */
.idp-safe-modal{padding:0!important}
.idp-safe-modal .modal-dialog{height:calc(100dvh - 1rem);max-height:calc(100dvh - 1rem);margin:.5rem auto;display:flex;align-items:stretch}
.idp-safe-modal .modal-content{height:100%;max-height:100%;overflow:hidden;display:flex;flex-direction:column}
.idp-safe-modal .modal-content>form{height:100%;min-height:0;display:flex;flex-direction:column}
.idp-safe-modal .modal-header,.idp-safe-modal .modal-footer{flex:0 0 auto;background:#fff;z-index:2}
.idp-safe-modal .modal-body{flex:1 1 auto;min-height:0;overflow-y:auto!important;overscroll-behavior:contain;-webkit-overflow-scrolling:touch;padding-bottom:1.25rem}
.idp-safe-modal .modal-footer{border-top:1px solid #e5e7eb;box-shadow:0 -4px 12px rgba(15,23,42,.04)}
.idp-mini-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem}.idp-mini{border:1px solid #e5eaf0;border-radius:12px;padding:.75rem;background:#fbfdff}.idp-mini-title{font-size:.75rem;font-weight:800;color:#475569}.idp-mini-text{margin-top:.25rem;font-size:.79rem;color:#334155;white-space:pre-line;overflow-wrap:anywhere}.idp-source-note{font-size:.7rem;color:#64748b}.idp-doc-row{display:grid;grid-template-columns:minmax(150px,1.2fr) minmax(120px,.8fr) minmax(120px,.8fr) minmax(180px,1.4fr);gap:.5rem;align-items:center;padding:.55rem 0;border-bottom:1px dashed #e5eaf0}.idp-doc-row:last-child{border-bottom:0}
@media(max-width:767px){.idp-mini-grid{grid-template-columns:1fr}.idp-doc-row{grid-template-columns:1fr}.idp-safe-modal .modal-dialog{width:calc(100% - .5rem);height:calc(100dvh - .5rem);max-height:calc(100dvh - .5rem);margin:.25rem auto}.idp-safe-modal .modal-footer{display:grid;grid-template-columns:1fr 1fr}.idp-safe-modal .modal-footer .btn{width:100%}}
</style>

@if($currentPlan)
<div class="idp-box idp-card">
    <div class="idp-card-head">
        <div><h5 class="idp-card-title"><i class="bi bi-people me-2 text-primary"></i>เครือข่ายสนับสนุนและบุคคลสำคัญ</h5><div class="small text-muted mt-1">ระบุเฉพาะบุคคล/หน่วยงานที่มีบทบาทต่อแผน และเก็บข้อมูลติดต่อเท่าที่จำเป็น</div></div>
        @if($canUpdateSupplement)<button type="button" class="idp-btn idp-outline" data-bs-toggle="modal" data-bs-target="#idpSupportModal"><i class="bi bi-pencil-square"></i>จัดการเครือข่าย</button>@endif
    </div>
    <div class="idp-body">
        @if($networkRows->isNotEmpty())
            <div class="idp-mini-grid">
                @foreach($networkRows as $row)
                    <div class="idp-mini">
                        <div class="d-flex justify-content-between align-items-start gap-2"><div class="fw-bold small">{{ data_get($row,'name') ?: data_get($row,'organization') ?: 'เครือข่ายสนับสนุน' }}</div>@if(data_get($row,'support_level'))<span class="idp-priority">สนับสนุน {{ $supportLevelLabels[data_get($row,'support_level')] ?? data_get($row,'support_level') }}</span>@endif</div>
                        <div class="idp-source-note mt-1">{{ $networkTypeLabels[data_get($row,'type')] ?? 'อื่น ๆ' }}@if(data_get($row,'organization')) • {{ data_get($row,'organization') }}@endif</div>
                        @if(data_get($row,'role'))<div class="idp-mini-text"><strong>บทบาท:</strong> {{ data_get($row,'role') }}</div>@endif
                        @if(data_get($row,'contact_note'))<div class="idp-source-note mt-1"><strong>ข้อมูลติดต่อที่จำเป็น:</strong> {{ data_get($row,'contact_note') }}</div>@endif
                    </div>
                @endforeach
            </div>
        @elseif(filled($currentPlan->support_network_summary))
            <div class="idp-value">{{ $currentPlan->support_network_summary }}</div>
        @else
            <div class="text-muted small">ยังไม่ได้ระบุเครือข่ายสนับสนุน</div>
        @endif
        @if($networkRows->isNotEmpty() && filled($currentPlan->support_network_summary))<div class="idp-history-note mt-3"><strong>สรุป:</strong> {{ $currentPlan->support_network_summary }}</div>@endif
    </div>
</div>

<div class="idp-box idp-card">
    <div class="idp-card-head">
        <div><h5 class="idp-card-title"><i class="bi bi-house-check me-2 text-primary"></i>แผนก่อนจำหน่ายและติดตามหลังจำหน่าย</h5><div class="small text-muted mt-1">เตรียมความพร้อมด้านที่อยู่อาศัย การศึกษา อาชีพ การดำรงชีวิต ผู้ดูแล และการรับช่วงต่อ</div></div>
        @if($canUpdateSupplement)<button type="button" class="idp-btn idp-outline" data-bs-toggle="modal" data-bs-target="#idpDischargeModal"><i class="bi bi-pencil-square"></i>จัดทำแผน</button>@endif
    </div>
    <div class="idp-body">
        @if($dischargeHasData)
            <div class="idp-mini-grid">
                @foreach(['housing_readiness'=>'ที่อยู่อาศัย','education_readiness'=>'การศึกษา','career_readiness'=>'อาชีพ','income_living_readiness'=>'รายได้/การดำรงชีวิต','caregiver_after_discharge'=>'ผู้ดูแลหลังจำหน่าย','receiving_agency'=>'หน่วยงานรับช่วงต่อ'] as $key=>$label)
                    @if(filled(data_get($discharge,$key)))<div class="idp-mini"><div class="idp-mini-title">{{ $label }}</div><div class="idp-mini-text">{{ data_get($discharge,$key) }}</div></div>@endif
                @endforeach
            </div>
            <div class="row g-2 mt-2">
                @if(filled(data_get($discharge,'planned_discharge_date')))<div class="col-md-4"><div class="idp-mini"><div class="idp-mini-title">วันที่คาดว่าจะจำหน่าย</div><div class="idp-mini-text">{{ $thaiDate(data_get($discharge,'planned_discharge_date')) }}</div></div></div>@endif
                @foreach(['followup_1m'=>'แผนติดตาม 1 เดือน','followup_3m'=>'แผนติดตาม 3 เดือน','followup_6m'=>'แผนติดตาม 6 เดือน'] as $key=>$label)
                    @if(filled(data_get($discharge,$key)))<div class="col-md-4"><div class="idp-mini"><div class="idp-mini-title">{{ $label }}</div><div class="idp-mini-text">{{ data_get($discharge,$key) }}</div></div></div>@endif
                @endforeach
            </div>
            @if(filled(data_get($discharge,'readiness_summary')))<div class="idp-history-note mt-3"><strong>สรุปความพร้อม:</strong> {{ data_get($discharge,'readiness_summary') }}</div>@endif
        @else
            <div class="text-muted small">ยังไม่มีแผนก่อนจำหน่าย — สามารถจัดทำเมื่อเคสเริ่มมีแนวทางจำหน่าย/ส่งต่อที่ชัดเจน</div>
        @endif
        @if(!$isSelectedActive && $currentPlan->assessments->where('assessment_type','post_discharge')->isNotEmpty())<div class="idp-history-note mt-3"><i class="bi bi-check2-circle me-1"></i>มีผลประเมินหลังจำหน่ายแล้ว {{ $currentPlan->assessments->where('assessment_type','post_discharge')->count() }} ครั้ง</div>@endif
    </div>
</div>
@endif

<div class="idp-box idp-card">
    <div class="idp-card-head">
        <div><h5 class="idp-card-title"><i class="bi bi-arrow-left-right me-2 text-primary"></i>ประวัติการส่งต่อและประสานหน่วยงาน</h5><div class="small text-muted mt-1">รายการจำหน่าย/ส่งต่อเดิมถูกนำมาแสดงร่วมกับการประสานในแผนโดยไม่ต้องกรอกซ้ำ</div></div>
        @if($canCreateCoordination)<button type="button" class="idp-btn idp-primary" data-bs-toggle="modal" data-bs-target="#idpCoordinationModal"><i class="bi bi-plus-circle"></i>บันทึกการประสาน</button>@endif
    </div>
    <div class="idp-body">
        @if($coordinations->isEmpty() && $referrals->isEmpty())<div class="text-muted small">ยังไม่มีประวัติการส่งต่อหรือประสานหน่วยงาน</div>@else
            <div class="idp-table"><table><thead><tr><th>วันที่</th><th>แหล่งข้อมูล</th><th>หน่วยงาน/เรื่อง</th><th>ผล/สถานะ</th><th>นัดถัดไป</th><th></th></tr></thead><tbody>
            @foreach($coordinations as $row)
                <tr><td>{{ $thaiDate($row->coordination_date) }}</td><td><span class="badge bg-primary-subtle text-primary-emphasis">ประสาน</span></td><td><strong>{{ $row->agency_name }}</strong><div class="small text-muted">{{ $row->subject }}@if($row->coordinator_name) • ผู้ประสาน {{ $row->coordinator_name }}@endif</div></td><td>{{ \Illuminate\Support\Str::limit($row->result ?: '-',90) }}<div class="small text-muted">{{ $coordStatusLabels[$row->status] ?? $row->status }}</div></td><td>{{ $thaiDate($row->next_appointment_date) }}</td><td class="text-end"><div class="d-flex justify-content-end gap-1">@if($canUpdateCoordination)<button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#idpCoordEdit{{ $row->id }}">แก้ไข</button>@endif @if($canDeleteCoordination)<form method="POST" action="{{ route('individual-development.coordinations.destroy',[$client->id,$row->id]) }}" class="idp-coord-delete">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">ลบ</button></form>@endif</div></td></tr>
            @endforeach
            @foreach($referrals as $refer)
                <tr><td>{{ $thaiDate($refer->refer_date) }}</td><td><span class="badge bg-secondary-subtle text-secondary-emphasis">ข้อมูลเดิม</span></td><td><strong>{{ $refer->destination ?: ($refer->translate?->translate_name ?? 'จำหน่าย/ส่งต่อ') }}</strong><div class="small text-muted">{{ $refer->translate?->translate_name ?? '' }}</div></td><td>{{ \Illuminate\Support\Str::limit($refer->committee_result ?: $refer->remark ?: '-',90) }}<div class="small text-muted">{{ $refer->approve_status ?? '-' }}</div></td><td>-</td><td></td></tr>
            @endforeach
            </tbody></table></div>
        @endif
    </div>
</div>

<div class="idp-box idp-card">
    <div class="idp-card-head">
        <div><h5 class="idp-card-title"><i class="bi bi-folder-check me-2 text-primary"></i>เอกสารสำคัญและสถานะเอกสาร</h5><div class="small text-muted mt-1">ไม่เก็บไฟล์ซ้ำ — ตรวจว่ามีไฟล์เดิมในระบบหรือไม่ และเก็บเฉพาะสถานะ/วันหมดอายุ/หมายเหตุที่ใช้ติดตาม</div></div>
        @if($canUpdateDocuments)<button type="button" class="idp-btn idp-outline" data-bs-toggle="modal" data-bs-target="#idpDocumentsModal"><i class="bi bi-pencil-square"></i>ปรับสถานะ</button>@endif
    </div>
    <div class="idp-body">
        <div class="row g-2">
            @foreach($documentTypes as $type=>$label)
                @php($ds=$documentStatuses->get($type))
                @php($uploaded=in_array($type,$uploadedFileTypes,true))
                @php($effective=($ds?->expires_at && $ds->expires_at->lt(now('Asia/Bangkok')->startOfDay())) ? 'expired' : ($ds?->status ?: ($uploaded?'available':'missing')))
                <div class="col-md-6 col-xl-4"><div class="idp-mini h-100"><div class="d-flex justify-content-between gap-2"><div class="fw-bold small">{{ $label }}</div><span class="badge bg-{{ $docStatusClass[$effective] ?? 'secondary' }}-subtle text-{{ $docStatusClass[$effective] ?? 'secondary' }}-emphasis">{{ $docStatusLabels[$effective] ?? $effective }}</span></div><div class="idp-source-note mt-1">ไฟล์ในระบบ: {{ $uploaded ? 'มี' : 'ยังไม่พบ' }}@if($ds?->expires_at) • หมดอายุ {{ $thaiDate($ds->expires_at) }}@endif</div>@if($ds?->note)<div class="idp-mini-text">{{ $ds->note }}</div>@endif</div></div>
            @endforeach
        </div>
    </div>
</div>

@if($currentPlan && $canUpdateSupplement)
<div class="modal fade idp-safe-modal" id="idpSupportModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl"><div class="modal-content"><form method="POST" action="{{ route('individual-development.support-network.update',$client->id) }}">@csrf @method('PATCH')<div class="modal-header"><div><h5 class="modal-title fw-bold">เครือข่ายสนับสนุนและบุคคลสำคัญ</h5><div class="small text-muted">สูงสุด 10 รายการ • ข้อมูลติดต่อให้บันทึกเฉพาะที่จำเป็นต่อการปฏิบัติงาน</div></div><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body">
@for($i=0;$i<10;$i++) @php($row=data_get($currentPlan->support_network_profile,$i,[]))
<div class="border rounded-3 p-3 mb-3"><div class="fw-bold small mb-2">เครือข่ายลำดับที่ {{ $i+1 }}</div><div class="row g-2"><div class="col-md-4"><input class="form-control" name="support_network_profile[{{ $i }}][name]" value="{{ old('support_network_profile.'.$i.'.name',data_get($row,'name')) }}" placeholder="ชื่อบุคคล"></div><div class="col-md-4"><select class="form-select" name="support_network_profile[{{ $i }}][type]"><option value="">ประเภทความสัมพันธ์</option>@foreach($networkTypeLabels as $v=>$l)<option value="{{ $v }}" @selected(old('support_network_profile.'.$i.'.type',data_get($row,'type'))===$v)>{{ $l }}</option>@endforeach</select></div><div class="col-md-4"><input class="form-control" name="support_network_profile[{{ $i }}][organization]" value="{{ old('support_network_profile.'.$i.'.organization',data_get($row,'organization')) }}" placeholder="หน่วยงาน/สถานที่"></div><div class="col-md-4"><select class="form-select" name="support_network_profile[{{ $i }}][support_level]"><option value="">ระดับการสนับสนุน</option>@foreach($supportLevelLabels as $v=>$l)<option value="{{ $v }}" @selected(old('support_network_profile.'.$i.'.support_level',data_get($row,'support_level'))===$v)>{{ $l }}</option>@endforeach</select></div><div class="col-md-8"><input class="form-control" name="support_network_profile[{{ $i }}][role]" value="{{ old('support_network_profile.'.$i.'.role',data_get($row,'role')) }}" placeholder="บทบาท/สิ่งที่ช่วยเหลือ"></div><div class="col-12"><input class="form-control" name="support_network_profile[{{ $i }}][contact_note]" value="{{ old('support_network_profile.'.$i.'.contact_note',data_get($row,'contact_note')) }}" placeholder="ข้อมูลติดต่อที่จำเป็น (ถ้ามี)"></div></div></div>
@endfor
<label class="form-label fw-bold">สรุปเครือข่ายสนับสนุน</label><textarea class="form-control" rows="3" name="support_network_summary">{{ old('support_network_summary',$currentPlan->support_network_summary) }}</textarea>
</div><div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>บันทึก</button></div></form></div></div></div>

<div class="modal fade idp-safe-modal" id="idpDischargeModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl"><div class="modal-content"><form method="POST" action="{{ route('individual-development.discharge-plan.update',$client->id) }}">@csrf @method('PATCH')<div class="modal-header"><div><h5 class="modal-title fw-bold">แผนก่อนจำหน่ายและติดตามหลังจำหน่าย</h5><div class="small text-muted">จัดทำเมื่อแนวทางจำหน่าย/ส่งต่อเริ่มชัดเจน และใช้ Outcome หลังจำหน่ายบันทึกผลจริงภายหลัง</div></div><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="row g-3"><div class="col-md-4"><label class="form-label fw-bold">วันที่คาดว่าจะจำหน่าย</label><input type="date" class="form-control" name="planned_discharge_date" value="{{ old('planned_discharge_date',data_get($discharge,'planned_discharge_date')) }}"></div>@foreach(['housing_readiness'=>'ความพร้อมด้านที่อยู่อาศัย','education_readiness'=>'ความพร้อมด้านการศึกษา','career_readiness'=>'ความพร้อมด้านอาชีพ','income_living_readiness'=>'รายได้/การดำรงชีวิต','caregiver_after_discharge'=>'ผู้ดูแลหลังจำหน่าย','receiving_agency'=>'หน่วยงานรับช่วงต่อ'] as $key=>$label)<div class="col-md-6"><label class="form-label fw-bold">{{ $label }}</label><textarea class="form-control" rows="3" name="{{ $key }}">{{ old($key,data_get($discharge,$key)) }}</textarea></div>@endforeach @foreach(['followup_1m'=>'แผนติดตาม 1 เดือน','followup_3m'=>'แผนติดตาม 3 เดือน','followup_6m'=>'แผนติดตาม 6 เดือน'] as $key=>$label)<div class="col-md-4"><label class="form-label fw-bold">{{ $label }}</label><textarea class="form-control" rows="3" name="{{ $key }}">{{ old($key,data_get($discharge,$key)) }}</textarea></div>@endforeach<div class="col-12"><label class="form-label fw-bold">สรุปความพร้อม/ประเด็นที่ต้องจัดการก่อนจำหน่าย</label><textarea class="form-control" rows="4" name="readiness_summary">{{ old('readiness_summary',data_get($discharge,'readiness_summary')) }}</textarea></div></div></div><div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>บันทึกแผน</button></div></form></div></div></div>
@endif

@if($canUpdateCoordination)
@foreach($coordinations as $row)
<div class="modal fade idp-safe-modal" id="idpCoordEdit{{ $row->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><form method="POST" action="{{ route('individual-development.coordinations.update',[$client->id,$row->id]) }}">@csrf @method('PATCH')<div class="modal-header"><h5 class="modal-title fw-bold">แก้ไขผลการประสานหน่วยงาน</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="row g-3"><div class="col-md-4"><label class="form-label fw-bold">วันที่ประสาน <span class="text-danger">*</span></label><input type="date" class="form-control" name="coordination_date" max="{{ now('Asia/Bangkok')->format('Y-m-d') }}" value="{{ old('coordination_date',$row->coordination_date?->format('Y-m-d')) }}" required></div><div class="col-md-8"><label class="form-label fw-bold">หน่วยงาน <span class="text-danger">*</span></label><input class="form-control" name="agency_name" value="{{ old('agency_name',$row->agency_name) }}" required></div><div class="col-12"><label class="form-label fw-bold">เรื่องที่ประสาน <span class="text-danger">*</span></label><input class="form-control" name="subject" value="{{ old('subject',$row->subject) }}" required></div><div class="col-md-6"><label class="form-label fw-bold">ผู้ประสาน</label><input class="form-control" name="coordinator_name" value="{{ old('coordinator_name',$row->coordinator_name) }}"></div><div class="col-md-6"><label class="form-label fw-bold">สถานะเรื่อง</label><select class="form-select" name="status">@foreach($coordStatusLabels as $v=>$l)<option value="{{ $v }}" @selected(old('status',$row->status)===$v)>{{ $l }}</option>@endforeach</select></div><div class="col-12"><label class="form-label fw-bold">ผลการประสาน</label><textarea class="form-control" name="result" rows="4">{{ old('result',$row->result) }}</textarea></div><div class="col-md-5"><label class="form-label fw-bold">นัดหมายครั้งถัดไป</label><input type="date" class="form-control" name="next_appointment_date" value="{{ old('next_appointment_date',$row->next_appointment_date?->format('Y-m-d')) }}"></div><div class="col-md-7"><label class="form-label fw-bold">เอกสารประกอบ/หมายเหตุเอกสาร</label><input class="form-control" name="document_note" value="{{ old('document_note',$row->document_note) }}"></div></div></div><div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-primary" type="submit">บันทึกการแก้ไข</button></div></form></div></div></div>
@endforeach
@endif

@if($canCreateCoordination)
<div class="modal fade idp-safe-modal" id="idpCoordinationModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><form method="POST" action="{{ route('individual-development.coordinations.store',$client->id) }}">@csrf<div class="modal-header"><h5 class="modal-title fw-bold">บันทึกการประสานหน่วยงาน</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="row g-3"><div class="col-md-4"><label class="form-label fw-bold">วันที่ประสาน <span class="text-danger">*</span></label><input type="date" class="form-control" name="coordination_date" max="{{ now('Asia/Bangkok')->format('Y-m-d') }}" value="{{ old('coordination_date',now('Asia/Bangkok')->format('Y-m-d')) }}" required></div><div class="col-md-8"><label class="form-label fw-bold">หน่วยงาน <span class="text-danger">*</span></label><input class="form-control" name="agency_name" value="{{ old('agency_name') }}" required></div><div class="col-12"><label class="form-label fw-bold">เรื่องที่ประสาน <span class="text-danger">*</span></label><input class="form-control" name="subject" value="{{ old('subject') }}" required></div><div class="col-md-6"><label class="form-label fw-bold">ผู้ประสาน</label><input class="form-control" name="coordinator_name" value="{{ old('coordinator_name') }}"></div><div class="col-md-6"><label class="form-label fw-bold">สถานะเรื่อง</label><select class="form-select" name="status">@foreach($coordStatusLabels as $v=>$l)<option value="{{ $v }}" @selected(old('status','open')===$v)>{{ $l }}</option>@endforeach</select></div><div class="col-12"><label class="form-label fw-bold">ผลการประสาน</label><textarea class="form-control" name="result" rows="4">{{ old('result') }}</textarea></div><div class="col-md-5"><label class="form-label fw-bold">นัดหมายครั้งถัดไป</label><input type="date" class="form-control" name="next_appointment_date" value="{{ old('next_appointment_date') }}"></div><div class="col-md-7"><label class="form-label fw-bold">เอกสารประกอบ/หมายเหตุเอกสาร</label><input class="form-control" name="document_note" value="{{ old('document_note') }}" placeholder="ระบุชื่อเอกสารหรือที่จัดเก็บ ไม่ต้องแนบไฟล์ซ้ำ"></div></div></div><div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-primary" type="submit">บันทึกการประสาน</button></div></form></div></div></div>
@endif

@if($canUpdateDocuments)
<div class="modal fade idp-safe-modal" id="idpDocumentsModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl"><div class="modal-content"><form method="POST" action="{{ route('individual-development.documents.update',$client->id) }}">@csrf @method('PATCH')<div class="modal-header"><div><h5 class="modal-title fw-bold">สถานะเอกสารสำคัญ</h5><div class="small text-muted">สถานะนี้เป็น metadata สำหรับติดตาม ไม่ใช่การอัปโหลดไฟล์ใหม่</div></div><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body">@foreach($documentTypes as $type=>$label) @php($ds=$documentStatuses->get($type))<div class="idp-doc-row"><div><strong class="small">{{ $label }}</strong><div class="idp-source-note">ไฟล์เดิมในระบบ: {{ in_array($type,$uploadedFileTypes,true)?'มี':'ยังไม่พบ' }}</div></div><select class="form-select" name="documents[{{ $type }}][status]"><option value="">อัตโนมัติตามไฟล์</option>@foreach($docStatusLabels as $v=>$l)<option value="{{ $v }}" @selected(old('documents.'.$type.'.status',$ds?->status)===$v)>{{ $l }}</option>@endforeach</select><input type="date" class="form-control" name="documents[{{ $type }}][expires_at]" value="{{ old('documents.'.$type.'.expires_at',$ds?->expires_at?->format('Y-m-d')) }}" title="วันหมดอายุ"><input class="form-control" name="documents[{{ $type }}][note]" value="{{ old('documents.'.$type.'.note',$ds?->note) }}" placeholder="หมายเหตุ"></div>@endforeach</div><div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>บันทึกสถานะ</button></div></form></div></div></div>
@endif

<script>
document.addEventListener('DOMContentLoaded',function(){
 document.querySelectorAll('.idp-coord-delete').forEach(function(form){form.addEventListener('submit',function(e){if(!window.Swal){if(!confirm('ยืนยันลบรายการประสานที่บันทึกผิด?'))e.preventDefault();return;}e.preventDefault();Swal.fire({icon:'warning',title:'ยืนยันลบรายการ?',text:'ใช้เฉพาะรายการที่บันทึกผิด หากเป็นประวัติจริงควรเก็บไว้',showCancelButton:true,confirmButtonText:'ลบ',cancelButtonText:'ยกเลิก',confirmButtonColor:'#b91c1c'}).then(function(r){if(r.isConfirmed)form.submit();});});});
});
</script>
{{-- IDP_VALIDATION_COMPLETE_V1_INCLUDE --}}
@include('frontend.client.individual_development._validation')
