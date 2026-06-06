document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.ts-filter').forEach(select => {
        new TomSelect(select,{
            create:false,
            controlInput:null,
            maxOptions:50,
            maxItems:1,
            dropdownParent:'body',
            closeAfterSelect:true,
        });
    });

    function applyArchiveFilters(){

        const searchQuery =
            document.getElementById('archiveSearch')
            .value.toLowerCase();

        const typeVal =
            document.getElementById('filter-type')
            .value;

        const sortType =
            document.getElementById('sort-items')
            .value;

        let rows =
            Array.from(document.querySelectorAll('.archive-row'));

        const tbody =
            document.querySelector('tbody[role="rowgroup"]');

        rows.forEach(row => {

            const name =
                row.getAttribute('data-name');

            const type =
                row.getAttribute('data-type');

            const matchesSearch =
                name.includes(searchQuery);

            const matchesType =
                typeVal === 'all' ||
                type === typeVal;

            row.style.display =
                matchesSearch && matchesType
                    ? ''
                    : 'none';
        });

        let visibleRows =
            rows.filter(row => row.style.display !== 'none');

        visibleRows.sort((a,b)=>{

            const createdA =
                parseInt(a.getAttribute('data-created'));

            const createdB =
                parseInt(b.getAttribute('data-created'));

            const nameA =
                a.getAttribute('data-name');

            const nameB =
                b.getAttribute('data-name');

            switch(sortType){

                case 'latest':
                    return createdB - createdA;

                case 'qty_desc':
                    return createdA - createdB;

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

    document.getElementById('archiveSearch')
        ?.addEventListener('input', applyArchiveFilters);

    document.querySelectorAll('.ts-filter')
        .forEach(select =>
            select.addEventListener('change', applyArchiveFilters)
        );
});