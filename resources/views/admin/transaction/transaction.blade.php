@extends($layout)

@section('title', 'Transactions')

@section('content')
  <div class="container">
    <div class="row mb-3">
      <h1 class="heading">Sales Transactions</h1>
    </div>
  </div>

  <div class="container main-content">
    <div class="row table-controls mb-3">
      <div class="searchbox">
        <span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
        </span>

        <input type="text" id="salesSearch" class="border searchBar" placeholder="Search transactions...">
      </div>

      <div class="filters">
        {{-- Branch --}}
        @if($isAdminLevel)
          <select id="filter-branch" class="ts-filter">
              <option value="all">All Branches</option>
              @foreach($branches as $branch)
                  <option value="{{ $branch->id }}">
                      {{ $branch->name }}
                  </option>
              @endforeach
          </select>
        @endif

        {{-- Payment Method --}}
        <select id="filter-payment" class="ts-filter">
          <option value="all">All Payment Methods</option>
            @foreach($paymentMethods as $paymentMethod)
              <option value="{{ strtolower($paymentMethod) }}">
                {{ ucwords($paymentMethod) }}
              </option>
            @endforeach
        </select>
          

        {{-- Payment Status --}}
        <select id="filter-payment-status" class="ts-filter">
          <option value="all">All Payment Status</option>
            @foreach($paymentStatuses as $paymentStatus)
              <option value="{{ strtolower($paymentStatus) }}">
                {{ ucwords($paymentStatus) }}
              </option>
            @endforeach
        </select>

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
            <th>Receipt No.</th>
            <th>Branch</th>
            <th>Total Amount</th>
            <th>Payment Method</th>
            <th>Payment Status</th>
            <th>Date</th>
          </tr>
        </thead>

        <tbody role="rowgroup">
          @foreach($transactions as $transaction)
            <tr class="transaction-row" 
              data-receipt="{{ strtolower($transaction->receipt_no) }}"
              data-branch-id="{{ $transaction->branch_id }}"
              data-branch="{{ strtolower($transaction->branch_name ?? '') }}"
              data-amount="{{ number_format($transaction->total_amount, 2) }}"
              data-payment="{{ strtolower($transaction->payment_method ?? '') }}"
              data-payment-status="{{ strtolower($transaction->payment_status ?? '') }}"
              data-date="{{ strtolower(\Carbon\Carbon::parse($transaction->created_at)->format('M j, Y g:i A')) }}"
              data-created="{{ \Carbon\Carbon::parse($transaction->created_at)->timestamp }}">

                <td role="cell">{{ $transaction->receipt_no }}</td>

                <td role="cell">{{ $transaction->branch_name ?? 'Unknown Branch' }}</td>

                <td role="cell">₱{{ number_format($transaction->total_amount, 2) }}</td>

                <td role="cell">{{ ucwords($transaction->payment_method ?? 'Unknown') }}</td>

                <td role="cell">{{ ucwords($transaction->payment_status ?? 'Unknown') }}</td>

                <td role="cell">
                  <div class="d-flex item-group">
                    <span class="item-data">
                      {{ \Carbon\Carbon::parse($transaction->created_at)->format('M j, Y g:i A') }}
                    </span>

                    <div class="dropdown-wrapper">
                      <button type="button" class="more-actions" onclick="toggleDropdown(this)">
                        <span class="icon-wrapper">
                          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-160q-33 0-56.5-23.5T400-240q0-33 23.5-56.5T480-320q33 0 56.5 23.5T560-240q0 33-23.5 56.5T480-160Zm0-240q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0-240q-33 0-56.5-23.5T400-720q0-33 23.5-56.5T480-800q33 0 56.5 23.5T560-720q0 33-23.5 56.5T480-640Z"/></svg>
                        </span>
                      </button>

                      <div class="dropdown-menu border" style="display: none;">
                        <div class="dropdown-section">
                          <button type="button" class="btn dropdown-item transaction-details-btn"
                          data-id="{{ $transaction->id }}"
                          data-url="{{ $isAdminLevel ? route('admin.transactions.show', $transaction->id) : route('cashier.transactions.show', $transaction->id) }}">
                          <span class="icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M694-166q6-6 6-14v-120q0-8-6-14t-14-6q-8 0-14 6t-6 14v120q0 8 6 14t14 6q8 0 14-6Zm-14-194q8 0 14-6t6-14q0-8-6-14t-14-6q-8 0-14 6t-6 14q0 8 6 14t14 6ZM538.5-138.5Q480-197 480-280t58.5-141.5Q597-480 680-480t141.5 58.5Q880-363 880-280t-58.5 141.5Q763-80 680-80t-141.5-58.5ZM520-600h160L480-800l200 200-200-200v160q0 17 11.5 28.5T520-600ZM200-80q-33 0-56.5-23.5T120-160v-640q0-33 23.5-56.5T200-880h287q16 0 30.5 6t25.5 17l194 194q11 11 17 25.5t6 30.5v13q0 17-13.5 28t-31.5 8q-8-1-17-1.5t-18-.5q-57 0-107.5 21.5T484-480H320q-17 0-28.5 11.5T280-440q0 17 11.5 28.5T320-400h107q-9 19-15 39t-9 41h-83q-17 0-28.5 11.5T280-280q0 17 11.5 28.5T320-240h83q5 29 15 56.5t26 52.5q11 17 2.5 34T420-80H200Z"/></svg>
                          </span>
                        <span>View Details</span>
                    </button>

                    @if($transaction->payment_status !== 'refunded' && \Carbon\Carbon::parse($transaction->created_at)->addMinutes(30)->greaterThan(now()))
                    <button type="button" class="btn dropdown-item dropdown-red transaction-refund-btn"
                    data-id="{{ $transaction->id }}"
                    data-receipt="{{ $transaction->receipt_no }}"
                    data-amount="{{ $transaction->total_amount }}"
                    data-url="{{ $isAdminLevel
                        ? route('admin.transactions.refund', $transaction->id)
                        : route('cashier.transactions.refund', $transaction->id) }}">
                      <span class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M486-314q33 0 56.5-15.5T566-378q0-29-24.5-47T454-466q-59-21-86.5-50T340-592q0-41 28.5-74.5T446-710v-15q0-14 10.5-24.5T481-760q14 0 24.5 10.5T516-725v15q29 2 53.5 19.5T609-648q7 11 1 23.5T590-607q-13 5-26 1t-21-15q-10-12-25-19.5t-36-7.5q-35 0-53.5 15T410-592q0 26 23 41t83 35q72 26 96 61t24 77q0 29-10 51t-26.5 37.5Q583-274 561-264.5T514-250v15q0 14-10.5 24.5T479-200q-14 0-24.5-10.5T444-235v-17q-38-8-65-30t-43-56q-6-14 .5-27t20.5-18q13-5 26 .5t20 17.5q14 26 35.5 38.5T486-314Zm-6 274q-112 0-206-51T120-227v67q0 17-11.5 28.5T80-120q-17 0-28.5-11.5T40-160v-160q0-17 11.5-28.5T80-360h160q17 0 28.5 11.5T280-320q0 17-11.5 28.5T240-280h-59q48 72 126.5 116T480-120q141 0 242.5-94T838-445q2-16 14-25.5t28-9.5q17 0 29 10.5t10 25.5q-7 85-44 158.5t-96 128q-59 54.5-135.5 86T480-40Zm0-800q-141 0-242.5 94T122-515q-2 16-14 25.5T80-480q-17 0-29-10.5T41-516q7-85 44-158.5t96-128q59-54.5 135.5-86T480-920q112 0 206 51t154 136v-67q0-17 11.5-28.5T880-840q17 0 28.5 11.5T920-800v160q0 17-11.5 28.5T880-600H720q-17 0-28.5-11.5T680-640q0-17 11.5-28.5T720-680h59q-48-72-126.5-116T480-840Z"/></svg>
                      </span>
                      <span>Refund Transaction</span>
                    </button>
                    @endif

                        </div>
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
  @include('admin.transaction.modals.detailsModal')
  @include('admin.transaction.modals.refundModal')
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
  <link rel="stylesheet" href="{{ asset('css/admin/transaction/transactionDetails.css') }}">
  @endpush
@endonce

@once
  @push('scripts')
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/dashboard/toggleDropdown.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/dashboard/filters/tsTransactionFilter.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/dashboard/modal/transactionModal.js') }}" defer></script>
  @endpush
@endonce