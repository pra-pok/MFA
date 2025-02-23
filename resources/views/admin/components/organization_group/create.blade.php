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
            <div class="mt-3">
                <label for="title" class="form-label">Title</label>
                <input
                    type="text"
                    name="title"
                    class="form-control required"
                    id="title"/>
            </div>
            <div class="mt-3 ">
                <label for="rank" class="form-label">Rank</label>
                <input
                    type="number"
                    name="rank"
                    class="form-control"
                    min="0"
                    max="100"
                    id="rank"/>
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
