@php
    $isEdit = isset($accident) && $accident;
    $oldTreatNo = old('treat_no', $accident->treat_no ?? '');
@endphp

<div class="form-card card mb-3">
    <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
        <div>
            <h5 class="section-title">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                {{ $isEdit ? 'แก้ไขข้อมูลการบาดเจ็บ' : 'ฟอร์มบันทึกข้อมูลการบาดเจ็บ' }}
            </h5>
            <p class="section-subtitle">
                กรุณากรอกข้อมูลให้ครบถ้วน โดยเฉพาะวันเกิดเหตุ สถานที่ รายละเอียด และวันที่บันทึก
            </p>
        </div>
        <div>
            <span class="badge text-bg-light border">
                {{ $isEdit ? 'Editing Mode' : 'Create Mode' }}
            </span>
        </div>
    </div>

    <div class="card-body">
        <form
            id="accident-form"
            method="POST"
            action="{{ $isEdit ? route('accident.update', $accident->id) : route('accident.store') }}"
        >
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <input type="hidden" name="client_id" value="{{ $client->id }}">

            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <label class="modern-label">วันที่เกิดเหตุ <span class="required">*</span></label>
                  <input
                        type="date"
                        name="incident_date"
                        class="form-control @error('incident_date') is-invalid @enderror"
                        value="{{ old('incident_date', \App\Helpers\ThaiDateHelper::toInputDate($accident->incident_date ?? null)) }}"
                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                    >
                 
                    @error('incident_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-xl-5">
                    <label class="modern-label">สถานที่เกิดเหตุ <span class="required">*</span></label>
                    <input
                        type="text"
                        name="location"
                        class="form-control @error('location') is-invalid @enderror"
                        value="{{ old('location', $accident->location ?? '') }}"
                        placeholder="เช่น ห้องเรียน สนามเด็กเล่น หอพัก"
                    >
                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-xl-4">
                    <label class="modern-label">ผู้พบเห็นเหตุการณ์</label>
                    <input
                        type="text"
                        name="eyewitness"
                        class="form-control @error('eyewitness') is-invalid @enderror"
                        value="{{ old('eyewitness', $accident->eyewitness ?? '') }}"
                        placeholder="ระบุชื่อผู้พบเห็น"
                    >
                    @error('eyewitness')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="modern-label">รายละเอียดการบาดเจ็บ <span class="required">*</span></label>
                    <textarea
                        name="detail"
                        rows="4"
                        class="form-control @error('detail') is-invalid @enderror"
                        placeholder="อธิบายลักษณะเหตุการณ์ อวัยวะที่บาดเจ็บ หรือความรุนแรงโดยสังเขป"
                    >{{ old('detail', $accident->detail ?? '') }}</textarea>
                    @error('detail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="modern-label">สาเหตุของการบาดเจ็บ <span class="required">*</span></label>
                    <input
                        type="text"
                        name="cause"
                        class="form-control @error('cause') is-invalid @enderror"
                        value="{{ old('cause', $accident->cause ?? '') }}"
                        placeholder="เช่น ลื่นล้ม ชนของแข็ง ถูกของมีคมบาด"
                    >
                    @error('cause')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="modern-label d-block">การพบแพทย์ <span class="required">*</span></label>

                    <div class="radio-card-group">
                        <div class="radio-card">
                            <input
                                type="radio"
                                name="treat_no"
                                id="treat_yes"
                                value="พบแพทย์"
                                {{ $oldTreatNo === 'พบแพทย์' ? 'checked' : '' }}
                            >
                            <label for="treat_yes">
                                <span class="icon-wrap"><i class="bi bi-hospital"></i></span>
                                <span>พบแพทย์</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input
                                type="radio"
                                name="treat_no"
                                id="treat_no_option"
                                value="ไม่พบแพทย์"
                                {{ $oldTreatNo === 'ไม่พบแพทย์' ? 'checked' : '' }}
                            >
                            <label for="treat_no_option">
                                <span class="icon-wrap"><i class="bi bi-house-heart"></i></span>
                                <span>ไม่พบแพทย์</span>
                            </label>
                        </div>
                    </div>

                    @error('treat_no')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div id="medical-section" class="medical-box">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="modern-label">สถานพยาบาล</label>
                                <input
                                    type="text"
                                    name="hospital"
                                    class="form-control @error('hospital') is-invalid @enderror"
                                    value="{{ old('hospital', $accident->hospital ?? '') }}"
                                    placeholder="เช่น โรงพยาบาล..."
                                >
                                @error('hospital')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="modern-label">ผลวินิจฉัย</label>
                                <input
                                    type="text"
                                    name="diagnosis"
                                    class="form-control @error('diagnosis') is-invalid @enderror"
                                    value="{{ old('diagnosis', $accident->diagnosis ?? '') }}"
                                    placeholder="ผลการตรวจหรือวินิจฉัย"
                                >
                                @error('diagnosis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="modern-label">แพทย์นัดครั้งต่อไป</label>
                                <input
                                    type="date"
                                    name="appointment"
                                    class="form-control @error('appointment') is-invalid @enderror"
                                   value="{{ old('appointment', \App\Helpers\ThaiDateHelper::toInputDate($accident->appointment ?? null)) }}"
                                >
                                @error('appointment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="modern-label">การรักษา</label>
                    <textarea
                        name="treatment"
                        rows="3"
                        class="form-control @error('treatment') is-invalid @enderror"
                        placeholder="เช่น ทำแผล ทายา รับประทานยา พักสังเกตอาการ"
                    >{{ old('treatment', $accident->treatment ?? '') }}</textarea>
                    @error('treatment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="modern-label">การป้องกัน/การแก้ไข</label>
                    <textarea
                        name="protection"
                        rows="3"
                        class="form-control @error('protection') is-invalid @enderror"
                        placeholder="ระบุมาตรการป้องกันไม่ให้เกิดซ้ำ"
                    >{{ old('protection', $accident->protection ?? '') }}</textarea>
                    @error('protection')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="modern-label">ผู้ดูแล</label>
                    <input
                        type="text"
                        name="caretaker"
                        class="form-control @error('caretaker') is-invalid @enderror"
                        value="{{ old('caretaker', $accident->caretaker ?? '') }}"
                        placeholder="ระบุชื่อผู้ดูแล"
                    >
                    @error('caretaker')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="modern-label">วันที่บันทึก <span class="required">*</span></label>
                   <input
                        type="date"
                        name="record_date"
                        class="form-control @error('record_date') is-invalid @enderror"
                        value="{{ old('record_date', !empty($accident->record_date) ? \Carbon\Carbon::parse($accident->record_date)->format('Y-m-d') : now('Asia/Bangkok')->toDateString()) }}"
                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                    >
                    @error('record_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <div class="d-flex flex-wrap gap-2 w-100">
                        <button type="submit" class="btn btn-success btn-modern">
                            <i class="bi bi-save me-1"></i>
                            {{ $isEdit ? 'อัปเดตข้อมูล' : 'บันทึกข้อมูล' }}
                        </button>

                        @if($isEdit)
                            <a href="{{ route('accident.add', $client->id) }}" class="btn btn-outline-secondary btn-modern">
                                <i class="bi bi-arrow-left-circle me-1"></i>กลับหน้าหลัก
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>