document.addEventListener('DOMContentLoaded', function(){

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

    function applyTableFilters(){

        const searchQuery =
            document.getElementById('tableSearch')
            .value.toLowerCase();

        const branchVal =
            document.getElementById('filter-branch')
            .value;

        const sortType =
            document.getElementById('sort-items')
            .value;

        let rows =
            Array.from(document.querySelectorAll('.table-row'));

        const tbody =
            document.querySelector('tbody[role="rowgroup"]');

        rows.forEach(row => {

            const tableNum =
                row.getAttribute('data-number');

            const branch =
                row.getAttribute('data-branch');

            const matchesSearch =
                tableNum.includes(searchQuery);

            const matchesBranch =
                branchVal === 'all' ||
                branch === branchVal;

            row.style.display =
                matchesSearch && matchesBranch
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

            switch(sortType){

                case 'latest':
                    return createdB - createdA;

                case 'qty_desc':
                    return createdA - createdB;

                default:
                    return 0;
            }
        });

        visibleRows.forEach(row =>
            tbody.appendChild(row)
        );
    }

    document.getElementById('tableSearch')
        ?.addEventListener('input', applyTableFilters);

    document.querySelectorAll('.ts-filter')
        .forEach(select =>
            select.addEventListener('change', applyTableFilters)
        );
});