@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('#nextBtn').on('click', function (e) {
                e.preventDefault();

                const currentPane = $(".tab-pane.fade.show.active");
                const form = currentPane.find('form');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: new FormData(form[0]),
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === "success") {
                            // Log the success response
                            console.log('Response:', response);

                            // Update hidden input for organization_id
                            $('input[name="organization_id"]').val(response.data);

                            // Show success message
                            alert(response.message);

                            // Move to the next tab
                            const nextTab = currentPane.next('.tab-pane.fade');
                            if (nextTab.length) {
                                currentPane.removeClass('show active'); // Hide current pane
                                nextTab.addClass('show active'); // Show next pane

                                // Update tab link styles
                                $('.nav-tabs .nav-link.active').removeClass('active');
                                $('[data-bs-target="#' + nextTab.attr('id') + '"]').addClass('active');
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
        });
    </script>
@endsection
