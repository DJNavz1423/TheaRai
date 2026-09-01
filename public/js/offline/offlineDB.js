const OfflineDB = (() => {

    const DB_NAME = 'thearai_offline';
    const DB_VERSION = 2;

    const STORES = {
        users: 'users',
        branches: 'branches',
        menuItems: 'menu_items',
        orders: 'offline_orders'
    };

    function open() {

        return new Promise((resolve, reject) => {

            const request =
                indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = function (event) {

                const db = event.target.result;

                if (!db.objectStoreNames.contains(STORES.users)) {
                    db.createObjectStore(
                        STORES.users,
                        { keyPath: 'email' }
                    );
                }

                if (!db.objectStoreNames.contains(STORES.branches)) {
                    db.createObjectStore(
                        STORES.branches,
                        { keyPath: 'id' }
                    );
                }

                if (!db.objectStoreNames.contains(STORES.menuItems)) {
                    db.createObjectStore(
                        STORES.menuItems,
                        { keyPath: 'id' }
                    );
                }

                if (!db.objectStoreNames.contains(STORES.orders)) {

                    const store =
                        db.createObjectStore(
                            STORES.orders,
                            { keyPath: 'local_id' }
                        );

                    store.createIndex(
                        'sync_status',
                        'sync_status',
                        { unique: false }
                    );
                }
            };

            request.onsuccess = () => {
                resolve(request.result);
            };

            request.onerror = () => {
                reject(request.error);
            };

            request.onblocked = () => {
                console.warn(
                    'IndexedDB upgrade is blocked. Close other TheaRai tabs.'
                );
            };
        });
    }

    async function put(storeName, data) {

        const db = await open();

        return new Promise((resolve, reject) => {

            const tx =
                db.transaction(
                    storeName,
                    'readwrite'
                );

            tx.objectStore(storeName).put(data);

            tx.oncomplete = () => resolve(true);
            tx.onerror = () => reject(tx.error);
        });
    }

    async function get(storeName, key) {

        const db = await open();

        return new Promise((resolve, reject) => {

            const tx =
                db.transaction(
                    storeName,
                    'readonly'
                );

            const request =
                tx.objectStore(storeName).get(key);

            request.onsuccess =
                () => resolve(request.result || null);

            request.onerror =
                () => reject(request.error);
        });
    }

    async function getAll(storeName) {

        const db = await open();

        return new Promise((resolve, reject) => {

            const tx =
                db.transaction(
                    storeName,
                    'readonly'
                );

            const request =
                tx.objectStore(storeName).getAll();

            request.onsuccess =
                () => resolve(request.result || []);

            request.onerror =
                () => reject(request.error);
        });
    }

    async function remove(storeName, key) {

        const db = await open();

        return new Promise((resolve, reject) => {

            const tx =
                db.transaction(
                    storeName,
                    'readwrite'
                );

            tx.objectStore(storeName).delete(key);

            tx.oncomplete =
                () => resolve(true);

            tx.onerror =
                () => reject(tx.error);
        });
    }

    return {
        open,
        put,
        get,
        getAll,
        remove,
        STORES
    };

})();