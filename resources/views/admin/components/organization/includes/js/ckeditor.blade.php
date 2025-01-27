<script>
    $('#add-row').on('click', function () {
        const uniqueId = `ckeditor-${Date.now()}-${Math.floor(Math.random() * 1000)}`;
       // let total = $('.file-block textarea').length
       //  console.log(total);
        const newRow = $('.clone-file').clone();
        newRow.find('select').prop('selectedIndex', 0);
        newRow.find(".ck-editor").remove();
        newRow.find('textarea').val('');
        const newTextareaId = `ckeditor${uniqueId}`;
        newRow.find('textarea').attr('id', newTextareaId);
        $('.file-block').append(newRow);

        setTimeout(function () {
            const newTextarea = document.getElementById(newTextareaId);
            if (newTextarea) {
                console.log(`Initializing CKEditor for textarea with id: ${newTextareaId}`);
                CKEDITOR.ClassicEditor.create(newTextarea, {
                    readOnly: false,
                    toolbar: {
                        items: [
                            'exportPDF', 'exportWord', '|',
                            'findAndReplace', 'selectAll', '|',
                            'heading', '|',
                            'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                            'bulletedList', 'numberedList', 'todoList', '|',
                            'outdent', 'indent', '|',
                            'undo', 'redo',
                            '-',
                            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                            'alignment', '|',
                            'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                            'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                            'textPartLanguage', '|',
                            'sourceEditing'
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    placeholder: ' ',
                    fontFamily: {
                        options: [
                            'default',
                            'Arial, Helvetica, sans-serif',
                            'Courier New, Courier, monospace',
                            'Georgia, serif',
                            'Lucida Sans Unicode, Lucida Grande, sans-serif',
                            'Tahoma, Geneva, sans-serif',
                            'Times New Roman, Times, serif',
                            'Trebuchet MS, Helvetica, sans-serif',
                            'Verdana, Geneva, sans-serif'
                        ],
                        supportAllValues: true
                    },
                    fontSize: {
                        options: [10, 12, 14, 'default', 18, 20, 22],
                        supportAllValues: true
                    },
                    htmlSupport: {
                        allow: [
                            {
                                name: /.*/,
                                attributes: true,
                                classes: true,
                                styles: true
                            }
                        ]
                    },
                    htmlEmbed: {
                        showPreviews: true
                    },
                    removePlugins: [
                        'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'MultiLevelList',
                        'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                        'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                        'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',
                        'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents',
                        'PasteFromOfficeEnhanced', 'CaseChange'
                    ]
                })
                    .then(editor => {

                        editors[newTextareaId] = editor;
                    })
                    .catch(error => {
                        console.error(`Error initializing CKEditor for #${newTextareaId}:`, error);
                    });
            } else {
                console.error(`Textarea with id ${newTextareaId} not found.`);
            }
        }, 100);
        newRow.find('.select-course').select2();
    });

    $(document).on('click', '.remove-row', function () {
        if ($('.file-block .form-row').length > 1) {
            $(this).closest('.form-row').remove();
        } else {
            alert("You must have at least one row.");
        }
    });

</script>
