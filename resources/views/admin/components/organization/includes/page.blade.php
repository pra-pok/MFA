<form action="{{ route('organization-page.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="organization_id" value="{{ $data['record']->id ?? '' }}"/>
    <div class="panel-body">
        <div class="file-block-page">
            @if (isset($data['organization_pages']) && $data['organization_pages']->count() > 0)
            @foreach ($data['organization_pages']  as $key => $item)
                <div class="card mb-3 clone-file-page">
                    <div class="card-body">
                        <div class="row form-row">
                            <input type="hidden" name="id[{{$key}}]" value="{{ $item->id ?? ' '}}" >
                            <div class="col-md-4 mt-3">
                                <label>Select Page</label>
                                <select class="form-control select-page required" name="page_category_id[]" aria-label="Select Page">
                                    <option selected disabled>Select Page</option>
                                    @foreach ($data['page'] as $pageKey => $pageValue)
                                        <option value="{{ $pageKey }}" {{ $item->page_category_id == $pageKey ? 'selected' : '' }}>
                                            {{ $pageValue }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control editor" name="description[]" rows="3">{{ $item->description ?? '' }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <input type="hidden" name="status[{{ $key }}]" value="0">
                                <input value="1" type="checkbox" name="status[{{ $key }}]" class="checkbox" {{ $item->status ? 'checked' : '' }} />
                            </div>
                            {{-- <div class="mt-3 text-end">
                                <button type="button" class="btn btn-danger remove-row-page"><i class="bx bx-trash"></i></button>
                            </div> --}}
                            <div class="mt-3 text-end">
                                <button type="button" class="btn btn-danger remove-row-page" data-id="{{ $item->id ?? '' }}">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @else
            <div class="card mb-3 clone-file-page">
                <div class="card-body">
                    <div class="row form-row">
                        <input type="hidden" name="id[ ]" value="" >
                        <div class="col-md-4 mt-3">
                            <label>Select Page</label>
                            <select class="form-control select-page required" name="page_category_id[]" aria-label="Select Page">
                                <option selected disabled>Select Page</option>
                                @foreach ($data['page'] as $pageKey => $pageValue)
                                    <option value="{{ $pageKey }}" >
                                        {{ $pageValue }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
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
            <button type="button" class="btn btn-primary" id="add-row-page"><i class="bx bx-plus"></i></button>
        </div>
    </div>
</form>
