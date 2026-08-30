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


    function applyActivityFilters() {

        const searchQuery =
            document.getElementById('activitySearch')
                .value
                .toLowerCase();


        const actionVal =
            document.getElementById('filter-action')
                .value;


        const modelVal =
            document.getElementById('filter-model')
                .value;


        const userVal =
            document.getElementById('filter-user')
                .value;


        const sortType =
            document.getElementById('sort-items')
                .value;


        let rows =
            Array.from(
                document.querySelectorAll('.activity-row')
            );


        const tbody =
            document.querySelector(
                'tbody[role="rowgroup"]'
            );


        rows.forEach(row => {

            const action =
                row.getAttribute('data-action');

            const model =
                row.getAttribute('data-model');

            const user =
                row.getAttribute('data-user');

            const description =
                row.getAttribute('data-description');


            const matchesSearch =
                description.includes(searchQuery) ||
                model.includes(searchQuery) ||
                user.includes(searchQuery) ||
                action.includes(searchQuery);


            const matchesAction =
                actionVal === 'all' ||
                action === actionVal;


            const matchesModel =
                modelVal === 'all' ||
                model === modelVal;


            const matchesUser =
                userVal === 'all' ||
                user === userVal;


            if (
                matchesSearch &&
                matchesAction &&
                matchesModel &&
                matchesUser
            ) {

                row.style.display = '';

            } else {

                row.style.display = 'none';

            }

        });


        let visibleRows =
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


        visibleRows.forEach(row =>
            tbody.appendChild(row)
        );

    }


    const searchBar =
        document.getElementById(
            'activitySearch'
        );


    if (searchBar) {

        searchBar.addEventListener(
            'input',
            applyActivityFilters
        );

    }


    document
        .querySelectorAll('.ts-filter')
        .forEach(select => {

            select.addEventListener(
                'change',
                applyActivityFilters
            );

        });


    applyActivityFilters();

});