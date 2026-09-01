const CACHE_NAME = 'thearai-v6';

const STATIC_FILES = [
    '/login',

    '/css/style.css',
    '/css/loader.css',
    '/css/login/login-style.css',

    '/js/login/visibility-toggle.js',
    '/js/login/remember-user.js',

    '/js/offline/offlineAuth.js',
    '/js/offline/password.js',
    '/js/offline/registerOfflineUser.js',
    '/js/offline/offlineDB.js'
];


self.addEventListener('install', event => {

    event.waitUntil(

        caches.open(CACHE_NAME)
            .then(async cache => {

                for (const file of STATIC_FILES) {

                    try {

                        await cache.add(file);

                    } catch (error) {

                        console.warn(
                            'Failed to cache:',
                            file,
                            error
                        );
                    }
                }
            })
    );

    self.skipWaiting();
});


self.addEventListener('activate', event => {

    event.waitUntil(

        caches.keys().then(keys => {

            return Promise.all(

                keys
                    .filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            );

        }).then(() => {

            return self.clients.claim();
        })
    );
});


/*
|--------------------------------------------------------------------------
| FETCH HANDLER
|--------------------------------------------------------------------------
|
| Replace your OLD fetch handler with everything below.
|
*/

self.addEventListener('fetch', event => {

    const request = event.request;

    /*
    |--------------------------------------------------------------------------
    | Only handle GET requests
    |--------------------------------------------------------------------------
    |
    | POST/PUT/DELETE requests such as:
    |
    | /login
    | /heartbeat
    | /admin/inventory
    | /cashier/pos/order
    |
    | are allowed to go directly to the server.
    |
    */

    if (request.method !== 'GET') {
        return;
    }


    const url =
        new URL(request.url);


    /*
    |--------------------------------------------------------------------------
    | LOGIN PAGE
    |--------------------------------------------------------------------------
    */

    if (
        request.mode === 'navigate' &&
        url.pathname === '/login'
    ) {

        event.respondWith(

            (async () => {

                /*
                * Try network first.
                */

                try {

                    const response =
                        await fetch(request);


                    /*
                    * Only cache successful responses.
                    */

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
                        'Login network request failed.'
                    );
                }


                /*
                * Network failed.
                * Use cached login page.
                */

                const cached =
                    await caches.match(
                        '/login'
                    );


                if (cached) {
                    return cached;
                }


                /*
                * Nothing cached.
                */

                return new Response(

                    `
                    <!DOCTYPE html>

                    <html>

                    <head>
                        <meta charset="UTF-8">
                        <meta
                            name="viewport"
                            content="width=device-width, initial-scale=1.0"
                        >
                        <title>
                            TheaRai Eatery
                        </title>
                    </head>

                    <body>

                        <h2>
                            Offline login unavailable
                        </h2>

                        <p>
                            Please open the login page once
                            while connected to the internet.
                        </p>

                    </body>

                    </html>
                    `,

                    {
                        status: 503,

                        headers: {
                            'Content-Type':
                                'text/html; charset=UTF-8'
                        }
                    }
                );

            })()
        );


        return;
    }


    /*
    |--------------------------------------------------------------------------
    | ALL OTHER PAGE NAVIGATION
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | /admin/dashboard
    | /admin/inventory
    | /admin/menu
    | /admin/expenses
    | /cashier/pos
    |
    */

    if (request.mode === 'navigate') {

        event.respondWith(

            (async () => {

                /*
                |--------------------------------------------------------------------------
                | 1. Try network first
                |--------------------------------------------------------------------------
                */

                try {

                    const response =
                        await fetch(request);


                    if (response.ok) {

                        const cache =
                            await caches.open(
                                CACHE_NAME
                            );


                        /*
                        * Save successful page for
                        * future offline use.
                        */

                        await cache.put(
                            request,
                            response.clone()
                        );


                        return response;
                    }

                } catch (error) {

                    console.log(
                        'Navigation network unavailable.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | 2. Network failed -> use cached page
                |--------------------------------------------------------------------------
                */

                const cached =
                    await caches.match(
                        request
                    );


                if (cached) {
                    return cached;
                }


                /*
                |--------------------------------------------------------------------------
                | 3. Page wasn't cached
                |--------------------------------------------------------------------------
                |
                | As a final fallback show login.
                |
                */

                const login =
                    await caches.match(
                        '/login'
                    );


                if (login) {
                    return login;
                }


                /*
                |--------------------------------------------------------------------------
                | 4. Nothing available
                |--------------------------------------------------------------------------
                */

                return new Response(

                    `
                    <!DOCTYPE html>

                    <html>

                    <head>
                        <meta charset="UTF-8">

                        <title>
                            TheaRai Eatery
                        </title>
                    </head>

                    <body>

                        <h2>
                            This page is not available offline.
                        </h2>

                        <p>
                            Open this page once while online
                            so it can be cached.
                        </p>

                    </body>

                    </html>
                    `,

                    {
                        status: 503,

                        headers: {
                            'Content-Type':
                                'text/html; charset=UTF-8'
                        }
                    }
                );

            })()
        );


        return;
    }


    /*
    |--------------------------------------------------------------------------
    | OTHER GET REQUESTS
    |--------------------------------------------------------------------------
    |
    | CSS
    | JS
    | images
    | fonts
    | GET API requests
    |
    */

    event.respondWith(

        caches.match(request)

            .then(cachedResponse => {

                /*
                * Return cached resource immediately.
                */

                if (cachedResponse) {
                    return cachedResponse;
                }


                /*
                * Otherwise try the network.
                */

                return fetch(request)

                    .then(response => {

                        /*
                        * Cache successful responses.
                        */

                        if (response.ok) {

                            const cachePromise =
                                caches.open(
                                    CACHE_NAME
                                )
                                .then(cache => {

                                    return cache.put(
                                        request,
                                        response.clone()
                                    );

                                });


                            event.waitUntil(
                                cachePromise
                            );
                        }


                        return response;

                    })

                    .catch(() => {

                        /*
                        * Nothing available.
                        */

                        return new Response(
                            '',
                            {
                                status: 503
                            }
                        );

                    });

            })
    );

});