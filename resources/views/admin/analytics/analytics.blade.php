@extends('layouts.admin')

@section('title', 'Analytics')
  
@section('content')
  <div class="container">
    <div class="row mb-5">
      <h1 class="heading">TheaRai Analytics</h1>
    </div>

    <div class="kpi-grid d-grid-mb-3">
      <a class="kpi-card border">
        <div class="kpi-content">
          <h3 class="kpi-label">Total Revenue Today</h3>
          <data value="{{ $salesData->daily_total }}" class="kpi-value format-peso"></data>
          <p class="text-muted kpi-description">(Cash & E-Cash)</p>
        </div>

        <span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Zm23.5 269.5Q514-221 514-235v-15q50-9 86-39t36-89q0-42-24-77t-96-61q-60-20-83-35t-23-41q0-26 18.5-41t53.5-15q20 0 35 7t25 19q10 12 22.5 16.5t23.5-.5q15-6 20.5-20.5T606-653q-16-23-39.5-39T516-710v-15q0-14-10.5-24.5T481-760q-14 0-24.5 10.5T446-725v15q-50 11-78 44t-28 74q0 47 27.5 76t86.5 50q63 23 87.5 41t24.5 47q0 33-23.5 48.5T486-314q-26 0-47-12.5T404-364q-8-14-21-19t-26 0q-14 5-20.5 19t-.5 27q16 34 43 55.5t65 29.5v17q0 14 10.5 24.5T479-200q14 0 24.5-10.5Z"/></svg>
        </span>
      </a>

      <a class="kpi-card border">
        <div class="kpi-content">
          <h3 class="kpi-label">Today's Orders</h3>
          <data value="{{ $salesData->daily_count }}" class="kpi-value"></data>
          <p class="text-muted kpi-description">(Counter & QR)</p>
        </div>

        <span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M346-160H240q-33 0-56.5-23.5T160-240v-106l-77-78q-11-12-17-26.5T60-480q0-15 6-29.5T83-536l77-78v-106q0-33 23.5-56.5T240-800h106l78-77q12-11 26.5-17t29.5-6q15 0 29.5 6t26.5 17l78 77h106q33 0 56.5 23.5T800-720v106l77 78q11 12 17 26.5t6 29.5q0 15-6 29.5T877-424l-77 78v106q0 33-23.5 56.5T720-160H614l-78 77q-12 11-26.5 17T480-60q-15 0-29.5-6T424-83l-78-77Zm-106-80h100v-160q-26-6-43-27.5T280-477v-143q0-8 6-14t14-6q8 0 14 6t6 14v131h30v-131q0-8 6-14t14-6q8 0 14 6t6 14v131h30v-131q0-8 6-14t14-6q8 0 14 6t6 14v143q0 28-17 49.5T400-400v180l80 80 80-80v-148q-26-15-43-50.5T500-500q0-58 26-99t64-41q37 0 63.5 41t26.5 99q0 47-17 82.5T620-368v128h100v-140l100-100-100-100v-140H580L480-820 380-720H240v140L140-480l100 100v140Zm240-240Z"/></svg>
        </span>
      </a>

      <a class="kpi-card border">
        <div class="kpi-content">
          <h3 class="kpi-label">Total Active Menu Items</h3>
          <data value="{{ $activeMenuCount ?? 0 }}" class="kpi-value"></data>
          <p class="text-muted kpi-description">Available Dishes</p>
        </div>

        <span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M240-80q-33 0-56.5-23.5T160-160v-80q-17 0-28.5-11.5T120-280q0-17 11.5-28.5T160-320v-120q-17 0-28.5-11.5T120-480q0-17 11.5-28.5T160-520v-120q-17 0-28.5-11.5T120-680q0-17 11.5-28.5T160-720v-80q0-33 23.5-56.5T240-880h480q33 0 56.5 23.5T800-800v640q0 33-23.5 56.5T720-80H240Zm0-80h480v-640H240v80q17 0 28.5 11.5T280-680q0 17-11.5 28.5T240-640v120q17 0 28.5 11.5T280-480q0 17-11.5 28.5T240-440v120q17 0 28.5 11.5T280-280q0 17-11.5 28.5T240-240v80Zm140-280v130q0 13 8.5 21.5T410-280q13 0 21.5-8.5T440-310v-130q26-7 43-28.5t17-48.5v-143q0-8-6-14t-14-6q-8 0-14 6t-6 14v131h-30v-131q0-8-6-14t-14-6q-8 0-14 6t-6 14v131h-30v-131q0-8-6-14t-14-6q-8 0-14 6t-6 14v143q0 27 17 48.5t43 28.5Zm220 0v130q0 13 8.5 21.5T630-280q13 0 21.5-8.5T660-310v-347q0-11-7.5-17t-19.5-6q-13 0-28.5 7T575-652q-17 17-26 38.5t-9 46.5v87q0 17 11.5 28.5T580-440h20ZM240-160v-640 640Z"/></svg>
        </span>
      </a>

      <a class="kpi-card border">
        <div class="kpi-content">
          <h3 class="kpi-label">Low Stock Alerts</h3>
          <data value="{{ $lowStockCount ?? 0 }}" class="kpi-value"></data>
          <p class="text-muted kpi-description">Items with low stock</p>
        </div>

        <span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M109-120q-11 0-20-5.5T75-140q-5-9-5.5-19.5T75-180l370-640q6-10 15.5-15t19.5-5q10 0 19.5 5t15.5 15l370 640q6 10 5.5 20.5T885-140q-5 9-14 14.5t-20 5.5H109Zm69-80h604L480-720 178-200Zm330.5-51.5Q520-263 520-280t-11.5-28.5Q497-320 480-320t-28.5 11.5Q440-297 440-280t11.5 28.5Q463-240 480-240t28.5-11.5Zm0-120Q520-383 520-400v-120q0-17-11.5-28.5T480-560q-17 0-28.5 11.5T440-520v120q0 17 11.5 28.5T480-360q17 0 28.5-11.5ZM480-460Z"/></svg>
        </span>
      </a>
    </div>
  </div>

  <div class="container grid-content d-grid">
    <div class="col dashboard-section">
      <div class="chart-card">
        <metabase-dashboard
          token="{{ $token }}"
          with-title="false"
          with-downloads="true">
        </metabase-dashboard>
      </div>
    </div>
  </div>
@endsection

@once
  @push('styles')

  @endpush
@endonce

@once
  @push('scripts')
  <script type="text/javascript" src="{{ asset('js/utils/currency.js') }}"></script>
  <script defer src="{{ $metabaseSiteUrl }}/app/embed.js"></script>
    <script>
      function defineMetabaseConfig(config) {
        window.metabaseConfig = config;
      }
    </script>

    <script>
      defineMetabaseConfig({
        "theme": {
          "preset": "light"
        },
        "isGuest": true,
        "instanceUrl": "{{ $metabaseSiteUrl }}"
      });
    </script>
  @endpush
@endonce