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
                    <option value="{{ $branch->id }}" data-cash="{{ $branch->available_cash }}">{{ $branch->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="input-group">
              <label for="reg-amount">Amount</label>
              <input type="text" inputmode="decimal" pattern="[0-9]*(\.[0-9]+)?" id="reg-amount" name="total_amount" step="0.01" required placeholder="Enter amount...">
              <span class="icon-wrapper unit-abbr" id="reg-cash-display">/₱0.00</span>
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

  <!-- ====================================== -->

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
                    <option value="{{ $branch->id }}" data-cash="{{ $branch->available_cash }}">{{ $branch->name }}</option>
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

              <tfoot class="border-t">
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
                    <div class="row">
                      <span id="restock-grand-total" class="format-peso">0</span>
                      <input type="hidden" name="total_amount" id="hidden-grand-total">
                      <span class="icon-wrapper unit-abbr" id="restock-cash-display" style="display:none;">/₱0.00</span>
                    </div>
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