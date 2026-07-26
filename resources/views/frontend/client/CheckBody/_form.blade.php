@php
    use App\Helpers\ThaiDateHelper;

    $isEdit = isset($checkbody) && $checkbody;
    $oldDevelopment = old('development', $checkbody->development ?? 'สมวัย');
    $oldDevelopmentType = old('development_type', $checkbody->development_type ?? 'เด็กทั่วไป');
    $oldSpecialSupportType = old('special_support_type', $checkbody->special_support_type ?? '');
    $assessorDateValue = old('assessor_date', ThaiDateHelper::toInputDate($checkbody->assessor_date ?? null));
@endphp

<div class="form-card card mb-3">
    <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
        <div>
            <h5 class="section-title">
                <i class="bi bi-clipboard2-pulse me-2 text-primary"></i>
                {{ $isEdit ? 'แก้ไขข้อมูลการตรวจสุขภาพ' : 'ฟอร์มบันทึกการตรวจสุขภาพเบื้องต้น' }}
            </h5>
            <p class="section-subtitle">
                บันทึกผลการตรวจร่างกาย พัฒนาการ และข้อมูลสุขภาพพื้นฐานของผู้รับบริการ
            </p>
        </div>
        <div>
            <span class="badge text-bg-light border">
                {{ $isEdit ? 'Editing Mode' : 'Create Mode' }}
            </span>
        </div>
    </div>

    <div class="card-body">
        <form id="checkbody-form" method="POST"
            action="{{ $isEdit ? route('check_body.update', $checkbody->id) : route('check_body.store') }}">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <input type="hidden" name="client_id" value="{{ $client->id }}">

            <div class="row g-3">
                <div class="col-md-4 col-xl-3">
                    <label class="modern-label">วันที่ตรวจ <span class="required">*</span></label>

                    <input type="date" name="assessor_date"
                        class="form-control @error('assessor_date') is-invalid @enderror"
                        value="{{ $assessorDateValue }}" max="{{ now('Asia/Bangkok')->toDateString() }}" required>

                    @error('assessor_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 col-xl-3">
                    <label class="modern-label">ผู้ตรวจ / ผู้บันทึก <span class="required">*</span></label>
                    <input type="text" name="recorder" class="form-control @error('recorder') is-invalid @enderror"
                        value="{{ old('recorder', $checkbody->recorder ?? '') }}"
                        placeholder="ระบุชื่อผู้ตรวจหรือผู้บันทึก">
                    @error('recorder')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 col-xl-6">
                    <label class="modern-label d-block">พัฒนาการ <span class="required">*</span></label>
                    <div class="radio-card-group">
                        <div class="radio-card">
                            <input type="radio" name="development" id="development_normal" value="สมวัย"
                                {{ $oldDevelopment === 'สมวัย' ? 'checked' : '' }}>
                            <label for="development_normal">
                                <span class="icon-wrap"><i class="bi bi-emoji-smile"></i></span>
                                <span>สมวัย</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" name="development" id="development_abnormal" value="ไม่สมวัย"
                                {{ $oldDevelopment === 'ไม่สมวัย' ? 'checked' : '' }}>
                            <label for="development_abnormal">
                                <span class="icon-wrap"><i class="bi bi-exclamation-triangle"></i></span>
                                <span>ไม่สมวัย</span>
                            </label>
                        </div>
                    </div>
                    @error('development')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12" id="development-detail-field"
                    style="{{ $oldDevelopment === 'ไม่สมวัย' ? '' : 'display:none;' }}">
                    <label class="modern-label">รายละเอียดกรณีพัฒนาการไม่สมวัย</label>
                    <textarea name="detail" rows="3" class="form-control @error('detail') is-invalid @enderror"
                        placeholder="อธิบายรายละเอียดเพิ่มเติม">{{ old('detail', $checkbody->detail ?? '') }}</textarea>
                    @error('detail')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- PATCH: เพิ่มส่วนการพัฒนาใหม่ --}}
                <div class="col-12">
                    <label class="modern-label d-block">การส่งเสริมและพัฒนา<span class="required">*</span></label>
                    <div class="radio-card-group">
                        <div class="radio-card">
                            <input type="radio" name="development_type" id="development_type_normal"
                                value="เด็กทั่วไป" {{ $oldDevelopmentType === 'เด็กทั่วไป' ? 'checked' : '' }}>
                            <label for="development_type_normal">
                                <span class="icon-wrap"><i class="bi bi-person"></i></span>
                                <span>เด็กทั่วไป</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" name="development_type" id="development_type_special"
                                value="เด็กกลุ่มพิเศษ" {{ $oldDevelopmentType === 'เด็กกลุ่มพิเศษ' ? 'checked' : '' }}>
                            <label for="development_type_special">
                                <span class="icon-wrap"><i class="bi bi-stars"></i></span>
                                <span>เด็กกลุ่มพิเศษ</span>
                            </label>
                        </div>
                    </div>
                    @error('development_type')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12" id="special-support-section"
                    style="{{ $oldDevelopmentType === 'เด็กกลุ่มพิเศษ' ? '' : 'display:none;' }}">
                    <div class="metric-box">
                        <div class="row g-3">
                            <div class="col-lg-8">
                                <label class="modern-label">ประเภทการสนับสนุน</label>
                                <select name="special_support_type" id="special_support_type"
                                    class="form-select @error('special_support_type') is-invalid @enderror">
                                    <option value="">-- กรุณาเลือก --</option>
                                    <option value="ต้องการการสนับสนุนด้านการเรียนรู้ (อ่าน เขียน คำนวณ)"
                                        {{ $oldSpecialSupportType === 'ต้องการการสนับสนุนด้านการเรียนรู้ (อ่าน เขียน คำนวณ)' ? 'selected' : '' }}>
                                        ต้องการการสนับสนุนด้านการเรียนรู้ (อ่าน เขียน คำนวณ)
                                    </option>
                                    <option value="ต้องการการสนับสนุนด้านพฤติกรรมและอารมณ์ (การควบคุมอารมณ์, สมาธิ)"
                                        {{ $oldSpecialSupportType === 'ต้องการการสนับสนุนด้านพฤติกรรมและอารมณ์ (การควบคุมอารมณ์, สมาธิ)' ? 'selected' : '' }}>
                                        ต้องการการสนับสนุนด้านพฤติกรรมและอารมณ์ (การควบคุมอารมณ์, สมาธิ)
                                    </option>
                                    <option value="ต้องการการสนับสนุนด้านสังคม (การเข้าสังคม, ทำงานร่วมกับเพื่อน)"
                                        {{ $oldSpecialSupportType === 'ต้องการการสนับสนุนด้านสังคม (การเข้าสังคม, ทำงานร่วมกับเพื่อน)' ? 'selected' : '' }}>
                                        ต้องการการสนับสนุนด้านสังคม (การเข้าสังคม, ทำงานร่วมกับเพื่อน)
                                    </option>
                                    <option value="ต้องการการสนับสนุนด้านร่างกาย (การเคลื่อนไหว, สุขภาพ)"
                                        {{ $oldSpecialSupportType === 'ต้องการการสนับสนุนด้านร่างกาย (การเคลื่อนไหว, สุขภาพ)' ? 'selected' : '' }}>
                                        ต้องการการสนับสนุนด้านร่างกาย (การเคลื่อนไหว, สุขภาพ)
                                    </option>
                                    <option value="มีศักยภาพพิเศษที่ควรส่งเสริม (ดนตรี, กีฬา, ศิลปะ)"
                                        {{ $oldSpecialSupportType === 'มีศักยภาพพิเศษที่ควรส่งเสริม (ดนตรี, กีฬา, ศิลปะ)' ? 'selected' : '' }}>
                                        มีศักยภาพพิเศษที่ควรส่งเสริม (ดนตรี, กีฬา, ศิลปะ)
                                    </option>
                                    <option value="อื่น ๆ" {{ $oldSpecialSupportType === 'อื่น ๆ' ? 'selected' : '' }}>
                                        อื่น ๆ
                                    </option>
                                </select>
                                @error('special_support_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-4" id="special-support-other-wrap"
                                style="{{ $oldSpecialSupportType === 'อื่น ๆ' ? '' : 'display:none;' }}">
                                <label class="modern-label">อื่น ๆ (ระบุ)</label>
                                <input type="text" name="special_support_other" id="special_support_other"
                                    class="form-control @error('special_support_other') is-invalid @enderror"
                                    value="{{ old('special_support_other', $checkbody->special_support_other ?? '') }}"
                                    placeholder="ระบุรายละเอียดเพิ่มเติม">
                                @error('special_support_other')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="metric-box">
                        <div class="row g-3">
                            <div class="col-md-6 col-xl-3">
                                <label class="modern-label">น้ำหนัก (กก.)</label>
                                <input type="number" step="0.01" min="0" name="weight"
                                    class="form-control @error('weight') is-invalid @enderror"
                                    value="{{ old('weight', $checkbody->weight ?? '') }}" placeholder="เช่น 25.50">
                                @error('weight')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-3">
                                <label class="modern-label">ส่วนสูง (ซม.)</label>
                                <input type="number" step="0.01" min="0" name="height"
                                    class="form-control @error('height') is-invalid @enderror"
                                    value="{{ old('height', $checkbody->height ?? '') }}" placeholder="เช่น 120.00">
                                @error('height')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-3">
                                <label class="modern-label">สุขภาพช่องปาก</label>
                                <input type="text" name="oral"
                                    class="form-control @error('oral') is-invalid @enderror"
                                    value="{{ old('oral', $checkbody->oral ?? '') }}"
                                    placeholder="เช่น ปกติ / ฟันผุ">
                                @error('oral')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-3">
                                <label class="modern-label">รูปร่าง / ลักษณะ</label>
                                <input type="text" name="appearance"
                                    class="form-control @error('appearance') is-invalid @enderror"
                                    value="{{ old('appearance', $checkbody->appearance ?? '') }}"
                                    placeholder="เช่น สมส่วน / ผอม">
                                @error('appearance')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <label class="modern-label">ร่องรอย / บาดแผล</label>
                    <input type="text" name="wound" class="form-control @error('wound') is-invalid @enderror"
                        value="{{ old('wound', $checkbody->wound ?? '') }}">
                    @error('wound')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-xl-4">
                    <label class="modern-label">โรคประจำตัว</label>
                    <input type="text" name="disease" class="form-control @error('disease') is-invalid @enderror"
                        value="{{ old('disease', $checkbody->disease ?? '') }}">
                    @error('disease')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-xl-4">
                    <label class="modern-label">สุขอนามัย</label>
                    <input type="text" name="hygiene" class="form-control @error('hygiene') is-invalid @enderror"
                        value="{{ old('hygiene', $checkbody->hygiene ?? '') }}">
                    @error('hygiene')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-xl-4">
                    <label class="modern-label">สุขภาพ</label>
                    <input type="text" name="health" class="form-control @error('health') is-invalid @enderror"
                        value="{{ old('health', $checkbody->health ?? '') }}">
                    @error('health')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-xl-4">
                    <label class="modern-label">การปลูกฝี</label>
                    <input type="text" name="inoculation"
                        class="form-control @error('inoculation') is-invalid @enderror"
                        value="{{ old('inoculation', $checkbody->inoculation ?? '') }}">
                    @error('inoculation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-xl-4">
                    <label class="modern-label">การฉีดยา</label>
                    <input type="text" name="injection"
                        class="form-control @error('injection') is-invalid @enderror"
                        value="{{ old('injection', $checkbody->injection ?? '') }}">
                    @error('injection')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-xl-4">
                    <label class="modern-label">การให้วัคซีน</label>
                    <input type="text" name="vaccination"
                        class="form-control @error('vaccination') is-invalid @enderror"
                        value="{{ old('vaccination', $checkbody->vaccination ?? '') }}">
                    @error('vaccination')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-xl-4">
                    <label class="modern-label">โรคติดต่อ</label>
                    <input type="text" name="contagious"
                        class="form-control @error('contagious') is-invalid @enderror"
                        value="{{ old('contagious', $checkbody->contagious ?? '') }}">
                    @error('contagious')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-xl-4">
                    <label class="modern-label">การเจ็บป่วยอื่น ๆ</label>
                    <input type="text" name="other" class="form-control @error('other') is-invalid @enderror"
                        value="{{ old('other', $checkbody->other ?? '') }}">
                    @error('other')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="modern-label">ประวัติการแพ้ยา</label>
                    <input type="text" name="drug_allergy"
                        class="form-control @error('drug_allergy') is-invalid @enderror"
                        value="{{ old('drug_allergy', $checkbody->drug_allergy ?? '') }}">
                    @error('drug_allergy')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="modern-label">หมายเหตุ</label>
                    <textarea name="remark" rows="3" class="form-control @error('remark') is-invalid @enderror"
                        placeholder="ระบุข้อมูลเพิ่มเติม">{{ old('remark', $checkbody->remark ?? '') }}</textarea>
                    @error('remark')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-success btn-modern">
                            <i class="bi bi-save me-1"></i>
                            {{ $isEdit ? 'อัปเดตข้อมูล' : 'บันทึกข้อมูล' }}
                        </button>

                        @if ($isEdit)
                            <a href="{{ route('check_body.add', $client->id) }}"
                                class="btn btn-outline-secondary btn-modern">
                                <i class="bi bi-arrow-left-circle me-1"></i>กลับหน้าหลัก
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
