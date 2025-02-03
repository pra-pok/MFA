<script>
    $('#add-row-page').on('click', function () {
        const newRow = $('.clone-file-page:first').clone();
        newRow.find('select').prop('selectedIndex', 0);
        newRow.find('textarea').val('');
        newRow.find('input').val('');
        newRow.find('input[type="checkbox"][value="1"]').prop('checked', false);
        // newRow.find('input[type="checkbox"]').prop('checked', false);
        newRow.find(".ck-editor").remove();
        newRow.find('textarea').each(function () {
            $(this).val('');
            const newTextarea = $(this).clone();
            newTextarea.removeAttr('id');
            $(this).replaceWith(newTextarea);
            initializeEditor(newTextarea[0]);
        });
        $('.file-block-page').append(newRow);
        newRow.find('.select-page').select2();
    });
    $(document).on('click', '.remove-row-page', function () {
        if ($('.file-block-page .clone-file-page').length > 1) {
            let row = $(this).closest('.clone-file-page');
            let id = row.find("input[name^='id']").val();
            if (id) {
                if (confirm("Are you sure you want to delete this course?")) {
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
                            alert("Error deleting course: " + xhr.responseText);
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
    // $(document).on('click', '.remove-row-page', function () {
    //     if ($('.clone-file-page').length > 1) {
    //         $(this).closest('.clone-file-page').remove();
    //     } else {
    //         alert("You must have at least one row.");
    //     }
    // });
    // Toggle Status Checkbox
    function toggleStatus() {
        $('.checkbox').on('change', function () {
            const hiddenInput = $(this).siblings('input[type="hidden"]');
            hiddenInput.val(this.checked ? '1' : '0');
        });
    }
    toggleStatus();

    // $('#add-row-page').on('click', function () {
    //     const newRow = $('.clone-file-page:first').clone();
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
    //     $('.file-block-page').append(newRow);
    //     newRow.find('.select-page').select2();
    // });
    // $(document).on('click', '.remove-row', function () {
    //     if ($('.file-block .clone-file').length > 1) {
    //         const row = $(this).closest('.clone-file');
    //         const textareaId = row.find('textarea').attr('id');
    //         if (editors[textareaId]) {
    //             editors[textareaId].destroy();
    //             delete editors[textareaId];
    //         }
    //         row.remove();
    //     } else {
    //         alert("You must have at least one row.");
    //     }
    // });
    // function toggleStatus() {
    //     var visibleCheckbox = $('#status');
    //     var hiddenCheckbox = $('input[name="status"][value="0"]');
    //
    //     if (visibleCheckbox.prop('checked')) {
    //         hiddenCheckbox.prop('checked', false);
    //     } else {
    //         hiddenCheckbox.prop('checked', true);
    //     }
    // }
</script>
