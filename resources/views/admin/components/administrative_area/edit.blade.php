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
                        <form action="{{ route($_base_route . '.update', $data['record']->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div>
                                <label for="name" class="form-label">Administrative Area Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    id="name"
                                    value="{{$data['record']->name}}"/>
                            </div>
                            <div>
                                <label for="slug" class="form-label">Slug</label>
                                <input
                                    type="text"
                                    name="slug"
                                    class="form-control"
                                    id="slug" value="{{$data['record']->slug}}"  />
                            </div>
                            <div>
                                <label for="rank" class="form-label">Rank</label>
                                <input
                                    type="number"
                                    name="rank"
                                    class="form-control"
                                    id="rank" value="{{$data['record']->rank}}" />
                            </div>
                            <div>
                                <label for="parent_id" class="form-label">Parent:</label>
                                <select class="form-select" name="parent_id">
                                    <option value="">None</option>
                                    @foreach($data['parents'] as $key => $value)
                                        <option value="{{ $key }}" {{ $data['record']->parent_id === $key ? 'selected' : '' }} >
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div><br>
                            <div>
                                <label for="status" class="form-label">Status</label>

                                    <input
                                        name="status"
                                        class="form-check-input"
                                        type="radio"
                                        value="1"
                                        id="activeStatus"
                                        {{ isset($data['record']->status) && $data['record']->status == 1 ? 'checked' : '' }}
                                    />
                                    <label class="form-check-label" for="activeStatus"> Active </label>

                                    <input
                                        name="status"
                                        class="form-check-input"
                                        type="radio"
                                        value="0"
                                        id="deactiveStatus"
                                        {{ isset($data['record']->status) && $data['record']->status == 0 ? 'checked' : '' }}
                                    />
                                    <label class="form-check-label" for="deactiveStatus"> De-Active </label>

                            </div>
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

