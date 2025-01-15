<div class="mt-4">
    <!-- Button trigger modal -->
    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Edit {{$_panel}}</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-6">
                                <label for="name" class="form-label">Administrative Area Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    id="name"
                                    placeholder="Enter The Administrative Area Name"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-6">
                                <label for="slug" class="form-label">Slug</label>
                                <input
                                    type="text"
                                    name="slug"
                                    class="form-control"
                                    id="slug" placeholder="slug" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-6">
                                <label for="rank" class="form-label">Rank</label>
                                <input
                                    type="number"
                                    name="rank"
                                    class="form-control"
                                    id="rank" placeholder="Enter number i.e. ( 1,2,3...)" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-6">
                                <label for="parent_id" class="form-label">Parent:</label>
                                <select class="form-select" name="parent_id">
                                    <option value="">None</option>
                                    @foreach($data['parents'] as $key => $value)
                                        <option value="{{ $key }}" >
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-6">

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
                                <label class="form-check-label" for="deactiveStatus"> De-Active </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="margin: 10px;">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
