<div class="modal fade" id="add-vaccine-modal" tabindex="-1" aria-labelledby="addVaccineLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered vaccine-modal-dialog">
        <div class="modal-content vaccine-modal-content">
            <form action="{{ route('vaccine.store') }}" method="POST" id="add-vaccine-form" novalidate>
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">

                <div class="modal-header vaccine-modal-header vaccine-modal-header--primary">
                    <div class="vaccine-modal-header-text">
                        <h5 class="modal-title fw-bold mb-1" id="addVaccineLabel">
                            <i class="bi bi-capsule-pill me-2"></i>เพิ่มข้อมูลวัคซีน
                        </h5>
                        <div class="small opacity-75">
                            กรอกข้อมูลให้ครบถ้วนเพื่อใช้ติดตามประวัติการรับวัคซีน
                        </div>
                    </div>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="vaccine-form-body p-3 p-md-4">
                        <div class="vaccine-form-section">
                           

                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label required">วันที่รับวัคซีน</label>
                                    <input type="date"
                                    name="date"
                                    class="form-control @error('date') is-invalid @enderror"
                                    value="{{ old('date') }}"
                                    max="{{ now('Asia/Bangkok')->toDateString() }}">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-8">
                                    <label class="form-label required">ชนิดวัคซีน</label>
                                    <input type="text"
                                           name="vaccine_name"
                                           class="form-control @error('vaccine_name') is-invalid @enderror"
                                           value="{{ old('vaccine_name') }}"
                                           placeholder="ระบุชนิดวัคซีน">
                                    @error('vaccine_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">สถานพยาบาล</label>
                                    <input type="text"
                                           name="hospital"
                                           class="form-control @error('hospital') is-invalid @enderror"
                                           value="{{ old('hospital') }}"
                                           placeholder="ระบุสถานพยาบาล">
                                    @error('hospital')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">ผู้บันทึก</label>
                                    <input type="text"
                                           name="recorder"
                                           class="form-control @error('recorder') is-invalid @enderror"
                                           value="{{ old('recorder') }}"
                                           placeholder="ระบุชื่อผู้บันทึก">
                                    @error('recorder')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">หมายเหตุ</label>
                                    <textarea name="remark"
                                              class="form-control @error('remark') is-invalid @enderror"
                                              rows="4"
                                              placeholder="รายละเอียดเพิ่มเติม">{{ old('remark') }}</textarea>
                                    @error('remark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer vaccine-modal-footer">
                    <button type="button" class="btn btn-outline-secondary vaccine-btn vaccine-btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        <span>ปิด</span>
                    </button>

                    <button type="submit" class="btn btn-primary vaccine-btn vaccine-btn-primary">
                        <i class="bi bi-save"></i>
                        <span>บันทึกข้อมูล</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.vaccine-page #add-vaccine-modal{
    --vaccine-modal-gap: 20px;
}

.vaccine-page #add-vaccine-modal .vaccine-modal-dialog{
    width: min(1280px, calc(100vw - 32px));
    max-width: min(1280px, calc(100vw - 32px));
    margin: 16px auto;
}

.vaccine-page #add-vaccine-modal .vaccine-modal-content{
    border: 0;
    border-radius: var(--vaccine-radius);
    overflow: hidden;
    height: min(820px, calc(100vh - 32px));
    max-height: calc(100vh - 32px);
    background: #fff;
}

.vaccine-page #add-vaccine-modal .vaccine-modal-content > form{
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
}

.vaccine-page #add-vaccine-modal .vaccine-modal-header{
    border-bottom: 0;
    padding: 1rem 1.25rem;
    flex: 0 0 auto;
    background: linear-gradient(135deg, var(--vaccine-primary) 0%, var(--vaccine-primary-dark) 100%);
    color: #fff;
}

.vaccine-page #add-vaccine-modal .vaccine-modal-header-text{
    min-width: 0;
    padding-right: 12px;
}

.vaccine-page #add-vaccine-modal .modal-body{
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
    background: #fbfdff;
    padding: 0;
}

.vaccine-page #add-vaccine-modal .vaccine-form-body{
    min-height: 100%;
}

.vaccine-page #add-vaccine-modal .vaccine-form-section{
    background: #fff;
    border: 1px solid var(--vaccine-border-soft);
    border-radius: 16px;
    padding: 1rem;
}

.vaccine-page #add-vaccine-modal .vaccine-section-title{
    font-size: .95rem;
    font-weight: 700;
    color: var(--vaccine-text);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}

.vaccine-page #add-vaccine-modal .vaccine-section-title::before{
    content: "";
    width: 6px;
    height: 18px;
    border-radius: 999px;
    background: var(--vaccine-primary);
    display: inline-block;
}

.vaccine-page #add-vaccine-modal .form-label{
    font-size: .86rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: .45rem;
}

.vaccine-page #add-vaccine-modal .form-label.required::after{
    content: " *";
    color: var(--vaccine-danger);
}

.vaccine-page #add-vaccine-modal .form-control,
.vaccine-page #add-vaccine-modal .form-select{
    border-radius: 12px;
    border: 1px solid #dbe2ea;
    min-height: 44px;
    padding: .65rem .9rem;
    font-size: .92rem;
}

.vaccine-page #add-vaccine-modal textarea.form-control{
    min-height: 120px;
}

.vaccine-page #add-vaccine-modal .form-control:focus,
.vaccine-page #add-vaccine-modal .form-select:focus{
    border-color: #93c5fd;
    box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .12);
}

.vaccine-page #add-vaccine-modal .invalid-feedback{
    font-size: .8rem;
}

.vaccine-page #add-vaccine-modal .vaccine-modal-footer{
    flex: 0 0 auto;
    border-top: 1px solid var(--vaccine-border);
    background: #fff;
    padding: .9rem 1.25rem;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
    box-shadow: 0 -8px 18px rgba(15, 23, 42, 0.05);
}

.vaccine-page #add-vaccine-modal .vaccine-btn{
    min-height: 44px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    padding: .68rem 1rem;
    font-weight: 700;
    white-space: nowrap;
}

.vaccine-page #add-vaccine-modal .vaccine-btn-primary,
.vaccine-page #add-vaccine-modal .vaccine-btn-secondary{
    min-width: 150px;
}

@media (max-width: 1399.98px){
    .vaccine-page #add-vaccine-modal .vaccine-modal-dialog{
        width: min(1140px, calc(100vw - 24px));
        max-width: min(1140px, calc(100vw - 24px));
        margin: 12px auto;
    }

    .vaccine-page #add-vaccine-modal .vaccine-modal-content{
        height: calc(100vh - 24px);
        max-height: calc(100vh - 24px);
    }
}

@media (max-width: 1199.98px){
    .vaccine-page #add-vaccine-modal .vaccine-modal-dialog{
        width: calc(100vw - 20px);
        max-width: calc(100vw - 20px);
        margin: 10px auto;
    }

    .vaccine-page #add-vaccine-modal .vaccine-modal-content{
        height: calc(100vh - 20px);
        max-height: calc(100vh - 20px);
    }

    .vaccine-page #add-vaccine-modal .vaccine-modal-footer{
        justify-content: space-between;
        padding: .85rem 1rem;
    }

    .vaccine-page #add-vaccine-modal .vaccine-btn-primary,
    .vaccine-page #add-vaccine-modal .vaccine-btn-secondary{
        min-width: 136px;
    }
}

@media (max-width: 767.98px){
    .vaccine-page #add-vaccine-modal .vaccine-modal-dialog{
        width: calc(100vw - 12px);
        max-width: calc(100vw - 12px);
        margin: 6px auto;
    }

    .vaccine-page #add-vaccine-modal .vaccine-modal-content{
        height: calc(100vh - 12px);
        max-height: calc(100vh - 12px);
        border-radius: 14px;
    }

    .vaccine-page #add-vaccine-modal .vaccine-modal-header{
        padding: .9rem 1rem;
    }

    .vaccine-page #add-vaccine-modal .vaccine-form-body{
        padding: 1rem !important;
    }

    .vaccine-page #add-vaccine-modal .vaccine-modal-footer{
        padding: .85rem 1rem;
        flex-direction: column-reverse;
    }

    .vaccine-page #add-vaccine-modal .vaccine-modal-footer .vaccine-btn{
        width: 100%;
        min-width: 0;
    }
}

@media (max-width: 575.98px){
    .vaccine-page #add-vaccine-modal .vaccine-modal-dialog{
        width: 100vw;
        max-width: 100vw;
        margin: 0;
    }

    .vaccine-page #add-vaccine-modal .vaccine-modal-content{
        height: 100dvh;
        max-height: 100dvh;
        border-radius: 0;
    }

    .vaccine-page #add-vaccine-modal .vaccine-modal-header{
        position: sticky;
        top: 0;
        z-index: 20;
    }

    .vaccine-page #add-vaccine-modal .vaccine-modal-footer{
        position: sticky;
        bottom: 0;
        z-index: 20;
        padding: .8rem .9rem;
    }
}
</style>
@endpush