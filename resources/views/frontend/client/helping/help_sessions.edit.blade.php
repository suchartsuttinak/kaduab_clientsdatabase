@extends('admin_client.admin_client')

@section('content')
@php
    $clientName = $client->fullname ?? $client->full_name ?? '-';
    $sessionItems = $session->items->map(function ($item) {
        return [
            'item_name' => $item->item_name,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
        ];
    })->values()->all();

    $formItems = old('items', $sessionItems);

    if (!is_array($formItems) || count($formItems) === 0) {
        $formItems = [['item_name' => '', 'quantity' => 1, 'unit_price' => '']];
    }
@endphp

<style>
    .help-entry-page {
        --he-primary: #2563eb;
        --he-primary-dark: #1d4ed8;
        --he-text: #0f172a;
        --he-muted: #64748b;
        --he-border: #dbe3ef;
        padding: 1rem 0 3rem;
    }

    .help-entry-shell {
        max-width: 1180px;
        margin: 0 auto;
    }

    .help-entry-header,
    .help-entry-card {
        background: #fff;
        border: 1px solid var(--he-border);
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .045);
    }

    .help-entry-header {
        min-height: 88px;
        padding: 1.1rem 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .help-entry-heading {
        display: flex;
        align-items: center;
        gap: .9rem;
        min-width: 0;
    }

    .help-entry-icon {
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #eff6ff;
        color: var(--he-primary);
        font-size: 1.15rem;
    }

    .help-entry-title {
        margin: 0;
        color: var(--he-text);
        font-size: clamp(1.25rem, 1.6vw, 1.5rem);
        font-weight: 800;
        line-height: 1.35;
    }

    .help-entry-subtitle {
        margin-top: .25rem;
        color: var(--he-muted);
        font-size: clamp(.92rem, 1vw, 1rem);
        line-height: 1.45;
    }

    .help-entry-subtitle strong {
        color: var(--he-text);
        font-weight: 800;
    }

    .help-entry-back,
    .help-entry-add,
    .help-entry-save {
        min-height: 42px;
        padding: .62rem 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        border-radius: 12px;
        font-weight: 750;
        text-decoration: none;
        white-space: nowrap;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .help-entry-back {
        color: #7c3aed;
        background: #fff;
        border: 1px solid #8b5cf6;
    }

    .help-entry-back:hover,
    .help-entry-back:focus {
        color: #6d28d9;
        background: #faf5ff;
        transform: translateY(-1px);
    }

    .help-entry-card {
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .help-entry-card-head {
        min-height: 54px;
        padding: .85rem 1.1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
        background: #f8fafc;
        border-bottom: 1px solid var(--he-border);
    }

    .help-entry-card-title {
        display: flex;
        align-items: center;
        gap: .55rem;
        color: var(--he-text);
        font-size: 1rem;
        font-weight: 800;
    }

    .help-entry-card-title i {
        color: var(--he-primary);
    }

    .help-entry-card-body {
        padding: 1.1rem;
    }

    .help-entry-label {
        margin-bottom: .45rem;
        color: #334155;
        font-weight: 700;
    }

    .help-entry-page .form-control {
        min-height: 44px;
        border: 1px solid #cbd5e1;
        border-radius: 11px;
        color: var(--he-text);
    }

    .help-entry-page .form-control:focus {
        border-color: #93b4ff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .1);
    }

    .help-entry-table-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid var(--he-border);
        border-radius: 14px;
        background: #fff;
    }

    .help-entry-table {
        width: 100%;
        min-width: 920px;
        margin: 0;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
    }

    .help-entry-table th {
        padding: .8rem .7rem;
        color: #334155;
        background: #eff6ff;
        border-bottom: 1px solid #bfdbfe;
        font-size: .9rem;
        font-weight: 800;
        vertical-align: middle;
        white-space: nowrap;
    }

    .help-entry-table td {
        padding: .65rem;
        border-bottom: 1px solid #edf2f7;
        vertical-align: top;
        background: #fff;
    }

    .help-entry-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .help-entry-table .col-item { text-align: left; }
    .help-entry-table .col-quantity { text-align: center; }
    .help-entry-table .col-money { text-align: right; }
    .help-entry-table .col-action { text-align: center; }

    .help-entry-table .quantity-input {
        text-align: center;
        font-variant-numeric: tabular-nums;
    }

    .help-entry-table .money-input,
    .help-entry-table .total-price {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .help-entry-table .total-price {
        color: #0f172a;
        background: #f8fafc;
        font-weight: 800;
    }

    .help-entry-remove {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #fecaca;
        border-radius: 10px;
        color: #b91c1c;
        background: #fef2f2;
    }

    .help-entry-remove:hover,
    .help-entry-remove:focus {
        color: #fff;
        background: #dc2626;
        border-color: #dc2626;
    }

    .help-entry-add {
        color: #1d4ed8;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }

    .help-entry-add:hover,
    .help-entry-add:focus {
        color: #fff;
        background: var(--he-primary);
        border-color: var(--he-primary);
        transform: translateY(-1px);
    }

    .help-entry-summary {
        margin-top: 1rem;
        padding: .85rem 1rem;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .7rem;
        border: 1px solid #dbeafe;
        border-radius: 13px;
        background: #f8fbff;
    }

    .help-entry-summary-label {
        color: var(--he-muted);
        font-weight: 700;
    }

    .help-entry-summary-value {
        color: #1d4ed8;
        font-size: 1.15rem;
        font-weight: 900;
        font-variant-numeric: tabular-nums;
    }

    .help-entry-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .7rem;
        flex-wrap: wrap;
    }

    .help-entry-save {
        min-width: 158px;
        color: #fff;
        border: 1px solid #1d4ed8;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        box-shadow: 0 7px 16px rgba(37, 99, 235, .2);
    }

    .help-entry-save:hover,
    .help-entry-save:focus {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(37, 99, 235, .26);
    }

    .help-entry-save:disabled {
        color: #fff;
        border-color: #1d4ed8;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        opacity: .72;
        cursor: not-allowed;
        transform: none;
    }

    .help-entry-errors {
        margin-bottom: 1rem;
        padding: .9rem 1rem;
        border: 1px solid #fecaca;
        border-radius: 13px;
        color: #991b1b;
        background: #fef2f2;
    }

    @media (max-width: 767.98px) {
        .help-entry-page { padding-top: .75rem; }
        .help-entry-header { padding: 1rem; }
        .help-entry-heading,
        .help-entry-header > .help-entry-back { width: 100%; }
        .help-entry-card-body { padding: .85rem; }
        .help-entry-footer > * { width: 100%; }
    }

    @media (max-width: 575.98px) {
        .help-entry-title { font-size: 1.12rem; }
        .help-entry-subtitle { font-size: .9rem; }
        .help-entry-summary { justify-content: space-between; }
    }
</style>

<div class="container-fluid help-entry-page">
    <div class="help-entry-shell">
        <header class="help-entry-header">
            <div class="help-entry-heading">
                <span class="help-entry-icon" aria-hidden="true">
                    <i class="bi bi-bag-heart-fill"></i>
                </span>

                <div>
                    <h1 class="help-entry-title">แก้ไขข้อมูลการช่วยเหลือ</h1>
                    <div class="help-entry-subtitle">
                        ผู้รับบริการ: <strong>{{ $clientName }}</strong>
                    </div>
                </div>
            </div>

            <a href="{{ route('help_sessions.show', $client->id) }}"
               class="help-entry-back">
                <i class="bi bi-arrow-left-circle"></i>
                <span>กลับ</span>
            </a>
        </header>

        @if($errors->any())
            <div class="help-entry-errors" role="alert">
                <div class="fw-bold mb-1">กรุณาตรวจสอบข้อมูล</div>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('help_sessions.update', ['client' => $client->id, 'session' => $session->id]) }}"
              method="POST"
              id="help-entry-form">
            @csrf
            @method('PUT')

            <section class="help-entry-card">
                <div class="help-entry-card-head">
                    <div class="help-entry-card-title">
                        <i class="bi bi-calendar-check"></i>
                        <span>ข้อมูลการช่วยเหลือ</span>
                    </div>
                </div>

                <div class="help-entry-card-body">
                    <div class="row">
                        <div class="col-12 col-md-5 col-lg-4">
                            <label for="help_date" class="help-entry-label">
                                วันที่ให้ความช่วยเหลือ <span class="text-danger">*</span>
                            </label>

                            <input type="date"
                                   name="help_date"
                                   id="help_date"
                                   class="form-control @error('help_date') is-invalid @enderror"
                                   value="{{ old('help_date', $session->help_date) }}"
                                   max="{{ now('Asia/Bangkok')->toDateString() }}"
                                   required>

                            @error('help_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">เลือกย้อนหลังได้ แต่ต้องไม่เกินวันปัจจุบัน</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="help-entry-card">
                <div class="help-entry-card-head">
                    <div class="help-entry-card-title">
                        <i class="bi bi-box-seam"></i>
                        <span>รายการช่วยเหลือ</span>
                    </div>

                    <button type="button" class="help-entry-add" id="add-row">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มรายการ</span>
                    </button>
                </div>

                <div class="help-entry-card-body">
                    <div class="help-entry-table-wrap">
                        <table class="help-entry-table" id="items-table">
                            <colgroup>
                                <col style="width: 42%;">
                                <col style="width: 12%;">
                                <col style="width: 18%;">
                                <col style="width: 18%;">
                                <col style="width: 10%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="col-item">รายการ</th>
                                    <th class="col-quantity">จำนวน</th>
                                    <th class="col-money">ราคา/หน่วย (บาท)</th>
                                    <th class="col-money">ราคารวม (บาท)</th>
                                    <th class="col-action">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($formItems as $index => $item)
                                    @php
                                        $quantity = $item['quantity'] ?? 1;
                                        $unitPrice = $item['unit_price'] ?? '';
                                        $rowTotal = is_numeric($quantity) && is_numeric($unitPrice)
                                            ? (float) $quantity * (float) $unitPrice
                                            : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="text"
                                                   name="items[{{ $index }}][item_name]"
                                                   class="form-control item-name @error("items.$index.item_name") is-invalid @enderror"
                                                   value="{{ $item['item_name'] ?? '' }}"
                                                   maxlength="255"
                                                   placeholder="เช่น ข้าวสาร เครื่องใช้ หรือค่าเดินทาง"
                                                   required>
                                            @error("items.$index.item_name")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number"
                                                   name="items[{{ $index }}][quantity]"
                                                   class="form-control quantity-input @error("items.$index.quantity") is-invalid @enderror"
                                                   value="{{ $quantity }}"
                                                   min="1"
                                                   max="1000000"
                                                   step="1"
                                                   inputmode="numeric"
                                                   required>
                                            @error("items.$index.quantity")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number"
                                                   name="items[{{ $index }}][unit_price]"
                                                   class="form-control money-input unit-price @error("items.$index.unit_price") is-invalid @enderror"
                                                   value="{{ $unitPrice }}"
                                                   min="0"
                                                   max="999999999.99"
                                                   step="0.01"
                                                   inputmode="decimal"
                                                   placeholder="0.00"
                                                   required>
                                            @error("items.$index.unit_price")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text"
                                                   class="form-control total-price"
                                                   value="{{ $rowTotal !== null ? number_format($rowTotal, 2) : '' }}"
                                                   placeholder="0.00"
                                                   readonly
                                                   tabindex="-1">
                                        </td>
                                        <td class="col-action">
                                            <button type="button"
                                                    class="help-entry-remove remove-row"
                                                    title="ลบรายการ"
                                                    aria-label="ลบรายการ">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @error('items')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    <div class="help-entry-summary">
                        <span class="help-entry-summary-label">ยอดรวมทั้งหมด</span>
                        <span class="help-entry-summary-value" id="grand-total">0.00 บาท</span>
                    </div>
                </div>
            </section>

            <div class="help-entry-footer">
                <a href="{{ route('help_sessions.show', $client->id) }}"
                   class="help-entry-back">
                    <i class="bi bi-x-circle"></i>
                    <span>ยกเลิก</span>
                </a>

                <button type="submit" class="help-entry-save" id="save-btn">
                    <i class="bi bi-check2-circle"></i>
                    <span>บันทึกการแก้ไข</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('help-entry-form');
    const tableBody = document.querySelector('#items-table tbody');
    const addButton = document.getElementById('add-row');
    const saveButton = document.getElementById('save-btn');
    const grandTotalElement = document.getElementById('grand-total');
    const validationErrors = @json($errors->all());
    let rowIndex = {{ count($formItems) }};
    let submitting = false;

    const moneyFormatter = new Intl.NumberFormat('th-TH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    function rowTemplate(index) {
        return `
            <tr>
                <td>
                    <input type="text" name="items[${index}][item_name]"
                           class="form-control item-name" maxlength="255"
                           placeholder="เช่น ข้าวสาร เครื่องใช้ หรือค่าเดินทาง" required>
                </td>
                <td>
                    <input type="number" name="items[${index}][quantity]"
                           class="form-control quantity-input" value="1"
                           min="1" max="1000000" step="1" inputmode="numeric" required>
                </td>
                <td>
                    <input type="number" name="items[${index}][unit_price]"
                           class="form-control money-input unit-price"
                           min="0" max="999999999.99" step="0.01"
                           inputmode="decimal" placeholder="0.00" required>
                </td>
                <td>
                    <input type="text" class="form-control total-price"
                           placeholder="0.00" readonly tabindex="-1">
                </td>
                <td class="col-action">
                    <button type="button" class="help-entry-remove remove-row"
                            title="ลบรายการ" aria-label="ลบรายการ">
                        <i class="bi bi-trash3"></i>
                    </button>
                </td>
            </tr>`;
    }

    function calculateRow(row) {
        const quantity = Number(row.querySelector('.quantity-input')?.value || 0);
        const unitPrice = Number(row.querySelector('.unit-price')?.value || 0);
        const total = quantity > 0 && unitPrice >= 0 ? quantity * unitPrice : 0;
        const totalField = row.querySelector('.total-price');

        if (totalField) {
            totalField.value = Number.isFinite(total) && (quantity > 0)
                ? moneyFormatter.format(total)
                : '';
        }

        return Number.isFinite(total) ? total : 0;
    }

    function calculateGrandTotal() {
        let total = 0;
        tableBody.querySelectorAll('tr').forEach(function (row) {
            total += calculateRow(row);
        });
        grandTotalElement.textContent = moneyFormatter.format(total) + ' บาท';
    }

    addButton.addEventListener('click', function () {
        tableBody.insertAdjacentHTML('beforeend', rowTemplate(rowIndex));
        rowIndex += 1;
        calculateGrandTotal();

        const rows = tableBody.querySelectorAll('tr');
        rows[rows.length - 1]?.querySelector('.item-name')?.focus();
    });

    tableBody.addEventListener('click', function (event) {
        const removeButton = event.target.closest('.remove-row');
        if (!removeButton) return;

        removeButton.closest('tr')?.remove();
        calculateGrandTotal();
    });

    tableBody.addEventListener('input', function (event) {
        if (event.target.matches('.quantity-input, .unit-price')) {
            calculateGrandTotal();
        }
    });

    form.addEventListener('submit', function (event) {
        if (submitting) {
            event.preventDefault();
            return;
        }

        if (tableBody.querySelectorAll('tr').length === 0) {
            event.preventDefault();
            const message = 'กรุณาเพิ่มรายการช่วยเหลืออย่างน้อย 1 รายการ';

            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ยังไม่มีรายการช่วยเหลือ',
                    text: message,
                    confirmButtonText: 'ตกลง'
                });
            } else {
                window.alert(message);
            }
            return;
        }

        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
            form.querySelector(':invalid')?.focus();
            return;
        }

        submitting = true;
        saveButton.disabled = true;
        saveButton.querySelector('span').textContent = 'กำลังบันทึก...';
    });

    calculateGrandTotal();

    if (validationErrors.length && window.Swal) {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาตรวจสอบข้อมูล',
            html: validationErrors.map(function (message) {
                const div = document.createElement('div');
                div.textContent = message;
                return div.innerHTML;
            }).join('<br>'),
            confirmButtonText: 'ตกลง'
        });
    }
});
</script>
@endsection
