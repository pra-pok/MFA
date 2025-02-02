@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('role.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Create Role</h5>
            <div class="card-body">
                <form action="{{ route('role.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label class="form-label" for="name">Role Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Accountant">
                        <x-error key="name" />
                    </div>

                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection
