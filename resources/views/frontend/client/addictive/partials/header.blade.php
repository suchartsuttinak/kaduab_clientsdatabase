<div>
    <style>
        /* =========================================================
           HEADER
        ========================================================= */
        .addictive-header-pro {
            position: relative;
            overflow: hidden;
            padding: 1.15rem 1.35rem;
            border-bottom: 1px solid #dbeafe;
            border-radius: 18px 18px 0 0;
            background:
                linear-gradient(135deg, #eef5ff 0%, #f8fbff 58%, #ffffff 100%);
        }

        .addictive-header-pro::after {
            content: "";
            position: absolute;
            right: -58px;
            top: -72px;
            width: 190px;
            height: 190px;
            border: 26px solid rgba(37, 99, 235, 0.045);
            border-radius: 50%;
            pointer-events: none;
        }

        .addictive-header-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .addictive-header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
        }

        .addictive-header-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            background: linear-gradient(145deg, #dbeafe, #eff6ff);
            color: #2563eb;
            border: 1px solid #bfdbfe;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.12);
        }

        .addictive-header-icon i {
            font-size: 1.45rem;
            line-height: 1;
        }

        .addictive-header-text {
            min-width: 0;
        }

        .addictive-header-title {
            margin: 0;
            font-size: 1.28rem;
            font-weight: 800;
            color: #1e3a5f;
            line-height: 1.35;
            letter-spacing: -0.01em;
        }

        .addictive-header-sub {
            margin-top: 0.32rem;
            font-size: 0.88rem;
            color: #64748b;
            line-height: 1.5;
        }

        .addictive-header-sub strong {
            color: #0f172a;
            font-weight: 800;
        }

        .addictive-header-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.7rem;
            flex-wrap: wrap;
            flex: 0 0 auto;
        }

        .addictive-header-btn,
        .addictive-back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.42rem;
            min-height: 42px;
            padding: 0.55rem 0.95rem;
            border-radius: 12px;
            font-size: 0.86rem;
            font-weight: 700;
            white-space: nowrap;
            text-decoration: none;
            transition:
                transform .2s ease,
                box-shadow .2s ease,
                background-color .2s ease,
                border-color .2s ease,
                color .2s ease;
        }

        .addictive-header-btn {
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.20);
        }

        .addictive-header-btn:hover,
        .addictive-header-btn:focus {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 11px 22px rgba(37, 99, 235, 0.26);
        }

        .addictive-back-btn {
            color: #7c3aed;
            border: 1px solid #8b5cf6;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 5px 12px rgba(124, 58, 237, 0.08);
        }

        .addictive-back-btn:hover,
        .addictive-back-btn:focus {
            color: #6d28d9;
            background: #faf5ff;
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(124, 58, 237, 0.12);
        }

        /* =========================================================
           EMPTY STATE
           ต้องคงส่วนนี้ไว้ เพราะ addictive_create.blade.php เรียกใช้
        ========================================================= */
        .addictive-empty-state {
            min-height: 318px;
            margin: 1.85rem 1rem 1rem;
            padding: 2.5rem 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }

        .addictive-empty-icon {
            width: 82px;
            height: 82px;
            margin-bottom: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border: 1px solid #bfdbfe;
            border-radius: 50%;
            background: #eff6ff;
            color: #2563eb;
        }

        .addictive-empty-icon i {
            font-size: 1.7rem;
            line-height: 1;
        }

        .addictive-empty-title {
            margin: 0;
            color: #0f172a;
            font-size: 1.12rem;
            font-weight: 800;
            line-height: 1.45;
        }

        .addictive-empty-description {
            max-width: 700px;
            margin: 0.55rem auto 1.15rem;
            color: #64748b;
            font-size: 0.92rem;
            line-height: 1.65;
        }

        .addictive-empty-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 44px;
            padding: 0.65rem 1.15rem;
            border-radius: 12px;
            font-weight: 800;
            box-shadow: 0 9px 20px rgba(37, 99, 235, 0.22);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .addictive-empty-button:hover,
        .addictive-empty-button:focus {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.26);
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */
        @media (max-width: 767.98px) {
            .addictive-header-pro {
                padding: 1rem;
                border-radius: 16px 16px 0 0;
            }

            .addictive-header-inner {
                align-items: stretch;
            }

            .addictive-header-left,
            .addictive-header-right {
                width: 100%;
            }

            .addictive-header-right > * {
                flex: 1 1 calc(50% - 0.35rem);
            }

            .addictive-empty-state {
                min-height: 300px;
                margin: 1rem 0.75rem 0.75rem;
                padding: 2rem 1rem;
            }
        }

        @media (max-width: 575.98px) {
            .addictive-header-left {
                align-items: flex-start;
                gap: 0.8rem;
            }

            .addictive-header-icon {
                width: 52px;
                height: 52px;
                border-radius: 15px;
            }

            .addictive-header-icon i {
                font-size: 1.25rem;
            }

            .addictive-header-title {
                font-size: 1.05rem;
            }

            .addictive-header-sub {
                font-size: 0.78rem;
            }

            .addictive-header-right {
                flex-direction: column;
            }

            .addictive-header-right > * {
                width: 100%;
                flex: 1 1 auto;
            }

            .addictive-empty-state {
                min-height: 280px;
                margin: 0.75rem;
                padding: 1.75rem 0.9rem;
            }

            .addictive-empty-icon {
                width: 72px;
                height: 72px;
            }

            .addictive-empty-title {
                font-size: 1rem;
            }

            .addictive-empty-description {
                font-size: 0.84rem;
            }

            .addictive-empty-button {
                width: 100%;
            }
        }
    </style>

    <div class="addictive-header-pro">
        <div class="addictive-header-inner">

            <div class="addictive-header-left">
                <div class="addictive-header-icon">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>

                <div class="addictive-header-text">
                    <h6 class="addictive-header-title">
                        ข้อมูลการตรวจสารเสพติด
                    </h6>

                    <div class="addictive-header-sub">
                        ผู้รับบริการ:
                        <strong>{{ $client->fullname ?: '-' }}</strong>
                    </div>
                </div>
            </div>

            <div class="addictive-header-right">
                @if($hasAddictiveData ?? false)
                    <button type="button"
                            class="btn addictive-header-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#createAddictiveModal">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูล</span>
                    </button>
                @endif

                <a href="{{ route('admin.index', $client->id) }}"
                   class="addictive-back-btn"
                   aria-label="กลับหน้าหลักผู้รับบริการ">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับ</span>
                </a>
            </div>

        </div>
    </div>
</div>
