@extends('admin.layouts.app')
@section('css')

@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Create {{ $_panel }}</h5>
                    @include('admin.includes.buttons.button-back')
                    @include('admin.includes.flash_message_error')
                    <ul class="nav nav-tabs nav-fill" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-justified-profile" aria-controls="navs-justified-profile"
                                    aria-selected="true">
                                Basic Information
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-justified-gallery" aria-controls="navs-justified-gallery"
                                    aria-selected="false">
                                Gallery
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-justified-social" aria-controls="navs-justified-social"
                                    aria-selected="false">
                                Social Media
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-justified-course" aria-controls="navs-justified-course"
                                    aria-selected="false">
                                Course
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-justified-page" aria-controls="navs-justified-page"
                                    aria-selected="false">
                                Page
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
                    </div>
                    <div class="" style="padding:20px;">
                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btnPrev btn-primary" id="prevBtn" >Previous</button>
                            <button type="button" class=" btn-primary" id="nextBtn">Next</button>
                            <button type="button" class="btnSave btn-success" id="saveBtn" >Save</button>
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
@endsection
