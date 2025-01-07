@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Assign Permissions</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Assign Permissions</h5>
            <div class="card-body">
                <form action="{{ route('user.assign-roles', $user) }}" method="POST">
                    @csrf

                    <p>User: {{ $user->name }}</p>

                    <div class="mb-6">
                        @foreach ($roles as $role)
                            <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" type="checkbox" id="{{ $role->name }}" name="roles[]"
                                    value="{{ $role->name }}" @checked($user->hasRole($role->name))>
                                <label class="form-check-label" for="{{ $role->name }}">{{ $role->name }}</label>
                            </div>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-primary">Save Roles</button>
                </form>
            </div>
        </div>
    </div>
@endsection
