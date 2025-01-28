$(() => {
    setMandatoryStar();
});
// function setMandatoryStar() {
//    // console.log("hello world!")
//     $("input[required],select[required],textarea[required]").each(function () {
//         $(this).parent().find("label").append("<span class='mandatory text-danger'>*</span>");
//     });
// }

function setMandatoryStar() {
    // Target elements with the "required" class instead of the "required" attribute
    $("input.required, select.required, textarea.required").each(function () {
        $(this).parent().find("label").append("<span class='mandatory text-danger'>*</span>");
    });
}



function initializeCKEditor(uniqueId){
    const newTextarea = document.getElementById(uniqueId);
    if (newTextarea) {
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
                editors[uniqueId] = editor; // Store the editor instance
            })
            .catch(error => {
                console.error(`Error initializing CKEditor for #${uniqueId}:`, error);
            });
    } else {
        console.error(`Textarea with id ${uniqueId} not found.`);
    }
}
