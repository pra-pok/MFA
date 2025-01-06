@extends('layouts.master')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
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
                    </div>

                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection
