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
            ×
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
            ×
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

        return items.map(function (item) {

            const stock = Number(item.stock_quantity);
            const unit = item.unit || '';

            return `
                <div>
                    ${item.name}
                    <strong>
                        (${stock} ${unit})
                    </strong>
                    <small>
                        — ${item.branch_name}
                    </small>
                </div>
            `;

        }).join('');
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