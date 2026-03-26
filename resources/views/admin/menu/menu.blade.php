@extends('layouts.admin')

@section('title', 'Menu Items')

@section('content')
  <section id="menu-items">
    <div class="container">
      <div class="row mb-4">
          <h1 class="heading">Menu Items ({{ count($menuItems) }})</h1>

          <div class="row heading-btn-row">
              <a href="#" class="btn">
                  <span class="icon-wrapper">
                      <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M433-80q-27 0-46.5-18T363-142l-9-66q-13-5-24.5-12T307-235l-62 26q-25 11-50 2t-39-32l-47-82q-14-23-8-49t27-43l53-40q-1-7-1-13.5v-27q0-6.5 1-13.5l-53-40q-21-17-27-43t8-49l47-82q14-23 39-32t50 2l62 26q11-8 23-15t24-12l9-66q4-26 23.5-44t46.5-18h94q27 0 46.5 18t23.5 44l9 66q13 5 24.5 12t22.5 15l62-26q25-11 50-2t39 32l47 82q14 23 8 49t-27 43l-53 40q1 7 1 13.5v27q0 6.5-2 13.5l53 40q21 17 27 43t-8 49l-48 82q-14 23-39 32t-50-2l-60-26q-11 8-23 15t-24 12l-9 66q-4 26-23.5 44T527-80h-94Zm7-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/></svg>
                  </span>
              </a>

              <button id="addButton" class="btn" type="button" onclick="document.getElementById('addMenuModal').style.display='block'">
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
        </div>

        <div class="container table-container border">
            <table>
                <thead>
                    <tr>
                        <th>Dish Name</th>
                        <th>Category</th>
                        <th>Selling Price</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($menuItems as $dish)
                    <tr>
                        <td>
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

                        <td><span class="item-data">{{ $dish->category_name ?? 'Uncategorized' }}</span></td>
                        
                        <td>
                            <span class="item-data text-success fw-bold">&#8369;{{ number_format($dish->final_price, 2) }}</span>
                        </td>
                        
                        <td class="text-end">
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

    <div id="addModal" class="modal">
        <div class="modal-dialog">
            <form action="" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h2>Add New Dish</h2>

                    <span class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
                    </span>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="input-group">
                            <label for="name">Dish Name</label>
                            <input type="text" name="name" id="name" placeholder="e.g., Chicken Adobo" required>
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
                    
                    <div class="row">
                        <div class="row table-heading"><h3>Recipe & Costing</h3></div>
                        <div class="container table-container modal-table border">
                            <table>
                                <thead>
                                    <tr class="border-b">
                                        <th class="border-r">S.N</th>
                                        <th class="border-r">Ingredient Name</th>
                                        <th class="border-r">Quantity Per Serve</th>
                                        <th class="border-r">Unit Cost</th>
                                        <th>Cost Per Serve</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td colspan="3" class="border-r">
                                            <button class="btn">
                                                <span class="icon-wrapper">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
                                                </span>

                                                <span>Add Ingredient</span>
                                            </button>
                                        </td>
                                        <td><span>Sub-Total</span></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row">
                            <div class="input-group">
                                <label for="q-factor">Q-Factor (10%)</label>
                                <input type="number" name="q_factor" id="q-factor" required readonly>
                            </div>

                            <div class="input-group">
                                <label for="margin">Target Margin (%)</label>
                                <input type="number" name="margin" id="margin" required value="">
                            </div>

                            <div class="input-group">
                                <label for="calculated-price">Suggested Price</label>
                                <input type="number" name="calculated_price" id="calculated-price" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <label for="final-price">Your Price</label>
                                <input type="number" name="final_price" id="final-price" required>
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
  </section>
@endsection

@once
  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/menu/menuItems.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/tableControls.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
  @endpush
@endonce

@once
  @push('scripts')
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}"></script>
  <script>
            // 1. Initialize standard TomSelect for the Category dropdown
            new TomSelect('#category', { create: false, maxItems: 1 });

            // 2. Load the ingredients from your database into Javascript
            const ingredientsData = @json($ingredients);
            let rowCount = 0;

            // 3. Logic to add a new Recipe Row
            document.getElementById('addIngredientBtn').addEventListener('click', function() {
                const container = document.getElementById('recipe-list');
                
                // Build the HTML for the new row
                const row = document.createElement('div');
                row.className = 'row mb-2 d-flex align-items-end recipe-row';
                row.innerHTML = `
                    <div class="input-group flex-grow-1 me-2">
                        <label>Ingredient</label>
                        <select name="ingredients[${rowCount}][ingredient_id]" class="recipe-select" required>
                            <option value="">Select...</option>
                            ${ingredientsData.map(i => `<option value="${i.id}" data-cost="${i.s_unit_price}">${i.name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="input-group" style="width: 120px;">
                        <label>Qty Used</label>
                        <input type="number" name="ingredients[${rowCount}][quantity_used]" class="recipe-qty" step="0.01" required>
                    </div>
                    <div class="input-group ms-2" style="width: 120px;">
                        <label>Cost</label>
                        <input type="text" class="line-cost" readonly style="background:#eee;">
                    </div>
                    <button type="button" class="btn btn-sm btn-danger ms-2 remove-row" style="height: 40px;">X</button>
                `;
                
                container.appendChild(row);
                
                // Initialize TomSelect for this specific new dropdown
                const selectEl = row.querySelector('.recipe-select');
                new TomSelect(selectEl, { create: false, maxItems: 1 });

                // Add Event Listeners for Live Math
                selectEl.tomselect.on('change', calculateAll);
                row.querySelector('.recipe-qty').addEventListener('input', calculateAll);
                row.querySelector('.remove-row').addEventListener('click', function() {
                    row.remove();
                    calculateAll();
                });

                rowCount++;
            });

            // 4. The Live Math Engine
            function calculateAll() {
                let totalCost = 0;

                // Loop through every recipe row
                document.querySelectorAll('.recipe-row').forEach(row => {
                    const select = row.querySelector('.recipe-select');
                    const qty = parseFloat(row.querySelector('.recipe-qty').value) || 0;
                    
                    if (select.value) {
                        // Find the cost per unit from the <option> data attribute
                        const selectedOption = select.options[select.selectedIndex];
                        const costPerUnit = parseFloat(selectedOption.getAttribute('data-cost')) || 0;
                        
                        const lineCost = costPerUnit * qty;
                        totalCost += lineCost;
                        row.querySelector('.line-cost').value = '₱' + lineCost.toFixed(2);
                    }
                });

                // Update Total Cost
                document.getElementById('calc-total-cost').value = '₱' + totalCost.toFixed(2);

                // Update Suggested Price
                const margin = parseFloat(document.getElementById('calc-margin').value) || 0;
                let suggested = 0;
                if (margin < 100 && totalCost > 0) {
                    suggested = totalCost / (1 - (margin / 100));
                }
                document.getElementById('calc-suggested').value = '₱' + suggested.toFixed(2);
            }

            // Listen for margin changes
            document.getElementById('calc-margin').addEventListener('input', calculateAll);
        </script>
  @endpush
@endonce