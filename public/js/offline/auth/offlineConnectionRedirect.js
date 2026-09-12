(function () {
    let checking = false;
    let serverAvailable = true;

    async function checkConnection() {
        if (checking) {
            return;
        }

        checking = true;

        try {
            const response = await fetch('/up', {
                cache: 'no-store',
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error(`Health check failed: ${response.status}`);
            }

            serverAvailable = true;
        } catch (error) {
            if (serverAvailable) {
                serverAvailable = false;
                await redirectToOffline();
            }
        } finally {
            checking = false;
        }
    }

    async function redirectToOffline() {
        const onlineUser = window.theaRaiOnlineUser;

        if (!onlineUser || !window.OfflineAuth) {
            return;
        }

        const offlineUser = await OfflineAuth.getUser(onlineUser.email);

        if (!offlineUser || !offlineUser.password_hash || !offlineUser.sync_token) {
            return;
        }

        const auth = {
            authenticated: true,
            offline: true,
            id: offlineUser.id,
            name: offlineUser.name,
            email: offlineUser.email,
            role: offlineUser.role,
            branch_id: offlineUser.branch_id,
            sync_token: offlineUser.sync_token
        };

        sessionStorage.setItem('offline_auth', JSON.stringify(auth));

        if (['admin', 'dev', 'owner'].includes(offlineUser.role)) {
            const branch = onlineUser.branch_id;
            window.location.replace(
                branch
                    ? `/offline-pos?branch=${encodeURIComponent(branch)}`
                    : '/offline-select-branch'
            );
            return;
        }

        window.location.replace('/offline-pos');
    }

    window.addEventListener('offline', redirectToOffline);
    window.addEventListener('online', checkConnection);
    setInterval(checkConnection, 5000);
    checkConnection();
})();