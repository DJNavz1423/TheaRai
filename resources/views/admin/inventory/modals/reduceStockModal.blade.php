<div id="reduceStockModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form id="reduceStockForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h2>Reduce Stock</h2>

                <button type="button" class="btn close-btn" onclick="document.getElementById('reduceStockModal').style.display='none'">
                    <span class="icon-wrapper close-modal">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
                    </span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <label for="reduce_stock_branch">Target Branch</label>
                        <select name="branch_id" id="reduce_stock_branch" class="unit-selector" required>
                            <option value="" disabled selected>Which branch is losing this stock?</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="input-group">
                        <label for="reduce_quantity">Quantity To Reduce (Spoilage, Waste, etc.)</label>
                        <div>
                            <input type="number" name="quantity" id="reduce_quantity" step="0.01" min="0.01" required placeholder="Enter quantity here" oninput="calculateLiveStock('reduce')">
                        <select name="unit_type" id="reduce_unit" class="unit-toggle" onchange="calculateLiveStock('reduce')"></select>
                        </div>
                        
                        <div>
                            <small class="text-muted">Current Global Stock: <strong id="reduce_current_stock_display">0</strong></small >
                            <small id="reduce_new_stock_wrapper" class="text-muted" style="display: none;">New Global Stock: <strong id="reduce_new_stock_display">0</strong></small >
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="input-group">
                        <label for="reduce_remarks">Reason / Remarks</label>
                        <input type="text" name="remarks" id="reduce_remarks" placeholder="e.g., Spoiled, Spilled, etc." required>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('reduceStockModal').style.display='none'">
                    <span>Cancel</span>
                </button>

                <button type="submit" class="btn">
                    <span>Reduce Stock</span>
                </button>
            </div>
        </form>
    </div>
 </div>