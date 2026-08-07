@php
    $psyFormContext = old('_form_context');
    $psyEditOldValues = [
        'id' => old('_edit_id'),
        'sent_date' => old('sent_date'),
        'hotpital' => old('hotpital'),
        'psycho_id' => old('psycho_id'),
        'diagnose' => old('diagnose'),
        'appoin_date' => old('appoin_date'),
        'drug_no' => old('drug_no'),
        'drug_name' => old('drug_name'),
        'disa_no' => old('disa_no'),
        'client_id' => old('client_id', $client->id),
    ];
    $psyValidationMessages = $errors->all();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const config = window.psychiatricConfig || {};
    const createModalElement = document.getElementById('createPsychiatricModal');
    const editModalElement = document.getElementById('editPsychiatricModal');
    const createForm = document.getElementById('psychiatric-form');
    const editForm = document.getElementById('psychiatric-edit-form');
    const filterPanel = document.getElementById('psychiatricFilterPanel');
    const filterToggle = document.querySelector('[data-psychiatric-filter-toggle]');
    const formContext = @json($psyFormContext);
    const editOldValues = @json($psyEditOldValues);
    const validationMessages = @json($psyValidationMessages);

    function modalInstance(element) {
        if (!element || !window.bootstrap || !bootstrap.Modal) {
            return null;
        }

        return bootstrap.Modal.getOrCreateInstance(element, {
            backdrop: 'static',
            keyboard: false
        });
    }


    function syncPsychiatricFilterToggle(isOpen) {
        if (!filterToggle) {
            return;
        }

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
        syncPsychiatricFilterToggle(filterPanel.classList.contains('show'));

        filterPanel.addEventListener('shown.bs.collapse', function () {
            syncPsychiatricFilterToggle(true);

            const firstFilter = filterPanel.querySelector('input:not([disabled])');
            if (firstFilter) {
                window.setTimeout(function () {
                    firstFilter.focus({ preventScroll: true });
                }, 100);
            }
        });

        filterPanel.addEventListener('hidden.bs.collapse', function () {
            syncPsychiatricFilterToggle(false);
        });
    }

    [createModalElement, editModalElement].forEach(function (modalElement) {
        if (modalElement && modalElement.parentElement !== document.body) {
            document.body.appendChild(modalElement);
        }

        modalElement?.addEventListener('show.bs.modal', function () {
            document.body.classList.add('psychiatric-modal-open');
        });

        modalElement?.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('psychiatric-modal-open');
            modalElement.querySelectorAll('button[type="submit"]').forEach(function (button) {
                button.disabled = false;
            });
        });
    });

    function setFieldValue(form, name, value) {
        if (!form) {
            return;
        }

        const field = form.elements.namedItem(name);
        if (!field) {
            return;
        }

        if (field instanceof RadioNodeList) {
            Array.from(form.querySelectorAll('[name="' + name + '"]')).forEach(function (radio) {
                radio.checked = String(radio.value) === String(value ?? '');
            });
            return;
        }

        field.value = value ?? '';
    }

    function selectedRadioValue(form, name) {
        return form?.querySelector('input[name="' + name + '"]:checked')?.value || '';
    }

    function syncAppointmentMinimum(form) {
        const sentDate = form?.querySelector('input[name="sent_date"]');
        const appointmentDate = form?.querySelector('input[name="appoin_date"]');

        if (!appointmentDate) {
            return;
        }

        if (sentDate?.value) {
            appointmentDate.min = sentDate.value;
        } else {
            appointmentDate.removeAttribute('min');
        }
    }

    function syncDrugField(form) {
        if (!form) {
            return;
        }

        const isEdit = form.id === 'psychiatric-edit-form';
        const wrapper = document.getElementById(isEdit ? 'edit_drug_name_field' : 'drug_name_field_new');
        const drugName = form.querySelector('input[name="drug_name"]');
        const receivesDrug = selectedRadioValue(form, 'drug_no') === 'yes';

        wrapper?.classList.toggle('is-hidden', !receivesDrug);

        if (drugName) {
            drugName.disabled = !receivesDrug;
            drugName.required = receivesDrug;

            if (!receivesDrug) {
                drugName.value = '';
                drugName.classList.remove('is-invalid');
            }
        }
    }

    function initializeForm(form) {
        if (!form) {
            return;
        }

        form.querySelectorAll('input[name="drug_no"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                syncDrugField(form);
            });
        });

        const sentDate = form.querySelector('input[name="sent_date"]');
        sentDate?.addEventListener('input', function () {
            syncAppointmentMinimum(form);
        });
        sentDate?.addEventListener('change', function () {
            syncAppointmentMinimum(form);
        });

        form.querySelectorAll('.form-control, .form-select').forEach(function (field) {
            ['input', 'change'].forEach(function (eventName) {
                field.addEventListener(eventName, function () {
                    field.classList.remove('is-invalid');
                });
            });
        });

        form.addEventListener('submit', function (event) {
            syncDrugField(form);
            syncAppointmentMinimum(form);
            form.classList.add('was-validated');

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    window.setTimeout(function () {
                        firstInvalid.focus({ preventScroll: true });
                    }, 250);
                }
                return;
            }

            // ป้องกันการกดซ้ำ โดยไม่เพิ่ม spinner และไม่เปลี่ยนข้อความบนปุ่ม
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
            }
        });

        syncAppointmentMinimum(form);
        syncDrugField(form);
    }

    initializeForm(createForm);
    initializeForm(editForm);

    function populateEditForm(data, updateUrl) {
        if (!editForm || !data) {
            return;
        }

        const id = data.id || data._edit_id;
        editForm.action = updateUrl || (config.updateBaseUrl + '/' + id);

        setFieldValue(editForm, '_edit_id', id);
        setFieldValue(editForm, 'client_id', data.client_id);
        setFieldValue(editForm, 'sent_date', data.sent_date);
        setFieldValue(editForm, 'hotpital', data.hotpital);
        setFieldValue(editForm, 'psycho_id', data.psycho_id);
        setFieldValue(editForm, 'diagnose', data.diagnose);
        setFieldValue(editForm, 'appoin_date', data.appoin_date);
        setFieldValue(editForm, 'drug_no', data.drug_no || 'no');
        setFieldValue(editForm, 'drug_name', data.drug_name);
        setFieldValue(editForm, 'disa_no', data.disa_no || 'no');

        syncAppointmentMinimum(editForm);
        syncDrugField(editForm);
    }

    document.querySelectorAll('.js-psychiatric-edit').forEach(function (button) {
        button.addEventListener('click', async function () {
            const id = button.dataset.id;
            const editUrl = button.dataset.editUrl || (config.editJsonUrl + '/' + id);
            const updateUrl = button.dataset.updateUrl || (config.updateBaseUrl + '/' + id);
            button.disabled = true;

            try {
                const response = await fetch(editUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('ไม่สามารถโหลดข้อมูลสำหรับแก้ไขได้');
                }

                const data = await response.json();
                populateEditForm(data, updateUrl);
                modalInstance(editModalElement)?.show();
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
            } finally {
                button.disabled = false;
            }
        });
    });

    document.querySelectorAll('.js-psychiatric-delete').forEach(function (button) {
        button.addEventListener('click', function () {
            const form = document.getElementById(button.dataset.formId || '');
            if (!form) {
                return;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ยืนยันการลบข้อมูล',
                    text: 'ข้อมูลที่ลบแล้วจะไม่สามารถกู้คืนได้',
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
                        form.submit();
                    }
                });
                return;
            }

            if (window.confirm('ยืนยันการลบข้อมูลนี้ใช่หรือไม่?')) {
                form.submit();
            }
        });
    });

    const filterStart = document.getElementById('psych_date_from');
    const filterEnd = document.getElementById('psych_date_to');

    function syncFilterDates() {
        if (!filterEnd) {
            return;
        }

        if (filterStart?.value) {
            filterEnd.min = filterStart.value;
        } else {
            filterEnd.removeAttribute('min');
        }
    }

    filterStart?.addEventListener('input', syncFilterDates);
    filterStart?.addEventListener('change', syncFilterDates);
    syncFilterDates();

    function setupPsychiatricDataTable() {
        const tableElement = document.getElementById('datatable-psychiatric');
        if (!tableElement || !window.jQuery || !jQuery.fn.DataTable) {
            return;
        }

        const $table = jQuery(tableElement);

        /*
         * Layout หรือสคริปต์เดิมอาจ initialize ตารางไว้ก่อนแล้ว
         * ทำลาย instance เดิมและสร้างใหม่เพียงครั้งเดียว เพื่อป้องกัน
         * ช่องค้นหา/จำนวนรายการซ้ำ หัวตารางเหลื่อม และ scrollbar ซ้อน
         */
        if (jQuery.fn.DataTable.isDataTable(tableElement)) {
            $table.DataTable().destroy();
        }

        const dataTable = $table.DataTable({
            destroy: true,
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            order: [[0, 'desc']],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            dom: '<"psy-dt-top"<"psy-dt-length"l><"psy-dt-search"f>>rt<"psy-dt-bottom"<"psy-dt-info"i><"psy-dt-paging"p>>',
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
                loadingRecords: 'กำลังโหลด...',
                processing: 'กำลังประมวลผล...',
                search: 'ค้นหา:',
                zeroRecords: 'ไม่พบข้อมูลที่ตรงกับการค้นหา',
                paginate: {
                    first: 'หน้าแรก',
                    last: 'หน้าสุดท้าย',
                    next: 'ถัดไป',
                    previous: 'ก่อนหน้า'
                }
            },
            initComplete: function () {
                const api = this.api();
                api.columns.adjust();

                const wrapper = tableElement.closest('.dataTables_wrapper');
                wrapper?.setAttribute('data-permission-keep', '');
                wrapper?.querySelectorAll('input, select, button, a').forEach(function (element) {
                    element.setAttribute('data-permission-keep', '');
                });
            }
        });

        const tableWrapper = tableElement.closest('.psy-inline-table-wrap');
        tableWrapper?.classList.add('is-datatable-ready');

        window.requestAnimationFrame(function () {
            dataTable.columns.adjust().draw(false);
        });

        let resizeTimer = null;
        window.addEventListener('resize', function () {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(function () {
                dataTable.columns.adjust();
            }, 120);
        });
    }

    /* รอ Layout และสคริปต์ส่วนกลางทำงานก่อน แล้ว normalize DataTable หนึ่งครั้ง */
    window.setTimeout(setupPsychiatricDataTable, 50);

    if (formContext === 'psychiatric_create') {
        modalInstance(createModalElement)?.show();
    }

    if (formContext === 'psychiatric_edit' && editOldValues.id) {
        populateEditForm(editOldValues, config.updateBaseUrl + '/' + editOldValues.id);
        modalInstance(editModalElement)?.show();
    }

    if (validationMessages.length > 0 && typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'กรุณาตรวจสอบข้อมูล',
            html: validationMessages.map(function (message) {
                const element = document.createElement('div');
                element.textContent = message;
                return element.innerHTML;
            }).join('<br>'),
            icon: 'error',
            confirmButtonText: 'ตกลง'
        });
    }
});
</script>
