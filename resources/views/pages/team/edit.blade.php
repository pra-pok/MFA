@extends('layouts.master')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('team.index') }}">Teams</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Edit Team</h5>
            <div class="card-body">
                <form action="{{ route('team.update', $team->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label class="form-label" for="name">Team Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Demo Team"
                            value="{{ $team->name }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
