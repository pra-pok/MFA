const defaultLoaderConfig = {includeDots: true, includeLoader: true};
const PLEASE_WAIT = "Please wait";

$(() => {
    setMandatoryStar();
    bindFormValidator();
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

function bindFormValidator() {
    $("form").each(function () {
        const formInstance = $(this);
        formInstance.validate();
        if (!formInstance.hasClass("ajax")) handleValidation(formInstance);
    });
}

function handleValidation(formInstance) {
    $("button:not(.btn-close)", formInstance).click((e) => {
        e.preventDefault();
        if (isFormValid(formInstance)) {
            showConfirmDialog("Confirm Submit", "Are you sure you want to submit this form ?", generateCallback(submitFormNow, [e.target, true]));
        }
    });
}

function isFormValid(instance) {
    return $(instance).valid();
}

function showButtonLoader(instance = "#btnFilter", text, config = defaultLoaderConfig) {
    $(instance).attr("data-loader", $(instance).html());
    let data = `${config.includeLoader ? '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' : ''}
                ${text ? ` <span class="loader-text">` + text + (config.includeDots ? "..." : "") + "</span>" : ""}`;
    $(instance).html(data).prop("disabled", true);
}

function hideButtonLoader(instance = "#btnFilter") {
    $(instance).html($(instance).attr("data-loader"));
    $(instance).find(".waves-ripple").remove();
    $(instance).prop("disabled", false);
}
    function validateAndSubmit(instance) {
    /* const form = $(instance).closest("form");
     if (!form.valid()) return;

     form.submit();*/
  }
