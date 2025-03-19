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
                            @method('put')
                            <div class="mt-3">
                                <label for="organization_id" class="form-label">College/School</label>
                                <select name="organization_id[]" id="organization_id"
                                    class="form-control required select2-ajax">

                                    <option value="{{ $data['record']->organizationsignup->id }}" selected>
                                        {{ $data['record']->organizationsignup->full_name }}
                                    </option>
                                </select>

                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label for="title" class="form-label">Mail Driver</label>
                                    <input type="text" name="mail_driver" value="{{ $data['record']->mail_driver }}"
                                        class="form-control required" id="mail_driver" placeholder="e.g., smtp" />
                                    @if ($errors->has('mail_driver'))
                                        <div class="error">{{ $errors->first('mail_driver') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="mail_host" class="form-label">Mail Host</label>
                                    <input type="text" name="mail_host" class="form-control required"
                                        value="{{ $data['record']->mail_host }}" id="mail_host"
                                        placeholder="e.g., smtp.gmail.com" />

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label for="thumbnail_file" class="form-label">Mail Port</label>
                                    <input type="text" name="mail_port" class="form-control required"
                                        value="{{ $data['record']->mail_port }}" placeholder="e.g., 587" id="mail_port" />

                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="mail_username" class="form-label">Mail Username</label>
                                    <input type="text" name="mail_username" class="form-control required"
                                        value="{{ $data['record']->mail_username }}"
                                        placeholder="e.g., your_email@gmail.com" id="mail_username" />

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label for="mail_password" class="form-label">Mail Password</label>
                                    <input type="text" name="mail_password" class="form-control required"
                                        value="{{ $data['record']->mail_password }}" placeholder="Enter your email password"
                                        id="mail_password" />
                                    @error('mail_password')
                                        <div class="alert-danger fs-6">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="mail_encryption" class="form-label">Mail Encryption</label>
                                    <input type="text" name="mail_encryption" class="form-control required"
                                        value="{{ $data['record']->mail_encryption }}" placeholder="e.g., tls or ssl"
                                        id="mail_encryption" />

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label for="mail_from_address" class="form-label">Mail Address</label>
                                    <input type="text" name="mail_from_address" class="form-control required"
                                        value="{{ $data['record']->mail_from_address }}"
                                        placeholder="e.g., no-reply@yourdomain.com" id="mail_from_address" />

                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="mail_from_name" class="form-label">Mail From Name</label>
                                    <input type="text" name="mail_from_name" class="form-control"
                                        value="{{ $data['record']->mail_from_name }}" placeholder="e.g., Your Company Name"
                                        id="mail_from_name" />

                                </div>
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
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return { id: item.id, text: item.full_name };
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
