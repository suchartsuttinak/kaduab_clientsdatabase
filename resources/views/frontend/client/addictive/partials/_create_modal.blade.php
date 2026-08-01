@php
    $addExam = $addictiveAddHasErrors ? (string) old('exam', '0') : '0';
    $addRefer = $addictiveAddHasErrors ? (string) old('refer', '') : '';
    $nextAddictiveCount = ((int) ($addictives->max('count') ?? 0)) + 1;
@endphp

<style>
    #createAddictiveModal,
    #editAddictiveModal {
        z-index: 2147483000 !important;
        padding-right: 0 !important;
    }

    body.addictive-modal-open .modal-backdrop {
        z-index: 2147482990 !important;
    }

    body.addictive-modal-open {
        overflow: hidden;
    }

    #createAddictiveModal .modal-dialog,
    #editAddictiveModal .modal-dialog {
        width: calc(100% - 2rem);
        max-width: 1040px;
        height: calc(100dvh - 2rem);
        min-height: 0;
        margin: 1rem auto;
    }

    #createAddictiveModal .modal-content,
    #editAddictiveModal .modal-content {
        display: flex;
        height: 100%;
        min-height: 0;
        overflow: hidden;
        border: 0;
        border-radius: 22px;
        box-shadow: 0 30px 80px rgba(15, 23, 42, .24);
    }

    #createAddictiveModal form,
    #editAddictiveModal form {
        display: flex;
        min-height: 0;
        flex: 1 1 auto;
        flex-direction: column;
    }

    #createAddictiveModal .modal-header,
    #editAddictiveModal .modal-header {
        flex: 0 0 auto;
        border-bottom: 1px solid #e8eef6;
        background: linear-gradient(135deg, #ffffff 0%, #f7fbff 100%);
    }

    #createAddictiveModal .modal-body,
    #editAddictiveModal .modal-body {
        min-height: 0;
        flex: 1 1 auto;
        overflow-x: hidden;
        overflow-y: auto;
        background: #f8fafc;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
    }

    #createAddictiveModal .modal-footer,
    #editAddictiveModal .modal-footer {
        flex: 0 0 auto;
        border-top: 1px solid #e8eef6;
        background: #ffffff;
    }

    #createAddictiveModal .refer-option-grid,
    #editAddictiveModal .refer-option-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .8rem;
    }

    #createAddictiveModal .refer-option-card,
    #editAddictiveModal .refer-option-card {
        display: flex;
        min-height: 108px;
        align-items: flex-start;
        gap: .75rem;
        padding: .9rem;
        border: 1px solid #dbe4f0;
        border-radius: 16px;
        background: #ffffff;
        cursor: pointer;
        transition: border-color .18s ease, background-color .18s ease, box-shadow .18s ease;
    }

    #createAddictiveModal .refer-option-card:hover,
    #editAddictiveModal .refer-option-card:hover,
    #createAddictiveModal .refer-option-card:has(input:checked),
    #editAddictiveModal .refer-option-card:has(input:checked) {
        border-color: #60a5fa;
        background: #eff6ff;
        box-shadow: 0 0 0 .16rem rgba(37, 99, 235, .08);
    }

    #createAddictiveModal .refer-option-icon,
    #editAddictiveModal .refer-option-icon {
        display: inline-flex;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #dbeafe;
        color: #1d4ed8;
    }

    #createAddictiveModal .refer-option-body,
    #editAddictiveModal .refer-option-body {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: .2rem;
    }

    #createAddictiveModal .refer-option-title,
    #editAddictiveModal .refer-option-title {
        color: #0f172a;
        font-weight: 800;
    }

    #createAddictiveModal .refer-option-desc,
    #editAddictiveModal .refer-option-desc {
        color: #64748b;
        font-size: .82rem;
        line-height: 1.5;
    }

    #createAddictiveModal .refer-option-badge,
    #editAddictiveModal .refer-option-badge {
        display: inline-flex;
        width: fit-content;
        margin-top: .2rem;
        padding: .25rem .5rem;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: .72rem;
        font-weight: 800;
    }

    #createAddictiveModal .addictive-refer-invalid,
    #editAddictiveModal .addictive-refer-invalid {
        padding: .75rem;
        border: 1px solid rgba(220, 38, 38, .45);
        border-radius: 14px;
        background: #fef2f2;
    }

    #createAddictiveModal button[type="submit"]:disabled,
    #editAddictiveModal button[type="submit"]:disabled {
        cursor: not-allowed;
        opacity: 1;
    }

    @media (max-width: 767.98px) {
        #createAddictiveModal,
        #editAddictiveModal {
            padding: 0 !important;
        }

        #createAddictiveModal .modal-dialog,
        #editAddictiveModal .modal-dialog {
            width: 100%;
            max-width: none;
            height: 100dvh;
            margin: 0;
        }

        #createAddictiveModal .modal-content,
        #editAddictiveModal .modal-content {
            border-radius: 0;
        }

        #createAddictiveModal .refer-option-grid,
        #editAddictiveModal .refer-option-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="modal fade" id="createAddictiveModal" tabindex="-1" aria-labelledby="createAddictiveLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content addictive-modal">
            <div class="modal-header addictive-modal-header">
                <div class="addictive-modal-title-wrap">
                    <div class="addictive-modal-icon"><i class="bi bi-eyedropper"></i></div>
                    <div>
                        <h5 class="modal-title addictive-modal-title mb-0" id="createAddictiveLabel">
                            เพิ่มข้อมูลการตรวจสารเสพติด
                        </h5>
                        <div class="addictive-modal-subtitle">บันทึกผลการตรวจและข้อมูลการติดตามอย่างเป็นระบบ</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
            </div>

            <form id="addictive-form" action="{{ route('addictive.store') }}" method="POST" novalidate>
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="_form_context" value="addictive_add">

                <div class="modal-body addictive-modal-body">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-clipboard2-pulse"></i>
                            ข้อมูลการตรวจ
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-4 col-lg-3">
                                <label class="form-label form-label-modern">
                                    วันที่ตรวจ <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="date"
                                       class="form-control form-control-modern {{ $addictiveAddHasErrors && $errors->has('date') ? 'is-invalid' : '' }}"
                                       value="{{ $addictiveAddHasErrors ? old('date') : now('Asia/Bangkok')->toDateString() }}"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       required>
                                @if($addictiveAddHasErrors && $errors->has('date'))
                                    <div class="invalid-feedback">{{ $errors->first('date') }}</div>
                                @endif
                            </div>

                            <div class="col-12 col-md-4 col-lg-3">
                                <label class="form-label form-label-modern">ครั้งที่</label>
                                <input type="number"
                                       name="count"
                                       class="form-control form-control-modern"
                                       value="{{ $nextAddictiveCount }}"
                                       readonly>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label class="form-label form-label-modern d-block">
                                    ผลการตรวจ <span class="text-danger">*</span>
                                </label>
                                <div class="radio-card-group">
                                    <label class="radio-card">
                                        <input type="radio" name="exam" value="0" id="exam_no_new"
                                               {{ $addExam === '0' ? 'checked' : '' }} required>
                                        <span class="radio-card-content">
                                            <span class="radio-card-title">ไม่พบสารเสพติด</span>
                                            <span class="radio-card-desc">ผลการตรวจเป็นปกติ</span>
                                        </span>
                                    </label>
                                    <label class="radio-card">
                                        <input type="radio" name="exam" value="1" id="exam_yes_new"
                                               {{ $addExam === '1' ? 'checked' : '' }} required>
                                        <span class="radio-card-content">
                                            <span class="radio-card-title">พบสารเสพติด</span>
                                            <span class="radio-card-desc">ต้องเลือกแนวทางดำเนินการต่อ</span>
                                        </span>
                                    </label>
                                </div>
                                @if($addictiveAddHasErrors && $errors->has('exam'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('exam') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="refer_field_new" {{ $addExam === '1' ? '' : 'hidden' }}>
                        <div class="form-section-title">
                            <i class="bi bi-arrow-left-right"></i>
                            การดำเนินการต่อ <span class="text-danger">*</span>
                        </div>
                        <p class="text-muted small mb-3">กรุณาเลือกแนวทางดำเนินการเมื่อผลตรวจพบสารเสพติด</p>

                        <div data-refer-wrap class="{{ $addictiveAddHasErrors && $errors->has('refer') ? 'addictive-refer-invalid' : '' }}">
                            <div class="refer-option-grid">
                                <label class="refer-option-card" for="refer_treatment_new">
                                    <input type="radio" name="refer" value="1" id="refer_treatment_new"
                                           {{ $addRefer === '1' ? 'checked' : '' }}>
                                    <span class="refer-option-icon"><i class="bi bi-hospital"></i></span>
                                    <span class="refer-option-body">
                                        <span class="refer-option-title">ส่งต่อบำบัด</span>
                                        <span class="refer-option-desc">ส่งต่อเข้ารับการบำบัดรักษาตามแนวทางการดูแล</span>
                                        <span class="refer-option-badge">เข้าสู่การรักษา</span>
                                    </span>
                                </label>

                                <label class="refer-option-card" for="refer_followup_new">
                                    <input type="radio" name="refer" value="2" id="refer_followup_new"
                                           {{ $addRefer === '2' ? 'checked' : '' }}>
                                    <span class="refer-option-icon"><i class="bi bi-shield-check"></i></span>
                                    <span class="refer-option-body">
                                        <span class="refer-option-title">ติดตามดูแลต่อเนื่อง</span>
                                        <span class="refer-option-desc">เฝ้าระวัง ประเมินซ้ำ และติดตามผลอย่างต่อเนื่อง</span>
                                        <span class="refer-option-badge">เฝ้าระวังต่อเนื่อง</span>
                                    </span>
                                </label>
                            </div>
                            <div data-refer-client-error class="invalid-feedback d-none">
                                กรุณาเลือกแนวทางดำเนินการเมื่อพบสารเสพติด
                            </div>
                            @if($addictiveAddHasErrors && $errors->has('refer'))
                                <div class="invalid-feedback d-block">{{ $errors->first('refer') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-journal-text"></i>
                            รายละเอียดเพิ่มเติม
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label form-label-modern">บันทึกผล</label>
                                <textarea name="record"
                                          rows="4"
                                          maxlength="3000"
                                          class="form-control form-control-modern {{ $addictiveAddHasErrors && $errors->has('record') ? 'is-invalid' : '' }}"
                                          placeholder="ระบุผลการตรวจ รายละเอียด หรือข้อสังเกตเพิ่มเติม">{{ $addictiveAddHasErrors ? old('record') : '' }}</textarea>
                                @if($addictiveAddHasErrors && $errors->has('record'))
                                    <div class="invalid-feedback">{{ $errors->first('record') }}</div>
                                @endif
                            </div>

                            <div class="col-12 col-lg-6">
                                <label class="form-label form-label-modern">
                                    ผู้ตรวจ <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="recorder"
                                       maxlength="255"
                                       class="form-control form-control-modern {{ $addictiveAddHasErrors && $errors->has('recorder') ? 'is-invalid' : '' }}"
                                       value="{{ $addictiveAddHasErrors ? old('recorder') : '' }}"
                                       placeholder="ชื่อผู้ตรวจ / ผู้บันทึกข้อมูล"
                                       required>
                                @if($addictiveAddHasErrors && $errors->has('recorder'))
                                    <div class="invalid-feedback">{{ $errors->first('recorder') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer addictive-modal-footer">
                    <button type="button" class="btn btn-cancel-modern" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> ปิด
                    </button>
                    <button type="submit" class="btn btn-save-modern">
                        <i class="bi bi-save"></i> บันทึกผล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
