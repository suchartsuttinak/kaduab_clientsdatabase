@extends('admin_client.admin_client')
@section('content')

<link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">

<style>
    .ff-page{
        --ff-primary:#0f766e;
        --ff-primary-2:#14b8a6;
        --ff-primary-soft:rgba(15,118,110,.10);
        --ff-border:#d9e5e7;
        --ff-border-strong:#c9d8da;
        --ff-bg:#f4f8f9;
        --ff-card:#ffffff;
        --ff-card-soft:#f9fcfc;
        --ff-text:#1f2937;
        --ff-muted:#6b7280;
        --ff-danger:#dc2626;
        --ff-shadow:0 10px 24px rgba(15,23,42,.05);
        --ff-shadow-lg:0 18px 40px rgba(15,23,42,.08);
        --ff-radius-xl:22px;
        --ff-radius-lg:18px;
        --ff-radius-md:14px;
        --ff-radius-sm:12px;
    }

    .ff-page{
        background:linear-gradient(180deg,#f8fbfb 0%,#f3f7f8 100%);
        padding:1rem;
        color:var(--ff-text);
    }

    .ff-shell{
        background:rgba(255,255,255,.72);
        border:1px solid var(--ff-border);
        border-radius:var(--ff-radius-xl);
        box-shadow:var(--ff-shadow);
        overflow:hidden;
        backdrop-filter:blur(8px);
    }

    .ff-alert-wrap{
        padding:1rem 1rem 0;
    }

    .ff-alert{
        border-radius:14px;
        border:1px solid transparent;
        box-shadow:0 6px 16px rgba(15,23,42,.04);
        margin-bottom:.75rem;
    }

    .ff-body{
        padding:1rem;
    }

    .ff-hero{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:1rem;
        padding:.25rem .1rem 1rem;
    }

    .ff-hero-title{
        margin:0;
        font-size:1.2rem;
        font-weight:800;
        line-height:1.2;
        color:var(--ff-text);
    }

    .ff-hero-subtitle{
        margin:.35rem 0 0;
        font-size:.92rem;
        line-height:1.55;
        color:var(--ff-muted);
    }

    .ff-hero-badge{
        flex:0 0 auto;
        display:inline-flex;
        align-items:center;
        gap:.55rem;
        padding:.68rem .9rem;
        border-radius:999px;
        background:#fff;
        border:1px solid var(--ff-border);
        color:var(--ff-primary);
        font-weight:700;
        box-shadow:0 8px 18px rgba(15,23,42,.04);
        white-space:nowrap;
    }

    .ff-grid{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:1rem;
    }

    .ff-card{
        background:var(--ff-card);
        border:1px solid var(--ff-border);
        border-radius:var(--ff-radius-lg);
        box-shadow:0 8px 20px rgba(15,23,42,.04);
        overflow:hidden;
        height:100%;
    }

    .ff-card-header{
        display:flex;
        align-items:center;
        gap:.7rem;
        padding:1rem;
        background:linear-gradient(180deg,#ffffff 0%,#f9fcfc 100%);
        border-bottom:1px solid var(--ff-border);
    }

    .ff-card-icon{
        width:42px;
        height:42px;
        min-width:42px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:12px;
        background:linear-gradient(135deg,rgba(15,118,110,.12),rgba(20,184,166,.10));
        color:var(--ff-primary);
        border:1px solid rgba(15,118,110,.10);
    }

    .ff-card-title{
        margin:0;
        font-size:1rem;
        font-weight:800;
        line-height:1.25;
        color:var(--ff-text);
    }

    .ff-card-subtitle{
        margin:.22rem 0 0;
        font-size:.86rem;
        color:var(--ff-muted);
    }

    .ff-card-body{
        padding:1rem;
    }

    .ff-form-grid{
        display:grid;
        grid-template-columns:repeat(12,minmax(0,1fr));
        gap:.95rem .9rem;
    }

    .ff-span-12{grid-column:span 12;}
    .ff-span-8{grid-column:span 8;}
    .ff-span-6{grid-column:span 6;}
    .ff-span-4{grid-column:span 4;}

    .ff-field{
        min-width:0;
    }

    .ff-label{
        display:inline-flex;
        align-items:center;
        gap:.35rem;
        margin-bottom:.45rem;
        font-size:.93rem;
        font-weight:700;
        color:#334155;
        line-height:1.35;
    }

    .ff-required{
        color:var(--ff-danger);
        font-weight:800;
    }

    .ff-input,
    .ff-select,
    .ff-textarea,
    .ff-unit .input-group-text{
        min-height:46px;
        border-radius:var(--ff-radius-sm);
        border:1px solid #d7e3e5;
        font-size:.95rem;
        box-shadow:none;
    }

    .ff-input,
    .ff-select,
    .ff-textarea{
        background:#fff;
    }

    .ff-input:focus,
    .ff-select:focus,
    .ff-textarea:focus{
        border-color:rgba(15,118,110,.35);
        box-shadow:0 0 0 .22rem rgba(15,118,110,.10);
    }

    .ff-textarea{
        min-height:110px;
        resize:vertical;
    }

    .ff-textarea.ff-textarea-sm{min-height:96px;}
    .ff-textarea.ff-textarea-md{min-height:124px;}
    .ff-textarea.ff-textarea-lg{min-height:146px;}

    .ff-inline-radio{
        display:flex;
        flex-wrap:wrap;
        gap:.75rem 1rem;
        align-items:center;
        min-height:46px;
        padding:.65rem .9rem;
        border:1px solid #d7e3e5;
        border-radius:var(--ff-radius-sm);
        background:#fff;
    }

    .ff-inline-radio.is-invalid{
        border-color:rgba(220,38,38,.45);
    }

    .ff-inline-radio .form-check{
        margin:0;
    }

    .ff-inline-radio .form-check-label{
        font-size:.93rem;
        color:var(--ff-text);
    }

    .ff-detail-box{
        padding:.9rem;
        border-radius:var(--ff-radius-md);
        background:linear-gradient(180deg,#fbfefe 0%,#f6fbfb 100%);
        border:1px dashed var(--ff-border-strong);
    }

    .ff-unit{
        display:flex;
        align-items:center;
        gap:.6rem;
    }

    .ff-unit .unit-text{
        white-space:nowrap;
        font-size:.9rem;
        color:#4b5563;
        font-weight:600;
    }

    .ff-checklist{
        padding:.95rem;
        background:linear-gradient(180deg,#fcfefe 0%,#f8fbfb 100%);
        border:1px solid var(--ff-border);
        border-radius:var(--ff-radius-md);
    }

    .ff-checklist-grid{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:.7rem;
    }

    .ff-check-item{
        display:flex;
        align-items:flex-start;
        gap:.7rem;
        padding:.85rem .85rem;
        border:1px solid #e4ecee;
        border-radius:12px;
        background:#fff;
        cursor:pointer;
        transition:.18s ease;
        margin:0;
        min-height:54px;
    }

    .ff-check-item:hover{
        border-color:rgba(15,118,110,.18);
        background:#fbfefe;
        box-shadow:0 6px 14px rgba(15,23,42,.04);
    }

    .ff-check-item input{
        margin-top:.2rem;
        flex:0 0 auto;
    }

    .ff-check-item span{
        font-size:.93rem;
        line-height:1.45;
        color:var(--ff-text);
        word-break:break-word;
    }

    .ff-error{
        display:block;
        margin-top:.38rem;
        font-size:.82rem;
        line-height:1.35;
        color:var(--ff-danger);
    }

    .ff-input.is-invalid,
    .ff-select.is-invalid,
    .ff-textarea.is-invalid{
        border-color:rgba(220,38,38,.45);
        background-image:none;
    }

    .ff-actions{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1rem;
        margin-top:1rem;
        padding-top:1rem;
        border-top:1px solid var(--ff-border);
    }

    .ff-actions-note{
        color:var(--ff-muted);
        font-size:.88rem;
    }

    .ff-submit{
        min-height:48px;
        min-width:170px;
        padding:.82rem 1.25rem;
        border-radius:14px;
        font-size:.95rem;
        font-weight:800;
        border:0;
        background:linear-gradient(135deg,var(--ff-primary),var(--ff-primary-2));
        box-shadow:0 10px 22px rgba(15,118,110,.18);
    }

    .ff-submit:hover{
        filter:brightness(.98);
        transform:translateY(-1px);
    }

    .ff-submit:disabled{
        opacity:.82;
        transform:none;
    }

    @media (max-width: 1199.98px){
        .ff-grid{
            grid-template-columns:1fr;
        }
    }

    @media (max-width: 991.98px){
        .ff-page{
            padding:.8rem;
        }

        .ff-body{
            padding:.9rem;
        }

        .ff-hero{
            flex-direction:column;
            align-items:flex-start;
        }
    }

    @media (max-width: 767.98px){
        .ff-page{
            padding:.55rem;
        }

        .ff-shell{
            border-radius:16px;
        }

        .ff-body,
        .ff-alert-wrap{
            padding:.75rem;
        }

        .ff-card{
            border-radius:16px;
        }

        .ff-card-header,
        .ff-card-body{
            padding:.9rem;
        }

        .ff-form-grid{
            grid-template-columns:1fr;
            gap:.85rem;
        }

        .ff-span-12,
        .ff-span-8,
        .ff-span-6,
        .ff-span-4{
            grid-column:span 1;
        }

        .ff-checklist-grid{
            grid-template-columns:1fr;
        }

        .ff-actions{
            flex-direction:column;
            align-items:stretch;
        }

        .ff-submit{
            width:100%;
        }
    }
</style>

<div class="container-fluid ff-page">
    <div class="ff-shell">
        <div class="ff-alert-wrap">
            @if(session('error'))
                <div class="alert alert-danger ff-alert">{{ session('error') }}</div>
            @endif

            @if(session('success'))
                <div class="alert alert-success ff-alert">{{ session('success') }}</div>
            @endif
        </div>

        {{-- @include('admin_client.include.tabs') --}}

        <div class="ff-body">
            <div class="ff-hero">
                <div>
                    <h2 class="ff-hero-title">บันทึกการสอบข้อเท็จจริงเบื้องต้น</h2>
                    <p class="ff-hero-subtitle">
                        ออกแบบใหม่ให้ใช้งานง่ายขึ้น ดูทันสมัย รองรับทุกหน้าจอ และช่วยลดความผิดพลาดในการกรอกข้อมูล
                    </p>
                </div>

                <div class="ff-hero-badge">
                    <i class="bi bi-shield-check"></i>
                    <span>โหมดเพิ่มข้อมูลใหม่</span>
                </div>
            </div>

            <form action="{{ route('factfinding.store') }}" method="POST" id="factfindingForm" novalidate>
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">

                <div class="ff-grid">
                    <section class="ff-card">
                        <div class="ff-card-header">
                            <div class="ff-card-icon">
                                <i class="bi bi-clipboard2-pulse"></i>
                            </div>
                            <div>
                                <h4 class="ff-card-title">ข้อมูลการสอบข้อเท็จจริงเบื้องต้น</h4>
                                <p class="ff-card-subtitle">ข้อมูลเบื้องต้น สุขภาพ เอกสาร และความสัมพันธ์ในครอบครัว</p>
                            </div>
                        </div>

                        <div class="ff-card-body">
                            <div class="ff-form-grid">
                                <div class="ff-field ff-span-4">
                                    <label for="date" class="ff-label">
                                        วันที่นำส่ง <span class="ff-required">*</span>
                                    </label>

                                    <input type="date"
                                        name="date"
                                        id="date"
                                        class="form-control ff-input @error('date') is-invalid @enderror"
                                        value="{{ old('date') }}"
                                        max="{{ now('Asia/Bangkok')->toDateString() }}">

                                    @error('date')
                                        <span class="ff-error" id="date-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-8">
                                    <label for="fact_name" class="ff-label">ผู้นำส่ง <span class="ff-required">*</span></label>
                                    <input type="text" name="fact_name" id="fact_name" class="form-control ff-input @error('fact_name') is-invalid @enderror" value="{{ old('fact_name') }}">
                                    @error('fact_name')
                                        <span class="ff-error" id="fact_name-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="appearance" class="ff-label">รูปพรรณสัณฐาน</label>
                                    <input type="text" name="appearance" id="appearance" class="form-control ff-input @error('appearance') is-invalid @enderror" value="{{ old('appearance') }}">
                                    @error('appearance')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="skin" class="ff-label">สีผิว</label>
                                    <input type="text" name="skin" id="skin" class="form-control ff-input @error('skin') is-invalid @enderror" value="{{ old('skin') }}">
                                    @error('skin')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="scar" class="ff-label">ตำหนิ/แผลเป็น</label>
                                    <input type="text" name="scar" id="scar" class="form-control ff-input @error('scar') is-invalid @enderror" value="{{ old('scar') }}">
                                    @error('scar')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="disability" class="ff-label">ลักษณะความพิการ</label>
                                    <input type="text" name="disability" id="disability" class="form-control ff-input @error('disability') is-invalid @enderror" value="{{ old('disability') }}">
                                    @error('disability')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label class="ff-label d-block">ประวัติการเจ็บป่วย <span class="ff-required">*</span></label>
                                    <div class="ff-inline-radio @error('sick') is-invalid @enderror">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sick" id="sickYes" value="1" {{ old('sick') === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sickYes">มี</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sick" id="sickNo" value="0" {{ old('sick') === '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sickNo">ไม่มี</label>
                                        </div>
                                    </div>
                                    @error('sick')
                                        <span class="ff-error" id="sick-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12" id="sickDetailGroup" style="{{ old('sick') == 1 ? '' : 'display:none;' }}">
                                    <div class="ff-detail-box">
                                        <label for="sick_detail" class="ff-label">รายละเอียดการเจ็บป่วย <span class="ff-required">*</span></label>
                                        <textarea name="sick_detail" id="sick_detail" rows="4" class="form-control ff-textarea ff-textarea-sm @error('sick_detail') is-invalid @enderror" {{ old('sick') == 1 ? 'required' : '' }}>{{ old('sick_detail') }}</textarea>
                                        @error('sick_detail')
                                            <span class="ff-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="treatment" class="ff-label">การรักษาพยาบาล</label>
                                    <input type="text" name="treatment" id="treatment" class="form-control ff-input @error('treatment') is-invalid @enderror" value="{{ old('treatment') }}">
                                    @error('treatment')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="hospital" class="ff-label">สถานพยาบาล</label>
                                    <input type="text" name="hospital" id="hospital" class="form-control ff-input @error('hospital') is-invalid @enderror" value="{{ old('hospital') }}">
                                    @error('hospital')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-4">
                                    <label for="blood_group" class="ff-label">กรุ๊ปเลือด</label>
                                    <select name="blood_group" id="blood_group" class="form-select ff-select @error('blood_group') is-invalid @enderror">
                                        <option value="">-- กรุณาเลือกกรุ๊ปเลือด --</option>
                                        <option value="A" {{ old('blood_group') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('blood_group') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="AB" {{ old('blood_group') == 'AB' ? 'selected' : '' }}>AB</option>
                                        <option value="O" {{ old('blood_group') == 'O' ? 'selected' : '' }}>O</option>
                                        <option value="ไม่ระบุ" {{ old('blood_group') == 'ไม่ระบุ' ? 'selected' : '' }}>ไม่ระบุ</option>
                                    </select>
                                    @error('blood_group')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-4">
                                    <label for="weight" class="ff-label">น้ำหนัก</label>
                                    <div class="ff-unit">
                                        <input type="text" name="weight" id="weight" class="form-control ff-input @error('weight') is-invalid @enderror" value="{{ old('weight') }}">
                                        <span class="unit-text">กิโลกรัม</span>
                                    </div>
                                    @error('weight')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-4">
                                    <label for="height" class="ff-label">ส่วนสูง</label>
                                    <div class="ff-unit">
                                        <input type="text" name="height" id="height" class="form-control ff-input @error('height') is-invalid @enderror" value="{{ old('height') }}">
                                        <span class="unit-text">เซนติเมตร</span>
                                    </div>
                                    @error('height')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="hygiene" class="ff-label">ความสะอาดร่างกาย</label>
                                    <input type="text" name="hygiene" id="hygiene" class="form-control ff-input @error('hygiene') is-invalid @enderror" value="{{ old('hygiene') }}">
                                    @error('hygiene')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="oral_health" class="ff-label">สุขภาพช่องปาก</label>
                                    <input type="text" name="oral_health" id="oral_health" class="form-control ff-input @error('oral_health') is-invalid @enderror" value="{{ old('oral_health') }}">
                                    @error('oral_health')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="injury" class="ff-label">การบาดเจ็บ/บาดแผล</label>
                                    <input type="text" name="injury" id="injury" class="form-control ff-input @error('injury') is-invalid @enderror" value="{{ old('injury') }}">
                                    @error('injury')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="marital_id" class="ff-label">สถานะการสมรส <span class="ff-required">*</span></label>
                                    <select name="marital_id" id="marital_id" class="form-select ff-select @error('marital_id') is-invalid @enderror">
                                        <option value="">--สถานะการสมรส--</option>
                                        @foreach ($maritals as $item)
                                            <option value="{{ $item->id }}" {{ old('marital_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->marital_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('marital_id')
                                        <span class="ff-error" id="marital_id-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="relation_parent" class="ff-label">ความสัมพันธ์ระหว่างบิดา/มารดา</label>
                                    <textarea name="relation_parent" id="relation_parent" rows="3" class="form-control ff-textarea ff-textarea-sm @error('relation_parent') is-invalid @enderror">{{ old('relation_parent') }}</textarea>
                                    @error('relation_parent')
                                        <span class="ff-error" id="relation_parent-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="relation_family" class="ff-label">ความสัมพันธ์ระหว่างบุคคลในครอบครัว</label>
                                    <textarea name="relation_family" id="relation_family" rows="3" class="form-control ff-textarea ff-textarea-sm @error('relation_family') is-invalid @enderror">{{ old('relation_family') }}</textarea>
                                    @error('relation_family')
                                        <span class="ff-error" id="relation_family-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="relation_child" class="ff-label">ความสัมพันธ์ระหว่างเด็กกับบุคคลในครอบครัว</label>
                                    <textarea name="relation_child" id="relation_child" rows="3" class="form-control ff-textarea ff-textarea-sm @error('relation_child') is-invalid @enderror">{{ old('relation_child') }}</textarea>
                                    @error('relation_child')
                                        <span class="ff-error" id="relation_child-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="evidence" class="ff-label">เอกสารเพิ่มเติม</label>
                                    <input type="text" name="evidence" id="evidence" class="form-control ff-input @error('evidence') is-invalid @enderror" value="{{ old('evidence') }}">
                                    @error('evidence')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label class="ff-label d-block">
                                        เอกสารที่เกี่ยวข้อง
                                        <span class="ff-required">* (เลือกได้มากกว่า 1 รายการ)</span>
                                    </label>

                                    <div class="ff-checklist">
                                        <div class="ff-checklist-grid">
                                            @foreach($documents as $document)
                                                <label for="document{{ $document->id }}" class="ff-check-item">
                                                    <input type="checkbox"
                                                        name="documents[]"
                                                        value="{{ $document->id }}"
                                                        id="document{{ $document->id }}"
                                                        {{ is_array(old('documents')) && in_array($document->id, old('documents')) ? 'checked' : '' }}>
                                                    <span>{{ $document->document_name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="ff-card">
                        <div class="ff-card-header">
                            <div class="ff-card-icon">
                                <i class="bi bi-house-heart"></i>
                            </div>
                            <div>
                                <h4 class="ff-card-title">การประเมินสภาวะเด็กและครอบครัว</h4>
                                <p class="ff-card-subtitle">ประเมินสภาพแวดล้อม ความต้องการ และการวินิจฉัยปัญหา</p>
                            </div>
                        </div>

                        <div class="ff-card-body">
                            <div class="ff-form-grid">
                                <div class="ff-field ff-span-12">
                                    <label for="ex_conditions" class="ff-label">สภาพที่อยู่อาศัยภายนอก</label>
                                    <textarea name="ex_conditions" id="ex_conditions" rows="3" class="form-control ff-textarea ff-textarea-sm @error('ex_conditions') is-invalid @enderror">{{ old('ex_conditions') }}</textarea>
                                    @error('ex_conditions')
                                        <span class="ff-error" id="ex_conditions-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="in_conditions" class="ff-label">สภาพที่อยู่อาศัยภายใน</label>
                                    <textarea name="in_conditions" id="in_conditions" rows="3" class="form-control ff-textarea ff-textarea-sm @error('in_conditions') is-invalid @enderror">{{ old('in_conditions') }}</textarea>
                                    @error('in_conditions')
                                        <span class="ff-error" id="in_conditions-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="environment" class="ff-label">สภาพแวดล้อม</label>
                                    <textarea name="environment" id="environment" rows="4" class="form-control ff-textarea ff-textarea-md @error('environment') is-invalid @enderror">{{ old('environment') }}</textarea>
                                    @error('environment')
                                        <span class="ff-error" id="environment-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="cause_problem" class="ff-label">สาเหตุที่เข้ารับการสงเคราะห์</label>
                                    <textarea name="cause_problem" id="cause_problem" rows="3" class="form-control ff-textarea ff-textarea-sm @error('cause_problem') is-invalid @enderror">{{ old('cause_problem') }}</textarea>
                                    @error('cause_problem')
                                        <span class="ff-error" id="cause_problem-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="need" class="ff-label">ความต้องการความช่วยเหลือ</label>
                                    <textarea name="need" id="need" rows="3" class="form-control ff-textarea ff-textarea-sm @error('need') is-invalid @enderror">{{ old('need') }}</textarea>
                                    @error('need')
                                        <span class="ff-error" id="need-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="case_history" class="ff-label">ประวัติความเป็นมา</label>
                                    <textarea name="case_history" id="case_history" rows="4" class="form-control ff-textarea ff-textarea-md @error('case_history') is-invalid @enderror">{{ old('case_history') }}</textarea>
                                    @error('case_history')
                                        <span class="ff-error" id="case_history-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="information" class="ff-label">ข้อเท็จจริงอื่นๆ</label>
                                    <textarea name="information" id="information" rows="4" class="form-control ff-textarea ff-textarea-md @error('information') is-invalid @enderror">{{ old('information') }}</textarea>
                                    @error('information')
                                        <span class="ff-error" id="information-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="diagnosis" class="ff-label">การวินิจฉัยปัญหา</label>
                                    <textarea name="diagnosis" id="diagnosis" rows="4" class="form-control ff-textarea ff-textarea-md @error('diagnosis') is-invalid @enderror">{{ old('diagnosis') }}</textarea>
                                    @error('diagnosis')
                                        <span class="ff-error" id="diagnosis-error">{{ $message }}</span>
                                    @enderror
                                </div>

                              <div class="ff-field ff-span-4">
                                <label for="receive_date" class="ff-label">
                                    วันที่บันทึก <span class="ff-required">*</span>
                                </label>

                                <input type="date"
                                    name="receive_date"
                                    id="receive_date"
                                    class="form-control ff-input @error('receive_date') is-invalid @enderror"
                                    value="{{ old('receive_date') }}"
                                    max="{{ now('Asia/Bangkok')->toDateString() }}">

                                @error('receive_date')
                                    <span class="ff-error" id="receive_date-error">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                        <div class="ff-field ff-span-6">
                            <label class="ff-label">ผู้บันทึก</label>

                            <input type="text"
                                class="form-control ff-input"
                                value="{{ auth()->user()->name ?? 'ไม่ระบุผู้บันทึก' }}"
                                readonly>
                        </div>

                        </div> {{-- ff-form-grid --}}
                        </div> {{-- ff-card-body --}}
                        </section> {{-- ff-card --}}
                        </div> {{-- ff-grid --}}

                        <div class="ff-actions">
                            <div class="ff-actions-note">
                                กรุณาตรวจสอบข้อมูลสำคัญให้ครบถ้วนก่อนบันทึก
                            </div>

                            <button type="submit"
                                    class="btn btn-success ff-submit"
                                    id="ffSubmitBtn">
                                <i class="bi bi-check-circle me-1"></i>
                                บันทึกข้อมูล
                            </button>
                        </div>

                        </form>
                        </div> {{-- ff-body --}}
                        </div> {{-- ff-shell --}}
                        </div> {{-- ff-page --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const yes = document.getElementById('sickYes');
    const no = document.getElementById('sickNo');
    const detail = document.getElementById('sickDetailGroup');
    const detailField = document.getElementById('sick_detail');
    const form = document.getElementById('factfindingForm');
    const submitBtn = document.getElementById('ffSubmitBtn');

    function toggleDetail() {
        if (!yes || !no || !detail || !detailField) return;

        if (yes.checked) {
            detail.style.display = '';
            detailField.setAttribute('required', 'required');
        } else {
            detail.style.display = 'none';
            detailField.removeAttribute('required');
            if (!detailField.dataset.keepValue) {
                detailField.value = '';
            }
        }
    }

    if (yes && no) {
        yes.addEventListener('change', toggleDetail);
        no.addEventListener('change', toggleDetail);
        toggleDetail();
    }

    document.addEventListener('input', function (e) {
        const target = e.target;
        if (!target.matches('input, textarea, select')) return;

        const errorId = target.id ? `${target.id}-error` : null;
        if (errorId) {
            const errorEl = document.getElementById(errorId);
            if (errorEl) errorEl.remove();
        }

        target.classList.remove('is-invalid');
    });

    document.querySelectorAll('input[name="sick"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const errorMsg = document.getElementById('sick-error');
            if (errorMsg) errorMsg.remove();
            document.querySelector('.ff-inline-radio')?.classList.remove('is-invalid');
        });
    });

    if (form && submitBtn) {
        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>กำลังบันทึก...';
        });
    }
});
</script>

@endsection