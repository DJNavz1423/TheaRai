document.addEventListener(
    'DOMContentLoaded',
    async function () {

        if (!navigator.onLine) {
            return;
        }


        try {

            const response =
                await fetch(
                    '/offline/data',
                    {
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );


            if (!response.ok) {
                return;
            }


            const data =
                await response.json();


            if (!data.success) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Cache branches
            |--------------------------------------------------------------------------
            */

            for (const branch of data.branches) {
                await OfflineDB.put(OfflineDB.STORES.branches, branch);
            }
            /*
            |--------------------------------------------------------------------------
            | Cache menu items for every branch
            |--------------------------------------------------------------------------
            */

            for (const item of data.menu_items) {
                await OfflineDB.put(
                    OfflineDB.STORES.menuItems,
                    {
                        ...item,
                        offline_key: `${item.branch_id}:${item.id}`
                    }
                );
            }
            
            /*
            |--------------------------------------------------------------------------
            | Cache menu categories
            |--------------------------------------------------------------------------
            */

            for (const category of data.categories) {
                await OfflineDB.put(OfflineDB.STORES.categories, category);
            }


            console.log(
                'Offline POS data cached successfully.'
            );


        } catch (error) {

            console.error(
                'Offline POS data caching failed:',
                error
            );

        }

    }
);