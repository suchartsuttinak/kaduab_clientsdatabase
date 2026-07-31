@extends('admin_client.admin_client')

@section('content')
    <style>
        .client-file-page {
            --cf-border: #dbe3ee;
            --cf-text: #0f172a;
            --cf-muted: #64748b;
            padding: 16px 0 30px;
        }

        .client-file-page .client-file-card {
            border: 1px solid var(--cf-border);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
            overflow: hidden;
        }

        .client-file-page .client-file-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid var(--cf-border);
            background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
            flex-wrap: wrap;
        }

        .client-file-page .client-file-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            color: var(--cf-text);
            font-size: 1.05rem;
            font-weight: 800;
        }

        .client-file-page .client-file-subtitle {
            margin-top: 3px;
            color: var(--cf-muted);
            font-size: .9rem;
        }

        .client-file-page .client-file-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 28px;
            padding: 0 9px;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: .82rem;
            font-weight: 800;
        }

        .client-file-page .client-file-body {
            padding: 16px 18px 20px;
        }

        .client-file-page .client-file-list {
            border: 1px solid var(--cf-border);
            border-radius: 14px;
            overflow: hidden;
        }

        .client-file-page .client-file-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 16px;
            align-items: center;
            padding: 14px 15px;
            background: #fff;
        }

        .client-file-page .client-file-row + .client-file-row {
            border-top: 1px solid #e7edf5;
        }

        .client-file-page .client-file-row:hover {
            background: #fbfdff;
        }

        .client-file-page .client-file-info {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 0;
        }

        .client-file-page .client-file-icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #fef2f2;
            color: #dc2626;
            font-size: 1.25rem;
        }

        .client-file-page .client-file-type {
            color: #1e40af;
            font-size: .85rem;
            font-weight: 750;
        }

        .client-file-page .client-file-name {
            margin-top: 2px;
            color: var(--cf-text);
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .client-file-page .client-file-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 5px 14px;
            margin-top: 5px;
            color: var(--cf-muted);
            font-size: .82rem;
        }

        .client-file-page .client-file-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 7px;
            flex-wrap: nowrap;
        }

        .client-file-page .client-file-btn {
            min-width: 108px;
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 8px 13px;
            border: 1px solid transparent;
            border-radius: 11px;
            font-size: .86rem;
            font-weight: 750;
            line-height: 1.2;
            text-decoration: none;
            white-space: nowrap;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease;
        }

        .client-file-page .client-file-btn:hover {
            transform: translateY(-1px);
        }

        .client-file-page .client-file-btn:active {
            transform: translateY(0);
        }

        .client-file-page .client-file-btn:focus-visible {
            outline: 0;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .16);
        }

        .client-file-page .client-file-btn-primary {
            min-width: 132px;
            min-height: 44px;
            border-color: #1d4ed8;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 8px 18px rgba(37, 99, 235, .20);
        }

        .client-file-page .client-file-btn-primary:hover,
        .client-file-page .client-file-btn-primary:focus {
            border-color: #1e40af;
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            color: #fff;
            box-shadow: 0 10px 22px rgba(37, 99, 235, .25);
        }

        .client-file-page .client-file-btn-view {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .client-file-page .client-file-btn-view:hover,
        .client-file-page .client-file-btn-view:focus {
            background: #dbeafe;
            color: #1e40af;
        }

        .client-file-page .client-file-btn-download {
            border-color: #cbd5e1;
            background: #fff;
            color: #334155;
        }

        .client-file-page .client-file-btn-download:hover,
        .client-file-page .client-file-btn-download:focus {
            border-color: #94a3b8;
            background: #f1f5f9;
            color: #0f172a;
        }

        .client-file-page .client-file-btn-danger {
            border-color: #fecaca;
            background: #fff1f2;
            color: #be123c;
        }

        .client-file-page .client-file-btn-danger:hover,
        .client-file-page .client-file-btn-danger:focus {
            border-color: #fda4af;
            background: #ffe4e6;
            color: #9f1239;
        }

        .client-file-page .client-file-btn-secondary {
            border-color: #cbd5e1;
            background: #fff;
            color: #475569;
        }

        .client-file-page .client-file-btn-secondary:hover,
        .client-file-page .client-file-btn-secondary:focus {
            border-color: #94a3b8;
            background: #f1f5f9;
            color: #1e293b;
        }

        .client-file-page .client-file-btn:disabled,
        .client-file-page .client-file-btn.is-processing {
            opacity: 1;
            cursor: not-allowed;
            transform: none;
        }

        .client-file-page .client-file-btn-danger:disabled,
        .client-file-page .client-file-btn-danger.is-processing {
            border-color: #fecaca;
            background: #fff1f2;
            color: #be123c;
        }

        .client-file-page .client-file-empty {
            padding: 38px 18px;
            border: 1px dashed #bfdbfe;
            border-radius: 14px;
            background: #f8fbff;
            text-align: center;
        }

        .client-file-page .client-file-empty-icon {
            display: block;
            margin-bottom: 8px;
            color: #60a5fa;
            font-size: 2rem;
        }

        .client-file-page .client-file-bottom {
            display: flex;
            justify-content: flex-start;
            margin-top: 16px;
        }

        @media (max-width: 1199.98px) {
            .client-file-page .client-file-row {
                grid-template-columns: 1fr;
            }

            .client-file-page .client-file-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 767.98px) {
            .client-file-page {
                padding-top: 10px;
            }

            .client-file-page .client-file-header,
            .client-file-page .client-file-body {
                padding-left: 14px;
                padding-right: 14px;
            }

            .client-file-page .client-file-header .client-file-btn-primary {
                width: 100%;
            }

            .client-file-page .client-file-row {
                padding: 13px;
            }

            .client-file-page .client-file-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                width: 100%;
            }

            .client-file-page .client-file-actions form {
                display: contents;
            }

            .client-file-page .client-file-actions .client-file-btn {
                width: 100%;
                min-width: 0;
            }

            .client-file-page .client-file-actions form .client-file-btn {
                grid-column: 1 / -1;
            }

            .client-file-page .client-file-bottom .client-file-btn {
                width: 100%;
            }
        }
    </style>

    @php
        $clientName = $client->full_name ?? $client->fullname ?? '-';
    @endphp

    <div class="container-fluid client-file-page">
        <div class="client-file-card">
            <div class="client-file-header">
                <div>
                    <h5 class="client-file-title">
                        <i class="bi bi-folder2-open text-primary" aria-hidden="true"></i>
                        <span>เอกสารผู้รับบริการ</span>
                        <span class="client-file-count">{{ $client->files->count() }}</span>
                    </h5>
                    <div class="client-file-subtitle">
                        ผู้รับบริการ: <strong class="text-primary">{{ $clientName }}</strong>
                    </div>
                </div>

                <a href="{{ route('client_files.create', $client->id) }}"
                    class="client-file-btn client-file-btn-primary">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    <span>เพิ่มเอกสาร</span>
                </a>
            </div>

            <div class="client-file-body">
                @if ($client->files->isEmpty())
                    <div class="client-file-empty">
                        <i class="bi bi-file-earmark-text client-file-empty-icon" aria-hidden="true"></i>
                        <div class="fw-bold text-dark">ยังไม่มีไฟล์เอกสาร</div>
                        <div class="text-muted small mt-1">กดปุ่ม “เพิ่มเอกสาร” เพื่ออัปโหลดไฟล์ PDF</div>
                    </div>
                @else
                    <div class="client-file-list">
                        @foreach ($client->files as $file)
                            @php
                                $uploadedAt = $file->uploaded_at
                                    ? \Carbon\Carbon::parse($file->uploaded_at)->timezone('Asia/Bangkok')
                                    : null;

                                $thaiUploadedAt = $uploadedAt
                                    ? $uploadedAt->format('d/m/') . ($uploadedAt->year + 543) . ' ' . $uploadedAt->format('H:i') . ' น.'
                                    : '-';
                            @endphp

                            <div class="client-file-row">
                                <div class="client-file-info">
                                    <span class="client-file-icon">
                                        <i class="bi bi-file-earmark-pdf-fill" aria-hidden="true"></i>
                                    </span>

                                    <div class="min-w-0">
                                        <div class="client-file-type">
                                            {{ $fileTypes[$file->file_type] ?? $file->file_type ?? 'ไม่ระบุประเภท' }}
                                        </div>
                                        <div class="client-file-name" title="{{ $file->file_name }}">
                                            {{ $file->file_name ?: 'document.pdf' }}
                                        </div>
                                        <div class="client-file-meta">
                                            <span>
                                                <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>
                                                อัปโหลด {{ $thaiUploadedAt }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="client-file-actions">
                                    <a href="{{ route('client_files.view', [$client->id, $file->id]) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="client-file-btn client-file-btn-view">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                        <span>ดูไฟล์</span>
                                    </a>

                                    <a href="{{ route('client_files.download', [$client->id, $file->id]) }}"
                                        class="client-file-btn client-file-btn-download">
                                        <i class="bi bi-download" aria-hidden="true"></i>
                                        <span>ดาวน์โหลด</span>
                                    </a>

                                    <form action="{{ route('client_files.destroy', [$client->id, $file->id]) }}"
                                        method="POST"
                                        class="delete-file-form"
                                        data-file-name="{{ $file->file_name ?: 'document.pdf' }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="client-file-btn client-file-btn-danger">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                            <span class="delete-file-text">ลบ</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="client-file-bottom">
                    <a href="{{ route('admin.index', $client->id) }}"
                        class="client-file-btn client-file-btn-secondary">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        <span>กลับหน้าหลัก</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));

            if (successMessage && typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: successMessage,
                    confirmButtonText: 'ตกลง',
                    timer: 2600,
                    timerProgressBar: true
                });
            }

            if (errorMessage && typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: errorMessage,
                    confirmButtonText: 'ตกลง'
                });
            }

            document.querySelectorAll('.delete-file-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const button = form.querySelector('button[type="submit"]');
                    const buttonText = form.querySelector('.delete-file-text');
                    const fileName = form.dataset.fileName || 'เอกสารนี้';

                    const submitDelete = function () {
                        if (button?.disabled) {
                            return;
                        }

                        if (button) {
                            button.disabled = true;
                            button.classList.add('is-processing');
                            button.setAttribute('aria-disabled', 'true');
                        }

                        if (buttonText) {
                            buttonText.textContent = 'กำลังลบ...';
                        }

                        HTMLFormElement.prototype.submit.call(form);
                    };

                    if (typeof Swal === 'undefined') {
                        if (window.confirm('คุณต้องการลบไฟล์เอกสารนี้ใช่หรือไม่?')) {
                            submitDelete();
                        }
                        return;
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'ยืนยันการลบเอกสาร',
                        text: 'ไฟล์ “' + fileName + '” จะถูกลบออกจากระบบ',
                        showCancelButton: true,
                        confirmButtonText: 'ลบเอกสาร',
                        cancelButtonText: 'ยกเลิก',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        reverseButtons: true,
                        focusCancel: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            submitDelete();
                        }
                    });
                });
            });
        });
    </script>
@endpush
