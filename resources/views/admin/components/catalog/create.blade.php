<form id="myForm" action="{{ route($_base_route . '.store') }}" method="POST" enctype="multipart/form-data">
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
                <div class="col-sm-6">
                    <div class="mb-6">
                        <label class="form-label" for="rank">Rank</label>
                        <input type="number" class="form-control" id="rank" name="rank"
                               min="0" max="100">
                        <x-error key="rank"/>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-6">
                        <label class="form-label" for="data">Limit</label>
                        <input type="number" class="form-control" id="data" name="data"
                               min="0" max="1000">
                        <x-error key="data"/>
                    </div>
                </div>
                <div class="col-md-5">
                    <label for="role" class="form-label">Type</label>
                    <select class="form-select required" id="type" name="type" aria-label="Type">
                        <option selected disabled>Type</option>
                        @foreach ($data['type'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
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
            <button type="button" class="btn btn-primary">Create
            </button>
        </div>
    </div>
</form>
