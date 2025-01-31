@extends('admin.layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
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
                @include('includes.message')

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
                                        <br> <span style="font-size: 13px;"> {{ $user->username ?? 'No Username ' }} </span>

                                        <div class="dropdown" style="margin-left: 251px; margin-top: -22px;">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('user.edit', $user) }}"><i
                                                        class="bx bx-edit-alt me-1"></i> Edit</a>
                                                {{--                                                <a class="dropdown-item" href="{{ route('user.assign-roles', $user) }}"><i--}}
                                                {{--                                                        class="bx bx-edit-alt me-1"></i> Assign Roles</a>--}}
                                                <form action="{{ route('user.destroy', $user) }}" method="POST"
                                                      onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item"><i
                                                            class="bx bx-trash me-1"></i>
                                                        Delete</button>
                                                </form>

                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                            {{$user->role->name ?? 'No Role'}}

                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->status == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->updatedBy)
                                            {{  $user->updatedBy->username }}
                                            <br>
                                            {{ $user->updated_at }}
                                        @else
                                            {{ $user->createdBy->username  }}
                                            <br>
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
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

    </script>
@endsection
