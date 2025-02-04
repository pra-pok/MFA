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

        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <h5 class="card-header"> {{ $_panel }}</h5>
                    <div class="card-body">
                        <form action="{{ route($_base_route . '.update' , $data['record']->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-6">
                                        <label class="form-label" for="full_name">Full Name</label>
                                        <input type="text" class="form-control required" id="full_name"
                                               name="full_name" value="{{$data['record']->full_name}}">
                                        <x-error key="full_name"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-6">
                                        <label class="form-label" for="name">Username</label>
                                        <input type="text" class="form-control required" id="username" name="username"value="{{$data['record']->username}}">
                                        <x-error key="username"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{$data['record']->email}}">
                                        <x-error key="email"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="phone">Contact Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{$data['record']->phone}}">
                                        <x-error key="phone"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" value="{{$data['record']->address}}">
                                        <x-error key="address"/>
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
                                        {{ isset($data['record']->status) && $data['record']->status == 1 ? 'checked' : '' }}
                                    />
                                    <label class="form-check-label" for="activeStatus"> Active </label>

                                    <input
                                        name="status"
                                        class="form-check-input"
                                        type="radio"
                                        value="0"
                                        id="deactiveStatus"
                                        {{ isset($data['record']->status) && $data['record']->status == 0 ? 'checked' : '' }}
                                    />
                                    <label class="form-check-label" for="deactiveStatus"> In-Active </label>

                                </div>
                                <br>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
