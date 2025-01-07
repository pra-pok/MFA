@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('team.index') }}">Teams</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Create Team</h5>
            <div class="card-body">
                <form action="{{ route('team.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label class="form-label" for="name">Team Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Demo Team">
                        <x-error key="name" />
                    </div>

                    <div class="card-body border mb-6">
                        <small class="text-light fw-medium">Team Super Admin Login</small>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-6">
                                    <label class="form-label" for="admin_email">Admin Email</label>
                                    <input type="text" class="form-control" id="admin_email" name="admin_email"
                                        placeholder="Email">
                                    <x-error key="admin_email" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="mb-6">
                                    <label class="form-label" for="admin_password">Admin Password</label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password"
                                        placeholder="Password" aria-describedby="passwordHelp">
                                    <x-error key="admin_password" />
                                    <div id="passwordHelp" class="form-text">
                                        Password should be at least 8 characters
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="mb-6">
                                    <label class="form-label" for="admin_password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control" id="admin_password_confirmation"
                                        name="admin_password_confirmation" placeholder="Confirm Password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection
