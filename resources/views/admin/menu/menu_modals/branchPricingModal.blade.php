<div id="manageBranchModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form id="manageBranchForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h2>Manage <span id="manage-dish-name" style="color: var(--primary);"></span></h2>
                <button type="button" class="btn close-btn" onclick="document.getElementById('manageBranchModal').style.display='none'">
                    <span class="icon-wrapper close-modal"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg></span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Global Default Price: <strong class="format-peso" id="manage-global-price"></strong></p>
                <p class="text-muted mb-3" style="font-size: 0.85rem;">Leave a branch price blank to automatically use the Global Default Price. Uncheck the box if the dish is currently unavailable at that location.</p>
                
                <div class="container table-container modal-table border mb-1">
                    <table>
                        <thead>
                            <tr class="border-b">
                                <th class="border-r">Branch Name</th>
                                <th class="border-r" style="width: 150px;">Custom Price Override</th>
                                <th style="width: 100px; text-align: center;">Available?</th>
                            </tr>
                        </thead>
                        <tbody id="branch-pricing-list">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('manageBranchModal').style.display='none'"><span>Cancel</span></button>
                <button type="submit" class="btn"><span>Save Branch Settings</span></button>
            </div>
        </form>
    </div>
</div>