(function () {
    let syncing = false;

    async function syncOrders() {
        if (syncing || !navigator.onLine) {
            return;
        }

        const offlineAuth = JSON.parse(sessionStorage.getItem('offline_auth') || 'null');
        const hasOfflineAuth =
            offlineAuth &&
            offlineAuth.authenticated === true &&
            offlineAuth.offline === true;

        if (!hasOfflineAuth) {
            return;
        }

        const orders = (await OfflineDB.getAll(OfflineDB.STORES.orders))
            .filter(order => order.sync_status === 'pending');

        if (!orders.length) {
            return;
        }

        syncing = true;

        try {
            const loginPage = await fetch('/login', { cache: 'no-store' });
            const loginHtml = await loginPage.text();
            const csrfToken = new DOMParser()
                .parseFromString(loginHtml, 'text/html')
                .querySelector('meta[name="csrf-token"]')?.content || '';

            const response = await fetch('/offline/orders/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ orders })
            });

            if (response.status === 401 || response.status === 419) {
                sessionStorage.setItem('offline_sync_pending', 'true');
                window.location.replace('/login?sync=1');
                return;
            }

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            for (const result of data.results || []) {
                if (result.success) {
                    await OfflineDB.remove(OfflineDB.STORES.orders, result.local_id);
                }
            }

            const remaining = (await OfflineDB.getAll(OfflineDB.STORES.orders))
                .some(order => order.sync_status === 'pending');

            if (!remaining) {
                sessionStorage.removeItem('offline_sync_pending');
                goOnline(offlineAuth.role);
            }
        } catch (error) {
            console.warn('Offline orders will retry when the server is available.', error);
        } finally {
            syncing = false;
        }
    }

    function goOnline(role) {
        sessionStorage.removeItem('offline_auth');
        localStorage.removeItem('offline_active_branch_id');

        const destination = ['admin', 'dev', 'owner'].includes(role)
            ? '/admin/pos/select-branch'
            : '/cashier/pos';

        window.location.replace(destination);
    }

    window.addEventListener('online', syncOrders);
    document.addEventListener('DOMContentLoaded', syncOrders);
})();