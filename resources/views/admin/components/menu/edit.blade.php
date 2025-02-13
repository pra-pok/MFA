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
                    <h5 class="card-header">{{ $_panel }}</h5>
                    @include('admin.includes.buttons.button-back')
                    @include('admin.includes.flash_message_error')
                    <div class="card-body">
                        <form action="{{ route($_base_route . '.update', $data['record']->id) }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select required" id="role" name="role" aria-label="Role">
                                        <option selected disabled>Role</option>
                                        @foreach ($data['role'] as $key => $value)
                                            <option
                                                value="{{ $key }}" {{ $data['record']->role === $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 module-section d-none">
                                    <label for="module_id" class="form-label">Module</label>
                                    <select class="form-select required"
                                            data-id="{{ $data['moduleDataId'] }}"
                                            id="module_id" name="module_id">
                                        <option selected disabled>Module</option>
                                    </select>
                                </div>

                                <div class="col-md-3 sub-module-section d-none">
                                    <label for="sub_module_id" class="form-label">Sub-Module</label>
                                    <select class="form-select required"
                                            data-id="{{ $data['subModuleDataId'] }}"
                                            id="sub_module_id" name="sub_module_id">
                                        <option selected disabled>Sub-Module</option>
                                    </select>
                                </div>

                                <div class="col-md-3 menuline-section d-none">
                                    <label for="menuline_id" class="form-label">Menuline</label>
                                    <select class="form-select required"
                                            data-id="{{ $data['menulineDataId'] }}" id="menuline_id"
                                            name="menuline_id">
                                        <option selected disabled>Menuline</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-6">
                                        <label class="form-label" for="name">Name</label>
                                        <input type="text" class="form-control required" id="name" name="name"
                                               value="{{ $data['record']->name }}">
                                        <x-error key="name"/>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="mb-6">
                                        <label class="form-label" for="rank">Rank</label>
                                        <input type="number" class="form-control" id="rank" name="rank"
                                               min="0" max="100" value="{{ $data['record']->rank }}">
                                        <x-error key="rank"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="icon">Icon</label>
                                        <input type="text" class="form-control" id="icon" name="icon"
                                               value="{{ $data['record']->icon }}">
                                        <x-error key="icon"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="url">Target Url</label>
                                        <input type="text" class="form-control" id="url" name="url"
                                               value="{{ $data['record']->url }}">
                                        <x-error key="url"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="permission_key">Permission Key</label>
                                        <input type="text" class="form-control" id="permission_key"
                                               name="permission_key" value="{{ $data['record']->permission_key }}">
                                        <x-error key="permission_key"/>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check custom-checkbox mb-6">
                                        <input class="form-check-input" type="checkbox" id="viewMenu"
                                               name="is_view_menu"
                                               value="1"
                                               onclick="toggleText('viewMenu', 'viewMenuText')" {{ $data['record']->is_view_menu == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" id="viewMenuText" for="viewMenu"
                                        >Is View Menu</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check custom-checkbox mb-6">
                                        <input class="form-check-input" type="checkbox" id="active" name="is_active"
                                               value="1"
                                               onclick="toggleText('active', 'activeText')" {{ $data['record']->is_active == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" id="activeText" for="active"
                                        >Active</label>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Update</button>
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

        $(document).ready(function() {
            let menuData = {};
            function resetDropdowns() {
                $("#module_id, #sub_module_id, #menuline_id").html('<option selected disabled>None</option>');
            }
            function toggleSections(selectedRole) {
                $('.module-section, .sub-module-section, .menuline-section').addClass('d-none');

                if (selectedRole === 'Sub Module') {
                    $('.module-section').removeClass('d-none');
                } else if (selectedRole === 'Menuline') {
                    $('.module-section, .sub-module-section').removeClass('d-none');
                } else if (selectedRole === 'Sub Menuline') {
                    $('.module-section, .sub-module-section, .menuline-section').removeClass('d-none');
                }
            }
            function populateDropdown(dropdown, items, selectedValue) {
                dropdown.empty().append('<option selected disabled>None</option>');
                $.each(items, function(index, item) {
                    let isSelected = item.id == selectedValue ? "selected" : "";
                    dropdown.append(`<option value="${item.id}" ${isSelected}>${item.name}</option>`);
                });
            }
            function loadInitialData() {
                let selectedRole = $('#role').val();
                let selectedModule = $('#module_id').data('id');
                let selectedSubModule = $('#sub_module_id').data('id');
                let selectedMenuline = $('#menuline_id').data('id');

                toggleSections(selectedRole);

                if (selectedRole) {
                    $.ajax({
                        url: "{{ url('menu/get-all-data') }}/" + selectedRole,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            menuData = data;
                            populateDropdown($('#module_id'), data.modules, selectedModule);

                            if (selectedModule) {
                                filterSubModules(selectedModule, selectedSubModule);
                            }

                            if (selectedSubModule) {
                                filterMenuLines(selectedSubModule, selectedMenuline);
                            }
                        },
                        error: function() {
                            console.error("Error fetching data.");
                            alert("Error fetching data.");
                        }
                    });
                }
            }

            function filterSubModules(moduleId, selectedSubModule) {
                let subModules = menuData.subModules.filter(sub => sub.parent_id == moduleId);
                populateDropdown($('#sub_module_id'), subModules, selectedSubModule);

                if (selectedSubModule) {
                    filterMenuLines(selectedSubModule, $('#menuline_id').data('id'));
                }
            }

            function filterMenuLines(subModuleId, selectedMenuline) {
                let menuLines = menuData.menuLines.filter(menu => menu.parent_id == subModuleId);
                populateDropdown($('#menuline_id'), menuLines, selectedMenuline);
            }

            loadInitialData();

            $('#role').on('change', function() {
                let selectedRole = $(this).val();
                toggleSections(selectedRole);
                resetDropdowns();

                if (selectedRole) {
                    $.ajax({
                        url: "{{ url('menu/get-all-data') }}/" + selectedRole,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            menuData = data;
                            populateDropdown($('#module_id'), data.modules, null);
                        },
                        error: function() {
                            console.error("Error fetching data.");
                            alert("Error fetching data.");
                        }
                    });
                }
            });

            $('#module_id').on('change', function() {
                let moduleId = $(this).val();
                let selectedRole = $('#role').val();

                $('.sub-module-section').toggleClass('d-none', !(selectedRole === 'Menuline' ||
                    selectedRole === 'Sub Menuline'));
                $('#sub_module_id').empty().append('<option selected disabled>None</option>');
                $('#menuline_id').empty().append('<option selected disabled>None</option>');

                filterSubModules(moduleId, null);
            });

            $('#sub_module_id').on('change', function() {
                let subModuleId = $(this).val();
                let selectedRole = $('#role').val();

                $('.menuline-section').toggleClass('d-none', selectedRole !== 'Sub Menuline');
                $('#menuline_id').empty().append('<option selected disabled>None</option>');

                filterMenuLines(subModuleId, null);
            });

            $('#menuline_id').on('change', function() {
                let selectedRole = $('#role').val();
                if (selectedRole !== 'Sub Menuline') {
                    $('.menuline-section').addClass('d-none');
                }
            });
        });
    </script>
@endsection
