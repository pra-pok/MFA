<form action="{{ route('organization-course.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="organization_id" value="{{ $data['record']->id ?? '' }} " />
    <div class="panel-body">
        <div class="file-block">
            @if (isset($data['organization_courses']) && $data['organization_courses']->count() > 0)
                @foreach ($data['organization_courses'] as $key => $record)
                    <div class="card mb-3 clone-file">
                        <div class="card-body">
                            <div class="row form-row">
                                <input type="hidden" name="id[{{$key}}]" value="{{ $record->id ?? ' '}}" >
                                <div class="col-md-4">
                                    <label for="course_id">Select Course</label>
                                    <select class="form-control select-course required" name="course_id[]"
                                            aria-label="Select Course">
                                        <option selected disabled>Select Course</option>
                                        @foreach ($data['courses'] as $courseKey => $courseValue)
                                            <option
                                                value="{{ $courseKey }}" {{ $record->course_id == $courseKey ? 'selected' : '' }}>
                                                {{ $courseValue }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="start_fee" class="form-label">Min Fee Range</label>
                                    <input type="number" class="form-control" name="start_fee[]" step="0.01"
                                           value="{{ $record->start_fee ?? '' }}" placeholder="Enter minimum fee"/>
                                </div>
                                <div class="col-md-4">
                                    <label for="end_fee" class="form-label">Max Fee Range</label>
                                    <input type="number" class="form-control" name="end_fee[]" step="0.01"
                                           value="{{ $record->end_fee ?? '' }}" placeholder="Enter maximum fee"/>
                                </div>
                                <div class="mb-3">
                                    <label for="description_{{ $key }}" class="form-label">Description</label>
                                    <textarea class="form-control editor" name="description[]" rows="3">{{ $record->description ?? '' }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <input type="hidden" name="status[{{ $key }}]" value="0">
                                    <input value="1" type="checkbox"  name="status[{{ $key }}]" class="checkbox" style="width: 50px;" {{ $record->status ? 'checked' : '' }} />
                                </div>
                                {{-- <div class="mt-3 text-end">
                                    <button type="button" class="btn btn-danger remove-row"><i class="bx bx-trash"></i></button>
                                </div> --}}
                                <div class="mt-3 text-end">
                                    <button type="button" class="btn btn-danger remove-row" data-id="{{ $record->id ?? '' }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card mb-3 clone-file">
                    <div class="card-body">
                        <div class="row form-row">
                            <div class="col-md-4">
                                <label for="course_id">Select Course</label>
                                <select class="form-control select-course required" name="course_id[]"
                                        aria-label="Select Course">
                                    <option selected disabled>Select Course</option>
                                    @foreach ($data['courses'] as $courseKey => $courseValue)
                                        <option value="{{ $courseKey }}">{{ $courseValue }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="start_fee" class="form-label">Min Fee Range</label>
                                <input type="number" class="form-control" name="start_fee[]" step="0.01" />
                            </div>
                            <div class="col-md-4">
                                <label for="end_fee" class="form-label">Max Fee Range</label>
                                <input type="number" class="form-control" name="end_fee[]" step="0.01" placeholder="Enter maximum fee"/>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control editor" name="description[]" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <input type="hidden" name="status[]" value="0">
                                <input value="1" type="checkbox"  name="status[]" class="checkbox" style="width: 50px;" />
                            </div>
                            <div class="mt-3 text-end">
                                <button type="button" class="btn btn-danger remove-row"><i class="bx bx-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="mt-4 text-end">
            <button type="button" class="btn btn-primary" id="add-row"><i class="bx bx-plus"></i></button>
        </div>
    </div>
</form>
