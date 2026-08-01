<script>
document.addEventListener('DOMContentLoaded', function () {
    const config = window.addictiveConfig || {};
    const createModalEl = document.getElementById('createAddictiveModal');
    const editModalEl = document.getElementById('editAddictiveModal');
    const createForm = document.getElementById('addictive-form');
    const editForm = document.getElementById('addictive-edit-form');

    [createModalEl, editModalEl].forEach(function (modalEl) {
        if (modalEl && modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
    });

    function selectedExam(form) {
        const checked = form?.querySelector('input[name="exam"]:checked');
        return checked ? String(checked.value) : '';
    }

    function syncReferSection(form, section) {
        if (!form || !section) {
            return;
        }

        const mustRefer = selectedExam(form) === '1';
        const referInputs = section.querySelectorAll('input[name="refer"]');
        const referWrap = section.querySelector('[data-refer-wrap]');
        const clientError = section.querySelector('[data-refer-client-error]');

        section.hidden = !mustRefer;
        section.setAttribute('aria-hidden', mustRefer ? 'false' : 'true');

        referInputs.forEach(function (input) {
            input.disabled = !mustRefer;
            input.required = mustRefer;

            if (!mustRefer) {
                input.checked = false;
            }
        });

        if (!mustRefer) {
            referWrap?.classList.remove('addictive-refer-invalid');
            clientError?.classList.add('d-none');
        }
    }

    function validateRefer(form, section, showMessage) {
        if (!form || !section || selectedExam(form) !== '1') {
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

        if (!target) {
            return;
        }

        const scrollTarget = target.closest('.form-section, [data-refer-wrap]') || target;
        scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });

        window.setTimeout(function () {
            try {
                target.focus({ preventScroll: true });
            } catch (error) {
                target.focus();
            }
        }, 250);
    }

    function bindForm(form, modalEl, section) {
        if (!form) {
            return;
        }

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
            if (submitButton) {
                // ป้องกันการกดซ้ำ โดยคงข้อความและไอคอนเดิม ไม่มี spinner
                submitButton.disabled = true;
            }
        });

        modalEl?.addEventListener('show.bs.modal', function () {
            document.body.classList.add('addictive-modal-open');
            syncReferSection(form, section);
        });

        modalEl?.addEventListener('shown.bs.modal', function () {
            const modalBody = modalEl.querySelector('.modal-body');
            if (modalBody) {
                modalBody.scrollTop = 0;
            }
        });

        modalEl?.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('addictive-modal-open');
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = false;
            }
        });

        syncReferSection(form, section);
    }

    const createReferSection = document.getElementById('refer_field_new');
    const editReferSection = document.getElementById('edit_refer_field');

    bindForm(createForm, createModalEl, createReferSection);
    bindForm(editForm, editModalEl, editReferSection);

    function fillEditForm(data) {
        if (!editForm || !data) {
            return;
        }

        const id = data.id ?? '';
        editForm.action = config.updateBaseUrl + '/' + encodeURIComponent(id);

        const editId = document.getElementById('edit_id');
        const editDate = document.getElementById('edit_date');
        const editCount = document.getElementById('edit_count');
        const editRecord = document.getElementById('edit_record');
        const editRecorder = document.getElementById('edit_recorder');

        if (editId) editId.value = id;
        if (editDate) editDate.value = data.date ?? '';
        if (editCount) editCount.value = data.count ?? '';
        if (editRecord) editRecord.value = data.record ?? '';
        if (editRecorder) editRecorder.value = data.recorder ?? '';

        editForm.querySelectorAll('input[name="exam"]').forEach(function (input) {
            input.checked = String(input.value) === String(data.exam ?? '');
        });

        editForm.querySelectorAll('input[name="refer"]').forEach(function (input) {
            input.checked = String(input.value) === String(data.refer ?? '');
        });

        syncReferSection(editForm, editReferSection);
    }

    window.openEditAddictive = async function (id) {
        if (!id || !editModalEl || !editForm || !window.bootstrap) {
            return;
        }

        try {
            const response = await fetch(
                config.jsonUrl + '/' + encodeURIComponent(id),
                {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error('ไม่สามารถโหลดข้อมูลสำหรับแก้ไขได้');
            }

            const data = await response.json();
            fillEditForm(data);
            bootstrap.Modal.getOrCreateInstance(editModalEl).show();
        } catch (error) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ไม่สามารถเปิดข้อมูลได้',
                    text: error.message || 'กรุณาลองใหม่อีกครั้ง',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            } else {
                window.alert(error.message || 'ไม่สามารถเปิดข้อมูลได้');
            }
        }
    };

    window.confirmDelete = function (formId, message) {
        const deleteForm = document.getElementById(formId);
        if (!deleteForm) {
            return;
        }

        if (typeof Swal !== 'undefined') {
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
                if (result.isConfirmed) {
                    deleteForm.submit();
                }
            });
            return;
        }

        if (window.confirm(message || 'ยืนยันการลบข้อมูลนี้ใช่หรือไม่?')) {
            deleteForm.submit();
        }
    };

    if (config.addHasErrors && createModalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(createModalEl, {
            backdrop: 'static',
            keyboard: false
        }).show();
    }

    if (config.editHasErrors && editModalEl && window.bootstrap && config.editOldValues?.id) {
        fillEditForm(config.editOldValues);
        bootstrap.Modal.getOrCreateInstance(editModalEl, {
            backdrop: 'static',
            keyboard: false
        }).show();
    }

    if (window.jQuery && $.fn.DataTable && document.getElementById('datatable-addictive')) {
        const table = $('#datatable-addictive');

        if (!$.fn.DataTable.isDataTable(table)) {
            const dataTable = table.DataTable({
                autoWidth: false,
                scrollX: false,
                order: [[1, 'asc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                        targets: -1
                    }
                ],
                language: {
                    emptyTable: 'ไม่พบข้อมูล',
                    info: 'แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ',
                    infoEmpty: 'แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ',
                    infoFiltered: '(กรองจากทั้งหมด _MAX_ รายการ)',
                    lengthMenu: 'แสดง _MENU_ รายการ',
                    search: 'ค้นหา:',
                    zeroRecords: 'ไม่พบข้อมูลที่ตรงกับการค้นหา',
                    paginate: {
                        first: 'หน้าแรก',
                        last: 'หน้าสุดท้าย',
                        next: 'ถัดไป',
                        previous: 'ก่อนหน้า'
                    }
                }
            });

            let resizeTimer = null;
            window.addEventListener('resize', function () {
                window.clearTimeout(resizeTimer);
                resizeTimer = window.setTimeout(function () {
                    dataTable.columns.adjust();
                }, 120);
            });
        }
    }
});

window.addEventListener('pageshow', function () {
    document.querySelectorAll('#addictive-form button[type="submit"], #addictive-edit-form button[type="submit"]').forEach(function (button) {
        button.disabled = false;
    });
});
</script>
