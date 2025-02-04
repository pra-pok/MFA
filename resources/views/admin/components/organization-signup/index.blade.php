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
            <h5 class="card-header">{{$_panel}}</h5>
            <div class="card-body" >
                <div class="d-flex justify-content-between mb-3">
                    @include('admin.includes.buttons.button-create')

                    <div class="ml-auto">
                        @include('admin.includes.buttons.button_display_trash')
                    </div>
                </div>
                @include('admin.includes.flash_message')
                <div class=" text-nowrap">
                    <table id="datatable" class=" table table-bordered">
                        <thead>
                        <tr>
                            <th width="6%">SN</th>
                            <th>Full Name</th>
                            <th>Email/Phone</th>
                            <th>Address</th>
                            <th>Status</th>
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

    <div class="mt-4">
        <!-- Button trigger modal -->
        <!-- Modal -->
        <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                @include('admin.components.organization-signup.change-password')
            </div>
        </div>
    </div>
    <div class="mt-4">
        <!-- Button trigger modal -->
        <!-- Modal -->
        <div class="modal fade" id="basicModalBlock" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                @include('admin.components.organization-signup.block')
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('#datatable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '{{ route( 'organization-signup.index') }}',
                dataSrc: function (json) {
                    if (json.data) {
                        return json.data;
                    } else {
                        console.error("Data format error", json);
                        return [];
                    }
                },
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                error: function(xhr, status, error) {
                    console.error("Error loading data: " + error);
                    alert("Failed to load data. Please check your console for details.");
                }
            },
            columns: [
                { data: null },
                {data: 'full_name'},
                { data: 'email' },
                { data: 'address' },
                { data: 'status' },
                { data: 'createds.username' },
            ],
            rowCallback: function (row, data, index) {
                const statusBadge = data.status === 1
                    ? '<span class="badge bg-label-success me-1">Active</span>'
                    : '<span class="badge bg-label-danger ">De-Active</span>';
                const editUrl = `{{ url('organization-signup/${data.id}/edit') }}`;
                const deleteUrl = `{{ url('organization-signup/${data.id}') }}`;
                const modifiedByName = data.updatedBy && data.updatedBy.username
                    ? data.updatedBy.username
                    : (data.createds && data.createds.username ? data.createds.username : 'Unknown');
                const modifiedDate = data.updated_at ? new Date(data.updated_at).toLocaleString() : (data.created_at ? new Date(data.created_at).toLocaleString() : '');
                const rowContent = `
            <td>${index + 1}</td>
            <td class="position-relative">${data.full_name}<br><span> ${data.username} </span>
                <div class="dropdown d-inline-block">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow position-absolute top-50 end-0 translate-middle-y" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded fs-5 d-none"></i>
                        </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="${editUrl}">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="showBasicModal(${data.id})">
                            <i class="bx bx-edit-alt me-1"></i> Change Password
                        </a>
                         <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="showBasicModalBlock(${data.id})">
                            <i class="bx bx-block"></i> Block
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
            <td>${data.email}<br>
                <span> ${data.phone} </span>
            </td>
            <td>${data.address}</td>
            <td>${statusBadge}</td>
            <td>
                ${modifiedByName}<br>
                ${modifiedDate}
            </td>
        `;
                $(row).html(rowContent);
            },
            pageLength: 10,
            lengthMenu: [ 10, 25, 50 , 75, 100, 150],
            responsive: true
        });
        $('#basicModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });
    </script>
    @include('admin.includes.javascript.display_none')
    <script>
        function showBasicModal(id) {
            $("#id").val(id);
            var myModal = new bootstrap.Modal(document.getElementById('basicModal'));
            myModal.show();
        }
        $(document).ready(function () {
            $("#resetPasswordForm").submit(function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route($_base_route . '.reset') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr) {
                        alert("Error: " + xhr.responseJSON.message);
                    }
                });
            });
        });

        function showBasicModalBlock(id) {
            $("#id").val(id);
            var myModal = new bootstrap.Modal(document.getElementById('basicModalBlock'));
            myModal.show();
        }
    </script>
@endsection
