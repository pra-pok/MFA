@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $_panel }}</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <h5 class="card-header"> {{ $_panel }}</h5>
                    @include('admin.includes.buttons.button-back')
                    @include('admin.includes.flash_message_error')
                    <div class="card-body">
                        <form action="{{ route($_base_route . '.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 ">
                                    <label for="role" class="form-label"> Role </label>
                                    <select class="form-select required" id="role" name="role" aria-label="Role">
                                        <option selected disabled>Role</option>
                                        @foreach ($data['role'] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 module-section d-none">
                                    <label for="module_id" class="form-label"> Module </label>
                                    <select class="form-select required" id="module_id" name="parent_id">
                                        <option selected disabled> Module</option>
                                    </select>
                                </div>
                                <div class="col-md-3 sub-module-section d-none">
                                    <label for="sub_module_id" class="form-label"> Sub-Module </label>
                                    <select class="form-select required" id="sub_module_id" name="parent_id">
                                        <option selected disabled> Sub-Module</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-4 menuline-section d-none">
                                    <label for="menuline_id" class="form-label"> Menuline </label>
                                    <select class="form-select required" id="menuline_id" name="parent_id">
                                        <option selected disabled> Menuline</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-6">
                                        <label class="form-label" for="name"> Name</label>
                                        <input type="text" class="form-control required" id="name"
                                               name="name">
                                        <x-error key="name"/>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="mb-6">
                                        <label class="form-label" for="rank">Rank</label>
                                        <input type="number" class="form-control" id="rank" name="rank" min="0"
                                               max="100">
                                        <x-error key="rank"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="icon">Icon</label>
                                        <input type="text" class="form-control" id="icon" name="icon">
                                        <x-error key="icon"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="url">Target Url</label>
                                        <input type="text" class="form-control" id="url" name="url">
                                        <x-error key="url"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="permission_key">Permission Key</label>
                                        <input type="text" class="form-control" id="permission_key"
                                               name="permission_key">
                                        <x-error key="permission_key"/>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check custom-checkbox mb-6">
                                        <input class="form-check-input" type="checkbox" id="viewMenu"
                                               name="is_view_menu" value="1"
                                               onclick="toggleText('viewMenu', 'viewMenuText')">
                                        <label class="form-check-label" id="viewMenuText" for="viewMenu"
                                               style="display: none;">Is View Menu</label>
                                    </div>

                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check custom-checkbox mb-6">
                                        <input class="form-check-input" type="checkbox" id="active" name="is_active"
                                               value="1"
                                               onclick="toggleText('active', 'activeText')">
                                        <label class="form-check-label" id="activeText" for="active"
                                               style="display: none;">Active</label>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function toggleText(checkboxId, textId) {
            let checkbox = document.getElementById(checkboxId);
            let text = document.getElementById(textId);
            text.style.display = checkbox.checked ? "block" : "none";
        }
        $(document).ready(function () {
            $('#role').on('change', function () {
                let selectedRole = $(this).val();
                console.log("Selected Role:", selectedRole);
                $('.module-section, .sub-module-section, .menuline-section').addClass('d-none');
                if (selectedRole === 'Sub Module') {
                    $('.module-section').removeClass('d-none');
                } else if (selectedRole === 'Menuline') {
                    $('.module-section, .sub-module-section').removeClass('d-none');
                } else if (selectedRole === 'Sub Menuline') {
                    $('.module-section, .sub-module-section, .menuline-section').removeClass('d-none');
                }

                {{--if (selectedRole) {--}}
                    {{--    $.ajax({--}}
                    {{--        url: "{{ url('menu/get-all-data') }}/" + selectedRole,--}}
                    {{--        type: "GET",--}}
                    {{--        dataType: "json",--}}
                    {{--        success: function (data) {--}}
                    {{--            console.log("Fetched Data:", data);--}}

                    {{--            $('#module_id, #sub_module_id, #menuline_id').empty().append('<option selected disabled>Select..</option>');--}}

                    {{--            if (data.modules) {--}}
                    {{--                $.each(data.modules, function (key, value) {--}}
                    {{--                    $('#module_id').append('<option value="' + key + '">' + value + '</option>');--}}
                    {{--                });--}}
                    {{--            }--}}
                    {{--            if (data.subModules) {--}}
                    {{--                $.each(data.subModules, function (key, value) {--}}
                    {{--                    $('#sub_module_id').append('<option value="' + key + '">' + value + '</option>');--}}
                    {{--                });--}}
                    {{--            }--}}
                    {{--            if (data.menuLines) {--}}
                    {{--                $.each(data.menuLines, function (key, value) {--}}
                    {{--                    $('#menuline_id').append('<option value="' + key + '">' + value + '</option>');--}}
                    {{--                });--}}
                    {{--            }--}}
                    {{--        },--}}
                    {{--        error: function () {--}}
                    {{--            console.log("Error fetching data.");--}}
                    {{--            alert("Error fetching data.");--}}
                    {{--        }--}}
                    {{--    });--}}
                    {{--}--}}
                if (selectedRole) {
                    $.ajax({
                        url: "{{ url('menu/get-all-data') }}/" + selectedRole,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            console.log("Fetched Data:", data);
                            $('#module_id').empty().append('<option selected disabled>Select Module</option>');
                            if (data.modules && data.modules.length > 0) {
                                $('.module-section').removeClass('d-none');
                                $.each(data.modules, function (key, value) {
                                    $('#module_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                                });
                            }
                            window.menuData = data;
                        },
                        error: function () {
                            console.log("Error fetching data.");
                            alert("Error fetching data.");
                        }
                    });
                }
                $('#module_id').on('change', function () {
                    let moduleId = $(this).val();
                    let data = window.menuData;
                    if (data && data.subModules) {
                        let filteredSubModules = data.subModules.filter(sub => sub.parent_id == moduleId);
                        $('#sub_module_id').empty().append('<option selected disabled>Select Sub-Module</option>');
                        if (filteredSubModules.length > 0) {
                            $('.sub-module-section').removeClass('d-none');
                            $.each(filteredSubModules, function (key, value) {
                                $('#sub_module_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        } else {
                            $('.sub-module-section').addClass('d-none');
                        }
                    }
                });
                $('#sub_module_id').on('change', function () {
                    let subModuleId = $(this).val();
                    let data = window.menuData;
                    if (data && data.menuLines) {
                        let filteredMenuLines = data.menuLines.filter(menu => menu.parent_id == subModuleId);
                        $('#menuline_id').empty().append('<option selected disabled>Select Menuline</option>');
                        if (filteredMenuLines.length > 0) {
                            $('.menuline-section').removeClass('d-none');
                            $.each(filteredMenuLines, function (key, value) {
                                $('#menuline_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        } else {
                            $('.menuline-section').addClass('d-none');
                        }
                    }
                });
            });
        });

    </script>

@endsection
