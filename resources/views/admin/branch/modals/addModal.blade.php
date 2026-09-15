<div id="addModal" class="modal" style="display: none;">
  <div class="modal-dialog">
    <form action="{{ route('admin.branches.store') }}" method="POST" class="modal-content">
      @csrf

    <div class="modal-header">
      <h2>Add New Branch</h2>

      <button type="button" class="btn close-btn" onclick="document.getElementById('addModal').style.display='none'">
        <span class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
          </svg>
        </span>
      </button>
    </div>

      <div class="modal-body">
        <div class="row">
          <div class="input-group">
          <label for="branch_name">
            Branch Name
          </label>

          <input type="text" id="branch_name" name="name" class="border" placeholder="Enter branch name" value="{{ old('name') }}" required
          >
        </div>
        </div>
        
        <div class="row">
          <div class="input-group">
          <label for="branch_address">
            Address
          </label>

          <textarea
            id="branch_address"
            name="address"
            class="border"
            placeholder="Enter branch address"
            rows="3"
          >{{ old('address') }}</textarea>
        </div>
        </div>
      </div>


      <div class="modal-footer">

        <button
          type="button"
          class="btn"
          onclick="document.getElementById('addModal').style.display='none'"
        >
          Cancel
        </button>

        <button
          type="submit"
          class="btn"
        >
          Add Branch
        </button>

      </div>

    </form>

  </div>

</div>