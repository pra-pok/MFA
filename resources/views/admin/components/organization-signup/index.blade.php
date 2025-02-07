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
                <div class=" text-nowrap table-responsive">
                    <table id="datatable" class=" table table-bordered ">
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
                type: "GET",
                data: function (d) {
                    d.tenant_id = $('#tenant_id').val();
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataSrc: function (json) {
                    console.log("API Response:", json);
                    return json.data || [];
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
                    : '<span class="badge bg-label-danger ">In-Active</span>';
                const editUrl = `{{ url('organization-signup/${data.id}/edit') }}`;
                const deleteUrl = `{{ url('organization-signup/${data.id}') }}`;
                const modifiedByName = data.updatedBy && data.updatedBy.username
                    ? data.updatedBy.username
                    : (data.createds && data.createds.username ? data.createds.username : 'Unknown');
                const modifiedDate = data.updated_at ? new Date(data.updated_at).toLocaleString() : (data.created_at ? new Date(data.created_at).toLocaleString() : '');
                const blockButtonClass = data.status === 1 ? "text-danger" : "text-success";
                const blockIcon = data.status === 1 ? "bx-block" : "bx-check-circle";
                const blockText = data.status === 1 ? "Block" : "Unblock";
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
                        <a id="block-btn-${data.id}"
                           class="dropdown-item ${blockButtonClass}"
                           href="javascript:void(0)"
                           onclick="showBasicModalBlock(${data.id})">
                            <i class="bx ${blockIcon}"></i> ${blockText}
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
                <span class="text-danger"> | Team Id: ${data.tenant_id}</span>
            </td>
            <td>${data.email}<br>
                <span> ${data.phone} </span>
            </td>
            <td>${data.address}</td>
            <td>${statusBadge}
                <div style="white-space: pre-wrap; word-wrap: break-word;">${data.comment ?? ''}</div>
            </td>
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
        // function showBasicModalBlock(id, status) {
        //     $("#block_id").val(id);
        //     $("#block_status").val(status);
        //
        //     if (status === 1) {
        //         $("#commentField").hide();
        //     } else {
        //         $("#commentField").show();
        //     }
        //     var myBlockModal = new bootstrap.Modal(document.getElementById("basicModalBlock"));
        //     myBlockModal.show();
        // }
        function showBasicModalBlock(id) {
            $.ajax({
                url: '{{ route("organization-signup.getDataMessage") }}',
                method: 'GET',
                data: { id: id, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success && response.data) {
                        $('#block_id').val(id);
                        $('#block_status').val(response.data.status);
                        $('#modalTitle').text(response.data.full_name);
                        $('#userFullName').text(response.data.full_name);
                        if (response.data.comment) {
                            $('#comment').text(response.data.comment);
                        } else {
                            $('#comment').text(" ");
                        }

                        var myBlockModal = new bootstrap.Modal(document.getElementById('basicModalBlock'));
                        myBlockModal.show();
                    } else {
                        alert("Failed to fetch data.");
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching data:', xhr);
                    alert("An error occurred while fetching data.");
                }
            });
        }

        $(document).ready(function () {
            $("#blockForm").submit(function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                var organizationId = $("#block_id").val();
                var status = $("#block_status").val();

                $.ajax({
                    url: "{{ route('organization-signup.block') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $("#basicModalBlock").modal("hide");
                            let button = $("#block-btn-" + organizationId);
                            if (response.status === 1) {
                                button.html('<i class="bx bx-block"></i> Block');
                                button.removeClass("text-success").addClass("text-danger");
                                $(`#status-badge-${organizationId}`).html('<span class="badge bg-label-success">Active</span>');
                                $("#commentField").hide();
                                $("#commentField textarea").val('');
                                $.ajax({
                                    url: "{{ route('organization-signup.clearComment') }}",
                                    type: "POST",
                                    data: { id: organizationId, _token: "{{ csrf_token() }}" },
                                    success: function(clearResponse) {
                                        if (clearResponse.success) {
                                            console.log('Comment cleared successfully');
                                        } else {
                                            console.error('Failed to clear comment');
                                        }
                                    },
                                    error: function(xhr) {
                                        console.error("Error clearing comment: " + (xhr.responseJSON?.message || "Something went wrong!"));
                                    }
                                });
                            } else {
                                button.html('<i class="bx bx-check-circle"></i> Unblock');
                                button.removeClass("text-danger").addClass("text-success");
                                $(`#status-badge-${organizationId}`).html('<span class="badge bg-label-danger">In-Active</span>');
                            }
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr) {
                        alert("Error: " + (xhr.responseJSON?.message || "Something went wrong!"));
                    }
                });
            });
        });
    </script>
@endsection
