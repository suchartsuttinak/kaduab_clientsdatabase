(function (window, document) {
    'use strict';

    function escapeSelector(value) {
        if (window.CSS && typeof window.CSS.escape === 'function') {
            return window.CSS.escape(value);
        }
        return String(value).replace(/(["'\\.#:[\]()=<>+~*^$| ])/g, '\\$1');
    }

    function normaliseText(value) {
        return String(value ?? '')
            .replace(/\s+/g, ' ')
            .trim()
            .toLocaleLowerCase('th-TH');
    }

    function parseSortableValue(raw) {
        const value = String(raw ?? '').trim();
        if (value === '') return { type: 'text', value: '' };

        if (/^-?\d+(?:[.,]\d+)?$/.test(value.replace(/,/g, ''))) {
            return { type: 'number', value: Number(value.replace(/,/g, '')) };
        }

        const iso = value.match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (iso) return { type: 'number', value: Number(iso[1] + iso[2] + iso[3]) };

        const thaiDate = value.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
        if (thaiDate) {
            let year = Number(thaiDate[3]);
            if (year > 2400) year -= 543;
            return {
                type: 'number',
                value: year * 10000 + Number(thaiDate[2]) * 100 + Number(thaiDate[1])
            };
        }

        return { type: 'text', value: normaliseText(value) };
    }

    function getControls(table) {
        const id = table.id;
        if (!id) return {};
        const safe = escapeSelector(id);
        return {
            toolbar: document.querySelector('[data-kst-controls="' + safe + '"]'),
            footer: document.querySelector('[data-kst-footer="' + safe + '"]')
        };
    }

    function analyseHeaders(table) {
        const headers = Array.from(table.querySelectorAll('thead th'));
        const nonSearchColumns = new Set();

        headers.forEach(function (header, index) {
            const label = normaliseText(header.textContent);
            const isActionColumn = /^(จัดการ|การจัดการ|action|actions)$/.test(label);
            const hasCheckbox = !!header.querySelector('input[type="checkbox"]');
            const noSort = header.hasAttribute('data-kst-nosort') ||
                header.getAttribute('data-orderable') === 'false' ||
                isActionColumn ||
                hasCheckbox;
            const noSearch = header.hasAttribute('data-kst-nosearch') ||
                header.getAttribute('data-searchable') === 'false' ||
                isActionColumn ||
                hasCheckbox;

            if (noSearch) nonSearchColumns.add(index);

            if (!noSort) {
                header.dataset.kstSortable = '1';
                header.tabIndex = 0;
                header.setAttribute('role', 'button');
                header.setAttribute('aria-sort', 'none');
                header.dataset.kstColumn = String(index);
            }
        });

        return nonSearchColumns;
    }

    function init(table) {
        if (!table || table.dataset.kstInitialized === '1') return;
        table.dataset.kstInitialized = '1';

        const tbody = table.tBodies[0];
        if (!tbody) {
            table.classList.add('kst-ready');
            return;
        }

        const originalRows = Array.from(tbody.rows).filter(function (row) {
            return !row.classList.contains('kst-no-results');
        });

        const controls = getControls(table);
        const toolbar = controls.toolbar;
        const footer = controls.footer;
        const searchInput = toolbar?.querySelector('[data-kst-search]') || null;
        const lengthSelect = toolbar?.querySelector('[data-kst-length]') || null;
        const info = footer?.querySelector('[data-kst-info]') || null;
        const pageLabel = footer?.querySelector('[data-kst-page-label]') || null;
        const prevButton = footer?.querySelector('[data-kst-prev]') || null;
        const nextButton = footer?.querySelector('[data-kst-next]') || null;

        let pageLength = Number(table.dataset.pageLength || lengthSelect?.value || 10);
        if (!Number.isFinite(pageLength) || pageLength < 1) pageLength = 10;
        let currentPage = 1;
        let query = '';
        let sortColumn = null;
        let sortDirection = 'asc';
        let workingRows = originalRows.slice();
        let noResultsRow = null;

        const nonSearchColumns = analyseHeaders(table);

        originalRows.forEach(function (row, index) {
            row.dataset.kstOriginalIndex = String(index);
            row.dataset.kstSearchText = normaliseText(
                Array.from(row.cells)
                    .filter(function (_cell, cellIndex) { return !nonSearchColumns.has(cellIndex); })
                    .map(function (cell) { return cell.textContent; })
                    .join(' ')
            );
        });

        function filteredRows() {
            if (!query) return workingRows.slice();
            return workingRows.filter(function (row) {
                return row.dataset.kstSearchText.includes(query);
            });
        }

        function updateRowNumbers(rows, start) {
            const firstHeader = table.querySelector('thead th:first-child');
            if (!firstHeader || normaliseText(firstHeader.textContent) !== 'ลำดับ') return;

            rows.forEach(function (row, index) {
                const firstCell = row.cells[0];
                if (firstCell) firstCell.textContent = String(start + index + 1);
            });
        }

        function render() {
            const matched = filteredRows();
            const totalFiltered = matched.length;
            const totalPages = Math.max(1, Math.ceil(totalFiltered / pageLength));
            currentPage = Math.min(Math.max(1, currentPage), totalPages);

            const startIndex = (currentPage - 1) * pageLength;
            const endIndex = Math.min(startIndex + pageLength, totalFiltered);
            const visibleRows = matched.slice(startIndex, endIndex);
            const visibleSet = new Set(visibleRows);

            originalRows.forEach(function (row) {
                row.hidden = !visibleSet.has(row);
                row.style.display = visibleSet.has(row) ? '' : 'none';
            });

            if (noResultsRow) {
                noResultsRow.remove();
                noResultsRow = null;
            }

            if (totalFiltered === 0) {
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'kst-no-results';
                const cell = document.createElement('td');
                cell.colSpan = Math.max(1, table.querySelectorAll('thead th').length);
                cell.textContent = 'ไม่พบข้อมูลที่ตรงกับการค้นหา';
                noResultsRow.appendChild(cell);
                tbody.appendChild(noResultsRow);
            }

            updateRowNumbers(visibleRows, startIndex);

            if (info) {
                if (totalFiltered === 0) {
                    info.textContent = 'แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ';
                } else {
                    const filteredSuffix = totalFiltered !== originalRows.length
                        ? ' (กรองจากทั้งหมด ' + originalRows.length + ' รายการ)'
                        : '';
                    info.textContent = 'แสดง ' + (startIndex + 1) + ' ถึง ' + endIndex +
                        ' จากทั้งหมด ' + totalFiltered + ' รายการ' + filteredSuffix;
                }
            }

            if (pageLabel) pageLabel.textContent = 'หน้า ' + currentPage + ' / ' + totalPages;
            if (prevButton) prevButton.disabled = currentPage <= 1 || totalFiltered === 0;
            if (nextButton) nextButton.disabled = currentPage >= totalPages || totalFiltered === 0;

            table.classList.add('kst-ready');
        }

        function sortByColumn(columnIndex, header) {
            if (sortColumn === columnIndex) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = columnIndex;
                sortDirection = 'asc';
            }

            table.querySelectorAll('thead th[data-kst-sortable="1"]').forEach(function (item) {
                item.setAttribute('aria-sort', item === header
                    ? (sortDirection === 'asc' ? 'ascending' : 'descending')
                    : 'none');
            });

            workingRows.sort(function (a, b) {
                const aCell = a.cells[columnIndex];
                const bCell = b.cells[columnIndex];
                const aRaw = aCell?.dataset.order ?? aCell?.textContent ?? '';
                const bRaw = bCell?.dataset.order ?? bCell?.textContent ?? '';
                const av = parseSortableValue(aRaw);
                const bv = parseSortableValue(bRaw);

                let result;
                if (av.type === 'number' && bv.type === 'number') {
                    result = av.value - bv.value;
                } else {
                    result = String(av.value).localeCompare(String(bv.value), 'th', { numeric: true });
                }

                if (result === 0) {
                    result = Number(a.dataset.kstOriginalIndex) - Number(b.dataset.kstOriginalIndex);
                }
                return sortDirection === 'asc' ? result : -result;
            });

            workingRows.forEach(function (row) { tbody.appendChild(row); });
            currentPage = 1;
            render();
        }

        table.querySelectorAll('thead th[data-kst-sortable="1"]').forEach(function (header) {
            const handler = function () {
                sortByColumn(Number(header.dataset.kstColumn), header);
            };
            header.addEventListener('click', handler);
            header.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    handler();
                }
            });
        });

        searchInput?.addEventListener('input', function () {
            query = normaliseText(searchInput.value);
            currentPage = 1;
            render();
        });

        lengthSelect?.addEventListener('change', function () {
            const requested = Number(lengthSelect.value);
            if (Number.isFinite(requested) && requested > 0) {
                pageLength = requested;
                table.dataset.pageLength = String(requested);
                currentPage = 1;
                render();
            }
        });

        prevButton?.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage -= 1;
                render();
            }
        });

        nextButton?.addEventListener('click', function () {
            currentPage += 1;
            render();
        });

        render();
    }

    function initAll(root) {
        (root || document).querySelectorAll('table[data-stable-table]').forEach(init);
    }

    window.KaduabStableTable = Object.freeze({ init: init, initAll: initAll });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { initAll(document); }, { once: true });
    } else {
        initAll(document);
    }
})(window, document);
