@extends('layouts.admin')

@section('title', 'Expenses')

@section('content')
  <div class="container">
    <div class="row mb-3">
      <h1 class="heading">Expenses & Restocks</h1>

      <div class="row heading-btn-row">
        <div class="dropdown-wrapper pos-relative">
          <button id="addBtn" class="btn" type="button" onclick="toggleDropdown(this)">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
            </span>
            <span>Add New Expense</span>
          </button>

          <div id="expenseDropdown" class="dropdown-menu border" style="display: none;">
            <div class="dropdown-section">
              <button type="button" class="dropdown-item btn" onclick="openModal('regularExpenseModal')">
              <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm400 240H120q-33 0-56.5-23.5T40-240v-400q0-17 11.5-28.5T80-680q17 0 28.5 11.5T120-640v400h640q17 0 28.5 11.5T800-200q0 17-11.5 28.5T760-160ZM280-400v-320 320Z"/></svg>
              </span>
              Regular Expense
            </button>

            <button type="button" class="dropdown-item btn" onclick="openModal('restockModal')">
              <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-640h338-18 14-334Zm440 0h120-120Zm-424-80h528l-34-40H250l-34 40Zm184 270 80-40 80 40v-190H400v190ZM200-120q-33 0-56.5-23.5T120-200v-499q0-14 4.5-27t13.5-24l50-61q11-14 27.5-21.5T250-840h460q18 0 34.5 7.5T772-811l50 61q9 11 13.5 24t4.5 27v156q0 17-11.5 28.5T800-503q-17 0-28.5-11.5T760-543v-97H640v153q-35 20-61 49.5T538-371l-58-29-102 51q-20 10-39-1.5T320-385v-255H200v440h311q17 0 28.5 11.5T551-160q0 16-11.5 28T511-120H200Zm531.5-11.5Q720-143 720-160v-80h-80q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320h80v-80q0-17 11.5-28.5T760-440q17 0 28.5 11.5T800-400v80h80q17 0 28.5 11.5T920-280q0 17-11.5 28.5T880-240h-80v80q0 17-11.5 28.5T760-120q-17 0-28.5-11.5ZM200-640h338-18 14-334Z"/></svg>
              </span>
              Restock Ingredients
            </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container main-content">
    <div class="row table-controls mb-3">
      <div class="searchbox">
        <span class="icon-wrapper">
           <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
        </span>

        <input type="text" id="expenseSearch" class="border searchBar" placeholder="Search expenses...">
      </div>

      <div class="filters">
        <select id="filter-type" class="ts-filter">
          <option value="all" selected>All Types</option>
          <option value="regular">Regular</option>
          <option value="restock">Restock</option>
          <option value="ingredient_purchase">Ingredient Purchase</option>
        </select>

        <select id="filter-source" class="ts-filter">
          <option value="all" selected>All Sources</option>
          <option value="cash_in_hand">System Cash</option>
          <option value="external_cash">External Cash</option>
        </select>

        <select id="sort-items" class="ts-filter">
          <option value="latest" selected>Latest</option>
          <option value="amount_desc">High Amount</option>
          <option value="amount_asc">Low Amount</option>
          <option value="desc_asc">Description: A-Z</option>
          <option value="desc_desc">Description: Z-A</option>
        </select>
      </div>
    </div>

    <div class="container table-container border">
      <table role="table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Branch</th>
            <th>Type</th>
            <th>Source</th>
            <th>Description</th>
            <th>Amount</th>
          </tr>
        </thead>

        <tbody role="rowgroup">
          @forelse($expenses ?? [] as $expense)  
          <tr role="row" class="expense-row"
            data-type="{{ $expense->expense_type }}"
            data-source="{{ $expense->fund_source }}"
            data-amount="{{ $expense->total_amount }}"
            data-desc="{{ strtolower($expense->description) }}"
            data-created="{{ strtotime($expense->created_at) }}">
            <td role="cell" data-cell="date">{{ \Carbon\Carbon::parse($expense->created_at)->format('M d, Y') }}</td>
            <td role="cell" data-cell="branch"><span style="font-weight: 500; color: var(--primary);">{{ $expense->branch_name ?? 'Global' }}</span></td>
            <td role="cell" data-cell="type"><span>{{ ucfirst(str_replace('_', ' ', $expense->expense_type)) }}</span></td>
            <td role="cell" data-cell="source"><span>{{ ucfirst(str_replace('_', ' ', $expense->fund_source)) }}</span></td>
            <td role="cell" data-cell="description"><span>{{ $expense->description }}</span></td>
            <td role="cell" data-cell="amount" class="format-peso" value="{{ $expense->total_amount }}">{{ number_format($expense->total_amount, 2) }}</td>
          </tr>
          @empty
                <tr>
                    <td colspan="4" class="text-muted" style="text-align: center; padding: 20px;">Expenses is empty.</td>
                </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div id="regularExpenseModal" class="modal" style="display: none;">
    <div class="modal-dialog">
      <form action="{{ url('/admin/expenses/regular') }}" method="POST" class="modal-content">
        @csrf
        <input type="hidden" name="expense_type" value="regular">
        <input type="hidden" name="fund_source" value="cash_in_hand">

        <div class="modal-header">
          <h2>Add Regular Expense</h2>
          <button type="button" class="btn close-btn" onclick="closeModal('regularExpenseModal')">
            <span class="icon-wrapper close-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
          </span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="input-group">
              <label for="reg-branch">Target Branch</label>
              <select name="branch_id" id="reg-branch" class="unit-selector" required>
                <option value="" disabled selected>Select Branch...</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="input-group">
              <label for="reg-amount">Amount</label>
              <input type="number" id="reg-amount" name="total_amount" step="0.01" required placeholder="Enter amount...">
            </div>
          </div>

          <div class="row">
            <div class="input-group">
              <label for="reg-desc">Description</label>
              <textarea name="description" id="reg-desc" required placeholder="e.g., Electric Bill, Rent, Repair, etc."></textarea>
            </div>
          </div>

          <div class="row">
            <small class="text-muted">Note: This amount will be deducted directly from the selected branch's system cash.</small>
          </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn">Save Expense</button>
        </div>
      </form>
    </div>
  </div>


  <div id="restockModal" class="modal" style="display: none;">
    <div class="modal-dialog">
      <form action="{{ url('/admin/expenses/restock') }}" method="POST" class="modal-content">
        @csrf
        <input type="hidden" name="expense_type" value="restock">

        <div class="modal-header">
          <h2>Restock Ingredients</h2>
          
          <button type="button" class="btn close-btn" onclick="closeModal('restockModal')">
            <span class="icon-wrapper close-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
            </span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="input-group">
              <label for="restock-branch">Target Branch (Where is stock going?)</label>
              <select name="branch_id" id="restock-branch" class="unit-selector" required>
                <option value="" disabled selected>Select Branch...</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
              </select>
            </div>
            
            <div class="input-group">
              <label for="fund-source">Payment Source</label>
              <select name="fund_source" id="fund-source" class="unit-selector" required>
                <option value="external_cash">External Cash</option>
                <option value="cash_in_hand">System Cash (Deducted from Branch)</option>
              </select>
            </div>
          </div>
          
          <div class="row">
            <div class="input-group">
              <label for="restock-desc">Batch Note (Optional)</label>
              <input name="description" id="restock-desc" placeholder="e.g., Weekly Market Run">
            </div>
          </div>

          <div class="container table-container modal-table border">
            <table>
              <thead>
                <tr class="border-b">
                  <th class="border-r">S.N</th>
                  <th class="border-r">Ingredient Name</th>
                  <th class="border-r">Qty Purchased</th>
                  <th class="border-r">Unit Cost</th>
                  <th>Total Line Cost</th>
                </tr>
              </thead>

              <tbody id="restock-list"></tbody>

              <tfoot>
                <tr>
                  <td colspan="3" class="border-r">
                    <button type="button" id="addRestockRowBtn" class="btn">
                      <span class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
                      </span>
                      <span>Add Item</span>
                    </button>
                  </td>

                  <td><span>Grand Total</span></td>
                  <td>
                    <span id="restock-grand-total" class="format-peso">0</span>
                    <input type="hidden" name="total_amount" id="hidden-grand-total">
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn" onclick="document.getElementById('restockModal').style.display='none'">
                    <span>Cancel</span>
          </button>

          <button class="btn" type="submit">Process Restock</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@once
    @push('styles')
      <link rel="stylesheet" href="{{ asset('css/admin/expenses/expenses.css') }}">
      <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
      <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
      <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
      <link rel="stylesheet" href="{{ asset('css/admin/modal.css') }}">
      <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
      <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
      <link rel="stylesheet" href="{{ asset('css/admin/filters.css') }}">
    @endpush
@endonce

@once
    @push('scripts')
        <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/utils/currency.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/dashboard/toggleDropdown.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/dashboard/filters/tsExpensesFilter.js') }}"></script>
        
        <script>
          // modal
         function openModal(id){
          document.getElementById(id).style.display = 'flex';
          document.getElementById('expenseDropdown').style.display = 'none';
          
          if(id === 'restockModal' && document.querySelectorAll('.restock-row').length === 0){
            document.getElementById('addRestockRowBtn').click(); 
          }
         }

         function closeModal(id){
          document.getElementById(id).style.display = 'none';
         }

         // restock dynamic table
         const ingredientsData = @json($ingredients ?? []);
         let restockRowCount = 0;

         const ingredientOptions = ingredientsData.map(i => {
          return `<option value="${i.id}"
            data-pcost="${i.purchase_price}"
            data-pabbr="${i.primary_unit_abbr}">
            ${i.name}</option>`;
         }).join('');

         function attachRestockListeners(row){
          const selectEl = row.querySelector('.restock-select');
          const qtyInput = row.querySelector('.restock-qty');
          const activeCostInput = row.querySelector('.active-unit-cost');
          const unitCostDisplay = row.querySelector('.unit-cost-display');

          new TomSelect(selectEl, {
            create: false,
            maxItems: 1,
            dropdownParent: 'body'
          });

          selectEl.tomselect.on('change', function(val){
            if(!val){
              row.querySelector('.unit-label').textContent = 'Unit';
              activeCostInput.value = 0;
              unitCostDisplay.textContent = '₱0.00';
              calculateRestock();
              return;
            }

            const originalOption = selectEl.querySelector(`option[value="${val}"]`);

            row.querySelector('.unit-label').textContent = originalOption.dataset.pabbr;
            const cost = parseFloat(originalOption.dataset.pcost) || 0;

            activeCostInput.value = cost;
            unitCostDisplay.textContent = window.formatPeso.format(cost)
            calculateRestock();
          });

          qtyInput.addEventListener('input', calculateRestock);

          row.querySelector('.remove-row-btn').addEventListener('click', function(){
            row.remove();
            updateRestockSerialNumbers();
            calculateRestock();
          });
         }

         document.getElementById('addRestockRowBtn').addEventListener('click', function(){
          const container = document.getElementById('restock-list');
          const row = document.createElement('tr');
          row.className = 'restock-row';

          row.innerHTML = `
            <td><span class="serial-number"></span></td>
            <td>
              <select name="items[${restockRowCount}][ingredient_id]" class="restock-select" required>
                <option value="" class="d-none"></option>
                ${ingredientOptions}
              </select>  
            </td>
            <td>
              <div>
                <input type="number" name="items[${restockRowCount}][quantity]" class="restock-qty" step="0.01" min="0.01" value="1" required>
                <span class="unit-label text-muted">Unit</span>  
              </div>  
            </td>
            <td>
              <span class="unit-cost-display">₱0.00</span>
              <input type="hidden" class="active-unit-cost" value="0">  
            </td>
            <td>
              <div>
                <span class="line-cost-display">₱0.00</span>  
                <button type="button" class="remove-row-btn btn" style="padding: 4px; background: none; border: none; cursor: pointer;">
                    <span class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ff4d4f"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM428.5-291.5Q440-303 440-320v-280q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q17 0 28.5-11.5Zm160 0Q600-303 600-320v-280q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v280q0 17 11.5 28.5T560-280q17 0 28.5-11.5ZM280-720v520-520Z"/></svg>
                      </span>
                  </button>
              </div>  
            </td>
          `;

          container.appendChild(row);
          attachRestockListeners(row);
          updateRestockSerialNumbers();
          restockRowCount++;
         });

         function updateRestockSerialNumbers(){
          document.querySelectorAll('.restock-row').forEach((row, index) => {
            row.querySelector('.serial-number').textContent = index + 1;
          });
         }

         function calculateRestock(){
          let grandTotal = 0;

          document.querySelectorAll('.restock-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.restock-qty').value) ||0;
            const unitCost = parseFloat(row.querySelector('.active-unit-cost').value) || 0;

            const lineCost = qty * unitCost;
            grandTotal += lineCost;

            row.querySelector('.line-cost-display').textContent = window.formatPeso.format(lineCost);
          });

          document.getElementById('restock-grand-total').textContent = window.formatPeso.format(grandTotal);
          document.getElementById('hidden-grand-total').value = grandTotal;
         }
        </script>

  @if(session('error'))
      <script>alert("🚨 ERROR: {{ session('error') }}");</script>
  @endif

  @if(session('success'))
      <script>alert("✅ SUCCESS: {{ session('success') }}");</script>
  @endif

  @if($errors->any())
      <script>
          let errorMsgs = "⚠️ Please fix these errors:\n";
          @foreach ($errors->all() as $error)
              errorMsgs += "• {{ $error }}\n";
          @endforeach
          alert(errorMsgs);
      </script>
  @endif
  
    @endpush
@endonce