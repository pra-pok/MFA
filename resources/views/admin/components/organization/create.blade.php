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
    <script>
        $(document).ready(function () {

            const updateButtonsVisibility = () => {
                const currentTabIndex = $('.nav-tabs .nav-link.active').parent().index();
                const totalTabs = $('.nav-tabs .nav-link').length;
                if (currentTabIndex > 0) {
                    $('#prevBtn').show();
                } else {
                    $('#prevBtn').hide();
                }

                if (currentTabIndex === totalTabs - 1) {
                    $('#saveBtn').show();
                    $('#nextBtn').hide();
                } else {
                    $('#saveBtn').hide();
                    $('#nextBtn').show();
                }
            };

            $('#nextBtn').on('click', function (e) {
                e.preventDefault();
                const currentPane = $(".tab-pane.fade.show.active");
                const form = currentPane.find('form');
                if (typeof CKEDITOR !== 'undefined') {
                    Object.keys(editors).forEach(id => {
                        if (editors[id]) {
                            const editorData = editors[id].getData();
                            $(`#${id}`).val(editorData);
                        }
                    });
                }
                const formData = new FormData(form[0]);

                if (!validateTab(form)) {
                    alert('Please fill out all required fields.');
                    return;
                }
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === "success") {
                            console.log('Response:', response);
                            if (response.data) {
                                $('input[name="organization_id"]').val(response.data);
                            }
                            const currentTabIndex = $('.nav-tabs .nav-link.active').parent().index();
                            const nextTabIndex = currentTabIndex + 1;
                            const nextTabLink = $('.nav-tabs .nav-link').eq(nextTabIndex);
                            const nextTabPane = $('.tab-pane').eq(nextTabIndex);

                            if (nextTabLink.length && nextTabPane.length) {
                                $('.nav-tabs .nav-link.active').removeClass('active');
                                nextTabLink.addClass('active');
                                currentPane.removeClass('show active');
                                nextTabPane.addClass('show active');
                                updateButtonsVisibility();
                            } else {
                                alert('This is the last tab.');
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

            $('#prevBtn').on('click', function (e) {
                e.preventDefault();
                const currentTabIndex = $('.nav-tabs .nav-link.active').parent().index();
                const prevTabIndex = currentTabIndex - 1;
                const prevTabLink = $('.nav-tabs .nav-link').eq(prevTabIndex);
                const prevTabPane = $('.tab-pane').eq(prevTabIndex);

                if (prevTabLink.length && prevTabPane.length) {
                    $('.nav-tabs .nav-link.active').removeClass('active');
                    prevTabLink.addClass('active');
                    $('.tab-pane.fade.show.active').removeClass('show active');
                    prevTabPane.addClass('show active');
                    updateButtonsVisibility();
                }
            });
            $('#saveBtn').on('click', function (e) {
                e.preventDefault();
                const currentPane = $(".tab-pane.fade.show.active");
                const form = currentPane.find('form');
                const formData = new FormData(form[0]);
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === "success") {
                            alert('Data saved successfully!');
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
            updateButtonsVisibility();

            function validateTab(form) {
                let isValid = true;
                form.find('.required').each(function () {
                    if ($(this).val() === '') {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                return isValid;
            }

            $('#name').on('input', function () {
                var name = $(this).val();
                var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('#slug').val(slug);
            });

            const tableBody = $("#datatable tbody");

            tableBody.on("click", ".add-row", function (e) {
                e.preventDefault();
                const lastRow = tableBody.find("tr:last");
                const newRow = lastRow.clone();
                const rowCount = tableBody.find("tr").length + 1;

                newRow.find("td:first").text(rowCount);
                newRow.find("input, select").each(function () {
                    const input = $(this);
                    if (input.is("select")) {
                        const originalValue = tableBody.find("tr:first select").val();
                        input.val(originalValue);
                    } else if (input.is(":text") || input.is(":file")) {
                        input.val("");
                    } else if (input.is(":radio")) {
                        const baseName = input.attr("name").split("-")[0];
                        input.attr("name", `${baseName}-${rowCount}`);
                        input.attr("id", input.attr("id").split("-")[0] + `-${rowCount}`);
                        input.prop("checked", input.is("[defaultChecked]"));
                    }
                });
                tableBody.append(newRow);
            });

            tableBody.on("click", ".remove-row", function (e) {
                e.preventDefault();
                const rows = tableBody.find("tr");
                if (rows.length > 1) {
                    $(this).closest("tr").remove();
                    tableBody.find("tr").each(function (index) {
                        const row = $(this);
                        row.find("td:first").text(index + 1);
                        row.find("input, select").each(function () {
                            const input = $(this);
                            if (input.is(":radio")) {
                                const baseName = input.attr("name").split("-")[0];
                                input.attr("name", `${baseName}-${index + 1}`);
                                input.attr("id", input.attr("id").split("-")[0] + `-${index + 1}`);
                            }
                        });
                    });
                } else {
                    alert("At least one row must remain in the table.");
                }
            });

            tableBody.on("change", "input[type='radio'][name^='type']", function () {
                const row = $(this).closest("tr");
                const mediaText = row.find(".media-text");
                const mediaFile = row.find(".media-file");
                if ($(this).val() === "1") {
                    mediaText.hide();
                    mediaFile.show();
                } else if ($(this).val() === "0") {
                    mediaText.show();
                    mediaFile.hide();
                }
            });
            $('.select-course').select2();
            $('.add-row').click(function() {
                var newRow = $('.form-row:first').clone();
                newRow.find('input').val('');
                newRow.find('select').prop('selectedIndex', 0);
                newRow.find('textarea').val('');
                var rowCount = $('#form-rows .form-row').length + 1;
                newRow.find('.row-number').text(rowCount);
                $('#form-rows').append(newRow);
                newRow.find('.select-course').select2();
                newRow.find('.remove-row').click(function() {
                    if ($('#form-rows .form-row').length > 1) {
                        $(this).closest('.form-row').remove();
                    }
                });
            });
        });
    </script>
@endsection
