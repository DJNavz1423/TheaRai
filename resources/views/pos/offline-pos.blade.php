@extends('layouts.offline')

@section('title', 'POS')

@section('content')
  <div class="container products-container">
    <div class="row table-controls border-b">
      <div class="searchbox">
        <span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
            <path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
          </svg>
        </span>

            <input type="text" id="menuSearch" class="border searchBar" placeholder="Search products...">
        </div>

        <div class="filters">
            <select id="category" class="ts-filter unit-selector">
                <option value="all">
                    All Categories
                </option>
            </select>
        </div>
    </div>

    <div id="dish-grid" class="product-grid"></div>
</div>

<div class="container cart-container border-l">
    <div class="cart-header border-b">

        <h2>
          <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
              <path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h440q17 0 28.5 11.5T760-320q0 17-11.5 28.5T720-280H280q-45 0-68-39.5t-2-78.5l54-98-144-304H80q-17 0-28.5-11.5T40-840q0-17 11.5-28.5T80-880h65q11 0 21 6t15 17l27 57Zm134 280h280-280Z"/>
            </svg>
          </span>
            Current Order
        </h2>

        <button id="clearCartBtn" class="btn clear-btn">
          Clear All
        </button>
    </div>

    <div class="cart-items">
        <div id="empty-cart-msg">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                <path d="M400-640q-17 0-28.5-11.5T360-680q0-17 11.5-28.5T400-720h160q17 0 28.5 11.5T600-680q0 17-11.5 28.5T560-640H400ZM223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM120-800H80q-17 0-28.5-11.5T40-840q0-17 11.5-28.5T80-880h66q11 0 21 6t15 17l159 337h280l145-260q5-10 14-15t20-5q23 0 34.5 19.5t.5 39.5L692-482q-11 20-29.5 31T622-440H324l-44 80h440q17 0 28.5 11.5T760-320q0 17-11.5 28.5T720-280H280q-45 0-68.5-39t-1.5-79l54-98-144-304Z"/>
              </svg>
            </span>

            <span class="text-muted">Cart is empty.</span>
            <span class="text-muted">Add products to get started.</span>
        </div>
    </div>


    <div class="cart-summary border-t">
      <div class="summary-row summary-total">
        <h2>Total</h2>
        <h3 id="cart-total">&#8369;0.00</h3>
      </div>
    </div>

    <div class="cart-footer border-t">
      <div class="cart-actions">
        <div class="input-group">
          <label for="payment-method">Payment Method</label>
            <select id="payment-method" class="unit-selector">
              <option value="cash">Cash</option>
              <option value="digital">E-Wallet (e.g., Gcash, Maya)</option>
            </select>
        </div>

            <div id="cash-fields" class="cash-fields row">
              <div class="input-group">
                <label for="cash-tendered">Cash Tendered</label>
                <input type="text" inputmode="decimal" pattern="[0-9]*(\.[0-9]+)?" id="cash-tendered" class="cash-input" min="1" step="0.01">
              </div>

                <div class="input-group">
                  <label for="change-amount">Change</label>

                    <input type="text" id="change-amount" class="change-input" readonly value="&#8369;0.00">
                </div>
            </div>

            <div id="digital-fields" class="row digital-fields" style="display:none;">
                <div class="input-group">
                    <label for="reference-number">Ref No. (Optional)</label>
                    <input type="text" id="reference-number" placeholder="e.g., Gcash Transaction ID">
                </div>
            </div>
        </div>


        <button type="button" id="checkoutBtn" class="btn checkout-btn">
            Checkout
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                <path d="M727-440H120q-17 0-28.5-11.5T80-480q0-17 11.5-28.5T120-520h607L572-676q-11-11-11.5-27.5T572-732q11-11 28-11t28 11l224 224q6 6 8.5 13t2.5 15q0 8-2.5 15t-8.5 13L628-228q-11 11-27.5 11T572-228q-12-12-12-28.5t12-28.5l155-155Z"/>
              </svg>
            </span>
        </button>
    </div>


    {{-- Receipt preview --}}

    <div
        id="in-cart-preview"
        style="
            display:none;
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:var(--light-pure);
            z-index:10;
            flex-direction:column;
        ">

        <div class="cart-header border-b">

            <h2>
              <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                  <path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T760-80H240Zm280-520v-200H240v640h520v-440H520ZM240-800v200-200 640-640Z"/></svg>
                </span>
                Review Receipt
            </h2>

        </div>


        <div style="
                flex:1;
                overflow-y:auto;
                padding:1.5rem;
                font-family:monospace;
                font-size:14px;
                background:#fff;">

          <div style="
                    text-align:center;
                    border-bottom:1px dashed #ccc;
                    padding-bottom:10px;
                    margin-bottom:10px;">

            <strong id="preview-branch" style="font-size:16px; font-weight:900;color:var(--secondary-deep);"></strong>
            <br>
            <small id="preview-address" ></small>
          </div>


          <div style="margin-bottom:10px;font-size:12px;">
            Date: <span id="preview-date"></span><br>
            Payment:<span id="preview-payment-method"></span>
          </div>

          <table style="width:100%;border-collapse:collapse;">
            <thead>
              <tr>
                <th>Qty</th>
                <th>Item</th>
                <th>Amount</th>
              </tr>
            </thead>

            <tbody id="preview-items"></tbody>
          </table>


            <div style="margin-top:10px;">
              <strong>Total: </strong>

              <span id="preview-total"></span><br>

              Cash Tendered:

              <span id="preview-tendered"></span><br>

              Change:

              <span id="preview-change"></span>
            </div>
        </div>

        <div class="cart-footer border-t" style="display:flex;gap:1rem;padding:1rem;">

            <button type="button" id="cancelPreviewBtn" class="btn cancel-btn" 
            style=" flex: 1; 
            background: none; 
            border: 1px solid var(--secondary-soft); 
            color: var(--secondary);">
              Back to Cart
            </button>

            <button type="button" id="confirmProcessBtn" class="btn checkout-btn" style="flex: 1;">
                Save Order
            </button>
        </div>
    </div>
</div>

@endsection


@once
  @push('styles')
  <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
  <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/filters.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pos/pos.css') }}">
  @endpush
@endonce


@once
  @push('scripts')
  <script src="{{ asset('js/tomSelect/tomSelect.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}" defer></script>
  <script src="{{ asset('js/utils/currency.js') }}" defer></script>

  <script src="{{ asset('js/offline/pos/cachePOSData.js') }}" defer></script>
  <script src="{{ asset('js/offline/pos/offlinePOS.js') }}" defer></script>
  <script src="{{ asset('js/offline/pos/syncOfflineOrders.js') }}" defer></script>
  @endpush
@endonce