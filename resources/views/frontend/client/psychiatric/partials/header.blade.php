<div class="card-header psychiatric-header">
    <style>
        .psy-header-pro {
            padding: 1rem 1.1rem;
            border-bottom: 1px solid #e2e8f0;
            background:
                linear-gradient(135deg, #f8fbff 0%, #eef5ff 60%, #ffffff 100%);
        }

        .psy-header-pro-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.9rem;
            flex-wrap: wrap;
        }

        .psy-header-pro-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .psy-header-pro-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #2563eb;
            flex: 0 0 auto;
        }

        .psy-header-pro-icon i {
            font-size: 1rem;
        }

        .psy-header-pro-text {
            min-width: 0;
        }

        .psy-header-pro-title {
            margin: 0;
            font-size: 0.98rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.35;
        }

        .psy-header-pro-sub {
            margin-top: 0.15rem;
            font-size: 0.78rem;
            color: #64748b;
            line-height: 1.4;
        }

        .psy-header-pro-right {
            flex: 0 0 auto;
        }

        .psy-header-pro-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.8rem;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.15);
            white-space: nowrap;
        }

        /* hover */
        .psy-header-pro-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.2);
        }

        /* mobile */
        @media (max-width: 575.98px) {
            .psy-header-pro {
                padding: 0.9rem;
            }

            .psy-header-pro-inner {
                flex-direction: column;
                align-items: stretch;
            }

            .psy-header-pro-right {
                width: 100%;
            }

            .psy-header-pro-btn {
                width: 100%;
                justify-content: center;
            }

            .psy-header-pro-title {
                font-size: 0.92rem;
            }

            .psy-header-pro-sub {
                font-size: 0.75rem;
            }
        }
    </style>

    <div class="psy-header-pro">
        <div class="psy-header-pro-inner">

            <!-- LEFT -->
            <div class="psy-header-pro-left">
                <div class="psy-header-pro-icon">
                    <i class="bi bi-clipboard-check"></i>
                </div>

                <div class="psy-header-pro-text">
                    <h6 class="psy-header-pro-title">
                        ข้อมูลการตรวจวินิจฉัยทางจิตเวช
                    </h6>
                    <div class="psy-header-pro-sub">
                        บันทึกผลการตรวจ วินิจฉัย และการติดตามผู้รับบริการ
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="psy-header-pro-right">
                <button type="button"
                        class="btn btn-primary psy-header-pro-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#createPsychiatricModal"
                        id="btn-create-psychiatric">
                    <i class="bi bi-plus-circle"></i>
                    <span>เพิ่มข้อมูล</span>
                </button>
            </div>

        </div>
    </div>
</div>