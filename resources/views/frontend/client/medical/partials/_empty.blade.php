<div class="card border-0 shadow-sm medical-table-card">
    <div class="card-body">
        <div class="medical-empty-state text-center py-5 px-3">
            <div class="medical-empty-state-icon mb-3">
                <i class="bi bi-clipboard-x"></i>
            </div>
            @if(request()->filled('start_date') || request()->filled('end_date') || old('start_date') || old('end_date'))
                <h6 class="fw-bold mb-2">ไม่พบข้อมูลในช่วงวันที่ที่เลือก</h6>
                <p class="text-muted mb-3 small">
                    ลองเปลี่ยนช่วงวันที่ หรือล้างค่าตัวกรองเพื่อดูข้อมูลทั้งหมด
                </p>
            @else
                <h6 class="fw-bold mb-2">ยังไม่มีข้อมูลการรักษาพยาบาล</h6>
                <p class="text-muted mb-3 small">
                    เริ่มต้นเพิ่มข้อมูลการรักษาเพื่อให้ระบบติดตามสุขภาพได้ครบถ้วน
                </p>
            @endif
            <button type="button"
                    class="btn btn-primary medical-btn medical-btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#add-medical-modal">
                <i class="bi bi-plus-circle"></i>
                <span>เพิ่มข้อมูล</span>
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
.medical-page .medical-empty-state-icon {
    width: 72px;
    height: 72px;
    margin: 0 auto;
    border-radius: 18px;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}
</style>
@endpush