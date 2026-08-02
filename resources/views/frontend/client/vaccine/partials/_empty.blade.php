@if(request()->filled('start_date') || request()->filled('end_date'))
    <div class="card border-0 shadow-sm vaccine-table-card">
        <div class="card-body">
            <div class="vaccine-filter-empty-state text-center py-5 px-3">
                <div class="vaccine-filter-empty-icon mb-3">
                    <i class="bi bi-search"></i>
                </div>

                <h6 class="fw-bold mb-2">ไม่พบข้อมูลตามช่วงวันที่ที่เลือก</h6>

                <p class="text-muted mb-3 small">
                    ลองเปลี่ยนช่วงวันที่ หรือล้างตัวกรองเพื่อแสดงข้อมูลวัคซีนทั้งหมด
                </p>

                <a href="{{ route('vaccine.index', ['client_id' => $client->id]) }}"
                   class="btn btn-outline-secondary vaccine-btn vaccine-btn-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>ล้างตัวกรอง</span>
                </a>
            </div>
        </div>
    </div>
@else
    <section class="vaccine-first-empty-state" role="status">
        <div class="vaccine-first-empty-icon" aria-hidden="true">
            <i class="bi bi-capsule-pill"></i>
        </div>

        <h2 class="vaccine-first-empty-title">
            ยังไม่มีข้อมูลประวัติการให้วัคซีน
        </h2>

        <p class="vaccine-first-empty-description">
            เริ่มต้นบันทึกประวัติการรับวัคซีนของผู้รับบริการรายนี้
            เพื่อให้ติดตามสุขภาพและการได้รับวัคซีนได้อย่างต่อเนื่อง
        </p>

        <button type="button"
                class="vaccine-first-empty-button"
                data-bs-toggle="modal"
                data-bs-target="#add-vaccine-modal">
            <i class="bi bi-plus-circle"></i>
            <span>เพิ่มข้อมูลวัคซีนครั้งแรก</span>
        </button>
    </section>
@endif

@push('styles')
<style>
.vaccine-page .vaccine-first-empty-state {
    min-height: 320px;
    padding: 2.5rem 1.25rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: #fff;
    border: 1px solid #dbe3ef;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .045);
}

.vaccine-page .vaccine-first-empty-icon,
.vaccine-page .vaccine-filter-empty-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eff6ff;
    color: #2563eb;
}

.vaccine-page .vaccine-first-empty-icon {
    width: 82px;
    height: 82px;
    margin-bottom: 1rem;
    border: 1px solid #bfdbfe;
    border-radius: 50%;
}

.vaccine-page .vaccine-first-empty-icon i {
    font-size: 1.7rem;
    line-height: 1;
}

.vaccine-page .vaccine-filter-empty-icon {
    width: 72px;
    height: 72px;
    margin-right: auto;
    margin-left: auto;
    border-radius: 18px;
    font-size: 1.8rem;
}

.vaccine-page .vaccine-first-empty-title {
    margin: 0;
    color: #0f172a;
    font-size: 1.15rem;
    font-weight: 800;
    line-height: 1.45;
}

.vaccine-page .vaccine-first-empty-description {
    max-width: 720px;
    margin: .55rem auto 1.2rem;
    color: #64748b;
    font-size: .92rem;
    line-height: 1.65;
}

.vaccine-page .vaccine-first-empty-button {
    min-height: 44px;
    padding: .65rem 1.15rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    border: 1px solid #2563eb;
    border-radius: 12px;
    color: #fff;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    box-shadow: 0 9px 20px rgba(37, 99, 235, .22);
    font-weight: 800;
    transition: transform .18s ease, box-shadow .18s ease;
}

.vaccine-page .vaccine-first-empty-button:hover,
.vaccine-page .vaccine-first-empty-button:focus {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 12px 24px rgba(37, 99, 235, .27);
}

@media (max-width: 767.98px) {
    .vaccine-page .vaccine-first-empty-state {
        min-height: 300px;
        padding: 2rem 1rem;
    }
}

@media (max-width: 575.98px) {
    .vaccine-page .vaccine-first-empty-state {
        min-height: 310px;
        padding: 1.8rem .9rem;
    }

    .vaccine-page .vaccine-first-empty-icon {
        width: 72px;
        height: 72px;
    }

    .vaccine-page .vaccine-first-empty-title {
        font-size: 1rem;
    }

    .vaccine-page .vaccine-first-empty-description {
        font-size: .84rem;
    }

    .vaccine-page .vaccine-first-empty-button {
        width: 100%;
    }
}
</style>
@endpush