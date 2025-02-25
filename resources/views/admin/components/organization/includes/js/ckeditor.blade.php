<script>
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = (Math.random() * 16) | 0,
                v = c === 'x' ? r : (r & 0x3) | 0x8;
            return v.toString(16);
        });
    }

    // $('#add-row').on('click', function () {
    //     const newRow = $('.accordion-item:first').clone();
    //     newRow.find('select').prop('selectedIndex', 0);
    //     newRow.find('textarea').val('');
    //     newRow.find('input').val('');
    //     newRow.find('input[type="checkbox"][value="1"]').prop('checked', false);
    //     newRow.find(".ck-editor").remove();
    //     newRow.find('textarea').each(function () {
    //         $(this).val('');
    //         const newTextarea = $(this).clone();
    //         newTextarea.removeAttr('id');
    //         $(this).replaceWith(newTextarea);
    //         initializeEditor(newTextarea[0]);
    //     });
    //     $('.accordion').append(newRow);
    //     newRow.find('.select-course').select2();
    //
    // });
    // $(document).on('click', '.remove-row', function () {
    //     if ($('.accordion .accordion-item').length > 1) {
    //         let row = $(this).closest('.accordion-item');
    //         let id = row.find("input[name^='id']").val(); // Get hidden ID
    //         if (id) {
    //             if (confirm("Are you sure you want to delete this course?")) {
    //                 $.ajax({
    //                     url: `/delete/${id}`, // Route to delete
    //                     type: "DELETE",
    //                     headers: {
    //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //                     },
    //                     success: function (response) {
    //                         if (response.success) {
    //                             row.remove(); // Remove row from form
    //                         } else {
    //                             alert(response.message);
    //                         }
    //                     },
    //                     error: function (xhr) {
    //                         alert("Error deleting course: " + xhr.responseText);
    //                     }
    //                 });
    //             }
    //         } else {
    //             row.remove(); // If no ID, just remove row
    //         }
    //     } else {
    //         alert("You must have at least one row.");
    //     }
    // });
    // function toggleStatus() {
    //     $('.checkbox').on('change', function () {
    //         const hiddenInput = $(this).siblings('input[type="hidden"]');
    //         hiddenInput.val(this.checked ? '1' : '0');
    //     });
    // }
    // toggleStatus();

    $(document).ready(function () {
        $('#add-row').on('click', function () {
            let newIndex = Date.now();
            let newRow = $('.accordion-item:first').clone();
            // Assign new unique IDs for the accordion header, button, and collapse section
            let newHeading = newRow.find('.accordion-header');
            let newButton = newRow.find('.accordion-button');
            let newCollapse = newRow.find('.accordion-collapse');

            newHeading.attr('id', `heading-${newIndex}`);
            newButton.attr('data-bs-target', `#collapse-${newIndex}`)
                .attr('aria-controls', `collapse-${newIndex}`)
                .removeClass("collapsed")
                .attr("aria-expanded", "true");
            newCollapse.attr('id', `collapse-${newIndex}`)
                .attr('aria-labelledby', `heading-${newIndex}`)
                .addClass("show");

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
                initializeEditor(newTextarea[0]); // Reinitialize editor
            });

            // Append new row and initialize select2 for dropdowns
            $('.accordion').append(newRow);
            newRow.find('.select-course').select2();
        });

        // Handle row deletion with AJAX request
        $(document).on('click', '.remove-row', function () {
            if ($('.accordion .accordion-item').length > 1) {
                let row = $(this).closest('.accordion-item');
                let id = row.find("input[name^='id']").val(); // Get hidden ID

                if (id) {
                    if (confirm("Are you sure you want to delete this course?")) {
                        $.ajax({
                            url: `/delete/${id}`, // Route to delete
                            type: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    row.remove(); // Remove row from form
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
