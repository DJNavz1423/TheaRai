document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.ts-filter').forEach(select => {
        new TomSelect(select, {
            create: false,
            controlInput: null,
            maxOptions: 50,
            maxItems: 1,
            dropdownParent: 'body',
            closeAfterSelect: true,
        });
    });


    function applyBranchFilters() {

        const searchQuery =
            document.getElementById('branchSearch')
                .value
                .trim()
                .toLowerCase();


        const sortType =
            document.getElementById('sort-items').value;


        const rows =
            Array.from(
                document.querySelectorAll('.branch-row')
            );


        rows.forEach(row => {

            const name =
                row.getAttribute('data-name') || '';

            const address =
                row.getAttribute('data-address') || '';

            const inventory =
                row.getAttribute('data-inventory') || '';

            const inventoryRaw =
                row.getAttribute('data-inventory-raw') || '';

            const date =
                row.getAttribute('data-date') || '';

            const dateFull =
                row.getAttribute('data-date-full') || '';

            const dateIso =
                row.getAttribute('data-date-iso') || '';


            const matchesSearch =
                name.includes(searchQuery) ||
                address.includes(searchQuery) ||
                inventory.includes(searchQuery) ||
                inventoryRaw.includes(searchQuery) ||
                date.includes(searchQuery) ||
                dateFull.includes(searchQuery) ||
                dateIso.includes(searchQuery);


            if (matchesSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }

        });


        const visibleRows =
            rows.filter(
                row => row.style.display !== 'none'
            );


        visibleRows.sort((a, b) => {

            const createdA =
                parseInt(
                    a.getAttribute('data-created')
                ) || 0;

            const createdB =
                parseInt(
                    b.getAttribute('data-created')
                ) || 0;


            switch (sortType) {

                case 'latest':
                    return createdB - createdA;

                case 'oldest':
                    return createdA - createdB;

                default:
                    return 0;
            }

        });


        const tbody =
            document.querySelector(
                'tbody[role="rowgroup"]'
            );


        visibleRows.forEach(row => {
            tbody.appendChild(row);
        });

    }


    const searchBar =
        document.getElementById('branchSearch');


    if (searchBar) {
        searchBar.addEventListener(
            'input',
            applyBranchFilters
        );
    }


    document
        .querySelectorAll('.ts-filter')
        .forEach(select => {

            select.addEventListener(
                'change',
                applyBranchFilters
            );

        });


    applyBranchFilters();

});