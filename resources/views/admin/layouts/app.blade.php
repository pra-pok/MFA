<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-menu-fixed layout-compact"
      dir="ltr" data-theme="theme-default" data-template="vertical-menu-template-free" data-style="light">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
          class="template-customizer-theme-css" />
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
                        <span class="app-brand-logo demo">
                            <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                 xmlns:xlink="http://www.w3.org/1999/xlink">
                                <defs>
                                    <path
                                        d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                                        id="path-1"></path>
                                    <path
                                        d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                                        id="path-3"></path>
                                    <path
                                        d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                                        id="path-4"></path>
                                    <path
                                        d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                                        id="path-5"></path>
                                </defs>
                                <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                                        <g id="Icon" transform="translate(27.000000, 15.000000)">
                                            <g id="Mask" transform="translate(0.000000, 8.000000)">
                                                <mask id="mask-2" fill="white">
                                                    <use xlink:href="#path-1"></use>
                                                </mask>
                                                <use fill="#696cff" xlink:href="#path-1"></use>
                                                <g id="Path-3" mask="url(#mask-2)">
                                                    <use fill="#696cff" xlink:href="#path-3"></use>
                                                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                                                </g>
                                                <g id="Path-4" mask="url(#mask-2)">
                                                    <use fill="#696cff" xlink:href="#path-4"></use>
                                                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                                                </g>
                                            </g>
                                            <g id="Triangle"
                                               transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                                                <use fill="#696cff" xlink:href="#path-5"></use>
                                                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </span>
                    <span class="app-brand-text demo menu-text fw-bold ms-2">mfa</span>
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
                    <div class="container-xxl">
                        <div
                            class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                            <div class="text-body">
                                ©
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>
                                , made with ❤️ by
                                <a href="https://myfreeadmission.com" target="_blank" class="footer-link">My Free Admission</a>
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
{{--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>--}}
<script src="https://cdn.datatables.net/2.2.0/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.0/js/dataTables.bootstrap5.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{asset('assets/js/helper.js')}}"></script>
{{--<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>--}}
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
            placeholder: 'Enter description...',
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
{{--<script>--}}
{{--    const editorClass = 'editor';--}}
{{--    const editors = {};--}}
{{--    document.querySelectorAll(`.${editorClass}`).forEach((element, index) => {--}}
{{--        const uniqueId = element.id || `editor_${index}`;--}}
{{--        element.setAttribute('id', uniqueId);--}}
{{--        CKEDITOR.ClassicEditor.create(element, {--}}
{{--            toolbar: {--}}
{{--                items: [--}}
{{--                    'exportPDF', 'exportWord', '|',--}}
{{--                    'findAndReplace', 'selectAll', '|',--}}
{{--                    'heading', '|',--}}
{{--                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',--}}
{{--                    'bulletedList', 'numberedList', 'todoList', '|',--}}
{{--                    'outdent', 'indent', '|',--}}
{{--                    'undo', 'redo',--}}
{{--                    '-',--}}
{{--                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',--}}
{{--                    'alignment', '|',--}}
{{--                    'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',--}}
{{--                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',--}}
{{--                    'textPartLanguage', '|',--}}
{{--                    'sourceEditing'--}}
{{--                ],--}}
{{--                shouldNotGroupWhenFull: true--}}
{{--            },--}}
{{--            list: {--}}
{{--                properties: {--}}
{{--                    styles: true,--}}
{{--                    startIndex: true,--}}
{{--                    reversed: true--}}
{{--                }--}}
{{--            },--}}
{{--            heading: {--}}
{{--                options: [--}}
{{--                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },--}}
{{--                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },--}}
{{--                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },--}}
{{--                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },--}}
{{--                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },--}}
{{--                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },--}}
{{--                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }--}}
{{--                ]--}}
{{--            },--}}
{{--            placeholder: ' ',--}}
{{--            fontFamily: {--}}
{{--                options: [--}}
{{--                    'default',--}}
{{--                    'Arial, Helvetica, sans-serif',--}}
{{--                    'Courier New, Courier, monospace',--}}
{{--                    'Georgia, serif',--}}
{{--                    'Lucida Sans Unicode, Lucida Grande, sans-serif',--}}
{{--                    'Tahoma, Geneva, sans-serif',--}}
{{--                    'Times New Roman, Times, serif',--}}
{{--                    'Trebuchet MS, Helvetica, sans-serif',--}}
{{--                    'Verdana, Geneva, sans-serif'--}}
{{--                ],--}}
{{--                supportAllValues: true--}}
{{--            },--}}
{{--            fontSize: {--}}
{{--                options: [10, 12, 14, 'default', 18, 20, 22],--}}
{{--                supportAllValues: true--}}
{{--            },--}}
{{--            htmlSupport: {--}}
{{--                allow: [--}}
{{--                    {--}}
{{--                        name: /.*/,--}}
{{--                        attributes: true,--}}
{{--                        classes: true,--}}
{{--                        styles: true--}}
{{--                    }--}}
{{--                ]--}}
{{--            },--}}
{{--            htmlEmbed: {--}}
{{--                showPreviews: true--}}
{{--            },--}}
{{--            link: {--}}
{{--                decorators: {--}}
{{--                    addTargetToExternalLinks: true,--}}
{{--                    defaultProtocol: 'https://',--}}
{{--                    toggleDownloadable: {--}}
{{--                        mode: 'manual',--}}
{{--                        label: 'Downloadable',--}}
{{--                        attributes: {--}}
{{--                            download: 'file'--}}
{{--                        }--}}
{{--                    }--}}
{{--                }--}}
{{--            },--}}
{{--            mention: {--}}
{{--                feeds: [--}}
{{--                    {--}}
{{--                        marker: '@',--}}
{{--                        feed: [--}}
{{--                            '@apple', '@bears', '@brownie', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',--}}
{{--                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',--}}
{{--                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',--}}
{{--                            '@sugar', '@sweet', '@topping', '@wafer'--}}
{{--                        ],--}}
{{--                        minimumCharacters: 1--}}
{{--                    }--}}
{{--                ]--}}
{{--            },--}}
{{--            removePlugins: [--}}
{{--                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'MultiLevelList',--}}
{{--                'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',--}}
{{--                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',--}}
{{--                'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',--}}
{{--                'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents',--}}
{{--                'PasteFromOfficeEnhanced', 'CaseChange'--}}
{{--            ]--}}
{{--        })--}}
{{--            .then(editor => {--}}
{{--                editors[uniqueId] = editor;--}}
{{--            })--}}
{{--            .catch(error => {--}}
{{--                console.error(`Error initializing editor for element with id or index ${uniqueId}:`, error);--}}
{{--            });--}}
{{--    });--}}
{{--    function getEditorData(index) {--}}
{{--        if (editors[index]) {--}}
{{--            return editors[index].getData();--}}
{{--        } else {--}}
{{--            console.error(`Editor instance for ${index} not found.`);--}}
{{--            return null;--}}
{{--        }--}}
{{--    }--}}
{{--</script>--}}
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
