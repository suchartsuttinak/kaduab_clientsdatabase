<div class="card border-0 shadow-sm vaccine-table-card">
    <div class="card-body">
        <div class="vaccine-empty-state text-center py-5 px-3">
            <div class="vaccine-empty-state-icon mb-3">
                <i class="bi {{ request()->filled('start_date') || request()->filled('end_date') ? 'bi-search' : 'bi-capsule' }}"></i>
            </div>

            @if(request()->filled('start_date') || request()->filled('end_date'))
                <h6 class="fw-bold mb-2">ไม่พบข้อมูลตามช่วงวันที่ที่เลือก</h6>
                <p class="text-muted mb-3 small">
                    ลองเปลี่ยนช่วงวันที่ หรือล้างตัวกรองเพื่อแสดงข้อมูลวัคซีนทั้งหมด
                </p>
                <a href="{{ route('vaccine.index', ['client_id' => $client->id]) }}"
                   class="btn btn-outline-secondary vaccine-btn vaccine-btn-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>ล้างตัวกรอง</span>
                </a>
            @else
                <h6 class="fw-bold mb-2">ยังไม่มีข้อมูลวัคซีน</h6>
                <p class="text-muted mb-3 small">
                    เริ่มต้นเพิ่มข้อมูลวัคซีนเพื่อให้ระบบติดตามได้ครบถ้วน
                </p>
                <button type="button"
                        class="btn btn-primary vaccine-btn vaccine-btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#add-vaccine-modal">
                    <i class="bi bi-plus-circle"></i>
                    <span>เพิ่มข้อมูล</span>
                </button>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.vaccine-page .vaccine-empty-state-icon {
    width:72px;
    height:72px;
    margin:0 auto;
    border-radius:18px;
    background:#eff6ff;
    color:var(--vaccine-primary);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.8rem;
}
</style>
@endpush
