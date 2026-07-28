@extends('layouts.admin')

@section('title', 'Menu Categories')

@section('content')
<div class="container">
    <div class="row mb-3">
      <h1 class="heading">Menu Categories</h1>

      <div class="row heading-btn-row">
        <button id="addButton" class="btn" type="button" onclick="document.getElementById('addModal').style.display='flex'">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
                </span>
                <span>Add New Category</span>
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
            <input type="text" id="menuCategorySearch" class="border searchBar" placeholder="Search category...">
        </div>

        <div class="filters">
            <select id="sort-menuCategories" class="ts-filter">
                <option value="latest" selected>Latest</option>
                <option value="name_asc">Name: A-Z</option>
                <option value="name_desc">Name: Z-A</option>
            </select>
        </div>
    </div>

    <div class="container table-container border mb-5">
      <table role="table">
        <thead>
          <tr role="row">
            <th>Category Name</th>
            <th>Date Created</th>
            <th style="text-align: center;">Actions</th>
          </tr>
        </thead>

        <tbody role="rowgroup">
          @foreach ($categories as $category)
          <tr role="row">
            <td role="cell">{{ $category->name }}</td>
            <td role="cell">{{ $category->created_at ? \Carbon\Carbon::parse($category->created_at)->format('M d, Y') : '--'}}</td>
            <td role="cell">
              <div class="row" style="justify-content: center; align-items: center; gap: 10px;">
                <button type="button" class="btn btn-icon">
                  <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0d884e"><path d="M160-120q-17 0-28.5-11.5T120-160v-97q0-16 6-30.5t17-25.5l505-504q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L313-143q-11 11-25.5 17t-30.5 6h-97Zm544-528 56-56-56-56-56 56 56 56Z"/></svg>
                  </span>
                </button>

                <button type="button" class="btn btn-icon">
                  <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm200-284 76 76q11 11 28 11t28-11q11-11 11-28t-11-28l-76-76 76-76q11-11 11-28t-11-28q-11-11-28-11t-28 11l-76 76-76-76q-11-11-28-11t-28 11q-11 11-11 28t11 28l76 76-76 76q-11 11-11 28t11 28q11 11 28 11t28-11l76-76Z"/></svg>
                  </span>
                </button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
        
      </table>
    </div>
  </div>
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
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/dashboard/filters/tsTableFilter.js') }}"></script>
  @endpush
@endonce