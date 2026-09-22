document.addEventListener(
    'DOMContentLoaded',
    async function () {

        /*
        |--------------------------------------------------------------------------
        | Offline authentication
        |--------------------------------------------------------------------------
        */

        let auth = null;

        try {

            auth =
                JSON.parse(
                    sessionStorage.getItem(
                        'offline_auth'
                    )
                );

        } catch (error) {

            auth = null;

        }


        if (
            !auth ||
            !auth.authenticated ||
            !auth.offline
        ) {

            window.location.href =
                '/login';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Branch
        |--------------------------------------------------------------------------
        */

        const params =
            new URLSearchParams(
                window.location.search
            );

        let branchId =
            params.get('branch');


        if (!branchId) {

            branchId =
                localStorage.getItem(
                    'offline_active_branch_id'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Admin / Dev / Owner must select branch
        |--------------------------------------------------------------------------
        */

        if (
            ['admin', 'dev', 'owner']
                .includes(auth.role) &&
            !branchId
        ) {

            window.location.href =
                '/offline-select-branch';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Staff uses their cached branch
        |--------------------------------------------------------------------------
        */

        if (
            auth.role === 'staff' &&
            !branchId
        ) {

            branchId = auth.branch_id;
        }

        if (
            auth.role === 'staff' &&
            !branchId
        ) {

            alert(
                'No offline branch is assigned to this account.'
            );

            window.location.href =
                '/login';

            return;
        }


        branchId =
            String(branchId);


        localStorage.setItem(
            'offline_active_branch_id',
            branchId
        );


        /*
        |--------------------------------------------------------------------------
        | DOM
        |--------------------------------------------------------------------------
        */

        const dishGrid =
            document.getElementById(
                'dish-grid'
            );

        const searchInput =
            document.getElementById(
                'menuSearch'
            );

        const categorySelect =
            document.getElementById(
                'category'
            );

        const cartItemsContainer =
            document.querySelector(
                '.cart-items'
            );

        const emptyCartMsg =
            document.getElementById(
                'empty-cart-msg'
            );

        const cartTotalEl =
            document.getElementById(
                'cart-total'
            );

        const cartSubtotalEl =
            document.getElementById(
                'cart-subtotal'
            );

        const discountPercentInput =
            document.getElementById(
                'discount-percent'
            );

        const discountAmountInput =
            document.getElementById(
                'discount-amount'
            );

        const clearCartBtn =
            document.getElementById(
                'clearCartBtn'
            );

        const checkoutBtn =
            document.getElementById(
                'checkoutBtn'
            );

        const paymentMethodSelect =
            document.getElementById(
                'payment-method'
            );

        const cashFields =
            document.getElementById(
                'cash-fields'
            );

        const digitalFields =
            document.getElementById(
                'digital-fields'
            );

        const cashTenderedInput =
            document.getElementById(
                'cash-tendered'
            );

        const changeAmountInput =
            document.getElementById(
                'change-amount'
            );

        const referenceNumberInput =
            document.getElementById(
                'reference-number'
            );


        let menuItems = [];

        let categories = [];

        let cart = [];

        let discountMode = null;


        /*
        |--------------------------------------------------------------------------
        | Load branch
        |--------------------------------------------------------------------------
        */

        const branches =
            await OfflineDB.getAll(
                OfflineDB.STORES.branches
            );

        const activeBranch =
            branches.find(
                branch =>
                    String(branch.id) ===
                    String(branchId)
            );


        if (!activeBranch) {

            alert(
                'Selected branch is not available offline.'
            );

            window.location.href =
                '/offline-select-branch';

            return;
        }


        document.title =
            `TheaRai Eatery | ${activeBranch.name} - Offline POS`;


        /*
        |--------------------------------------------------------------------------
        | Load menu
        |--------------------------------------------------------------------------
        */

        const cachedItems =
            await OfflineDB.getAll(
                OfflineDB.STORES.menuItems
            );


        menuItems =
            cachedItems.filter(
                item =>
                    String(item.branch_id) ===
                    String(branchId)
            );


        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        const categoryMap =
            new Map();


        menuItems.forEach(item => {

            if (
                item.category_id !== null &&
                item.category_id !== undefined
            ) {

                categoryMap.set(
                    String(item.category_id),
                    item.category_id
                );

            }

        });


        const cachedCategories =
            await OfflineDB.getAll(
                OfflineDB.STORES.categories
            );

        const categoryOptions =
            cachedCategories
                .filter(category =>
                    categoryMap.has(String(category.id))
                )
                .sort((a, b) =>
                    String(a.name).localeCompare(String(b.name))
                )
                .map(category => ({
                    value: String(category.id),
                    text: String(category.name)
                }));

        categorySelect.innerHTML =
            '<option value="all">All Categories</option>';

        categoryOptions.forEach(option => {
            const element = document.createElement('option');
            element.value = option.value;
            element.textContent = option.text;
            categorySelect.appendChild(element);
        });

        if (categorySelect.tomselect) {
            categorySelect.tomselect.clearOptions();
            categorySelect.tomselect.addOption([
                { value: 'all', text: 'All Categories' },
                ...categoryOptions
            ]);
            categorySelect.tomselect.refreshOptions(false);
        }


        /*
        |--------------------------------------------------------------------------
        | Render menu
        |--------------------------------------------------------------------------
        */

        function renderMenu(items) {

            dishGrid.innerHTML = '';


            if (!items.length) {

                dishGrid.innerHTML = `

                    <div
                        class="text-muted"
                        style="
                            grid-column:1/-1;
                            text-align:center;
                            padding:2rem;
                        "
                    >
                        No products available offline.
                    </div>

                `;

                return;
            }


            items.forEach(item => {

                const card =
                    document.createElement('div');

                card.className =
                    'dish-card';


                const price =
                    Number(
                        item.final_price || 0
                    );


                const initialChar =
                    String(item.name)
                        .charAt(0)
                        .toUpperCase();


                const imageHTML =
                    item.img_url
                        ? `
                            <img
                                src="${escapeHtml(
                                    item.img_url
                                )}"
                                alt="${escapeHtml(
                                    item.name
                                )}"
                            >
                          `
                        : `
                            <div class="placeholder">
                                ${escapeHtml(
                                    initialChar
                                )}
                            </div>
                          `;


                card.innerHTML = `

                    <div class="dish-img">

                        ${imageHTML}

                    </div>


                    <div class="dish-info">

                        <span class="dish-name">
                            ${escapeHtml(
                                item.name
                            )}
                        </span>


                        <span class="dish-price">

                            ${
                                window.formatPeso
                                    ? window.formatPeso.format(
                                        price
                                    )
                                    : `₱${price.toFixed(2)}`
                            }

                        </span>


                        <button
                            type="button"
                            class="btn card-btn"
                        >
                            Add To Cart
                        </button>

                    </div>

                `;


                card.addEventListener(
                    'click',
                    function () {

                        addToCart(item);

                    }
                );


                dishGrid.appendChild(card);

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Filtering
        |--------------------------------------------------------------------------
        */

        function applyFilters() {

            const query =
                searchInput.value
                    .trim()
                    .toLowerCase();


            const category =
                categorySelect.value;


            let filtered =
                menuItems;


            if (query) {

                filtered =
                    filtered.filter(
                        item =>
                            String(
                                item.name
                            )
                            .toLowerCase()
                            .includes(query)
                    );

            }


            if (category !== 'all') {

                filtered =
                    filtered.filter(
                        item =>
                            String(
                                item.category_id
                            ) ===
                            String(category)
                    );

            }


            renderMenu(filtered);

        }


        searchInput.addEventListener(
            'input',
            applyFilters
        );


        categorySelect.addEventListener(
            'change',
            applyFilters
        );


        /*
        |--------------------------------------------------------------------------
        | Cart
        |--------------------------------------------------------------------------
        */

        function addToCart(item) {

            const existing =
                cart.find(
                    cartItem =>
                        cartItem.id === item.id
                );


            if (existing) {

                existing.quantity++;

            } else {

                cart.push({

                    id:
                        item.id,

                    name:
                        item.name,

                    price:
                        Number(
                            item.final_price || 0
                        ),

                    quantity:
                        1

                });

            }


            updateCartUI();

        }


        function updateCartQty(
            id,
            change
        ) {

            const item =
                cart.find(
                    cartItem =>
                        cartItem.id === id
                );


            if (!item) {
                return;
            }


            item.quantity += change;


            if (item.quantity <= 0) {

                cart =
                    cart.filter(
                        cartItem =>
                            cartItem.id !== id
                    );

            }


            updateCartUI();

        }


        function getCartSubtotal() {
            return cart.reduce(
                (sum, item) => sum + item.price * item.quantity,
                0
            );
        }

        function getDiscountAmount(subtotal) {
            if (!discountMode || subtotal <= 0) {
                return 0;
            }

            if (discountMode === 'percentage') {
                const percentage = Math.min(
                    Math.max(Number(discountPercentInput.value) || 0, 0),
                    100
                );

                return subtotal * percentage / 100;
            }

            return Math.min(
                Math.max(Number(discountAmountInput.value) || 0, 0),
                subtotal
            );
        }

        function updateDiscountFields(subtotal, activeInput = null) {
            if (subtotal <= 0) {
                discountPercentInput.value = '';
                discountAmountInput.value = '';
                discountMode = null;
                return;
            }

            const discountAmount = getDiscountAmount(subtotal);

            if (discountMode === 'percentage') {
                const percentage = Math.min(
                    Math.max(Number(discountPercentInput.value) || 0, 0),
                    100
                );

                if (activeInput !== discountPercentInput) {
                    discountPercentInput.value = percentage || '';
                }

                discountAmountInput.value = discountAmount
                    ? discountAmount.toFixed(2)
                    : '';
            }

            if (discountMode === 'amount') {
                const amount = Math.min(
                    Math.max(Number(discountAmountInput.value) || 0, 0),
                    subtotal
                );

                discountPercentInput.value = amount
                    ? ((amount / subtotal) * 100).toFixed(2)
                    : '';

                if (activeInput !== discountAmountInput) {
                    discountAmountInput.value = amount
                        ? amount.toFixed(2)
                        : '';
                }
            }
        }

        function updateCartUI(activeInput = null) {

            cartItemsContainer.innerHTML = '';

            const subtotal = getCartSubtotal();
            const discountAmount = getDiscountAmount(subtotal);
            const total = Math.max(0, subtotal - discountAmount);


            if (!cart.length) {

                cartItemsContainer.appendChild(
                    emptyCartMsg
                );

                cartTotalEl.textContent =
                    formatMoney(0);

                cartSubtotalEl.textContent =
                    formatMoney(0);

                updateDiscountFields(0);

                checkoutBtn.disabled =
                    true;

                clearCartBtn.disabled =
                    true;

                calculateChange();

                return;
            }


            checkoutBtn.disabled =
                false;

            clearCartBtn.disabled =
                false;


            cart.forEach(item => {

                const lineTotal =
                    item.price *
                    item.quantity;


                const row =
                    document.createElement(
                        'div'
                    );


                row.className =
                    'cart-item';


                row.innerHTML = `

                    <div class="item-info">

                        <div class="item-name">
                            ${escapeHtml(
                                item.name
                            )}
                        </div>

                        <div class="price-field">

                            <div class="item-price">

                                ${formatMoney(
                                    item.price
                                )}

                            </div>

                            <div class="line-total">

                                ${formatMoney(
                                    lineTotal
                                )}

                            </div>

                        </div>

                    </div>


                    <div class="quantity-controls">

                        <button
                            class="qty-minus"
                            data-id="${item.id}"
                        >
                            −
                        </button>

                        <span class="qty">
                            ${item.quantity}
                        </span>

                        <button
                            class="qty-plus"
                            data-id="${item.id}"
                        >
                            +
                        </button>

                    </div>


                    <button
                        class="remove-btn"
                        data-id="${item.id}"
                    >
                        <span class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM428.5-291.5Q440-303 440-320v-280q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q17 0 28.5-11.5Zm160 0Q600-303 600-320v-280q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v280q0 17 11.5 28.5T560-280q17 0 28.5-11.5ZM280-720v520-520Z"/></svg>
                                </span>
                    </button>

                `;


                cartItemsContainer.appendChild(
                    row
                );

            });


            updateDiscountFields(subtotal, activeInput);
            cartSubtotalEl.textContent = formatMoney(subtotal);
            cartTotalEl.textContent = formatMoney(total);


            document
                .querySelectorAll('.qty-minus')
                .forEach(button => {

                    button.addEventListener(
                        'click',
                        function () {

                            updateCartQty(
                                Number(
                                    this.dataset.id
                                ),
                                -1
                            );

                        }
                    );

                });


            document
                .querySelectorAll('.qty-plus')
                .forEach(button => {

                    button.addEventListener(
                        'click',
                        function () {

                            updateCartQty(
                                Number(
                                    this.dataset.id
                                ),
                                1
                            );

                        }
                    );

                });


            document
                .querySelectorAll('.remove-btn')
                .forEach(button => {

                    button.addEventListener(
                        'click',
                        function () {

                            cart =
                                cart.filter(
                                    item =>
                                        item.id !==
                                        Number(
                                            this.dataset.id
                                        )
                                );

                            updateCartUI();

                        }
                    );

                });


            calculateChange();

        }

        discountPercentInput.addEventListener('input', function () {
            discountMode = 'percentage';
            updateCartUI(this);
        });

        discountAmountInput.addEventListener('input', function () {
            discountMode = 'amount';
            updateCartUI(this);
        });


        /*
        |--------------------------------------------------------------------------
        | Payment method
        |--------------------------------------------------------------------------
        */

        paymentMethodSelect.addEventListener(
            'change',
            function () {

                if (
                    this.value ===
                    'digital'
                ) {

                    cashFields.style.display =
                        'none';

                    digitalFields.style.display =
                        'flex';

                    cashTenderedInput.value =
                        '';

                    changeAmountInput.value =
                        formatMoney(0);

                } else {

                    cashFields.style.display =
                        'flex';

                    digitalFields.style.display =
                        'none';

                    referenceNumberInput.value =
                        '';

                }

            }
        );


        function calculateChange() {

            const subtotal = getCartSubtotal();
            const total = Math.max(0, subtotal - getDiscountAmount(subtotal));


            const cash =
                Number(
                    cashTenderedInput.value
                ) || 0;


            const change =
                Math.max(
                    0,
                    cash - total
                );


            changeAmountInput.value =
                formatMoney(change);

        }


        cashTenderedInput.addEventListener(
            'input',
            calculateChange
        );


        /*
        |--------------------------------------------------------------------------
        | Checkout preview
        |--------------------------------------------------------------------------
        */

        checkoutBtn.addEventListener(
            'click',
            function () {

                if (!cart.length) {
                    return;
                }


                const method =
                    paymentMethodSelect.value;


                const cashTendered =
                    Number(
                        cashTenderedInput.value
                    ) || 0;


                const subtotal = getCartSubtotal();
                const discountAmount = getDiscountAmount(subtotal);
                const total = Math.max(0, subtotal - discountAmount);


                if (
                    method === 'cash' &&
                    cashTendered < total
                ) {

                    alert(
                        'Insufficient cash tendered!'
                    );

                    return;
                }


                document
                    .getElementById(
                        'preview-branch'
                    )
                    .textContent =
                        activeBranch.name;


                document
                    .getElementById(
                        'preview-address'
                    )
                    .textContent =
                        activeBranch.address ||
                        'Davao City';


                document
                    .getElementById(
                        'preview-date'
                    )
                    .textContent =
                        new Date()
                            .toLocaleString();


                document
                    .getElementById(
                        'preview-payment-method'
                    )
                    .textContent =
                        method.toUpperCase();


                const previewItems =
                    document.getElementById(
                        'preview-items'
                    );


                previewItems.innerHTML =
                    '';


                cart.forEach(item => {

                    const row =
                        document.createElement(
                            'tr'
                        );


                    row.innerHTML = `

                        <td>
                            ${item.quantity}x
                        </td>

                        <td>
                            ${escapeHtml(
                                item.name
                            )}
                        </td>

                        <td>
                            ${formatMoney(
                                item.price *
                                item.quantity
                            )}
                        </td>

                    `;


                    previewItems.appendChild(
                        row
                    );

                });


                document
                    .getElementById(
                        'preview-subtotal'
                    )
                    .textContent =
                        formatMoney(subtotal);

                document
                    .getElementById(
                        'preview-discount'
                    )
                    .textContent =
                        formatMoney(discountAmount);

                document
                    .getElementById(
                        'preview-total'
                    )
                    .textContent =
                        formatMoney(total);


                document
                    .getElementById(
                        'preview-tendered'
                    )
                    .textContent =
                        formatMoney(
                            method === 'digital'
                                ? total
                                : cashTendered
                        );


                document
                    .getElementById(
                        'preview-change'
                    )
                    .textContent =
                        formatMoney(
                            method === 'digital'
                                ? 0
                                : cashTendered -
                                  total
                        );


                document
                    .getElementById(
                        'in-cart-preview'
                    )
                    .style.display =
                        'flex';

            }
        );


        document
            .getElementById(
                'cancelPreviewBtn'
            )
            .addEventListener(
                'click',
                function () {

                    document
                        .getElementById(
                            'in-cart-preview'
                        )
                        .style.display =
                        'none';

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Save Offline Order
        |--------------------------------------------------------------------------
        */

        document
            .getElementById(
                'confirmProcessBtn'
            )
            .addEventListener(
                'click',
                async function () {

                    const button =
                        this;


                    button.disabled =
                        true;

                    button.textContent =
                        'Saving...';


                    try {

                        const method =
                            paymentMethodSelect.value;


                        const cashTendered =
                            Number(
                                cashTenderedInput.value
                            ) || 0;


                        const subtotal = getCartSubtotal();

                        const discountAmount = getDiscountAmount(subtotal);

                        const total = Math.max(0, subtotal - discountAmount);

                        const localId = crypto.randomUUID();

                        const receiptNo = createOfflineReceiptNo();

                        const order = {

                            local_id:
                                localId,

                            receipt_no:
                                receiptNo,

                            branch_id:
                                Number(branchId),

                            sync_token:
                                auth.sync_token,

                            total_amount:
                                total,

                            subtotal_amount:
                                subtotal,

                            discount_type:
                                discountMode,

                            discount_value:
                                discountMode === 'percentage'
                                    ? Number(discountPercentInput.value) || 0
                                    : Number(discountAmountInput.value) || 0,

                            discount_amount:
                                discountAmount,

                            cash_tendered:
                                method === 'digital'
                                    ? total
                                    : cashTendered,

                            change_amount:
                                method === 'digital'
                                    ? 0
                                    : cashTendered -
                                      total,

                            payment_method:
                                method,

                            reference_number:
                                referenceNumberInput
                                    .value
                                    .trim() ||
                                null,

                            items:
                                cart.map(
                                    item => ({

                                        menu_item_id:
                                            item.id,

                                        quantity:
                                            item.quantity,

                                        price_at_time:
                                            item.price,

                                        subtotal:
                                            item.price *
                                            item.quantity

                                    })
                                ),

                            created_at:
                                new Date()
                                    .toISOString(),

                            sync_status:
                                'pending'

                        };


                        await OfflineDB.put(
                            OfflineDB.STORES.orders,
                            order
                        );


                        alert(
                            `Order ${receiptNo} saved offline.`
                        );


                        cart = [];


                        cashTenderedInput.value =
                            '';

                        referenceNumberInput.value =
                            '';


                        updateCartUI();


                        document
                            .getElementById(
                                'in-cart-preview'
                            )
                            .style.display =
                            'none';


                    } catch (error) {

                        console.error(
                            'Failed to save offline order:',
                            error
                        );

                        alert(
                            'Failed to save the order locally.'
                        );

                    } finally {

                        button.disabled =
                            false;

                        button.textContent =
                            'Save Order';

                    }

                }
            );


        clearCartBtn.addEventListener(
            'click',
            function () {

                if (!cart.length) {
                    return;
                }


                if (
                    confirm(
                        'Clear the Cart?'
                    )
                ) {

                    cart = [];

                    cashTenderedInput.value =
                        '';

                    updateCartUI();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Initial render
        |--------------------------------------------------------------------------
        */

        renderMenu(menuItems);

        updateCartUI();

    }
);


function createOfflineReceiptNo() {

    const timestamp =
        new Date()
            .toISOString()
            .replace(/\D/g, '')
            .slice(0, 14);


    const random =
        Math.floor(
            Math.random() * 9999
        )
        .toString()
        .padStart(4, '0');


    return `OFF-${timestamp}-${random}`;

}


function formatMoney(amount) {

    if (
        window.formatPeso &&
        typeof window.formatPeso.format ===
        'function'
    ) {

        return window.formatPeso.format(
            Number(amount) || 0
        );

    }


    return `₱${(
        Number(amount) || 0
    ).toFixed(2)}`;

}


function escapeHtml(value) {

    const div =
        document.createElement('div');

    div.textContent =
        value ?? '';

    return div.innerHTML;

}