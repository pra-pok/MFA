@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">
                <a href="{{ route('user.create') }}" class="btn btn-primary">Create User</a>
            </h5>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                        <tr>
                            <th width="100">SN</th>
                            <th width="100">Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th width="100">Modified By/At</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $user->name }}
                                    <br><span style="font-size: 13px;">{{ $user->username ?? 'No Username' }}</span>

                                    <div class="dropdown" style="margin-left: 251px; margin-top: -22px;">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('user.edit', $user) }}"><i class="bx bx-edit-alt me-1"></i>Edit</a>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="showPasswordModal({{ $user->id }})">
                                                <i class="bx bx-edit-alt me-1"></i>Change Password
                                            </a>
                                            <a id="block-btn-{{ $user->id }}"
                                               class="dropdown-item {{ $user->status == 1 ? 'text-danger' : 'text-success' }}"
                                               href="javascript:void(0)"
                                               onclick="showBlockModal({{ $user->id }})">
                                                <i class="bx {{ $user->status == 1 ? 'bx-block' : 'bx-check-circle' }}"></i>
                                                {{ $user->status == 1 ? 'Block' : 'Unblock' }}
                                            </a>
                                            <form action="{{ route('user.destroy', $user) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item"><i
                                                        class="bx bx-trash me-1"></i> Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->role->name ?? 'No Role' }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if ($user->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">In-active</span>
                                    @endif
                                    <div style="white-space: pre-wrap; word-wrap: break-word;">{!!  $user->comment !!}</div>
                                </td>
                                <td>
                                    @if($user->updatedBy)
                                        {{ $user->updatedBy->username }}<br>
                                        {{ $user->updated_at }}
                                    @else
                                        {{ $user->createdBy->username }}<br>
                                        {{ $user->created_at }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            @include('admin.components.user.change-password')
        </div>
    </div>

    <!-- Block User Modal -->
    <div class="modal fade" id="basicModalBlock" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            @include('admin.components.user.block')
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
        function showPasswordModal(id) {
            $("#userId").val(id);
            var myModal = new bootstrap.Modal(document.getElementById('basicModal'));
            myModal.show();
        }
        $(document).ready(function () {
            $("#resetPasswordForm").submit(function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('user.reset') }}",
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
        function showBlockModal(id) {
            $.ajax({
                url: '{{ route('user.getDataMessage') }}',
                method: 'GET',
                data: { id: id, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success && response.data) {
                        $('#block_id').val(id);
                        $('#block_status').val(response.data.status);
                        $('#modalTitle').text(response.data.name);
                        $('#userFullName').text(response.data.name);
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
                var userId = $("#block_id").val();
                var status = $("#block_status").val();
                $.ajax({
                    url: "{{ route('user.block') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $("#basicModalBlock").modal("hide");
                            let button = $("#block-btn-" + userId);
                            if (response.status === 1) {
                                button.html('<i class="bx bx-block"></i> Block');
                                button.removeClass("text-success").addClass("text-danger");
                                $(`#status-badge-${userId}`).html('<span class="badge bg-label-success">Active</span>');
                                $("#commentField").hide();
                                $("#commentField textarea").val('');
                                $.ajax({
                                    url: "{{ route('user.clearComment') }}",
                                    type: "POST",
                                    data: { user_id: userId, _token: "{{ csrf_token() }}" },
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
                                $(`#status-badge-${userId}`).html('<span class="badge bg-label-danger">In-Active</span>');
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
    @include('admin.includes.javascript.display_none')
@endsection
