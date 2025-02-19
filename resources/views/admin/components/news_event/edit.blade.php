@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-6">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Edit {{ $_panel }}</h5>
                    @include('admin.includes.buttons.button-back')
                    @include('admin.includes.flash_message_error')
                    <div class="card-body">
                        <form action="{{ route($_base_route . '.update', $data['record']->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input
                                        type="text"
                                        name="title"
                                        class="form-control required"
                                        id="title"
                                       value="{{ $data['record']->title }}"/>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input
                                        type="text"
                                        name="slug"
                                        class="form-control required"
                                        id="slug" value="{{ $data['record']->slug }}"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label for="thumbnail_file" class="form-label">Thumbnail</label>
                                    <input
                                        type="file"
                                        name="thumbnail_file"
                                        class="form-control"
                                        id="thumbnail_file"/>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="pdf_file" class="form-label">File</label>
                                    <input
                                        type="file"
                                        name="pdf_file"
                                        class="form-control"
                                        id="pdf_file"/>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label for="short_description" class="form-label">Short Description</label>
                                <textarea class="form-control" name="short_description" rows="3">{!! $data['record']->short_description !!}</textarea>
                            </div>
                            <div class="mt-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control editor" name="description" rows="3">{!! $data['record']->description !!}</textarea>
                            </div>
                            <div class="mt-3">
                                <label for="organization_id" class="form-label">College/School</label>
                                <select name="organization_id[]" id="organization_id" class="form-control select2-ajax" multiple>
                                    @foreach($data['record']->organizations as $organization)
                                        <option value="{{ $organization->id }}" selected>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @include('admin.includes.edit_meta')
                            @include('admin.includes.edit_status')
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('admin.includes.slug')
    <script>
        $(document).ready(function () {
            let selectedOrganizations = @json($data['record']->organizations->map(fn($org) => ['id' => $org->id, 'text' => $org->name]));

            $('#organization_id').select2({
                placeholder: "Search for a College/School",
                allowClear: true,
                ajax: {
                    url: "{{ route($_base_route . '.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return { id: item.id, text: item.name };
                            })
                        };
                    },
                    cache: true
                }
            });

            // Set pre-selected values
            $('#organization_id').select2('data', selectedOrganizations);
            $('#organization_id').trigger('change'); // Refresh select2
        });
    </script>
@endsection

