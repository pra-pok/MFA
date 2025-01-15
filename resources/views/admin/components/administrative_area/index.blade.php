@extends('admin.layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">{{$_panel}}</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">{{$_panel}}</h5>
            <div style="margin-left: 15px;">
                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#basicModal">
                    Create {{$_panel}}
                </button>
            </div>
            @include('admin.includes.buttons.button_display_trash')
            @include('admin.includes.flash_message')
            <div class="card-body" >
                <div class=" text-nowrap">
                    <table id="datatable" class=" table table-bordered">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Parent ID</th>
                                <th>Administrative Area Name</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Create By/At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="area">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
{{--    createModal--}}
    <div class="mt-4">
        <!-- Button trigger modal -->
        <!-- Modal -->
        <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="{{ route($_base_route . '.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel1">Create {{$_panel}}</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-6">
                                    <label for="name" class="form-label">Administrative Area Name</label>
                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control"
                                        id="name"
                                        placeholder="Enter The Administrative Area Name"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-6">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input
                                        type="text"
                                        name="slug"
                                        class="form-control"
                                        id="slug" placeholder="slug" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-6">
                                    <label for="rank" class="form-label">Rank</label>
                                    <input
                                        type="number"
                                        name="rank"
                                        class="form-control"
                                        id="rank" placeholder="Enter number i.e. ( 1,2,3...)" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-6">
                                    <label for="parent_id" class="form-label">Parent:</label>
                                    <select class="form-select" name="parent_id">
                                        <option value="">None</option>
                                        @foreach($data['parents'] as $key => $value)
                                            <option value="{{ $key }}" >
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-6">

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
                           </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="margin: 10px;">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Edit Modal --}}
    <div class="mt-4">
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel1">Edit {{ $_panel }}</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="edit-id">
                            <div class="row">
                                <div class="col mb-6">
                                    <label for="edit-name" class="form-label">Administrative Area Name</label>
                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control"
                                        id="edit-name"
                                        placeholder="Enter The Administrative Area Name"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-6">
                                    <label for="edit-slug" class="form-label">Slug</label>
                                    <input
                                        type="text"
                                        name="slug"
                                        class="form-control"
                                        id="edit-slug"
                                        placeholder="Slug"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-6">
                                    <label for="edit-rank" class="form-label">Rank</label>
                                    <input
                                        type="number"
                                        name="rank"
                                        class="form-control"
                                        id="edit-rank"
                                        placeholder="Enter number i.e. (1, 2, 3...)"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-6">
                                    <label for="edit-parent" class="form-label">Parent:</label>
                                    <select class="form-select" id="edit-parent" name="parent_id">
                                        <option value="">None</option>
                                        @foreach($data['parents'] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-6">
                                    <label for="edit-status" class="form-label">Status</label><br>
                                    <input
                                        name="status"
                                        class="form-check-input"
                                        type="radio"
                                        value="1"
                                        id="edit-active"/>
                                    <label class="form-check-label" for="edit-active"> Active </label>

                                    <input
                                        name="status"
                                        class="form-check-input"
                                        type="radio"
                                        value="0"
                                        id="edit-inactive"/>
                                    <label class="form-check-label" for="edit-inactive"> De-Active </label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Edit Model --}}

@endsection
@section('js')
    <script>
        {{--$(document).ready(function () {--}}
        {{--    $.ajax({--}}
        {{--        url: "{{ route($_base_route . '.index') }}",--}}
        {{--        type: "GET",--}}
        {{--        headers: {--}}
        {{--            'X-CSRF-TOKEN': "{{ csrf_token() }}"--}}
        {{--        },--}}
        {{--        success: function (response) {--}}
        {{--            const tbody = $('#area');--}}
        {{--            tbody.empty();--}}

        {{--            response.forEach((data, index) => {--}}
        {{--                const formattedDate = data.created_at ? new Date(data.created_at).toLocaleString() : '';--}}
        {{--                const statusBadge = data.status === 1--}}
        {{--                    ? '<span class="badge bg-label-primary me-1">Active</span>'--}}
        {{--                    : '<span class="badge bg-label-danger">De-Active</span>';--}}

        {{--                const row = `--}}
        {{--            <tr>--}}
        {{--                <td>${index + 1}</td>--}}
        {{--                <td>${data.parent?.name || 'No Administrative area'}</td>--}}
        {{--                <td>${data.name}</td>--}}
        {{--                <td>${data.slug}</td>--}}
        {{--                <td>${statusBadge}</td>--}}
        {{--                <td>--}}
        {{--                    ${data.createds?.name || 'Null'}<br>--}}
        {{--                    ${formattedDate}--}}
        {{--                </td>--}}
        {{--                <td>--}}
        {{--                    <button class="btn btn-sm btn-primary">Edit</button>--}}
        {{--                    <button class="btn btn-sm btn-danger">Delete</button>--}}
        {{--                </td> <!-- Actions -->--}}
        {{--            </tr>--}}
        {{--        `;--}}
        {{--                tbody.append(row);--}}
        {{--            });--}}


        {{--            $('#datatable').DataTable({--}}
        {{--                processing: true,--}}
        {{--                serverSide: true,--}}
        {{--                paging: false,--}}
        {{--                ordering: true,--}}
        {{--                info: true,--}}
        {{--                lengthChange: true,--}}
        {{--                search: true--}}

        {{--            });--}}
        {{--        },--}}
        {{--        error: function (xhr, status, error) {--}}
        {{--            console.error("Failed to load data:", error);--}}
        {{--            alert("Error loading data. Please try again.");--}}
        {{--        }--}}
        {{--    });--}}
        {{--    --}}
        {{--});--}}

        $(document).ready(function () {
            $('#datatable').DataTable({
                ajax: {
                    url: '{{ route($_base_route . '.index') }}',
                    dataSrc: '',
                    type: "GET",
                     headers: {
                           'X-CSRF-TOKEN': "{{ csrf_token() }}"
                     },
                },
                columns: [
                    { data: null },
                    { data: 'parent.name' },
                    { data: 'name' },
                    { data: 'slug' },
                    { data: 'status' },
                    { data: 'createds.name' },
                    { data: null }
                ],
                columnDefs: [
                    {
                        targets: '_all',
                        visible: true
                    }
                ],
                rowCallback: function (row, data, index) {
                    const formattedDate = data.created_at ? new Date(data.created_at).toLocaleString() : '';
                    const statusBadge = data.status === 1
                        ? '<span class="badge bg-label-primary me-1">Active</span>'
                        : '<span class="badge bg-label-danger">De-Active</span>';


                    // Custom row format
                    const rowContent = `
                    <td>${index + 1}</td>
                    <td>${data.parent?.name || 'No Administrative area'}</td>
                    <td>${data.name}</td>
                    <td>${data.slug}</td>
                    <td>${statusBadge}</td>
                    <td>
                        ${data.createds?.name || 'Null'}<br>
                        ${formattedDate}
                    </td>
                    <td>
<div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu">
                            <form action="" method="POST" onsubmit="return confirm('Are you sure?');">
                                {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="dropdown-item">
                        <i class="bx bx-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
        </td>
`;

                    // Replace the content of the row
                    $(row).html(rowContent);
                },
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50],
                responsive: true
            });
        });

    </script>
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
