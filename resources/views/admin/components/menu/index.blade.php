@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">{{$_panel}}</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between mb-3">
                    @include('admin.includes.buttons.button-create')

                    <div class="ml-auto">
                        @include('admin.includes.buttons.button_display_trash')
                    </div>
                </div>

                <div class=" text-nowrap table-responsive">
                    <table id="datatable" class=" table table-bordered ">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Parent Module</th>
                            <th>Icon</th>
                            <th>Role</th>
                            <th>Permission Key/Target Url</th>
                            <th width="5%">Rank</th>
                            <th class="text-center">Status/View Menu</th>
                            <th>Modified By/At</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('#datatable').DataTable({
            ajax: {
                url: '{{ route('menu.index') }}',
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataSrc: function (json) {
                    console.log("API Response:", json);
                    return json.data || [];
                },
                error: function (xhr, status, error) {
                    console.error("Error loading data: " + error);
                    alert("Failed to load data. Please check your console for details.");
                }
            },
            columns: [
                {data: 'name'},
                {data: 'parent.name'},
                {data: 'icon'},
                {data: 'role'},
                {data: 'permission_key'},
                {data: 'rank'},
                {data: 'is_active'},
                {data: 'createds.username'},
            ],
            rowCallback: function (row, data, index) {
                const statusBadge = data.is_active === 1
                    ? '<span class="badge bg-label-success me-1">Active</span>'
                    : '<span class="badge bg-label-danger ">In-Active</span>';
                const viewMenu = data.is_view_menu === 1
                    ? '<span class="badge bg-label-success me-1">Yes</span>'
                    : '<span class="badge bg-label-danger ">No</span>';
                const editUrl = `{{ url('menu/${data.id}/edit') }}`;
                const deleteUrl = `{{ url('menu/${data.id}') }}`;
                const modifiedByName = data.updatedBy && data.updatedBy.username
                    ? data.updatedBy.username
                    : (data.createds && data.createds.username ? data.createds.username : 'Unknown');
                const modifiedDate = data.updated_at ? new Date(data.updated_at).toLocaleString() : (data.created_at ? new Date(data.created_at).toLocaleString() : '');
                const rowContent = `
                <td class="position-relative">${data.name}
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow position-absolute top-50 end-0 translate-middle-y" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded fs-5 d-none"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="${editUrl}">
                                <i class="bx bx-edit-alt me-1"></i> Edit
                            </a>
                            <form action="${deleteUrl}" method="POST" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bx bx-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </td>
                <td>${data.parent?.name || ''}</td>
                <td>${data.icon}</td>
                <td>${data.role}</td>
                <td>${data.permission_key} <br> <a href="${data.url}" target="_blank">${data.url || '#'}</a></td>
                <td>${data.rank}</td>
                <td>${statusBadge} ${viewMenu}</td>
                <td>
                    ${modifiedByName}<br>
                    ${modifiedDate}
                </td>
            `;
                $(row).html(rowContent);
            },
            pageLength: 10,
            lengthMenu: [10, 25, 50, 75, 100, 150],
            responsive: true
        });
    </script>
    @include('admin.includes.javascript.display_none')
@endsection
