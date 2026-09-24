<div id="qrOrderAlert" class="qr-order-alert" role="alert" aria-live="polite" hidden>
    <div class="qr-order-alert-content">
        <div class="qr-order-alert-icon">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m326-90-58-98-110-24q-15-3-24-15.5t-7-27.5l11-113-75-86q-10-11-10-26t10-26l75-86-11-113q-2-15 7-27.5t24-15.5l110-24 58-98q8-13 22-17.5t28 1.5l104 44 104-44q14-6 28-1.5t22 17.5l58 98 110 24q15 3 24 15.5t7 27.5l-11 113 75 86q10 11 10 26t-10 26l-75 86 11 113q2 15-7 27.5T802-212l-110 24-58 98q-8 13-22 17.5T584-74l-104-44-104 44q-14 6-28 1.5T326-90Zm52-72 102-44 104 44 56-96 110-26-10-112 74-84-74-86 10-112-110-24-58-96-102 44-104-44-56 96-110 24 10 112-74 86 74 84-10 114 110 24 58 96Zm102-318Zm28.5 188.5Q520-303 520-320t-11.5-28.5Q497-360 480-360t-28.5 11.5Q440-337 440-320t11.5 28.5Q463-280 480-280t28.5-11.5Zm0-160Q520-463 520-480v-160q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640v160q0 17 11.5 28.5T480-440q17 0 28.5-11.5Z"/></svg>
            </span>
        </div>

        <div class="qr-order-alert-text">
            <strong>New QR Order</strong>
            <span id="qrOrderAlertMessage">
                A new QR order has been received.
            </span>
        </div>

        <button type="button" id="qrOrderAlertView" class="btn">
            View Orders
        </button>

        <button type="button" id="qrOrderAlertClose" class="qr-order-alert-close" aria-label="Close notification">
            <span class="icon-wrapper close-modal">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
            </span>
        </button>
    </div>
</div>

@php
    $qrOrdersUrl = in_array(auth()->user()->role, ['admin', 'dev', 'owner'])
        ? url('/admin/qr-orders')
        : url('/cashier/qr-orders');
@endphp

<script>
(function () {

    const badges = document.querySelectorAll('.qr-notif-badge');

    const alertBox = document.getElementById('qrOrderAlert');

    const alertMessage = document.getElementById('qrOrderAlertMessage');

    const alertView = document.getElementById('qrOrderAlertView');

    const alertClose = document.getElementById('qrOrderAlertClose');

    const qrOrdersUrl = @json($qrOrdersUrl);
    
    let alertTimer = null;


    /*
    |--------------------------------------------------------------------------
    | Update notification badges
    |--------------------------------------------------------------------------
    */

    function updateBadges(count) {

        badges.forEach(function (badge) {

            if (count > 0) {
                badge.innerText = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Show QR order banner
    |--------------------------------------------------------------------------
    */

    function showQrOrderAlert(order) {

        if (!order) {
            return;
        }

        const tableText = order.table_number ? `Table ${order.table_number}` : 'Table';

        alertMessage.innerText = `${tableText} • Receipt ${order.receipt_no}`;

        clearTimeout(alertTimer);

        alertBox.hidden = false;

        requestAnimationFrame(function () {
            alertBox.classList.add('show');
        });

        alertTimer =
            setTimeout(
                hideQrOrderAlert,
                7000
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Hide QR order banner
    |--------------------------------------------------------------------------
    */

    function hideQrOrderAlert() {

        alertBox.classList.remove('show');

        setTimeout(function () {

            if (!alertBox.classList.contains('show')) {
                alertBox.hidden = true;
            }

        }, 350);
    }


    /*
    |--------------------------------------------------------------------------
    | Check whether this is a new QR order
    |--------------------------------------------------------------------------
    */

    function checkForNewQrOrder(data) {

        if (!data.branch_id || !data.latest_order
        ) {
            return;
        }

        const order = data.latest_order;

        const storageKey = `qr_notif_last_seen_${data.branch_id}`;

        let lastSeen = null;

        try {
            lastSeen = JSON.parse(localStorage.getItem(storageKey));
        } catch (error) {
            lastSeen = null;
        }


        const currentTime =
            new Date(order.created_at).getTime();


        const lastTime = lastSeen && lastSeen.created_at ? new Date(lastSeen.created_at).getTime() : 0;


        const isNewOrder = !lastSeen || currentTime > lastTime || (currentTime === lastTime && Number(order.id) > Number(lastSeen.id || 0));

        if (!isNewOrder) {
            return;
        }


        /*
        * Remember this order so navigating between
        * pages does not show the same alert again.
        */

        try {

            localStorage.setItem(
                storageKey,
                JSON.stringify({
                    id: order.id,
                    created_at: order.created_at
                })
            );

        } catch (error) {

            console.warn(
                'Could not save QR notification state.',
                error
            );

        }

        showQrOrderAlert(order);
    }


    /*
    |--------------------------------------------------------------------------
    | Fetch QR notifications
    |--------------------------------------------------------------------------
    */

    async function fetchQrNotifications() {

        try {

            const response =
                await fetch(
                    '{{ route("qr.orders.notifications") }}'
                );

            if (!response.ok) {
                return;
            }

            const data =
                await response.json();


            updateBadges(
                Number(data.count) || 0
            );


            checkForNewQrOrder(data);

        } catch (error) {

            console.error(
                'Error fetching QR notifications:',
                error
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Banner controls
    |--------------------------------------------------------------------------
    */

    alertView.addEventListener(
        'click',
        function () {

            window.location.href =
                qrOrdersUrl;

        }
    );


    alertClose.addEventListener(
        'click',
        hideQrOrderAlert
    );


    /*
    |--------------------------------------------------------------------------
    | Initial check + polling
    |--------------------------------------------------------------------------
    */

    fetchQrNotifications();
    setInterval(fetchQrNotifications, 7000);
})();
</script>