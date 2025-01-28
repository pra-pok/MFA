<script>
    $('#add-row').on('click', function () {
        const newRow = $('.clone-file:first').clone();
        newRow.find('select').prop('selectedIndex', 0);
        newRow.find('textarea').val('');
        $('.file-block').append(newRow);
        newRow.find('.select-course').select2();
    });
    $(document).on('click', '.remove-row', function () {
        if ($('.file-block .clone-file').length > 1) {
            const row = $(this).closest('.clone-file');
            // const textareaId = row.find('textarea').attr('id');
            // if (editors[textareaId]) {
            //     editors[textareaId].destroy();
            //     delete editors[textareaId];
            // }
            row.remove();
        } else {
            alert("You must have at least one row.");
        }
    });

</script>
