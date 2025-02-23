<script>
    $('#add-row-member').on('click', function () {
        const newRow = $('.member-clone-file:first').clone();
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
        $('.member-file-block').append(newRow);
    });
    $(document).on('click', '.remove-row', function () {
        if ($('.member-file-block .member-clone-file').length > 1) {
            let row = $(this).closest('.member-clone-file');
            let id = row.find("input[name^='id']").val();
            if (id) {
                if (confirm("Are you sure you want to delete this member?")) {
                    $.ajax({
                        url: `/delete/${id}`, // Route to delete
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
                            alert("Error deleting member: " + xhr.responseText);
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
    function toggleStatus() {
        $('.checkbox').on('change', function () {
            const hiddenInput = $(this).siblings('input[type="hidden"]');
            hiddenInput.val(this.checked ? '1' : '0');
        });
    }
    toggleStatus();
</script>
