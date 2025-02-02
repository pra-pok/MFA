@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">
                <a href="{{ route('role.create') }}" class="btn btn-primary">Create Role</a>
            </h5>
            <div class="card-body">
                @include('includes.message')

                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th width="150">SN</th>
                                <th>Role</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('role.edit', $role) }}"><i
                                                        class="bx bx-edit-alt me-1"></i> Edit</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('role.assign-permissions', $role) }}"><i
                                                        class="bx bx-edit-alt me-1"></i> Assign Permissions</a>
                                                <form action="{{ route('role.destroy', $role) }}" method="POST"
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
