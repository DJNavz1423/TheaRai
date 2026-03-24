@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
  <section id="user-management">
    <div class="container">
        <div class="row mb-4">
            <h1 class="heading">User List ({{ count($users) }})</h1>
            <div class="row heading-btn-row">
                <button id="addButton" class="btn" type="button">
                    <span class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
                    </span>
                    <span>Add New User</span>
                </button>
            </div>
        </div>
    </div>

    <div class="container table-container border mb-5">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="d-flex item-group">
                            <span class="item-data">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td><span class="item-data">{{ $user->email }}</span></td>
                    <td>
                        <span class="badge {{ $user->role == 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td><span class="item-data">{{ $user->created_at ? $user->created_at->format('M d, Y') : '--' }}</span></td>
                    <td>
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

    <!-- Add User Modal -->
    <div id="addModal" class="modal">
        <div class="modal-dialog">
            <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h2>Add New User</h2>
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
                    <button type="submit" class="btn btn-primary w-100">Save User</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@once 
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/peopleMng/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/table.css') }}">
    @endpush
@endonce