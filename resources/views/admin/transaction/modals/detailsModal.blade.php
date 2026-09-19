<div id="transactionDetailsModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
              <h2>Transaction Details</h2>

              <button type="button" id="closeTransactionDetails" class="close-btn">
                <span class="icon-wrapper close-modal">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
                </span>
              </button>
          </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <strong>Receipt No.</strong>
                    <span id="detail-receipt"></span>
                </div>

                <div class="row mb-3">
                    <strong>Branch</strong>
                    <span id="detail-branch"></span>
                </div>

                <div class="row mb-3" id="detail-subtotal-row" style="display: none;">
                    <strong>Subtotal</strong>
                    <span id="detail-subtotal"></span>
                </div>

                <div class="row mb-3" id="detail-discount-row" style="display: none;">
                    <strong>Discount</strong>
                    <span id="detail-discount"></span>
                </div>

                <div class="row mb-3">
                    <strong>Total Amount</strong>
                    <span id="detail-total"></span>
                </div>

                <div class="row mb-3">
                    <strong>Payment Method</strong>
                    <span id="detail-payment-method"></span>
                </div>

                <div class="row mb-3">
                    <strong>Payment Status</strong>
                    <span id="detail-payment-status"></span>
                </div>

                <div class="row mb-3">
                    <strong>Date</strong>
                    <span id="detail-date"></span>
                </div>


                <div class="border-t" style="margin-top: 1.5rem; padding-top: 1rem;">
                    <h3 class="mb-1 d-flex purchased-header">
                      <span>Purchased Items</span>
                      <span>Total</span>
                    </h3>

                    <div id="detail-items">
                      <p class="text-muted">Loading items...</p>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>