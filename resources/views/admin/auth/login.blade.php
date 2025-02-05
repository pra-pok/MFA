@extends('admin.layouts.auth')
@section('content')
    <form id="formAuthentication" class="mb-6" action="{{ route('mfa-admin.login') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label for="username" class="form-label">Username</label>
            <input class="form-control" type="text" name="username" :value="old( 'username')" required autofocus
                autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>
        {{--<img src="{{route('mfa_ext.file', ['file' => '1738490041.jpg'])}}" alt="">--}}


        <div class="mb-6 form-password-toggle">
            <label class="form-label" for="password">Password</label>
            <div class="input-group input-group-merge">
                <input id="password" class="form-control" type="password" name="password" required
                    autocomplete="current-password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
        </div>
        <div class="mb-8">
            <div class="d-flex justify-content-between mt-8">
                <div class="form-check mb-0 ms-2">
                    <input class="form-check-input" name="remember" type="checkbox" id="remember-me" />
                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        <span>Forgot Password?</span>
                    </a>
                @endif
            </div>
        </div>
        <div class="mb-6">
            <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
        </div>
    </form>
@endsection
