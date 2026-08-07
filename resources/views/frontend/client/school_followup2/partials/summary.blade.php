<style>
.client-summary-clean{
    position: relative;
    background: #ffffff;
    border: 1px solid #e8edf3;
    border-radius: 22px;
    padding: 24px 26px 22px;
    margin-bottom: 24px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, .045);
    overflow: hidden;
}

.client-summary-clean::before{
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 4px;
    background: linear-gradient(90deg, #2563eb, #38bdf8, #e2e8f0);
}

.summary-profile{
    display: flex;
    align-items: center;
    gap: 18px;
}

.summary-avatar-image-wrap{
    width: 88px;
    height: 88px;
    border-radius: 50%;
    overflow: hidden;
    flex: 0 0 88px;
    border: 4px solid #eef6ff;
    box-shadow: 0 8px 22px rgba(37, 99, 235, .13);
    background: #f8fafc;
}

.summary-avatar-image{
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.summary-profile-text{
    min-width: 0;
}

.summary-eyebrow{
    display: inline-flex;
    align-items: center;
    font-size: 13px;
    font-weight: 700;
    color: #2563eb;
    background: #eff6ff;
    border: 1px solid #dbeafe;
    border-radius: 999px;
    padding: 5px 12px;
    margin-bottom: 8px;
}

.summary-name{
    margin: 0;
    font-size: 24px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.35;
}

.summary-caption{
    margin-top: 4px;
    color: #64748b;
    font-size: 14px;
}

.summary-divider{
    height: 1px;
    background: linear-gradient(
        90deg,
        rgba(37, 99, 235, .18),
        rgba(226, 232, 240, .9),
        rgba(226, 232, 240, 0)
    );
    margin: 22px 0 20px;
}

.summary-content{
    min-width: 0;
}

.summary-section-title{
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
    font-size: 16px;
    font-weight: 800;
    color: #1e293b;
}

.summary-section-title i{
    color: #2563eb;
    font-size: 18px;
}

.summary-info-grid{
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    column-gap: 34px;
    row-gap: 18px;
}

.summary-info-item{
    position: relative;
    min-width: 0;
    padding: 0 0 13px 0;
    border-bottom: 1px solid #edf2f7;
}

.summary-info-item::after{
    content: "";
    position: absolute;
    left: 0;
    bottom: -1px;
    width: 34px;
    height: 2px;
    border-radius: 999px;
    background: #bfdbfe;
}

.info-label{
    display: block;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 5px;
}

.info-value{
    display: block;
    color: #0f172a;
    font-size: 15px;
    font-weight: 800;
    line-height: 1.45;
    overflow-wrap: anywhere;
}

.summary-note{
    display: flex;
    align-items: flex-start;
    gap: 9px;
    margin-top: 22px;
    padding-top: 16px;
    border-top: 1px dashed #cbd5e1;
    color: #475569;
    font-size: 14px;
    line-height: 1.65;
}

.summary-note i{
    color: #2563eb;
    font-size: 16px;
    margin-top: 3px;
    flex: 0 0 auto;
}

@media (max-width: 1199.98px){
    .summary-info-grid{
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 26px;
    }
}

@media (max-width: 767.98px){
    .client-summary-clean{
        padding: 20px;
        border-radius: 18px;
    }

    .summary-profile{
        align-items: flex-start;
        gap: 14px;
    }

    .summary-avatar-image-wrap{
        width: 74px;
        height: 74px;
        flex-basis: 74px;
        border-width: 3px;
    }

    .summary-name{
        font-size: 20px;
    }

    .summary-caption{
        font-size: 13px;
    }

    .summary-divider{
        margin: 18px 0;
    }

    .summary-info-grid{
        grid-template-columns: 1fr;
        row-gap: 12px;
    }

    .summary-info-item{
        display: grid;
        grid-template-columns: 120px minmax(0, 1fr);
        gap: 12px;
        align-items: start;
        padding-bottom: 10px;
    }

    .summary-info-item::after{
        width: 28px;
    }

    .info-label{
        margin-bottom: 0;
        font-size: 13px;
    }

    .info-value{
        font-size: 14px;
        text-align: right;
    }

    .summary-note{
        font-size: 13px;
        margin-top: 18px;
    }
}

@media (max-width: 430px){
    .summary-profile{
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .summary-info-item{
        grid-template-columns: 1fr;
        gap: 3px;
    }

    .info-value{
        text-align: left;
    }
}
</style>

<div class="client-summary-clean">
    <div class="summary-profile">
        <div class="summary-avatar-image-wrap">
            <img src="{{ $profileImage }}" alt="รูปผู้รับบริการ" class="summary-avatar-image">
        </div>

        <div class="summary-profile-text">
            <div class="summary-eyebrow">ข้อมูลผู้รับบริการ</div>
            <h4 class="summary-name">{{ $clientName }}</h4>
            <div class="summary-caption">
                ข้อมูลสำหรับบันทึกและติดตามผลการเรียน
            </div>
        </div>
    </div>

    <div class="summary-divider"></div>

    <div class="summary-content">
        <div class="summary-section-title">
            <i class="bi bi-person-vcard"></i>
            ข้อมูลพื้นฐาน
        </div>

        <div class="summary-info-grid">
            <div class="summary-info-item">
                <span class="info-label">ชื่อ - สกุล</span>
                <strong class="info-value">{{ $clientName }}</strong>
            </div>

            <div class="summary-info-item">
                <span class="info-label">อายุ</span>
                <strong class="info-value">{{ $clientAge }} ปี</strong>
            </div>

            <div class="summary-info-item">
                <span class="info-label">สถานศึกษา</span>
                <strong class="info-value">{{ $schoolName }}</strong>
            </div>

            <div class="summary-info-item">
                <span class="info-label">ระดับชั้น</span>
                <strong class="info-value">{{ $educationName }}</strong>
            </div>

            <div class="summary-info-item">
                <span class="info-label">ภาคเรียน</span>
                <strong class="info-value">{{ $semesterName }}</strong>
            </div>

            <div class="summary-info-item">
                <span class="info-label">จำนวนรายการติดตาม</span>
                <strong class="info-value">{{ $followups->count() }} รายการ</strong>
            </div>
        </div>

        <div class="summary-note">
            <i class="bi bi-info-circle"></i>
            <span>
                หน้านี้ใช้สำหรับบันทึกและติดตามผลการเรียนของผู้รับบริการ พร้อมรองรับการแก้ไขและออกรายงานภายหลัง
            </span>
        </div>
    </div>
</div>