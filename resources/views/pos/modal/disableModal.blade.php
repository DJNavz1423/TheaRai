<div id="disableReasonModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Disable Menu Item</h2>

                <button type="button" class="btn close-btn" id="closeDisableReasonModal">
                  <span class="icon-wrapper close-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
                </span>
                </button>
            </div>

            <div class="modal-body">

                <p id="disableReasonItemName"></p>

                <div class="input-group mb-3">
                    <label for="disableReasonSelect">
                        Reason
                    </label>

                    <select
                        id="disableReasonSelect"
                        class="unit-selector"
                    >
                        <option value="">Select a reason</option>
                        <option value="sold_out">Sold Out</option>
                        <option value="out_of_stock">Out of Stock</option>
                        <option value="others">Others</option>
                    </select>
                </div>

                <div
                    class="input-group"
                    id="disableOtherReasonWrapper"
                    style="display: none;"
                >
                    <label for="disableOtherReason">
                        Specify Reason
                    </label>

                    <input
                        type="text"
                        id="disableOtherReason"
                        maxlength="255"
                        placeholder="Enter reason"
                    >
                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn"
                    id="cancelDisableReason"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    class="btn"
                    id="confirmDisableReason"
                    disabled
                >
                    Disable
                </button>

            </div>

        </div>
    </div>
</div>