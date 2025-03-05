<script>
    $(document).ready(function () {
        $('.nav-tabs button').removeAttr('role data-bs-toggle');
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
            if (!form.valid()) {
                return; // Exit if the form is not valid
            }

            if (typeof CKEDITOR !== 'undefined') {
                document.querySelectorAll('.editor').forEach((element) => {
                    const editorInstance = editors[element.id || element.getAttribute('id')];
                    if (editorInstance) {
                        $(element).val(editorInstance.getData());
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
                        if(currentTabIndex ===0 && response.data){
                            $('input[name="organization_id"]').val(response.data);
                        }
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
                        showValidationErrors(form, errors);
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
            for (let editorId in editors) {
                const editor = editors[editorId];
                const textarea = document.getElementById(editorId);
                if (textarea) {
                    textarea.value = editor.getData();
                }
            }
            const currentPane = $(".tab-pane.fade.show.active");
            const form = currentPane.find('form');
            const organizationId = $('input[name="organization_id"]').val();
            if (!organizationId) {
                alert("Organization ID is missing.");
                return;
            }
            $('.select-course').select2();
            $('.select-page').select2();
            const formData = new FormData(form[0]);
            formData.append('organization_id', organizationId);
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
        // const tableBody = $("#datatable tbody");
        // tableBody.on("click", ".add-row", function (e) {
        //     e.preventDefault();
        //     const lastRow = tableBody.find("tr:last");
        //     const newRow = lastRow.clone();
        //     const rowCount = tableBody.find("tr").length + 1;
        //     newRow.find("td:first").text(rowCount);
        //     newRow.find("input, select").each(function () {
        //         const input = $(this);
        //         if (input.is("select")) {
        //             const originalValue = tableBody.find("tr:first select").val();
        //             input.val(originalValue);
        //         } else if (input.is(":text") || input.is(":file")) {
        //             input.val("");
        //         } else if (input.is(":radio")) {
        //             const baseName = input.attr("name").split("-")[0];
        //             input.attr("name", `${baseName}-${rowCount}`);
        //             input.attr("id", input.attr("id").split("-")[0] + `-${rowCount}`);
        //             input.prop("checked", input.is("[defaultChecked]"));
        //         }
        //     });
        //     tableBody.append(newRow);
        // });
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
        var defaultCountryId = $('#country_id').val();
        if (defaultCountryId) {
            loadAdministrativeAreas(defaultCountryId);
        }

        // When the country changes
        $('#country_id').change(function () {
            var countryId = this.value;
            $("#parent_id").html('<option value="">None</option>');
            if (countryId) {
                loadAdministrativeAreas(countryId);
            }
        });

        // When administrative area changes
        $('#parent_id').change(function () {
            var parentId = this.value;
            $("#district_id").html('<option value="">None</option>');
            if (parentId) {
                loadDistricts(parentId);
            }
        });

        // When district changes
        $('#district_id').change(function () {
            var districtId = this.value;
            $("#locality_id").html('<option value="">None</option>');
            if (districtId) {
                loadLocalities(districtId);
            }
        });

        // Function to load administrative areas (provinces/states)
        function loadAdministrativeAreas(countryId) {
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
                    }
                }
            });
        }

        // Function to load districts
        function loadDistricts(parentId) {
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
                    }
                }
            });
        }

        // Function to load localities
        function loadLocalities(districtId) {
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
    function showValidationErrors(form, errors) {
        form.find('.error').remove(); // Remove previous errors

        for (const field in errors) {
            const input = form.find(`[name="${field}"]`);
            if (input.length) {
                input.after(`<span class="error text-danger">${errors[field][0]}</span>`); // Show error below input
            }
        }
    }

    /**
     * Initialize form validation
     */
    function bindFormValidator() {
        $("form").each(function () {
            const formInstance = $(this);
            formInstance.validate({
                errorPlacement: function (error, element) {
                    error.addClass('text-danger');
                    error.insertAfter(element); // Show error below field
                }
            });

            if (!formInstance.hasClass("ajax")) {
                handleValidation(formInstance);
            }
        });
    }
</script>
