<div id="activityDetailsModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Activity Details</h2>

                <button
                    type="button"
                    class="btn close-btn"
                    onclick="document.getElementById('activityDetailsModal').style.display='none'"
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

                <div class="input-group mb-3">
                    <label>User</label>

                    <input
                        type="text"
                        id="activityDetailUser"
                        readonly
                    >
                </div>

                <div class="input-group mb-3">
                    <label>Model Type</label>

                    <input
                        type="text"
                        id="activityDetailModel"
                        readonly
                    >
                </div>

                <div class="input-group mb-3">
                    <label>Action</label>

                    <input
                        type="text"
                        id="activityDetailAction"
                        readonly
                    >
                </div>

                <div class="input-group mb-3">
                    <label>Timestamp</label>

                    <input
                        type="text"
                        id="activityDetailTimestamp"
                        readonly
                    >
                </div>

                <div class="input-group mb-3">
                    <label>Description</label>

                    <textarea
                        id="activityDetailDescription"
                        readonly
                        rows="5"
                        style="resize: vertical;"
                    ></textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn"
                    onclick="document.getElementById('activityDetailsModal').style.display='none'"
                >
                    Close
                </button>
            </div>

        </div>
    </div>
</div>