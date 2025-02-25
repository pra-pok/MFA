<script>
    function membergenerateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = (Math.random() * 16) | 0,
                v = c === 'x' ? r : (r & 0x3) | 0x8;
            return v.toString(16);
        });
    }
    $('#add-row-member').on('click', function () {
        const newRow = $('.member-clone-file:first').clone();
       // const index = $('.member-file-block .member-clone-file').length;
        const index = $('.member-clone-file').length;
        let newIndex = membergenerateUUID();
        let newHeading = newRow.find('.accordion-header');
        let newButton = newRow.find('.accordion-button');
        let newCollapse = newRow.find('.accordion-collapse');
        newHeading.attr('id', `member-heading-${newIndex}`);
        newButton.attr('data-bs-target', `#member-collapse-${newIndex}`)
            .attr('aria-controls', `member-collapse-${newIndex}`)
            .removeClass("collapsed")
            .attr("aria-expanded", "true");
        newCollapse.attr('id', `member-collapse-${newIndex}`)
            .attr('aria-labelledby', `member-heading-${newIndex}`)
            .addClass("show")
            .removeAttr('data-bs-parent');
        newButton.text("New Member");
        newRow.find('select[name^="organization_group_id"]').on("change", function () {
            let selectedText = $(this).find("option:selected").text();
            newButton.text(selectedText || "New Member");
        });
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
        newRow.find('select[name^="organization_group_id"]').attr('name', `organization_group_id[${index}]`);
        newRow.find('input[name^="id"]').remove();
        newRow.find('input[name^="name"]').attr('name', `name[${index}]`);
        newRow.find('input[name^="rank"]').attr('name', `rank[${index}]`);
        newRow.find('input[name^="designation"]').attr('name', `designation[${index}]`);
        newRow.find('input[name^="photo_file"]').attr('name', `photo_file[${index}]`);
        newRow.find('textarea[name^="bio"]').attr('name', `bio[${index}]`);
        newRow.find('input[name^="status"]').attr('name', `status[${index}]`);
        newRow.find('input[type="file"]').val('');
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
