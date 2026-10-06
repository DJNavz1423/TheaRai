document.addEventListener('DOMContentLoaded', function () {
    const filterControls = document.querySelector('.stock-audit-filter-controls');
    const filterContainer = document.querySelector('.stock-audit-filters');
    const searchInput = document.getElementById('search');

    if (!filterControls || !filterContainer || !searchInput) {
        return;
    }

    document.querySelectorAll('.stock-audit-filter-controls .ts-filter').forEach(function (select) {
        new TomSelect(select, {
            create: false,
            controlInput: null,
            maxOptions: 50,
            maxItems: 1,
            dropdownParent: 'body',
            closeAfterSelect: true,
            placeholder: select.id === 'branch_id' ? 'All Branches' : undefined,
        });
    });

    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');

    function applyDynamicFilters() {
        const url = new URL(window.location.href);
        const params = url.searchParams;

        params.delete('page');
        params.set('source_type', document.getElementById('source_type').value);

        const branchId = document.getElementById('branch_id').value;
        if (branchId && branchId !== 'all') {
            params.set('branch_id', branchId);
        } else {
            params.delete('branch_id');
        }

        const search = searchInput.value.trim();
        if (search) {
            params.set('search', search);
        } else {
            params.delete('search');
        }

        window.location.assign(url.toString());
    }

    document.querySelectorAll('.stock-audit-date-filter-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (dateFrom.value > dateTo.value) {
                event.preventDefault();
                dateTo.setCustomValidity('"To date" must be the same as or later than "From date".');
                dateTo.reportValidity();
                dateTo.setCustomValidity('');
                return;
            }

            const branchId = document.getElementById('branch_id').value;
            form.elements.namedItem('branch_id').value = branchId === 'all' ? '' : branchId;
            form.elements.namedItem('source_type').value = document.getElementById('source_type').value;
            form.elements.namedItem('search').value = searchInput.value.trim();
        });
    });

    document.querySelectorAll('.stock-audit-filter-controls .ts-filter').forEach(function (select) {
        select.addEventListener('change', applyDynamicFilters);
    });

    function applyAuditSearch() {
        const query = searchInput.value.trim().toLowerCase();
        const rows = Array.from(document.querySelectorAll('.stock-audit-row'));
        const emptyRow = document.querySelector('.stock-audit-search-empty');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const matchesSearch = row.textContent.toLowerCase().includes(query);
            row.style.display = matchesSearch ? '' : 'none';

            if (matchesSearch) {
                visibleCount += 1;
            }
        });

        if (emptyRow) {
            emptyRow.style.display = query && rows.length > 0 && visibleCount === 0 ? '' : 'none';
        }
    }

    searchInput.addEventListener('input', applyAuditSearch);

    applyAuditSearch();
});
