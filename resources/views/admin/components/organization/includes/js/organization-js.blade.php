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
                        $('input[name="organization_id"]').val(response.data);
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

            // Ensure organization_id is correct
            const organizationId = $('input[name="organization_id"]').val();
            if (!organizationId) {
                alert("Organization ID is missing.");
                return;
            }

            const formData = new FormData(form[0]);
            formData.append('organization_id', organizationId); // Append organization_id manually if necessary

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
        $('#country_id').change(function () {
            var idcountry = this.value;
            $("#parent_id").html('<option value="">None</option>'); // Reset the parent dropdown
            if (idcountry) {
                $.ajax({
                    url: '/organization/get-parents-by-country', // Replace with your route URL
                    type: "GET",
                    data: {
                        id: idcountry,
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
        });
    });

</script>
