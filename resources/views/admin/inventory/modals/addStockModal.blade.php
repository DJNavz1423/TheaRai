<div id="addStockModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form id="addStockForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h2>Add Stock</h2>

                <button type="button" class="btn close-btn" onclick="document.getElementById('addStockModal').style.display='none'">
                    <span class="icon-wrapper close-modal">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
                    </span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <label for="add_stock_branch">Target Branch</label>
                        <select name="branch_id" id="add_stock_branch" class="unit-selector" required>
                            <option value="" disabled selected>Which branch is receiving this stock?</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="input-group">
                        <label for="add_quantity">Quantity To Add</label>
                        <div>
                            <input type="number" name="quantity" id="add_quantity" step="0.01" min="0.01" required placeholder="Enter quantity" oninput="calculateLiveStock('add')">
                        <select name="unit_type" id="add_unit" class="unit-toggle" onchange="calculateLiveStock('add'); updatePriceUnitDisplay('add');">
                        </select>
                        </div>
                        
                        <div>
                            <small class="text-muted">Current Global Stock: <strong id="add_current_stock_display">0</strong> </small>
                            <small id="add_new_stock_wrapper" class="text-muted" style="display: none;">New Global Stock: <strong id="add_new_stock_display">0</strong></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="input-group">
                        <label for="add_price">Purchase Price (Per Unit)</label>
                        <span class="icon-wrapper currency-symbol">&#8369;</span>
                        <input type="number" name="unit_price" id="add_price" step="0.01" min="0.01" required>
                        <span class="text-muted" id="add_price_unit_display"></span>
                    </div>

                    <div class="input-group">
                        <label for="add_fund_source">Payment Source</label>
                        <select name="fund_source" id="add_fund_source" class="unit-selector" required>
                            <option value="cash_in_hand">System Cash</option>
                            <option value="external_cash">External Cash</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="input-group">
                        <label for="add_remarks">Remarks</label>
                        <input type="text" name="remarks" id="add_remarks" placeholder="e.g., Supplier Delivery... etc.">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('addStockModal').style.display='none'">
                    <span>Cancel</span>
                </button>

                <button type="submit" class="btn">
                    <span>Confirm Restock</span>
                </button>
            </div>
        </form>
    </div>
 </div>