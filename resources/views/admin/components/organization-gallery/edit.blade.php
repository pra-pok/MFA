@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
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
                            <div>
                                <label for="stream_id" class="form-label">Select Stream Name</label>
                                <select class="form-select" id="stream_id" name="stream_id" aria-label="Select Stream Name">
                                    <option selected disabled>Select Stream Name</option>
                                    @foreach ($data['stream'] as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ $data['record']->stream_id === $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="level_id" class="form-label">Select Level Name</label>
                                <select class="form-select" id="level_id" name="level_id" aria-label="Select Level Name">
                                    <option selected disabled>Select Level Name</option>
                                    @foreach ($data['level'] as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ $data['record']->level_id === $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="title" class="form-label">Title</label>
                                <input
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    id="title"
                                    value="{{$data['record']->title}}"/>
                            </div>
                            <div>
                                <label for="slug" class="form-label">Slug</label>
                                <input
                                    type="text"
                                    name="slug"
                                    class="form-control"
                                    id="slug" value="{{$data['record']->slug}}"  />
                            </div>
                            <div>
                                <label for="rank" class="form-label">Rank</label>
                                <input
                                    type="number"
                                    name="rank"
                                    class="form-control"
                                    id="rank" value="{{$data['record']->rank}}" />
                            </div>
                            <div>
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="des" rows="3">{!! $data['record']->description !!}</textarea>
                            </div>
                            <div>
                                <label for="eligibility" class="form-label">Eligibility</label>
                                <textarea class="form-control" name="eligibility" id="eligibility" rows="3">{!! $data['record']->eligibility !!}</textarea>
                            </div>
                            <div>
                                <label for="job_prospects" class="form-label">Job Prospects</label>
                                <textarea class="form-control" name="job_prospects" id="job_prospects" rows="3">{!! $data['record']->job_prospects !!}</textarea>
                            </div>
                            <div>
                                <label for="syllabus" class="form-label">Syllabus</label>
                                <textarea class="form-control" name="syllabus" id="syllabus" rows="3">{!! $data['record']->syllabus !!}</textarea>
                            </div>
                            <div>
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input
                                    type="text"
                                    name="meta_title"
                                    class="form-control"
                                    id="meta_title"
                                    value="{{$data['record']->meta_title}}"/>
                            </div>
                            <div>
                                <label for="meta_keywords" class="form-label">Meta Keyword</label>
                                <input
                                    type="text"
                                    name="meta_keywords"
                                    class="form-control"
                                    id="meta_keywords"
                                    value="{{$data['record']->meta_keywords}}"/>
                            </div>
                            <div>
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <input
                                    type="text"
                                    name="meta_description"
                                    class="form-control"
                                    id="meta_description"
                                    value="{{$data['record']->meta_description}}"/>
                            </div><br>
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
                                    <label class="form-check-label" for="deactiveStatus"> De-Active </label>

                            </div>
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

