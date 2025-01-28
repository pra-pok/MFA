@extends('admin.layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Edit {{ $_panel }}</h5>
                    @include('admin.includes.buttons.button-back')
                    @include('admin.includes.flash_message_error')
                    <div class="card-body">
                        <form action="{{ route($_base_route . '.update', $data['record']->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mt-3">
                                <label for="stream_id" class="form-label">Select Stream Name</label>
                                <select class="form-select" id="stream_id" name="stream_id" aria-label="Select Stream Name" required>
                                    <option selected disabled>Select Stream Name</option>
                                    @foreach ($data['stream'] as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ $data['record']->stream_id === $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-3">
                                <label for="level_id" class="form-label">Select Level Name</label>
                                <select class="form-select" id="level_id" name="level_id" aria-label="Select Level Name" required>
                                    <option selected disabled>Select Level Name</option>
                                    @foreach ($data['level'] as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ $data['record']->level_id === $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-3">
                                <label for="title" class="form-label">Title</label>
                                <input
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    id="title"
                                    value="{{$data['record']->title}}" required/>
                            </div>
                            <div class="mt-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input
                                    type="text"
                                    name="slug"
                                    class="form-control"
                                    id="slug" value="{{$data['record']->slug}}"  />
                            </div>
                            <div class="mt-3">
                                <label for="rank" class="form-label">Rank</label>
                                <input
                                    type="number"
                                    name="rank"
                                    min="0"
                                    max="100"
                                    class="form-control"
                                    id="rank" value="{{$data['record']->rank}}"  />
                            </div>
                            <div class="mt-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control editor" name="description"  rows="3" >{!! $data['record']->description !!}</textarea>
                            </div>
                            <div class="mt-3">
                                <label for="eligibility" class="form-label">Eligibility</label>
                                <textarea class="form-control editor" name="eligibility"  rows="3" >{!! $data['record']->eligibility !!}</textarea>
                            </div>
                            <div class="mt-3">
                                <label for="job_prospects" class="form-label">Job Prospects</label>
                                <textarea class="form-control editor" name="job_prospects"  rows="3" >{!! $data['record']->job_prospects !!}</textarea>
                            </div>
                            <div class="mt-3">
                                <label for="syllabus" class="form-label">Syllabus</label>
                                <textarea class="form-control editor" name="syllabus"  rows="3" >{!! $data['record']->syllabus !!}</textarea>
                            </div>
                            @include('admin.includes.edit_meta')
                            @include('admin.includes.edit_status')
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Update</button>
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

