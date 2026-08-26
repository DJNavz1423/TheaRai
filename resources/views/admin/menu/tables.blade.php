@extends('layouts.admin')

@section('title', 'Table Management')

@section('content')
<div class="container">
    <div class="row mb-3">
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
    <div class="row table-controls mb-3">
        <div class="searchbox">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </span>

            <input type="text" id="tableSearch" class="border searchBar" placeholder="Search tables...">
        </div>

        <div class="filters">
            <select id="filter-branch" class="ts-filter">
                <option value="all" selected>All Branches</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>

            <select id="sort-items" class="ts-filter">
                <option value="latest" selected>Latest</option>
                <option value="qty_desc">Oldest</option>
            </select>
        </div>
    </div>

    <div class="container table-container border">
        <table role="table">
            <thead>
                <tr>
                    <th>Branch</th>
                    <th>Table Number</th>
                    <th>QR Code URL</th>
                    <th>Print QR</th>
                    <th>Date Created</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            
            <tbody role="rowgroup">
                @forelse($tables as $table)
                @php
                    // Generates the exact URL the customer phone will scan
                    $qrUrl = $baseUrl . "/qr-menu?branch=" . $table->branch_id . "&table=" . $table->id;
                @endphp
                <tr role="row" class="table-row"
                    data-branch="{{ $table->branch_id }}"
                    data-number="{{ strtolower($table->table_number) }}"
                    data-created="{{ strtotime($table->created_at ?? now()) }}">
                    <td role="cell"><span style="font-weight: bold; color: var(--primary);">{{ $table->branch_name }}</span></td>
                    <td role="cell"><span class="item-data">Table {{ $table->table_number }}</span></td>
                    <td role="cell"><span class="text-muted" style="font-size: 0.8rem;">{{ $qrUrl }}</span></td>
                    <td role="cell">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qrUrl) }}" alt="QR Code" style="width: 50px; height: 50px; border-radius: 4px; cursor: pointer; border: 1px solid #ccc;" onclick="window.open(this.src, '_blank')">
                    </td>

                    <td data-cell="created" role="cell">{{ $table->created_at ? \Carbon\Carbon::parse($table->created_at)->format('M d, Y') : 'N/A' }}</td>

                    <td role="cell">
                        <div class="row" style="justify-content: center; align-items: center; gap: 10px;">
                            <button type="button" class="btn btn-icon" onclick="openEditModal(
                                {{ $table->id }},
                                {{ $table->branch_id }},
                                '{{ $table->table_number }}'
                            )">
                                <span class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0d884e"><path d="M160-120q-17 0-28.5-11.5T120-160v-97q0-16 6-30.5t17-25.5l505-504q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L313-143q-11 11-25.5 17t-30.5 6h-97Zm544-528 56-56-56-56-56 56 56 56Z"/></svg>
                                </span>
                            </button>

                            <button type="button" class="btn btn-icon" onclick="openDeleteModal(
                                {{ $table->id }},
                                '{{ $table->table_number }}'
                            )">
                                <span class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm200-284 76 76q11 11 28 11t28-11q11-11 11-28t-11-28l-76-76 76-76q11-11 11-28t-11-28q-11-11-28-11t-28 11l-76 76-76-76q-11-11-28-11t-28 11q-11 11-11 28t11 28l76 76-76 76q-11 11-11 28t11 28q11 11 28 11t28-11l76-76Z"/></svg>
                                </span>
                            </button>
                        </div>
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

{{-- Include Modals --}}
@include('admin.menu.tableModals.addModal')

@include('admin.menu.tableModals.editModal')

@include('admin.menu.tableModals.deleteModal')

@endsection

@once
  @push('styles')
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
    <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/dashboard/filters/tsTableFilter.js') }}" defer></script>

    <script>
    function openEditModal(id, branchId, tableNumber){
        document.getElementById('editTableForm').action =
            "/admin/tables/" + id;

        const branchSelect = document.getElementById('edit_branch_id');

        if (branchSelect.tomselect) {
            branchSelect.tomselect.setValue(branchId.toString());
        } else {
            branchSelect.value = branchId;
        }

        document.getElementById('edit_table_number').value = tableNumber;

        document.getElementById('editModal').style.display = 'flex';
    }

    function openDeleteModal(id, tableNumber){
        document.getElementById('deleteForm').action =
            "/admin/tables/" + id;

        document.getElementById('deleteTableName').textContent =
            "Table " + tableNumber;

        document.getElementById('deleteModal').style.display = 'flex';
    }
    </script>

    

    @if(session('error')) <script>alert("🚨 ERROR: {{ session('error') }}");</script> @endif
    @if(session('success')) <script>alert("✅ SUCCESS: {{ session('success') }}");</script> @endif
    @endpush
@endonce