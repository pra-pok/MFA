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
                            <div>
                                <label for="name" class="form-label">Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    id="name"
                                    placeholder="Enter The Name"
                                />
                            </div>
                            <div>
                                <label for="slug" class="form-label">Slug</label>
                                <input
                                    type="text"
                                    name="slug"
                                    class="form-control"
                                    id="slug" placeholder="slug"
                                />
                            </div>
                            <div>
                                <label for="rank" class="form-label">Rank</label>
                                <input
                                    type="number"
                                    name="rank"
                                    class="form-control"
                                    id="rank" placeholder="Enter number i.e. ( 1,2,3...)"
                                />
                            </div>
                            <div>
                                <label for="iso_code" class="form-label">ISO Code</label>
                                <input
                                    type="text"
                                    name="iso_code"
                                    class="form-control"
                                    id="iso_code"
                                    placeholder="Enter The Iso Code"
                                />

                            </div>
                            <div>
                                <label for="currency" class="form-label">Currency</label>
                                <input
                                    type="text"
                                    name="currency"
                                    class="form-control"
                                    id="currency"
                                    placeholder="Enter The Currency"
                                />

                            </div>
                            <div>
                                <label for="icon" class="form-label">Icon</label>
                                <input
                                    type="text"
                                    name="icon"
                                    class="form-control"
                                    id="icon"
                                    placeholder="Enter The Icon"
                                />

                            </div>
                            <div>
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input
                                    type="text"
                                    name="meta_title"
                                    class="form-control"
                                    id="meta_title"
                                    placeholder="Enter The Meta Title"
                                />
                            </div>
                            <div>
                                <label for="meta_keywords" class="form-label">Meta Keyword</label>
                                <input
                                    type="text"
                                    name="meta_keywords"
                                    class="form-control"
                                    id="meta_keywords"
                                    placeholder="Enter The Meta Keyword"
                                />
                            </div>
                            <div>
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <input
                                    type="text"
                                    name="meta_description"
                                    class="form-control"
                                    id="meta_description"
                                    placeholder="Enter The Meta Description"
                                />
                            </div><br>
                            <div>
                                <label for="status" class="form-label">Status</label>

                                    <input
                                        name="status"
                                        class="form-check-input"
                                        type="radio"
                                        value="1"
                                        id="activeStatus"
                                        checked
                                    />
                                    <label class="form-check-label" for="activeStatus"> Active </label>

                                    <input
                                        name="status"
                                        class="form-check-input"
                                        type="radio"
                                        value="0"
                                        id="deactiveStatus"
                                    />
                                    <label class="form-check-label" for="deactiveStatus"> De-Active </label>
                            </div>
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
