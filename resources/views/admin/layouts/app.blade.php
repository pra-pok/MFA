<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-menu-fixed layout-compact"
    dir="ltr" data-theme="theme-default" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/myfree-favicon.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.1.0/ckeditor5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.0/css/dataTables.bootstrap5.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    @yield('css')
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ route('dashboard') }}" class="app-brand-link">
                        <img src="{{ asset('assets/img/mfa-logo.png') }}" width="100%">
                    </a>
                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
                    </a>
                </div>
                <div class="menu-inner-shadow"></div>
                @include('admin.includes.sidebar')
            </aside>
            <div class="layout-page">
                @include('admin.includes.navbar')
                <div class="content-wrapper">
                    @yield('content')
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-fluid">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <?php
                                $version = getenv('Version') ?: 'Unknown Version';
                                ?>
                                <div class="text-body">
                                    <span><?php echo $version; ?></span> ,made with ❤️ by
                                    ©
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>

                                    <a href="https://myfreeadmission.com" target="_blank" class="footer-link">My Free
                                        Admission</a>
                                </div>
                                <div class="d-none d-lg-inline-block">
                                    <a href="https://myfreeadmission.com" class="footer-link me-4"
                                        target="_blank">Privacy Policy</a>
                                </div>
                            </div>
                        </div>
                    </footer>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdn.datatables.net/2.2.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.0/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('assets/js/helper.js') }}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        let editorCounter = 0;
        const editorClass = 'editor';
        const editors = {};

        function initializeEditor(element) {
            const uniqueId = `editor_${editorCounter++}`;
            element.setAttribute('id', uniqueId);

            CKEDITOR.ClassicEditor.create(element, {
                    toolbar: {
                        items: [
                            'exportPDF', 'exportWord', '|',
                            'findAndReplace', 'selectAll', '|',
                            'heading', '|',
                            'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript',
                            'removeFormat', '|',
                            'bulletedList', 'numberedList', 'todoList', '|',
                            'outdent', 'indent', '|',
                            'undo', 'redo',
                            '-',
                            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                            'alignment', '|',
                            'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock',
                            'htmlEmbed', '|',
                            'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                            'textPartLanguage', '|',
                            'sourceEditing'
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    placeholder: ' ',
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
                    editors[uniqueId] = editor;
                })
                .catch(error => {
                    console.error(`Error initializing editor for ${uniqueId}:`, error);
                });
        }

        document.querySelectorAll(`.${editorClass}`).forEach((element) => {
            initializeEditor(element);
        });
    </script>
    {{-- <script> --}}
    {{--    const editorClass = 'editor'; --}}
    {{--    const editors = {}; --}}
    {{--    document.querySelectorAll(`.${editorClass}`).forEach((element, index) => { --}}
    {{--        const uniqueId = element.id || `editor_${index}`; --}}
    {{--        element.setAttribute('id', uniqueId); --}}
    {{--        CKEDITOR.ClassicEditor.create(element, { --}}
    {{--            toolbar: { --}}
    {{--                items: [ --}}
    {{--                    'exportPDF', 'exportWord', '|', --}}
    {{--                    'findAndReplace', 'selectAll', '|', --}}
    {{--                    'heading', '|', --}}
    {{--                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|', --}}
    {{--                    'bulletedList', 'numberedList', 'todoList', '|', --}}
    {{--                    'outdent', 'indent', '|', --}}
    {{--                    'undo', 'redo', --}}
    {{--                    '-', --}}
    {{--                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|', --}}
    {{--                    'alignment', '|', --}}
    {{--                    'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|', --}}
    {{--                    'specialCharacters', 'horizontalLine', 'pageBreak', '|', --}}
    {{--                    'textPartLanguage', '|', --}}
    {{--                    'sourceEditing' --}}
    {{--                ], --}}
    {{--                shouldNotGroupWhenFull: true --}}
    {{--            }, --}}
    {{--            list: { --}}
    {{--                properties: { --}}
    {{--                    styles: true, --}}
    {{--                    startIndex: true, --}}
    {{--                    reversed: true --}}
    {{--                } --}}
    {{--            }, --}}
    {{--            heading: { --}}
    {{--                options: [ --}}
    {{--                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' }, --}}
    {{--                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' }, --}}
    {{--                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }, --}}
    {{--                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }, --}}
    {{--                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }, --}}
    {{--                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' }, --}}
    {{--                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' } --}}
    {{--                ] --}}
    {{--            }, --}}
    {{--            placeholder: ' ', --}}
    {{--            fontFamily: { --}}
    {{--                options: [ --}}
    {{--                    'default', --}}
    {{--                    'Arial, Helvetica, sans-serif', --}}
    {{--                    'Courier New, Courier, monospace', --}}
    {{--                    'Georgia, serif', --}}
    {{--                    'Lucida Sans Unicode, Lucida Grande, sans-serif', --}}
    {{--                    'Tahoma, Geneva, sans-serif', --}}
    {{--                    'Times New Roman, Times, serif', --}}
    {{--                    'Trebuchet MS, Helvetica, sans-serif', --}}
    {{--                    'Verdana, Geneva, sans-serif' --}}
    {{--                ], --}}
    {{--                supportAllValues: true --}}
    {{--            }, --}}
    {{--            fontSize: { --}}
    {{--                options: [10, 12, 14, 'default', 18, 20, 22], --}}
    {{--                supportAllValues: true --}}
    {{--            }, --}}
    {{--            htmlSupport: { --}}
    {{--                allow: [ --}}
    {{--                    { --}}
    {{--                        name: /.*/, --}}
    {{--                        attributes: true, --}}
    {{--                        classes: true, --}}
    {{--                        styles: true --}}
    {{--                    } --}}
    {{--                ] --}}
    {{--            }, --}}
    {{--            htmlEmbed: { --}}
    {{--                showPreviews: true --}}
    {{--            }, --}}
    {{--            link: { --}}
    {{--                decorators: { --}}
    {{--                    addTargetToExternalLinks: true, --}}
    {{--                    defaultProtocol: 'https://', --}}
    {{--                    toggleDownloadable: { --}}
    {{--                        mode: 'manual', --}}
    {{--                        label: 'Downloadable', --}}
    {{--                        attributes: { --}}
    {{--                            download: 'file' --}}
    {{--                        } --}}
    {{--                    } --}}
    {{--                } --}}
    {{--            }, --}}
    {{--            mention: { --}}
    {{--                feeds: [ --}}
    {{--                    { --}}
    {{--                        marker: '@', --}}
    {{--                        feed: [ --}}
    {{--                            '@apple', '@bears', '@brownie', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream', --}}
    {{--                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o', --}}
    {{--                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé', --}}
    {{--                            '@sugar', '@sweet', '@topping', '@wafer' --}}
    {{--                        ], --}}
    {{--                        minimumCharacters: 1 --}}
    {{--                    } --}}
    {{--                ] --}}
    {{--            }, --}}
    {{--            removePlugins: [ --}}
    {{--                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'MultiLevelList', --}}
    {{--                'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges', --}}
    {{--                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges', --}}
    {{--                'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType', --}}
    {{--                'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents', --}}
    {{--                'PasteFromOfficeEnhanced', 'CaseChange' --}}
    {{--            ] --}}
    {{--        }) --}}
    {{--            .then(editor => { --}}
    {{--                editors[uniqueId] = editor; --}}
    {{--            }) --}}
    {{--            .catch(error => { --}}
    {{--                console.error(`Error initializing editor for element with id or index ${uniqueId}:`, error); --}}
    {{--            }); --}}
    {{--    }); --}}
    {{--    function getEditorData(index) { --}}
    {{--        if (editors[index]) { --}}
    {{--            return editors[index].getData(); --}}
    {{--        } else { --}}
    {{--            console.error(`Editor instance for ${index} not found.`); --}}
    {{--            return null; --}}
    {{--        } --}}
    {{--    } --}}
    {{-- </script> --}}
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    @yield('js')
</body>

</html>
