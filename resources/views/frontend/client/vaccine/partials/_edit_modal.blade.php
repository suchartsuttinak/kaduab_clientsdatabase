<div class="modal fade" id="edit-vaccine-modal" tabindex="-1" aria-labelledby="editVaccineLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered vaccine-modal-dialog">
        <div class="modal-content vaccine-modal-content">
            <form method="POST" id="edit-vaccine-form" action="" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" name="client_id" id="edit_client_id">

                <div class="modal-header vaccine-modal-header vaccine-modal-header--warning">
                    <div class="vaccine-modal-header-text">
                        <h5 class="modal-title fw-bold mb-1" id="editVaccineLabel">
                            <i class="bi bi-pencil-square me-2"></i>แก้ไขข้อมูลวัคซีน
                        </h5>
                        <div class="small opacity-75">
                            ปรับปรุงข้อมูลให้ถูกต้องและเป็นปัจจุบัน
                        </div>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="vaccine-form-body p-3 p-md-4">
                        <div class="vaccine-form-section">
                            <div class="vaccine-section-title">ข้อมูลการรับวัคซีน</div>

                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label required">วันที่รับวัคซีน</label>
                                    <input type="date"
                                        name="date"
                                        id="edit_date"
                                        class="form-control"
                                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                                        required>
                                </div>

                                <div class="col-12 col-md-8">
                                    <label class="form-label required">ชนิดวัคซีน</label>
                                    <input type="text" name="vaccine_name" id="edit_vaccine_name" class="form-control" placeholder="ระบุชนิดวัคซีน">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">สถานพยาบาล</label>
                                    <input type="text" name="hospital" id="edit_hospital" class="form-control" placeholder="ระบุสถานพยาบาล">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">ผู้บันทึก</label>
                                    <input type="text" name="recorder" id="edit_recorder" class="form-control" placeholder="ระบุชื่อผู้บันทึก">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">หมายเหตุ</label>
                                    <textarea name="remark" id="edit_remark" class="form-control" rows="4" placeholder="รายละเอียดเพิ่มเติม"></textarea>
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

                    <button type="submit" class="btn btn-success vaccine-btn vaccine-btn-success">
                        <i class="bi bi-save"></i>
                        <span>อัปเดตข้อมูล</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.vaccine-page #edit-vaccine-modal{
    --vaccine-modal-gap: 20px;
}

.vaccine-page #edit-vaccine-modal .vaccine-modal-dialog{
    width: min(1280px, calc(100vw - 32px));
    max-width: min(1280px, calc(100vw - 32px));
    margin: 16px auto;
}

.vaccine-page #edit-vaccine-modal .vaccine-modal-content{
    border: 0;
    border-radius: var(--vaccine-radius);
    overflow: hidden;
    height: min(820px, calc(100vh - 32px));
    max-height: calc(100vh - 32px);
    background: #fff;
}

.vaccine-page #edit-vaccine-modal .vaccine-modal-content > form{
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
}

.vaccine-page #edit-vaccine-modal .vaccine-modal-header{
    border-bottom: 0;
    padding: 1rem 1.25rem;
    flex: 0 0 auto;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #78350f;
}

.vaccine-page #edit-vaccine-modal .vaccine-modal-header-text{
    min-width: 0;
    padding-right: 12px;
}

.vaccine-page #edit-vaccine-modal .modal-body{
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
    background: #fffcf5;
    padding: 0;
}

.vaccine-page #edit-vaccine-modal .vaccine-form-body{
    min-height: 100%;
}

.vaccine-page #edit-vaccine-modal .vaccine-form-section{
    background: #fff;
    border: 1px solid #f3ead7;
    border-radius: 16px;
    padding: 1rem;
}

.vaccine-page #edit-vaccine-modal .vaccine-section-title{
    font-size: .95rem;
    font-weight: 700;
    color: var(--vaccine-text);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}

.vaccine-page #edit-vaccine-modal .vaccine-section-title::before{
    content: "";
    width: 6px;
    height: 18px;
    border-radius: 999px;
    background: var(--vaccine-warning);
    display: inline-block;
}

.vaccine-page #edit-vaccine-modal .form-label{
    font-size: .86rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: .45rem;
}

.vaccine-page #edit-vaccine-modal .form-label.required::after{
    content: " *";
    color: var(--vaccine-danger);
}

.vaccine-page #edit-vaccine-modal .form-control,
.vaccine-page #edit-vaccine-modal .form-select{
    border-radius: 12px;
    border: 1px solid #dbe2ea;
    min-height: 44px;
    padding: .65rem .9rem;
    font-size: .92rem;
}

.vaccine-page #edit-vaccine-modal textarea.form-control{
    min-height: 120px;
}

.vaccine-page #edit-vaccine-modal .form-control:focus,
.vaccine-page #edit-vaccine-modal .form-select:focus{
    border-color: #fcd34d;
    box-shadow: 0 0 0 .2rem rgba(245, 158, 11, .14);
}

.vaccine-page #edit-vaccine-modal .vaccine-modal-footer{
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

.vaccine-page #edit-vaccine-modal .vaccine-btn{
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

.vaccine-page #edit-vaccine-modal .vaccine-btn-success,
.vaccine-page #edit-vaccine-modal .vaccine-btn-secondary{
    min-width: 150px;
}

@media (max-width: 1399.98px){
    .vaccine-page #edit-vaccine-modal .vaccine-modal-dialog{
        width: min(1140px, calc(100vw - 24px));
        max-width: min(1140px, calc(100vw - 24px));
        margin: 12px auto;
    }

    .vaccine-page #edit-vaccine-modal .vaccine-modal-content{
        height: calc(100vh - 24px);
        max-height: calc(100vh - 24px);
    }
}

@media (max-width: 1199.98px){
    .vaccine-page #edit-vaccine-modal .vaccine-modal-dialog{
        width: calc(100vw - 20px);
        max-width: calc(100vw - 20px);
        margin: 10px auto;
    }

    .vaccine-page #edit-vaccine-modal .vaccine-modal-content{
        height: calc(100vh - 20px);
        max-height: calc(100vh - 20px);
    }

    .vaccine-page #edit-vaccine-modal .vaccine-modal-footer{
        justify-content: space-between;
        padding: .85rem 1rem;
    }

    .vaccine-page #edit-vaccine-modal .vaccine-btn-success,
    .vaccine-page #edit-vaccine-modal .vaccine-btn-secondary{
        min-width: 136px;
    }
}

@media (max-width: 767.98px){
    .vaccine-page #edit-vaccine-modal .vaccine-modal-dialog{
        width: calc(100vw - 12px);
        max-width: calc(100vw - 12px);
        margin: 6px auto;
    }

    .vaccine-page #edit-vaccine-modal .vaccine-modal-content{
        height: calc(100vh - 12px);
        max-height: calc(100vh - 12px);
        border-radius: 14px;
    }

    .vaccine-page #edit-vaccine-modal .vaccine-modal-header{
        padding: .9rem 1rem;
    }

    .vaccine-page #edit-vaccine-modal .vaccine-form-body{
        padding: 1rem !important;
    }

    .vaccine-page #edit-vaccine-modal .vaccine-modal-footer{
        padding: .85rem 1rem;
        flex-direction: column-reverse;
    }

    .vaccine-page #edit-vaccine-modal .vaccine-modal-footer .vaccine-btn{
        width: 100%;
        min-width: 0;
    }
}

@media (max-width: 575.98px){
    .vaccine-page #edit-vaccine-modal .vaccine-modal-dialog{
        width: 100vw;
        max-width: 100vw;
        margin: 0;
    }

    .vaccine-page #edit-vaccine-modal .vaccine-modal-content{
        height: 100dvh;
        max-height: 100dvh;
        border-radius: 0;
    }

    .vaccine-page #edit-vaccine-modal .vaccine-modal-header{
        position: sticky;
        top: 0;
        z-index: 20;
    }

    .vaccine-page #edit-vaccine-modal .vaccine-modal-footer{
        position: sticky;
        bottom: 0;
        z-index: 20;
        padding: .8rem .9rem;
    }
}
</style>
@endpush