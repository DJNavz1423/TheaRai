<div id="addModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ url('/admin/users') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h2>Add New User</h2>

                <button type="button" class="btn close-btn" onclick="document.getElementById('addModal').style.display='none'">
                    <span class="icon-wrapper close-modal">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <label for="user-name-input">Full Name</label>
                    <input type="text" name="name" id="user-name-input" required placeholder="Juan Dela Cruz">
                </div>
                
                <div class="input-group mb-3">
                    <label for="user-email-input">Email Address</label>
                    <input type="email" name="email" id="user-email-input" required pattern="[a-zA-Z0-9._%+-]+@thearai\.com\.ph$" title="Must be a @thearai.com.ph email address" placeholder="juan123@thearai.com.ph">
                </div>

                <div class="input-group mb-3">
                    <label for="user-phone-input">Phone Number</label>
                    <input
                        type="text"
                        name="phone_number"
                        id="user-phone-input"
                        maxlength="11"
                        minlength="11"
                        inputmode="numeric"
                        pattern="[0-9]{11}"
                        placeholder="Optional: 09XXXXXXXXX"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                    >
                    <small class="text-muted">
                        Optional
                    </small>
                </div>

                <div class="input-group mb-3">
                    <label for="user-password-input">Password</label>
                    <input type="password" name="password" id="user-password-input" minlength="10" required placeholder="Min. 10 characters">
                </div>

                <div class="input-group mb-3">
                    <label for="role-select">Role</label>
                    <select name="role" id="role-select" class="unit-selector" required>
                        <option value="staff" selected>Staff</option>
                        <option value="admin">Admin</option>
                        
                        @if(in_array(auth()->user()->role, ['dev', 'owner']))
                        <option value="dev">
                            Developer
                        </option>

                        <option value="owner">
                            Owner
                        </option>
                        @endif
                    </select>
                </div>
                
                <div class="input-group mb-3" id="branch-wrapper">
                    <label for="branch-select">Assigned Branch</label>
                    <select name="branch_id" id="branch-select" class="unit-selector" placeholder="Select a branch..." required>
                        <option value="" selected disabled>Select a branch...</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Staff must be assigned to a specific branch for POS access.</small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('addModal').style.display='none'">
                    <span>Cancel</span>
                </button>
                <button type="submit" class="btn">Save User</button>
            </div>
        </form>
    </div>
</div>