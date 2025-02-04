@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Create User</h5>
            <div class="card-body">
                <form action="{{ route('user.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-6">
                                <label class="form-label" for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="John Doe">
                                <x-error key="name" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-6">
                            <label for="role_id" class="form-label">Select Role</label>
                            <select class="form-select" id="role_id" name="role_id" aria-label="Select Role">
                                <option selected disabled>Select Role</option>
                                @foreach ($data['roles'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-6">
                                <label class="form-label" for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       placeholder="john123">
                                <x-error key="username" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-6">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="john@example.com">
                                <x-error key="email" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-6">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" aria-describedby="passwordHelp">
                                <x-error key="password" />
                                <div id="passwordHelp" class="form-text">
                                    Password should be at least 8 characters
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-6">
                                <label class="form-label" for="password_confirmation">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Confirm Password">
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
                                checked
                            />
                            <label class="form-check-label" for="activeStatus"> Active </label>

                            <input
                                name="status"
                                class="form-check-input"
                                type="radio"
                                value="0"
                                id="deactiveStatus"

                            />
                            <label class="form-check-label" for="deactiveStatus"> In-Active </label>
                        </div> <br>

                    </div><br>

                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection
