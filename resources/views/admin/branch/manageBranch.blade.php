@extends('layouts.admin')

@section('title', 'Branch Management')

@section('content')
  <div class="container">
    <div class="row mb-3">
      <h1 class="heading">Branch Management ({{ count($branches) }})</h1>

      <div class="row heading-btn-row">
        <button id="addButton" class="btn" type="button" onclick="document.getElementById('addModal').style.display='flex'">
          <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
          </span>
          <span>Add New Branch</span>
        </button>
      </div>
    </div>
  </div>

  <div class="container main-content">
    <div class="row table-controler mb-3">
       <div class="searchbox">
        <span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
        </span>

        <input type="text" id="branchSearch" class="border searchBar" placeholder="Search branches...">
      </div>

      <div class="filters">
        {{-- Sort --}}
        <select id="sort-items" class="ts-filter">
          <option value="latest" selected>Latest</option>
          <option value="oldest">Oldest</option>
        </select>
      </div>
    </div>

    <div class="container table-container border">
      <table role="table">
        <thead>
          <tr>
            <th>Branch Name</th>
            <th>Address</th>
            <th>Dishes Available</th>
            <th>Inventory Value</th>
            <th>Created</th>
            <th style="text-align: center;">Actions</th>  
          </tr>
        </thead>

        <tbody role="rowgroup">
          @foreach($branches as $branch)

            <tr
              class="branch-row"
              data-name="{{ strtolower($branch->name) }}"
              data-address="{{ strtolower($branch->address ?? '') }}"
              data-inventory="{{ number_format($branch->inventory_value, 2) }}"
              data-inventory-raw="{{ number_format($branch->inventory_value, 2, '.', '') }}"
              data-date="{{ strtolower(\Carbon\Carbon::parse($branch->created_at)->format('M j, Y')) }}"
              data-date-full="{{ strtolower(\Carbon\Carbon::parse($branch->created_at)->format('F j, Y')) }}"
              data-date-iso="{{ \Carbon\Carbon::parse($branch->created_at)->format('Y-m-d') }}"
              data-created="{{ \Carbon\Carbon::parse($branch->created_at)->timestamp }}">

              {{-- Branch Name --}}
              <td role="cell">
                {{ $branch->name }}
              </td>


              {{-- Address --}}
              <td role="cell">
                {{ $branch->address ?? 'No address' }}
              </td>


              {{-- Dishes Available --}}
              <td role="cell">
                {{ $branch->dishes_available }}
              </td>


              {{-- Inventory Value --}}
              <td role="cell">
                ₱{{ number_format($branch->inventory_value, 2) }}
              </td>


              {{-- Created --}}
              <td role="cell">
                {{ \Carbon\Carbon::parse($branch->created_at)->format('M j, Y') }}
              </td>

              {{-- Actions --}}
              <td role="cell">
                <div class="dropdown-wrapper">
                  <button type="button" class="more-actions" onclick="toggleDropdown(this)">
                    <span class="icon-wrapper">
                      <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-160q-33 0-56.5-23.5T400-240q0-33 23.5-56.5T480-320q33 0 56.5 23.5T560-240q0 33-23.5 56.5T480-160Zm0-240q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0-240q-33 0-56.5-23.5T400-720q0-33 23.5-56.5T480-800q33 0 56.5 23.5T560-720q0 33-23.5 56.5T480-640Z"/></svg>
                    </span>
                  </button>

                  <div class="dropdown-menu border" style="display: none;">
                    <div class="dropdown-section">
                      <button type="button" class="btn dropdown-item branch-edit-btn"
                    data-id="{{ $branch->id }}"
                    data-name="{{ $branch->name }}"
                    data-address="{{ $branch->address ?? '' }}">
                    <span class="icon-wrapper">
                      <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M160-120q-17 0-28.5-11.5T120-160v-97q0-16 6-30.5t17-25.5l505-504q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L313-143q-11 11-25.5 17t-30.5 6h-97Zm544-528 56-56-56-56-56 56 56 56Z"/></svg>
                    </span>
                    <span>Edit</span>
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

  {{-- Modals --}}
  @include('admin.branch.modals.addModal')
  @include('admin.branch.modals.editModal')
@endsection

@once
  @push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/modal.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
  <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
  <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/filters.css') }}">
  @endpush
@endonce

@once
  @push('scripts')
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/dashboard/toggleDropdown.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/dashboard/filters/tsBranchFilter.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/dashboard/modal/branchModal.js') }}" defer></script>

  @if(session('error'))
    <script>
        alert("🚨 ERROR: {{ session('error') }}");
    </script>
@endif

@if(session('success'))
    <script>
        alert("✅ SUCCESS: {{ session('success') }}");
    </script>
  @endpush
@endonce