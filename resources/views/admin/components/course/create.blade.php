@extends('admin.layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Create {{ $_panel }}</h5>
                    @include('admin.includes.buttons.button-back')
                    @include('admin.includes.flash_message_error')
                    <div class="card-body">
                        <form action="{{ route($_base_route . '.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mt-3">
                                <label for="stream_id" class="form-label">Select Stream Name</label>
                                <select class="form-select required" id="stream_id" name="stream_id" aria-label="Select Stream Name" >
                                    <option selected disabled>Select Stream Name</option>
                                    @foreach ($data['stream'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('stream_id'))
                                    <div class="error">{{ $errors->first('stream_id') }}</div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <label for="level_id" class="form-label">Select Level Name</label>
                                <select class="form-select required" id="level_id" name="level_id" aria-label="Select Level Name" >
                                    <option selected disabled>Select Level Name</option>
                                    @foreach ($data['level'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('level_id'))
                                    <div class="error">{{ $errors->first('level_id') }}</div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <label for="title" class="form-label">Title</label>
                                <input
                                    type="text"
                                    name="title"
                                    class="form-control required"
                                    id="title"
                                    placeholder="Enter The Title" />

                                @if($errors->has('title'))
                                    <div class="error">{{ $errors->first('title') }}</div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input
                                    type="text"
                                    name="slug"
                                    class="form-control required"
                                    id="slug" placeholder="slug" />
                            </div>
                            <div class="mt-3">
                                <label for="short_title" class="form-label">Short Title</label>
                                <input
                                    type="text"
                                    name="short_title"
                                    class="form-control required"
                                    id="short_title"
                                    placeholder="Enter The Short Title" />
                                @if($errors->has('short_title'))
                                    <div class="error">{{ $errors->first('short_title') }}</div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <label for="rank" class="form-label">Rank</label>
                                <input
                                    type="number"
                                    name="rank"
                                    min="0"
                                    max="100"
                                    class="form-control required"
                                    id="rank" placeholder="Enter number i.e. ( 1,2,3...)"  />
                                @if($errors->has('rank'))
                                    <div class="error">{{ $errors->first('rank') }}</div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control required" name="description" id="des" rows="3" ></textarea>
                            </div>
                            <div class="mt-3">
                                <label for="eligibility" class="form-label">Eligibility</label>
                                <textarea class="form-control" name="eligibility" id="eligibility" rows="3" ></textarea>
                            </div>
                            <div class="mt-3">
                                <label for="job_prospects" class="form-label">Job Prospects</label>
                                <textarea class="form-control" name="job_prospects" id="job_prospects" rows="3" ></textarea>
                            </div>
                            <div class="mt-3">
                                <label for="syllabus" class="form-label">Syllabus</label>
                                <textarea class="form-control" name="syllabus" id="syllabus" rows="3" ></textarea>
                            </div>
                            @include('admin.includes.create_meta')
                            @include('admin.includes.create_status')
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
       </div>
    </div>
@endsection
@section('js')
    @include('admin.includes.slug')
@endsection
