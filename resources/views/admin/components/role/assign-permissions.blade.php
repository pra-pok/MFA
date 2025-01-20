@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('role.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">Assign Permissions</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Assign Permissions</h5>
            <div class="card-body">
                <form action="{{ route('role.assign-permissions.save', $role->id) }}" method="POST">
                    @csrf

                    <p>Role: {{ $role->name }}</p>

                    @php
                        // Group permissions by their prefix (e.g., 'role' from 'role__create')
                        $permissionGroups = collect($permissions)->groupBy(function ($permission) {
                            return explode('__', $permission->name)[0];
                        });

                        // Get the list of permissions already assigned to the role by their IDs
                        $ownedPermissions = $role->permissions ? $role->permissions->pluck('id')->toArray() : [];
                    @endphp

                    @foreach ($permissionGroups as $groupName => $permissionGroup)
                        <div class="mb-6">
                            <small class="text-light fw-medium d-block">{{ ucFirst($groupName) }}</small>
                            @foreach ($permissionGroup as $permission)
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="checkbox" id="permission_{{ $permission->id }}"
                                           name="permissions[]" value="{{ $permission->id }}"
                                        @checked(in_array($permission->id, $ownedPermissions))>
                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        {{ explode('__', $permission->name)[1] }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary">Save Permissions</button>
                </form>
            </div>
        </div>
    </div>
@endsection
