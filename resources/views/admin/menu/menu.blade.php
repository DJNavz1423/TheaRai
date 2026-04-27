@extends('layouts.admin')

@section('title', 'Menu Items')

@section('content')
<div class="container">
    <div class="row mb-3">
        <h1 class="heading">Menu Items ({{ count($menuItems) }})</h1>

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
                <span>Add New Dish</span>
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

            <input type="text" id="menuSearch" class="border searchBar" placeholder="Search products...">
        </div>
        <div class="filters">
            <select id="filter-category" class="ts-filter">
                <option value="all" selected>All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select id="sort-items" class="ts-filter">
                <option value="latest" selected>Latest</option>
                <option value="price_desc">High Price</option>
                <option value="price_asc">Low Price</option>
                <option value="name_asc">Name: A-Z</option>
                <option value="name_desc">Name: Z-A</option>
            </select>
        </div>
    </div>

    <div class="container table-container border">
        <table role="table">
            <thead>
                <tr>
                    <th>Dish Name</th>
                    <th>Category</th>
                    <th>Selling Price</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody role="rowgroup">
                @forelse($menuItems as $dish)
                <tr role="row" class="menu-row" 
                    data-name="{{ strtolower($dish->name) }}"
                    data-category="{{ $dish->category_id ?? 'all' }}"
                    data-price="{{ $dish->final_price }}"
                    data-created="{{ strtotime($dish->created_at ?? now()) }}">
                    <td role="cell" data-cell="name">
                        <div class="d-flex item-group">
                            <span class="item-img">
                                @empty($dish->img_url)
                                <span>{{ $dish->name[0] }}</span>
                                @else
                                <img src="{{ $dish->img_url }}" alt="Dish Image">
                                @endempty
                            </span>
                            <span class="item-data">{{ $dish->name }}</span>
                        </div>
                    </td>

                    <td role="cell" data-cell="category"><span class="item-data">{{ $dish->category_name ?? 'Uncategorized' }}</span></td>
                    
                    <td role="cell" data-cell="price">
                        <span class="item-data">&#8369;{{ number_format($dish->final_price, 2) }}</span>
                    </td>
                    
                    <td role="cell" data-cell="actions">
                        <div class="dropdown-wrapper">
                            <button type="button" class="more-actions" onclick="toggleDropdown(this)">
                            <span class="icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-160q-33 0-56.5-23.5T400-240q0-33 23.5-56.5T480-320q33 0 56.5 23.5T560-240q0 33-23.5 56.5T480-160Zm0-240q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0-240q-33 0-56.5-23.5T400-720q0-33 23.5-56.5T480-800q33 0 56.5 23.5T560-720q0 33-23.5 56.5T480-640Z"/></svg>
                            </span>
                        </button>

                        <div class="dropdown-menu border" style="display: none;">
                            <div class="dropdown-section">
                                <button type="button" class="dropdown-item btn" onclick="openBranchPricingModal({{ $dish->id }}, '{{ addslashes($dish->name) }}', {{ $dish->final_price }})">
                                    <span class="icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M856-390 570-104q-12 12-27 18t-30 6q-15 0-30-6t-27-18L103-457q-11-11-17-25.5T80-513v-287q0-33 23.5-56.5T160-880h287q16 0 31 6.5t26 17.5l352 353q12 12 17.5 27t5.5 30q0 15-5.5 29.5T856-390ZM513-160l286-286-353-354H160v286l353 354ZM260-640q25 0 42.5-17.5T320-700q0-25-17.5-42.5T260-760q-25 0-42.5 17.5T200-700q0 25 17.5 42.5T260-640Zm220 160Z"/></svg>
                                    </span>
                                    Manage Branch Pricing
                                </button>

                                <button type="button" class="dropdown-item btn">
                                    <span class="icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M160-120q-17 0-28.5-11.5T120-160v-97q0-16 6-30.5t17-25.5l505-504q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L313-143q-11 11-25.5 17t-30.5 6h-97Zm544-528 56-56-56-56-56 56 56 56Z"/></svg>
                                    </span>
                                    Edit Item
                                </button>

                                <button type="button" class="dropdown-item btn">
                                    <span class="icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v126q0 17-13.5 28t-31.5 8q-8-1-17-1.5t-18-.5q-20 0-40 2.5t-40 8.5v-51q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v90q-24 17-44.5 38.5T440-424v-176q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q0 29 6.5 57.5T424-168q8 17-1.5 32.5T396-120H280Zm258.5-18.5Q480-197 480-280t58.5-141.5Q597-480 680-480t141.5 58.5Q880-363 880-280t-58.5 141.5Q763-80 680-80t-141.5-58.5ZM700-288v-92q0-8-6-14t-14-6q-8 0-14 6t-6 14v91q0 8 3 15.5t9 13.5l60 60q6 6 14 6t14-6q6-6 6-14t-6-14l-60-60Z"/></svg>
                                    </span>
                                    Delete Item
                                </button>
                            </div>
                        </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-muted" style="text-align: center; padding: 20px;">Menu is empty.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- modal -->

<div id="addModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ url('/admin/menu') }}" method="POST" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h2>Add New Dish</h2>

                <button type="button" class="btn close-btn" onclick="document.getElementById('addModal').style.display='none'">
                    <span class="icon-wrapper close-modal">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
                    </span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <label for="name">Dish Name</label>
                        <input type="text" name="name" id="name" placeholder="e.g., Bulalo" required>
                    </div>

                    <div class="input-group">
                        <label for="category">Category</label>
                        <select name="category_id" id="category" class="unit-selector" placeholder="Select category..." required>
                            <option value="" class="d-none"></option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="branch-select-wrapper">
                        <div class="input-group">
                            <label for="dish_branches">Available In Branches</label>
                            <select name="branch_ids[]" id="dish_branches" placeholder="Select branches..." multiple required>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" selected>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select the branches where this dish will be sold.</small>
                        </div>
                    </div>
                </div>
                
                <div class="tab-container">
                    <div class="row tab-titles mb-3">
                        <h3 class="tab-links active-link underline-fullwidth" onclick="openTab(event, 'recipe')">Recipe & Costing</h3>
                        <h3 class="tab-links underline-fullwidth" onclick="openTab(event, 'others')">Others</h3>
                    </div>
                        
                    <div class="container table-container modal-table border mb-1">
                        <table>
                            <thead>
                                <tr class="border-b">
                                    <th class="border-r">S.N</th>
                                    <th class="border-r">Ingredient Item Name</th>
                                    <th class="border-r">Quantity Per Serve</th>
                                    <th class="border-r">Unit Cost</th>
                                    <th>Cost Per Serve</th>
                                </tr>
                            </thead>

                            <tbody id="recipe-list">
                                <tr class="recipe-row">
                                    <td><span class="serial-number">1</span></td>
                                    <td>
                                        <select name="ingredients[0][ingredient_id]" class="recipe-select" required>
                                            <option value="" class="d-none"></option>
                                            @foreach($ingredients as $ingredient)
                                                <option value="{{ $ingredient->id }}" 
                                                    data-pcost="{{ $ingredient->purchase_price }}" 
                                                    data-scost="{{ $ingredient->s_unit_price }}" 
                                                    data-pabbr="{{ $ingredient->primary_unit_abbr }}" 
                                                    data-sabbr="{{ $ingredient->secondary_unit_abbr }}">
                                                    {{ $ingredient->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="ingredients[0][quantity_used]" class="recipe-qty" step="0.01" min="0" value="" required>

                                            <select name="ingredients[0][unit_type]"  class="unit-toggle">
                                                <option value="" class="d-none">Unit</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="unit-cost-display">₱0.00</span>
                                        <input type="hidden" class="active-unit-cost" value="0">
                                    </td>
                                    <td>
                                        <div>
                                            <span class="line-cost-display">&#8369;0.00</span>
                                            <button type="button" class="remove-row">
                                                <span class="icon-wrapper">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM428.5-291.5Q440-303 440-320v-280q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q17 0 28.5-11.5Zm160 0Q600-303 600-320v-280q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v280q0 17 11.5 28.5T560-280q17 0 28.5-11.5ZM280-720v520-520Z"/></svg>
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot class="border-t">
                                <tr>
                                    <td colspan="3" class="border-r">
                                        <button class="btn" type="button" id="addIngredientBtn">
                                            <span class="icon-wrapper">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
                                            </span>
                                            <span>Add Ingredient</span>
                                        </button>
                                    </td>
                                    <td><span>Sub-Total</span></td>
                                    <td>
                                        <span id="sub-total-display">&#8369;0.00</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="tab-contents active-tab" id="recipe">
                        <div class="row">
                            <div class="input-group">
                                <span for="q-factor">Q-Factor (10%):</span>
                                <data id="q-factor-display" class="format-peso">0.00</data>
                            </div>

                             <div class="input-group">
                                <span>Suggested Price:</span>
                                <data id="suggested-price-display" class="format-peso">0.00</data>
                            </div>
                        </div>

                        <div class="row">
                           <div class="input-group">
                                <label for="margin">Target Margin (%)</label>
                                <input type="number" name="margin" id="margin" required value="30">
                            </div>

                            <div class="input-group">
                                <label for="final-price">Your Price</label>
                                <span class="icon-wrapper currency-symbol">&#8369;</span>
                                <input type="number" name="final_price" id="final-price" required>
                            </div>
                        </div>
                    </div>

                    <div id="others" class="tab-contents">
                        <div class="row">
                            <div class="input-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" placeholder="Enter Description..."></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                            <label for="image">Add Image</label>
                                <div id="upload-box" class="image-upload">
                                    <input type="file" accept="image/png,.png,image/jpeg,.jpeg,image/jpg,.jpg" name="img_url" id="image" style="display: none;">

                                    <span id="upload-icon" class="icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M771.5-691.5Q760-703 760-720v-40h-40q-17 0-28.5-11.5T680-800q0-17 11.5-28.5T720-840h40v-40q0-17 11.5-28.5T800-920q17 0 28.5 11.5T840-880v40h40q17 0 28.5 11.5T920-800q0 17-11.5 28.5T880-760h-40v40q0 17-11.5 28.5T800-680q-17 0-28.5-11.5ZM440-260q75 0 127.5-52.5T620-440q0-75-52.5-127.5T440-620q-75 0-127.5 52.5T260-440q0 75 52.5 127.5T440-260Zm0-80q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29ZM120-120q-33 0-56.5-23.5T40-200v-480q0-33 23.5-56.5T120-760h126l50-54q11-12 26.5-19t32.5-7h205q17 0 28.5 11.5T600-800v60q0 25 17.5 42.5T660-680h20v20q0 25 17.5 42.5T740-600h60q17 0 28.5 11.5T840-560v360q0 33-23.5 56.5T760-120H120Z"/></svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('addModal').style.display='none'">
                    <span>Cancel</span>
                </button>

                <button type="submit" class="btn">
                <span>Add Menu Dish</span>
            </button>
            </div>
        </form>
    </div>
</div>


<div id="manageBranchModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form id="manageBranchForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h2>Manage <span id="manage-dish-name" style="color: var(--primary);"></span></h2>
                <button type="button" class="btn close-btn" onclick="document.getElementById('manageBranchModal').style.display='none'">
                    <span class="icon-wrapper close-modal"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg></span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Global Default Price: <strong class="format-peso" id="manage-global-price"></strong></p>
                <p class="text-muted mb-3" style="font-size: 0.85rem;">Leave a branch price blank to automatically use the Global Default Price. Uncheck the box if the dish is currently unavailable at that location.</p>
                
                <div class="container table-container modal-table border mb-1">
                    <table>
                        <thead>
                            <tr class="border-b">
                                <th class="border-r">Branch Name</th>
                                <th class="border-r" style="width: 150px;">Custom Price Override</th>
                                <th style="width: 100px; text-align: center;">Available?</th>
                            </tr>
                        </thead>
                        <tbody id="branch-pricing-list">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('manageBranchModal').style.display='none'"><span>Cancel</span></button>
                <button type="submit" class="btn"><span>Save Branch Settings</span></button>
            </div>
        </form>
    </div>
</div>
@endsection

@once
  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/menu/menuItems.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/filters.css') }}">
  @endpush
@endonce

@once
  @push('scripts')
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/utils/currency.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/dashboard/toggleTab.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/dashboard/imageUpload.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/dashboard/filters/tsMenuFilter.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/dashboard/toggleDropdown.js') }}"></script>

  <script>
    const ingredientsData = @json($ingredients);

    const branchTs = new TomSelect('#dish_branches', {
        hideSelected: false, 
        closeAfterSelect: false, 
        maxItems: null,
        dropdownClass: 'ts-dropdown branch-dropdown',
        render: {
            item: function(data, escape) {
                return '<div style="display: none;"></div>'; 
            }
        },
        onInitialize: function() {
            updateBranchText(this);

            this.control_input.readOnly = true; 
            this.control_input.style.cursor = 'pointer';
            this.control.style.cursor = 'pointer';
            
            this.dropdown.addEventListener('mousedown', (e) => {
                const option = e.target.closest('.option');
                if (option && option.classList.contains('selected')) {
                    const value = option.dataset.value;
                    this.removeItem(value);
                    this.refreshOptions(false);
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        },
        onChange: function() {
            updateBranchText(this);
        },
        // NEW: Force the text to stay when clicking away
        onBlur: function() {
            updateBranchText(this);
        }
    });

    // Dynamically updates the text inside the input box
    function updateBranchText(ts) {
        const count = ts.items.length;
        let text = 'Select branches...';
        
        if (count === 1) text = '1 branch selected';
        else if (count > 1) text = `${count} branches selected`;
        
        // NEW: Update TomSelect's internal memory so it stops reverting
        ts.settings.placeholder = text; 
        
        // Update the physical input
        ts.control_input.placeholder = text;
        ts.control_input.style.opacity = '1';
        ts.control_input.style.color = 'var(--secondary)';
    }

    let rowCount = 1; 

    const ingredientOptions = ingredientsData.map(i => {
        return `<option value="${i.id}" 
            data-pcost="${i.purchase_price}" 
            data-scost="${i.s_unit_price}" 
            data-pabbr="${i.primary_unit_abbr}" 
            data-sabbr="${i.secondary_unit_abbr}">
            ${i.name}
        </option>`;
    }).join('');

    function attachRowListeners(row) {
        const selectEl = row.querySelector('.recipe-select');
        const unitToggle = row.querySelector('.unit-toggle');
        const qtyInput = row.querySelector('.recipe-qty');
        const activeCostInput = row.querySelector('.active-unit-cost');
        const unitCostDisplay = row.querySelector('.unit-cost-display');
        
        new TomSelect(selectEl, { 
            create: false, 
            maxItems: 1,
            dropdownParent: 'body'
        });

        if (!unitToggle.tomselect) {
            new TomSelect(unitToggle, {
                controlInput: null,
                maxOptions: null,
                placeholder: "Unit",
                dropdownParent: 'body'
            });
        }

        selectEl.tomselect.on('change', function(val) {
            const tsUnit = unitToggle.tomselect;

            if(!val) {
                tsUnit.clear(true);
                tsUnit.clearOptions();
                activeCostInput.value = 0;
                unitCostDisplay.textContent = '₱0.00';
                calculateAll();
                return;
            }

            const originalOption = selectEl.querySelector(`option[value="${val}"]`);
            tsUnit.clear(true);
            tsUnit.clearOptions();

            tsUnit.addOption({
                value: 'secondary',
                text: originalOption.dataset.sabbr,
                cost: originalOption.dataset.scost
            });

            tsUnit.addOption({
                value: 'primary',
                text: originalOption.dataset.pabbr,
                cost: originalOption.dataset.pcost
            });

            tsUnit.refreshOptions(false);
            tsUnit.setValue('secondary', false);
        });

        unitToggle.tomselect.on('change', function(value) {
        const selected = this.options[value];
        if (!selected) return;

        const cost = parseFloat(selected.cost) || 0;

        activeCostInput.value = cost;
        unitCostDisplay.textContent = `₱${cost.toFixed(2)} / ${selected.text}`;

        calculateAll();
    });

        qtyInput.addEventListener('input', calculateAll);

        row.querySelector('.remove-row').addEventListener('click', function() {
            if (document.querySelectorAll('.recipe-row').length > 1) {
                row.remove();
                updateSerialNumbers();
                calculateAll();
            } else {
                alert("You must have at least one ingredient row.");
            }
        });
    }

    function closeOpenDropdowns() {
        document.querySelectorAll('.recipe-select, .unit-toggle').forEach(selectEl => {
            if (selectEl.tomselect && selectEl.tomselect.isOpen) {
                selectEl.tomselect.close(); 
                selectEl.tomselect.blur(); 
            }
        });
    }

    const modalDialog = document.querySelector('#addModal .modal-dialog');
    if (modalDialog) {
        modalDialog.addEventListener('scroll', closeOpenDropdowns, { passive: true });
    }
    
    window.addEventListener('scroll', closeOpenDropdowns, { passive: true });

    document.querySelectorAll('.recipe-row').forEach(row => attachRowListeners(row));

    document.getElementById('addIngredientBtn').addEventListener('click', function() {
        const container = document.getElementById('recipe-list');
        const row = document.createElement('tr');
        row.className = 'recipe-row';
        
        row.innerHTML = `
            <td><span class="serial-number"></span></td>
            <td>
                <select name="ingredients[${rowCount}][ingredient_id]" class="recipe-select" required>
                    <option value="" class="d-none"></option>
                    ${ingredientOptions}
                </select>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" name="ingredients[${rowCount}][quantity_used]" class="recipe-qty" step="0.01" min="0" value="" required>
                    <select name="ingredients[${rowCount}][unit_type]" class="unit-toggle">
                        <option value="" class="d-none">Unit</option>
                    </select>
                </div>
            </td>
            <td>
                <span class="unit-cost-display">₱0.00</span>
                <input type="hidden" class="active-unit-cost" value="0">
            </td>
            <td>
                <div>
                    <span class="line-cost-display">₱0.00</span>
                    <button type="button" class="remove-row">
                        <span class="icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM428.5-291.5Q440-303 440-320v-280q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q17 0 28.5-11.5Zm160 0Q600-303 600-320v-280q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v280q0 17 11.5 28.5T560-280q17 0 28.5-11.5ZM280-720v520-520Z"/></svg>
                        </span>
                    </button>
                </div>
            </td>
        `;
        
        container.appendChild(row);
        attachRowListeners(row);
        updateSerialNumbers();
        rowCount++;
    });

    function updateSerialNumbers() {
        document.querySelectorAll('.recipe-row').forEach((row, index) => {
            row.querySelector('.serial-number').textContent = index + 1;
        });
    }

    function calculateAll() {
        let subTotal = 0;

        document.querySelectorAll('.recipe-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.recipe-qty').value) || 0;
            const unitCost = parseFloat(row.querySelector('.active-unit-cost').value) || 0;
            
            const lineCost = qty * unitCost;
            subTotal += lineCost;
            
            row.querySelector('.line-cost-display').textContent = '₱' + lineCost.toFixed(2);
        });

        const qFactor = subTotal * 0.10; 
        const totalCost = subTotal + qFactor;
        const marginPct = parseFloat(document.getElementById('margin').value) || 0;
        
        let suggested = 0;
        if (marginPct < 100 && totalCost > 0) {
            suggested = totalCost / (1 - (marginPct / 100));
        }

        document.getElementById('sub-total-display').textContent = '₱' + subTotal.toFixed(2);
        document.getElementById('q-factor-display').textContent = '₱' + qFactor.toFixed(2);
        document.getElementById('suggested-price-display').textContent = '₱' + suggested.toFixed(2);
    }

    document.getElementById('margin').addEventListener('input', calculateAll);
  </script>

  <script>
    function openBranchPricingModal(dishId, dishName, globalPrice) {
        document.getElementById('manage-dish-name').innerText = dishName;
        document.getElementById('manage-global-price').innerText = '₱' + parseFloat(globalPrice).toFixed(2);
        document.getElementById('manageBranchForm').action = `/admin/menu/${dishId}/branch-pricing`;
        
        const tbody = document.getElementById('branch-pricing-list');
        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center;">Loading branches...</td></tr>';
        
        document.getElementById('manageBranchModal').style.display = 'flex';

        // Fetch current branch pivot data from the database
        fetch("{{ url('/admin/menu') }}/" + dishId + "/branch-pricing")
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-muted" style="text-align: center;">This dish is not assigned to any branches yet.</td></tr>';
                    return;
                }

                data.forEach((branch, index) => {
                    const priceValue = branch.branch_price !== null ? branch.branch_price : '';
                    const isChecked = branch.is_available ? 'checked' : '';
                    
                    tbody.innerHTML += `
                        <tr class="border-b">
                            <td class="border-r">
                                <strong>${branch.name}</strong>
                                <input type="hidden" name="branch_data[${index}][branch_id]" value="${branch.branch_id}">
                            </td>
                            <td class="border-r">
                                <div class="input-group" style="margin: 0;">
                                    <input type="number" name="branch_data[${index}][branch_price]" step="0.01" min="0" value="${priceValue}" placeholder="Uses Global" style="padding: 5px; width: 100%;">
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <input type="checkbox" name="branch_data[${index}][is_available]" ${isChecked} style="width: 20px; height: 20px; cursor: pointer;">
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(error => {
                tbody.innerHTML = '<tr><td colspan="3" class="text-danger" style="text-align: center;">Error loading branch data.</td></tr>';
            });
    }
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
@endpush
@endonce