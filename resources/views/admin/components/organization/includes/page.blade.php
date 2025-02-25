<form action="{{ route('organization-page.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="organization_id" value="{{ $data['record']->id ?? '' }}"/>
    <div class="panel-body">
        <div class="accordion file-block-page " id="organizationPagesAccordion">
            @if (isset($data['organization_pages']) && $data['organization_pages']->count() > 0)
                @foreach ($data['organization_pages'] as $key => $item)
                    <div class="accordion-item clone-file-page">
                        <h2 class="accordion-header" id="heading{{ $key }}">
                            <button class="accordion-button {{ $key == 0 ? '' : 'collapsed' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $key }}"
                                    aria-expanded="{{ $key == 0 ? 'true' : 'false' }}"
                                    aria-controls="collapse{{ $key }}">
                                Page {{ $key + 1 }}
                            </button>
                        </h2>
                        <div id="collapse{{ $key }}" class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}"
                             aria-labelledby="heading{{ $key }}" data-bs-parent="#organizationPagesAccordion">
                            <div class="accordion-body">
                                <div class="row form-row">
                                    <input type="hidden" name="id[{{$key}}]" value="{{ $item->id ?? '' }}">
                                    <div class="col-md-4 mt-3">
                                        <label>Page</label>
                                        <select class="form-control select-page required" name="page_category_id[]"
                                                aria-label="Page">
                                            <option selected disabled>Page</option>
                                            @foreach ($data['page'] as $pageKey => $pageValue)
                                                <option
                                                    value="{{ $pageKey }}" {{ $item->page_category_id == $pageKey ? 'selected' : '' }}>
                                                    {{ $pageValue }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control editor" name="description[]"
                                                  rows="3">{{ $item->description ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-6 mt-3 d-flex align-items-center">
                                        <input type="hidden" name="status[{{ $key }}]" value="0">
                                        <input value="1" type="checkbox" name="status[{{ $key }}]"
                                               class="form-check-input checkbox status-checkbox me-2"
                                            {{ $item->status ? 'checked' : '' }} />
                                        <label
                                            class="form-check-label status-label">{{ $item->status ? 'Active' : 'Inactive' }}</label>
                                    </div>
                                    <div class="col-md-6 mt-3 text-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-row"
                                                data-id="{{ $item->id ?? '' }}">
                                            <i class="bx bx-trash me-1 fs-5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="accordion-item clone-file-page">
                    <h2 class="accordion-header" id="heading0">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse0" aria-expanded="true" aria-controls="collapse0">
                           New Page
                        </button>
                    </h2>
                    <div id="collapse0" class="accordion-collapse collapse show" aria-labelledby="heading0"
                         data-bs-parent="#organizationPagesAccordion">
                        <div class="accordion-body">
                            <div class="row form-row">
                                <input type="hidden" name="id[]" value="">
                                <div class="col-md-4 mt-3">
                                    <label>Page</label>
                                    <select class="form-control select-page required" name="page_category_id[]"
                                            aria-label="Page">
                                        <option selected disabled>Page</option>
                                        @foreach ($data['page'] as $pageKey => $pageValue)
                                            <option value="{{ $pageKey }}">{{ $pageValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
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
            @endif
        </div>
        <div class="mt-4 text-end">
            <button type="button" class="btn btn-primary btn-sm" id="add-row-page"><i class="bx bx-plus"></i></button>
        </div>
    </div>
</form>
