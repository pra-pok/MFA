{{-- @extends('admin.layouts.app')
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
                            <div class="mt-3">
                                <label for="title" class="form-label">Title</label>
                                <input
                                    type="text"
                                    name="title"
                                    class="form-control required"
                                    id="title"
                                    placeholder="Enter The Title"
                                />
                            </div>
                            <div class="mt-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input
                                    type="text"
                                    name="slug"
                                    class="form-control required"
                                    id="slug" placeholder="slug"
                                />
                            </div>
                            <div class="mt-3">
                                <label for="rank" class="form-label">Rank</label>
                                <input
                                    type="number"
                                    name="rank"
                                    class="form-control required"
                                    min="0"
                                    max="100"
                                    id="rank" placeholder="Enter number i.e. ( 1,2,3...)"
                                />
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
@endsection --}}
<form action="{{ route($_base_route . '.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Create {{$_panel}}</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row g-6">
                <div class="col mb-0">
                    <label for="title" class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control required"
                        id="title"/>

                    @if($errors->has('title'))
                        <div class="error">{{ $errors->first('title') }}</div>
                    @endif
                </div>
                <div class="col mb-0">
                    <label for="rank" class="form-label">Rank</label>
                    <input
                        type="number"
                        name="rank"
                        min="0"
                        max="100"
                        class="form-control required"
                        id="rank" />
                    @if($errors->has('rank'))
                        <div class="error">{{ $errors->first('rank') }}</div>
                    @endif
                </div>
            </div>
            <div class="row ">
                <div >
                    <label for="slug" class="form-label">Slug</label>
                    <input
                        type="text"
                        name="slug"
                        class="form-control required"
                        id="slug" />
                </div>
            </div>
            @include('admin.includes.create_meta')
            @include('admin.includes.create_status')
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        style="margin: 10px;">
                    Close
                </button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </div>
    </div>
</form>
