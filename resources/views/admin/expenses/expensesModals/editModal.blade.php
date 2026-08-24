<div id="editExpenseModal" class="modal" style="display: none;">
    <div class="modal-dialog">

        <form
            id="editExpenseForm"
            action=""
            method="POST"
            class="modal-content"
        >
            @csrf
            @method('PUT')

            <div class="modal-header">

                <h2>Edit Expense</h2>

                <button
                    type="button"
                    class="btn close-btn"
                    onclick="document.getElementById('editExpenseModal').style.display='none'"
                >
                    <span class="icon-wrapper close-modal">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="#e3e3e3">
                            <path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/>
                        </svg>
                    </span>
                </button>

            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="input-group">
                        <label for="edit-expense-branch">
                            Branch
                        </label>

                        <select
                            id="edit-expense-branch"
                            name="branch_id"
                            class="unit-selector"
                            required
                        >
                            <option value="" disabled>
                                Select branch...
                            </option>

                            @foreach($branches as $branch)
                                <option
                                    value="{{ $branch->id }}"
                                >
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="input-group">

                        <label for="edit-expense-type">
                            Expense Type
                        </label>

                        <select
                            id="edit-expense-type"
                            name="expense_type"
                            class="unit-selector"
                            required
                        >
                            <option value="regular">
                                Regular
                            </option>

                            <option value="restock">
                                Restock
                            </option>

                            <option value="ingredient_purchase">
                                Ingredient Purchase
                            </option>
                        </select>

                    </div>
                </div>

                <div class="row">
                    <div class="input-group">

                        <label for="edit-fund-source">
                            Fund Source
                        </label>

                        <select
                            id="edit-fund-source"
                            name="fund_source"
                            class="unit-selector"
                            required
                        >
                            <option value="cash_in_hand">
                                System Cash
                            </option>

                            <option value="external_cash">
                                External Cash
                            </option>
                        </select>

                    </div>
                </div>

                <div class="row">
                    <div class="input-group">

                        <label for="edit-expense-description">
                            Description
                        </label>

                        <textarea
                            id="edit-expense-description"
                            name="description"
                            required
                            maxlength="1000"
                            placeholder="Enter expense description..."
                        ></textarea>

                    </div>
                </div>

                <div class="row">
                    <div class="input-group">

                        <label for="edit-expense-amount">
                            Amount
                        </label>

                        <input
                            type="text"
                            inputmode="decimal"
                            pattern="[0-9]*(\.[0-9]+)?"
                            id="edit-expense-amount"
                            name="total_amount"
                            min="0.01"
                            step="0.01"
                            required
                        >

                    </div>
                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn"
                    onclick="document.getElementById('editExpenseModal').style.display='none'"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn"
                >
                    Update Expense
                </button>

            </div>

        </form>

    </div>
</div>