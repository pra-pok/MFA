@extends('layouts.master')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Dashboard</h5>
            <div class="card-body">
                <p>{{ __("You're logged in!") }}</p>
            </div>
        </div>
    </div>
@endsection
