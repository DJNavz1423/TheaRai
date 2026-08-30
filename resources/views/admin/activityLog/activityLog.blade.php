@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('content')
  <div class="container">
    <div class="row mb-3">
      <h1 class="heading">Activity Logs ({{ count($activityLogs) }})</h1>
    </div>
  </div>

  <div class="container main-content">
    <div class="row table-controls mb-3">
      <div class="searchbox">
        <span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
        </span>

        <input type="text" id="activitySearch" class="border searchBar" placeholder="Search activities...">
      </div>

      <div class="filters">
        <select id="filter-action" class="ts-filter">
          <option value="all" selected>All Actions</option>
          <option value="created">Created</option>
          <option value="updated">Updated</option>
          <option value="archived">Archived</option>
          <option value="restored">Restored</option>
          <option value="deleted">Deleted</option>
        </select>

        <select id="filter-model" class="ts-filter">
          <option value="all" selected>
              All Types
          </option>

          @foreach($activityLogs->pluck('model_type')->unique()->sort() as $modelType)

              <option value="{{ strtolower($modelType) }}">
                  {{ ucwords(str_replace('_', ' ', $modelType)) }}
              </option>

          @endforeach
        </select>

        <select id="filter-user" class="ts-filter">
          <option value="all" selected>
              All Users
          </option>

          @foreach($activityLogs->pluck('user_name')->filter()->unique()->sort() as $userName)

              <option value="{{ strtolower($userName) }}">
                  {{ $userName }}
              </option>

          @endforeach
        </select>

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
            <th>Model Type</th>
            <th>Action</th>
            <th>User</th>
            <th>Description</th>
            <th>Timestamp</th>
          </tr>
        </thead>

        <tbody role="rowgroup">
          @forelse($activityLogs as $log)

              @php

                  $modelType =
                      strtolower($log->model_type);

                  $action =
                      strtolower($log->action);

                  $userName =
                      strtolower(
                          $log->user_name ?? 'system'
                      );

                  $description =
                      strtolower(
                          $log->description ?? ''
                      );

                  $createdTimestamp =
                      strtotime($log->created_at);

                  $actionLabel =
                      ucfirst($action);

                  $modelLabel =
                      ucwords(
                          str_replace(
                              '_',
                              ' ',
                              $log->model_type
                          )
                      );

              @endphp


              <tr
                  role="row"
                  class="activity-row"

                  data-action="{{ $action }}"

                  data-model="{{ $modelType }}"

                  data-user="{{ $userName }}"

                  data-description="{{ $description }}"

                  data-created="{{ $createdTimestamp }}"
              >

                  {{-- Model Type --}}
                  <td role="cell">

                      <span class="badge">
                          {{ $modelLabel }}
                      </span>

                  </td>


                  {{-- Action --}}
                  <td role="cell">

                      @if($action === 'created')

                          <span
                              class="badge"
                              style="color: var(--tertiary-dark);"
                          >
                              Created
                          </span>

                      @elseif($action === 'updated')

                          <span
                              class="badge"
                              style="color: var(--quaternary);"
                          >
                              Updated
                          </span>

                      @elseif($action === 'archived')

                          <span
                              class="badge"
                              style="color: var(--danger);"
                          >
                              Archived
                          </span>

                      @elseif($action === 'restored')

                          <span
                              class="badge"
                              style="color: var(--tertiary-dark);"
                          >
                              Restored
                          </span>

                      @elseif($action === 'deleted')

                          <span
                              class="badge"
                              style="color: var(--danger);"
                          >
                              Deleted
                          </span>

                      @else

                          <span class="badge">
                              {{ $actionLabel }}
                          </span>

                      @endif

                  </td>


                  {{-- User --}}
                  <td role="cell">

                      <span class="item-data">
                          {{ $log->user_name ?? 'System' }}
                      </span>

                  </td>


                  {{-- Description --}}
                  <td role="cell">

                      <span class="item-data">

                          {{ $log->description ?? '--' }}

                      </span>

                  </td>


                  {{-- Timestamp --}}
                  <td role="cell">
                    <div class="d-flex item-group">
                      <span
                          class="item-data"
                          title="{{ \Carbon\Carbon::parse($log->created_at)->format('F j, Y g:i:s A') }}"
                      >

                          {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}

                      </span>

                      <div class="dropdown-wrapper">
                        <button type="button" class="more-actions" onclick="toggleDropdown(this)">
                          <span class="icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-160q-33 0-56.5-23.5T400-240q0-33 23.5-56.5T480-320q33 0 56.5 23.5T560-240q0 33-23.5 56.5T480-160Zm0-240q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0-240q-33 0-56.5-23.5T400-720q0-33 23.5-56.5T480-800q33 0 56.5 23.5T560-720q0 33-23.5 56.5T480-640Z"/></svg>
                          </span>
                        </button>

                        <div class="dropdown-menu border" style="display: none;">
                          <div class="dropdown-section">
                            <div class="dropdown-header">
                              <p class="text-muted">Details</p>
                            </div>

                            <button type="button" class="dropdown-item btn" onclick="openActivityDetailsModal(
                                  {{ $log->id }},
                                  '{{ $log->description ?? 'Activity Details' }}',
                                  '{{ $log->user_name ?? 'System' }}',
                                  '{{ $modelLabel }}',
                                  '{{ $actionLabel }}',
                                  '{{ \Carbon\Carbon::parse($log->created_at)->format('F j, Y g:i:s A') }}'
                              )">
                              <span class="icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M694-166q6-6 6-14v-120q0-8-6-14t-14-6q-8 0-14 6t-6 14v120q0 8 6 14t14 6q8 0 14-6Zm-14-194q8 0 14-6t6-14q0-8-6-14t-14-6q-8 0-14 6t-6 14q0 8 6 14t14 6ZM538.5-138.5Q480-197 480-280t58.5-141.5Q597-480 680-480t141.5 58.5Q880-363 880-280t-58.5 141.5Q763-80 680-80t-141.5-58.5ZM520-600h160L480-800l200 200-200-200v160q0 17 11.5 28.5T520-600ZM200-80q-33 0-56.5-23.5T120-160v-640q0-33 23.5-56.5T200-880h287q16 0 30.5 6t25.5 17l194 194q11 11 17 25.5t6 30.5v13q0 17-13.5 28t-31.5 8q-8-1-17-1.5t-18-.5q-57 0-107.5 21.5T484-480H320q-17 0-28.5 11.5T280-440q0 17 11.5 28.5T320-400h107q-9 19-15 39t-9 41h-83q-17 0-28.5 11.5T280-280q0 17 11.5 28.5T320-240h83q5 29 15 56.5t26 52.5q11 17 2.5 34T420-80H200Z"/></svg>
                              </span>
                              See Details
                            </button>
                          </div>

                          <div class="dropdown-section">
                            <div class="dropdown-header">
                              <p class="text-muted">Actions</p>
                            </div>

                            <button type="button" class="dropdown-item btn" onclick="openActivityEditModal( {{ $log->id }}, '{{ addslashes($log->description ?? '') }}')">
                              <span class="icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0d884e"><path d="M160-120q-17 0-28.5-11.5T120-160v-97q0-16 6-30.5t17-25.5l505-504q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L313-143q-11 11-25.5 17t-30.5 6h-97Zm544-528 56-56-56-56-56 56 56 56Z"/></svg>
                              </span>
                              Edit Description
                            </button>

                            <button type="button" class="dropdown-item btn dropdown-red" onclick="openActivityDeleteModal( {{ $log->id }}, '{{ $log->description ?? 'Activity Log' }}')">
                              <span class="icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm200-284 76 76q11 11 28 11t28-11q11-11 11-28t-11-28l-76-76 76-76q11-11 11-28t-11-28q-11-11-28-11t-28 11l-76 76-76-76q-11-11-28-11t-28 11q-11 11-11 28t11 28l76 76-76 76q-11 11-11 28t11 28q11 11 28 11t28-11l76-76Z"/></svg>
                              </span>
                              Remove Log
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
              </tr>

          @empty

            <tr>

                <td
                    colspan="6"
                    class="text-muted"
                    style="text-align:center; padding:20px;"
                >
                    No activity logs found.
                </td>

            </tr>

          @endforelse

        </tbody>

      </table>
    </div>
  </div>



  {{-- Modals --}}

  @include('admin.activityLog.modals.detailsModal')
  @include('admin.activityLog.modals.editModal')
  @include('admin.activityLog.modals.deleteModal')
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
  <script type="text/javascript" src="{{ asset('js/dashboard/filters/tsActivityLogsFilter.js') }}" defer></script>

  <script>
    function openActivityDetailsModal(id, description, user, model, action, timestamp){
        document.getElementById('activityDetailUser').value = user;

        document.getElementById('activityDetailModel').value = model;

        document.getElementById('activityDetailAction').value = action;

        document.getElementById('activityDetailTimestamp').value = timestamp;

        document.getElementById('activityDetailDescription').value = description;

        document.getElementById('activityDetailsModal').style.display = 'flex';
    }

    function openActivityEditModal(id, description){
        document.getElementById('activityEditForm').action = "/admin/activity-logs/" + id;

        document.getElementById('activityEditDescription').value = description;

        document.getElementById('activityEditModal').style.display = 'flex';
    }
</script>

<script>
    function openActivityDeleteModal(id, description){
        document.getElementById('activityDeleteForm').action =
            "/admin/activity-logs/" + id;

        document.getElementById('activityDeleteDescription').innerText =
            description;

        document.getElementById('activityDeleteModal')
            .style.display = 'flex';
    }
</script>

        @if(session('error'))

            <script>
                alert(
                    "🚨 ERROR: {{ session('error') }}"
                );
            </script>

        @endif


        @if(session('success'))

            <script>
                alert(
                    "✅ SUCCESS: {{ session('success') }}"
                );
            </script>

        @endif
  @endpush
@endonce