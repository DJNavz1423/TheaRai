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

<div class="container mb-3">
    <div class="row table-controls mb-3">
        <div class="searchbox">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </span>

            <input type="text" id="userSearch" class="border searchBar" placeholder="Search products...">
        </div>

        <div class="filters"></div>
    </div>

    <div class="container table-container border mb-5">
    <table role="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody role="rowgroup">
            @foreach($users as $user)
            <tr role="row">
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
                <td data-cell="date" role="cell"><span class="item-data">{{ $user->created_at ? $user->created_at->format('M d, Y') : '--' }}</span></td>
                <td data-cell="actions" role="cell">
                    <button class="more-actions">
                        <span class="icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-160q-33 0-56.5-23.5T400-240q0-33 23.5-56.5T480-320q33 0 56.5 23.5T560-240q0 33-23.5 56.5T480-160Zm0-240q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0-240q-33 0-56.5-23.5T400-720q0-33 23.5-56.5T480-800q33 0 56.5 23.5T560-720q0 33-23.5 56.5T480-640Z"/></svg>
                        </span>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

<!-- Add User Modal -->
<div id="addModal" class="modal">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content">
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
                    <label>Full Name</label>
                    <input type="text" name="name" required placeholder="Juan Dela Cruz">
                </div>
                <div class="input-group mb-3">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="juan@example.com">
                </div>
                <div class="input-group mb-3">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Min. 6 characters">
                </div>
                <div class="input-group mb-3">
                    <label>Role</label>
                    <select name="role" class="unit-selector">
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
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
@endsection

@once 
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/peopleMng/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/modal.css') }}">
    @endpush
@endonce