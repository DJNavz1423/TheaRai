document.addEventListener('DOMContentLoaded', function(){

    // ==========================================
    // TOM SELECT
    // ==========================================

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


    // ==========================================
    // CATEGORY FILTERS
    // ==========================================

    function applyCategoryFilters(){

        const searchQuery =
            document.getElementById('categorySearch')
            .value
            .toLowerCase();

        const sortType =
            document.getElementById('sort-items')
            .value;

        // Only category rows
        let rows =
            Array.from(document.querySelectorAll('.category-row'));

        const tbody =
            document.querySelector('#categoryTable tbody');

        // FILTER
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

        // SORT
        let visibleRows =
            rows.filter(row =>
                row.style.display !== 'none'
            );

        visibleRows.sort((a, b) => {

            const createdA =
                parseInt(a.getAttribute('data-created')) || 0;

            const createdB =
                parseInt(b.getAttribute('data-created')) || 0;

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

        visibleRows.forEach(row =>
            tbody.appendChild(row)
        );
    }


    // ==========================================
    // UNIT FILTERS
    // ==========================================

    function applyUnitFilters(){

        const searchQuery =
            document.getElementById('unitSearch')
            .value
            .toLowerCase();

        const sortType =
            document.getElementById('unit-sort-items')
            .value;

        // Only unit rows
        let rows =
            Array.from(document.querySelectorAll('.unit-row'));

        const tbody =
            document.querySelector('#unitTable tbody');

        // FILTER
        rows.forEach(row => {

            const name =
                row.getAttribute('data-name');

            const abbreviation =
                row.getAttribute('data-abbreviation');

            const matchesSearch =
                name.includes(searchQuery) ||
                abbreviation.includes(searchQuery);

            row.style.display =
                matchesSearch
                    ? ''
                    : 'none';
        });

        // SORT
        let visibleRows =
            rows.filter(row =>
                row.style.display !== 'none'
            );

        visibleRows.sort((a, b) => {

            const createdA =
                parseInt(a.getAttribute('data-created')) || 0;

            const createdB =
                parseInt(b.getAttribute('data-created')) || 0;

            const nameA =
                a.getAttribute('data-name');

            const nameB =
                b.getAttribute('data-name');

            switch(sortType){

                case 'unit-latest':
                    return createdB - createdA;

                case 'unit-name_asc':
                    return nameA.localeCompare(nameB);

                case 'unit-name_desc':
                    return nameB.localeCompare(nameA);

                default:
                    return 0;
            }
        });

        visibleRows.forEach(row =>
            tbody.appendChild(row)
        );
    }


    // ==========================================
    // EVENT LISTENERS
    // ==========================================

    const categorySearch =
        document.getElementById('categorySearch');

    if(categorySearch){
        categorySearch.addEventListener(
            'input',
            applyCategoryFilters
        );
    }


    const unitSearch =
        document.getElementById('unitSearch');

    if(unitSearch){
        unitSearch.addEventListener(
            'input',
            applyUnitFilters
        );
    }


    const categorySort =
        document.getElementById('sort-items');

    if(categorySort){
        categorySort.addEventListener(
            'change',
            applyCategoryFilters
        );
    }


    const unitSort =
        document.getElementById('unit-sort-items');

    if(unitSort){
        unitSort.addEventListener(
            'change',
            applyUnitFilters
        );
    }


    // Initial load
    applyCategoryFilters();
    applyUnitFilters();

});