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
                                    aria-selected="false" id="gallery-tab">
                            <span class="d-none d-sm-block">
                                <i class="tf-icons bx bx-image bx-sm me-1_5 align-text-bottom"></i> Gallery
                            </span>
                                <i class="bx bx-image bx-sm d-sm-none"></i>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-justified-social" aria-controls="navs-justified-social"
                                    aria-selected="false" id="social-tab">
                            <span class="d-none d-sm-block">
                                <i class="tf-icons bx bx-shocked bx-sm me-1_5 align-text-bottom"></i> Social Media
                            </span>
                                <i class="bx bx-shocked bx-sm d-sm-none"></i>
                            </button>
                        </li>
                    </ul>
                    <form action="{{ route($_base_route . '.store') }}" method="POST" id="organizationForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="current_tab" id="currentTab" value="navs-justified-profile">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="navs-justified-profile" role="tabpanel">
                                <div class="card-body">
                                    @include('admin.components.organization.includes.basic_information')
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
                            <div>
                                <button type="submit" id="nextBtn" class="btn btn-primary">Next</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('#name').on('input', function () {
                var name = $(this).val();
                var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('#slug').val(slug);
            });
            $('#nextBtn').on('click', function (e) {
                e.preventDefault();
                const form = $('#organizationForm');
                const currentTab = $('#currentTab').val();
                const currentPane = $('#' + currentTab);
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: new FormData(form[0]),
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            const nextTab = currentPane.next('.tab-pane').attr('id');
                            if (nextTab) {
                                currentPane.removeClass('show active');
                                $('#' + nextTab).addClass('show active');
                                $('#currentTab').val(nextTab);

                                $('.nav-tabs .nav-link.active').removeClass('active');
                                $('[data-bs-target="#' + nextTab + '"]').addClass('active');
                            } else {
                                alert('No more tabs to navigate.');
                            }
                        } else {
                            alert(response.message || 'An error occurred while saving data.');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = '';
                            for (const field in errors) {
                                errorMessages += `${errors[field][0]}\n`;
                            }
                            alert(errorMessages);
                        } else {
                            alert('An unexpected error occurred. Please try again.');
                        }
                    }
                });
            });
        });
    </script>
@endsection
