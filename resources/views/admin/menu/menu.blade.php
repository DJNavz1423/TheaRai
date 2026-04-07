@extends('layouts.admin')

@section('title', 'Menu Items')

@section('content')
<div class="container">
    <div class="row mb-4">
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

<div class="container mb-3">
    <div class="row table-controls mb-3">
        <div class="searchbox">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </span>

            <input type="text" id="menuSearch" class="border searchBar" placeholder="Search products...">
        </div>
        <div class="filters"></div>
    </div>

    <div class="container table-container border">
        <table role="table">
            <thead>
                <tr>
                    <th>Dish Name</th>
                    <th>Category</th>
                    <th>Selling Price</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody role="rowgroup">
                @foreach($menuItems as $dish)
                <tr role="row">
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
                    
                    <td role="cell" data-cell="actions" class="text-end">
                        <button class="more-actions">
                            <span class="icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-160q-33 0-56.5-23.5T400-240q0-33 23.5-56.5T480-320q33 0 56.5 23.5T560-240q0 33-23.5 56.5T480-160Zm0-240q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0-240q-33 0-56.5-23.5T400-720q0-33 23.5-56.5T480-800q33 0 56.5 23.5T560-720q0 33-23.5 56.5T480-640Z"/></svg>
                            </span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- modal -->

<div id="addModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form action="" method="POST" class="modal-content">
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
                        <select name="category_id" id="category" class="unit-selector" placeholder="Select category...">
                            <option value="" class="d-none"></option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="container tab-container">
                    <div class="row tab-titles mt-4 mb-1">
                        <h3>Recipe & Costing</h3>
                        <h3>Others</h3>
                    </div>
                        
                    <div class="container table-container modal-table border">
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
                                        <div>
                                            <input type="number" name="ingredients[0][quantity_used]" class="recipe-qty" step="0.01" min="0" value="1" required>

                                            <select name="ingredients[0][unit_type]"  class="unit-toggle">
                                                <option value="" class="d-none">Unit</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="unit-cost-display"></span>
                                        <input type="hidden" class="active-unit-cost" value="0">
                                    </td>
                                    <td>
                                        <div>
                                            <span class="line-cost-display">&#8369;0.00</span>
                                            <button class="remove-row">
                                                <span class="icon-wrapper">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM428.5-291.5Q440-303 440-320v-280q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q17 0 28.5-11.5Zm160 0Q600-303 600-320v-280q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v280q0 17 11.5 28.5T560-280q17 0 28.5-11.5ZM280-720v520-520Z"/></svg>
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot>
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
                                <span for="q-factor">Q-Factor (10%)</span>
                                <span id="q-factor-display">&#8369;0.00</span>
                            </div>

                            <div class="input-group">
                                <label for="margin">Target Margin (%)</label>
                                <input type="number" name="margin" id="margin" required value="30">
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <span>Suggested Price</span>
                                <span id="suggested-price-display">&#8369;0.00</span>
                            </div>

                            <div class="input-group">
                                <label for="final-price">Your Price</label>
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
                                <div class="image-upload">
                                    <input type="file" accept="image/png,.png,image/jpeg,.jpeg,image/jpg,.jpg" multiple name="img_url" id="image" class="d-none">

                                    <span class="icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440ZM120-120q-33 0-56.5-23.5T40-200v-480q0-33 23.5-56.5T120-760h126l50-54q11-12 26.5-19t32.5-7h165q17 0 28.5 11.5T560-800q0 17-11.5 28.5T520-760H355l-73 80H120v480h640v-320q0-17 11.5-28.5T800-560q17 0 28.5 11.5T840-520v320q0 33-23.5 56.5T760-120H120Zm640-640h-40q-17 0-28.5-11.5T680-800q0-17 11.5-28.5T720-840h40v-40q0-17 11.5-28.5T800-920q17 0 28.5 11.5T840-880v40h40q17 0 28.5 11.5T920-800q0 17-11.5 28.5T880-760h-40v40q0 17-11.5 28.5T800-680q-17 0-28.5-11.5T760-720v-40ZM440-260q75 0 127.5-52.5T620-440q0-75-52.5-127.5T440-620q-75 0-127.5 52.5T260-440q0 75 52.5 127.5T440-260Zm0-80q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29Z"/></svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn">
                <span>Add Menu Dish</span>
            </button>
            </div>
        </form>
    </div>
</div>
@endsection

@once
  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/menu/menuItems.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/tableControls.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/modal.css') }}">
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
    const ingredientsData = @json($ingredients);
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
        // Grab all our elements for this specific row
        const selectEl = row.querySelector('.recipe-select');
        const unitToggle = row.querySelector('.unit-toggle');
        const qtyInput = row.querySelector('.recipe-qty');
        const activeCostInput = row.querySelector('.active-unit-cost');
        const unitCostDisplay = row.querySelector('.unit-cost-display');
        
        // Initialize TomSelect on the ingredient dropdown
        new TomSelect(selectEl, { create: false, maxItems: 1, dropdownParent: 'body' });

        // When the Ingredient changes...
        selectEl.tomselect.on('change', function(val) {
            // Reset if they clear the dropdown
            if(!val) {
                unitToggle.innerHTML = '<option value="" class="d-none">Unit</option>';
                activeCostInput.value = 0;
                unitCostDisplay.textContent = '₱0.00';
                calculateAll();
                return;
            }

            // THE FIX: We must query the original HTML element to get the data attributes
            const originalOption = selectEl.querySelector(`option[value="${val}"]`);
            
            // Now we can safely read the dataset and build the unit dropdown
            unitToggle.innerHTML = `
                <option value="secondary" data-cost="${originalOption.dataset.scost}">${originalOption.dataset.sabbr}</option>
                <option value="primary" data-cost="${originalOption.dataset.pcost}">${originalOption.dataset.pabbr}</option>
            `;
            
            // Tell the unit dropdown to update the costs
            unitToggle.dispatchEvent(new Event('change'));
        });

        // When Unit toggles, grab the cost directly from the dataset
        unitToggle.addEventListener('change', function() {
            // This is a standard HTML select, so this works normally
            const selectedOpt = this.options[this.selectedIndex];
            if(!selectedOpt || !selectedOpt.value) return; 
            
            const cost = parseFloat(selectedOpt.getAttribute('data-cost')) || 0;
            activeCostInput.value = cost;
            unitCostDisplay.textContent = `₱${cost.toFixed(2)} / ${selectedOpt.text}`;
            calculateAll();
        });

        // Recalculate if quantity is typed
        qtyInput.addEventListener('input', calculateAll);

        // Handle deleting the row
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

    // Initialize the hardcoded row
    document.querySelectorAll('.recipe-row').forEach(row => attachRowListeners(row));

    // Add Row logic
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
                <div>
                    <input type="number" name="ingredients[${rowCount}][quantity_used]" class="recipe-qty" step="0.01" min="0" value="1" required>
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

    // The Core Math Engine
    function calculateAll() {
        let subTotal = 0;

        document.querySelectorAll('.recipe-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.recipe-qty').value) || 0;
            // Uses the DB cost saved in the hidden input
            const unitCost = parseFloat(row.querySelector('.active-unit-cost').value) || 0;
            
            // Calculate Cost Per Serve
            const lineCost = qty * unitCost;
            subTotal += lineCost;
            
            row.querySelector('.line-cost-display').textContent = '₱' + lineCost.toFixed(2);
        });

        // 10% Q-Factor
        const qFactor = subTotal * 0.10; 
        const totalCost = subTotal + qFactor;
        const marginPct = parseFloat(document.getElementById('margin').value) || 0;
        
        let suggested = 0;
        if (marginPct < 100 && totalCost > 0) {
            suggested = totalCost / (1 - (marginPct / 100));
        }

        // Update spans
        document.getElementById('sub-total-display').textContent = '₱' + subTotal.toFixed(2);
        document.getElementById('q-factor-display').textContent = '₱' + qFactor.toFixed(2);
        document.getElementById('suggested-price-display').textContent = '₱' + suggested.toFixed(2);
    }

    document.getElementById('margin').addEventListener('input', calculateAll);
        </script>
  @endpush
@endonce