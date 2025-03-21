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
            <div class="row">
                <div class="col-sm-6">
                    <div class="mb-6">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" class="form-control required" id="title" name="title">
                        <x-error key="title"/>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-check custom-checkbox mb-6">
                        <input class="form-check-input" type="checkbox" id="status" name="status"
                               value="1" checked onclick="toggleText('status', 'activeText')">
                        <label class="form-check-label" id="activeText" for="status"
                               >Active</label>
                    </div>
                </div>
            </div>
            <br>
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </div>
</form>

