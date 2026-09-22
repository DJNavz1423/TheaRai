document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | POS Filter Function
    |--------------------------------------------------------------------------
    */

    function applyPosFilters() {

        const searchQuery =
            document.getElementById('menuSearch')
                .value
                .trim()
                .toLowerCase();

        const categoryVal =
            document.getElementById('category').value;

        const statusVal =
            document.getElementById('filter-status').value;


        const filteredItems =
            window.menuItems.filter(item => {

                /*
                |--------------------------------------------------------------------------
                | Search
                |--------------------------------------------------------------------------
                */

                const itemName =
                    String(item.name)
                        .toLowerCase();

                const matchesSearch =
                    itemName.includes(searchQuery);


                /*
                |--------------------------------------------------------------------------
                | Category
                |--------------------------------------------------------------------------
                */

                const matchesCategory =
                    categoryVal === 'all' ||
                    String(item.category_id) === String(categoryVal);


                /*
                |--------------------------------------------------------------------------
                | Availability
                |--------------------------------------------------------------------------
                */

                const isAvailable = item.is_available === true || item.is_available == 1;

                const disabledReason = item.disabled_reason || 'disabled';

                let matchesStatus = true;

                if (statusVal === 'enabled') {
                    matchesStatus = isAvailable;

                } else if (statusVal === 'sold_out') {
                    matchesStatus = !isAvailable && disabledReason === 'sold_out';

                } else if (statusVal === 'out_of_stock') {
                    matchesStatus = !isAvailable && disabledReason === 'out_of_stock';

                } else if (statusVal === 'disabled') {
                    /*
                    * "Disabled" means every disabled type.
                    */
                    matchesStatus = !isAvailable;
                }


                return (
                    matchesSearch &&
                    matchesCategory &&
                    matchesStatus
                );

            });


        renderMenu(filteredItems);
    }


    /*
    |--------------------------------------------------------------------------
    | Make applyPosFilters available to the POS inline script
    |--------------------------------------------------------------------------
    */

    window.applyPosFilters = applyPosFilters;


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    const searchBar =
        document.getElementById('menuSearch');

    if (searchBar) {

        searchBar.addEventListener(
            'input',
            applyPosFilters
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Category + Status Filters
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.ts-filter')
        .forEach(select => {

            select.addEventListener(
                'change',
                applyPosFilters
            );

        });


    /*
    |--------------------------------------------------------------------------
    | Initial Display
    |--------------------------------------------------------------------------
    */

    applyPosFilters();

});