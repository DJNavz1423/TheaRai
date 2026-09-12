(function () {
    let syncing = false;
    let serverWasUnavailable = true;
    const statusElement = document.getElementById('offlineSyncStatus');

    function setStatus(message, visible = true) {
        if (!statusElement) {
            return;
        }

        statusElement.textContent = message;
        statusElement.hidden = !visible;
    }

    async function isServerAvailable() {
        try {
            const response = await fetch('/up', {
                cache: 'no-store',
                headers: { 'Accept': 'application/json' }
            });

            return response.ok;
        } catch (error) {
            return false;
        }
    }

    async function syncOrders() {
        if (syncing || !navigator.onLine) {
            return;
        }

        if (!(await isServerAvailable())) {
            serverWasUnavailable = true;
            setStatus('', false);
            return;
        }

        if (serverWasUnavailable) {
            setStatus('Connection restored. Synchronizing offline orders...');
            serverWasUnavailable = false;
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
            try {
                const sessionResponse = await fetch('/offline/session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        sync_token: offlineAuth.sync_token
                    })
                });

                if (!sessionResponse.ok) {
                    setStatus('Online session could not be restored.');
                    return;
                }

                setStatus('Connection restored. Returning online...', true);
                goOnline(offlineAuth.role);
            } catch (error) {
                setStatus('Online session could not be restored. Retrying...');
            }

            return;
        }

        syncing = true;

        try {
            const syncOrders = orders.map(order => ({
                ...order,
                sync_token: order.sync_token || offlineAuth.sync_token
            }));

            const response = await fetch('/offline/orders/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ orders: syncOrders })
            });

            if (response.status === 401 || response.status === 419) {
                setStatus('Online server is ready, but offline sync credentials are missing.');
                return;
            }

            if (!response.ok) {
                setStatus(`Order sync failed (${response.status}). Retrying...`);
                return;
            }

            const data = await response.json();
            const failedResults = (data.results || [])
                .filter(result => !result.success);

            for (const result of data.results || []) {
                if (result.success) {
                    await OfflineDB.remove(OfflineDB.STORES.orders, result.local_id);
                }
            }

            if (failedResults.length) {
                const reason = failedResults
                    .map(result => result.error || 'Unknown server error')
                    .join('; ');

                setStatus(`Offline order sync failed: ${reason}`);
                console.error('Offline order sync rejected:', data);
                return;
            }

            const remaining = (await OfflineDB.getAll(OfflineDB.STORES.orders))
                .some(order => order.sync_status === 'pending');

            if (!remaining) {
                sessionStorage.removeItem('offline_sync_pending');
                setStatus('Orders synchronized. Returning online...', true);
                goOnline(offlineAuth.role);
            }
        } catch (error) {
            serverWasUnavailable = true;
            setStatus('Order sync could not reach the server. Retrying...');
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
    setInterval(syncOrders, 5000);
})();