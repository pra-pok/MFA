<script>
    $(document).ready(function () {
        const updateButtonsVisibility = () => {
            const currentTabIndex = $('.nav-tabs .nav-link.active').parent().index();
            const totalTabs = $('.nav-tabs .nav-link').length;
            if (currentTabIndex > 0) {
                $('#edit-prevBtn').show();
            } else {
                $('#edit-prevBtn').hide();
            }
            if (currentTabIndex === totalTabs - 1) {
                $('#edit-saveBtn').show();
                $('#edit-nextBtn').hide();
            } else {
                $('#edit-saveBtn').hide();
                $('#edit-nextBtn').show();
            }
        };
        $('#edit-nextBtn').on('click', function (e) {
            e.preventDefault();
            const currentPane = $(".tab-pane.fade.show.active");
            const form = currentPane.find('form');
            if (typeof CKEDITOR !== 'undefined') {
                document.querySelectorAll('.editor').forEach((element) => {
                    const editorInstance = editors[element.id || element.getAttribute('id')];
                    if (editorInstance) {
                        const editorData = editorInstance.getData();
                        $(element).val(editorData);
                    }
                });
            }
            const formData = new FormData(form[0]);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.status === "success") {
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
        $('#edit-prevBtn').on('click', function (e) {
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
        $('#edit-saveBtn').on('click', function (e) {
            e.preventDefault();
            const currentPane = $(".tab-pane.fade.show.active");
            const form = currentPane.find('form');
            if (typeof CKEDITOR !== 'undefined') {
                document.querySelectorAll('.editor').forEach((element) => {
                    const editorInstance = editors[element.id || element.getAttribute('id')];
                    if (editorInstance) {
                        const editorData = editorInstance.getData();
                        $(element).val(editorData);
                    }
                });
            }
            const formData = new FormData(form[0]);
            $('.member-clone-file').each(function (i) {
                console.log(`Row ${i} - Name:`, $(this).find('input[name^="name"]').val());
            });
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
            const rowCount = tableBody.find("tr").length;
            newRow.find("td:first").text(rowCount);
            newRow.find("input, select").each(function () {
                const input = $(this);
                const name = input.attr("name");
                if (name) {
                    const newName = name.replace(/\[\d+\]/, `[${rowCount}]`);
                    input.attr("name", newName);
                }
                if (input.is("select")) {
                    const originalValue = tableBody.find("tr:first select").val();
                    input.val(originalValue);
                } else if (input.is(":text") || input.is(":file") || input.is("input[type='number']")) {
                    input.val("");
                } else if (input.is(":radio")) {
                    const baseName = name.match(/[a-zA-Z_]+/)[0];
                    input.attr("name", `${baseName}[${rowCount}]`);
                    input.attr("id", input.attr("id").split("-")[0] + `-${rowCount}`);
                    input.prop("checked", input.val() === "1");
                } else if (input.is(":checkbox")) {
                    input.prop("checked", false);
                }
            });
            tableBody.append(newRow);
        });
        tableBody.on("click", ".remove-row", function (e) {
            e.preventDefault();
            const rows = tableBody.find("tr");
            if (rows.length > 1) {
                let row = $(this).closest("tr");
                let id = row.find("input[name^='id']").val();
                if (id) {
                    if (confirm("Are you sure you want to delete this gallery?")) {
                        $.ajax({
                            url: `/organization_gallery/gallery-delete/${id}`,
                            type: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    row.remove();
                                    updateTableIndexes();
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function (xhr) {
                                alert("Error deleting gallery: " + xhr.responseText);
                            }
                        });
                    }
                } else {
                    row.remove();
                    updateTableIndexes();
                }
            } else {
                alert("At least one row must remain in the table.");
            }
        });
        function updateTableIndexes() {
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
        }
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
        const modal = $('#imageModal');
        const modalImage = $('#fullSizeImage');
        $('.clickable-image').on('click', function () {
            const fullImageSrc = $(this).data('bs-image');
            modalImage.attr('src', fullImageSrc);
        });
        var defaultCountryId = $('#country_id').val();
        var selectedParentId = $('#parent_id').attr('data-id');
        var selectedDistrictId = $('#district_id').attr('data-id');
        var selectedLocalityId = $('#locality_id').attr('data-id');
        if (defaultCountryId) {
            loadAdministrativeAreas(defaultCountryId, selectedParentId, function() {
                if (selectedParentId) {
                    loadDistricts(selectedParentId, selectedDistrictId, function() {
                        if (selectedDistrictId) {
                            loadLocalities(selectedDistrictId, selectedLocalityId);
                        }
                    });
                }
            });
        }
        $('#country_id').change(function () {
            var countryId = this.value;
            $("#parent_id").html('<option value="">None</option>');
            $("#district_id").html('<option value="">None</option>');
            $("#locality_id").html('<option value="">None</option>');

            if (countryId) {
                loadAdministrativeAreas(countryId, null);
            }
        });
        $('#parent_id').change(function () {
            var parentId = this.value;
            $("#district_id").html('<option value="">None</option>');
            $("#locality_id").html('<option value="">None</option>');

            if (parentId) {
                loadDistricts(parentId, null);
            }
        });
        $('#district_id').change(function () {
            var districtId = this.value;
            $("#locality_id").html('<option value="">None</option>');

            if (districtId) {
                loadLocalities(districtId, null);
            }
        });
        function loadAdministrativeAreas(countryId, selectedParentId, callback = null) {
            $.ajax({
                url: '/organization/get-parents-by-country',
                type: "GET",
                data: {
                    id: countryId,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function (result) {
                    if (result.parents) {
                        $.each(result.parents, function (key, value) {
                            $("#parent_id").append('<option value="' + key + '">' + value + '</option>');
                        });
                        if (selectedParentId) {
                            $('#parent_id').val(selectedParentId).trigger('change');
                        }
                        if (callback) callback();
                    }
                }
            });
        }
        function loadDistricts(parentId, selectedDistrictId, callback = null) {
            $.ajax({
                url: '/organization/get-districts-by-parent',
                type: "GET",
                data: {
                    id: parentId,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function (result) {
                    if (result.districts) {
                        $.each(result.districts, function (key, value) {
                            $("#district_id").append('<option value="' + key + '">' + value + '</option>');
                        });

                        if (selectedDistrictId) {
                            $('#district_id').val(selectedDistrictId).trigger('change');
                        }

                        if (callback) callback();
                    }
                }
            });
        }
        function loadLocalities(districtId, selectedLocalityId) {
            $.ajax({
                url: '/organization/get-localities-by-district',
                type: "GET",
                data: {
                    id: districtId,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function (result) {
                    if (result.localities) {
                        $.each(result.localities, function (key, value) {
                            $("#locality_id").append('<option value="' + key + '">' + value + '</option>');
                        });

                        if (selectedLocalityId) {
                            $('#locality_id').val(selectedLocalityId).trigger('change');
                        }
                    }
                }
            });
        }
        $('#parent_id').select2();
        $('.select-country').select2();
        $('.select-type').select2();
        $('.select-university').select2();
        $('.district').select2();
        $('.locality').select2();
    });
</script>
