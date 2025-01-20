<form action="{{ route($_base_route . '.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">Create {{$_panel}}</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row g-6">
                <div class="col mb-0">
                    <label for="country_id" class="form-label">Country</label>
                    <select class="form-select" id="country_id" name="country_id" required>
                        <option value="">None</option>
                        @foreach($data['country'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col mb-0">
                    <label for="parent_id" class="form-label">Parent</label>
                    <select class="form-select" id="parent_id" name="parent_id">
                        <option value="">None</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col mb-6">
                    <label for="name" class="form-label">Name</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        id="name" required/>
                </div>
            </div>
            <div class="row g-6">
                <div class="col-md-8">
                    <label for="slug" class="form-label">Slug</label>
                    <input
                        type="text"
                        name="slug"
                        class="form-control"
                        id="slug" required/>
                </div>
                <div class="col-md-4">
                    <label for="rank" class="form-label">Rank</label>
                    <input
                        type="number"
                        name="rank"
                        class="form-control"
                        min="0"
                        max="100"
                        id="rank" />
                </div>
            </div>
            <br>
            <div>
                <label for="status" class="form-label">Status</label>

                <input
                    name="status"
                    class="form-check-input"
                    type="radio"
                    value="1"
                    id="activeStatus"
                    checked
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
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        style="margin: 10px;">
                    Close
                </button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </div>
    </div>
</form>
