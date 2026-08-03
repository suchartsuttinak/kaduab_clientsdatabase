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
            gap: .75rem;
            margin: 0 0 1rem;
            padding: .9rem 1rem;
            color: #1e3a5f;
            background: linear-gradient(180deg, #eff6ff 0%, #f8fbff 100%);
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            font-size: .92rem;
            font-weight: 600;
            box-shadow: 0 6px 16px rgba(37, 99, 235, .06);
        }

        .permission-readonly-banner i {
            flex: 0 0 auto;
            margin-top: .08rem;
            color: #2563eb;
            font-size: 1rem;
        }

        .permission-readonly-banner small {
            display: block;
            margin-top: .15rem;
            color: #52657c;
            font-weight: 400;
        }

        /* แสดงข้อมูลเดิมให้ชัดเจน แม้ช่องควบคุมถูกล็อก */
        form.permission-readonly-form input[readonly],
        form.permission-readonly-form textarea[readonly],
        form.permission-readonly-form select:disabled,
        form.permission-readonly-form input:disabled,
        form.permission-readonly-form textarea:disabled,
        form.permission-readonly-form button:disabled {
            opacity: 1 !important;
        }

        form.permission-readonly-form input[readonly],
        form.permission-readonly-form textarea[readonly],
        form.permission-readonly-form select:disabled,
        form.permission-readonly-form input[type="date"]:disabled,
        form.permission-readonly-form input[type="time"]:disabled,
        form.permission-readonly-form input[type="datetime-local"]:disabled,
        form.permission-readonly-form input[type="file"]:disabled {
            color: #24364b !important;
            background-color: #f8fafc !important;
            border-color: #dbe4ef !important;
            box-shadow: none !important;
            cursor: default !important;
            -webkit-text-fill-color: #24364b !important;
        }

        form.permission-readonly-form input[type="checkbox"]:disabled,
        form.permission-readonly-form input[type="radio"]:disabled {
            opacity: 1 !important;
            cursor: default !important;
            filter: none !important;
        }

        form.permission-readonly-form .select2-container--disabled,
        form.permission-readonly-form .select2-container--default.select2-container--disabled .select2-selection--single,
        form.permission-readonly-form .select2-container--default.select2-container--disabled .select2-selection--multiple {
            opacity: 1 !important;
            color: #24364b !important;
            background: #f8fafc !important;
            border-color: #dbe4ef !important;
            cursor: default !important;
        }

        form.permission-readonly-form [contenteditable="false"] {
            color: #24364b !important;
            background-color: #f8fafc !important;
            cursor: default !important;
        }

        form.permission-readonly-form .permission-readonly-locked {
            pointer-events: none !important;
            user-select: text;
        }
    </style>

    <script>
        window.__FORM_PERMISSION_UI__ = @json($permissionUi, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        document.addEventListener('DOMContentLoaded', function () {
            const state = window.__FORM_PERMISSION_UI__ || {};
            const deniedRoutes = Array.isArray(state.denied_routes) ? state.denied_routes : [];
            const current = state.current || null;

            function normalizeMethod(method) {
                return String(method || 'GET').toUpperCase();
            }

            function deniedRule(urlValue, method) {
                if (!urlValue) return null;

                let url;
                try {
                    url = new URL(urlValue, window.location.origin);
                } catch (e) {
                    return null;
                }

                if (url.origin !== window.location.origin) return null;

                const pathname = url.pathname || '/';
                const normalizedMethod = normalizeMethod(method);

                return deniedRoutes.find(function (rule) {
                    const methods = Array.isArray(rule.methods)
                        ? rule.methods.map(normalizeMethod)
                        : [];

                    if (!methods.includes(normalizedMethod)) return false;

                    try {
                        return new RegExp(rule.pattern).test(pathname);
                    } catch (e) {
                        return false;
                    }
                }) || null;
            }

            function routeDenied(urlValue, method) {
                return deniedRule(urlValue, method) !== null;
            }

            function hideElement(element) {
                if (!element || element.dataset.permissionUiProcessed === '1') return;

                element.dataset.permissionUiProcessed = '1';
                element.classList.add('permission-ui-hidden');
                element.setAttribute('aria-hidden', 'true');
            }

            function effectiveFormMethod(form) {
                const spoofed = form.querySelector('input[name="_method"]');
                return normalizeMethod(spoofed ? spoofed.value : form.method);
            }

            function isNavigationButton(button) {
                if (!button) return false;

                return button.matches(
                    '[data-bs-toggle="tab"], [data-toggle="tab"], ' +
                    '[data-bs-toggle="collapse"], [data-toggle="collapse"], ' +
                    '[data-bs-dismiss="modal"], [data-dismiss="modal"], ' +
                    '.btn-close, [aria-label="Close"], [data-permission-keep]'
                );
            }

            function lockRichTextEditors(form) {
                form.querySelectorAll('[contenteditable="true"]').forEach(function (element) {
                    element.setAttribute('contenteditable', 'false');
                    element.setAttribute('aria-readonly', 'true');
                    element.classList.add('permission-readonly-locked');
                });

                if (window.tinymce && Array.isArray(window.tinymce.editors)) {
                    window.tinymce.editors.forEach(function (editor) {
                        const target = editor && editor.targetElm;
                        if (!target || !form.contains(target)) return;

                        try {
                            editor.mode.set('readonly');
                        } catch (e) {
                            try { editor.setMode('readonly'); } catch (ignored) {}
                        }
                    });
                }

                if (window.CKEDITOR && window.CKEDITOR.instances) {
                    Object.keys(window.CKEDITOR.instances).forEach(function (key) {
                        const editor = window.CKEDITOR.instances[key];
                        const element = editor && editor.element && editor.element.$;
                        if (!element || !form.contains(element)) return;

                        try { editor.setReadOnly(true); } catch (e) {}
                    });
                }
            }

            function makeFormReadOnly(form) {
                if (!form || form.dataset.permissionReadonlyApplied === '1') return;

                form.dataset.permissionReadonlyApplied = '1';
                form.classList.remove('permission-ui-hidden');
                form.removeAttribute('aria-hidden');
                form.classList.add('permission-readonly-form');
                form.setAttribute('data-permission-readonly', 'true');
                form.setAttribute('aria-readonly', 'true');

                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    event.stopImmediatePropagation();

                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'info',
                            title: 'โหมดอ่านอย่างเดียว',
                            text: 'บัญชีนี้ไม่มีสิทธิ์บันทึกการแก้ไขข้อมูล',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                }, true);

                form.querySelectorAll('input, textarea, select').forEach(function (field) {
                    if (field.type === 'hidden') return;

                    const tag = field.tagName.toLowerCase();
                    const type = String(field.type || '').toLowerCase();
                    const canUseReadonly = tag === 'textarea' || [
                        'text', 'search', 'email', 'url', 'tel', 'number',
                        'password'
                    ].includes(type);

                    if (canUseReadonly) {
                        field.readOnly = true;
                        field.setAttribute('readonly', 'readonly');
                        field.setAttribute('aria-readonly', 'true');
                    } else {
                        field.disabled = true;
                        field.setAttribute('disabled', 'disabled');
                        field.setAttribute('aria-disabled', 'true');
                    }

                    field.classList.add('permission-readonly-locked');
                });

                form.querySelectorAll('button').forEach(function (button) {
                    if (isNavigationButton(button)) return;

                    const type = String(button.type || 'submit').toLowerCase();
                    const text = String(button.textContent || '').trim();
                    const looksLikeWriteAction =
                        ['submit', 'reset'].includes(type) ||
                        /บันทึก|เพิ่ม|แก้ไข|ลบ|อัปเดต|ยืนยัน|save|submit|update|delete/i.test(text) ||
                        button.matches('[name="submit"], [data-save], [data-delete], .btn-save, .btn-submit, .btn-delete');

                    if (looksLikeWriteAction) {
                        hideElement(button);
                    }
                });

                form.querySelectorAll('a').forEach(function (anchor) {
                    const text = String(anchor.textContent || '').trim();
                    const looksLikeWriteAction =
                        /เพิ่ม|แก้ไข|ลบ|บันทึก|อัปเดต|save|edit|delete|update/i.test(text) ||
                        anchor.matches('[data-save], [data-delete], .btn-save, .btn-edit, .btn-delete');

                    if (looksLikeWriteAction && !anchor.matches('[data-bs-toggle="tab"], [data-toggle="tab"], [data-permission-keep]')) {
                        hideElement(anchor.closest('li') || anchor);
                    }
                });

                lockRichTextEditors(form);
                window.setTimeout(function () { lockRichTextEditors(form); }, 250);
                window.setTimeout(function () { lockRichTextEditors(form); }, 1000);
            }

            // ซ่อนลิงก์ไปยัง Route ที่ไม่มีสิทธิ์ เช่น เพิ่ม แก้ไข ลบ และรายงาน
            document.querySelectorAll('a[href]').forEach(function (anchor) {
                if (routeDenied(anchor.getAttribute('href'), 'GET')) {
                    hideElement(anchor.closest('li') || anchor);
                }
            });

            // จัดการฟอร์มตามสิทธิ์
            document.querySelectorAll('form[action]').forEach(function (form) {
                const method = effectiveFormMethod(form);
                const rule = deniedRule(form.getAttribute('action'), method);

                if (!rule) return;

                /*
                 * จุดสำคัญของ V6.1:
                 * ผู้ใช้มีสิทธิ์ดู แต่ไม่มีสิทธิ์แก้ไข → คงฟอร์มและข้อมูลเดิมไว้
                 * แล้วเปลี่ยนเป็นโหมดอ่านอย่างเดียว แทนการซ่อนทั้งฟอร์ม
                 */
                const shouldRemainVisibleReadOnly =
                    current &&
                    current.view === true &&
                    rule.action === 'update';

                if (shouldRemainVisibleReadOnly) {
                    makeFormReadOnly(form);
                    return;
                }

                // ฟอร์มเพิ่ม/ลบที่ไม่มีสิทธิ์ยังคงซ่อนตามเดิม
                hideElement(form);
            });

            // หาก Modal มีเฉพาะฟอร์มที่ถูกซ่อน ให้ซ่อนปุ่มเปิด Modal ด้วย
            document.querySelectorAll('.modal').forEach(function (modal) {
                const forms = Array.from(modal.querySelectorAll('form'));
                if (forms.length === 0) return;

                const allDenied = forms.every(function (form) {
                    return form.classList.contains('permission-ui-hidden');
                });

                if (!allDenied || !modal.id) return;

                document.querySelectorAll(
                    '[data-bs-target="#' + CSS.escape(modal.id) + '"], ' +
                    '[data-target="#' + CSS.escape(modal.id) + '"]'
                ).forEach(hideElement);
            });

            // ปุ่มพิมพ์แบบ window.print() ไม่มี Route จึงตรวจจากสิทธิ์พิมพ์โดยตรง
            if (current && current.print === false) {
                document.querySelectorAll(
                    'button[onclick*="print"], a[onclick*="print"], .btn-print, [data-print]'
                ).forEach(hideElement);
            }

            // แสดงสถานะอ่านอย่างเดียวอย่างชัดเจน แต่ไม่ซ่อนเนื้อหาเดิม
            if (
                current &&
                current.view === true &&
                current.create === false &&
                current.update === false &&
                current.delete === false
            ) {
                const container = document.querySelector(
                    '.page-content .container-fluid, .content-page .container-fluid, main .container-fluid, main, .content-page'
                );

                if (container && !document.getElementById('permissionReadonlyBanner')) {
                    const banner = document.createElement('div');
                    banner.id = 'permissionReadonlyBanner';
                    banner.className = 'permission-readonly-banner';
                    banner.innerHTML =
                        '<i class="bi bi-eye-fill" aria-hidden="true"></i>' +
                        '<span>โหมดอ่านอย่างเดียว' +
                        '<small>สามารถดูและคัดลอกข้อมูลเดิมได้ แต่ไม่สามารถกรอก แก้ไข บันทึก หรือลบรายการ</small>' +
                        '</span>';
                    container.prepend(banner);
                }
            }
        });
    </script>
@endif
@endonce
