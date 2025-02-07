<form id="blockForm" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" id="block_id" value="">
    <input type="hidden" name="status" id="block_status" value="">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="userFullName"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div>
                <span id="comment"></span>
            </div>
            <div id="commentField" class="mt-3">
                <label for="comment" class="form-label">Comment</label>
                <textarea name="comment" class="form-control" id="comment" cols="10" rows="4"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="margin: 10px;">
                Close
            </button>
            <button type="submit" class="btn btn-primary">Block</button>
        </div>
    </div>
</form>
