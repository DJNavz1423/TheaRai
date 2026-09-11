const CACHE_NAME = 'thearai-v21';


/*
|--------------------------------------------------------------------------
| FILES REQUIRED BY THE OFFLINE APPLICATION
|--------------------------------------------------------------------------
*/

const STATIC_FILES = [

    /*
    |--------------------------------------------------------------------------
    | HTML
    |--------------------------------------------------------------------------
    */

    '/login',
    '/offline-select-branch',
    '/offline-pos',


    /*
    |--------------------------------------------------------------------------
    | GLOBAL CSS
    |--------------------------------------------------------------------------
    */

    '/css/style.css',
    '/css/loader.css',


    /*
    |--------------------------------------------------------------------------
    | LOGIN CSS
    |--------------------------------------------------------------------------
    */

    '/css/login/login-style.css',


    /*
    |--------------------------------------------------------------------------
    | LOGIN JS
    |--------------------------------------------------------------------------
    */

    '/js/login/visibility-toggle.js',
    '/js/login/remember-user.js',


    /*
    |--------------------------------------------------------------------------
    | OFFLINE DATABASE
    |--------------------------------------------------------------------------
    */

    '/js/offline/DB/offlineDB.js',


    /*
    |--------------------------------------------------------------------------
    | OFFLINE AUTH
    |--------------------------------------------------------------------------
    */

    '/js/offline/auth/offlineAuth.js',
    '/js/offline/auth/password.js',
    '/js/offline/auth/registerOfflineUser.js',
    '/js/offline/pos/syncOfflineOrders.js',

    /*
    |--------------------------------------------------------------------------
    | ADMIN/CASHIER LAYOUT
    |--------------------------------------------------------------------------
    */

    '/css/admin/sidebar.css',
    '/css/cashier/sidebar.css',
    '/css/cashier/header.css',
    '/css/pos/qrNotifBadge.css',

    '/js/dashboard/sidebarToggles.js',
    '/js/script.js',
    '/js/utils/liveClock.js',


    /*
    |--------------------------------------------------------------------------
    | OFFLINE POS CSS
    |--------------------------------------------------------------------------
    */

    '/css/admin/pos/selectBranch.css',
    '/css/admin/sectionHeading.css',
    '/css/pos/pos.css',
    '/css/pos/receiptPreview.css',
    '/css/admin/tableControls.css',
    '/css/admin/filters.css',


    /*
    |--------------------------------------------------------------------------
    | OFFLINE POS JS
    |--------------------------------------------------------------------------
    */

    '/js/offline/pos/offlineBranchSelect.js',
    '/js/offline/pos/offlinePOS.js',
    '/js/offline/pos/cachePOSData.js'
];


/*
|--------------------------------------------------------------------------
| OFFLINE HTML PAGES
|--------------------------------------------------------------------------
*/

const OFFLINE_PAGES = [
    '/offline-select-branch',
    '/offline-pos'
];


/*
|--------------------------------------------------------------------------
| INSTALL
|--------------------------------------------------------------------------
*/

self.addEventListener('install', event => {

    event.waitUntil(

        caches.open(CACHE_NAME)
            .then(async cache => {

                for (const file of STATIC_FILES) {

                    try {

                        const response =
                            await fetch(file);

                        if (!response.ok) {

                            console.warn(
                                'FAILED TO CACHE:',
                                file,
                                response.status
                            );

                            continue;
                        }


                        await cache.put(
                            file,
                            response
                        );


                        console.log(
                            'CACHED:',
                            file
                        );

                    } catch (error) {

                        console.error(
                            'CACHE ERROR:',
                            file,
                            error
                        );
                    }
                }
            })
    );


    self.skipWaiting();
});


/*
|--------------------------------------------------------------------------
| ACTIVATE
|--------------------------------------------------------------------------
*/

self.addEventListener('activate', event => {

    event.waitUntil(

        caches.keys()
            .then(keys => {

                return Promise.all(

                    keys
                        .filter(
                            key =>
                                key !== CACHE_NAME
                        )
                        .map(
                            key =>
                                caches.delete(key)
                        )
                );

            })
            .then(() => {

                return self.clients.claim();

            })
    );
});


/*
|--------------------------------------------------------------------------
| FETCH
|--------------------------------------------------------------------------
*/

self.addEventListener('fetch', event => {

    const request =
        event.request;


    /*
    |--------------------------------------------------------------------------
    | Only handle GET
    |--------------------------------------------------------------------------
    */

    if (
        request.method !== 'GET'
    ) {
        return;
    }


    const url =
        new URL(request.url);


    /*
    |--------------------------------------------------------------------------
    | Don't intercept external resources
    |--------------------------------------------------------------------------
    */

    if (
        url.origin !== self.location.origin
    ) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    if (
        request.mode === 'navigate' &&
        url.pathname === '/login'
    ) {

        event.respondWith(

            (async () => {

                /*
                * Network first while server is available.
                */

                try {

                    const response =
                        await fetch(request);


                    if (response.ok) {

                        const cache =
                            await caches.open(
                                CACHE_NAME
                            );


                        await cache.put(
                            '/login',
                            response.clone()
                        );


                        return response;
                    }

                } catch (error) {

                    console.log(
                        'Login network unavailable.'
                    );

                }


                /*
                * Server unavailable.
                */

                const cached =
                    await caches.match(
                        '/login'
                    );


                if (cached) {
                    return cached;
                }


                return new Response(
                    'Offline login page is not available.',
                    {
                        status: 503,
                        headers: {
                            'Content-Type':
                                'text/plain'
                        }
                    }
                );

            })()
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | OFFLINE POS PAGES
    |--------------------------------------------------------------------------
    */

    if (
        request.mode === 'navigate' &&
        OFFLINE_PAGES.includes(
            url.pathname
        )
    ) {

        event.respondWith(

            (async () => {

                try {

                    const response =
                        await fetch(request);


                    if (response.ok) {

                        const cache =
                            await caches.open(
                                CACHE_NAME
                            );


                        await cache.put(
                            request,
                            response.clone()
                        );


                        return response;
                    }

                } catch (error) {

                    console.log(
                        'Offline page unavailable.'
                    );

                }

                const cached =
                    await caches.match(request) ||
                    await caches.match(url.pathname);

                if (cached) {
                    return cached;
                }


                return new Response(
                    'This offline page has not been cached yet.',
                    {
                        status: 503,
                        headers: {
                            'Content-Type':
                                'text/plain'
                        }
                    }
                );

            })()
        );

        return;
    }


    // Never serve cached Laravel navigations: stale online layouts caused the
    // online and offline sessions to interfere with each other.
    if (request.mode === 'navigate') {
        event.respondWith(fetch(request));
        return;
    }

    // Cache only static assets. API responses and other dynamic GET requests
    // must always use the network.
    const isStaticAsset = ['style', 'script', 'image', 'font'].includes(
        request.destination
    );

    if (!isStaticAsset) {
        return;
    }

    event.respondWith(
        fetch(request).then(response => {
            if (response.ok && response.type === 'basic') {
                const clone = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
            }

            return response;
        }).catch(async () => {
            const cached = await caches.match(request);

            return cached || new Response('', {
                status: 503,
                statusText: 'Offline asset unavailable'
            });
        })
    );

});