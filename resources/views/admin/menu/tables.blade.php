@extends('layouts.admin')

@section('title', 'Table Management')

@section('content')
<div class="container">
    <div class="row mb-4">
        <h1 class="heading">Table & QR Management ({{ count($tables) }})</h1>
        <div class="row heading-btn-row">
            <button id="addButton" class="btn" type="button" onclick="document.getElementById('addModal').style.display='flex'">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
                </span>
                <span>Add New Table</span>
            </button>
        </div>
    </div>
</div>

<div class="container main-content">
    <div class="container table-container border">
        <table role="table">
            <thead>
                <tr>
                    <th>Branch</th>
                    <th>Table Number</th>
                    <th>QR Code URL</th>
                    <th>Print QR</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody role="rowgroup">
                @forelse($tables as $table)
                @php
                    // Generates the exact URL the customer phone will scan
                    $qrUrl = $baseUrl . "/qr-menu?branch=" . $table->branch_id . "&table=" . $table->id;
                @endphp
                <tr role="row">
                    <td role="cell"><span style="font-weight: bold; color: var(--primary);">{{ $table->branch_name }}</span></td>
                    <td role="cell"><span class="item-data">Table {{ $table->table_number }}</span></td>
                    <td role="cell"><span class="text-muted" style="font-size: 0.8rem;">{{ $qrUrl }}</span></td>
                    <td role="cell">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qrUrl) }}" alt="QR Code" style="width: 50px; height: 50px; border-radius: 4px; cursor: pointer; border: 1px solid #ccc;" onclick="window.open(this.src, '_blank')">
                    </td>
                    <td role="cell">
                        <form action="{{ route('admin.menu.tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Delete this table and deactivate its QR code?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="background: none; border: none; color: var(--danger); padding: 5px;">
                                <span class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm200-284 76 76q11 11 28 11t28-11q11-11 11-28t-11-28l-76-76 76-76q11-11 11-28t-11-28q-11-11-28-11t-28 11l-76 76-76-76q-11-11-28-11t-28 11q-11 11-11 28t11 28l76 76-76 76q-11 11-11 28t11 28q11 11 28 11t28-11l76-76Z"/></svg>
                                </span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-muted" style="text-align: center; padding: 20px;">No tables have been created yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="addModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ route('admin.menu.tables.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h2>Add New Table</h2>
                <button type="button" class="btn close-btn" onclick="document.getElementById('addModal').style.display='none'">
                    <span class="icon-wrapper close-modal"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <label>Branch</label>
                    <select name="branch_id" class="unit-selector" required style="width: 100%; padding: 8px;">
                        <option value="" disabled selected>Select branch...</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3">
                    <label>Table Number / Identifier</label>
                    <input type="text" name="table_number" required placeholder="e.g., 5 or VIP-1" style="width: 100%; padding: 8px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('addModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn">Generate QR Code</button>
            </div>
        </form>
    </div>
</div>
@endsection

@once
  @push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/modal.css') }}">
  @endpush
@endonce

@once
    @push('scripts')
    @if(session('error')) <script>alert("🚨 ERROR: {{ session('error') }}");</script> @endif
    @if(session('success')) <script>alert("✅ SUCCESS: {{ session('success') }}");</script> @endif
    @endpush
@endonce