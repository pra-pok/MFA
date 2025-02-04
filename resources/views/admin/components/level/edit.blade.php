<form id="editForm" method="POST" action="" >
    @csrf
    @method('PUT')
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit Level</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row g-6">
                <div class="col mb-0">
                    <label for="title" class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control required"
                        id="title" />

                    @if($errors->has('title'))
                        <div class="error">{{ $errors->first('title') }}</div>
                    @endif
                </div>
                <div class="col mb-0">
                    <label for="rank" class="form-label">Rank</label>
                    <input
                        type="number"
                        name="rank"
                        min="0"
                        max="100"
                        class="form-control required"
                        id="rank" />
                    @if($errors->has('rank'))
                        <div class="error">{{ $errors->first('rank') }}</div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div>
                    <label for="slug" class="form-label">Slug</label>
                    <input
                        type="text"
                        name="slug"
                        class="form-control required"
                        id="slug" />
                </div>
            </div>
            <div class="mt-3">
                <label for="meta_title" class="form-label">Meta Title</label>
                <textarea name="meta_title" class="form-control" id="meta_title" cols="3" rows="3"></textarea>
            </div>
            <div class="mt-3">
                <label for="meta_keywords" class="form-label">Meta Keyword</label>
                <textarea name="meta_keywords" class="form-control" id="meta_keywords"  rows="3"></textarea>
            </div>
            <div class="mt-3">
                <label for="meta_description" class="form-label">Meta Description</label>
                <textarea name="meta_description" class="form-control" id="meta_description" cols="3" rows="3"></textarea>
            </div><br>
            <div class="mt-3">
                <label for="status" class="form-label">Status</label>

                <input
                    name="status"
                    class="form-check-input"
                    type="radio"
                    value="1"
                    id="activeStatus"
                />
                <label class="form-check-label" for="activeStatus"> Active </label>

                <input
                    name="status"
                    class="form-check-input"
                    type="radio"
                    value="0"
                    id="deactiveStatus"
                />
                <label class="form-check-label" for="deactiveStatus"> In-Active </label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="margin: 10px;">
                Close
            </button>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </div>
</form>
