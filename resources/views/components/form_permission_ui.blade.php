@once
@php
    $permissionUi = \App\Support\FormPermissionUi::forUser(
        auth()->user(),
        request()->route()?->getName()
    );
@endphp

@if($permissionUi['enabled'] ?? false)
<style>
    .permission-ui-hidden {
        display: none !important;
    }

    .permission-readonly-banner {
        display: flex;
        align-items: flex-start;
        gap: .8rem;
        margin: 0 0 1rem;
        padding: .95rem 1rem;
        color: #163a63;
        background: linear-gradient(180deg, #eff6ff 0%, #f8fbff 100%);
        border: 1px solid #bfdbfe;
        border-radius: 14px;
        box-shadow: 0 8px 20px rgba(37, 99, 235, .07);
        font-size: .92rem;
        font-weight: 700;
    }

    .permission-readonly-banner .permission-banner-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: 34px;
        height: 34px;
        color: #1d4ed8;
        background: #dbeafe;
        border-radius: 10px;
    }

    .permission-readonly-banner small {
        display: block;
        margin-top: .12rem;
        color: #52657c;
        font-size: .82rem;
        font-weight: 400;
        line-height: 1.55;
    }

    .permission-readonly-form {
        --permission-field-bg: #f8fafc;
        --permission-field-text: #24364b;
        --permission-field-border: #dbe4ef;
    }

    .permission-readonly-form input[readonly],
    .permission-readonly-form textarea[readonly],
    .permission-readonly-form input:disabled,
    .permission-readonly-form textarea:disabled,
    .permission-readonly-form select:disabled,
    .permission-readonly-form .form-control:disabled,
    .permission-readonly-form .form-select:disabled {
        opacity: 1 !important;
        color: var(--permission-field-text) !important;
        -webkit-text-fill-color: var(--permission-field-text) !important;
        background-color: var(--permission-field-bg) !important;
        border-color: var(--permission-field-border) !important;
        box-shadow: none !important;
        cursor: default !important;
    }

    .permission-readonly-form input[type="checkbox"]:disabled,
    .permission-readonly-form input[type="radio"]:disabled {
        opacity: 1 !important;
        filter: none !important;
        cursor: default !important;
    }

    .permission-readonly-form input[type="file"]:disabled {
        color: #64748b !important;
    }

    .permission-readonly-form .select2-container--disabled,
    .permission-readonly-form .select2-container--default.select2-container--disabled .select2-selection--single,
    .permission-readonly-form .select2-container--default.select2-container--disabled .select2-selection--multiple,
    .permission-readonly-form .choices.is-disabled,
    .permission-readonly-form .choices.is-disabled .choices__inner {
        opacity: 1 !important;
        color: var(--permission-field-text) !important;
        background: var(--permission-field-bg) !important;
        border-color: var(--permission-field-border) !important;
        cursor: default !important;
    }

    .permission-readonly-form [contenteditable="false"] {
        color: var(--permission-field-text) !important;
        background: var(--permission-field-bg) !important;
        cursor: default !important;
    }

    .permission-readonly-form .permission-readonly-select-lock,
    .permission-readonly-form .select2-container--disabled,
    .permission-readonly-form .choices.is-disabled {
        pointer-events: none !important;
    }

    .permission-view-action {
        color: #1d4ed8 !important;
        background-color: #eff6ff !important;
        border-color: #bfdbfe !important;
    }

    .permission-view-action:hover,
    .permission-view-action:focus {
        color: #1e40af !important;
        background-color: #dbeafe !important;
        border-color: #93c5fd !important;
    }

    .permission-readonly-modal .modal-title::after {
        content: ' · อ่านอย่างเดียว';
        color: #2563eb;
        font-size: .78em;
        font-weight: 600;
    }

    @media (max-width: 575.98px) {
        .permission-readonly-banner {
            padding: .85rem;
            border-radius: 12px;
        }
    }
</style>

<script>
(function () {
    'use strict';

    // Permission Read-only V7.2 — Menu Integrity Hotfix
    const state = @json($permissionUi, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    window.__FORM_PERMISSION_UI__ = state;

    if (!state || state.enabled !== true || !state.current) {
        return;
    }

    const permissions = state.current;
    const deniedRoutes = Array.isArray(state.denied_routes) ? state.denied_routes : [];
    const currentRouteAction = String(state.route_action || 'view').toLowerCase();
    const readonlyMode = permissions.readonly === true || (
        permissions.view === true &&
        permissions.create === false &&
        permissions.update === false &&
        permissions.delete === false
    );

    const WRITE_WORDS = {
        create: /(เพิ่ม(?:ข้อมูล|รายการ|ผล|วิชา|สมาชิก|การติดตาม|ข่าว|ผู้|คำขอ)?|สร้าง(?:รายการ|คำขอ)?|รายการใหม่|บันทึกใหม่|ติดตามผล|add|create|new)/i,
        update: /(แก้ไข|ปรับปรุง|อัปเดต|เปลี่ยนแปลง|อนุมัติ|รออนุมัติ|คืนสถานะ|edit|update|modify)/i,
        delete: /(ลบ|นำออก|ล้างข้อมูล|ล้างค่า|delete|destroy|remove|trash)/i,
        save: /(บันทึก|ยืนยัน|ตกลงบันทึก|สมัครสมาชิก|ส่งข้อมูล|save|submit)/i,
        print: /(พิมพ์|รายงาน|print|report|pdf)/i,
        view: /(ดู|รายละเอียด|แสดง|เปิดดู|view|show|detail)/i
    };

    const CREATE_PATH = /(?:^|\/)(?:create|add|new|store)(?:\/|$)/i;
    const UPDATE_PATH = /(?:^|\/)(?:edit|update|modify)(?:\/|$)/i;
    const DELETE_PATH = /(?:^|\/)(?:delete|destroy|remove)(?:\/|$)/i;
    const PRINT_PATH = /(?:^|\/)(?:print|report|pdf)(?:\/|$)/i;

    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
        } else {
            callback();
        }
    }

    function normalizeMethod(method) {
        return String(method || 'GET').toUpperCase();
    }

    function resolveUrl(value) {
        if (!value || value === '#' || String(value).startsWith('javascript:')) {
            return null;
        }

        try {
            const url = new URL(value, window.location.origin);
            return url.origin === window.location.origin ? url : null;
        } catch (error) {
            return null;
        }
    }

    function deniedRule(urlValue, method) {
        const url = resolveUrl(urlValue);
        if (!url) return null;

        const normalizedMethod = normalizeMethod(method);

        return deniedRoutes.find(function (rule) {
            const methods = Array.isArray(rule.methods)
                ? rule.methods.map(normalizeMethod)
                : [];

            if (!methods.includes(normalizedMethod)) return false;

            try {
                return new RegExp(rule.pattern).test(url.pathname || '/');
            } catch (error) {
                return false;
            }
        }) || null;
    }

    function textOf(element) {
        return String(
            element?.getAttribute?.('aria-label') ||
            element?.getAttribute?.('title') ||
            element?.textContent ||
            ''
        ).replace(/\s+/g, ' ').trim();
    }

    function attributeCorpus(element) {
        if (!element) return '';

        const names = [
            'id', 'class', 'name', 'title', 'aria-label', 'onclick', 'href',
            'action', 'data-action', 'data-url', 'data-route', 'data-bs-target',
            'data-target', 'data-bs-toggle', 'data-toggle'
        ];

        return names.map(function (name) {
            return element.getAttribute?.(name) || '';
        }).join(' ') + ' ' + textOf(element);
    }

    function explicitAction(element) {
        const value = String(element?.dataset?.permissionAction || '').toLowerCase();
        return ['view', 'create', 'update', 'delete', 'print', 'filter', 'navigation'].includes(value)
            ? value
            : null;
    }

    function isKeepElement(element) {
        return !!element?.closest?.(
            '[data-permission-keep], [data-permission-action="view"], ' +
            '[data-permission-action="filter"], [data-permission-action="navigation"]'
        );
    }

    /*
     * PERMISSION_MENU_INTEGRITY_GUARD_V72
     *
     * เมนู Sidebar / Topbar ถูกคำนวณสิทธิ์รายฟอร์มจากฝั่ง Server โดย
     * App\Support\FormPermissionMenu อยู่แล้ว จึงต้องไม่ให้ Permission UI
     * ของ Route ปัจจุบันนำสิทธิ์ของฟอร์มหนึ่งไปซ่อนลิงก์ของฟอร์มอื่น
     *
     * ตัวอย่างปัญหาเดิม:
     * - เปิดหน้าอ่านอย่างเดียวของ “ประวัติผู้รับบริการ”
     * - JavaScript เห็นคำว่า add/create/edit ในลิงก์เมนูฟอร์มอื่น
     * - เมนู “บันทึกผลการเรียน”, “บันทึกการบาดเจ็บ” ฯลฯ ถูกซ่อนผิด
     *
     * การป้องกันนี้กระทบเฉพาะส่วน Navigation เท่านั้น
     * ปุ่มเพิ่ม/แก้ไข/ลบภายในเนื้อหาหน้ายังคงถูกควบคุมเหมือนเดิม
     * และ Middleware ฝั่ง Server ยังคงป้องกัน URL โดยตรงครบถ้วน
     */
    const PERMISSION_NAVIGATION_SELECTOR = [
        '[data-permission-menu]',
        '[data-navigation-menu]',
        '.app-sidebar-menu',
        '#sidebar-menu',
        '#side-menu',
        '.topbar-menu',
        '.topbar-collapse',
        '.navbar-nav',
        '.nav-tabs',
        '.nav-pills',
        '[role="tablist"]',
        '.breadcrumb'
    ].join(',');

    function isPermissionNavigationElement(element) {
        return !!element?.closest?.(PERMISSION_NAVIGATION_SELECTOR);
    }

    function isNavigationControl(element) {
        if (!element) return false;

        if (isKeepElement(element) || isPermissionNavigationElement(element)) return true;

        if (element.matches?.(
            '.btn-close, [aria-label="Close"], [data-bs-dismiss="modal"], [data-dismiss="modal"], ' +
            '[data-bs-toggle="tab"], [data-toggle="tab"], [data-bs-toggle="collapse"], [data-toggle="collapse"], ' +
            '.page-link, .paginate_button, [data-dt-idx], [data-bs-slide], [data-slide]'
        )) {
            return true;
        }

        const text = textOf(element);
        return /^(กลับ|ปิด|ยกเลิก|หน้าหลัก|ย้อนกลับ|close|cancel|back)$/i.test(text);
    }

    function semanticAction(element) {
        const explicit = explicitAction(element);
        if (explicit) return explicit;

        const text = textOf(element);
        const corpus = attributeCorpus(element);
        const structural = [
            element?.getAttribute?.('id') || '',
            element?.getAttribute?.('class') || '',
            element?.getAttribute?.('name') || '',
            element?.getAttribute?.('onclick') || '',
            element?.getAttribute?.('href') || '',
            element?.getAttribute?.('action') || '',
            element?.getAttribute?.('data-action') || '',
            element?.getAttribute?.('data-url') || '',
            element?.getAttribute?.('data-route') || '',
            element?.getAttribute?.('data-bs-target') || '',
            element?.getAttribute?.('data-target') || ''
        ].join(' ');
        const href = element?.getAttribute?.('href') || element?.getAttribute?.('action') || '';
        const url = resolveUrl(href);
        const path = url?.pathname || href;

        if (/อ่านเพิ่มเติม|รายละเอียดเพิ่มเติม/i.test(text)) return 'view';

        if (DELETE_PATH.test(path) || /delete|destroy|remove|trash/i.test(structural) || WRITE_WORDS.delete.test(text)) return 'delete';
        if (UPDATE_PATH.test(path) || /edit|update|modify|toggle.?status|approve|restore/i.test(structural) || WRITE_WORDS.update.test(text)) return 'update';
        if (CREATE_PATH.test(path) || /(?:^|[-_])(?:add|create|new|store)(?:[-_]|$)/i.test(structural) || WRITE_WORDS.create.test(text)) return 'create';
        if (PRINT_PATH.test(path) || /print|report|pdf/i.test(structural) || WRITE_WORDS.print.test(text)) return 'print';
        if (WRITE_WORDS.view.test(text)) return 'view';
        if (WRITE_WORDS.save.test(text) || /save|submit/i.test(structural)) return 'save';

        return null;
    }

    function effectiveFormMethod(form) {
        const spoofed = form.querySelector('input[name="_method"]');
        return normalizeMethod(spoofed ? spoofed.value : form.method);
    }

    function isFilterForm(form) {
        if (!form) return false;

        const explicit = explicitAction(form);
        if (explicit === 'filter' || explicit === 'navigation') return true;

        const corpus = attributeCorpus(form);
        const method = effectiveFormMethod(form);

        if (/filter|search|ค้นหา|กรอง/i.test(corpus)) return true;

        return method === 'GET' && !deniedRule(form.getAttribute('action'), 'GET');
    }

    function formAction(form) {
        const explicit = explicitAction(form);
        if (explicit && explicit !== 'filter' && explicit !== 'navigation') {
            return explicit;
        }

        const method = effectiveFormMethod(form);
        const actionUrl = form.getAttribute('action') || window.location.href;
        const routeRule = deniedRule(actionUrl, method);

        if (routeRule?.action) return String(routeRule.action).toLowerCase();
        if (method === 'DELETE') return 'delete';
        if (method === 'PUT' || method === 'PATCH') return 'update';
        if (method === 'GET') return 'view';

        const semantic = semanticAction(form);
        if (semantic && semantic !== 'save') return semantic;

        const modal = form.closest('.modal');
        if (modal) {
            const modalSemantic = semanticAction(modal);
            if (modalSemantic && modalSemantic !== 'save') return modalSemantic;

            if (form.querySelector('input[name="id"], input[name$="_id"][value]:not([value=""])')) {
                return 'update';
            }

            return 'create';
        }

        if (currentRouteAction === 'update') return 'update';
        if (currentRouteAction === 'create') return 'create';

        return null;
    }

    function hideElement(element) {
        if (!element || element.classList?.contains('permission-ui-hidden')) return;

        element.classList.add('permission-ui-hidden');
        element.setAttribute?.('aria-hidden', 'true');
        element.dataset.permissionUiHidden = '1';
    }

    function showReadOnlyNotice() {
        if (window.Swal) {
            window.Swal.fire({
                icon: 'info',
                title: 'โหมดอ่านอย่างเดียว',
                text: 'บัญชีนี้สามารถดูข้อมูลได้ แต่ไม่มีสิทธิ์เพิ่ม แก้ไข บันทึก หรือลบข้อมูล',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        if (window.toastr) {
            window.toastr.info('บัญชีนี้เป็นโหมดอ่านอย่างเดียว');
        }
    }

    function lockRichTextEditors(form) {
        form.querySelectorAll('[contenteditable="true"]').forEach(function (element) {
            element.setAttribute('contenteditable', 'false');
            element.setAttribute('aria-readonly', 'true');
        });

        if (window.tinymce && Array.isArray(window.tinymce.editors)) {
            window.tinymce.editors.forEach(function (editor) {
                const target = editor?.targetElm;
                if (!target || !form.contains(target)) return;

                try {
                    editor.mode.set('readonly');
                } catch (error) {
                    try { editor.setMode('readonly'); } catch (ignored) {}
                }
            });
        }

        if (window.CKEDITOR?.instances) {
            Object.keys(window.CKEDITOR.instances).forEach(function (key) {
                const editor = window.CKEDITOR.instances[key];
                const element = editor?.element?.$;
                if (!element || !form.contains(element)) return;
                try { editor.setReadOnly(true); } catch (ignored) {}
            });
        }
    }

    function lockField(field) {
        if (!field || field.type === 'hidden') return;
        if (isKeepElement(field)) return;

        const tag = String(field.tagName || '').toLowerCase();
        const type = String(field.type || '').toLowerCase();
        const readonlyTypes = [
            'text', 'search', 'email', 'url', 'tel', 'number', 'password',
            'date', 'time', 'datetime-local', 'month', 'week'
        ];

        if (tag === 'textarea' || (tag === 'input' && readonlyTypes.includes(type))) {
            field.readOnly = true;
            field.setAttribute('readonly', 'readonly');
            field.setAttribute('aria-readonly', 'true');
        } else {
            field.disabled = true;
            field.setAttribute('disabled', 'disabled');
            field.setAttribute('aria-disabled', 'true');

            if (tag === 'select' || type === 'checkbox' || type === 'radio' || type === 'file') {
                field.classList.add('permission-readonly-select-lock');
            }
        }
    }

    function isSubmitOrWriteControl(element) {
        if (!element) return false;

        const type = String(element.getAttribute?.('type') || '').toLowerCase();
        const action = semanticAction(element);

        return ['submit', 'reset'].includes(type) ||
            ['create', 'delete', 'save'].includes(action) ||
            (action === 'update' && element.closest('form'));
    }

    function lockForm(form) {
        if (!form) return;

        form.classList.remove('permission-ui-hidden');
        form.removeAttribute('aria-hidden');
        form.classList.add('permission-readonly-form');
        form.dataset.permissionReadonly = '1';
        form.setAttribute('aria-readonly', 'true');

        if (form.dataset.permissionReadonlyListener !== '1') {
            form.dataset.permissionReadonlyListener = '1';
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                event.stopImmediatePropagation();
                showReadOnlyNotice();
            }, true);
        }

        form.querySelectorAll('input, textarea, select').forEach(lockField);

        form.querySelectorAll('button, input[type="submit"], input[type="button"], input[type="reset"], a').forEach(function (control) {
            if (isNavigationControl(control)) return;

            const action = semanticAction(control);
            if (isSubmitOrWriteControl(control) || ['create', 'delete', 'save'].includes(action)) {
                hideElement(control.closest('li') || control);
            }
        });

        const modal = form.closest('.modal');
        if (modal) {
            modal.classList.add('permission-readonly-modal');
        }

        lockRichTextEditors(form);
        window.setTimeout(function () { lockRichTextEditors(form); }, 200);
        window.setTimeout(function () { lockRichTextEditors(form); }, 900);
    }

    function transformEditToView(control) {
        if (!control || control.dataset.permissionViewTransformed === '1') return;
        if (isSubmitOrWriteControl(control)) {
            hideElement(control);
            return;
        }

        control.dataset.permissionViewTransformed = '1';
        control.dataset.permissionAction = 'view';
        control.classList.add('permission-view-action');
        control.setAttribute('title', 'ดูข้อมูล (อ่านอย่างเดียว)');
        control.setAttribute('aria-label', 'ดูข้อมูล (อ่านอย่างเดียว)');

        const textNodeCandidate = Array.from(control.childNodes || []).find(function (node) {
            return node.nodeType === Node.TEXT_NODE && node.textContent.trim() !== '';
        });

        if (textNodeCandidate && /แก้ไข|edit|update/i.test(textNodeCandidate.textContent)) {
            textNodeCandidate.textContent = textNodeCandidate.textContent.replace(/แก้ไข|edit|update/ig, 'ดูข้อมูล');
        } else if (/แก้ไข|edit|update/i.test(textOf(control))) {
            const label = control.querySelector('span:not(.badge)');
            if (label) label.textContent = 'ดูข้อมูล';
        }

        const icon = control.querySelector('i');
        if (icon) {
            icon.className = 'bi bi-eye-fill';
        }
    }

    function processForm(form) {
        if (!form || isFilterForm(form)) return;

        const action = formAction(form);
        const inModal = !!form.closest('.modal');

        if (action === 'delete' && permissions.delete === false) {
            hideElement(form);
            return;
        }

        if (action === 'create' && permissions.create === false) {
            if (inModal || currentRouteAction === 'create') {
                hideElement(form);
            } else if (permissions.view === true) {
                lockForm(form);
            } else {
                hideElement(form);
            }
            return;
        }

        if (action === 'update' && permissions.update === false) {
            if (permissions.view === true) {
                lockForm(form);
            } else {
                hideElement(form);
            }
            return;
        }

        /* ฟอร์ม upsert รุ่นเก่าที่ Route ระบุไม่ชัดเจน */
        if (readonlyMode && action === null && !inModal) {
            lockForm(form);
        }
    }

    function processControl(control) {
        if (!control || isNavigationControl(control)) return;
        if (control.closest('.permission-ui-hidden')) return;

        let action = semanticAction(control);

        if (control.matches('a[href]')) {
            const rule = deniedRule(control.getAttribute('href'), 'GET');
            if (rule?.action) action = String(rule.action).toLowerCase();
        }

        if (action === 'create' && permissions.create === false) {
            hideElement(control.closest('li') || control);
            return;
        }

        if (action === 'delete' && permissions.delete === false) {
            hideElement(control.closest('form') || control.closest('li') || control);
            return;
        }

        if (action === 'print' && permissions.print === false) {
            hideElement(control.closest('li') || control);
            return;
        }

        if ((action === 'update' || action === 'save') && permissions.update === false) {
            if (action === 'update' && permissions.view === true && !isSubmitOrWriteControl(control)) {
                transformEditToView(control);
            } else {
                hideElement(control.closest('li') || control);
            }
            return;
        }

        if (readonlyMode) {
            const corpus = attributeCorpus(control);

            if (WRITE_WORDS.delete.test(corpus) || WRITE_WORDS.create.test(corpus) || WRITE_WORDS.save.test(corpus)) {
                hideElement(control.closest('form[method="POST"]') || control.closest('li') || control);
                return;
            }

            if (WRITE_WORDS.update.test(corpus) && !isSubmitOrWriteControl(control)) {
                transformEditToView(control);
            }
        }
    }

    function modalTargetSelector(modal) {
        if (!modal?.id) return null;
        const escaped = window.CSS?.escape ? CSS.escape(modal.id) : modal.id.replace(/([ #;?%&,.+*~\\':"!^$[\]()=>|/@])/g, '\\$1');
        return '[data-bs-target="#' + escaped + '"], [data-target="#' + escaped + '"], a[href="#' + escaped + '"]';
    }

    function processModal(modal) {
        if (!modal) return;

        const forms = Array.from(modal.querySelectorAll('form'));
        if (forms.length === 0) return;

        const allHidden = forms.every(function (form) {
            return form.classList.contains('permission-ui-hidden');
        });

        const hasReadonly = forms.some(function (form) {
            return form.dataset.permissionReadonly === '1';
        });

        const selector = modalTargetSelector(modal);
        if (!selector) return;

        document.querySelectorAll(selector).forEach(function (trigger) {
            if (allHidden) {
                hideElement(trigger.closest('li') || trigger);
            } else if (hasReadonly && permissions.view === true) {
                transformEditToView(trigger);
            }
        });
    }

    function processSubtree(root) {
        const scope = root?.nodeType === Node.ELEMENT_NODE ? root : document;

        if (scope.matches?.('form')) processForm(scope);
        scope.querySelectorAll?.('form').forEach(processForm);

        const selector = [
            'a[href]', 'button', 'input[type="submit"]', 'input[type="button"]',
            'input[type="reset"]', '[role="button"]', '[onclick]', '[data-bs-toggle="modal"]',
            '[data-toggle="modal"]'
        ].join(',');

        if (scope.matches?.(selector)) processControl(scope);
        scope.querySelectorAll?.(selector).forEach(processControl);

        if (scope.matches?.('.modal')) processModal(scope);
        scope.querySelectorAll?.('.modal').forEach(processModal);
    }

    function insertBanner() {
        if (!readonlyMode || document.getElementById('permissionReadonlyBanner')) return;

        const container = document.querySelector(
            '.content-page .content-scroll-x > .container-fluid, ' +
            '.content-page .content-scroll-x > .container, ' +
            '.content-page .container-fluid, .content-page .container, ' +
            '.page-content .container-fluid, .page-content .container, ' +
            'main .container-fluid, main .container, main, .content-page'
        );

        if (!container) return;

        const banner = document.createElement('div');
        banner.id = 'permissionReadonlyBanner';
        banner.className = 'permission-readonly-banner';
        banner.setAttribute('role', 'status');
        banner.innerHTML =
            '<span class="permission-banner-icon"><i class="bi bi-eye-fill" aria-hidden="true"></i></span>' +
            '<span>โหมดอ่านอย่างเดียว' +
            '<small>ข้อมูลยังแสดงตามปกติและสามารถคัดลอกได้ แต่ระบบปิดการเพิ่ม แก้ไข บันทึก และลบจากทั้งหน้าจอและฝั่งเซิร์ฟเวอร์</small>' +
            '</span>';

        container.prepend(banner);
    }

    function actionDenied(action) {
        return (action === 'create' && permissions.create === false) ||
            (action === 'update' && permissions.update === false) ||
            (action === 'delete' && permissions.delete === false) ||
            (action === 'print' && permissions.print === false) ||
            (action === 'save' && permissions.create === false && permissions.update === false);
    }

    onReady(function () {
        processSubtree(document);
        insertBanner();

        document.addEventListener('click', function (event) {
            const control = event.target.closest(
                'a[href], button, input[type="submit"], input[type="button"], [role="button"], [onclick]'
            );

            if (!control || isNavigationControl(control)) return;

            let action = semanticAction(control);
            if (control.matches('a[href]')) {
                const rule = deniedRule(control.getAttribute('href'), 'GET');
                if (rule?.action) action = String(rule.action).toLowerCase();
            }

            const readonlyViewTrigger = control.dataset.permissionViewTransformed === '1';
            if (readonlyViewTrigger) return;

            if (actionDenied(action)) {
                event.preventDefault();
                event.stopImmediatePropagation();
                showReadOnlyNotice();
            }
        }, true);

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || isFilterForm(form)) return;

            const action = formAction(form);
            if (form.dataset.permissionReadonly === '1' || actionDenied(action)) {
                event.preventDefault();
                event.stopImmediatePropagation();
                showReadOnlyNotice();
            }
        }, true);

        let observerScheduled = false;
        const pendingRoots = new Set();

        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === Node.ELEMENT_NODE) pendingRoots.add(node);
                });
            });

            if (observerScheduled || pendingRoots.size === 0) return;
            observerScheduled = true;

            window.requestAnimationFrame(function () {
                pendingRoots.forEach(processSubtree);
                pendingRoots.clear();
                observerScheduled = false;
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        /* รองรับ DataTables/Modal ที่วาดปุ่มใหม่หลังโหลดหน้า */
        if (window.jQuery) {
            window.jQuery(document).on(
                'draw.dt shown.bs.modal loaded.bs.modal',
                function (event) {
                    processSubtree(event.target || document);
                }
            );
        }
    });
})();
</script>
@endif
@endonce
