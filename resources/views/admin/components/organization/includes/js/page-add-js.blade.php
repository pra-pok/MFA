<script>
    // $('#add-row-page').on('click', function () {
    //     const newRow = $('.clone-file-page:first').clone();
    //     newRow.find('select').prop('selectedIndex', 0);
    //     // newRow.find(".ck-editor").remove();
    //     newRow.find('textarea').val('');
    //     $('.file-block-page').append(newRow);
    //     newRow.find('.select-page').select2();
    // });
    //
    // $(document).on('click', '.remove-row-page', function () {
    //     if ($('.file-block-page .clone-file-page').length > 1) {
    //         const row = $(this).closest('.clone-file-page');
    //         row.remove();
    //     } else {
    //         alert("You must have at least one row.");
    //     }
    // });

    $('#add-row-page').on('click', function () {
        const newRow = $('.clone-file-page:first').clone();
        newRow.find('select').prop('selectedIndex', 0); // Reset the select input
        newRow.find('textarea').val(''); // Clear the textarea content
        $('.file-block-page').append(newRow);

        // Initialize CKEditor for the newly added textarea
        newRow.find('.editor').each(function () {
            CKEDITOR.replace(this); // Replace each textarea with CKEditor
        });

        newRow.find('.select-page').select2(); // Re-initialize select2 (if needed)
    });
    $(document).on('click', '.remove-row-page', function () {
        if ($('.file-block-page .clone-file-page').length > 1) {
            const row = $(this).closest('.clone-file-page');
            row.remove();
        } else {
            alert("You must have at least one row.");
        }
    });

</script>
