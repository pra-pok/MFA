<form action="{{ route('organization-course.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="organization_id" value="{{ $data['record']->id ?? '' }}"/>
    <div id="form-rows">
        <div class="row form-row">
            <div class="col-md-4">
                <label for="course_id">Select Course</label>
                <select class="form-control select-course required" name="course_id[]" aria-label="Select Course">
                    <option selected disabled>Select Course</option>
                    @foreach ($data['courses'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="start_fee" class="form-label">Min Fee Range</label>
                    <input type="number" class="form-control" name="start_fee[]" step="0.01"
                           placeholder="Enter minimum fee"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="end_fee" class="form-label">Max Fee Range</label>
                    <input type="number" class="form-control" name="end_fee[]" step="0.01"
                           placeholder="Enter maximum fee"/>
                </div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description[]" id="desc" rows="3"></textarea>
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-danger remove-row"><i class="bx bx-trash"></i></button>
            </div>
        </div>
    </div><br>
    <div>
        <button type="button" class="btn btn-primary add-row"><i class="bx bx-plus"></i></button>
    </div>
</form>
