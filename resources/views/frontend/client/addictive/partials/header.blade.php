@php
    $addictiveClientDisplayName = filled($client->fullname ?? null)
        ? $client->fullname
        : trim((string) (
            $client->full_name
            ?? $client->name
            ?? trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''))
        ));

    $addictiveClientDisplayName = filled($addictiveClientDisplayName)
        ? $addictiveClientDisplayName
        : '-';

    $showAddictiveFirstEmptyState = $showAddictiveFirstEmptyState
        ?? ((isset($addictives) && $addictives->isEmpty())
            && !request()->filled('date_from')
            && !request()->filled('date_to'));

    $canShowAddictiveFilter = $canShowAddictiveFilter
        ?? (isset($addictives) && $addictives->isNotEmpty());
    $showAddictiveFilter = $showAddictiveFilter ?? false;
    $canAddictiveCreate = $canAddictiveCreate ?? true;
    $canAddictivePrint = $canAddictivePrint ?? true;
@endphp

<div class="ad-header-pro" data-permission-keep>
    <style>
        .addictive-page-v2 .ad-header-pro {
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
            padding: 1.15rem 1.35rem;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            background: linear-gradient(135deg, #eef5ff 0%, #f8fbff 58%, #ffffff 100%);
            box-shadow: 0 10px 28px rgba(37, 99, 235, 0.08);
        }

        .addictive-page-v2 .ad-header-pro::after {
            position: absolute;
            top: -72px;
            right: -58px;
            width: 190px;
            height: 190px;
            border: 26px solid rgba(37, 99, 235, 0.045);
            border-radius: 50%;
            content: "";
            pointer-events: none;
        }

        .addictive-page-v2 .ad-header-pro-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .addictive-page-v2 .ad-header-pro-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
        }

        .addictive-page-v2 .ad-header-pro-icon {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border: 1px solid #bfdbfe;
            border-radius: 18px;
            background: linear-gradient(145deg, #dbeafe, #eff6ff);
            color: #2563eb;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.12);
        }

        .addictive-page-v2 .ad-header-pro-icon i {
            font-size: 1.45rem;
            line-height: 1;
        }

        .addictive-page-v2 .ad-header-pro-text {
            min-width: 0;
        }

        .addictive-page-v2 .ad-header-pro-title {
            margin: 0;
            color: #1e3a5f;
            font-size: 1.28rem;
            font-weight: 800;
            line-height: 1.35;
            letter-spacing: -0.01em;
        }

        .addictive-page-v2 .ad-header-pro-client {
            margin-top: 0.32rem;
            color: #64748b;
            font-size: 0.88rem;
            line-height: 1.5;
        }

        .addictive-page-v2 .ad-header-pro-client span {
            color: #0f172a;
            font-weight: 800;
        }

        .addictive-page-v2 .ad-header-pro-actions {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: flex-end;
            gap: 0.7rem;
            flex-wrap: wrap;
        }

        .addictive-page-v2 .ad-header-pro-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.42rem;
            min-height: 42px;
            padding: 0.55rem 0.95rem;
            border-radius: 12px;
            font-size: 0.86rem;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease,
                        border-color .2s ease, color .2s ease;
        }

        .addictive-page-v2 .ad-header-pro-btn-filter {
            border: 1px solid #cbd5e1;
            background: rgba(255, 255, 255, 0.94);
            color: #334155;
            box-shadow: 0 5px 12px rgba(15, 23, 42, 0.06);
        }

        .addictive-page-v2 .ad-header-pro-btn-filter:hover,
        .addictive-page-v2 .ad-header-pro-btn-filter:focus,
        .addictive-page-v2 .ad-header-pro-btn-filter[aria-expanded="true"] {
            border-color: #93c5fd;
            background: #eff6ff;
            color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.12);
        }

        .addictive-page-v2 .ad-header-pro-btn-report {
            border: 1px solid #93c5fd;
            background: rgba(255, 255, 255, 0.94);
            color: #1d4ed8;
            box-shadow: 0 5px 12px rgba(37, 99, 235, 0.08);
        }

        .addictive-page-v2 .ad-header-pro-btn-report:hover,
        .addictive-page-v2 .ad-header-pro-btn-report:focus {
            border-color: #60a5fa;
            background: #eff6ff;
            color: #1e40af;
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.14);
        }

        .addictive-page-v2 .ad-header-pro-btn-add {
            border: 0;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.20);
        }

        .addictive-page-v2 .ad-header-pro-btn-add:hover,
        .addictive-page-v2 .ad-header-pro-btn-add:focus {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 11px 22px rgba(37, 99, 235, 0.26);
        }

        .addictive-page-v2 .ad-header-pro-btn-back {
            border: 1px solid #8b5cf6;
            background: rgba(255, 255, 255, 0.90);
            color: #7c3aed;
            box-shadow: 0 5px 12px rgba(124, 58, 237, 0.08);
        }

        .addictive-page-v2 .ad-header-pro-btn-back:hover,
        .addictive-page-v2 .ad-header-pro-btn-back:focus {
            border-color: #7c3aed;
            background: #faf5ff;
            color: #6d28d9;
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(124, 58, 237, 0.12);
        }

        .addictive-page-v2 .ad-header-pro-readonly {
            position: absolute;
            top: 0.5rem;
            right: 0.7rem;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.28rem 0.55rem;
            border: 1px solid #dbeafe;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.92);
            color: #475569;
            font-size: 0.7rem;
            font-weight: 700;
            line-height: 1;
        }

        .addictive-page-v2 .ad-header-pro-readonly i {
            color: #2563eb;
        }

        @media (max-width: 767.98px) {
            .addictive-page-v2 .ad-header-pro {
                padding: 1rem;
                border-radius: 16px;
            }

            .addictive-page-v2 .ad-header-pro-inner {
                align-items: stretch;
            }

            .addictive-page-v2 .ad-header-pro-left,
            .addictive-page-v2 .ad-header-pro-actions {
                width: 100%;
            }

            .addictive-page-v2 .ad-header-pro-actions .ad-header-pro-btn {
                flex: 1 1 calc(50% - 0.35rem);
            }
        }

        @media (max-width: 575.98px) {
            .addictive-page-v2 .ad-header-pro-left {
                align-items: flex-start;
                gap: 0.8rem;
            }

            .addictive-page-v2 .ad-header-pro-icon {
                width: 52px;
                height: 52px;
                border-radius: 15px;
            }

            .addictive-page-v2 .ad-header-pro-icon i {
                font-size: 1.25rem;
            }

            .addictive-page-v2 .ad-header-pro-title {
                font-size: 1.05rem;
            }

            .addictive-page-v2 .ad-header-pro-client {
                font-size: 0.78rem;
            }

            .addictive-page-v2 .ad-header-pro-actions {
                flex-direction: column;
            }

            .addictive-page-v2 .ad-header-pro-actions .ad-header-pro-btn {
                width: 100%;
                flex: 1 1 auto;
            }
        }
    </style>

    @if($isAddictiveReadOnly)
        <span class="ad-header-pro-readonly" data-permission-keep>
            <i class="bi bi-eye" aria-hidden="true"></i>
            <span>โหมดอ่านอย่างเดียว</span>
        </span>
    @endif

    <div class="ad-header-pro-inner">
        <div class="ad-header-pro-left">
            <div class="ad-header-pro-icon" aria-hidden="true">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>

            <div class="ad-header-pro-text">
                <h1 class="ad-header-pro-title">ข้อมูลการตรวจสารเสพติด</h1>
                <div class="ad-header-pro-client">
                    ผู้รับบริการ:
                    <span>{{ $addictiveClientDisplayName }}</span>
                </div>
            </div>
        </div>

        <div class="ad-header-pro-actions">
            @if($canShowAddictiveFilter && $canAddictivePrint)
                <button type="button"
                        class="btn ad-header-pro-btn ad-header-pro-btn-filter"
                        data-bs-toggle="collapse"
                        data-bs-target="#addictiveFilterPanel"
                        data-addictive-filter-toggle
                        data-permission-keep
                        aria-controls="addictiveFilterPanel"
                        aria-expanded="{{ $showAddictiveFilter ? 'true' : 'false' }}">
                    <i class="bi {{ $showAddictiveFilter ? 'bi-chevron-up' : 'bi-funnel' }}"
                       data-filter-toggle-icon
                       aria-hidden="true"></i>
                    <span data-filter-toggle-label>
                        {{ $showAddictiveFilter ? 'ซ่อนการค้นหา' : 'ค้นหารายการ' }}
                    </span>
                </button>
            @endif

            @if(!$showAddictiveFirstEmptyState && $canAddictivePrint)
                <a href="{{ route('addictive.report.all', $client->id) }}"
                   class="ad-header-pro-btn ad-header-pro-btn-report"
                   data-permission-action="print">
                    <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                    <span>รายงานรวม</span>
                </a>
            @endif

            @if(!$showAddictiveFirstEmptyState && $canAddictiveCreate)
                <button type="button"
                        class="btn ad-header-pro-btn ad-header-pro-btn-add"
                        data-bs-toggle="modal"
                        data-bs-target="#createAddictiveModal"
                        data-permission-action="create">
                    <i class="bi bi-plus-circle" aria-hidden="true"></i>
                    <span>เพิ่มข้อมูล</span>
                </button>
            @endif

            <a href="{{ route('admin.index', $client->id) }}"
               class="ad-header-pro-btn ad-header-pro-btn-back"
               data-permission-keep
               aria-label="กลับหน้าข้อมูลผู้รับบริการ">
                <i class="bi bi-arrow-left-circle" aria-hidden="true"></i>
                <span>กลับ</span>
            </a>
        </div>
    </div>
</div>
