@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('team.index') }}">Teams</a></li>
                <li class="breadcrumb-item active">Assign Permissions</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Assign Permissions</h5>
            <div class="card-body">
                <form action="{{ route('team.assign-permissions', $team) }}" method="POST">
                    @csrf

                    @php
                        $permissionGroups = collect($permissions)
                            ->groupBy(function ($permission) {
                                $key = explode('__', $permission)[0];
                                return $key;
                            })
                            ->except(['team']);

                        $ownedPermissions = $teamSuperAdminRole->permissions->pluck('name')->toArray();
                    @endphp

                    @foreach ($permissionGroups as $groupName => $permissionGroup)
                        <div class="mb-6">
                            <small class="text-light fw-medium d-block">{{ ucFirst($groupName) }}</small>
                            @foreach ($permissionGroup as $permission)
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="checkbox" id="{{ $permission }}"
                                        name="permissions[]" value="{{ $permission }}" @checked(in_array($permission, $ownedPermissions))>
                                    <label class="form-check-label"
                                        for="{{ $permission }}">{{ explode('__', $permission)[1] }}</label>
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
