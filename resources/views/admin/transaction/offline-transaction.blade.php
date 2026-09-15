@extends('layouts.offline')

@section('title', 'Offline Transactions')

@section('content')
  <div id="offlineSyncStatus" role="status" aria-live="polite" hidden style="position:fixed;top:1rem;right:1rem;z-index:1000;padding:.75rem 1rem;border-radius:.35rem;background:#a90d13;color:#fff;box-shadow:0 4px 14px rgba(0,0,0,.2);"></div>

  <div class="container">
    <div class="row mb-3">
      <h1 class="heading">Offline Transactions</h1>
    </div>
  </div>

  <div class="container main-content">
    <div class="row table-controls mb-3">
      <div class="searchbox">
        <span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
        </span>
        <input type="text" id="offlineTransactionSearch" class="border searchBar" placeholder="Search transactions...">
      </div>

      <div class="filters">
        <select id="offlineTransactionPayment" class="ts-filter">
          <option value="all">All Payment Methods</option>
          <option value="cash">Cash</option>
          <option value="digital">Digital</option>
        </select>
        <select id="offlineTransactionSort" class="ts-filter">
          <option value="latest" selected>Latest</option>
          <option value="oldest">Oldest</option>
        </select>
      </div>
    </div>

    <div class="container table-container border">
      <table role="table">
        <thead>
          <tr>
            <th>Receipt No.</th>
            <th>Branch</th>
            <th>Total Amount</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody id="offlineTransactionRows" role="rowgroup"></tbody>
      </table>
      <p id="offlineTransactionEmpty" class="text-muted" style="padding:1rem;text-align:center;">Loading offline transactions...</p>
    </div>
  </div>
@endsection

@once
  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/filters.css') }}">
  @endpush
@endonce

@once
  @push('scripts')
    <script src="{{ asset('js/tomSelect/tomSelect.js') }}" defer></script>
    <script src="{{ asset('js/tomSelect/tomSelectConfig.js') }}" defer></script>
    <script src="{{ asset('js/offline/pos/offlineTransactions.js') }}" defer></script>
    <script src="{{ asset('js/offline/pos/syncOfflineOrders.js') }}" defer></script>
  @endpush
@endonce
