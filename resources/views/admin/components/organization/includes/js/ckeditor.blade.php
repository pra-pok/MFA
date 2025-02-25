<script>
    function coursegenerateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = (Math.random() * 16) | 0,
                v = c === 'x' ? r : (r & 0x3) | 0x8;
            return v.toString(16);
        });
    }
    $(document).ready(function () {
        $('#add-row').on('click', function () {
            let newRow = $('.file-clone-course:first').clone();
            let newIndex = coursegenerateUUID();
            // newRow.find('.accordion-header').attr('id', `course-heading-${newIndex}`);
            let newHeading = newRow.find('.accordion-header');
            let newButton = newRow.find('.accordion-button');
            let newCollapse = newRow.find('.accordion-collapse');
            newHeading.attr('id', `course-heading-${newIndex}`);
            newButton.attr('data-bs-target', `#course-collapse-${newIndex}`)
                .attr('aria-controls', `course-collapse-${newIndex}`)
                .removeClass("collapsed")
                .attr("aria-expanded", "true");
            newCollapse.attr('id', `course-collapse-${newIndex}`)
                .attr('aria-labelledby', `course-heading-${newIndex}`)
                .addClass("show")
                .removeAttr('data-bs-parent');
            newButton.text("New Course");
            newRow.find('select[name^="course_id"]').on("change", function () {
                let selectedText = $(this).find("option:selected").text();
                newButton.text(selectedText || "New Course");
            });
            newRow.find('select').prop('selectedIndex', 0);
            newRow.find('textarea').val('');
            newRow.find('input').val('');
            newRow.find('input[type="checkbox"][value="1"]').prop('checked', false);
            newRow.find(".ck-editor").remove();
            newRow.find('textarea').each(function () {
                $(this).val('');
                const newTextarea = $(this).clone();
                newTextarea.removeAttr('id');
                $(this).replaceWith(newTextarea);
                initializeEditor(newTextarea[0]);
            });
            $('.file-block-course').append(newRow);
            newRow.find('.select-course').select2();
        });
        $(document).on('click', '.remove-row', function () {
            if ($('.file-block-course .file-clone-course').length > 1) {
                let row = $(this).closest('.file-clone-course');
                let id = row.find("input[name^='id']").val();
                if (id) {
                    if (confirm("Are you sure you want to delete this course?")) {
                        $.ajax({
                            url: `/delete/${id}`,
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
                                alert("Error deleting course: " + xhr.responseText);
                            }
                        });
                    }
                } else {
                    row.remove(); // If no ID, just remove row
                }
            } else {
                alert("You must have at least one row.");
            }
        });

        // Toggle status checkbox values
        function toggleStatus() {
            $(document).on('change', '.checkbox', function () {
                const hiddenInput = $(this).siblings('input[type="hidden"]');
                hiddenInput.val(this.checked ? '1' : '0');
            });
        }
        toggleStatus();
    });

</script>
