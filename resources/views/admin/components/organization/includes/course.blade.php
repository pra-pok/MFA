<form action="{{ route('organization-course.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="organization_id" value="{{ $data['record']->id ?? '' }} "/>
    <div class="file-block-course accordion " id="organizationCoursesAccordion">
        @if (isset($data['organization_courses']) && $data['organization_courses']->count() > 0)
            @foreach ($data['organization_courses'] as $key => $record)
                @php $uniqueId = 'course-' . uniqid(); @endphp
                <div class="file-clone-course">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="course-heading-{{$uniqueId}}">
                            <button class="accordion-button {{ $key == 0 ? '' : 'collapsed' }}" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#course-collapse-{{$uniqueId}}"
                                    aria-expanded="{{ $key == 0 ? 'true' : 'false' }}"
                                    aria-controls="course-collapse-{{$uniqueId}}">
                                {{ $record->course->title ?? '' }}
                            </button>
                        </h2>
                        <div id="course-collapse-{{$uniqueId}}"
                             class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}"
                             aria-labelledby="course-heading-{{$uniqueId}}"
                             data-bs-parent="#organizationCoursesAccordion">
                            <div class="accordion-body">
                                <div class="row form-row">
                                    <input type="hidden" name="id[{{$key}}]" value="{{ $record->id ?? '' }}">
                                    <div class="col-md-6 mt-3">
                                        <label for="course_id"> Course</label>
                                        <select class="form-control select-course required" name="course_id[]"
                                                aria-label=" Course">
                                            <option selected disabled> Course</option>
                                            @foreach ($data['courses'] as $courseKey => $courseValue)
                                                <option
                                                    value="{{ $courseKey }}" {{ $record->course_id == $courseKey ? 'selected' : '' }}>
                                                    {{ $courseValue }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <label for="university_id"> University</label>
                                        <select class="form-control select-course required" name="university_id[]"
                                                aria-label=" University">
                                            <option selected disabled> University</option>
                                            @foreach ($data['university'] as $Key => $Value)
                                                <option
                                                    value="{{ $Key }}" {{ $record->university_id == $Key ? 'selected' : '' }}>
                                                    {{ $Value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <label for="start_fee" class="form-label">Min Fee Range</label>
                                        <input type="number" class="form-control" name="start_fee[]" step="0.01"
                                               value="{{ $record->start_fee ?? '' }}" placeholder="Enter minimum fee"/>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <label for="end_fee" class="form-label">Max Fee Range</label>
                                        <input type="number" class="form-control" name="end_fee[]" step="0.01"
                                               value="{{ $record->end_fee ?? '' }}" placeholder="Enter maximum fee"/>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description_{{ $key }}" class="form-label">Description</label>
                                        <textarea class="form-control editor" name="description[]"
                                                  rows="3">{{ $record->description ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-6 mt-3 d-flex align-items-center">
                                        <input type="hidden" name="status[{{ $key }}]" value="0">
                                        <input value="1" type="checkbox" name="status[{{ $key }}]"
                                               class="form-check-input checkbox status-checkbox me-2"
                                            {{ $record->status ? 'checked' : '' }} />
                                        <label
                                            class="form-check-label status-label">{{ $record->status ? 'Active' : 'Inactive' }}</label>
                                    </div>
                                    <div class="col-md-6 mt-3 text-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-row"
                                                data-id="{{ $record->id ?? '' }}">
                                            <i class="bx bx-trash me-1 fs-5"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="file-clone-course">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="course-heading-new">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#course-collapse-new"
                                aria-expanded="true" aria-controls="course-collapse-new">
                            Add New Course
                        </button>
                    </h2>
                    <div id="course-collapse-new" class="accordion-collapse collapse show"
                         aria-labelledby="course-heading-new">
                        <div class="accordion-body">
                            <div class="row form-row">
                                <div class="col-md-6 mt-3">
                                    <label for="course_id"> Course</label>
                                    <select class="form-select select-course required" name="course_id[]"
                                            aria-label=" Course">
                                        <option selected disabled> Course</option>
                                        @foreach ($data['courses'] as $courseKey => $courseValue)
                                            <option value="{{ $courseKey }}">{{ $courseValue }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label for="university_id"> University</label>
                                    <select class="form-select select-course required" name="university_id[]"
                                            aria-label=" University">
                                        <option selected disabled> University</option>
                                        @foreach ($data['university'] as $Key => $Value)
                                            <option value="{{ $Key }}">{{ $Value }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label for="start_fee" class="form-label">Min Fee Range</label>
                                    <input type="number" class="form-control" name="start_fee[]" step="0.01"/>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label for="end_fee" class="form-label">Max Fee Range</label>
                                    <input type="number" class="form-control" name="end_fee[]" step="0.01"/>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control editor" name="description[]" rows="3"></textarea>
                                </div>

                                <div class="col-md-6 mt-3 d-flex align-items-center">
                                    <input type="hidden" name="status[]" value="0">
                                    <input value="1" type="checkbox" name="status[]"
                                           class="form-check-input checkbox status-checkbox me-2" checked/>
                                    <label class="form-check-label status-label">Active</label>
                                </div>

                                <div class="col-md-6 mt-3 text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                        <i class="bx bx-trash me-1 fs-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="mt-4 text-end">
        <button type="button" class="btn btn-primary btn-sm" id="add-row"><i class="bx bx-plus"></i></button>
    </div>
</form>
