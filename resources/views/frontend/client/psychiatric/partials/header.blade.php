@php
    $psychiatricClientDisplayName = filled($client->fullname ?? null)
        ? $client->fullname
        : trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));

    $isPsychiatricFirstEmptyState = $showPsychiatricFirstEmptyState
        ?? ((isset($psychiatrics) && $psychiatrics->isEmpty())
            && !request()->filled('start_date')
            && !request()->filled('end_date'));
@endphp

<div class="card-header psychiatric-header p-0 border-0 bg-transparent">
    <style>
        .psy-header-pro {
            position: relative;
            overflow: hidden;
            padding: 1.15rem 1.35rem;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            background:
                linear-gradient(135deg, #eef5ff 0%, #f8fbff 58%, #ffffff 100%);
            box-shadow: 0 10px 28px rgba(37, 99, 235, 0.08);
        }

        .psy-header-pro::after {
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

        .psy-header-pro-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .psy-header-pro-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
        }

        .psy-header-pro-icon {
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

        .psy-header-pro-icon i {
            font-size: 1.45rem;
            line-height: 1;
        }

        .psy-header-pro-text {
            min-width: 0;
        }

        .psy-header-pro-title {
            margin: 0;
            color: #1e3a5f;
            font-size: 1.28rem;
            font-weight: 800;
            line-height: 1.35;
            letter-spacing: -0.01em;
        }

        .psy-header-pro-client {
            margin-top: 0.32rem;
            color: #64748b;
            font-size: 0.88rem;
            line-height: 1.5;
        }

        .psy-header-pro-client span {
            color: #0f172a;
            font-weight: 800;
        }

        .psy-header-pro-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.7rem;
            flex-wrap: wrap;
            flex: 0 0 auto;
        }

        .psy-header-pro-btn {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.42rem;
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

        .psy-header-pro-btn-add {
            border: 0;
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.20);
        }

        .psy-header-pro-btn-add:hover,
        .psy-header-pro-btn-add:focus {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 11px 22px rgba(37, 99, 235, 0.26);
        }

        .psy-header-pro-btn-back {
            color: #7c3aed;
            background: rgba(255, 255, 255, 0.90);
            border: 1px solid #8b5cf6;
            box-shadow: 0 5px 12px rgba(124, 58, 237, 0.08);
        }

        .psy-header-pro-btn-back:hover,
        .psy-header-pro-btn-back:focus {
            color: #6d28d9;
            background: #faf5ff;
            border-color: #7c3aed;
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(124, 58, 237, 0.12);
        }

        @media (max-width: 767.98px) {
            .psy-header-pro {
                padding: 1rem;
                border-radius: 16px;
            }

            .psy-header-pro-inner {
                align-items: stretch;
            }

            .psy-header-pro-left,
            .psy-header-pro-actions {
                width: 100%;
            }

            .psy-header-pro-actions .psy-header-pro-btn {
                flex: 1 1 calc(50% - 0.35rem);
            }
        }

        @media (max-width: 575.98px) {
            .psy-header-pro-left {
                align-items: flex-start;
                gap: 0.8rem;
            }

            .psy-header-pro-icon {
                width: 52px;
                height: 52px;
                border-radius: 15px;
            }

            .psy-header-pro-icon i {
                font-size: 1.25rem;
            }

            .psy-header-pro-title {
                font-size: 1.05rem;
            }

            .psy-header-pro-client {
                font-size: 0.78rem;
            }

            .psy-header-pro-actions {
                flex-direction: column;
            }

            .psy-header-pro-actions .psy-header-pro-btn {
                width: 100%;
                flex: 1 1 auto;
            }
        }
    </style>

    <div class="psy-header-pro">
        <div class="psy-header-pro-inner">
            <div class="psy-header-pro-left">
                <div class="psy-header-pro-icon" aria-hidden="true">
                    <i class="bi bi-clipboard-check"></i>
                </div>

                <div class="psy-header-pro-text">
                    <h5 class="psy-header-pro-title">
                        ข้อมูลการตรวจวินิจฉัยทางจิตเวช
                    </h5>

                    <div class="psy-header-pro-client">
                        ผู้รับบริการ:
                        <span>{{ $psychiatricClientDisplayName ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="psy-header-pro-actions">
                @unless($isPsychiatricFirstEmptyState)
                    <button type="button"
                            class="btn psy-header-pro-btn psy-header-pro-btn-add"
                            data-bs-toggle="modal"
                            data-bs-target="#createPsychiatricModal"
                            id="btn-create-psychiatric">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูล</span>
                    </button>
                @endunless

                <a href="{{ route('client.show', $client->id) }}"
                   class="psy-header-pro-btn psy-header-pro-btn-back"
                   aria-label="กลับหน้าผู้รับบริการ">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับ</span>
                </a>
            </div>
        </div>
    </div>
</div>