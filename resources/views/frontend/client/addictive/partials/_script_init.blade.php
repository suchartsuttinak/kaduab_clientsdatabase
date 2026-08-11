<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const config = window.addictiveConfig || {};
    const canUpdate = Boolean(config.canUpdate);
    const readonlyRecords = config.readonlyRecords || {};
    const createModalEl = document.getElementById('createAddictiveModal');
    const editModalEl = document.getElementById('editAddictiveModal');
    const createForm = document.getElementById('addictive-form');
    const editForm = document.getElementById('addictive-edit-form');
    const createReferSection = document.getElementById('refer_field_new');
    const editReferSection = document.getElementById('edit_refer_field');

    const filterPanel = document.getElementById('addictiveFilterPanel');
    const filterToggle = document.querySelector('[data-addictive-filter-toggle]');

    function syncAddictiveFilterToggle(isOpen) {
        if (!filterToggle) return;

        filterToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        const icon = filterToggle.querySelector('[data-filter-toggle-icon]');
        const label = filterToggle.querySelector('[data-filter-toggle-label]');

        if (icon) {
            icon.className = isOpen ? 'bi bi-chevron-up' : 'bi bi-funnel';
        }

        if (label) {
            label.textContent = isOpen ? 'ซ่อนการค้นหา' : 'ค้นหารายการ';
        }
    }

    if (filterPanel) {
        syncAddictiveFilterToggle(filterPanel.classList.contains('show'));

        filterPanel.addEventListener('shown.bs.collapse', function () {
            syncAddictiveFilterToggle(true);
            const firstFilter = filterPanel.querySelector('input:not([disabled])');
            window.setTimeout(function () {
                firstFilter?.focus({ preventScroll: true });
            }, 120);
        });

        filterPanel.addEventListener('hidden.bs.collapse', function () {
            syncAddictiveFilterToggle(false);
        });
    }

    [createModalEl, editModalEl].forEach(function (modalEl) {
        if (modalEl && modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
    });

    function isReadonlyForm(form) {
        return form?.dataset?.addictiveReadonly === '1';
    }

    function selectedExam(form) {
        const checked = form?.querySelector('input[name="exam"]:checked');
        return checked ? String(checked.value) : '';
    }

    function syncReferSection(form, section) {
        if (!form || !section) return;

        const mustRefer = selectedExam(form) === '1';
        const readonly = isReadonlyForm(form);
        const referInputs = section.querySelectorAll('input[name="refer"]');
        const referWrap = section.querySelector('[data-refer-wrap]');
        const clientError = section.querySelector('[data-refer-client-error]');

        section.hidden = !mustRefer;
        section.setAttribute('aria-hidden', mustRefer ? 'false' : 'true');

        referInputs.forEach(function (input) {
            input.disabled = readonly || !mustRefer;
            input.required = !readonly && mustRefer;

            if (!mustRefer && !readonly) {
                input.checked = false;
            }
        });

        if (!mustRefer) {
            referWrap?.classList.remove('addictive-refer-invalid');
            clientError?.classList.add('d-none');
        }
    }

    function validateRefer(form, section, showMessage) {
        if (!form || !section || isReadonlyForm(form) || selectedExam(form) !== '1') {
            return true;
        }

        const valid = Boolean(form.querySelector('input[name="refer"]:checked'));
        const referWrap = section.querySelector('[data-refer-wrap]');
        const clientError = section.querySelector('[data-refer-client-error]');

        referWrap?.classList.toggle('addictive-refer-invalid', showMessage && !valid);
        clientError?.classList.toggle('d-none', !showMessage || valid);
        return valid;
    }

    function focusFirstInvalid(form, section) {
        const firstInvalid = form.querySelector(':invalid, .is-invalid');
        const fallback = section?.querySelector('input[name="refer"]:not(:disabled)');
        const target = firstInvalid || fallback;
        if (!target) return;

        const scrollTarget = target.closest('.form-section, [data-refer-wrap]') || target;
        scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });

        window.setTimeout(function () {
            try {
                target.focus({ preventScroll: true });
            } catch (error) {
                target.focus();
            }
        }, 220);
    }

    function bindForm(form, modalEl, section) {
        if (!form) return;

        form.querySelectorAll('input[name="exam"]').forEach(function (input) {
            input.addEventListener('change', function () {
                syncReferSection(form, section);
                validateRefer(form, section, false);
            });
        });

        form.querySelectorAll('input[name="refer"]').forEach(function (input) {
            input.addEventListener('change', function () {
                validateRefer(form, section, false);
            });
        });

        form.querySelectorAll('input, textarea, select').forEach(function (field) {
            const clearError = function () {
                field.classList.remove('is-invalid');
                field.removeAttribute('aria-invalid');
            };
            field.addEventListener('input', clearError);
            field.addEventListener('change', clearError);
        });

        form.addEventListener('submit', function (event) {
            if (isReadonlyForm(form)) {
                event.preventDefault();
                event.stopImmediatePropagation();
                return;
            }

            syncReferSection(form, section);
            const referValid = validateRefer(form, section, true);
            form.classList.add('was-validated');

            if (!form.checkValidity() || !referValid) {
                event.preventDefault();
                event.stopPropagation();
                focusFirstInvalid(form, section);
                return;
            }

            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) submitButton.disabled = true;
        });

        modalEl?.addEventListener('show.bs.modal', function () {
            document.body.classList.add('addictive-modal-open');
            syncReferSection(form, section);
        });

        modalEl?.addEventListener('shown.bs.modal', function () {
            const modalBody = modalEl.querySelector('.modal-body');
            if (modalBody) modalBody.scrollTop = 0;
        });

        modalEl?.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('addictive-modal-open');
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton && !isReadonlyForm(form)) submitButton.disabled = false;
        });

        syncReferSection(form, section);
    }

    function setValue(id, value) {
        const element = document.getElementById(id);
        if (element) element.value = value ?? '';
    }

    function setEditReadonlyMode(readonly) {
        if (!editForm || !editModalEl) return;

        editForm.dataset.addictiveReadonly = readonly ? '1' : '0';
        editModalEl.dataset.addictiveReadonly = readonly ? '1' : '0';

        const title = document.getElementById('editAddictiveLabel');
        const subtitle = document.getElementById('editAddictiveSubtitle');
        const titleIcon = document.getElementById('editAddictiveIcon');
        const submitButton = document.getElementById('addictive-edit-submit');

        if (title) title.textContent = readonly
            ? 'รายละเอียดการตรวจสารเสพติด'
            : 'แก้ไขข้อมูลการตรวจสารเสพติด';
        if (subtitle) subtitle.textContent = readonly
            ? 'แสดงข้อมูลแบบอ่านอย่างเดียว'
            : 'ปรับปรุงผลการตรวจและแนวทางดำเนินการต่อ';
        if (titleIcon) titleIcon.className = readonly ? 'bi bi-eye' : 'bi bi-pencil-square';
        if (submitButton) submitButton.classList.toggle('d-none', readonly);

        editForm.querySelectorAll('input, textarea, select').forEach(function (field) {
            if (field.type === 'hidden') return;

            const type = String(field.type || '').toLowerCase();
            const alwaysReadonly = field.id === 'edit_count';

            if (type === 'radio' || type === 'checkbox' || field.tagName === 'SELECT') {
                field.disabled = readonly;
            } else {
                field.readOnly = readonly || alwaysReadonly;
            }

            field.toggleAttribute('aria-readonly', readonly);
            field.classList.toggle('bg-light', readonly);
        });

        syncReferSection(editForm, editReferSection);
    }

    function fillEditForm(data, readonly) {
        if (!editForm || !data) return;

        const id = data.id ?? '';
        if (!readonly) {
            editForm.action = config.updateBaseUrl + '/' + encodeURIComponent(id);
        } else {
            editForm.removeAttribute('action');
        }

        setValue('edit_id', id);
        setValue('edit_date', data.date);
        setValue('edit_count', data.count);
        setValue('edit_record', data.record);
        setValue('edit_recorder', data.recorder);

        editForm.querySelectorAll('input[name="exam"]').forEach(function (input) {
            input.checked = String(input.value) === String(data.exam ?? '');
        });

        editForm.querySelectorAll('input[name="refer"]').forEach(function (input) {
            input.checked = String(input.value) === String(data.refer ?? '');
        });

        setEditReadonlyMode(Boolean(readonly));
        syncReferSection(editForm, editReferSection);
    }

    window.openAddictiveReadonly = function (id) {
        const record = readonlyRecords[String(id)] || readonlyRecords[id];
        if (!record || !editModalEl || !editForm || !window.bootstrap) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่พบข้อมูล',
                    text: 'ไม่สามารถเปิดข้อมูลรายการนี้ได้',
                    confirmButtonText: 'OK'
                });
            }
            return;
        }

        fillEditForm(record, true);
        bootstrap.Modal.getOrCreateInstance(editModalEl).show();
    };

    window.openEditAddictive = async function (id) {
        if (!canUpdate) {
            window.openAddictiveReadonly(id);
            return;
        }

        if (!id || !editModalEl || !editForm || !window.bootstrap) return;

        try {
            const response = await fetch(config.jsonUrl + '/' + encodeURIComponent(id), {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('ไม่สามารถโหลดข้อมูลสำหรับแก้ไขได้');

            const data = await response.json();
            fillEditForm(data, false);
            bootstrap.Modal.getOrCreateInstance(editModalEl).show();
        } catch (error) {
            if (window.Swal) {
                Swal.fire({
                    title: 'ไม่สามารถเปิดข้อมูลได้',
                    text: error.message || 'กรุณาลองใหม่อีกครั้ง',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                window.alert(error.message || 'ไม่สามารถเปิดข้อมูลได้');
            }
        }
    };

    window.confirmDelete = function (formId, message) {
        const deleteForm = document.getElementById(formId);
        if (!deleteForm) return;

        if (window.Swal) {
            Swal.fire({
                title: 'ยืนยันการลบข้อมูล',
                text: message || 'ข้อมูลที่ลบแล้วจะไม่สามารถกู้คืนได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'ลบข้อมูล',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) deleteForm.submit();
            });
            return;
        }

        if (window.confirm(message || 'ยืนยันการลบข้อมูลนี้ใช่หรือไม่?')) {
            deleteForm.submit();
        }
    };

    bindForm(createForm, createModalEl, createReferSection);
    bindForm(editForm, editModalEl, editReferSection);

    if (config.addHasErrors && createModalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(createModalEl, {
            backdrop: 'static',
            keyboard: false
        }).show();
    }

    if (config.editHasErrors && editModalEl && editForm && window.bootstrap && config.editOldValues?.id) {
        fillEditForm(config.editOldValues, !canUpdate);
        bootstrap.Modal.getOrCreateInstance(editModalEl, {
            backdrop: 'static',
            keyboard: false
        }).show();
    }

    const startDate = document.getElementById('addictive_date_from');
    const endDate = document.getElementById('addictive_date_to');
    function syncDateRange() {
        if (!endDate) return;
        if (startDate?.value) endDate.min = startDate.value;
        else endDate.removeAttribute('min');
    }
    startDate?.addEventListener('change', syncDateRange);
    syncDateRange();


    const flash = config.flash || {};
    if (flash.message && window.Swal) {
        const type = String(flash.type || 'success').toLowerCase();
        Swal.fire({
            icon: ['success', 'error', 'warning', 'info'].includes(type) ? type : 'success',
            title: type === 'success' ? 'ดำเนินการสำเร็จ' : 'แจ้งเตือน',
            text: flash.message,
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: true,
            confirmButtonText: 'OK'
        });
    }
});

window.addEventListener('pageshow', function () {
    document.querySelectorAll('#addictive-form button[type="submit"], #addictive-edit-form button[type="submit"]').forEach(function (button) {
        if (!button.classList.contains('d-none')) button.disabled = false;
    });
});
</script>
