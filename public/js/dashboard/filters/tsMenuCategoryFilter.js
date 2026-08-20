document.addEventListener('DOMContentLoaded', function(){

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

    function applyMenuCategoryFilters(){

        const searchQuery =
            document.getElementById('menuCategorySearch')
            .value
            .toLowerCase();

        const sortType =
            document.getElementById('sort-menuCategories')
            .value;

        const rows =
            Array.from(
                document.querySelectorAll('.menu-category-row')
            );

        const tbody =
            document.querySelector(
                'tbody[role="rowgroup"]'
            );

        rows.forEach(row => {

            const name =
                row.getAttribute('data-name');

            const matchesSearch =
                name.includes(searchQuery);

            row.style.display =
                matchesSearch
                    ? ''
                    : 'none';
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

            const nameA =
                a.getAttribute('data-name');

            const nameB =
                b.getAttribute('data-name');

            switch(sortType){

                case 'latest':
                    return createdB - createdA;

                case 'name_asc':
                    return nameA.localeCompare(nameB);

                case 'name_desc':
                    return nameB.localeCompare(nameA);

                default:
                    return 0;
            }
        });

        visibleRows.forEach(row => {
            tbody.appendChild(row);
        });
    }

    document.getElementById('menuCategorySearch')
        ?.addEventListener(
            'input',
            applyMenuCategoryFilters
        );

    document.getElementById('sort-menuCategories')
        ?.addEventListener(
            'change',
            applyMenuCategoryFilters
        );

    applyMenuCategoryFilters();
});