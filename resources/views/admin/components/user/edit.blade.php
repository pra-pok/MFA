@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Edit User</h5>
            <div class="card-body">
                <form action="{{ route('user.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')


                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-6">
                                <label class="form-label" for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="John Doe" value="{{ $user->name }}">
                                <x-error key="name" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-6">
                                <label for="role_id" class="form-label">Select Role</label>
                                <select class="form-select" id="role_id" name="role_id" aria-label="Select Role">
                                    <option selected disabled>Select Role</option>
                                    @foreach ($data['roles'] as $key => $value)
                                        <option value="{{ $key }}" {{ $key == $user->role_id ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-6">
                                <label class="form-label" for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="{{$user->username}}">
                                <x-error key="username" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-6">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="john@example.com" value="{{ $user->email }}">
                                <x-error key="email" />
                            </div>
                        </div>
                        <div>
                            <label for="status" class="form-label">Status</label>

                            <input
                                name="status"
                                class="form-check-input"
                                type="radio"
                                value="1"
                                id="activeStatus"
                                {{ isset($user->status) && $user->status == 1 ? 'checked' : '' }}
                            />
                            <label class="form-check-label" for="activeStatus"> Active </label>

                            <input
                                name="status"
                                class="form-check-input"
                                type="radio"
                                value="0"
                                id="deactiveStatus"
                                {{ isset($user->status) && $user->status == 0 ? 'checked' : '' }}
                            />
                            <label class="form-check-label" for="deactiveStatus"> In-Active </label>

                        </div>
                    </div> <br>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
