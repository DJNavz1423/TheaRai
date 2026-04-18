@extends('layouts.admin')


@section('title', 'Inventory')

@section('content')
 <div class="container">
    <div class="row mb-3">
        <h1 class="heading">Item List ({{ count($ingredients) }})</h1>

        <div class="row heading-btn-row">
            <a href="#" class="btn">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M433-80q-27 0-46.5-18T363-142l-9-66q-13-5-24.5-12T307-235l-62 26q-25 11-50 2t-39-32l-47-82q-14-23-8-49t27-43l53-40q-1-7-1-13.5v-27q0-6.5 1-13.5l-53-40q-21-17-27-43t8-49l47-82q14-23 39-32t50 2l62 26q11-8 23-15t24-12l9-66q4-26 23.5-44t46.5-18h94q27 0 46.5 18t23.5 44l9 66q13 5 24.5 12t22.5 15l62-26q25-11 50-2t39 32l47 82q14 23 8 49t-27 43l-53 40q1 7 1 13.5v27q0 6.5-2 13.5l53 40q21 17 27 43t-8 49l-48 82q-14 23-39 32t-50-2l-60-26q-11 8-23 15t-24 12l-9 66q-4 26-23.5 44T527-80h-94Zm7-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/></svg>
                </span>
            </a>

            <button id="addButton" class="btn" type="button" onclick="document.getElementById('addModal').style.display='flex'">
            <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
        </span>
        <span>Add New Item</span>
        </button>
        </div>
    </div>
 </div>

 <div class="container main-content">
    <div class="row table-controls mb-3">
        <div class="searchbox">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </span>

            <input type="text" id="ingredientSearch" class="border searchBar" placeholder="Search ingredients...">
        </div>
        <div class="filters">
                <select id="filter-category" class="ts-filter">
                    <option value="all" selected>All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <select id="filter-stock" class="ts-filter">
                    <option value="all" selected>All Stock</option>
                    <option value="in_stock">In Stock</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>

                <select id="filter-unit" class="ts-filter">
                    <option value="primary">Primary Unit</option>
                    <option value="secondary">Secondary Unit</option>
                </select>

                <select id="sort-items" class="ts-filter">
                    <option value="latest" selected>Latest</option>
                    <option value="qty_desc">High Quantity</option>
                    <option value="qty_asc">Low Quantity</option>
                    <option value="name_asc">Name: A-Z</option>
                    <option value="name_desc">Name: Z-A</option>
                </select>
        </div>
    </div>

    <div class="container table-container border">
        <table role="table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Item Code</th>
                    <th>Category</th>
                    <th>Purchase Price</th>
                    <th>Quantity</th>
                    <th>Last Updated</th>
                </tr>
            </thead>

            <tbody role="rowgroup">
                @foreach($ingredients as $item)
                <tr role="row" class="inventory-row"
                    data-name="{{ strtolower($item->name) }}"
                    data-category="{{ $item->category_id }}"
                    data-qty="{{ $item->stock_quantity }}"
                    data-threshold="{{ $item->alert_threshold ?? 0 }}"
                    data-created="{{ strtotime($item->updated_at) }}"
                    data-base-price="{{ $item->purchase_price }}"
                    data-conv="{{ $item->conversion_factor }}"
                    data-p-abbr="{{ $item->primary_unit_abbr }}"
                    data-s-abbr="{{ $item->secondary_unit_abbr }}">

                    <td data-cell="name" role="cell">
                        <div class="d-flex item-group">
                            <span class="item-img">
                                @empty($item->img_url)
                                <span>{{ $item->name[0] }}</span>
                                @else
                                <img src="{{ $item->img_url }}" alt="Item Image">
                                @endempty
                            </span>

                            <span class="item-data">
                                {{ $item->name }}
                            </span>
                        </div>
                    </td>

                    <td data-cell="code" role="cell">
                        @empty($item->item_code)
                        --
                        @else
                        <span class="item-data">{{ $item->item_code }}</span>
                        @endempty
                    </td>

                    <td data-cell="category" role="cell"><span class="item-data">{{ $item->category_name }}</span></td>
                    <td data-cell="price" role="cell">
                        <span class="item-data format-peso display-price" value="{{ $item->purchase_price }}">{{ $item->purchase_price }} / </span>
                        <span class="item-data display-unit">{{ $item->primary_unit_abbr }}</span>
                    </td>
                    <td data-cell="quantity" role="cell">
                        <div class="d-flex item-group">
                            <span class="item-data display-qty {{$item->stock_quantity < $item->alert_threshold && $item->stock_quantity > 0 ? 'low-stock' : ''}} {{$item->stock_quantity == 0 ? 'out-of-stock' : ''}}">{{ number_format($item->stock_quantity, 2) }} {{ $item->primary_unit_abbr }} </span>
                        </div>                        
                        
                    </td>


                    <td data-cell="last update" role="cell">
                        <div class="d-flex item-group">
                            <span class="item-data">
                                {{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('M d, Y') : '--' }}
                            </span>

                            <div class="dropdown-wrapper">
                                <button type="button" class="more-actions" onclick="toggleDropdown(this)">
                                    <span class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-160q-33 0-56.5-23.5T400-240q0-33 23.5-56.5T480-320q33 0 56.5 23.5T560-240q0 33-23.5 56.5T480-160Zm0-240q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0-240q-33 0-56.5-23.5T400-720q0-33 23.5-56.5T480-800q33 0 56.5 23.5T560-720q0 33-23.5 56.5T480-640Z"/></svg>
                                    </span>
                                </button>

                                <div class="dropdown-menu border" style="display: none;">
                                    <div class="dropdown-section">
                                        <div class="dropdown-header"><p class="text-muted">Adjust Stock</p></div>

                                        <button type="button" class="dropdown-item btn" onclick="openAddStockModal(
                                        {{ $item->id }}, 
                                        '{{ $item->primary_unit_abbr }}', 
                                        '{{ $item->secondary_unit_abbr }}', 
                                        {{ $item->conversion_factor }}, 
                                        {{ $item->stock_quantity }}, 
                                        {{ $item->purchase_price }})">
                                            <span class="icon-wrapper">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-640h338-18 14-334Zm440 0h120-120Zm-424-80h528l-34-40H250l-34 40Zm184 270 80-40 80 40v-190H400v190ZM200-120q-33 0-56.5-23.5T120-200v-499q0-14 4.5-27t13.5-24l50-61q11-14 27.5-21.5T250-840h460q18 0 34.5 7.5T772-811l50 61q9 11 13.5 24t4.5 27v156q0 17-11.5 28.5T800-503q-17 0-28.5-11.5T760-543v-97H640v153q-35 20-61 49.5T538-371l-58-29-102 51q-20 10-39-1.5T320-385v-255H200v440h311q17 0 28.5 11.5T551-160q0 16-11.5 28T511-120H200Zm531.5-11.5Q720-143 720-160v-80h-80q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320h80v-80q0-17 11.5-28.5T760-440q17 0 28.5 11.5T800-400v80h80q17 0 28.5 11.5T920-280q0 17-11.5 28.5T880-240h-80v80q0 17-11.5 28.5T760-120q-17 0-28.5-11.5ZM200-640h338-18 14-334Z"/></svg>
                                            </span>
                                            Add Stock
                                        </button>

                                        <button type="button" class="dropdown-item dropdown-red btn" onclick="openReduceStockModal(
                                        {{ $item->id }}, 
                                        '{{ $item->primary_unit_abbr }}', 
                                        '{{ $item->secondary_unit_abbr }}', 
                                        {{ $item->conversion_factor }}, 
                                        {{ $item->stock_quantity }})">
                                            <span class="icon-wrapper">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-120q-33 0-56.5-23.5T120-200v-499q0-14 4.5-27t13.5-24l50-61q11-14 27.5-21.5T250-840h460q18 0 34.5 7.5T772-811l50 61q9 11 13.5 24t4.5 27v111q0 12-8.5 20t-20.5 9q-25 2-46.5 11T725-520l-85 85v-205H320v255q0 23 19 34.5t39 1.5l102-51 83 42-59 58q-11 11-17.5 26t-6.5 31v83q0 17-11.5 28.5T440-120H200Zm360-40v-66q0-8 3-15.5t9-13.5l209-208q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-340L695-132q-6 6-13.5 9t-15.5 3h-66q-17 0-28.5-11.5T560-160Zm263-184 37-39-37-37-38 38 38 38ZM216-720h528l-34-40H250l-34 40Z"/></svg>
                                            </span>
                                            Reduce Stock
                                        </button>
                                    </div>

                                    <div class="dropdown-section border-t pt-2">
                                        <div class="dropdown-header"><p class="text-muted">Actions</p></div>
                                        
                                        <button type="button" class="dropdown-item btn" onclick="openEditModal(
                                            {{ $item->id }}, 
                                            '{{ addslashes($item->name) }}', 
                                            '{{ $item->item_code ?? '' }}', 
                                            '{{ $item->category_id }}', 
                                            '{{ $item->primary_unit_id }}', 
                                            '{{ $item->secondary_unit_id }}', 
                                            '{{ $item->conversion_factor }}', 
                                            '{{ $item->stock_quantity }}', 
                                            '{{ $item->purchase_price }}', 
                                            '{{ $item->alert_threshold ?? '' }}', 
                                            '{{ addslashes($item->description ?? '') }}',
                                            '{{ $item->img_url ?? ''}}')">
                                            <span class="icon-wrapper">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-120q-33 0-56.5-23.5T120-200v-499q0-14 4.5-27t13.5-24l50-61q11-14 27.5-21.5T250-840h460q18 0 34.5 7.5T772-811l50 61q9 11 13.5 24t4.5 27v111q0 12-8.5 20t-20.5 9q-25 2-46.5 11T725-520l-85 85v-205H320v255q0 23 19 34.5t39 1.5l102-51 83 42-59 58q-11 11-17.5 26t-6.5 31v83q0 17-11.5 28.5T440-120H200Zm360-40v-66q0-8 3-15.5t9-13.5l209-208q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-340L695-132q-6 6-13.5 9t-15.5 3h-66q-17 0-28.5-11.5T560-160Zm263-184 37-39-37-37-38 38 38 38ZM216-720h528l-34-40H250l-34 40Z"/></svg>
                                            </span>
                                            Edit Item
                                        </button>

                                        <button type="button" class="dropdown-item dropdown-red btn" onclick="openDeleteModal({{ $item->id }})">
                                            <span class="icon-wrapper">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v126q0 17-13.5 28t-31.5 8q-8-1-17-1.5t-18-.5q-20 0-40 2.5t-40 8.5v-51q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v90q-24 17-44.5 38.5T440-424v-176q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q0 29 6.5 57.5T424-168q8 17-1.5 32.5T396-120H280Zm258.5-18.5Q480-197 480-280t58.5-141.5Q597-480 680-480t141.5 58.5Q880-363 880-280t-58.5 141.5Q763-80 680-80t-141.5-58.5ZM700-288v-92q0-8-6-14t-14-6q-8 0-14 6t-6 14v91q0 8 3 15.5t9 13.5l60 60q6 6 14 6t14-6q6-6 6-14t-6-14l-60-60Z"/></svg>
                                            </span>
                                            Delete Item
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </td>
                    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
 </div>


 @include('admin.inventory.modals.addModal')

 @include('admin.inventory.modals.addStockModal')

 @include('admin.inventory.modals.reduceStockModal')

 @include('admin.inventory.modals.editModal')
 
 @include('admin.inventory.modals.deleteModal')

@endsection

@once
    @push('styles')
         <link rel="stylesheet" href="{{ asset('css/admin/inventory/inventory.css') }}">
         <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
        <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
        <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/filters.css') }}">
    @endpush
@endonce

@once
    @push('scripts')
        <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}"></script>

        <script type="text/javascript" src="{{ asset('js/dashboard/toggleDropdown.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/dashboard/filters/tsInventoryFilters.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/utils/currency.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/dashboard/toggleTab.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/dashboard/imageUpload.js') }}"></script>

        <script>
            // Injects row data into the Full Edit Modal
            function openEditModal(id, name, itemCode, categoryId, primaryUnitId, secondaryUnitId, conversionFactor, stockQty, purchasePrice, alertThreshold, description, imgUrl) {
                document.getElementById('editForm').action = "{{ url('/admin/inventory') }}/" + id;
                
                // 1. Standard text and number fields
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_item_code').value = itemCode;
                document.getElementById('edit_conversion_factor').value = conversionFactor;
                document.getElementById('edit_stock_qty').value = stockQty;
                document.getElementById('edit_purchase_price').value = purchasePrice;
                document.getElementById('edit_threshold').value = alertThreshold;
                document.getElementById('edit_description').value = description;

                // 2. Tom Select Dropdowns (Category, Primary Unit, Secondary Unit)
                let categorySelect = document.getElementById('edit_category');
                if (categorySelect.tomselect) { categorySelect.tomselect.setValue(categoryId); } 
                else { categorySelect.value = categoryId; } // Fallback if Tom Select fails to load

                let primarySelect = document.getElementById('edit_primary_unit');
                if (primarySelect.tomselect) { primarySelect.tomselect.setValue(primaryUnitId); } 
                else { primarySelect.value = primaryUnitId; }

                let targetOption = primarySelect.querySelector(`option[value="${primaryUnitId}"]`);
                let primaryAbbr = targetOption ? targetOption.getAttribute('data-abbr') : '';

                document.getElementById('edit_opening_stock_unit').innerText = primaryAbbr ? `${primaryAbbr}` : '';

                document.getElementById('edit_purchase_price_unit').innerText = primaryAbbr ? `/ ${primaryAbbr}` : '';

                let secondarySelect = document.getElementById('edit_secondary_unit');
                if (secondarySelect.tomselect) { secondarySelect.tomselect.setValue(secondaryUnitId); } 
                else { secondarySelect.value = secondaryUnitId; }

                //image

                const editUploadBox = document.getElementById('edit-upload-box');
                const editUploadIcon = document.getElementById('edit-upload-icon');

                const oldPreview = document.getElementById('edit-preview-figure');
                if (oldPreview) oldPreview.remove();

                const oldRemoveInput = document.getElementById('remove-image-flag');
                if (oldRemoveInput) oldRemoveInput.remove();
    
                document.getElementById('edit-image').value = '';

                if (imgUrl && imgUrl !== '') {
                    editUploadIcon.style.display = 'none'; // Hide the SVG
                    
                    const figure = document.createElement('figure');
                    figure.className = 'image-preview';
                    figure.id = 'edit-preview-figure';
                    figure.innerHTML = `
                        <img src="${imgUrl}" alt="Preview">
                        <button type="button" class="remove-img-btn" title="Remove image">✕</button>
                    `;
                    editUploadBox.appendChild(figure);
                    
                    figure.querySelector('.remove-img-btn').addEventListener('click', function(e) {
                        e.stopPropagation(); // Stop file browser from opening
                        figure.remove(); // Remove the image
                        editUploadIcon.style.display = 'block'; // Show SVG
                        
                        // Secretly tell Laravel to delete the image from the DB!
                        const removeInput = document.createElement('input');
                        removeInput.type = 'hidden';
                        removeInput.name = 'remove_image';
                        removeInput.value = 'true';
                        removeInput.id = 'remove-image-flag';
                        document.getElementById('editForm').appendChild(removeInput);
                    });
                } else {
                    editUploadIcon.style.display = 'flex';
                }

                // Show the modal
                document.getElementById('editModal').style.display = 'flex';
            }

            // Sets the correct URL for the Delete Modal
            function openDeleteModal(id) {
                document.getElementById('deleteForm').action = "{{ url('/admin/inventory') }}/" + id;
                document.getElementById('deleteModal').style.display = 'flex';
            }

            // Opens the Add Stock Modal (You'll need to set the form actions when you build the backend for this)
            let activeItemContext = {}; 
            function openAddStockModal(id, pUnit, sUnit, convFactor, currentStock, price) {
                activeItemContext = {
                    pUnit,
                    sUnit,
                    convFactor,
                    currentStock,
                    price
                };

                document.getElementById('addStockForm').action = "{{ url('/admin/inventory') }}/" + id + "/add-stock";

                let unitSelect = document.getElementById('add_unit');
                unitSelect.innerHTML = `
                    <option value="primary">${pUnit}</option>
                    <option value="secondary">${sUnit}</option>
                `;

                document.getElementById('add_quantity').value = '';

                document.getElementById('add_new_stock_wrapper').style.display = 'none';

                document.getElementById('add_price').value = price;

                document.getElementById('add_current_stock_display').innerText = `${currentStock} ${pUnit}`;
                document.getElementById('add_new_stock_display').innerText = `${currentStock} ${pUnit}`;

                document.getElementById('addStockModal').style.display = 'flex';
            }

            // Opens the Reduce Stock Modal 
            function openReduceStockModal(id, pUnit, sUnit, convFactor, currentStock) {
                activeItemContext = {
                    pUnit,
                    sUnit,
                    convFactor,
                    currentStock
                };
                document.getElementById('reduceStockForm').action = "{{ url('/admin/inventory') }}/" + id + "/reduce-stock";

                let unitSelect = document.getElementById('reduce_unit');
                unitSelect.innerHTML = `
                    <option value="primary">${pUnit}</option>
                    <option value="secondary">${sUnit}</option>
                `;

                document.getElementById('reduce_quantity').value = '';
                document.getElementById('reduce_remarks').value = '';

                document.getElementById('reduce_new_stock_wrapper').style.display = 'none';

                document.getElementById('reduce_current_stock_display').innerText = `${currentStock} ${pUnit}`;
                document.getElementById('reduce_new_stock_display').innerText = `${currentStock} ${pUnit}`;

                document.getElementById('reduceStockModal').style.display = 'flex';
            }

            function updatePriceUnitDisplay(type){
                if(type !== 'add')
                    return;

                let unitType = document.getElementById('add_unit').value;
                let displaySpan = document.getElementById('add_price_unit_display');
                let priceInput = document.getElementById('add_price');
                
                if (unitType === 'primary') {
                    displaySpan.innerText = `/ ${activeItemContext.pUnit}`;
                    priceInput.value = activeItemContext.price; // Back to base price
                } else {
                    displaySpan.innerText = `/ ${activeItemContext.sUnit}`;
                    // E.g., if price is 100/kg, and factor is 1000g/kg, new price is 0.10/g
                    let convertedPrice = activeItemContext.price / activeItemContext.convFactor;
                    priceInput.value = convertedPrice.toFixed(4); // Keep some decimals for accuracy
                }
            }

            function calculateLiveStock(type) {
                let inputQty = parseFloat(document.getElementById(`${type}_quantity`).value) || 0;
                let wrapper = document.getElementById(`${type}_new_stock_wrapper`);
                if (isNaN(inputQty) || inputQty <= 0) {
                    wrapper.style.display = 'none';
                    return;
                }
                wrapper.style.display = 'inline-block';
                let unitType = document.getElementById(`${type}_unit`).value;

                let actualQtyInPrimary = unitType === 'primary' ? inputQty : (inputQty / activeItemContext.convFactor);

                let newStock = 0;
                if(type === 'add'){
                    newStock = activeItemContext.currentStock + actualQtyInPrimary;
                } else if(type === 'reduce') {
                    newStock = activeItemContext.currentStock - actualQtyInPrimary;
                }

                if(newStock < 0) newStock = 0;

                document.getElementById(`${type}_new_stock_display`).innerText = `${newStock.toFixed(2)} ${activeItemContext.pUnit}`;
            }

            function updateLiveUI(type, isModalOpen = false) {
                let unitType = document.getElementById(`${type}_unit`).value;
                let inputQty = parseFloat(document.getElementById(`${type}_quantity`).value) || 0;
                let wrapper = document.getElementById(`${type}_new_stock_wrapper`);

                let isPrimary = (unitType === 'primary');
                let activeUnitLabel = isPrimary ? activeItemContext.pUnit : activeItemContext.sUnit;
                
                // 1. Calculate Current Stock in the selected unit
                let currentStockDisplayed = isPrimary 
                    ? activeItemContext.currentStock 
                    : (activeItemContext.currentStock * activeItemContext.convFactor);

                document.getElementById(`${type}_current_stock_display`).innerText = `${currentStockDisplayed.toFixed(2)} ${activeUnitLabel}`;

                // 2. Calculate Price in the selected unit (Add Modal Only)
                if (type === 'add') {
                    let currentPrice = isPrimary 
                        ? activeItemContext.price 
                        : (activeItemContext.price / activeItemContext.convFactor);
                    
                    document.getElementById('add_price_unit_display').innerText = `/ ${activeUnitLabel}`;
                    
                    // Only overwrite the input field value if we are just opening the modal OR changing the unit dropdown
                    // We don't want to overwrite it if the user is just typing in the quantity box
                    if (isModalOpen || event.type === 'change') {
                        document.getElementById('add_price').value = currentPrice.toFixed(4);
                    }
                }

                // 3. Calculate New Stock
                if (inputQty <= 0 || isNaN(inputQty)) {
                    wrapper.style.display = 'none';
                    return;
                }
                
                wrapper.style.display = 'inline-block';

                let newStock = 0;
                if (type === 'add') {
                    newStock = currentStockDisplayed + inputQty; // Both are in the same unit now
                } else if (type === 'reduce') {
                    newStock = currentStockDisplayed - inputQty; // Both are in the same unit now
                }

                if (newStock < 0) newStock = 0;
                document.getElementById(`${type}_new_stock_display`).innerText = `${newStock.toFixed(2)} ${activeUnitLabel}`;
            }
            
            // ATTACH THE EVENT LISTENERS
            document.getElementById('add_quantity').addEventListener('input', function() { updateLiveUI('add'); });
            document.getElementById('add_unit').addEventListener('change', function(event) { updateLiveUI('add'); });
            
            document.getElementById('reduce_quantity').addEventListener('input', function() { updateLiveUI('reduce'); });
            document.getElementById('reduce_unit').addEventListener('change', function(event) { updateLiveUI('reduce'); });

            document.getElementById('primary_unit').addEventListener('change', function() {
                let selectedOption = this.options[this.selectedIndex];
                let abbr = selectedOption ? selectedOption.getAttribute('data-abbr') : '';
                document.getElementById('add_opening_stock_unit').innerText = abbr ? `${abbr}` : '';
                document.getElementById('add_purchase_price_unit').innerText = abbr ? `/ ${abbr}` : '';
            });

            // Listen for unit changes in the EDIT Modal
            document.getElementById('edit_primary_unit').addEventListener('change', function() {
                let selectedOption = this.options[this.selectedIndex];
                let abbr = selectedOption ? selectedOption.getAttribute('data-abbr') : '';
                document.getElementById('edit_purchase_price_unit').innerText = abbr ? `/ ${abbr}` : '';
            });
        </script>

        @if(session('error'))
    <script>
        alert("🚨 ERROR: {{ session('error') }}");
    </script>
@endif

@if(session('success'))
    <script>
        alert("✅ SUCCESS: {{ session('success') }}");
    </script>
@endif

@if($errors->any())
    <script>
        let errorMessages = "⚠️ Please fix the following errors:\n\n";
        @foreach ($errors->all() as $error)
            errorMessages += "• {{ $error }}\n";
        @endforeach
        alert(errorMessages);
    </script>
@endif
    @endpush
@endonce