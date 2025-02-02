@extends('layouts.master')

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
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="john@example.com" value="{{ $user->email }}">
                                <x-error key="email" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
