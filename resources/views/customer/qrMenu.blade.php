<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/filters.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/digiMenu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
    
    @stack('styles')

    <title>{{ $branch->name }} | Menu</title>
</head>

<body>
    @include('loader')

    <header>
        <h2>TheaRai Eatery - {{ str_replace('TheaRai ', '', $branch->name) }}</h2>
        <p>Currently ordering for <strong>Table - {{ $table->table_number }}</strong></p>
    </header>

    <div class="row table-controls border-b">
        <div class="searchbox">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </span>
            <input type="text" id="searchInput" class="border searchBar" placeholder="Search menu..." oninput="filterMenu()">
        </div>
        
        <div class="filters">
            <select id="categoryFilter" onchange="filterMenu()" class="ts-filter unit-selector">
                <option value="all">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="menu-container"></div>

    <button id="floating-cart-btn" class="btn" onclick="openCart()">
        <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h440q17 0 28.5 11.5T760-320q0 17-11.5 28.5T720-280H280q-45 0-68-39.5t-2-78.5l54-98-144-304H80q-17 0-28.5-11.5T40-840q0-17 11.5-28.5T80-880h65q11 0 21 6t15 17l27 57Zm134 280h280-280Z"/></svg>
        </span>
        <div>View Order</div>
        <div><span id="fab-count">0</span></div>
    </button>

    <div id="cart-panel">
        <div class="cart-header">
            <h2>
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h440q17 0 28.5 11.5T760-320q0 17-11.5 28.5T720-280H280q-45 0-68-39.5t-2-78.5l54-98-144-304H80q-17 0-28.5-11.5T40-840q0-17 11.5-28.5T80-880h65q11 0 21 6t15 17l27 57Zm134 280h280-280Z"/></svg>
                </span>
                Your Orders
            </h2>
            <button class="close-cart-btn" onclick="closeCart()">✕</button>
        </div>
        <div class="cart-body" id="cart-items-container">
            </div>
        <div class="cart-footer">
            <div class="total-row">
                <span>Total Due:</span>
                <span id="cart-total-price">₱0.00</span>
            </div>
            <button id="checkout-btn" onclick="processCheckout()">Proceed to Checkout</button>
        </div>
    </div>

    <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}"></script>

    <script>
        const menuItems = @json($menuItems);
        const categories = @json($categories); // Pulled so we have access to category names
        let cart = {};

        const formatPeso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });

        // Helper function to build a single card HTML
        function createCardElement(item) {
            const initialChar = item.name.charAt(0).toUpperCase();
            const imgHTML = item.img_url 
                ? `<img src="${item.img_url}" alt="${item.name}">` 
                : `<div class="placeholder">${initialChar}</div>`;

            const card = document.createElement('div');
            card.className = 'menu-card';
            card.innerHTML = `
                <div class="dish-img">${imgHTML}</div>
                <div class="dish-info">
                    <span class="dish-name">${item.name}</span>
                    <span class="dish-price">${formatPeso.format(item.price)}</span>
                    <button class="btn add-btn" onclick="addToCart(${item.id}, '${item.name.replace(/'/g, "\\'")}', ${item.price}, '${item.img_url || ''}')">
                    <span class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>    
                    </span>  
                    Add to Order
                </button>
                </div>
            `;
            return card;
        }

        // Main render function handles BOTH grouped and flat layouts
        function renderMenu(items, isGrouped) {
            const container = document.getElementById('menu-container');
            container.innerHTML = '';

            if (items.length === 0) {
                container.innerHTML = '<p style="text-align:center; padding: 40px; color:var(--secondary-light);">No items found.</p>';
                return;
            }

            if (isGrouped) {
                // Render Grouped by Category
                categories.forEach(cat => {
                    const catItems = items.filter(item => item.category_id == cat.id);
                    
                    // ONLY render the category if there are items inside it!
                    if (catItems.length > 0) {
                        const section = document.createElement('div');
                        section.className = 'category-section';
                        
                        const heading = document.createElement('h3');
                        heading.className = 'category-heading';
                        heading.innerText = cat.name;
                        section.appendChild(heading);

                        const grid = document.createElement('div');
                        grid.className = 'menu-grid';
                        
                        catItems.forEach(item => {
                            grid.appendChild(createCardElement(item));
                        });

                        section.appendChild(grid);
                        container.appendChild(section);
                    }
                });
            } else {
                // Render Flat Grid (Search Results or Single Category)
                const grid = document.createElement('div');
                grid.className = 'menu-grid';
                items.forEach(item => {
                    grid.appendChild(createCardElement(item));
                });
                container.appendChild(grid);
            }
        }

        function filterMenu() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const catId = document.getElementById('categoryFilter').value;

            let filtered = menuItems;

            if (catId !== 'all') {
                filtered = filtered.filter(item => item.category_id == catId);
            }
            if (query !== '') {
                filtered = filtered.filter(item => item.name.toLowerCase().includes(query));
            }

            // ONLY show the headings if 'All Categories' is selected AND they are not actively typing a search query.
            const isGrouped = (catId === 'all' && query === '');

            renderMenu(filtered, isGrouped);
        }

        function addToCart(id, name, price, imgUrl) {
            if(cart[id]) {
                cart[id].quantity++;
            } else {
                cart[id] = { id, name, price, imgUrl, quantity: 1 };
            }
            updateCartUI();
        }

        function updateCartQty(id, change) {
            if(cart[id]) {
                cart[id].quantity += change;
                if(cart[id].quantity <= 0) {
                    delete cart[id];
                }
                updateCartUI();
            }
        }

        function removeCartItem(id) {
            delete cart[id];
            updateCartUI();
        }

        function updateCartUI() {
            const container = document.getElementById('cart-items-container');
            container.innerHTML = '';
            
            let totalQty = 0;
            let totalPrice = 0;

            for(let id in cart) {
                const item = cart[id];
                totalQty += item.quantity;
                totalPrice += (item.price * item.quantity);

                const initialChar = item.name.charAt(0).toUpperCase();
                const imgHTML = item.imgUrl 
                    ? `<img src="${item.imgUrl}" alt="${item.name}">` 
                    : `${initialChar}`;

                const row = document.createElement('div');
                row.className = 'cart-item';
                row.innerHTML = `
                    <div class="img-wrapper">${imgHTML}</div>
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">${formatPeso.format(item.price * item.quantity)}</div>
                        <div class="qty-controls">
                            <button class="qty-btn" onclick="updateCartQty(${item.id}, -1)">
                                <span class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M240-440q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h480q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H240Z"/></svg>
                                </span>        
                            </button>
                            <span>${item.quantity}</span>
                            <button class="qty-btn" onclick="updateCartQty(${item.id}, 1)">
                                <span class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
                                </span>
                            </button>
                        </div>
                    </div>
                    <button class="remove-btn" onclick="removeCartItem(${item.id})">
                        <span class="icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM428.5-291.5Q440-303 440-320v-280q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q17 0 28.5-11.5Zm160 0Q600-303 600-320v-280q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v280q0 17 11.5 28.5T560-280q17 0 28.5-11.5ZM280-720v520-520Z"/></svg>
                        </span>    
                    </button>
                `;
                container.appendChild(row);
            }

            // Update Floating Button
            const fab = document.getElementById('floating-cart-btn');
            document.getElementById('fab-count').innerText = totalQty;
            fab.style.display = totalQty > 0 ? 'flex' : 'none';

            // Update Panel Totals
            document.getElementById('cart-total-price').innerText = formatPeso.format(totalPrice);
            document.getElementById('checkout-btn').disabled = totalQty === 0;

            if (totalQty === 0) {
                container.innerHTML = `
                <div class="empty-cart-msg">
                    <span class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M400-640q-17 0-28.5-11.5T360-680q0-17 11.5-28.5T400-720h160q17 0 28.5 11.5T600-680q0 17-11.5 28.5T560-640H400ZM223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM120-800H80q-17 0-28.5-11.5T40-840q0-17 11.5-28.5T80-880h66q11 0 21 6t15 17l159 337h280l145-260q5-10 14-15t20-5q23 0 34.5 19.5t.5 39.5L692-482q-11 20-29.5 31T622-440H324l-44 80h440q17 0 28.5 11.5T760-320q0 17-11.5 28.5T720-280H280q-45 0-68.5-39t-1.5-79l54-98-144-304Z"/></svg>
                    </span>
                    <span class="text-muted">Your cart is empty.</span>
                    <span class="text-muted">Add some delicious food to get started.</span>
                </div>`;
            }
        }

        function openCart() {
            document.getElementById('cart-panel').classList.add('open');
            document.body.style.overflow = 'hidden'; 
        }

        function closeCart() {
            document.getElementById('cart-panel').classList.remove('open');
            document.body.style.overflow = '';
        }

        async function processCheckout() {
            const btn = document.getElementById('checkout-btn');
            btn.disabled = true;
            btn.innerText = "Processing...";

            const itemsArray = Object.values(cart);

            try {
                const response = await fetch("{{ route('qr.checkout') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        branch_id: {{ $branch->id }},
                        table_id: {{ $table->id }},
                        items: itemsArray
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.checkout_url;
                } else {
                    alert("Checkout failed: " + data.message);
                    btn.disabled = false;
                    btn.innerText = "Proceed to Checkout";
                }
            } catch (err) {
                alert("Network error.");
                btn.disabled = false;
                btn.innerText = "Proceed to Checkout";
            }
        }

        // Force a filter sweep on load to setup the initial grouped layout
        filterMenu();
        updateCartUI();
    </script>

    @stack('scripts')
</body>
</html>