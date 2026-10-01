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

                    <button type="button" class="toggle-visibility" aria-label="Show password" id="toggle-user-password">
                        <span class="icon-wrapper visibility-on">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                height="24px"
                                viewBox="0 -960 960 960"
                                width="24px"
                                fill="#e3e3e3">
                                <path d="M607.5-372.5Q660-425 660-500t-52.5-127.5Q555-680 480-680t-127.5 52.5Q300-575 300-500t52.5 127.5Q405-320 480-320t127.5-52.5Zm-204-51Q372-455 372-500t31.5-76.5Q435-608 480-608t76.5 31.5Q588-545 588-500t-31.5 76.5Q525-392 480-392t-76.5-31.5ZM235.5-272Q125-344 61-462q-5-9-7.5-18.5T51-500q0-10 2.5-19.5T61-538q64-118 174.5-190T480-800q134 0 244.5 72T899-538q5 9 7.5 18.5T909-500q0 10-2.5 19.5T899-462q-64 118-174.5 190T480-200q-134 0-244.5-72ZM480-500Zm207.5 160.5Q782-399 832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280q113 0 207.5-59.5Z"/>
                            </svg>
                        </span>

                        <span class="icon-wrapper visibility-off d-none">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                height="24px"
                                viewBox="0 -960 960 960"
                                width="24px"
                                fill="#e3e3e3">
                                <path d="M607-627q29 29 42.5 66t9.5 76q0 15-11 25.5T622-449q-15 0-25.5-10.5T586-485q5-26-3-50t-25-41q-17-17-41-26t-51-4q-15 0-25.5-11T430-643q0-15 10.5-25.5T466-679q38-4 75 9.5t66 42.5Zm-127-93q-19 0-37 1.5t-36 5.5q-17 3-30.5-5T358-742q-5-16 3.5-31t24.5-18q23-5 46.5-7t47.5-2q137 0 250.5 72T904-534q4 8 6 16.5t2 17.5q0 9-1.5 17.5T905-466q-18 40-44.5 75T802-327q-12 11-28 9t-26-16q-10-14-8.5-30.5T753-392q24-23 44-50t35-58q-50-101-144.5-160.5T480-720Zm0 520q-134 0-245-72.5T60-463q-5-8-7.5-17.5T50-500q0-10 2-19t7-18q20-40 46.5-76.5T166-680l-83-84q-11-12-10.5-28.5T84-820q11-11 28-11t28 11l680 680q11 11 11.5 27.5T820-84q-11 11-28 11t-28-11L624-222q-35 11-71 16.5t-73 5.5ZM222-624q-29 26-53 57t-41 67q50 101 144.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z"/>
                            </svg>
                        </span>
                    </button>
                </div>

                <div class="input-group mb-3">
                    <label for="role-select">Role</label>
                    <select name="role" id="role-select" class="unit-selector" required>
                        <option value="staff" selected>Cashier</option>
                        <option value="waiter">Waiter</option>
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
                    <small class="text-muted">Staffs must be assigned to a specific branch for POS access.</small>
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