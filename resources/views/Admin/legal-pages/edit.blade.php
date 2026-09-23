<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Edit {{ $legalPage->title }} - Blog Dinamika</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .ql-toolbar.ql-snow {
            border: 1px solid #e5e7eb !important;
            border-radius: 8px 8px 0 0 !important;
            background: #f9fafb;
        }

        .ql-container.ql-snow {
            border: 1px solid #e5e7eb !important;
            border-top: none !important;
            border-radius: 0 0 8px 8px !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 17px !important;
        }

        .ql-editor {
            min-height: 520px;
            padding: 2rem !important;
            color: #1f2937;
            line-height: 1.7 !important;
        }

        .ql-editor p {
            margin-top: 0 !important;
            margin-bottom: 1rem !important;
        }

        .ql-editor h2 {
            font-size: 1.5rem !important;
            line-height: 1.3 !important;
            font-weight: 700 !important;
            color: #111827;
            margin-top: 2rem !important;
            margin-bottom: 1rem !important;
        }

        .ql-editor h3 {
            font-size: 1.25rem !important;
            line-height: 1.4 !important;
            font-weight: 700 !important;
            color: #111827;
            margin-top: 1.5rem !important;
            margin-bottom: 0.75rem !important;
        }

        .ql-editor strong,
        .ql-editor b {
            font-weight: 700;
            color: #111827;
        }

        .ql-editor em,
        .ql-editor i {
            font-style: italic;
        }

        .ql-editor u {
            text-decoration: underline;
            text-decoration-thickness: 1px;
            text-underline-offset: 2px;
        }

        .ql-editor s,
        .ql-editor strike {
            text-decoration: line-through;
        }

        .ql-editor a {
            color: #2563eb !important;
            text-decoration: underline !important;
            text-decoration-thickness: 1px;
            text-underline-offset: 2px;
            overflow-wrap: anywhere;
        }

        .ql-editor ol,
        .ql-editor ul {
            padding-left: 1.75rem !important;
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }

        .ql-editor ol {
            list-style-type: decimal !important;
        }

        .ql-editor ul {
            list-style-type: disc !important;
        }

        .ql-editor ol > li,
        .ql-editor ul > li {
            padding-left: 0.25rem !important;
            margin-bottom: 0.35rem !important;
        }

        .ql-editor ol > li[data-list="bullet"] {
            list-style-type: disc !important;
        }

        .ql-editor ol > li[data-list="ordered"] {
            list-style-type: decimal !important;
        }

        .ql-editor .ql-ui {
            display: none;
        }

        .ql-editor blockquote {
            margin: 1.5rem 0 !important;
            padding: 0.75rem 1.25rem !important;
            border-left: 4px solid #9ca3af !important;
            color: #4b5563 !important;
            font-style: italic;
            background-color: #f9fafb;
            border-radius: 0 0.375rem 0.375rem 0;
        }

        .ql-snow .ql-picker-label {
            color: #6b7280;
        }

        .ql-snow .ql-stroke {
            stroke: #6b7280;
        }

        .ql-snow .ql-fill {
            fill: #6b7280;
        }

        .ql-snow .ql-picker-options {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 640px) {
            .ql-toolbar.ql-snow {
                padding: 6px;
            }

            .ql-toolbar .ql-formats {
                margin-right: 6px;
            }

            .ql-editor {
                min-height: 420px;
                padding: 1rem !important;
                font-size: 16px !important;
            }
        }
    </style>
</head>

<body class="bg-white min-h-screen">

    <nav class="border-b border-gray-100">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between gap-4">

            <a
                href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-2 text-gray-500 hover:text-black transition-colors"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>

                <span class="text-sm">Back</span>
            </a>

            <div class="flex items-center gap-3">
                <span
                    id="saveStatus"
                    class="text-xs font-medium text-gray-500 px-2.5 py-1"
                >
                    ✓ All changes saved
                </span>

                <button
                    type="button"
                    id="saveButton"
                    onclick="saveLegalPage()"
                    class="bg-black text-white px-5 py-2.5 rounded-md text-sm font-medium hover:bg-gray-800 transition-colors"
                >
                    Save Changes
                </button>
            </div>

        </div>
    </nav>


    <main class="max-w-5xl mx-auto px-6 py-10">

        <div class="mb-8">

            <div class="flex items-center gap-3 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-cyan-600">
                    Legal Document
                </span>

                <span class="text-xs text-gray-400">
                    {{ $legalPage->type === 'privacy_policy' ? 'Privacy Policy' : 'Legal Notice' }}
                </span>
            </div>

            <input
                type="text"
                id="legal-title"
                value="{{ $legalPage->title }}"
                class="w-full text-3xl md:text-4xl font-bold text-black leading-tight border-none outline-none bg-transparent"
                placeholder="Document title..."
                oninput="markDirty()"
            >

            <div class="mt-3 text-sm text-gray-400">
                Last updated:
                {{ $legalPage->updated_at?->format('F d, Y') ?? 'Not published yet' }}
            </div>

        </div>


        <div class="bg-white">

            <div id="legal-editor"></div>

        </div>


        @if(session('success'))
            <div
                id="serverMessage"
                class="mt-4 px-4 py-3 rounded-md bg-green-50 border border-green-200 text-sm text-green-700"
            >
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div
                class="mt-4 px-4 py-3 rounded-md bg-red-50 border border-red-200 text-sm text-red-700"
            >
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </main>


    <script>
        let quill;
        let isInitializing = true;
        let isDirty = false;
        let isSaving = false;


        function getCsrfToken() {
            return document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content');
        }


        function markDirty() {
            if (!isInitializing) {
                isDirty = true;
                updateSaveStatus();
            }
        }


        function clearDirty() {
            isDirty = false;
            updateSaveStatus();
        }


        function updateSaveStatus() {
            const statusEl =
                document.getElementById('saveStatus');

            if (!statusEl) {
                return;
            }

            if (isSaving) {
                statusEl.textContent = 'Saving...';

                statusEl.className =
                    'text-xs font-medium text-blue-600 px-2.5 py-1';

                return;
            }

            if (isDirty) {
                statusEl.textContent = 'Unsaved changes';

                statusEl.className =
                    'text-xs font-medium text-yellow-600 px-2.5 py-1';
            } else {
                statusEl.textContent = '✓ All changes saved';

                statusEl.className =
                    'text-xs font-medium text-gray-500 px-2.5 py-1';
            }
        }


        function initEditor() {
            quill = new Quill(
                '#legal-editor',
                {
                    theme: 'snow',

                    modules: {
                        toolbar: [
                            [
                                {
                                    header: [2, 3, false]
                                }
                            ],

                            [
                                'bold',
                                'italic',
                                'underline',
                                'strike'
                            ],

                            [
                                'blockquote'
                            ],

                            [
                                {
                                    list: 'ordered'
                                },
                                {
                                    list: 'bullet'
                                }
                            ],

                            [
                                'link'
                            ],

                            [
                                'clean'
                            ]
                        ]
                    },

                    placeholder: 'Write legal content...'
                }
            );


            const existingContent =
                @json($legalPage->content);


            if (existingContent) {
                quill.clipboard.dangerouslyPasteHTML(
                    existingContent,
                    'silent'
                );
            }


            quill.on(
                'text-change',
                function(delta, oldDelta, source) {
                    if (source === 'user') {
                        markDirty();
                    }
                }
            );


            isInitializing = false;
            clearDirty();
        }


        async function saveLegalPage() {
            if (isSaving) {
                return;
            }

            const titleEl =
                document.getElementById('legal-title');

            const title =
                titleEl.value.trim();

            const content =
                quill.root.innerHTML;


            if (!title) {
                alert('Title is required.');
                titleEl.focus();
                return;
            }


            if (!content || content === '<p><br></p>') {
                alert('Content cannot be empty.');
                quill.focus();
                return;
            }


            try {
                isSaving = true;
                updateSaveStatus();

                const response =
                    await fetch(
                        '{{ route('admin.legal-pages.update', $legalPage->type) }}',
                        {
                            method: 'PUT',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    getCsrfToken(),

                                'Accept':
                                    'application/json'
                            },

                            body: JSON.stringify({
                                title: title,
                                content: content
                            })
                        }
                    );


                const data =
                    await response.json();


                if (!response.ok) {
                    throw new Error(
                        data.message ||
                        'Failed to save legal page.'
                    );
                }


                clearDirty();

                alert('Legal page saved successfully.');

                window.location.reload();

            } catch (error) {
                console.error(
                    'Legal page save error:',
                    error
                );

                alert(
                    error.message ||
                    'Failed to save legal page.'
                );
            } finally {
                isSaving = false;
                updateSaveStatus();
            }
        }


        document.addEventListener(
            'DOMContentLoaded',
            function() {

                initEditor();


                window.addEventListener(
                    'beforeunload',
                    function(e) {

                        if (isDirty) {
                            e.preventDefault();
                            e.returnValue = '';
                        }

                    }
                );


                const backLink =
                    document.querySelector(
                        'nav a'
                    );


                if (backLink) {
                    backLink.addEventListener(
                        'click',
                        function(e) {

                            if (!isDirty) {
                                return;
                            }

                            const leave =
                                confirm(
                                    'You have unsaved changes. Leave this page?'
                                );

                            if (!leave) {
                                e.preventDefault();
                            }

                        }
                    );
                }

            }
        );
    </script>

</body>
</html>