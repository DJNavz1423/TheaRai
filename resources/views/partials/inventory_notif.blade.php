<div id="inventoryOutOfStockAlert"
    class="inventory-alert inventory-alert-danger"
    role="alert"
    aria-live="polite"
    hidden>

    <div class="inventory-alert-content">

        <div class="inventory-alert-icon">
            <span>!</span>
        </div>

        <div class="inventory-alert-text">
            <strong>Out of Stock</strong>
            <div id="inventoryOutOfStockMessage"></div>
        </div>

        <button type="button"
            class="inventory-alert-close"
            data-alert="out">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
        </button>

    </div>
</div>


<div id="inventoryLowStockAlert"
    class="inventory-alert inventory-alert-warning"
    role="alert"
    aria-live="polite"
    hidden>

    <div class="inventory-alert-content">

        <div class="inventory-alert-icon">
            <span>!</span>
        </div>

        <div class="inventory-alert-text">
            <strong>Low Stock</strong>
            <div id="inventoryLowStockMessage"></div>
        </div>

        <button type="button"
            class="inventory-alert-close"
            data-alert="low">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
        </button>

    </div>
</div>


<script>
(function () {

    const outAlert =
        document.getElementById('inventoryOutOfStockAlert');

    const lowAlert =
        document.getElementById('inventoryLowStockAlert');

    const outMessage =
        document.getElementById('inventoryOutOfStockMessage');

    const lowMessage =
        document.getElementById('inventoryLowStockMessage');


    let outTimer = null;
    let lowTimer = null;


    function showAlert(alertBox, messageElement, message, timerType) {

        messageElement.innerHTML = message;

        alertBox.hidden = false;

        requestAnimationFrame(function () {
            alertBox.classList.add('show');
        });


        if (timerType === 'out') {

            clearTimeout(outTimer);

            outTimer = setTimeout(function () {
                hideAlert(alertBox);
            }, 600000);

        } else {

            clearTimeout(lowTimer);

            lowTimer = setTimeout(function () {
                hideAlert(alertBox);
            }, 600000);
        }
    }


    function hideAlert(alertBox) {

        alertBox.classList.remove('show');

        setTimeout(function () {

            if (!alertBox.classList.contains('show')) {
                alertBox.hidden = true;
            }

        }, 350);
    }


    function buildMessage(items) {

        const visibleItems = items.slice(0, 6);
        const remainingItems = items.length - visibleItems.length;

        const itemsHtml = visibleItems.map(function (item) {

            const stock = Number(item.stock_quantity);
            const unit = item.unit || '';

            return `
                <div class="inventory-item">
                    <div class="inventory-item-name">
                        ${item.name}
                    </div>

                    <div class="inventory-item-stock">
                        ${stock} ${unit}
                    </div>

                    <div class="inventory-item-branch">
                        ${item.branch_name}
                    </div>
                </div>
            `;

        }).join('');

        return `
            <div class="inventory-items-grid">
                ${itemsHtml}
            </div>

            ${
                remainingItems > 0
                    ? `<div class="inventory-more">
                            and ${remainingItems} more item${remainingItems === 1 ? '' : 's'}
                    </div>`
                    : ''
            }
        `;
    }


    function checkInventoryNotifications(data) {

        const outOfStock =
            data.out_of_stock || [];

        const lowStock =
            data.low_stock || [];

        if (
            outOfStock.length === 0 &&
            lowStock.length === 0
        ) {
            return;
        }


        /*
        * Use the current Laravel session ID.
        * A new login gets a new session ID,
        * so the warning appears again on the next login.
        */
        const sessionId =
            @json(session()->getId());

        const storageKey =
            `inventory_notif_${sessionId}_${data.inventory_scope}`;


        /*
        * Only show the inventory warning once
        * during the current login session.
        */

        let alreadyShown = false;

        try {
            alreadyShown =
                sessionStorage.getItem(storageKey) === 'shown';
        } catch (error) {
            alreadyShown = false;
        }


        if (alreadyShown) {
            return;
        }


        try {
            sessionStorage.setItem(
                storageKey,
                'shown'
            );
        } catch (error) {
            console.warn(
                'Could not save inventory notification state.',
                error
            );
        }


        if (outOfStock.length > 0) {

            showAlert(
                outAlert,
                outMessage,
                buildMessage(outOfStock),
                'out'
            );
        }


        if (lowStock.length > 0) {

            showAlert(
                lowAlert,
                lowMessage,
                buildMessage(lowStock),
                'low'
            );
        }
    }


    document.querySelectorAll(
        '.inventory-alert-close'
    ).forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                if (button.dataset.alert === 'out') {
                    hideAlert(outAlert);
                }

                if (button.dataset.alert === 'low') {
                    hideAlert(lowAlert);
                }

            }
        );

    });

    document.addEventListener(
    'inventory-notifications-updated',
    function (event) {

        checkInventoryNotifications(
            event.detail
        );

    }
);
})();
</script>