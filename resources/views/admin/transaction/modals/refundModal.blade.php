<div id="refundModal" class="modal" style="display: none;">
  <div class="modal-dialog">

    <form id="refundForm" method="POST" class="modal-content">
      @csrf

      <div class="modal-header">

        <h2>Refund Transaction</h2>

        <button
          type="button"
          class="btn close-btn"
          onclick="document.getElementById('refundModal').style.display='none'"
        >
          <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg"
              height="24px"
              viewBox="0 -960 960 960"
              width="24px"
              fill="#e3e3e3">
              <path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 0 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/>
            </svg>
          </span>
        </button>

      </div>


      <div class="modal-body">

        <div class="row">
          <div class="input-group">

            <label>
              Receipt No.
            </label>

            <strong id="refund-receipt">
              --
            </strong>

          </div>
        </div>


        <div class="row">
          <div class="input-group">

            <label>
              Refund Amount
            </label>

            <strong id="refund-amount">
              ₱0.00
            </strong>

          </div>
        </div>


        <div class="row">
          <div class="input-group">

            <label for="refund_reason">
              Reason
            </label>

            <select id="refund_reason" name="refund_reason" class="unit-selector" required>
              <option value="">Select reason</option>
              <option value="food_quality">Food Quality</option>
              <option value="wrong_order">Wrong Order</option>
              <option value="customer_complaint">Customer Complaint</option>
              <option value="allergy_safety">Allergy / Safety</option>
              <option value="other">Other</option>
            </select>

          </div>
        </div>


        <div class="row">
          <div class="input-group">

            <label for="refund_condition">
              Food Condition
            </label>

            <select
              id="refund_condition"
              name="refund_condition"
              class="unit-selector"
              required
            >
              <option value="">Select condition</option>
              <option value="resellable">
                Untouched / Resellable
              </option>
              <option value="wasted">
                Opened / Consumed / Discarded
              </option>
            </select>

          </div>
        </div>

        <div class="modal-text">
          <p class="text-muted">
          Untouched / resellable food will restore the ingredients
          used by this transaction. Opened, consumed, or discarded
          food will not restore inventory.
        </p>
        </div>
      </div>


      <div class="modal-footer">

        <button
          type="button"
          class="btn"
          onclick="document.getElementById('refundModal').style.display='none'"
        >
          Cancel
        </button>

        <button
          type="submit"
          class="btn dropdown-red"
        >
          Refund Transaction
        </button>

      </div>

    </form>

  </div>
</div>