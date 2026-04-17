@extends('layouts.admin')

@section('title', 'Archive / Trash')

@section('content')
  <div class="container">
    <div class="row mb-4">
      <h1 class="heading">Archives / Trash ({{ count($archives) }})</h1>
      <p class="text-muted">Items here will be automatically deleted after 14 days.</p>
    </div>

    <div class="container mb-3">
      <div class="row table-controls mb-3">
        <div class="searchbox">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </span>

            <input type="text" id="archiveSearch" class="border searchBar" placeholder="Search ingredients...">
        </div>

        <div class="filters">
          
        </div>
      </div>
    </div>

    <div class="container table-container border">
      <table role="table">
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Type</th>
            <th>Archived Date</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody role="rowgroup">
          @forelse($archives as $item)
          <tr role="row">
            <td role="cell"><span class="item-data">{{ $item->name }}</span></td>
            <td role="cell"><span class="badge">{{ $item->type }}</span></td>
            <td role="cell">
              <span class="item-data text-danger">{{ \Carbon\Carbon::parse($item->deleted_at)->diffForHumans() }}</span>
            </td>
            <td role="cell">
              <div class="action-btns">
                <button type="button" class="btn restore-btn">
                  <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm160-366v126q0 17 11.5 28.5T480-320q17 0 28.5-11.5T520-360v-126l36 35q11 11 27.5 11t28.5-12q11-11 11-28t-11-28L508-612q-12-12-28-12t-28 12L348-508q-11 11-11.5 27.5T348-452q11 11 27.5 11.5T404-451l36-35Z"/></svg>
                  </span>
                </button>

                <button type="button" class="btn delete-btn">
                  <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm200-284 76 76q11 11 28 11t28-11q11-11 11-28t-11-28l-76-76 76-76q11-11 11-28t-11-28q-11-11-28-11t-28 11l-76 76-76-76q-11-11-28-11t-28 11q-11 11-11 28t11 28l76 76-76 76q-11 11-11 28t11 28q11 11 28 11t28-11l76-76Z"/></svg>
                  </span>
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-muted">The trash is empty.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div id="restoreModal" class="modal" style="display: none;">
    <div class="modal-dialog">
      <form action="{{ url('/admin/archive/restore') }}" method="POST" class="modal-content">
        @csrf
        <input type="hidden" name="id" id="restore_id">
        <input type="hidden" name="table_name" id="restore_table">

        <div class="modal-header">
          <h2>Restore Item?</h2>

          <button type="button" class="btn close-btn" onclick="document.getElementById('restoreModal').style.display='none'">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
            </span>
          </button>
        </div>

        <div class="modal-body">
          <p>Are you sure you want to restore <strong id="restore_name_display"></strong> to the active system?</p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn" onclick="document.getElementById('restoreModal').style.display='none'">
            <span>Cancel</span>
          </button>

          <button type="submit" class="btn">
            <span>Yes, Restore</span>
          </button>
        </div>
      </form>
    </div>
  </div>



  <div id="forceDeleteModal" class="modal" style="display: none;">
    <div class="modal-dialog">
      <form action="{{ url('/admin/archive/force-delete') }}" method="POST" class="modal-content">
        @csrf
        @method('DELETE')
        <input type="hidden" name="id" id="delete_id">
        <input type="hidden" name="table_name" id="delete_table">

        <div class="modal-header">
          <h2 class="text-danger">Permanently Delete Item?</h2>

          <button type="button" class="btn close-btn" onclick="document.getElementById('forceDeleteModal').style.display='none'">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
            </span>
          </button>
        </div>

        <div class="modal-body">
          <p>Are you sure you want to permanently delete <strong id="delete_name_display"></strong>? This action cannot be undone.</p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn" onclick="document.getElementById('forceDeleteModal').style.display='none'">
            <span>Cancel</span>
          </button>

          <button type="submit" class="btn">
            <span>Delete Forever</span>
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

@once
  @push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin/archives/archives.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/modal.css') }}">
  <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
  <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
  @endpush
@endonce

@once
  @push('scripts')
    <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}"></script>
    <script>
      function openRestoreModal(id, tableName, name) {
        document.getElementById('restore_id').value = id;
        document.getElementById('restore_table').value = tableName;
        document.getElementById('restore_name_display').innerText = name;
        document.getElementById('restoreModal').style.display = 'flex';
      }

      function openForceDeleteModal(id, tableName, name) {
        document.getElementById('delete_id').value = id;
        document.getElementById('delete_table').value = tableName;
        document.getElementById('delete_name_display').innerText = name;
        document.getElementById('forceDeleteModal').style.display = 'flex';
      }
    </script>
  @endpush
@endonce