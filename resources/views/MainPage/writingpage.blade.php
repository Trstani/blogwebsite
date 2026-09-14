<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Write Article - Blog Dinamika</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        /* ============================================
           EDITOR / ARTICLE PREVIEW
        ============================================ */

        /*
         * Keep the writing canvas aligned with the Reading Page.
         * The editor controls remain visible, but the actual article
         * content uses the same width, typography and media ratios.
         */
        .section-block {
            position: relative;
            margin-bottom: 2rem;
            padding: 0;
            border: 1px solid transparent;
            border-radius: 0.5rem;
            transition: border-color 0.2s ease;
        }

        .section-block:hover {
            border-color: #e5e7eb;
        }

        .section-block:hover .section-menu {
            opacity: 1;
        }

        .section-menu {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        /* ============================================
           QUILL / RICH ARTICLE TYPOGRAPHY
        ============================================ */

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
            font-size: 18px !important;
            min-height: 150px;
        }

        /*
         * These values intentionally mirror .rich-article-content
         * on the Reading Page.
         */
        .ql-editor {
            min-height: 150px;
            padding: 0.75rem 0.25rem !important;
            color: #1f2937;
            line-height: 1.5 !important;
        }

        .ql-editor p {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .ql-editor h2 {
            font-size: 1.75rem !important;
            line-height: 1.3 !important;
            font-weight: 700 !important;
            color: #111827;
            margin-top: 2rem !important;
            margin-bottom: 1rem !important;
        }

        .ql-editor h3 {
            font-size: 1.375rem !important;
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

        .ql-editor a:hover {
            color: #1d4ed8 !important;
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

        /* Quill 2 bullet representation */
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

        .ql-editor .ql-syntax {
            display: block;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
            background: #111827;
            color: #f9fafb;
            border-radius: 0.5rem;
            font-family: monospace;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .ql-editor.ql-blank::before {
            color: #9ca3af !important;
            font-style: normal !important;
            left: 0.25rem !important;
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

        /* ============================================
           MEDIA PREVIEW — MATCH READING PAGE
        ============================================ */

        .article-media-image {
            position: relative;
            width: 100%;
            aspect-ratio: 2 / 1;
            overflow: hidden;
            border-radius: 0.5rem;
        }

        .article-media-image img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
        }

        .article-media-gif {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 0.5rem;
        }

        .article-media-gif img {
            width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .article-media-video {
            width: 100%;
            overflow: hidden;
            border-radius: 0.5rem;
            background: #000;
        }

        .article-media-video iframe,
        .article-media-video video {
            display: block;
            width: 100%;
            max-width: 100%;
        }

        /* Mobile toolbar */
        @media (max-width: 640px) {
            .ql-toolbar.ql-snow {
                padding: 6px;
            }

            .ql-toolbar .ql-formats {
                margin-right: 6px;
            }

            .ql-editor {
                padding: 0.75rem 0 !important;
                font-size: 16px !important;
            }

            .ql-editor h2 {
                font-size: 1.5rem !important;
            }

            .ql-editor h3 {
                font-size: 1.25rem !important;
            }
        }

        /* ============================================
           IMAGE / GIF
        ============================================ */

        .upload-placeholder:hover {
            border-color: #9ca3af;
            background: #f3f4f6;
        }

        /* ============================================
           TEXTAREA
        ============================================ */

        textarea {
            overflow: hidden;
        }

        #article-title {
            min-height: 48px;
        }

        #article-description {
            min-height: 42px;
        }

        /* ============================================
           MODALS
        ============================================ */

        .modal-backdrop {
            backdrop-filter: blur(2px);
        }
    </style>
</head>

<body class="bg-white min-h-screen font-sans">

    {{-- =========================================================
         TOP NAVBAR
    ========================================================== --}}
    <nav class="border-b border-gray-100">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between gap-4">

            {{-- Dashboard --}}
            <a
                href="/writer/dashboard"
                class="flex items-center space-x-2 text-gray-500 hover:text-black transition-colors"
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

                <span class="text-sm">Dashboard</span>
            </a>

            {{-- Status --}}
            <div class="flex items-center gap-2">

                @if(isset($article) && $article)

                    @if($article->status === 'published')

                        <span class="text-xs font-medium text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                            Published
                        </span>

                    @elseif($article->status === 'pending')

                        <span class="text-xs font-medium text-yellow-600 bg-yellow-50 px-2.5 py-1 rounded-full">
                            Pending Review
                        </span>

                    @elseif($article->status === 'rejected')

                        <span class="text-xs font-medium text-red-600 bg-red-50 px-2.5 py-1 rounded-full">
                            Rejected
                        </span>

                    @else

                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                            Draft
                        </span>

                    @endif

                @endif

                <span
                    id="saveStatus"
                    class="text-xs font-medium text-gray-500 px-2.5 py-1"
                >
                    All changes saved
                </span>

            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">

                @if(isset($article))

                    {{-- DRAFT / REJECTED --}}
                    @if($article->status === 'draft' || $article->status === 'rejected')

                        <button
                            onclick="saveDraft()"
                            class="bg-transparent text-gray-500 px-6 py-2.5 border border-gray-200 rounded-md text-sm font-medium hover:border-black hover:text-black transition-colors"
                        >
                            Save Draft
                        </button>

                        <button
                            onclick="openSubmitModal()"
                            class="bg-black text-white px-6 py-2.5 rounded-md text-sm font-medium hover:bg-gray-800 transition-colors"
                        >
                            Submit for Review
                        </button>

                    {{-- PENDING --}}
                    @elseif($article->status === 'pending')

                        <button
                            onclick="saveDraft()"
                            class="bg-transparent text-gray-500 px-6 py-2.5 border border-gray-200 rounded-md text-sm font-medium hover:border-black hover:text-black transition-colors"
                        >
                            Save Draft
                        </button>

                    {{-- PUBLISHED --}}
                    @elseif($article->status === 'published')

                        <button
                            onclick="saveDraft()"
                            class="bg-blue-600 text-white px-6 py-2.5 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors"
                        >
                            Save
                        </button>

                    @endif

                @else

                    {{-- NEW ARTICLE --}}
                    <button
                        onclick="saveDraft()"
                        class="bg-transparent text-gray-500 px-6 py-2.5 border border-gray-200 rounded-md text-sm font-medium hover:border-black hover:text-black transition-colors"
                    >
                        Save Draft
                    </button>

                    <button
                        onclick="openSubmitModal()"
                        class="bg-black text-white px-6 py-2.5 rounded-md text-sm font-medium hover:bg-gray-800 transition-colors"
                    >
                        Submit for Review
                    </button>

                @endif

            </div>

        </div>
    </nav>


    {{-- =========================================================
         EDITOR AREA
    ========================================================== --}}
    <main class="max-w-5xl mx-auto px-6 py-10">

        {{-- =====================================================
             ARTICLE META
        ====================================================== --}}
        <div class="mb-8">

            {{-- Category --}}
            <p
                id="article-category"
                class="inline-block px-3 py-1 rounded-md text-xs font-medium bg-gray-300 text-gray-700 mb-3"
            ></p>

            {{-- Title --}}
            <textarea
                id="article-title"
                class="w-full text-3xl md:text-4xl font-bold text-black leading-tight border-none outline-none resize-none bg-transparent"
                placeholder="Article title..."
                rows="1"
                oninput="autoResize(this); markDirty();"
            ></textarea>

            {{-- Description --}}
            <textarea
                id="article-description"
                class="w-full text-lg text-gray-500 mt-4 border-none outline-none resize-none bg-transparent leading-relaxed"
                placeholder="Write a short description..."
                rows="1"
                oninput="autoResize(this); markDirty();"
            ></textarea>

        </div>


        {{-- =====================================================
             ADMIN REVIEW NOTES
        ====================================================== --}}
        @if(isset($article) && $article && $article->status === 'rejected' && $article->admin_notes)

            <div class="mt-4 mb-8 p-4 bg-red-50 border border-red-200 rounded-lg">

                <div class="flex items-center gap-2 mb-2">

                    <svg
                        class="w-4 h-4 text-red-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333-.578 2.5 1.732 3z"
                        />
                    </svg>

                    <span class="text-sm font-semibold text-red-700">
                        Admin Review
                    </span>

                </div>

                <p class="text-sm text-red-600 leading-relaxed">
                    {{ $article->admin_notes }}
                </p>

            </div>

        @endif


        {{-- =====================================================
             SECTIONS
        ====================================================== --}}
        <div
            id="sections"
            class="space-y-2"
        >

            {{-- Default first text section --}}
            <div
                class="section-block"
                data-type="text"
            >

                <div class="absolute right-2 top-2 section-menu z-10">

                    <button
                        onclick="removeSection(this)"
                        class="text-gray-300 hover:text-red-500 p-1 bg-white rounded"
                        title="Remove section"
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
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>

                </div>

                <div
                    class="quill-editor"
                    data-placeholder="Start writing..."
                ></div>

                <div class="mt-2 text-right">

                    <span class="text-xs text-gray-400 char-count">
                        0 / 10,000 characters
                    </span>

                </div>

            </div>

        </div>


        {{-- =====================================================
             ADD SECTION TOOLBAR
        ====================================================== --}}
        <div class="mt-6 flex items-center justify-between">

            <div class="flex gap-2 flex-wrap">

                {{-- TEXT --}}
                <button
                    onclick="addSection('text')"
                    id="addTextBtn"
                    class="px-2.5 py-1.5 border border-gray-200 rounded-md text-xs text-gray-500 bg-white hover:border-black hover:text-black transition-colors inline-flex items-center gap-1"
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
                            d="M4 6h16M4 12h16M4 18h7"
                        />
                    </svg>

                    Text
                </button>


                {{-- IMAGE --}}
                <button
                    onclick="addSection('image')"
                    id="addImageBtn"
                    class="px-2.5 py-1.5 border border-gray-200 rounded-md text-xs text-gray-500 bg-white hover:border-black hover:text-black transition-colors inline-flex items-center gap-1"
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
                            stroke-width="1.5"
                            d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"
                        />
                    </svg>

                    Image
                </button>


                {{-- VIDEO --}}
                <button
                    onclick="addSection('video')"
                    id="addVideoBtn"
                    class="px-2.5 py-1.5 border border-gray-200 rounded-md text-xs text-gray-500 bg-white hover:border-black hover:text-black transition-colors inline-flex items-center gap-1"
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
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                    Video
                </button>


                {{-- GIF --}}
                <button
                    onclick="addSection('gif')"
                    id="addGifBtn"
                    class="px-2.5 py-1.5 border border-gray-200 rounded-md text-xs text-gray-500 bg-white hover:border-black hover:text-black transition-colors inline-flex items-center gap-1"
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
                            stroke-width="1.5"
                            d="M14.25 6.151c0 .893-.228 1.736-.648 2.468a2.25 2.25 0 110-4.286 2.25 2.25 0 01.648 1.818zm2.25 3.75H10.5v-6h6v6zM2.25 15.75v2.25A2.25 2.25 0 004.5 20.25h15A2.25 2.25 0 0021.75 18v-2.25M3 11.25a3 3 0 013-3h12a3 3 0 013 3v7.5a3 3 0 01-3 3H6a3 3 0 01-3-3v-7.5z"
                        />
                    </svg>

                    GIF
                </button>

            </div>

            <span
                id="sectionCount"
                class="text-xs text-gray-400 whitespace-nowrap ml-4"
            >
                1 / 20 sections
            </span>

        </div>

    </main>


    {{-- =========================================================
         JAVASCRIPT
    ========================================================== --}}
    <script>

        /* =====================================================
           GLOBAL STATE
        ====================================================== */

        let currentArticleId = null;
        let isEditing = false;

        let pendingSectionDeleteBlock = null;
        let pendingSectionId = null;

        let isInitializing = true;
        let isDirty = false;
        let pendingNavigation = null;
        let isSaving = false;
        let autoSaveTimer = null;
        let submitModalState = 'confirm';


        /* =====================================================
           LIMITS
        ====================================================== */

        const LIMITS = {
            maxSections: 20,
            maxCharsPerSection: 10000,
            warningChars: 8000
        };


        /* =====================================================
           QUILL CONFIGURATION
        ====================================================== */

        function getQuillOptions(placeholder = 'Write something...') {

            return {
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

                placeholder: placeholder
            };
        }


        /* =====================================================
           DIRTY STATE
        ====================================================== */

        function markDirty() {
            console.trace('markDirty called', {
                isInitializing,
                isDirty,
                activeElement: document.activeElement?.outerHTML?.slice(0, 300)
            });

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



        /* =====================================================
           UNSAVED CHANGES MODAL
        ====================================================== */

        function showUnsavedChangesModal(destination = null) {

            pendingNavigation = destination;

            const modal =
                document.getElementById('unsavedChangesModal');

            if (!modal) {
                return;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }


        function closeUnsavedChangesModal() {

            const modal =
                document.getElementById('unsavedChangesModal');

            if (!modal) {
                return;
            }

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            pendingNavigation = null;
        }


        function handleStayButton() {

            closeUnsavedChangesModal();
        }


        function handleLeaveAnyway() {

            const destination = pendingNavigation;

            closeUnsavedChangesModal();

            if (destination) {

                window.location.href =
                    destination;
            }
        }


        async function handleSaveDraftFromModal() {

            if (isSaving) {
                return;
            }

            try {

                isSaving = true;

                const button =
                    document.querySelector(
                        '[onclick="handleSaveDraftFromModal()"]'
                    );

                if (button) {
                    button.disabled = true;
                }

                const sections =
                    getSectionsData();

                if (
                    !validateSections(sections)
                ) {
                    return;
                }

                const response =
                    await saveArticleData(sections);

                if (response.success) {

                    clearDirty();

                    const destination =
                        pendingNavigation;

                    closeUnsavedChangesModal();

                    if (destination) {
                        window.location.href =
                            destination;
                    }

                } else {

                    alert(
                        'Error saving draft: ' +
                        (
                            response.error ||
                            'Unknown error'
                        )
                    );
                }

            } catch (err) {

                console.error(
                    'Save error:',
                    err
                );

                alert(
                    'Error saving draft: ' +
                    err.message
                );

            } finally {

                isSaving = false;

                const button =
                    document.querySelector(
                        '[onclick="handleSaveDraftFromModal()"]'
                    );

                if (button) {
                    button.disabled = false;
                }
            }
        }


        /* =====================================================
           DOM READY
        ====================================================== */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const pathParts =
                    window.location.pathname.split('/');

                const editId =
                    pathParts.includes('write') &&
                    pathParts[pathParts.length - 1] !== 'write'
                        ? pathParts[pathParts.length - 1]
                        : null;


                /* ---------------------------------------------
                   EDIT MODE
                --------------------------------------------- */

                if (
                    editId &&
                    !isNaN(editId)
                ) {

                    isEditing = true;

                    loadArticle(editId);

                }


                /* ---------------------------------------------
                   NEW ARTICLE MODE
                --------------------------------------------- */

                else {

                    const title =
                        sessionStorage.getItem(
                            'articleTitle'
                        ) || '';

                    const category =
                        sessionStorage.getItem(
                            'articleCategory'
                        ) || '';

                    const description =
                        sessionStorage.getItem(
                            'articleDescription'
                        ) || '';

                    const categoryId =
                        sessionStorage.getItem(
                            'articleCategoryId'
                        ) || '1';


                    const titleEl =
                        document.getElementById(
                            'article-title'
                        );

                    const descriptionEl =
                        document.getElementById(
                            'article-description'
                        );


                    titleEl.value = title;

                    autoResize(titleEl);


                    if (category) {

                        document.getElementById(
                            'article-category'
                        ).textContent = category;
                    }


                    if (description) {

                        descriptionEl.value =
                            description;

                        autoResize(
                            descriptionEl
                        );
                    }


                    if (title) {

                        createArticle(
                            title,
                            categoryId,
                            description
                        );
                    }
                }


                /* ---------------------------------------------
                   INITIAL QUILL
                   (NEW ARTICLE MODE ONLY)
                --------------------------------------------- */

                if (!isEditing) {

                    initQuillEditors();

                    isInitializing = false;
                }


                updateSectionCount();

                updateSaveStatus();
                startAutoSave();


                /* ---------------------------------------------
                   DASHBOARD LINK
                --------------------------------------------- */

                const dashboardLink =
                    document.querySelector(
                        'a[href="/writer/dashboard"]'
                    );

                if (dashboardLink) {

                    dashboardLink.addEventListener(
                        'click',
                        function(e) {

                            if (isDirty) {

                                e.preventDefault();

                                showUnsavedChangesModal(
                                    '/writer/dashboard'
                                );
                            }
                        }
                    );
                }


                /* ---------------------------------------------
                   BROWSER NAVIGATION
                --------------------------------------------- */

                window.addEventListener(
                    'beforeunload',
                    function(e) {

                        if (isDirty) {

                            e.preventDefault();

                            e.returnValue = '';
                        }
                    }
                );

            }
        );


        /* =====================================================
           SECTION MANAGEMENT
        ====================================================== */

        function addSection(type) {

            const container =
                document.getElementById('sections');

            const currentSections =
                container.querySelectorAll('[data-type]');


            if (
                currentSections.length >=
                LIMITS.maxSections
            ) {

                alert(
                    `Maximum ${LIMITS.maxSections} sections allowed.`
                );

                return;
            }


            markDirty();


            const block =
                document.createElement('div');

            block.className =
                'section-block';

            block.setAttribute(
                'data-type',
                type
            );


            const menu = `
                <div class="absolute right-2 top-2 section-menu z-10">

                    <button
                        onclick="removeSection(this)"
                        class="text-gray-300 hover:text-red-500 p-1 bg-white rounded"
                        title="Remove section"
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
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>

                    </button>

                </div>
            `;


            /* ---------------------------------------------
               TEXT
            --------------------------------------------- */

            if (type === 'text') {

                block.innerHTML = menu + `
                    <div
                        class="quill-editor"
                        data-placeholder="Write something..."
                    ></div>

                    <div class="mt-2 text-right">

                        <span class="text-xs text-gray-400 char-count">
                            0 / ${LIMITS.maxCharsPerSection.toLocaleString()} characters
                        </span>

                    </div>
                `;

                container.appendChild(block);


                const editorEl =
                    block.querySelector(
                        '.quill-editor'
                    );


                const quill =
                    new Quill(
                        editorEl,
                        getQuillOptions(
                            'Write something...'
                        )
                    );


                editorEl.setAttribute(
                    'data-quill-init',
                    'true'
                );


                setupQuillCounter(
                    quill,
                    block
                );


                quill.focus();
            }


            /* ---------------------------------------------
               IMAGE
            --------------------------------------------- */

            else if (type === 'image') {

                block.innerHTML = menu + `
                    <div
                        class="upload-placeholder w-full min-h-[200px] border-2 border-dashed border-gray-200 rounded-lg flex flex-col items-center justify-center cursor-pointer transition-all bg-gray-50"
                        onclick="this.querySelector('input').click()"
                    >

                        <svg
                            class="w-10 h-10 text-gray-300 mb-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>

                        <p class="text-sm text-gray-400">
                            Click to upload image
                        </p>

                        <input
                            type="file"
                            accept="image/*"
                            class="hidden"
                            onchange="handleImageUpload(this)"
                        />

                    </div>
                `;

                container.appendChild(block);
            }


            /* ---------------------------------------------
               VIDEO
            --------------------------------------------- */

            else if (type === 'video') {

                block.innerHTML = menu + `
                    <div class="space-y-3">

                        <input
                            type="text"
                            class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm placeholder-gray-400 focus:outline-none focus:border-black"
                            placeholder="Enter video URL (YouTube, Vimeo, or direct MP4/WebM)"
                            oninput="markDirty()"
                            onkeyup="previewVideoUrl(this)"
                        />

                        <div class="video-preview text-xs text-gray-400">
                            Paste a YouTube, Vimeo, or direct video URL
                        </div>

                    </div>
                `;

                container.appendChild(block);
            }


            /* ---------------------------------------------
               GIF
            --------------------------------------------- */

            else if (type === 'gif') {

                block.innerHTML = menu + `
                    <div
                        class="upload-placeholder w-full min-h-[200px] border-2 border-dashed border-gray-200 rounded-lg flex flex-col items-center justify-center cursor-pointer transition-all bg-gray-50"
                        onclick="this.querySelector('input').click()"
                    >

                        <svg
                            class="w-10 h-10 text-gray-300 mb-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>

                        <p class="text-sm text-gray-400">
                            Click to upload GIF
                        </p>

                        <input
                            type="file"
                            accept=".gif,image/gif"
                            class="hidden"
                            onchange="handleGifUpload(this)"
                        />

                    </div>
                `;

                container.appendChild(block);
            }


            updateSectionCount();
        }


        /* =====================================================
           LOAD SECTION FROM DATABASE
        ====================================================== */

        function addSectionWithContent(
            type,
            content,
            sectionId = null,
            publicId = null
        ) {

            const container =
                document.getElementById('sections');

            const block =
                document.createElement('div');

            block.className =
                'section-block';

            block.setAttribute(
                'data-type',
                type
            );


            if (sectionId) {

                block.setAttribute(
                    'data-section-id',
                    sectionId
                );
            }


            const menu = `
                <div class="absolute right-2 top-2 section-menu z-10">

                    <button
                        onclick="removeSection(this)"
                        class="text-gray-300 hover:text-red-500 p-1 bg-white rounded"
                        title="Remove section"
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
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>

                    </button>

                </div>
            `;


            /* ---------------------------------------------
               TEXT
            --------------------------------------------- */

            if (type === 'text') {

                block.innerHTML = menu + `
                    <div
                        class="quill-editor"
                        data-placeholder="Write something..."
                    ></div>

                    <div class="mt-2 text-right">

                        <span class="text-xs text-gray-400 char-count">
                            0 / ${LIMITS.maxCharsPerSection.toLocaleString()} characters
                        </span>

                    </div>
                `;

                container.appendChild(block);


                const editorEl =
                    block.querySelector(
                        '.quill-editor'
                    );


                const quill =
                    new Quill(
                        editorEl,
                        getQuillOptions(
                            'Write something...'
                        )
                    );


                editorEl.setAttribute(
                    'data-quill-init',
                    'true'
                );


                /*
                 * Load existing HTML.
                 *
                 * Quill will parse the existing
                 * HTML and display it inside editor.
                 */
                if (content) {

                    quill.clipboard.dangerouslyPasteHTML(content, 'silent');
                }


                updateQuillCounter(
                    quill,
                    block
                );


                quill.on(
                    'text-change',
                    function(delta, oldDelta, source) {

                        console.log('QUILL TEXT CHANGE', {
                            source,
                            delta,
                            oldDelta,
                            isInitializing,
                            isDirty
                        });

                        if (source === 'user') {
                            markDirty();
                        }

                        updateQuillCounter(
                            quill,
                            block
                        );
                    }
                );
            }


            /* ---------------------------------------------
               IMAGE
            --------------------------------------------- */

            else if (type === 'image') {

                let displayUrl = content;

                let dataLocalPath = '';

                let dataPublicId =
                    publicId || '';


                if (
                    content &&
                    content.startsWith('uploads/')
                ) {

                    displayUrl =
                        '/storage/' + content;

                    dataLocalPath =
                        content;

                    dataPublicId = '';
                }


                block.innerHTML =
                    menu +
                    `
                    <div class="article-media-image">
                        <img
                            src="${escapeHtmlAttribute(displayUrl)}"
                            alt="Gambar Artikel"
                            data-public-id="${escapeHtmlAttribute(dataPublicId)}"
                            ${dataLocalPath
                                ? `data-local-path="${escapeHtmlAttribute(dataLocalPath)}"`
                                : ''
                            }
                        />
                    </div>
                    `;

                container.appendChild(block);
            }


            /* ---------------------------------------------
               VIDEO
            --------------------------------------------- */

            else if (type === 'video') {

                block.innerHTML = menu + `
                    <div class="space-y-3">

                        <input
                            type="text"
                            class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm placeholder-gray-400 focus:outline-none focus:border-black"
                            placeholder="Enter video URL"
                            value="${escapeHtmlAttribute(content || '')}"
                            oninput="markDirty()"
                            onblur="previewVideoUrl(this)"
                        />

                        <div class="video-preview text-xs text-gray-400">
                        </div>

                    </div>
                `;

                container.appendChild(block);


                if (content) {

                    const input =
                        block.querySelector(
                            'input'
                        );

                    setTimeout(
                        () => previewVideoUrl(input),
                        100
                    );
                }
            }


            /* ---------------------------------------------
               GIF
            --------------------------------------------- */

            else if (type === 'gif') {

                let displayUrl = content;

                let dataLocalPath = '';


                if (
                    content &&
                    content.startsWith('uploads/')
                ) {

                    displayUrl =
                        '/storage/' + content;

                    dataLocalPath =
                        content;
                }


                block.innerHTML =
                    menu +
                    `
                    <div class="article-media-gif">
                        <img
                            src="${escapeHtmlAttribute(displayUrl)}"
                            alt="GIF Animation"
                            ${dataLocalPath
                                ? `data-local-path="${escapeHtmlAttribute(dataLocalPath)}"`
                                : ''
                            }
                        />
                    </div>
                    `;

                container.appendChild(block);
            }


            updateSectionCount();
        }


        /* =====================================================
           QUILL INITIALIZATION
        ====================================================== */

        function initQuillEditors() {

            document
                .querySelectorAll(
                    '.quill-editor:not([data-quill-init])'
                )
                .forEach(function(el) {

                    const quill =
                        new Quill(
                            el,
                            getQuillOptions(
                                el.getAttribute(
                                    'data-placeholder'
                                ) ||
                                'Write something...'
                            )
                        );


                    el.setAttribute(
                        'data-quill-init',
                        'true'
                    );


                    const block =
                        el.closest(
                            '[data-type="text"]'
                        );


                    if (block) {

                        setupQuillCounter(
                            quill,
                            block
                        );
                    }
                });
        }


        /* =====================================================
           QUILL CHARACTER COUNTER
        ====================================================== */

        function setupQuillCounter(
            quill,
            block
        ) {

            updateQuillCounter(
                quill,
                block
            );


            quill.on(
                'text-change',
                function(delta, oldDelta, source) {

                    console.log('QUILL TEXT CHANGE', {
                        source,
                        delta,
                        oldDelta,
                        isInitializing,
                        isDirty
                    });

                    if (source === 'user') {
                        markDirty();
                    }

                    updateQuillCounter(
                        quill,
                        block
                    );
                }
            );
        }


        function updateQuillCounter(
            quill,
            block
        ) {

            const text =
                quill
                    .getText()
                    .trim();


            const charCount =
                text.length;


            const countEl =
                block.querySelector(
                    '.char-count'
                );


            if (!countEl) {
                return;
            }


            countEl.textContent =
                `${charCount.toLocaleString()} / ${LIMITS.maxCharsPerSection.toLocaleString()} characters`;


            countEl.classList.remove(
                'text-gray-400',
                'text-orange-500',
                'text-red-500'
            );


            if (
                charCount >
                LIMITS.maxCharsPerSection
            ) {

                countEl.classList.add(
                    'text-red-500'
                );

            } else if (
                charCount >
                LIMITS.warningChars
            ) {

                countEl.classList.add(
                    'text-orange-500'
                );

            } else {

                countEl.classList.add(
                    'text-gray-400'
                );
            }
        }


        /* =====================================================
           SECTION COUNT
        ====================================================== */

        function updateSectionCount() {

            const container =
                document.getElementById(
                    'sections'
                );

            const sections =
                container.querySelectorAll(
                    '[data-type]'
                );

            const countEl =
                document.getElementById(
                    'sectionCount'
                );


            if (countEl) {

                countEl.textContent =
                    `${sections.length} / ${LIMITS.maxSections} sections`;
            }
        }


        /* =====================================================
           REMOVE SECTION
        ====================================================== */

        function removeSection(btn) {

            const block =
                btn.closest(
                    '[data-type]'
                );

            const container =
                document.getElementById(
                    'sections'
                );


            if (
                container.children.length <= 1
            ) {

                alert(
                    'You must have at least one section.'
                );

                return;
            }


            const sectionId =
                block.getAttribute(
                    'data-section-id'
                );


            openSectionDeleteConfirm(
                block,
                sectionId
            );
        }


        /* =====================================================
           IMAGE UPLOAD
        ====================================================== */

        function handleImageUpload(input) {

            if (
                !input.files ||
                !input.files[0]
            ) {
                return;
            }


            const container =
                input.parentElement;

            const sectionBlock =
                input.closest(
                    '[data-type="image"]'
                );

            const oldImg =
                sectionBlock
                    ? sectionBlock.querySelector('img')
                    : null;

            const oldPublicId =
                oldImg
                    ? oldImg.dataset.publicId
                    : null;


            container.innerHTML = `
                <div class="w-full min-h-[200px] flex flex-col items-center justify-center bg-gray-50">

                    <svg
                        class="animate-spin w-8 h-8 text-gray-400 mb-2"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>

                    <p class="text-sm text-gray-400">
                        Uploading...
                    </p>

                </div>
            `;


            container.style.border = 'none';
            container.style.background = 'transparent';
            container.style.minHeight = 'auto';


            const formData =
                new FormData();

            formData.append(
                'file',
                input.files[0]
            );


            fetch(
                '/local-upload/image',
                {
                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN':
                            getCsrfToken(),

                        'Accept':
                            'application/json'
                    },

                    body: formData
                }
            )
            .then(async res => {

                if (!res.ok) {

                    const text =
                        await res.text();

                    try {

                        const json =
                            JSON.parse(text);

                        throw new Error(
                            json.message ||
                            'Server error'
                        );

                    } catch {

                        throw new Error(
                            'Server error: ' +
                            res.status
                        );
                    }
                }

                return res.json();
            })
            .then(data => {

                if (
                    data.success &&
                    data.path
                ) {

                    markDirty();


                    const newPath =
                        data.path;


                    /*
                     * Legacy Cloudinary cleanup.
                     *
                     * Kept because old articles
                     * may still contain Cloudinary images.
                     */
                    if (
                        oldPublicId &&
                        currentArticleId
                    ) {

                        fetch(
                            `/articles/${currentArticleId}/unsaved-image`,
                            {
                                method: 'DELETE',

                                headers: {
                                    'Content-Type':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        getCsrfToken(),

                                    'Accept':
                                        'application/json'
                                },

                                body: JSON.stringify({
                                    public_id:
                                        oldPublicId
                                })
                            }
                        )
                        .then(
                            res => res.json()
                        )
                        .then(
                            cleanupData => {

                                if (
                                    cleanupData.success
                                ) {

                                    console.log(
                                        'Old image cleanup queued:',
                                        oldPublicId
                                    );
                                }
                            }
                        )
                        .catch(
                            err =>
                                console.warn(
                                    'Old image cleanup error:',
                                    err
                                )
                        );
                    }


                    container.innerHTML = `
                        <div class="article-media-image">
                            <img
                                src="${escapeHtmlAttribute(data.url)}"
                                alt="Gambar Artikel"
                                data-public-id=""
                                data-local-path="${escapeHtmlAttribute(newPath)}"
                            />
                        </div>
                    `;

                } else {

                    showUploadError(
                        container,
                        data.message ||
                        'Upload error',
                        'image'
                    );
                }
            })
            .catch(err => {

                console.error(
                    'Upload error:',
                    err
                );

                showUploadError(
                    container,
                    err.message,
                    'image'
                );
            });
        }


        /* =====================================================
           VIDEO PREVIEW
        ====================================================== */

        function previewVideoUrl(input) {

            const url =
                input.value.trim();

            const previewEl =
                input.parentElement.querySelector(
                    '.video-preview'
                );


            if (!previewEl) {
                return;
            }


            if (!url) {

                previewEl.innerHTML =
                    '<p class="text-xs text-gray-400">Paste a YouTube, Vimeo, or direct video URL</p>';

                previewEl.className =
                    'video-preview text-xs text-gray-400';

                return;
            }


            previewEl.innerHTML =
                '<p class="text-xs text-gray-400">Loading preview...</p>';

            previewEl.className =
                'video-preview text-xs text-gray-400';


            if (
                !url.startsWith('http://') &&
                !url.startsWith('https://')
            ) {

                previewEl.innerHTML =
                    '<p class="text-xs text-red-500">✗ Invalid URL (must start with http:// or https://)</p>';

                previewEl.className =
                    'video-preview text-xs text-red-500';

                return;
            }


            const lowerUrl =
                url.toLowerCase();


            if (
                lowerUrl.includes('javascript:') ||
                lowerUrl.includes('data:') ||
                lowerUrl.includes('file:')
            ) {

                previewEl.innerHTML =
                    '<p class="text-xs text-red-500">✗ Invalid URL (dangerous scheme)</p>';

                previewEl.className =
                    'video-preview text-xs text-red-500';

                return;
            }


            if (!currentArticleId) {

                previewEl.innerHTML =
                    '<p class="text-xs text-yellow-600">⚠ Article not saved yet. Save article first to enable preview.</p>';

                previewEl.className =
                    'video-preview text-xs text-yellow-600';

                return;
            }


            fetch(
                `/articles/${currentArticleId}/preview-video`,
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'X-CSRF-TOKEN':
                            getCsrfToken(),

                        'Accept':
                            'application/json'
                    },

                    body: JSON.stringify({
                        url: url
                    })
                }
            )
            .then(
                res => res.json()
            )
            .then(data => {

                if (data.valid) {

                    previewEl.innerHTML =
                        data.embedHtml;

                    previewEl.className =
                        'video-preview article-media-video mt-3';

                } else {

                    previewEl.innerHTML =
                        `<p class="text-xs text-red-500">✗ ${escapeHtml(data.message || 'Invalid video URL')}</p>`;

                    previewEl.className =
                        'video-preview text-xs text-red-500';
                }
            })
            .catch(err => {

                console.error(
                    'Preview error:',
                    err
                );

                previewEl.innerHTML =
                    '<p class="text-xs text-red-500">✗ Error loading preview</p>';

                previewEl.className =
                    'video-preview text-xs text-red-500';
            });
        }


        /* =====================================================
           GIF UPLOAD
        ====================================================== */

        function handleGifUpload(input) {

            if (
                !input.files ||
                !input.files[0]
            ) {
                return;
            }


            const container =
                input.parentElement;


            container.innerHTML = `
                <div class="w-full min-h-[200px] flex flex-col items-center justify-center bg-gray-50">

                    <svg
                        class="animate-spin w-8 h-8 text-gray-400 mb-2"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>

                    <p class="text-sm text-gray-400">
                        Uploading GIF...
                    </p>

                </div>
            `;


            container.style.border = 'none';
            container.style.background = 'transparent';
            container.style.minHeight = 'auto';


            const formData =
                new FormData();

            formData.append(
                'file',
                input.files[0]
            );


            fetch(
                '/local-upload/gif',
                {
                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN':
                            getCsrfToken(),

                        'Accept':
                            'application/json'
                    },

                    body: formData
                }
            )
            .then(async res => {

                if (!res.ok) {

                    const text =
                        await res.text();

                    try {

                        const json =
                            JSON.parse(text);

                        throw new Error(
                            json.message ||
                            'Server error'
                        );

                    } catch {

                        throw new Error(
                            'Server error: ' +
                            res.status
                        );
                    }
                }

                return res.json();
            })
            .then(data => {

                if (
                    data.success &&
                    data.path
                ) {

                    markDirty();


                    const newPath =
                        data.path;


                    container.innerHTML = `
                        <div class="article-media-gif">
                            <img
                                src="${escapeHtmlAttribute(data.url)}"
                                alt="GIF Animation"
                                data-local-path="${escapeHtmlAttribute(newPath)}"
                            />
                        </div>
                    `;

                } else {

                    showUploadError(
                        container,
                        data.message ||
                        'Upload error',
                        'gif'
                    );
                }
            })
            .catch(err => {

                console.error(
                    'GIF upload error:',
                    err
                );

                showUploadError(
                    container,
                    err.message,
                    'gif'
                );
            });
        }


        /* =====================================================
           AUTOSAVE
        ====================================================== */

        function startAutoSave() {

            if (autoSaveTimer) {
                clearInterval(autoSaveTimer);
            }

            autoSaveTimer = setInterval(
                async function() {
                    if (isInitializing || isSaving) {
                        return;
                    }

                    if (!isDirty || !currentArticleId) {
                        return;
                    }

                    await autoSave();
                },
                10000
            );
        }


        async function autoSave() {

            if (isSaving || !currentArticleId || !isDirty) {
                return;
            }

            try {
                isSaving = true;
                updateSaveStatus();

                const sections = getSectionsData();

                if (!validateSections(sections)) {
                    return;
                }

                const data = await saveArticleData(sections);

                if (data.success) {
                    clearDirty();
                } else {
                    throw new Error(
                        data.error ||
                        data.message ||
                        'Auto-save failed'
                    );
                }

            } catch (err) {
                console.error('Auto-save error:', err);

                isDirty = true;

                const statusEl =
                    document.getElementById('saveStatus');

                if (statusEl) {
                    statusEl.textContent = 'Save failed';
                    statusEl.className =
                        'text-xs font-medium text-red-600 px-2.5 py-1';
                }

                setTimeout(function() {
                    if (!isSaving) {
                        updateSaveStatus();
                    }
                }, 2500);

            } finally {
                isSaving = false;
                updateSaveStatus();
            }
        }


        /* =====================================================
           SAVE / SUBMIT
        ====================================================== */

        async function saveDraft() {

            if (isSaving) {
                return;
            }


            if (!currentArticleId) {

                alert(
                    'Article belum tersimpan. Tunggu sebentar...'
                );

                return;
            }


            try {

                isSaving = true;


                const sections =
                    getSectionsData();


                if (
                    !validateSections(sections)
                ) {
                    return;
                }


                const data =
                    await saveArticleData(
                        sections
                    );


                if (data.success) {

                    clearDirty();

                    alert(
                        'Draft saved!'
                    );

                } else {

                    alert(
                        'Error saving draft: ' +
                        (
                            data.error ||
                            data.message ||
                            'Unknown error'
                        )
                    );
                }

            } catch (err) {

                console.error(
                    'Save error:',
                    err
                );

                alert(
                    'Error saving draft: ' +
                    err.message
                );

            } finally {

                isSaving = false;
                updateSaveStatus();
            }
        }


        function openSubmitModal() {

            if (isSaving) {
                return;
            }

            const modal =
                document.getElementById('submitReviewModal');

            if (!modal) {
                return;
            }

            submitModalState = 'confirm';
            updateSubmitModal();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }


        function closeSubmitModal() {

            const modal =
                document.getElementById('submitReviewModal');

            if (!modal) {
                return;
            }

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }


        function updateSubmitModal() {

            const confirmView =
                document.getElementById('submitConfirmView');
            const successView =
                document.getElementById('submitSuccessView');
            const errorView =
                document.getElementById('submitErrorView');

            if (confirmView) {
                confirmView.classList.toggle(
                    'hidden',
                    submitModalState !== 'confirm'
                );
            }

            if (successView) {
                successView.classList.toggle(
                    'hidden',
                    submitModalState !== 'success'
                );
            }

            if (errorView) {
                errorView.classList.toggle(
                    'hidden',
                    submitModalState !== 'error'
                );
            }
        }


        async function confirmSubmitArticle() {

            if (isSaving) {
                return;
            }

            closeSubmitModal();
            await submitArticle();
        }


        async function submitArticle() {

            if (isSaving) {
                return;
            }


            if (!currentArticleId) {

                alert(
                    'Article belum tersimpan. Tunggu sebentar...'
                );

                return;
            }


            try {

                isSaving = true;
                updateSaveStatus();


                const sections =
                    getSectionsData();


                if (
                    !validateSections(sections)
                ) {
                    return;
                }


                /*
                 * Save current article content first.
                 */
                const saveData =
                    await saveArticleData(
                        sections
                    );


                if (!saveData.success) {

                    throw new Error(
                        saveData.error ||
                        saveData.message ||
                        'Failed to save article'
                    );
                }


                /*
                 * Then submit for review.
                 */
                const response =
                    await fetch(
                        `/articles/${currentArticleId}/submit`,
                        {
                            method: 'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    getCsrfToken(),

                                'Accept':
                                    'application/json'
                            },

                            body: JSON.stringify({})
                        }
                    );


                const data =
                    await response.json();


                if (data.success) {

                    clearDirty();

                    submitModalState = 'success';
                    const modal =
                        document.getElementById('submitReviewModal');

                    if (modal) {
                        updateSubmitModal();
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    } else {
                        window.location.href = '/writer/dashboard';
                    }

                } else {

                    throw new Error(
                        data.error ||
                        data.message ||
                        'Failed to submit article'
                    );
                }

            } catch (err) {

                console.error(
                    'Submit error:',
                    err
                );

                submitModalState = 'error';

                const errorMessage =
                    document.getElementById('submitErrorMessage');

                if (errorMessage) {
                    errorMessage.textContent =
                        err.message || 'Failed to submit article';
                }

                const modal =
                    document.getElementById('submitReviewModal');

                if (modal) {
                    updateSubmitModal();
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

            } finally {

                isSaving = false;
                updateSaveStatus();
            }
        }


        /* =====================================================
           SAVE ARTICLE DATA
        ====================================================== */

        async function saveArticleData(
            sections
        ) {

            if (!currentArticleId) {

                throw new Error(
                    'Article ID not available'
                );
            }


            const titleEl =
                document.getElementById(
                    'article-title'
                );

            const descriptionEl =
                document.getElementById(
                    'article-description'
                );


            const title =
                titleEl
                    ? titleEl.value.trim()
                    : '';


            const description =
                descriptionEl
                    ? descriptionEl.value.trim()
                    : '';


            const response =
                await fetch(
                    `/articles/${currentArticleId}/sections`,
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json',

                            'X-CSRF-TOKEN':
                                getCsrfToken(),

                            'Accept':
                                'application/json'
                        },

                        /*
                         * We send title + description here too.
                         *
                         * The ArticleController will be updated
                         * in the next step to process these fields.
                         */
                        body: JSON.stringify({
                            title: title,
                            description: description,
                            sections: sections
                        })
                    }
                );


            if (!response.ok) {

                let message =
                    `HTTP ${response.status}`;

                try {

                    const errorData =
                        await response.json();

                    message =
                        errorData.message ||
                        errorData.error ||
                        message;

                } catch {
                    // Keep HTTP error message.
                }

                throw new Error(
                    message
                );
            }


            return await response.json();
        }


        /* =====================================================
           VALIDATION
        ====================================================== */

        function validateSections(
            sections
        ) {

            if (
                sections.length >
                LIMITS.maxSections
            ) {

                alert(
                    `Maximum ${LIMITS.maxSections} sections allowed.`
                );

                return false;
            }


            for (
                let i = 0;
                i < sections.length;
                i++
            ) {

                const section =
                    sections[i];


                if (
                    section.type === 'text'
                ) {

                    const charCount =
                        getHtmlTextLength(
                            section.content
                        );


                    if (
                        charCount >
                        LIMITS.maxCharsPerSection
                    ) {

                        alert(
                            `Section ${i + 1} exceeds ${LIMITS.maxCharsPerSection.toLocaleString()} character limit.`
                        );

                        return false;
                    }
                }
            }


            return true;
        }


        function getHtmlTextLength(
            html
        ) {

            const temp =
                document.createElement(
                    'div'
                );

            temp.innerHTML =
                html || '';


            return (
                temp.textContent ||
                temp.innerText ||
                ''
            )
                .trim()
                .length;
        }


        /* =====================================================
           LOAD ARTICLE
        ====================================================== */

        function loadArticle(id) {

            fetch(
                `/articles/${id}`,
                {
                    headers: {
                        'Accept':
                            'application/json',

                        'X-CSRF-TOKEN':
                            getCsrfToken()
                    }
                }
            )
            .then(
                res => {

                    if (!res.ok) {

                        throw new Error(
                            `HTTP ${res.status}`
                        );
                    }

                    return res.json();
                }
            )
            .then(data => {

                if (!data.success) {

                    throw new Error(
                        data.error ||
                        'Failed to load article'
                    );
                }


                currentArticleId =
                    data.article_id;


                /* -----------------------------------------
                   TITLE
                ------------------------------------------ */

                const titleEl =
                    document.getElementById(
                        'article-title'
                    );

                titleEl.value =
                    data.title || '';

                autoResize(
                    titleEl
                );


                /* -----------------------------------------
                   CATEGORY
                ------------------------------------------ */

                if (data.category) {

                    document.getElementById(
                        'article-category'
                    ).textContent =
                        data.category;
                }


                /* -----------------------------------------
                   DESCRIPTION
                ------------------------------------------ */

                const descriptionEl =
                    document.getElementById(
                        'article-description'
                    );

                descriptionEl.value =
                    data.description || '';

                autoResize(
                    descriptionEl
                );


                /* -----------------------------------------
                   SECTIONS
                ------------------------------------------ */

                if (
                    data.sections &&
                    data.sections.length > 0
                ) {

                    const container =
                        document.getElementById(
                            'sections'
                        );


                    container.innerHTML =
                        '';


                    data.sections.forEach(
                        function(section) {

                            addSectionWithContent(
                                section.type,
                                section.content,
                                section.id,
                                section.public_id
                            );
                        }
                    );

                } else {

                    /*
                     * Keep one empty text section.
                     */
                    const container =
                        document.getElementById(
                            'sections'
                        );

                    container.innerHTML = `
                        <div
                            class="section-block"
                            data-type="text"
                        >

                            <div class="absolute right-2 top-2 section-menu z-10">

                                <button
                                    onclick="removeSection(this)"
                                    class="text-gray-300 hover:text-red-500 p-1 bg-white rounded"
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
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>

                                </button>

                            </div>

                            <div
                                class="quill-editor"
                                data-placeholder="Start writing..."
                            ></div>

                            <div class="mt-2 text-right">

                                <span class="text-xs text-gray-400 char-count">
                                    0 / ${LIMITS.maxCharsPerSection.toLocaleString()} characters
                                </span>

                            </div>

                        </div>
                    `;

                    initQuillEditors();
                }


                updateSectionCount();


                /*
                 * IMPORTANT:
                 *
                 * Article is fully loaded.
                 * Only now allow dirty state.
                 */
                isInitializing =
                    false;

                clearDirty();

            })
            .catch(err => {

                console.error(
                    'Load error:',
                    err
                );

                alert(
                    'Failed to load article: ' +
                    err.message
                );

                isInitializing =
                    false;
            });
        }


        /* =====================================================
           GET SECTIONS DATA
        ====================================================== */

        function getSectionsData() {

            const sections = [];


            document
                .querySelectorAll('[data-type]')
                .forEach(function(block) {

                    const type =
                        block.getAttribute(
                            'data-type'
                        );


                    /* -----------------------------------------
                       TEXT
                    ------------------------------------------ */

                    if (type === 'text') {

                        const editor =
                            block.querySelector(
                                '.ql-editor'
                            );


                        if (editor) {

                            sections.push({
                                type: 'text',
                                content:
                                    editor.innerHTML
                            });
                        }
                    }


                    /* -----------------------------------------
                       IMAGE
                    ------------------------------------------ */

                    else if (
                        type === 'image'
                    ) {

                        const img =
                            block.querySelector(
                                'img'
                            );


                        if (!img) {
                            return;
                        }


                        const section = {
                            type: 'image'
                        };


                        if (
                            img.dataset.localPath
                        ) {

                            section.content =
                                img.dataset.localPath;

                            section.public_id =
                                null;

                        } else if (
                            img.dataset.publicId
                        ) {

                            /*
                             * Legacy Cloudinary image.
                             */
                            section.content =
                                img.src;

                            section.public_id =
                                img.dataset.publicId;

                        } else {

                            section.content =
                                img.src;
                        }


                        sections.push(
                            section
                        );
                    }


                    /* -----------------------------------------
                       VIDEO
                    ------------------------------------------ */

                    else if (
                        type === 'video'
                    ) {

                        const input =
                            block.querySelector(
                                'input[type="text"]'
                            );


                        if (
                            input &&
                            input.value.trim()
                        ) {

                            sections.push({
                                type: 'video',
                                content:
                                    input.value.trim()
                            });
                        }
                    }


                    /* -----------------------------------------
                       GIF
                    ------------------------------------------ */

                    else if (
                        type === 'gif'
                    ) {

                        const img =
                            block.querySelector(
                                'img'
                            );


                        if (!img) {
                            return;
                        }


                        const section = {
                            type: 'gif'
                        };


                        if (
                            img.dataset.localPath
                        ) {

                            section.content =
                                img.dataset.localPath;

                        } else {

                            section.content =
                                img.src;
                        }


                        sections.push(
                            section
                        );
                    }

                });


            return sections;
        }


        /* =====================================================
           SECTION DELETE CONFIRMATION
        ====================================================== */

        function openSectionDeleteConfirm(
            block,
            sectionId
        ) {

            pendingSectionDeleteBlock =
                block;

            pendingSectionId =
                sectionId;


            const modal =
                document.getElementById(
                    'sectionDeleteConfirmModal'
                );


            if (!modal) {

                console.error(
                    'Modal not found: sectionDeleteConfirmModal'
                );

                return;
            }


            modal.classList.remove(
                'hidden'
            );

            modal.classList.add(
                'flex'
            );
        }


        function closeSectionDeleteConfirm() {

            const modal =
                document.getElementById(
                    'sectionDeleteConfirmModal'
                );


            if (!modal) {
                return;
            }


            modal.classList.add(
                'hidden'
            );

            modal.classList.remove(
                'flex'
            );


            pendingSectionDeleteBlock =
                null;

            pendingSectionId =
                null;
        }


        async function confirmSectionDelete() {

            if (
                !pendingSectionDeleteBlock
            ) {

                closeSectionDeleteConfirm();

                return;
            }


            const block =
                pendingSectionDeleteBlock;

            const sectionId =
                pendingSectionId;


            try {

                /*
                 * Existing saved section.
                 */
                if (
                    sectionId &&
                    currentArticleId
                ) {

                    const response =
                        await fetch(
                            `/articles/${currentArticleId}/sections/${sectionId}`,
                            {
                                method: 'DELETE',

                                headers: {
                                    'Content-Type':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        getCsrfToken(),

                                    'Accept':
                                        'application/json'
                                }
                            }
                        );


                    const data =
                        await response.json();


                    if (!data.success) {

                        throw new Error(
                            data.message ||
                            data.error ||
                            'Failed to delete section'
                        );
                    }


                    block.remove();

                    updateSectionCount();

                    markDirty();

                    closeSectionDeleteConfirm();

                    return;
                }


                /*
                 * Unsaved section.
                 */
                const sectionType =
                    block.getAttribute(
                        'data-type'
                    );


                /* -----------------------------------------
                   UNSAVED IMAGE
                ------------------------------------------ */

                if (
                    sectionType === 'image'
                ) {

                    const img =
                        block.querySelector(
                            'img'
                        );


                    const publicId =
                        img
                            ? img.dataset.publicId
                            : null;

                    const localPath =
                        img
                            ? img.dataset.localPath
                            : null;


                    if (
                        currentArticleId &&
                        (publicId || localPath)
                    ) {

                        const body =
                            publicId
                                ? {
                                    public_id:
                                        publicId
                                }
                                : {
                                    local_path:
                                        localPath
                                };


                        const response =
                            await fetch(
                                `/articles/${currentArticleId}/unsaved-image`,
                                {
                                    method: 'DELETE',

                                    headers: {
                                        'Content-Type':
                                            'application/json',

                                        'X-CSRF-TOKEN':
                                            getCsrfToken(),

                                        'Accept':
                                            'application/json'
                                    },

                                    body:
                                        JSON.stringify(
                                            body
                                        )
                                }
                            );


                        const data =
                            await response.json();


                        if (!data.success) {

                            throw new Error(
                                data.message ||
                                data.error ||
                                'Failed to cleanup image'
                            );
                        }
                    }


                    block.remove();

                    updateSectionCount();

                    markDirty();

                    closeSectionDeleteConfirm();

                    return;
                }


                /* -----------------------------------------
                   UNSAVED GIF
                ------------------------------------------ */

                if (
                    sectionType === 'gif'
                ) {

                    const img =
                        block.querySelector(
                            'img'
                        );


                    const localPath =
                        img
                            ? img.dataset.localPath
                            : null;


                    if (
                        currentArticleId &&
                        localPath
                    ) {

                        const response =
                            await fetch(
                                `/articles/${currentArticleId}/unsaved-image`,
                                {
                                    method: 'DELETE',

                                    headers: {
                                        'Content-Type':
                                            'application/json',

                                        'X-CSRF-TOKEN':
                                            getCsrfToken(),

                                        'Accept':
                                            'application/json'
                                    },

                                    body:
                                        JSON.stringify({
                                            local_path:
                                                localPath
                                        })
                                }
                            );


                        const data =
                            await response.json();


                        if (!data.success) {

                            throw new Error(
                                data.message ||
                                data.error ||
                                'Failed to cleanup GIF'
                            );
                        }
                    }


                    block.remove();

                    updateSectionCount();

                    markDirty();

                    closeSectionDeleteConfirm();

                    return;
                }


                /* -----------------------------------------
                   UNSAVED TEXT / VIDEO
                ------------------------------------------ */

                block.remove();

                updateSectionCount();

                markDirty();

                closeSectionDeleteConfirm();

            } catch (err) {

                console.error(
                    'Delete section error:',
                    err
                );

                alert(
                    'Error deleting section: ' +
                    err.message
                );

                closeSectionDeleteConfirm();
            }
        }


        /* =====================================================
           CREATE ARTICLE
        ====================================================== */

        function createArticle(
            title,
            categoryId,
            description
        ) {

            const coverImage =
                sessionStorage.getItem(
                    'articleCover'
                ) || null;


            /*
             * Kept temporarily because your current
             * create flow still sends this field.
             *
             * ArticleController can ignore it if
             * the database does not have the column.
             */
            const coverPublicId =
                sessionStorage.getItem(
                    'articleCoverPublicId'
                ) || null;


            fetch(
                '/articles',
                {
                    method: 'POST',

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

                        category_id:
                            parseInt(
                                categoryId
                            ),

                        description:
                            description,

                        cover_image:
                            coverImage,

                        cover_image_public_id:
                            coverPublicId

                    })
                }
            )
            .then(
                async res => {

                    if (!res.ok) {

                        let message =
                            `HTTP ${res.status}`;

                        try {

                            const errorData =
                                await res.json();

                            message =
                                errorData.message ||
                                errorData.error ||
                                message;

                        } catch {
                            // Keep HTTP message.
                        }

                        throw new Error(
                            message
                        );
                    }

                    return res.json();
                }
            )
            .then(data => {

                if (data.success) {

                    currentArticleId =
                        data.article_id;


                    sessionStorage.removeItem(
                        'articleCover'
                    );

                    sessionStorage.removeItem(
                        'articleCoverPublicId'
                    );


                    console.log(
                        'Article created, ID:',
                        currentArticleId
                    );

                } else {

                    alert(
                        'Gagal membuat article: ' +
                        JSON.stringify(data)
                    );
                }
            })
            .catch(err => {

                console.error(
                    'Error creating article:',
                    err
                );

                alert(
                    'Error creating article: ' +
                    err.message
                );
            });
        }


        /* =====================================================
           AUTO RESIZE
        ====================================================== */

        function autoResize(el) {

            if (!el) {
                return;
            }

            el.style.height =
                'auto';

            el.style.height =
                el.scrollHeight + 'px';
        }


        /* =====================================================
           CSRF
        ====================================================== */

        function getCsrfToken() {

            const token =
                document
                    .querySelector(
                        'meta[name="csrf-token"]'
                    )
                    ?.getAttribute(
                        'content'
                    );


            if (!token) {

                throw new Error(
                    'CSRF token not found.'
                );
            }


            return token;
        }


        /* =====================================================
           HTML ESCAPE HELPERS
        ====================================================== */

        function escapeHtml(value) {

            const div =
                document.createElement(
                    'div'
                );

            div.textContent =
                value ?? '';

            return div.innerHTML;
        }


        function escapeHtmlAttribute(value) {

            return escapeHtml(
                value
            )
                .replace(
                    /"/g,
                    '&quot;'
                );
        }


        /* =====================================================
           UPLOAD ERROR
        ====================================================== */

        function showUploadError(
            container,
            message,
            type
        ) {

            const isGif =
                type === 'gif';


            const label =
                isGif
                    ? 'GIF'
                    : 'image';


            container.innerHTML = `
                <div class="w-full min-h-[200px] border-2 border-dashed border-red-300 rounded-lg flex flex-col items-center justify-center bg-red-50">

                    <svg
                        class="w-10 h-10 text-red-400 mb-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>

                    <p class="text-sm text-red-500">
                        Upload failed
                    </p>

                    <p class="text-xs text-red-400 mt-2 text-center px-4">
                        ${escapeHtml(message)}
                    </p>

                    <button
                        onclick="location.reload()"
                        class="mt-2 text-xs text-red-500 underline"
                    >
                        Try again
                    </button>

                </div>
            `;
        }

    </script>


    {{-- =========================================================
         SUBMIT FOR REVIEW MODAL
    ========================================================== --}}
    <div
        id="submitReviewModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center"
    >

        <div
            class="modal-backdrop absolute inset-0 bg-black/40"
            onclick="closeSubmitModal()"
        ></div>

        <div
            class="relative bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6"
        >

            {{-- CONFIRM --}}
            <div id="submitConfirmView">

                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-black">Submit for Review?</h3>
                </div>

                <p class="text-sm text-gray-600 leading-relaxed mb-6">
                    Your latest changes will be saved before this article is submitted.
                    Once submitted, the article will be sent to an administrator for review.
                </p>

                <div class="flex gap-3 justify-end">
                    <button
                        onclick="closeSubmitModal()"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                    >
                        Cancel
                    </button>

                    <button
                        onclick="confirmSubmitArticle()"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors"
                    >
                        Submit for Review
                    </button>
                </div>

            </div>

            {{-- SUCCESS --}}
            <div id="submitSuccessView" class="hidden text-center">

                <div class="mx-auto w-12 h-12 bg-green-50 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h3 class="mt-4 text-lg font-semibold text-black">
                    Submitted for Review
                </h3>

                <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                    Your article has been submitted successfully and is now waiting for administrator review.
                </p>

                <button
                    onclick="window.location.href='/writer/dashboard'"
                    class="mt-6 w-full px-4 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors"
                >
                    Back to Dashboard
                </button>

            </div>

            {{-- ERROR --}}
            <div id="submitErrorView" class="hidden">

                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333-.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-black">
                        Submission Failed
                    </h3>
                </div>

                <p
                    id="submitErrorMessage"
                    class="text-sm text-gray-600 leading-relaxed mb-6"
                ></p>

                <div class="flex justify-end">
                    <button
                        onclick="closeSubmitModal(); submitModalState='confirm';"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors"
                    >
                        Close
                    </button>
                </div>

            </div>

        </div>
    </div>


    {{-- =========================================================
         SECTION DELETE MODAL
    ========================================================== --}}
    <div
        id="sectionDeleteConfirmModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center"
    >

        <div
            class="modal-backdrop absolute inset-0 bg-black/40"
            onclick="closeSectionDeleteConfirm()"
        ></div>


        <div
            class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6"
        >

            <div class="flex items-center gap-3 mb-3">

                <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center">

                    <svg
                        class="w-5 h-5 text-red-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77-1.333.192-3 1.732 3z"
                        />
                    </svg>

                </div>

                <h3 class="text-lg font-semibold text-black">
                    Delete Section?
                </h3>

            </div>


            <p class="text-sm text-gray-600 mb-6">
                Are you sure you want to remove this section?
                This action cannot be undone and the content will be lost.
            </p>


            <div class="flex gap-3 justify-end">

                <button
                    onclick="closeSectionDeleteConfirm()"
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                >
                    Cancel
                </button>

                <button
                    onclick="confirmSectionDelete()"
                    class="px-4 py-2.5 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors"
                >
                    Delete
                </button>

            </div>

        </div>

    </div>


    {{-- =========================================================
         UNSAVED CHANGES MODAL
    ========================================================== --}}
    <div
        id="unsavedChangesModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center"
    >

        <div
            class="modal-backdrop absolute inset-0 bg-black/40"
            onclick="closeUnsavedChangesModal()"
        ></div>


        <div
            class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6"
        >

            <div class="flex items-center gap-3 mb-3">

                <div class="w-10 h-10 bg-yellow-50 rounded-full flex items-center justify-center">

                    <svg
                        class="w-5 h-5 text-yellow-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>

                </div>

                <h3 class="text-lg font-semibold text-black">
                    Unsaved Changes
                </h3>

            </div>


            <p class="text-sm text-gray-600 mb-6">
                You have unsaved changes in this article.
                Save your draft before leaving to make sure
                your changes are not lost.
            </p>


            <div class="flex gap-3 justify-end">

                <button
                    onclick="handleStayButton()"
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                >
                    Stay
                </button>

                <button
                    onclick="handleLeaveAnyway()"
                    class="px-4 py-2.5 text-sm font-medium text-gray-500 border border-gray-200 rounded-lg hover:border-black hover:text-black transition-colors"
                >
                    Leave Anyway
                </button>

                <button
                    onclick="handleSaveDraftFromModal()"
                    class="px-4 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors"
                >
                    Save Draft
                </button>

            </div>

        </div>

    </div>

</body>
</html>