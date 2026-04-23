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


  @include('admin.dashboard.dashboardChartsTables')
@endsection

@once
  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
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