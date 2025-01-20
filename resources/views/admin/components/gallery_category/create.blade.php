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
                    <label for="name" class="form-label">Title</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control required"
                        id="name"/>

                    @if($errors->has('name'))
                        <div class="error">{{ $errors->first('name') }}</div>
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
                <div >
                    <label for="slug" class="form-label">Slug</label>
                    <input
                        type="text"
                        name="slug"
                        class="form-control required"
                        id="slug" />
                </div>
            </div>
            @include('admin.includes.create_meta')
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
