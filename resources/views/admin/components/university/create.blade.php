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
                        <form action="{{ route('admin.university.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label for="country_id" class="form-label">Select Country Name</label>
                                    <select class="form-select select-course required" id="country_id" name="country_id" aria-label="Select Country Name" >
                                        <option selected disabled>Select Country Name</option>
                                        @foreach ($data['country'] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="types" class="form-label">Type</label>
                                    <select class="form-select required" id="types" name="types" aria-label="Select Type" >
                                        <option selected disabled>Select Type</option>
                                        @foreach ($data['type'] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input
                                        type="text"
                                        name="title"
                                        class="form-control required"
                                        id="title"
                                        placeholder="Enter The Title" />
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input
                                        type="text"
                                        name="slug"
                                        class="form-control required"
                                        id="slug" placeholder="slug" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label for="rank" class="form-label">Rank</label>
                                    <input
                                        type="number"
                                        name="rank"
                                        class="form-control required"
                                        min="0"
                                        max="100"
                                        id="rank" placeholder="Enter number i.e. ( 1,2,3...)"  />
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="logo" class="form-label">Logo</label>
                                    <input class="form-control" type="file" id="logo" name="image_file" />
                                </div>
                            </div>
                            <div class="mt-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control editor" name="description"  rows="3"></textarea>
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
        $(document).ready(function()
        {
            $('.select-course').select2();
        });
    </script>
@endsection
