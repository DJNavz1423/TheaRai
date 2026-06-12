@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="container">
    <div class="row mb-4">
        <h1 class="heading">User List ({{ count($users) }})</h1>
        <div class="row heading-btn-row">
            <button id="addButton" class="btn" type="button" onclick="document.getElementById('addModal').style.display='flex'">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
                </span>
                <span>Add New User</span>
            </button>
        </div>
    </div>
</div>

<div class="container main-content">
    <div class="row table-controls mb-3">
        <div class="searchbox">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </span>
            <input type="text" id="userSearch" class="border searchBar" placeholder="Search users...">
        </div>

        <div class="filters">
            <select id="filter-role" class="ts-filter">
                <option value="all" selected>All Roles</option>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
            </select>

            <select id="filter-branch" class="ts-filter">
                <option value="all" selected>All Branches</option>
                @foreach($branches as $branch)
                    <option value="{{ strtolower($branch->name) }}">{{ $branch->name }}</option>
                @endforeach
            </select>

            <select id="sort-users" class="ts-filter">
                <option value="latest" selected>Latest</option>
                <option value="name_asc">Name: A-Z</option>
                <option value="name_desc">Name: Z-A</option>
            </select>
        </div>
    </div>

    <div class="container table-container border mb-5">
    <table role="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Assigned Branch</th>
                <th>Joined Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody role="rowgroup">
            @foreach($users as $user)
            <tr role="row" class="user-row"
                data-name="{{ strtolower($user->name) }}"
                data-role="{{ strtolower($user->role) }}"
                data-branch="{{ strtolower($user->branch_name ?? 'unassigned') }}"
                data-created="{{ strtotime($user->created_at) }}">
                <td data-cell="name" role="cell">
                    <div class="d-flex item-group">
                        <span class="item-data">{{ $user->name }}</span>
                    </div>
                </td>
                <td data-cell="email" role="cell"><span class="item-data">{{ $user->email }}</span></td>
                <td data-cell="role" role="cell">
                    <span class="badge {{ $user->role == 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td data-cell="branch" role="cell">
                    <span class="item-data" style="color: {{ $user->role === 'admin' ? 'var(--primary)' : 'inherit' }}; font-weight: {{ $user->role === 'admin' ? '600' : 'normal' }};">
                        {{ $user->role === 'admin' ? 'All Branches (Admin)' : ($user->branch_name ?? 'Unassigned') }}
                    </span>
                </td>
                <td data-cell="date" role="cell">
                    <span class="item-data">
                        {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M d, Y') : '--' }}
                    </span>
                </td>
                <td data-cell="actions" role="cell">
                    <div class="dropdown-wrapper">
                            <button type="button" class="more-actions" onclick="toggleDropdown(this)">
                            <span class="icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-160q-33 0-56.5-23.5T400-240q0-33 23.5-56.5T480-320q33 0 56.5 23.5T560-240q0 33-23.5 56.5T480-160Zm0-240q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0-240q-33 0-56.5-23.5T400-720q0-33 23.5-56.5T480-800q33 0 56.5 23.5T560-720q0 33-23.5 56.5T480-640Z"/></svg>
                            </span>
                        </button>

                        <div class="dropdown-menu border" style="display: none;">
                            <div class="dropdown-section">
                                <button class="dropdown-item btn edit-user-btn"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-role="{{ $user->role }}"
                                    data-branch="{{ $user->branch_id }}"
                                    >
                                    <span class="icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M160-120q-17 0-28.5-11.5T120-160v-97q0-16 6-30.5t17-25.5l505-504q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L313-143q-11 11-25.5 17t-30.5 6h-97Zm544-528 56-56-56-56-56 56 56 56Z"/></svg>
                                    </span>
                                    Edit User Info
                                </button>

                                <button class="dropdown-item btn manage-password-btn"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}">
                                    <span class="icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-480ZM455-87q-134-44-214.5-166.5T160-516v-189q0-25 14.5-45t37.5-29l240-90q14-5 28-5t28 5l240 90q23 9 37.5 29t14.5 45v186q0 17-11.5 28.5T760-479q-17 0-28.5-11.5T720-519v-186l-240-90-240 90v189q0 121 68 220t172 132q16 5 23.5 20t2.5 31q-5 16-20 23.5T455-87Zm225-113h-80q-17 0-28.5-11.5T560-240q0-17 11.5-28.5T600-280h80v-80q0-17 11.5-28.5T720-400q17 0 28.5 11.5T760-360v80h80q17 0 28.5 11.5T880-240q0 17-11.5 28.5T840-200h-80v80q0 17-11.5 28.5T720-80q-17 0-28.5-11.5T680-120v-80ZM444-360h72q9 0 15.5-7.5T536-384l-19-105q20-10 31.5-29t11.5-42q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 23 11.5 42t31.5 29l-19 105q-2 9 4.5 16.5T444-360Z"/></svg>
                                    </span>
                                    Change Password
                                </button>

                                <button class="dropdown-item dropdown-red btn" onclick="openDeleteUserModal({{ $user->id }}, '{{ $user->name }}')">
                                    <span class="icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v126q0 17-13.5 28t-31.5 8q-8-1-17-1.5t-18-.5q-20 0-40 2.5t-40 8.5v-51q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v90q-24 17-44.5 38.5T440-424v-176q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q0 29 6.5 57.5T424-168q8 17-1.5 32.5T396-120H280Zm258.5-18.5Q480-197 480-280t58.5-141.5Q597-480 680-480t141.5 58.5Q880-363 880-280t-58.5 141.5Q763-80 680-80t-141.5-58.5ZM700-288v-92q0-8-6-14t-14-6q-8 0-14 6t-6 14v91q0 8 3 15.5t9 13.5l60 60q6 6 14 6t14-6q6-6 6-14t-6-14l-60-60Z"/></svg>
                                    </span>
                                    Remove User
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

<!-- Modals -->
@include('admin.peopleManagement.modals.addModal')

@include('admin.peopleManagement.modals.editModal')

@include('admin.peopleManagement.modals.passwordModal')

@include('admin.peopleManagement.modals.deleteModal')

@endsection

@once 
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/peopleMng/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/filters.css') }}">
    @endpush
@endonce

@once
    @push('scripts')
    <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dashboard/filters/tsUsersFilter.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dashboard/toggleDropdown.js') }}"></script>

    <script>
        // 1. Role-to-Branch Visibility Logic
        document.getElementById('role-select').addEventListener('change', function() {
            const branchWrapper = document.getElementById('branch-wrapper');
            const branchSelect = document.getElementById('branch-select');
            
            if (this.value === 'admin') {
                // Admins don't need a branch
                branchWrapper.style.display = 'none';
                branchSelect.removeAttribute('required');
                if (branchSelect.tomselect) {
                    branchSelect.tomselect.clear();
                }
            } else {
                // Staff absolutely need a branch
                branchWrapper.style.display = 'block';
                branchSelect.setAttribute('required', 'required');
            }
        });

        // 2. Auto-Generate Email Logic
        const nameInput = document.getElementById('user-name-input');
        const emailInput = document.getElementById('user-email-input');
        
        let emailManuallyEdited = false;
        let persistentRandomNum = null;

        // Listen if the user edits the email field themselves
        emailInput.addEventListener('input', function() {
            emailManuallyEdited = true;
        });

        // Generate email when typing a name
        nameInput.addEventListener('input', function() {
            if (!emailManuallyEdited) {
                // Grab just the first name and lowercase it
                let nameStr = this.value.trim().split(' ')[0].toLowerCase();
                
                // Strip out special characters, keeping only a-z and numbers
                nameStr = nameStr.replace(/[^a-z0-9]/g, ''); 
                
                if (nameStr.length > 0) {
                    // Generate the random number ONLY if we haven't generated one yet
                    if (persistentRandomNum === null) {
                        persistentRandomNum = Math.floor(Math.random() * 9999) + 1;
                    }
                    
                    emailInput.value = `${nameStr}${persistentRandomNum}@thearai.com.ph`;
                } else {
                    // If they delete the name completely, clear the email and reset the number
                    emailInput.value = '';
                    persistentRandomNum = null; 
                }
            }
        });
    </script>

    <script>
    // ==========================
    // Edit User Modal
    // ==========================

        function toggleEditBranch() {

            const role = document.getElementById('edit_role').value;

            const wrapper = document.getElementById('edit_branch_wrapper');

            const branch = document.getElementById('edit_branch');

            if(role === 'admin') {

                wrapper.style.display = 'none';

                branch.removeAttribute('required');

            } else {

                wrapper.style.display = 'block';

                branch.setAttribute('required', 'required');
            }
        }

        document.getElementById('edit_role') ?.addEventListener('change', toggleEditBranch);

        document.querySelectorAll('.edit-user-btn').forEach(button => {

            button.addEventListener('click', function () {

                const userId = this.dataset.id;

                document.getElementById('edit_user_id').value = userId;

                document.getElementById('edit_name').value = this.dataset.name;

                document.getElementById('edit_email').value = this.dataset.email;

                const roleSelect = document.getElementById('edit_role');
                const branchSelect = document.getElementById('edit_branch');

                if (roleSelect.tomselect) {
                    roleSelect.tomselect.setValue(this.dataset.role, true);
                } else {
                    roleSelect.value = this.dataset.role;
                }

                if (branchSelect.tomselect) {
                    branchSelect.tomselect.setValue(this.dataset.branch || '', true);
                } else {
                    branchSelect.value = this.dataset.branch || '';
                }

                document.getElementById('editUserForm').action = `/admin/users/${userId}`;

                toggleEditBranch();

                document.getElementById('editUserModal').style.display = 'flex';
            });

        });

        // ==========================
        // Delete User Modal
        // ==========================

        function openDeleteUserModal(id, name) {
            document.getElementById('deleteUserForm').action =
                `/admin/users/${id}`;

            document.getElementById('deleteUserText').innerText =
                `${name}?`;

            document.getElementById('deleteUserModal')
                .style.display = 'flex';
        }
    </script>

    <script>
        const newPasswordInput = document.getElementById('new-password');
        const confirmPasswordInput = document.getElementById('confirm-password');
        const passwordHint = document.getElementById('password-match-hint');

        function validatePasswordMatch() {

            const password = newPasswordInput.value;
            const confirm = confirmPasswordInput.value;

            if (password.length > 0 && password.length < 10) {
                passwordHint.textContent =
                    'Password must be at least 10 characters';

                passwordHint.style.color =
                    '#c90a1d';

                return;
            }

            if (confirm.length === 0) {
                passwordHint.textContent = '';
                return;
            }

            if (password === confirm) {
                passwordHint.textContent = '✓ Passwords match';
                passwordHint.style.color = '#0d884e';
            } else {
                passwordHint.textContent = '✗ Passwords do not match';
                passwordHint.style.color = '#c90a1d';
            }
        }

        newPasswordInput.addEventListener('input', validatePasswordMatch);
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);

        function setupPasswordToggle(inputId, buttonId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);

            if (!input || !button) return;

            const eyeOpen =
                button.querySelector('.visibility-on');

            const eyeClosed =
                button.querySelector('.visibility-off');

            button.addEventListener('click', () => {

                const isPassword =
                    input.type === 'password';

                input.type =
                    isPassword ? 'text' : 'password';

                eyeOpen.classList.toggle('d-none');
                eyeClosed.classList.toggle('d-none');
            });
        }

        setupPasswordToggle(
            'new-password',
            'toggle-new-password'
        );

        setupPasswordToggle(
            'confirm-password',
            'toggle-confirm-password'
        );

        const managePasswordForm = document.getElementById('managePasswordForm');

        if (managePasswordForm) { 
            managePasswordForm.addEventListener('submit', function(e) {
                if (
                    newPasswordInput.value !==
                    confirmPasswordInput.value
                ) {
                    e.preventDefault();

                    passwordHint.textContent =
                        'Passwords do not match';

                    passwordHint.style.color =
                        '#dc3545';

                    confirmPasswordInput.focus();
                }
            });
        }

        document.querySelectorAll('.manage-password-btn').forEach(button => {
            button.addEventListener('click', function() {

                const userId = this.dataset.id;

                document
                    .getElementById('managePasswordForm')
                    .action =
                    `/admin/users/${userId}/password`;

                newPasswordInput.value = '';
                confirmPasswordInput.value = '';
                passwordHint.textContent = '';

                document.getElementById('modalTitle').innerText = `Change Password for ${this.dataset.name}`;

                newPasswordInput.type = 'password';
                confirmPasswordInput.type = 'password';

                document.querySelectorAll('.visibility-off').forEach(el => el.classList.add('d-none'));

                document.querySelectorAll('.visibility-on').forEach(el => el.classList.remove('d-none'));

                document
                    .getElementById('managePasswordModal')
                    .style.display = 'flex';
            });

        });
    </script>

    @if(session('error'))
        <script>
            alert("🚨 ERROR: {{ session('error') }}");
        </script>
    @endif

    @if(session('success'))
        <script>
            alert("✅ SUCCESS: {{ session('success') }}");
        </script>
    @endif

    @if($errors->any())
        <script>
            let errorMessages = "⚠️ Please fix the following errors:\n\n";
            @foreach ($errors->all() as $error)
                errorMessages += "• {{ $error }}\n";
            @endforeach
            alert(errorMessages);
        </script>
    @endif
    @endpush
@endonce