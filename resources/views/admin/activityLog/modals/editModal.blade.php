<div id="activityEditModal" class="modal" style="display: none;">
    <div class="modal-dialog">

        <form
            id="activityEditForm"
            method="POST"
            class="modal-content"
        >
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h2>Edit Activity Description</h2>

                <button
                    type="button"
                    class="btn close-btn"
                    onclick="document.getElementById('activityEditModal').style.display='none'"
                >
                    <span class="icon-wrapper close-modal">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="#e3e3e3">
                            <path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/>
                        </svg>
                    </span>
                </button>
            </div>

            <div class="modal-body">

                <div class="input-group">
                    <label for="activityEditDescription">
                        Description
                    </label>

                    <textarea
                        id="activityEditDescription"
                        name="description"
                        rows="6"
                        maxlength="1000"
                        required
                        style="resize: vertical;"
                    ></textarea>
                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn"
                    onclick="document.getElementById('activityEditModal').style.display='none'"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn"
                >
                    Save Changes
                </button>

            </div>

        </form>

    </div>
</div>