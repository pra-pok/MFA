<form action="{{ route('organization-member.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="organization_id" value="{{ $data['record']->id ?? '' }} " />
    <div class="panel-body">
        <div class="member-file-block">
            @if (isset($data['organization_members']) && $data['organization_members']->count() > 0)
                @foreach ($data['organization_members'] as $key => $record)
                    <div class="card mb-3 member-clone-file">
                        <div class="card-body">
                            <div class="row form-row">
                                <input type="hidden" name="id[{{$key}}]" value="{{ $record->id ?? ' '}}" >
                                <div class="col-md-6 mt-3">
                                    <label for="organization_group_id">Group</label>
                                    <select class="form-control select-course required" name="organization_group_id[]"
                                            aria-label="Group">
                                        <option selected disabled>Group</option>
                                        @foreach ($data['groups'] as $courseKey => $courseValue)
                                            <option
                                                value="{{ $courseKey }}" {{ $record->organization_group_id == $courseKey ? 'selected' : '' }}>
                                                {{ $courseValue }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name[{{ $key }}]" value="{{$record->name}}" />
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="rank" class="form-label">Rank</label>
                                    <input type="number" class="form-control" name="rank[{{ $key }}]" max="100" min="0" value="{{$record->rank}}" />
                                </div>
                                <div class="col-md-5 mt-3">
                                    <label for="designation" class="form-label">Designation</label>
                                    <input type="text" class="form-control" name="designation[{{ $key }}]" value="{{$record->designation}}" />
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label for="photo_file" class="form-label">Photo</label>
                                    <input type="file" class="form-control" name="photo_file[{{ $key }}]" />
                                </div>
                                <div class="mb-3">
                                    <label for="bio_{{ $key }}" class="form-label">Bio</label>
                                    <textarea class="form-control editor" name="bio[]" rows="3">{{ $record->bio ?? '' }}</textarea>
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
                @endforeach
            @else
                <div class="card mb-3 member-clone-file">
                    <div class="card-body">
                        <div class="row form-row">
                            <div class="col-md-6 mt-3">
                                <label for="organization_group_id">Group</label>
                                <select class="form-select select-course required" name="organization_group_id[]" id="organization_group_id[0]"
                                        aria-label="Group">
                                    <option selected disabled>Group</option>
                                    @foreach ($data['groups'] as $courseKey => $courseValue)
                                        <option value="{{ $courseKey }}">{{ $courseValue }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text"  class="form-control" name="name[]" id="name[0]" />
                            </div>
                            <div class="col-md-3 mt-3">
                                <label for="rank" class="form-label">Rank</label>
                                <input type="number" class="form-control" name="rank[]" max="100" min="0" id="rank[0]" />
                            </div>
                            <div class="col-md-5 mt-3">
                                <label for="designation" class="form-label">Designation</label>
                                <input type="text" class="form-control" name="designation[]" id="designation[0]" />
                            </div>
                            <div class="col-md-4 mt-3">
                                <label for="photo_file" class="form-label">Photo</label>
                                <input type="file" class="form-control" name="photo_file[]" id="photo_file[0]" />
                            </div>
                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control editor" name="bio[]" id="bio[0]" rows="3"></textarea>
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
            @endif
        </div>
        <div class="mt-4 text-end">
            <button type="button" class="btn btn-primary btn-sm" id="add-row-member"><i class="bx bx-plus"></i></button>
        </div>
    </div>
</form>
