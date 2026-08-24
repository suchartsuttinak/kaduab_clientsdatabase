{{-- IDP_VALIDATION_COMPLETE_V1 --}}
@once
<style>
    .idp-validation-summary {
        border: 1px solid #fecaca;
        background: #fff7f7;
        color: #991b1b;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 16px;
        font-size: .92rem;
        line-height: 1.6;
    }
    .idp-validation-summary strong { display: block; margin-bottom: 4px; }
    .idp-validation-summary ul { margin: 0; padding-left: 1.2rem; }
    .idp-auto-feedback { display: block; }
    .idp-invalid-group {
        border: 1px solid #dc3545 !important;
        border-radius: 10px;
        padding: 8px;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    if (window.__IDP_VALIDATION_COMPLETE_V1__) return;
    window.__IDP_VALIDATION_COMPLETE_V1__ = true;

    const TODAY = @json(now('Asia/Bangkok')->format('Y-m-d'));
    const SERVER_ERRORS = @json($errors->toArray());

    const FIELD_LABELS = {
        start_date: 'วันที่เริ่มแผน',
        end_date: 'วันที่สิ้นสุดแผน',
        overall_goal: 'เป้าหมายภาพรวม',
        assessment_date: 'วันที่ประเมิน',
        domain_id: 'ด้านพัฒนา',
        indicator_id: 'ตัวชี้วัด',
        title: 'ชื่อเป้าหมาย',
        target_level: 'ระดับเป้าหมาย',
        success_indicator: 'ตัวชี้วัดความสำเร็จ',
        priority: 'ระดับความสำคัญ',
        target_date: 'กำหนดเสร็จ',
        activity_date: 'วันที่เริ่มกิจกรรม',
        detail: 'รายละเอียดกิจกรรม',
        status: 'สถานะ',
        followup_date: 'วันที่ติดตาม',
        overall_result: 'ผลติดตามภาพรวม',
        next_action: 'สิ่งที่ต้องทำต่อ',
        close_reason: 'เหตุผล/เกณฑ์ในการปิดแผน',
        cancel_reason: 'เหตุผล',
        reason: 'เหตุผล',
        final_outcome: 'ผลลัพธ์สุดท้าย',
        coordination_date: 'วันที่ประสาน',
        expiry_date: 'วันที่หมดอายุ'
    };

    const REQUIRED_NAMES = [
        'start_date', 'overall_goal',
        'assessment_date',
        'domain_id', 'title', 'target_level', 'success_indicator', 'priority',
        'activity_date', 'detail', 'status',
        'followup_date', 'overall_result'
    ];

    const HISTORICAL_DATE_NAMES = [
        'start_date', 'assessment_date', 'followup_date', 'coordination_date'
    ];

    function isWriteForm(form) {
        const method = String(form.getAttribute('method') || 'GET').toUpperCase();
        if (method === 'GET') return false;
        const override = form.querySelector('input[name="_method"]');
        if (override && String(override.value).toUpperCase() === 'DELETE') return false;
        return true;
    }

    function escapeAttr(value) {
        return String(value).replace(/\\/g, '\\\\').replace(/"/g, '\\"');
    }

    function dotToBracket(key) {
        const parts = String(key).split('.');
        if (parts.length < 2) return key;
        return parts.shift() + parts.map(function (part) { return '[' + part + ']'; }).join('');
    }

    function baseArrayName(key) {
        const match = String(key).match(/^([^.]+)\.\d+(?:\.|$)/);
        return match ? match[1] + '[]' : null;
    }

    function findFields(form, name) {
        const selector = '[name="' + escapeAttr(name) + '"]';
        try { return Array.from(form.querySelectorAll(selector)); }
        catch (error) { return []; }
    }

    function findFieldForError(key) {
        const candidates = [String(key), dotToBracket(key)];
        const arrayName = baseArrayName(key);
        if (arrayName) candidates.push(arrayName);

        for (const form of document.querySelectorAll('form')) {
            for (const name of candidates) {
                const fields = findFields(form, name);
                if (fields.length) return { form: form, field: fields[0], fields: fields };
            }
        }
        return null;
    }

    function labelFor(key) {
        const root = String(key).split('.')[0].replace(/\[.*$/, '');
        return FIELD_LABELS[root] || root.replace(/_/g, ' ');
    }

    function feedbackContainer(field) {
        return field.closest('.mb-3, .form-group, .col, [class*="col-"], .idp-field, .idp-indicator, .idp-score-wrap') || field.parentElement;
    }

    function appendFeedback(field, message) {
        if (!field) return;
        field.classList.add('is-invalid');
        field.setAttribute('aria-invalid', 'true');
        const container = feedbackContainer(field);
        if (!container) return;

        const existing = container.querySelector('.idp-auto-feedback');
        if (existing) {
            existing.textContent = message;
            return;
        }

        const div = document.createElement('div');
        div.className = 'invalid-feedback idp-auto-feedback';
        div.textContent = message;
        container.appendChild(div);
    }

    function clearAutoError(field) {
        if (!field) return;
        if (field.validity && field.validity.valid) {
            field.classList.remove('is-invalid');
            field.removeAttribute('aria-invalid');
            const container = feedbackContainer(field);
            const feedback = container ? container.querySelector('.idp-auto-feedback') : null;
            if (feedback) feedback.remove();
        }
    }

    function addServerSummary() {
        const entries = Object.entries(SERVER_ERRORS || {});
        if (!entries.length) return;

        entries.forEach(function ([key, messages]) {
            const found = findFieldForError(key);
            if (found && found.field) appendFeedback(found.field, Array.isArray(messages) ? messages[0] : String(messages));
        });

        const writeForms = Array.from(document.querySelectorAll('form')).filter(isWriteForm);
        const targetForm = writeForms.find(function (form) {
            return entries.some(function ([key]) {
                return !!findFieldInFormForKey(form, key);
            });
        }) || writeForms[0];

        if (!targetForm) return;
        if (targetForm.querySelector('.idp-validation-summary') || targetForm.querySelector('.alert-danger')) return;

        const box = document.createElement('div');
        box.className = 'idp-validation-summary';
        box.setAttribute('role', 'alert');
        const strong = document.createElement('strong');
        strong.textContent = 'กรุณาตรวจสอบข้อมูลก่อนบันทึก';
        box.appendChild(strong);
        const ul = document.createElement('ul');
        entries.slice(0, 8).forEach(function ([key, messages]) {
            const li = document.createElement('li');
            const msg = Array.isArray(messages) ? messages[0] : String(messages);
            li.textContent = msg || ('กรุณาตรวจสอบ ' + labelFor(key));
            ul.appendChild(li);
        });
        box.appendChild(ul);
        targetForm.insertBefore(box, targetForm.firstChild);
    }

    function findFieldInFormForKey(form, key) {
        const candidates = [String(key), dotToBracket(key)];
        const arrayName = baseArrayName(key);
        if (arrayName) candidates.push(arrayName);
        for (const name of candidates) {
            const fields = findFields(form, name);
            if (fields.length) return fields[0];
        }
        return null;
    }

    function setRequiredIfPresent(form, name) {
        const fields = findFields(form, name);
        if (!fields.length) return;
        const field = fields[0];
        if (field.disabled || field.readOnly || field.type === 'hidden') return;
        if (field.type === 'radio' || field.type === 'checkbox') {
            field.required = true;
        } else {
            field.required = true;
        }
    }

    function setHistoricalMax(form) {
        HISTORICAL_DATE_NAMES.forEach(function (name) {
            findFields(form, name).forEach(function (field) {
                if (field.type === 'date' && !field.disabled && !field.readOnly) field.max = TODAY;
            });
        });
    }

    function bindDateRelation(form, fromName, toName) {
        const from = findFields(form, fromName)[0];
        const to = findFields(form, toName)[0];
        if (!from || !to || from.type !== 'date' || to.type !== 'date') return;

        const sync = function () {
            if (from.value) to.min = from.value;
            else to.removeAttribute('min');
            clearAutoError(to);
        };
        from.addEventListener('change', sync);
        sync();
    }

    function bindInformationSources(form) {
        const checks = findFields(form, 'information_sources[]');
        if (!checks.length) return function () { return true; };

        // Baseline requires at least one source; evidence is required for every scored indicator.
        form.querySelectorAll('textarea[name^="items["][name$="[evidence]"]').forEach(function (field) {
            if (!field.disabled && !field.readOnly) field.required = true;
        });

        function validate() {
            const ok = checks.some(function (input) { return input.checked; });
            checks[0].setCustomValidity(ok ? '' : 'กรุณาเลือกแหล่งข้อมูลอย่างน้อย 1 รายการ');
            const wrap = checks[0].closest('.idp-source-list, .source-list, .card-body, .mb-3') || checks[0].parentElement;
            if (wrap) wrap.classList.toggle('idp-invalid-group', !ok);
            return ok;
        }

        checks.forEach(function (input) { input.addEventListener('change', validate); });
        validate();
        return validate;
    }

    function bindScoreGroups(form) {
        const scoreInputs = Array.from(form.querySelectorAll('input[name^="items["][name$="[score]"]'));
        const groups = new Map();
        scoreInputs.forEach(function (input) {
            if (!groups.has(input.name)) groups.set(input.name, []);
            groups.get(input.name).push(input);
        });
        groups.forEach(function (inputs) {
            const first = inputs[0];
            if (first && !first.disabled) first.required = true;
        });
    }

    function bindNextActionRule(form) {
        const result = findFields(form, 'overall_result')[0];
        const nextAction = findFields(form, 'next_action')[0];
        if (!result || !nextAction) return;

        const sync = function () {
            // Backend allows next_action to be empty only when the result is achieved.
            const required = !!result.value && result.value !== 'achieved';
            nextAction.required = required;
            if (!required) nextAction.setCustomValidity('');
            clearAutoError(nextAction);
        };
        result.addEventListener('change', sync);
        sync();
    }

    function bindCloseRule(form) {
        const action = String(form.getAttribute('action') || '');
        if (/\/close(?:\?|$)/.test(action)) {
            setRequiredIfPresent(form, 'close_reason');
            setRequiredIfPresent(form, 'final_outcome');
        } else if (/\/cancel(?:\?|$)/.test(action)) {
            setRequiredIfPresent(form, 'close_reason');
            setRequiredIfPresent(form, 'cancel_reason');
            setRequiredIfPresent(form, 'reason');
        }
    }

    function firstInvalid(form) {
        return form.querySelector(':invalid, .is-invalid');
    }

    function focusInvalid(field) {
        if (!field) return;
        const block = field.closest('.mb-3, .form-group, .card, .idp-indicator, [class*="col-"]') || field;
        try { block.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        catch (error) { block.scrollIntoView(); }
        window.setTimeout(function () {
            try { field.focus({ preventScroll: true }); }
            catch (error) { try { field.focus(); } catch (ignore) {} }
        }, 250);
    }

    function notifyInvalid() {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            window.Swal.fire({
                icon: 'warning',
                title: 'กรุณาตรวจสอบข้อมูล',
                text: 'กรอกข้อมูลที่จำเป็นและแก้ไขช่องที่ระบุ ก่อนกดบันทึกอีกครั้ง',
                confirmButtonText: 'OK'
            });
        }
    }

    document.querySelectorAll('form').forEach(function (form) {
        if (!isWriteForm(form)) return;
        if (form.dataset.idpValidationBound === '1') return;
        form.dataset.idpValidationBound = '1';
        form.setAttribute('novalidate', 'novalidate');

        REQUIRED_NAMES.forEach(function (name) { setRequiredIfPresent(form, name); });
        bindCloseRule(form);
        setHistoricalMax(form);
        bindDateRelation(form, 'start_date', 'end_date');
        bindDateRelation(form, 'followup_date', 'next_followup_date');
        bindDateRelation(form, 'coordination_date', 'next_appointment_date');
        bindScoreGroups(form);
        bindNextActionRule(form);
        const validateSources = bindInformationSources(form);

        form.querySelectorAll('input, select, textarea').forEach(function (field) {
            field.addEventListener('input', function () { clearAutoError(field); });
            field.addEventListener('change', function () { clearAutoError(field); });
        });

        form.addEventListener('submit', function (event) {
            if (form.dataset.idpSubmitting === '1') {
                event.preventDefault();
                event.stopImmediatePropagation();
                return;
            }

            validateSources();
            form.classList.add('was-validated');

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopImmediatePropagation();
                focusInvalid(firstInvalid(form));
                notifyInvalid();
                return;
            }

            form.dataset.idpSubmitting = '1';
            // Listener ยืนยันด้วย SweetAlert ของแต่ละหน้าทำงานใน bubble phase หลังจุดนี้ได้
            // ถ้า listener อื่นยกเลิก submit (เช่น ผู้ใช้กด “กลับไปตรวจ”) ต้องคืนสถานะทันที
            // ไม่เช่นนั้นฟอร์มจะถูกล็อกและกดบันทึกครั้งถัดไปไม่ได้
            window.setTimeout(function () {
                if (event.defaultPrevented) {
                    delete form.dataset.idpSubmitting;
                    return;
                }

                form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (button) {
                    button.disabled = true;
                    button.setAttribute('aria-disabled', 'true');
                });
            }, 0);
        }, true);
    });

    addServerSummary();

    const firstServerInvalid = document.querySelector('.is-invalid');
    if (firstServerInvalid) focusInvalid(firstServerInvalid);
});
</script>
@endonce
