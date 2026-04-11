@extends($layout)

@section('title', 'Point of Sale')

@section('content')
  <div class="container products-container">
    <div class="products-toolbar">
        <div class="searchbox">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </span>

            <input type="text" id="menuSearch" class="border searchBar" placeholder="Search products...">
        </div>

        <div class="filters">
            <select name="category_id" id="category" class="unit-selector">
                <option value="all" selected>All Categories</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="dish-grid" class="product-grid"></div>
  </div>

  <div class="container cart-container">
    <div class="cart-header">
        <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h440q17 0 28.5 11.5T760-320q0 17-11.5 28.5T720-280H280q-45 0-68-39.5t-2-78.5l54-98-144-304H80q-17 0-28.5-11.5T40-840q0-17 11.5-28.5T80-880h65q11 0 21 6t15 17l27 57Zm134 280h280-280Z"/></svg>
        </span>
        <h2>Current Order</h2>

        <span id="itemCount" class="badge">0 items</span>

        <button id="clearCartBtn" class="btn clear-btn">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM428.5-291.5Q440-303 440-320v-280q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q17 0 28.5-11.5Zm160 0Q600-303 600-320v-280q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v280q0 17 11.5 28.5T560-280q17 0 28.5-11.5ZM280-720v520-520Z"/></svg>
            </span>
        </button>
    </div>

    <div class="cart-body">
        <div class="cart-items">
            <div id="empty-cart-msg">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M400-640q-17 0-28.5-11.5T360-680q0-17 11.5-28.5T400-720h160q17 0 28.5 11.5T600-680q0 17-11.5 28.5T560-640H400ZM223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM120-800H80q-17 0-28.5-11.5T40-840q0-17 11.5-28.5T80-880h66q11 0 21 6t15 17l159 337h280l145-260q5-10 14-15t20-5q23 0 34.5 19.5t.5 39.5L692-482q-11 20-29.5 31T622-440H324l-44 80h440q17 0 28.5 11.5T760-320q0 17-11.5 28.5T720-280H280q-45 0-68.5-39t-1.5-79l54-98-144-304Z"/></svg>
                </span>

                <span class="text-muted">Cart is empty</span>

                <span class="text-muted">Add products to get started</span>
            </div>
        </div>

        <div class="cart-summary">
            <h2>Total</h2>
            <h3 id="cart-total">&#8369;0.00</h3>
        </div>
    </div>

    <div class="cart-footer">
            <div class="row">
                <div class="payment-method-section">
                    <label for="payment-method">Payment Method</label>
                    <select name="payment-method" id="payment-method">
                        <option value="cash">Cash</option>
                        <option value="digital">E-Wallet (e.g., Gcash, Maya)</option>
                    </select>
                </div>

                <div id="cash-fields" class="row">
                    <div class="input-group">
                        <label for="cash-tendered">Cash Tendered</label>
                        <input type="number" id="cash-tendered" class="cash-input" min="1" step="0.01">
                    </div>

                    <div class="input-group">
                        <label for="change-amount">Change</label>
                        <input type="text" id="change-amount" class="change-input" readonly value="&#8369;0.00">
                    </div>
                </div>

                <div id="digital-fields" class="row" style="display: none;">
                    <div class="input-group">
                        <label for="reference-number">Ref No. (Optional)</label>
                        <input type="text" id="reference-number" placeholder="e.g., Gcash Transaction ID">
                        <small class="text-muted">Exact amount recorded automatically.</small>
                    </div>
                </div>
            </div>

            <button type="button" id="checkoutBtn" class="btn checkout-btn">
                Checkout

                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M727-440H120q-17 0-28.5-11.5T80-480q0-17 11.5-28.5T120-520h607L572-676q-11-11-11.5-27.5T572-732q11-11 28-11t28 11l224 224q6 6 8.5 13t2.5 15q0 8-2.5 15t-8.5 13L628-228q-11 11-27.5 11T572-228q-12-12-12-28.5t12-28.5l155-155Z"/></svg>
                </span>
            </button>
        </div>
  </div>
@endsection

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
        <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
    @endpush
@endonce

@once
    @push('scripts')
     <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
     <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}"></script>
     <script type="text/javascript" src="{{ asset('js/utils/currency.js') }}"></script>
        <script>
            const menuItems = @json($menuItems);
            let cart = [];

            const dishGrid = document.getElementById('dish-grid');
            const cartItemsContainer = document.querySelector('.cart-items');
            const emptyCartMsg = document.getElementById('empty-cart-msg');
            const cartTotalEl = document.getElementById('cart-total');
            const clearCartBtn = document.getElementById('clearCartBtn');
            const checkoutBtn = document.getElementById('checkoutBtn');
            const cashTenderedInput = document.getElementById('cash-tendered');
            const changeAmountInput = document.getElementById('change-amount');
            const paymentMethodSelect = document.getElementById('payment-method');
            const cashFields = document.getElementById('cash-fields');
            const digitalFields = document.getElementById('digital-fields');
            const referenceNumberInput = document.getElementById('reference-number');
            
            //render menu items
            function renderMenu(items){
                dishGrid.innerHTML = '';

                items.forEach(item => {
                    const price = window.formatPeso.format(item.final_price);
                    const initialChar = item.name.charAt(0).toUpperCase();
                    const imgHTML = item.img_url 
                    ? `<img src="${item.img_url}" alt="${item.name}">` 
                    : `<div class="placeholder">${initialChar}</div>`;

                    const isAvailable = item.is_available === false ? false : true;
                    const soldOutClass = isAvailable ? '' : 'not-available';

                    const btnClass = isAvailable ? 'is-avail' : 'is-not-avail';
                    const btnText = isAvailable ? 'Mark Unavailable' : 'Mark Available';

                    const card = document.createElement('div');
                    card.className = 'dish-card ${soldOutClass}';

                    card.innerHTML = `
                        ${imgHTML}
                        <div class="dish-info">
                            <span class="dish-name">${item.name}</span>
                            <span class="dish-price">${price}</span>
                        </div>
                        <button class="toggle-avail-btn ${btnClass}" data-id="${item.id}">${btnText}</button>
                    `;

                    card.addEventListener('click', (e) => {
                        if(e.target.classList.contains('toggle-avail-btn')) return;

                        if(!isAvailable) return;

                        addToCart(item)
                    });

                    const toggleBtn = card.querySelector('.toggle-avail-btn');
                    toggleBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        toggleAvailability(item.id, toggleBtn, card);
                    })

                    card.addEventListener('mousedown', () => { if(isAvailable) card.style.transform = 'scale(0.95)' });
                    card.addEventListener('mouseup', () => card.style.transform = 'scale(1)');
                    card.addEventListener('mouseleave', () => card.style.transform = 'scale(1)');

                    dishGrid.appendChild(card);
                });
            }

            function toggleAvailability(item, buttonEl, cardEl){
                buttonEl.disabled = true;
                buttonEl.innerHTML = 'Updating...';

                fetch(`/cashier/pos/{id}/toggle-availability`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success){
                        item.is_available = data.is_available;

                        const catId = document.getElementById('category').value;
                        const query = document.getElementById('menuSearch').value.toLowerCase();

                        let filteredItems = menuItems;

                        if(catId !== 'all')
                            filteredItems = filteredItems.filter(item => item.category_id == catId);

                        if(query !== '')
                            filteredItems = filteredItems.filter(item => item.name.toLowerCase().includes(query));
                        
                        renderMenu(filteredItems);
                    } else {
                        alert('Failed to update availability');
                        buttonEl.disabled = false;
                        buttonEl.innerHTML = item.is_available ? 'Mark Unavailable' : 'Mark Available';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Something went wrong.');
                    buttonEl.disabled = false;
                    buttonEl.innerHTML = item.is_available ? 'Mark Unavailable' : 'Mark Available';
                });
            }

            //category filtering
            document.getElementById('category').addEventListener('change', (e) => {
                const catId = e.target.value;

                if(catId === 'all'){
                    renderMenu(menuItems);
                } else{
                    const filteredItems = menuItems.filter(item => item.category_id == catId);
                    renderMenu(filteredItems);
                }
            });

            //search filtering
            document.getElementById('menuSearch').addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const filteredItems = menuItems.filter(item => item.name.toLowerCase().includes(query));
                renderMenu(filteredItems);
            });

            //cart logic
            function addToCart(item){
                const existingItem = cart.find(c => c.id === item.id);
                if(existingItem){
                    existingItem.quantity++;
                } else {
                    cart.push({
                        id: item.id,
                        name: item.name,
                        price: parseFloat(item.final_price),
                        quantity: 1
                    });
                }
                updateCartUI();
            }

            function updateCartQty(id, change){
                const index = cart.findIndex(c => c.id === id);
                if (index > -1){
                    cart[index].quantity += change;
                    if(cart[index].quantity <=0){
                        cart.splice(index, 1)
                    }
                }
                updateCartUI();
            }

            function updateCartUI(){
                cartItemsContainer.innerHTML = '';
                let total = 0;

                if(cart.length === 0){
                    cartItemsContainer.appendChild(emptyCartMsg);
                    checkoutBtn.disabled = true;
                    clearCartBtn.disabled = true;
                    cartTotalEl.textContent = window.formatPeso.format(0);
                } else{
                    checkoutBtn.disabled = false;
                    clearCartBtn.disabled = false;

                    cart.forEach(item => {
                        const lineTotal = item.price * item.quantity;
                        total += lineTotal;

                        const row = document.createElement('div');
                        row.className = 'cart-item';
                        row.innerHTML = `
                            <div class="item-info">
                                <div class="item-name">${item.name}</div>
                                <div class="item-price">${window.formatPeso.format(item.price)}</div>
                            </div>

                            <div class="quantity-controls">
                                <button class="qty-minus" data-id="${item.id}">-</button>
                                <span class="qty">${item.quantity}</span>
                                <button class="qty-plus" data-id="${item.id}">+</button>
                            </div>

                            <div class="line-total">
                                ${window.formatPeso.format(lineTotal)}
                            </div>

                            <button class="remove-btn" data-id="${item.id}">
                                <span class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM428.5-291.5Q440-303 440-320v-280q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q17 0 28.5-11.5Zm160 0Q600-303 600-320v-280q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v280q0 17 11.5 28.5T560-280q17 0 28.5-11.5ZM280-720v520-520Z"/></svg>
                                </span>
                            </button>
                        `;
                        cartItemsContainer.appendChild(row);
                    });

                    document.querySelectorAll('.qty-minus').forEach(btn => {
                    btn.addEventListener('click', (e) => updateCartQty(parseInt(e.target.dataset.id), -1));
                    });

                    document.querySelectorAll('.qty-plus').forEach(btn => {
                    btn.addEventListener('click', (e) => updateCartQty(parseInt(e.target.dataset.id), 1));
                    });

                    document.querySelectorAll('.remove-btn').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            const button = e.target.closest('.remove-btn');
                            const idToRemove = parseInt(button.dataset.id);

                            cart = cart.filter(item => item.id !== idToRemove);

                            updateCartUI();
                        });
                    });
                }
                cartTotalEl.textContent = window.formatPeso.format(total);
                calculateChange();
            }

            //toggle payment fields
            paymentMethodSelect.addEventListener('change', (e) => {
                const method = e.target.value;

                if(method === 'digital'){
                    cashFields.style.display = 'none';
                    digitalFields.style.display = 'block';

                    cashTenderedInput.value = '';
                    changeAmountInput.value = window.formatPeso.format(0);
                } else{
                    cashFields.style.display = 'block';
                    digitalFields.style.display = 'none';
                    referenceNumberInput.value = '';
                }
            });

            // checkout | AJAX request
            checkoutBtn.addEventListener('click', (e) => {
                e.preventDefault();

                if(cart.length === 0) return;

                const selectedMethod = paymentMethodSelect.value;
                const cashTendered = parseFloat(cashTenderedInput.value) || 0;
                const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

                if(selectedMethod === 'cash' && cashTendered < totalAmount){
                    alert('Insufficient cash tendered!');
                    return;
                }

                checkoutBtn.disabled = true;
                clearCartBtn.disabled = true;
                checkoutBtn.innerHTML = 'Processing...';

                fetch("{{ route('cashier.pos.order') }}",{
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        cart: cart,
                        total_amount: totalAmount,
                        cash_tendered: selectedMethod === 'digital' ? totalAmount : cashTendered,
                        payment_method: selectedMethod,
                        reference_number: referenceNumberInput.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success){
                        alert('Order Paid & Stock Deducted Successfully!');
                        cart = [];
                        cashTenderedInput.value = '';
                        updateCartUI();
                    } else{
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Something went wrong. Check the console.');
                })
                .finally(() => {
                    checkoutBtn.innerHTML = 
                    `
                    Checkout

                    <span class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M727-440H120q-17 0-28.5-11.5T80-480q0-17 11.5-28.5T120-520h607L572-676q-11-11-11.5-27.5T572-732q11-11 28-11t28 11l224 224q6 6 8.5 13t2.5 15q0 8-2.5 15t-8.5 13L628-228q-11 11-27.5 11T572-228q-12-12-12-28.5t12-28.5l155-155Z"/></svg>
                    </span>
                    `;
                    checkoutBtn.disabled = false;
                    clearCartBtn.disabled = false;
                });
            });

            //clear cart
            clearCartBtn.addEventListener('click', () => {
                if(cart.length === 0) return;

                if(confirm('Clear the Cart?')){
                    cart = [];
                    document.getElementById('cash-tendered').value = '';
                    updateCartUI();
                }
            });

            function calculateChange(){
                const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                const cash = parseFloat(cashTenderedInput.value) || 0;

                let change = cash - totalAmount;

                if (change < 0 || totalAmount === 0){
                    change = 0;
                }

                changeAmountInput.value = window.formatPeso.format(change);
            }

            cashTenderedInput.addEventListener('input', calculateChange);

            renderMenu(menuItems);

        </script>
    @endpush
@endonce