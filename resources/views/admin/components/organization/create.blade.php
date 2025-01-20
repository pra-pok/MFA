@extends('admin.layouts.app')
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Create {{ $_panel }}</h5>

                    @include('admin.includes.flash_message_error')

                    <ul class="nav nav-tabs nav-fill" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-justified-profile" aria-controls="navs-justified-profile"
                                    aria-selected="true">
                            <span class="d-none d-sm-block">
                                <i class="tf-icons bx bx-user bx-sm me-1_5 align-text-bottom"></i> Basic Information
                            </span>
                                <i class="bx bx-user bx-sm d-sm-none"></i>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-justified-gallery" aria-controls="navs-justified-gallery"
                                    aria-selected="false">
                            <span class="d-none d-sm-block">
                                <i class="tf-icons bx bx-image bx-sm me-1_5 align-text-bottom"></i> Gallery
                            </span>
                                <i class="bx bx-image bx-sm d-sm-none"></i>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-justified-social" aria-controls="navs-justified-social"
                                    aria-selected="false">
                            <span class="d-none d-sm-block">
                                <i class="tf-icons bx bx-shocked bx-sm me-1_5 align-text-bottom"></i> Social Media
                            </span>
                                <i class="bx bx-shocked bx-sm d-sm-none"></i>
                            </button>
                        </li>
                    </ul>
                    <form action="{{ route($_base_route . '.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-justified-profile" role="tabpanel">
                            <div class="card-body">
                                    <div class="mb-3">
                                        <label for="administrative_area_id" class="form-label">Select Administrative Area Name</label>
                                        <select class="form-select required" id="administrative_area_id" name="administrative_area_id" aria-label="Select Administrative Area Name" >
                                            <option selected disabled>Select Administrative Area Name</option>
                                            @foreach ($data['area'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Select Type</label>
                                        <select class="form-select required" id="type" name="type" aria-label="Select Type">
                                            <option selected disabled>Select Type</option>
                                            @foreach ($data['type'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="name" class="form-label">College/School Name</label>
                                        <input type="text" name="name" class="form-control required" id="name" placeholder="Enter The College/School Name" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" name="slug" class="form-control required" id="slug" placeholder="Slug" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" name="address" class="form-control required" id="address" placeholder="Enter The Address" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control required" id="email" placeholder="Enter The Email" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="number" name="phone" class="form-control required" id="phone" placeholder="Enter Phone Number" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="text" name="website" class="form-control required" id="website" placeholder="Enter The Website" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Logo</label>
                                        <input type="file" name="logo_file" class="form-control required" id="logo" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="banner" class="form-label">Banner Image</label>
                                        <input type="file" name="banner_file" class="form-control required" id="banner" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control required" name="description" id="des" rows="6"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="search_keywords" class="form-label">Search Keywords</label>
                                        <textarea class="form-control" name="search_keywords" id="search_keywords" rows="3"></textarea>

                                    </div>
                                    <div class="mb-3">
                                        <label for="established_year" class="form-label ">Established Year</label>
                                        <input type="text" name="established_year" class="form-control required" id="established_year" placeholder="Enter Established Year" />
                                    </div>
                                @include('admin.includes.create_meta')
                                @include('admin.includes.create_status')
                            </div>
                        </div>

                        <div class="tab-pane fade" id="navs-justified-gallery" role="tabpanel">
                            <div class="card-body">
                                @include('admin.components.organization.includes.gallery')
                            </div>
                        </div>

                        <div class="tab-pane fade" id="navs-justified-social" role="tabpanel">
                            <div class="card-body">
                                @include('admin.components.organization.includes.social')
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Next</button>
                        </div>
                        <div style="margin-left: 125px; margin-top: -37px;">
                            <a href="{{ url()->previous() }}" class="btn rounded-pill btn-secondary"><i class="icon-base bx bx-arrow-back icon-sm"></i>Back</a>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @include('admin.includes.slug')
@endsection
