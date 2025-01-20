@extends('admin.layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Create {{ $_panel }}</h5>
                    @include('admin.includes.buttons.button-back')
                    @include('admin.includes.flash_message_error')
                    <div class="card-body">
                        <form action="{{ route('admin.country.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    id="name"
                                    placeholder="Enter The Name" required
                                />
                            </div>
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input
                                    type="text"
                                    name="slug"
                                    class="form-control"
                                    id="slug" placeholder="slug"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="rank" class="form-label">Rank</label>
                                <input
                                    type="number"
                                    name="rank"
                                    class="form-control"
                                    min="0"
                                    max="100"
                                    id="rank" placeholder="Enter number i.e. ( 1,2,3...)" required
                                />
                            </div>
                            <div class="mb-3">
                                <label for="iso_code" class="form-label">ISO Code</label>
                                <input
                                    type="text"
                                    name="iso_code"
                                    class="form-control"
                                    id="iso_code"
                                    placeholder="Enter The Iso Code" required
                                />

                            </div>
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <input
                                    type="text"
                                    name="currency"
                                    class="form-control"
                                    id="currency"
                                    placeholder="Enter The Currency" required
                                />

                            </div>
                            <div class="mb-3">
                                <label for="icon" class="form-label">Icon</label>
                                <input
                                    type="text"
                                    name="icon"
                                    class="form-control"
                                    id="icon"
                                    placeholder="Enter The Icon" required
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
