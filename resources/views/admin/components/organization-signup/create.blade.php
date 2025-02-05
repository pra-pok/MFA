@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $_panel }}</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <h5 class="card-header"> {{ $_panel }}</h5>
                    @include('admin.includes.buttons.button-back')
                    @include('admin.includes.flash_message_error')
                    <div class="card-body">
                        <form action="{{ route($_base_route . '.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="col-sm-6">
                                    <div class="mb-6">
                                        <label class="form-label" for="full_name">Full Name</label>
                                        <input type="text" class="form-control required" id="full_name"
                                               name="full_name">
                                        <x-error key="full_name"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-6">
                                        <label class="form-label" for="name">Username</label>
                                        <input type="text" class="form-control required" id="username" name="username">
                                        <x-error key="username"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                        <x-error key="email"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="phone">Contact Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone">
                                        <x-error key="phone"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-6">
                                        <label class="form-label" for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address">
                                        <x-error key="address"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-6 form-password-toggle">
                                        <label class="form-label" for="password">Password</label>
                                        <div class="input-group input-group-merge">
                                            <input id="password" class="form-control" type="password" name="password"
                                                   autocomplete="current-password" />
                                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-6 form-password-toggle">
                                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                                        <div class="input-group input-group-merge">
                                            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation"
                                                   autocomplete="current-password" />
                                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                                <br>

                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
