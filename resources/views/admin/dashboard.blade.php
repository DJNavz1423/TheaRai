@extends('layouts.admin')

@section('content')
<section id="dashboard">
  <div class="container">
    <div class="row mb-5">
      <h1 class="heading">Welcome back Admin</h1>

      <div class="quick-btn-row row">
        <button class="quick-btn btn"><a href="#"><span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-640q-33 0-56.5-23.5T200-720v-80q0-33 23.5-56.5T280-880h400q33 0 56.5 23.5T760-800v80q0 33-23.5 56.5T680-640H280Zm40-80h320q16 0 22.5-14.5T680-760q0-17-11.5-28.5T640-800H320q-17 0-28.5 11.5T280-760q11 11 17.5 25.5T320-720ZM160-80q-33 0-56.5-23.5T80-160v-40h800v40q0 33-23.5 56.5T800-80H160ZM80-240l139-313q10-22 30-34.5t43-12.5h376q23 0 43 12.5t30 34.5l139 313H80Zm260-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 160h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 160h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Z"/></svg>
          </span> <span>Quick POS</span>
          </a>
        </button>

        <button class="quick-btn btn"><a href="#"><span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
          </span> <span>Add Sales</span>
          </a>
        </button>

        <button class="quick-btn btn"><a href="#"><span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
          </span><span>Add Purchases</span>
        </a>
        </button>

        <button class="quick-btn btn"><a href="#"><span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M120-480q0-150 105-255t255-105v-40q0-12 11-18t21 2l125 94q16 12 16 32t-16 32l-125 94q-10 8-21 2t-11-18v-40q-91 0-155.5 64.5T260-480q0 33 9.5 63.5T296-360q11 16 9 34.5T288-296l-34 25q-18 14-40 11t-35-22q-29-43-44-93t-15-105Zm360 360v40q0 12-11 18t-21-2l-125-94q-16-12-16-32t16-32l125-94q10-8 21-2t11 18v40q91 0 155.5-64.5T700-480q0-33-9.5-63.5T664-600q-11-16-9-34.5t17-29.5l34-25q18-14 40-10.5t35 21.5q28 43 43.5 93T840-480q0 150-105 255T480-120Z"/></svg>
        </span><span>Restock Items</span>
        </a></button>
      </div>
    </div>



    <!-- KPI CARDS-->

    <div class="kpi-grid d-grid mb-3">
      <a class="kpi-card border">
        <div class="kpi-content">
          <h3 class="kpi-label">Inventory Value</h3>
          <data value="{{ $totalInventoryValue ?? 0 }}" class="kpi-value">&#8369;{{ number_format($totalInventoryValue ?? 0, 2) }}</data>
          <p class="text-muted kpi-description">Total stock value</p>
        </div>

        <span class="icon-wrapper kpi-icon">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M336-120q-91 0-153.5-62.5T120-336q0-38 13-74t37-65l142-171-68-136q-10-20 1.5-39t34.5-19h400q23 0 34.5 19t1.5 39l-68 136 142 171q24 29 37 65t13 74q0 91-63 153.5T624-120H336Zm144-200q-33 0-56.5-23.5T400-400q0-33 23.5-56.5T480-480q33 0 56.5 23.5T560-400q0 33-23.5 56.5T480-320Zm-95-360h190l40-80H345l40 80Zm-49 480h288q57 0 96.5-39.5T760-336q0-24-8.5-46.5T728-423L581-600H380L232-424q-15 18-23.5 41t-8.5 47q0 57 39.5 96.5T336-200Z"/></svg>
        </span>
      </a>

      <a class="kpi-card border">
        <div class="kpi-content">
          <h3 class="kpi-label">Sales <span class="month">({{ now()->format('F') }})</span></h3>
            <data value="0" class="kpi-value">&#8369;0.00</data>
            <p class="text-muted kpi-description">Sales this month</p>
        </div>

        <span class="icon-wrapper kpi-icon">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Zm23.5 269.5Q514-221 514-235v-15q50-9 86-39t36-89q0-42-24-77t-96-61q-60-20-83-35t-23-41q0-26 18.5-41t53.5-15q20 0 35 7t25 19q10 12 22.5 16.5t23.5-.5q15-6 20.5-20.5T606-653q-16-23-39.5-39T516-710v-15q0-14-10.5-24.5T481-760q-14 0-24.5 10.5T446-725v15q-50 11-78 44t-28 74q0 47 27.5 76t86.5 50q63 23 87.5 41t24.5 47q0 33-23.5 48.5T486-314q-26 0-47-12.5T404-364q-8-14-21-19t-26 0q-14 5-20.5 19t-.5 27q16 34 43 55.5t65 29.5v17q0 14 10.5 24.5T479-200q14 0 24.5-10.5Z"/></svg>
        </span>
      </a>

      <a class="kpi-card border">
        <div class="kpi-content">
          <h3 class="kpi-label">Purchases <span class="month">({{ now()->format('F') }})</span></h3>
          <data value="0" class="kpi-value">&#8369;0.00</data>
          <p class="text-muted kpi-description">Purchases this month</p>
        </div>

        <span class="icon-wrapper kpi-icon">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M486-314q33 0 56.5-15.5T566-378q0-29-24.5-47T454-466q-59-21-86.5-50T340-592q0-41 28.5-74.5T446-710v-15q0-14 10.5-24.5T481-760q14 0 24.5 10.5T516-725v15q29 2 53.5 19.5T609-648q7 11 1 23.5T590-607q-13 5-26 1t-21-15q-10-12-25-19.5t-36-7.5q-35 0-53.5 15T410-592q0 26 23 41t83 35q72 26 96 61t24 77q0 29-10 51t-26.5 37.5Q583-274 561-264.5T514-250v15q0 14-10.5 24.5T479-200q-14 0-24.5-10.5T444-235v-17q-38-8-65-30t-43-56q-6-14 .5-27t20.5-18q13-5 26 .5t20 17.5q14 26 35.5 38.5T486-314Zm-6 274q-112 0-206-51T120-227v67q0 17-11.5 28.5T80-120q-17 0-28.5-11.5T40-160v-160q0-17 11.5-28.5T80-360h160q17 0 28.5 11.5T280-320q0 17-11.5 28.5T240-280h-59q48 72 126.5 116T480-120q141 0 242.5-94T838-445q2-16 14-25.5t28-9.5q17 0 29 10.5t10 25.5q-7 85-44 158.5t-96 128q-59 54.5-135.5 86T480-40Zm0-800q-141 0-242.5 94T122-515q-2 16-14 25.5T80-480q-17 0-29-10.5T41-516q7-85 44-158.5t96-128q59-54.5 135.5-86T480-920q112 0 206 51t154 136v-67q0-17 11.5-28.5T880-840q17 0 28.5 11.5T920-800v160q0 17-11.5 28.5T880-600H720q-17 0-28.5-11.5T680-640q0-17 11.5-28.5T720-680h59q-48-72-126.5-116T480-840Z"/></svg>
        </span>
      </a>

      <a class="kpi-card border">
        <div class="kpi-content">
          <h3 class="kpi-label">Today's Order</h3>
          <data value="0" class="kpi-value">0</data>
          <p class="text-muted kpi-description">Total: &#8369;0.00</p>
        </div>

        <span class="icon-wrapper kpi-icon">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M346-160H240q-33 0-56.5-23.5T160-240v-106l-77-78q-11-12-17-26.5T60-480q0-15 6-29.5T83-536l77-78v-106q0-33 23.5-56.5T240-800h106l78-77q12-11 26.5-17t29.5-6q15 0 29.5 6t26.5 17l78 77h106q33 0 56.5 23.5T800-720v106l77 78q11 12 17 26.5t6 29.5q0 15-6 29.5T877-424l-77 78v106q0 33-23.5 56.5T720-160H614l-78 77q-12 11-26.5 17T480-60q-15 0-29.5-6T424-83l-78-77Zm-106-80h100v-160q-26-6-43-27.5T280-477v-143q0-8 6-14t14-6q8 0 14 6t6 14v131h30v-131q0-8 6-14t14-6q8 0 14 6t6 14v131h30v-131q0-8 6-14t14-6q8 0 14 6t6 14v143q0 28-17 49.5T400-400v180l80 80 80-80v-148q-26-15-43-50.5T500-500q0-58 26-99t64-41q37 0 63.5 41t26.5 99q0 47-17 82.5T620-368v128h100v-140l100-100-100-100v-140H580L480-820 380-720H240v140L140-480l100 100v140Zm240-240Z"/></svg>
        </span>
      </a>

      <a class="kpi-card border">
        <div class="kpi-content">
          <h3 class="kpi-label">Low Stock Alerts</h3>
          <data value="{{ $lowStockCount ?? 0 }}" class="kpi-value">{{ $lowStockCount ?? 0 }}</data>
          <p class="text-muted kpi-description">Items with low stock</p>
        </div>

        <span class="icon-wrapper kpi-icon">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M109-120q-11 0-20-5.5T75-140q-5-9-5.5-19.5T75-180l370-640q6-10 15.5-15t19.5-5q10 0 19.5 5t15.5 15l370 640q6 10 5.5 20.5T885-140q-5 9-14 14.5t-20 5.5H109Zm69-80h604L480-720 178-200Zm330.5-51.5Q520-263 520-280t-11.5-28.5Q497-320 480-320t-28.5 11.5Q440-297 440-280t11.5 28.5Q463-240 480-240t28.5-11.5Zm0-120Q520-383 520-400v-120q0-17-11.5-28.5T480-560q-17 0-28.5 11.5T440-520v120q0 17 11.5 28.5T480-360q17 0 28.5-11.5ZM480-460Z"/></svg>
        </span>
      </a>
    </div>
  </div>


  <!-- charts and tables | Chart section -->
  <div class="container">
    <div class="row">
      <!-- charts column -->
      <div class="col">
        
      </div>

      <div class="col"></div>
    </div>
  </div>
</section>
@endsection

@once
  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
  @endpush
@endonce