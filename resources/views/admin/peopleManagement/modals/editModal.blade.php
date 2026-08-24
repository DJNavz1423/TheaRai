<div id="editUserModal" class="modal" style="display:none;">
    <div class="modal-dialog">
        <form id="editUserForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')

            <input type="hidden" name="user_id" id="edit_user_id">

            <div class="modal-header">
                <h2>Edit User</h2>

                <button
                    type="button"
                    class="btn close-btn"
                    onclick="document.getElementById('editUserModal').style.display='none'"
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

                <div class="input-group mb-3">
                    <label for="edit_name">Full Name</label>
                    <input type="text"
                        name="name"
                        id="edit_name"
                        required>
                </div>

                <div class="input-group mb-3">
                    <label for="edit_email">Email Address</label>
                    <input type="email"
                        name="email"
                        id="edit_email"
                        required
                        pattern="[a-zA-Z0-9._%+-]+@thearai\.com\.ph$">
                </div>

                <div class="input-group mb-3">
                    <label for="edit_phone">Phone Number</label>

                    <input
                        type="text"
                        name="phone_number"
                        id="edit_phone"
                        maxlength="11"
                        minlength="11"
                        inputmode="numeric"
                        pattern="[0-9]{11}"
                        placeholder="Optional: 09XXXXXXXXX"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                    >
                </div>

                <div class="input-group mb-3">
                    <label for="edit_role">Role</label>

                    <select
                        name="role"
                        id="edit_role"
                        class="unit-selector"
                        required
                    >
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="input-group mb-3" id="edit_branch_wrapper">
                    <label for="edit_branch">Assigned Branch</label>

                    <select
                        name="branch_id"
                        id="edit_branch"
                        class="unit-selector"
                        placeholder="Select a branch..."
                        required
                    >
                        <option value="" disabled>Select a branch...</option>

                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>

                    <small class="text-muted">
                        Staff must be assigned to a specific branch for POS access.
                    </small>
                </div>

            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn"
                    onclick="document.getElementById('editUserModal').style.display='none'"
                >
                    Cancel
                </button>

                <button type="submit" class="btn">
                    Save Changes
                </button>
            </div>

        </form>
    </div>
</div>