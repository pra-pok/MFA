<script>
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = (Math.random() * 16) | 0,
                v = c === 'x' ? r : (r & 0x3) | 0x8;
            return v.toString(16);
        });
    }
    $(document).ready(function () {
        $('#add-row-page').on('click', function () {
            const newRow = $('.clone-file-page:first').clone();
            const newIndex = generateUUID();
            let newHeading = newRow.find('.accordion-header');
            let newButton = newRow.find('.accordion-button');
            let newCollapse = newRow.find('.accordion-collapse');

            newHeading.attr('id', `page-heading-${newIndex}`);
            newButton.attr('data-bs-target', `#page-collapse-${newIndex}`)
                .attr('aria-controls', `page-collapse-${newIndex}`)
                .removeClass("collapsed")
                .attr("aria-expanded", "true")
                .text(`New Page`);
            newCollapse.attr('id', `page-collapse-${newIndex}`)
                .attr('aria-labelledby', `page-heading-${newIndex}`)
                .addClass("show")
                .removeAttr('data-bs-parent');
            newButton.text("New Page");
            newRow.find('select[name^="page_category_id"]').on("change", function () {
                let selectedText = $(this).find("option:selected").text();
                newButton.text(selectedText || "New Page");
            });
            // Reset form values
            newRow.find('select').prop('selectedIndex', 0);
            newRow.find('textarea').val('');
            newRow.find('input').val('');
            newRow.find('input[type="checkbox"]').prop('checked', true);
            newRow.find('input[type="hidden"]').val('0');

            // Fix CKEditor instance (if applicable)
            newRow.find(".ck-editor").remove();
            newRow.find('textarea').each(function () {
                $(this).val('');
                const newTextarea = $(this).clone();
                newTextarea.removeAttr('id');
                $(this).replaceWith(newTextarea);
                initializeEditor(newTextarea[0]); // Ensure CKEditor initializes
            });

            $('.file-block-page').append(newRow);
            newRow.find('.select-page').select2();
        });

        $(document).on('click', '.remove-row-page', function () {
            if ($('.file-block-page .clone-file-page').length > 1) {
                let row = $(this).closest('.clone-file-page');
                let id = row.find("input[name^='id']").val();
                if (id) {
                    if (confirm("Are you sure you want to delete this page?")) {
                        $.ajax({
                            url: `/page-delete/${id}`,
                            type: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    row.remove();
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function (xhr) {
                                alert("Error deleting page: " + xhr.responseText);
                            }
                        });
                    }
                } else {
                    row.remove();
                }
            } else {
                alert("You must have at least one row.");
            }
        });

        function toggleStatus() {
            $(document).on('change', '.status-checkbox', function () {
                const hiddenInput = $(this).siblings('input[type="hidden"]');
                hiddenInput.val(this.checked ? '1' : '0');
            });
        }

        toggleStatus();
    });

</script>
