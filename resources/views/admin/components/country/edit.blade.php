@extends('admin.layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Edit {{ $_panel }}</h5>
                    @include('admin.includes.buttons.button-back')
                    @include('admin.includes.flash_message_error')
                    <div class="card-body">
                        <form action="{{ route('admin.country.update', $data['record']->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mt-3">
                                <label for="name" class="form-label">Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    id="name"
                                    value="{{$data['record']->name}}" required/>
                            </div>
                            <div class="mt-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input
                                    type="text"
                                    name="slug"
                                    class="form-control"
                                    id="slug" value="{{$data['record']->slug}}"  />
                            </div>
                            <div class="mt-3">
                                <label for="rank" class="form-label">Rank</label>
                                <input
                                    type="number"
                                    name="rank"
                                    class="form-control"
                                    min="0"
                                    max="100"
                                    id="rank" value="{{$data['record']->rank}}" required />
                            </div>
                            <div class="mt-3">
                                <label for="iso_code" class="form-label">Iso Code</label>
                                <input
                                    type="text"
                                    name="iso_code"
                                    class="form-control"
                                    id="iso_code"
                                    value="{{$data['record']->iso_code}}" required/>
                            </div>
                            <div class="mt-3">
                                <label for="currency" class="form-label">Currency</label>
                                <input
                                    type="text"
                                    name="currency"
                                    class="form-control"
                                    id="currency"
                                    value="{{$data['record']->currency}}" required/>
                            </div>
                            <div class="mt-3">
                                <label for="icon" class="form-label">Icon</label>
                                <input
                                    type="text"
                                    name="icon"
                                    class="form-control"
                                    id="icon"
                                    value="{{$data['record']->icon}}" required
                                />

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
    <script>
        $(document).ready(function() {
            $('#name').on('input', function() {
                var name = $(this).val();
                var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('#slug').val(slug);
            });
        });
    </script>
@endsection

