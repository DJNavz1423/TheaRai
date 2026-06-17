<div id="editModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form id="editTableForm" action="" method="POST" class="modal-content">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h2>Edit Table Details</h2>
                <button type="button" class="btn close-btn" onclick="document.getElementById('editModal').style.display='none'">
                    <span class="icon-wrapper close-modal"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <label for="edit_branch_id">Branch</label>
                    <select id="edit_branch_id" name="branch_id" class="unit-selector" placeholder="Select branch..." required>
                        <option value="" disabled selected>Select branch...</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3">
                    <label for="edit_table_number">Table Number / Identifier</label>
                    <input type="text" id="edit_table_number" name="table_number" required placeholder="e.g., 5 or VIP-1" style="width: 100%; padding: 8px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('editModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn">Update Table</button>
            </div>
        </form>
    </div>
</div>