(function (window, document) {
    'use strict';

    const registry = new Map();
    let resizeTimer = null;

    function hasDataTables() {
        return !!(
            window.jQuery &&
            window.jQuery.fn &&
            window.jQuery.fn.DataTable
        );
    }

    function resolveTable(target) {
        if (!target) return null;
        if (typeof target === 'string') return document.querySelector(target);
        if (target.nodeType === 1) return target;
        if (target.jquery && target.length) return target[0];
        return null;
    }

    function markPermissionKeep(tableElement) {
        const wrapper = tableElement?.closest('.dataTables_wrapper');
        if (!wrapper) return;

        wrapper.querySelectorAll(
            '.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate'
        ).forEach(function (controlArea) {
            controlArea.setAttribute('data-permission-keep', '');
            controlArea.querySelectorAll('input, select, button, a').forEach(function (element) {
                element.setAttribute('data-permission-keep', '');
            });
        });
    }

    function markReady(tableElement) {
        if (!tableElement) return;
        tableElement.dataset.datatableState = 'ready';
        tableElement.classList.add('is-datatable-ready');
    }

    function scheduleAdjust(api) {
        if (!api || !api.columns) return;

        window.requestAnimationFrame(function () {
            try {
                api.columns.adjust();
            } catch (error) {
                // ตารางอาจถูกนำออกจาก DOM ระหว่างเปลี่ยนหน้า ไม่ต้องรบกวนผู้ใช้
            }
        });
    }

    function register(tableElement, api) {
        if (!tableElement || !api) return;
        registry.set(tableElement, api);
        markReady(tableElement);
        markPermissionKeep(tableElement);
    }

    function mount(target, options) {
        const tableElement = resolveTable(target);
        if (!tableElement || !hasDataTables()) return null;

        const $ = window.jQuery;
        const $table = $(tableElement);

        tableElement.setAttribute('data-datatable-owner', 'page');

        if ($.fn.DataTable.isDataTable(tableElement)) {
            const existing = $table.DataTable();
            register(tableElement, existing);
            scheduleAdjust(existing);
            return existing;
        }

        const userOptions = options || {};
        const originalInitComplete = userOptions.initComplete;

        const stableDefaults = {
            destroy: false,
            responsive: false,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
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
            }
        };

        const finalOptions = $.extend(true, {}, stableDefaults, userOptions, {
            destroy: false,
            initComplete: function (settings, json) {
                const api = this.api();
                register(tableElement, api);

                if (typeof originalInitComplete === 'function') {
                    originalInitComplete.call(this, settings, json);
                }

                scheduleAdjust(api);
            }
        });

        const api = $table.DataTable(finalOptions);
        register(tableElement, api);
        return api;
    }

    function adjust(target) {
        const tableElement = resolveTable(target);
        if (!tableElement || !hasDataTables()) return;

        const $ = window.jQuery;
        if (!$.fn.DataTable.isDataTable(tableElement)) return;

        const api = $(tableElement).DataTable();
        register(tableElement, api);
        scheduleAdjust(api);
    }

    function adjustAll() {
        registry.forEach(function (api, tableElement) {
            if (!document.documentElement.contains(tableElement)) {
                registry.delete(tableElement);
                return;
            }
            scheduleAdjust(api);
        });
    }

    window.addEventListener('resize', function () {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(adjustAll, 140);
    }, { passive: true });

    window.KaduabDataTable = Object.freeze({
        mount: mount,
        adjust: adjust,
        adjustAll: adjustAll,
        markPermissionKeep: markPermissionKeep
    });
})(window, document);
