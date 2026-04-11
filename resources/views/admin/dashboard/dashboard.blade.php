@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
  <div class="container">
    <div class="row mb-3">
      <h1 class="heading">Welcome back Admin</h1>

      <div class="quick-btn-row row">
        <a href="{{ url('/cashier/pos') }}" target="_blank" class="quick-btn btn">
          <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-640q-33 0-56.5-23.5T200-720v-80q0-33 23.5-56.5T280-880h400q33 0 56.5 23.5T760-800v80q0 33-23.5 56.5T680-640H280Zm40-80h320q16 0 22.5-14.5T680-760q0-17-11.5-28.5T640-800H320q-17 0-28.5 11.5T280-760q11 11 17.5 25.5T320-720ZM160-80q-33 0-56.5-23.5T80-160v-40h800v40q0 33-23.5 56.5T800-80H160ZM80-240l139-313q10-22 30-34.5t43-12.5h376q23 0 43 12.5t30 34.5l139 313H80Zm260-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 160h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 160h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Z"/></svg>
          </span> <span>Quick POS</span>
          </a>

        <a href="#" target="_blank" class="quick-btn btn">
          <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
          </span><span>Add Expenses</span>
        </a>
        
        <a href="{{ url('/admin/inventory') }}" target="_blank" class="quick-btn btn">
          <span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M120-480q0-150 105-255t255-105v-40q0-12 11-18t21 2l125 94q16 12 16 32t-16 32l-125 94q-10 8-21 2t-11-18v-40q-91 0-155.5 64.5T260-480q0 33 9.5 63.5T296-360q11 16 9 34.5T288-296l-34 25q-18 14-40 11t-35-22q-29-43-44-93t-15-105Zm360 360v40q0 12-11 18t-21-2l-125-94q-16-12-16-32t16-32l125-94q10-8 21-2t11 18v40q91 0 155.5-64.5T700-480q0-33-9.5-63.5T664-600q-11-16-9-34.5t17-29.5l34-25q18-14 40-10.5t35 21.5q28 43 43.5 93T840-480q0 150-105 255T480-120Z"/></svg>
        </span><span>Restock Items</span>
        </a>
      </div>
    </div>



    <!-- KPI CARDS-->

    @include('admin.dashboard.dashboardKPI')
  </div>


  <!-- charts and tables | Chart section -->
  <div class="container grid-content d-grid mt-4">
      <!-- charts column -->
      <div class="col dashboard-section">
        <div class="section-content border">
          <div class="section-header mb-1">
          <h2>Cashflow Overview</h2>

          <div class="input-group">
          <select name="chart-timeframe" id="chart-timeframe" class="unit-selector">
            <option value="{{ $dailyToken }}">Daily</option>
            <option value="{{ $monthlyToken }}">Monthly</option>
          </select>
        </div>
        </div>

        <div class="chart-card">
          <metabase-question
            id="mb-chart"
            token="{{ $dailyToken }}"
            with-title="false"
            with-downloads="true">
          </metabase-question>
        </div>

        <div class="row chart-details">
          <div class="chart-legends">
            <div class="total-group">
            <div class="row">
              <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>
            </span>
            <span>Total Money In</span>
            </div>
            
            <data value="{{ $totalMoneyIn ?? 0 }}" class="format-peso"></data>
          </div>

          <div class="total-group">
            <div class="row">
              <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>
            </span>
            <span>Total Money Out</span>
            </div>
            
            <data value="{{ $totalMoneyOut ?? 0 }}" class="format-peso"></data>
          </div>
          </div>
        </div>
        </div>
        
      </div>

      
      <div class="col dashboard-section">
        <div class="section-content border">
          <div class="section-header mb-1">
            <h3 class="text-muted"><p>Total Balance (Cash):</p> </h3>
          </div>
          <div class="total-balance">
            <data value="{{ $currentCashBalance ?? 0 }}" class="format-peso"></data>
        </div>
        </div>

        <div class="section-content border">
          <div class="section-header">
          <h2>Low Stock Items</h2>
          <a href="#" class="view-all-link">Manage <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M727-440H120q-17 0-28.5-11.5T80-480q0-17 11.5-28.5T120-520h607L572-676q-11-11-11.5-27.5T572-732q11-11 28-11t28 11l224 224q6 6 8.5 13t2.5 15q0 8-2.5 15t-8.5 13L628-228q-11 11-27.5 11T572-228q-12-12-12-28.5t12-28.5l155-155Z"/></svg>
          </span>
          </a>
        </div>

        <div class="alert-list list">
          @if($lowStockItems->isEmpty())
          <div class="empty-content">
            <p class="text-muted">There are no low stock items currently.</p>
          </div>
            
          @else
            @foreach($lowStockItems as $item)
              <div class="alert-item list-item {{ $item->stock_quantity == 0 ? 'out-of-stock-item' : 'low-stock-item' }}">
               
                <div class="alert-item-details item-info">
                   <div class="item-name">{{ $item->name }}</div>
                  <div class="item-meta">
                    <span class="stock-qty {{ $item->stock_quantity == 0 ? 'out-of-stock' : 'low-stock' }}">{{ $item->stock_quantity }} {{$item->primary_unit_abbr}}</span>
                  <data value="{{ $item->purchase_price }}" class="stock-price format-peso">{{ $item->purchase_price }}</data>
                  </div>
                </div>
              

              @if($item->stock_quantity == 0)
              <span id="no-stock" class="icon-wrapper alert-status">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M508.5-291.5Q520-303 520-320t-11.5-28.5Q497-360 480-360t-28.5 11.5Q440-337 440-320t11.5 28.5Q463-280 480-280t28.5-11.5Zm0-160Q520-463 520-480v-160q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640v160q0 17 11.5 28.5T480-440q17 0 28.5-11.5ZM346-160H240q-33 0-56.5-23.5T160-240v-106l-77-78q-11-12-17-26.5T60-480q0-15 6-29.5T83-536l77-78v-106q0-33 23.5-56.5T240-800h106l78-77q12-11 26.5-17t29.5-6q15 0 29.5 6t26.5 17l78 77h106q33 0 56.5 23.5T800-720v106l77 78q11 12 17 26.5t6 29.5q0 15-6 29.5T877-424l-77 78v106q0 33-23.5 56.5T720-160H614l-78 77q-12 11-26.5 17T480-60q-15 0-29.5-6T424-83l-78-77Zm34-80 100 100 100-100h140v-140l100-100-100-100v-140H580L480-820 380-720H240v140L140-480l100 100v140h140Zm100-240Z"/></svg>
              </span>

              @else
              <span id="yes-stock" class="icon-wrapper alert-status">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M109-120q-11 0-20-5.5T75-140q-5-9-5.5-19.5T75-180l370-640q6-10 15.5-15t19.5-5q10 0 19.5 5t15.5 15l370 640q6 10 5.5 20.5T885-140q-5 9-14 14.5t-20 5.5H109Zm69-80h604L480-720 178-200Zm330.5-51.5Q520-263 520-280t-11.5-28.5Q497-320 480-320t-28.5 11.5Q440-297 440-280t11.5 28.5Q463-240 480-240t28.5-11.5Zm0-120Q520-383 520-400v-120q0-17-11.5-28.5T480-560q-17 0-28.5 11.5T440-520v120q0 17 11.5 28.5T480-360q17 0 28.5-11.5ZM480-460Z"/></svg>
              </span>
              @endif
              </div>
            @endforeach
           @endif
        </div>
        </div>
        
      </div>
    
      <div class="dashboard-section">
        <div class="section-content border">
          <div class="section-header">
          <h2>Recent Activities</h2>
        </div>

        <div class="activity-list list">
          @if($recentActivities->isEmpty())
          <div class="empty-content">
            <p class="text-muted">There are no recent activities today yet. Check back after some time.</p>
          </div>
          @else
            @foreach($recentActivities as $activity)
              <div class="activity-item list-item">
                <span class="icon-wrapper">

                </span>
              

              <div class="activity-info item-info">
                <div class="activity-name">
                {{ ucfirst($activity->action) }} {{ ucwords(str_replace('_', ' ', $activity->model_type)) }}
                </div>

                <span class="activity-details text-muted">
                  {{ \Illuminate\Support\Str::limit($activity->description, 50)}} - {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                </span>
              </div>
              </div>
            @endforeach
          @endif
        </div>
        </div>
      </div>


      <div class="dashboard-section">
        <div class="section-content border">
          <div class="section-header">
          <h2>Recent Transactions</h2>
        </div>
        
        <div class="transaction-list list">
          @if($recentTransactions->isEmpty())
          <div class="empty-content">
            <p class="text-muted">There are no recent transactions today yet. Check back after some time.</p>
          </div>

          @else
            @foreach($recentTransactions as $transaction)
              <div class="activity-item list-item">
                <span class="icon-wrapper">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M160-240v-436L98-810q-7-15-1-30.5t21-22.5q15-7 30.5-1.5T171-844l77 166h464l77-166q7-15 22.5-21t30.5 2q15 7 21 22.5t-1 30.5l-62 134v436q0 33-23.5 56.5T720-160H240q-33 0-56.5-23.5T160-240Zm240-200h160q17 0 28.5-11.5T600-480q0-17-11.5-28.5T560-520H400q-17 0-28.5 11.5T360-480q0 17 11.5 28.5T400-440ZM240-240h480v-358H240v358Zm0 0v-358 358Z"/></svg>
                </span>

                <div class="activity-info">
                  <span class="activity-name">Order #{{ $transaction->id }} ({{ ucfirst($transaction->payment_method) }})</span>
                  <span class="activity-details text-muted">
                    <data value="{{ $transaction->total_amount }}" class="format-peso"></data>
                    - {{ \Carbon\Carbon::parse($transaction->created_at)->diffForHumans() }}
                  </span>
                </div>
              </div>
            @endforeach
          @endif
        </div>
      </div>
        </div>
  </div>
@endsection

@once
  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard/dashboard.css') }}">
  @endpush
@endonce

@once 
  @push('scripts')
    <script type="text/javascript" src="{{ asset('js/utils/currency.js') }}"></script>
    <script defer src="{{ $metabaseSiteUrl }}/app/embed.js"></script>
    
    <script>
      function defineMetabaseConfig(config){
        window.metabaseConfig = config;
      }
    </script>

    <script>
      defineMetabaseConfig({
        "theme": {"preset": "light"},
        "isGuest": true,
        "instanceUrl": "{{ $metabaseSiteUrl }}"
      });
    </script>

    <script>
      document.addEventListener('DOMContentLoaded', function(){
        const timeframeSelect = document.getElementById('chart-timeframe');
        const metabaseChart = document.getElementById('mb-chart');

        timeframeSelect.addEventListener('change', function(){
          metabaseChart.setAttribute('token', this.value);
        });
      });
    </script>
  @endpush
@endonce