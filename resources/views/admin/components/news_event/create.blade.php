@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-6">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Create {{ $_panel }}</h5>
                    @include('admin.includes.buttons.button-back')
                    @include('admin.includes.flash_message_error')
                    <div class="card-body">
                        <form action="{{ route($_base_route . '.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input
                                        type="text"
                                        name="title"
                                        class="form-control required"
                                        id="title"
                                        placeholder="Enter The Title"/>
                                    @if($errors->has('title'))
                                        <div class="error">{{ $errors->first('title') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input
                                        type="text"
                                        name="slug"
                                        class="form-control required"
                                        id="slug" placeholder="slug"/>
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
                                <textarea class="form-control" name="short_description" rows="3"></textarea>
                            </div>
                            <div class="mt-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control editor" name="description" rows="3"></textarea>
                            </div>
                            <div class="mt-3">
                                <label for="organization_id" class="form-label">College/School</label>
                                <select name="organization_id[]" id="organization_id" class="form-control select2-ajax" multiple></select>
                            </div>

                            @include('admin.includes.create_meta')
                            @include('admin.includes.create_status')
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Create</button>
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
            $('#organization_id').select2({
                placeholder: "Search for a College/School",
                allowClear: true,
                ajax: {
                    url: "{{ route($_base_route . '.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term // Search query
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        });
    </script>

@endsection
