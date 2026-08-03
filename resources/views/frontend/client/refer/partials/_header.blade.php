@php
    $hasReferRows = isset($refers) && $refers->isNotEmpty();
    $canCreateRefer = $canCreateRefer ?? !in_array($client->release_status, ['pending_refer', 'refer'], true);
    $clientDisplayName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $clientAgeText = filled($client->age ?? null) ? ($client->age . ' ปี') : '-';
@endphp

<style>
.rf-main-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
    flex-wrap:wrap;
    margin-bottom:16px;
    padding:16px 20px;
    border:1px solid #dbe3ef;
    border-radius:18px;
    background:#fff;
    box-shadow:0 10px 30px rgba(15,23,42,.05);
}

.rf-header-title{
    display:flex;
    align-items:center;
    gap:.85rem;
    flex:1 1 430px;
    min-width:0;
}

.rf-header-icon{
    width:44px;
    height:44px;
    border-radius:13px;
    background:#eef2ff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-size:1.05rem;
    color:#4f46e5;
    flex:0 0 44px;
}

.rf-header-text{
    min-width:0;
}

.rf-header-text h1{
    margin:0;
    color:#0f172a;
    font-size:clamp(1.25rem,1.6vw,1.5rem);
    font-weight:800;
    line-height:1.35;
    letter-spacing:-.01em;
}

.rf-header-text p{
    margin:.3rem 0 0;
    color:#64748b;
    font-size:clamp(.92rem,1vw,1rem);
    font-weight:500;
    line-height:1.45;
}

.rf-header-text p strong{
    color:#0f172a;
    font-weight:800;
}

.rf-header-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:.65rem;
    flex-wrap:wrap;
}

.rf-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:42px;
    padding:10px 18px;
    border-radius:12px;
    font-size:14px;
    font-weight:700;
    line-height:1;
    border:1px solid transparent;
    cursor:pointer;
    text-decoration:none;
    transition:transform .18s ease,box-shadow .18s ease,background-color .18s ease,border-color .18s ease,color .18s ease;
    white-space:nowrap;
}

.rf-btn i{font-size:16px;}

.rf-btn-primary{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    box-shadow:0 7px 16px rgba(37,99,235,.22);
}

.rf-btn-primary:hover,
.rf-btn-primary:focus{
    color:#fff;
    transform:translateY(-1px);
    box-shadow:0 10px 20px rgba(37,99,235,.28);
}

.rf-btn-report{
    background:#fff;
    color:#0f766e;
    border-color:#99f6e4;
}

.rf-btn-report:hover,
.rf-btn-report:focus{
    background:#ecfdf5;
    color:#0f766e;
    border-color:#5eead4;
    transform:translateY(-1px);
}

.rf-btn-back{
    color:#7c3aed;
    background:#fff;
    border-color:#8b5cf6;
}

.rf-btn-back:hover,
.rf-btn-back:focus{
    color:#6d28d9;
    background:#faf5ff;
    border-color:#7c3aed;
    transform:translateY(-1px);
}

.rf-btn:active{transform:translateY(0);}

@media (max-width:767.98px){
    .rf-main-header{padding:14px;align-items:stretch;}
    .rf-header-title,.rf-header-actions{width:100%;}
    .rf-header-actions > *{flex:1 1 calc(50% - .35rem);}
}

@media (max-width:575.98px){
    .rf-header-title{align-items:flex-start;gap:.75rem;}
    .rf-header-icon{width:44px;height:44px;flex-basis:44px;}
    .rf-header-text h1{font-size:1.12rem;}
    .rf-header-text p{margin-top:.25rem;font-size:.9rem;}
    .rf-header-actions{flex-direction:column;}
    .rf-header-actions > *{width:100%;flex:1 1 auto;}
}
</style>

<div class="rf-main-header">
    <div class="rf-header-title">
        <span class="rf-header-icon" aria-hidden="true">
            <i class="bi bi-box-arrow-right"></i>
        </span>

        <div class="rf-header-text">
            <h1>ข้อมูลการจำหน่ายผู้รับออกจากสถานสงเคราะห์</h1>
            <p>
                ผู้รับบริการ: <strong>{{ $clientDisplayName }}</strong>
                <span class="mx-1">•</span>
                อายุ: <strong>{{ $clientAgeText }}</strong>
            </p>
        </div>
    </div>

    <div class="rf-header-actions">
        @if($hasReferRows)
            <a href="{{ route('refers.report', $client->id) }}"
               class="rf-btn rf-btn-report">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>รายงาน</span>
            </a>

            @if($canCreateRefer)
                <button type="button"
                        class="rf-btn rf-btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#createReferModal">
                    <i class="bi bi-plus-circle"></i>
                    <span>เพิ่มข้อมูลจำหน่าย</span>
                </button>
            @endif
        @endif

        <a href="{{ route('admin.index', $client->id) }}"
           class="rf-btn rf-btn-back"
           aria-label="กลับหน้าหลักผู้รับบริการ">
            <i class="bi bi-arrow-left-circle"></i>
            <span>กลับ</span>
        </a>
    </div>
</div>
