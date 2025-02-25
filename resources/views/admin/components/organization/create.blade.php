@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-6">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Create {{ $_panel }}</h5>
                    @include('admin.includes.buttons.button-back')
                    @include('admin.includes.flash_message_error')
                    <div class="card-header">
                        <ul class="nav nav-tabs nav-fill" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-justified-profile" aria-controls="navs-justified-profile"
                                        aria-selected="true">
                                    <i class='bx bx-user'></i>  Basic Information
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-justified-gallery" aria-controls="navs-justified-gallery"
                                        aria-selected="false">
                                    <i class='bx bx-image' ></i>  Gallery
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-justified-social" aria-controls="navs-justified-social"
                                        aria-selected="false">
                                    <i class='bx bxl-meta'></i>  Social Media
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-justified-course" aria-controls="navs-justified-course"
                                        aria-selected="false">
                                    <i class='bx bx-book'></i> Course
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-justified-page" aria-controls="navs-justified-page"
                                        aria-selected="false">
                                    <i class='bx bx-first-page'></i> Page
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-justified-facilities"
                                        aria-controls="navs-justified-facilities"
                                        aria-selected="false">
                                    <i class='bx bxs-face-mask'></i>  Facilities
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-justified-member"
                                        aria-controls="navs-justified-member"
                                        aria-selected="false">
                                    <i class='bx bxs-group'></i>  Members
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content mt-3">
                            <!-- Basic Information Tab -->
                            <div class="tab-pane fade show active" id="navs-justified-profile" role="tabpanel">
                                @include('admin.components.organization.includes.basic_information')
                            </div>
                            <!-- Gallery Tab -->
                            <div class="tab-pane fade" id="navs-justified-gallery" role="tabpanel">
                                @include('admin.components.organization.includes.gallery')
                            </div>
                            <!-- Social Media Tab -->
                            <div class="tab-pane fade" id="navs-justified-social" role="tabpanel">
                                @include('admin.components.organization.includes.social')
                            </div>
                            <!-- Course Tab -->
                            <div class="tab-pane fade" id="navs-justified-course" role="tabpanel">
                                @include('admin.components.organization.includes.course')
                            </div>
                            <!-- Page Tab -->
                            <div class="tab-pane fade" id="navs-justified-page" role="tabpanel">
                                @include('admin.components.organization.includes.page')
                            </div>
                            <div class="tab-pane fade" id="navs-justified-facilities" role="tabpanel">
                                @include('admin.components.organization.includes.facilities')
                            </div>
                            <div class="tab-pane fade" id="navs-justified-member" role="tabpanel">
                                @include('admin.components.organization.includes.member')
                            </div>
                        </div>
                        <div class="" style="padding:20px;">
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-primary" id="prevBtn" style="display: none;">Previous</button>
                                <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                                <button type="button" class="btn btn-success btn-sm" id="saveBtn" style="display: none;"><i class="bx bx-save"></i>Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('admin.components.organization.includes.js.organization-js')
    @include('admin.components.organization.includes.js.ckeditor')
    @include('admin.components.organization.includes.js.page-add-js')
    @include('admin.components.organization.includes.js.member')
@endsection
