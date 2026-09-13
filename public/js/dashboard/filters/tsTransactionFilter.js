document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Initialize TomSelect
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Apply Filters
    |--------------------------------------------------------------------------
    */

    function applyTransactionFilters() {

        const searchQuery = document.getElementById('salesSearch').value.trim().toLowerCase();

        const branchFilter = document.getElementById('filter-branch');

        const branchVal = branchFilter ? branchFilter.value : 'all';

        const paymentVal =
            document.getElementById('filter-payment')
                .value;


        const paymentStatusVal =
            document.getElementById('filter-payment-status')
                .value;


        const sortType =
            document.getElementById('sort-items')
                .value;


        const rows =
            Array.from(
                document.querySelectorAll('.transaction-row')
            );


        /*
        |--------------------------------------------------------------------------
        | Filtering
        |--------------------------------------------------------------------------
        */

        rows.forEach(row => {

            const receipt =
            row.getAttribute('data-receipt') || '';

            const branchId =
                row.getAttribute('data-branch-id') || '';

            const branch =
                row.getAttribute('data-branch') || '';

            const amount =
                row.getAttribute('data-amount') || '';

            const payment =
                row.getAttribute('data-payment') || '';

            const paymentStatus =
                row.getAttribute('data-payment-status') || '';

            const date =
                row.getAttribute('data-date') || '';


            const matchesSearch =
            receipt.includes(searchQuery) ||
            branch.includes(searchQuery) ||
            amount.includes(searchQuery) ||
            payment.includes(searchQuery) ||
            paymentStatus.includes(searchQuery) ||
            date.includes(searchQuery);


            const matchesBranch =
                branchVal === 'all' ||
                branchId === branchVal;


            const matchesPayment =
                paymentVal === 'all' ||
                payment === paymentVal;


            const matchesPaymentStatus =
                paymentStatusVal === 'all' ||
                paymentStatus === paymentStatusVal;


            if (
                matchesSearch &&
                matchesBranch &&
                matchesPayment &&
                matchesPaymentStatus
            ) {

                row.style.display = '';

            } else {

                row.style.display = 'none';

            }

        });


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

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


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    const searchBar =
        document.getElementById('salesSearch');


    if (searchBar) {

        searchBar.addEventListener(
            'input',
            applyTransactionFilters
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Filter Changes
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.ts-filter')
        .forEach(select => {

            select.addEventListener(
                'change',
                applyTransactionFilters
            );

        });


    /*
    |--------------------------------------------------------------------------
    | Initial Filter
    |--------------------------------------------------------------------------
    */

    applyTransactionFilters();

});