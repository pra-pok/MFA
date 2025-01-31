<form action="{{ route($_base_route . '.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Create {{$_panel}}</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row g-6">
                <div class="col mb-0">
                    <label for="title" class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control required"
                        id="title"/>

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
            <div class="row ">
                <div class="mt-3">
                    <label for="icon" class="form-label">Icon</label>
                    <input
                        type="text"
                        name="icon"
                        class="form-control required"
                        id="icon" placeholder="Example: bx bx-edit-alt me-1...." />
                </div>
            </div><br>
            @include('admin.includes.create_status')
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
