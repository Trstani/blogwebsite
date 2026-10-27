<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>About Page Editor — Blog Dinamika</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        :root {
            --ce-cyan: #35d4e5;
            --ce-cyan-dark: #0891a2;
            --ce-black: #111111;
            --ce-text: #27272a;
            --ce-muted: #71717a;
            --ce-border: #e4e4e7;
            --ce-background: #fafafa;
            --ce-white: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--ce-background);
            color: var(--ce-text);
        }

        button,
        a,
        textarea {
            -webkit-tap-highlight-color: transparent;
        }

        button:focus-visible,
        a:focus-visible {
            outline: 3px solid rgb(53 212 229 / 45%);
            outline-offset: 3px;
        }

        .editor-topbar {
            border-bottom: 1px solid var(--ce-border);
            background: rgb(255 255 255 / 96%);
            backdrop-filter: blur(12px);
        }

        .editor-brand-mark {
            display: inline-flex;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--ce-cyan);
            box-shadow: 0 0 0 4px rgb(53 212 229 / 15%);
        }

        .editor-back-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #52525b;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: color 160ms ease;
        }

        .editor-back-link:hover {
            color: var(--ce-black);
        }

        .editor-save-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            border: 1px solid var(--ce-black);
            border-radius: 7px;
            background: var(--ce-black);
            padding: 10px 18px;
            color: var(--ce-white);
            font-size: 13px;
            font-weight: 600;
            transition:
                background 160ms ease,
                border-color 160ms ease,
                transform 160ms ease;
        }

        .editor-save-button:hover:not(:disabled) {
            border-color: #27272a;
            background: #27272a;
        }

        .editor-save-button:active:not(:disabled) {
            transform: translateY(1px);
        }

        .editor-save-button:disabled {
            cursor: not-allowed;
            opacity: 0.55;
        }

        .editor-preview-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #3f3f46;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: color 160ms ease;
        }

        .editor-preview-link:hover {
            color: var(--ce-cyan-dark);
        }

        .editor-language {
            display: flex;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid #bceff3;
            border-radius: 8px;
            background: #ecfeff;
            color: #155e75;
            font-family: Consolas, Monaco, monospace;
            font-size: 12px;
            font-weight: 700;
        }

        .editor-section {
            margin-bottom: 22px;
            overflow: hidden;
            border: 1px solid var(--ce-border);
            border-radius: 10px;
            background: var(--ce-white);
            box-shadow: 0 1px 3px rgb(0 0 0 / 3%);
            transition: border-color 160ms ease;
        }

        .editor-section:focus-within {
            border-color: #a5eaf0;
        }

        .editor-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid var(--ce-border);
            background: var(--ce-white);
            padding: 20px 24px;
        }

        .editor-section-body {
            padding: 24px;
        }

        .code-editor {
            display: block;
            width: 100%;
            min-height: 360px;
            border: 1px solid var(--ce-border);
            border-radius: 8px;
            background: #ffffff;
            padding: 18px;
            color: #27272a;
            font-family: Consolas, Monaco, 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.8;
            resize: vertical;
            tab-size: 4;
            transition:
                border-color 160ms ease,
                box-shadow 160ms ease;
        }

        .code-editor:focus {
            outline: none;
            border-color: var(--ce-cyan-dark);
            box-shadow: 0 0 0 3px rgb(53 212 229 / 16%);
        }

        .code-editor::placeholder {
            color: #a1a1aa;
        }

        .editor-help {
            margin-top: 12px;
            color: var(--ce-muted);
            font-size: 12px;
            line-height: 1.8;
        }

        .editor-help code {
            border: 1px solid #e4e4e7;
            border-radius: 4px;
            background: #f4f4f5;
            padding: 2px 5px;
            color: #3f3f46;
            font-family: Consolas, Monaco, monospace;
            font-size: 11px;
        }

        .editor-message {
            margin-bottom: 24px;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 13px;
            line-height: 1.7;
        }

        .editor-message-success {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .editor-message-error {
            border-color: #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        .editor-status-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            flex-shrink: 0;
            border-radius: 50%;
            background: currentColor;
        }

        .editor-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            border-top: 1px solid var(--ce-border);
            padding-top: 24px;
        }

        @media (max-width: 640px) {
            .editor-section-header {
                padding: 16px;
            }

            .editor-section-body {
                padding: 16px;
            }

            .code-editor {
                min-height: 280px;
                padding: 14px;
                font-size: 12px;
            }

            .editor-footer {
                align-items: stretch;
                flex-direction: column;
                gap: 16px;
            }

            .editor-footer .editor-save-button {
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50 text-gray-900">

    {{-- Top Navigation --}}
    <nav class="editor-topbar sticky top-0 z-20">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">

            <a
                href="{{ route('admin.dashboard') }}"
                class="editor-back-link"
            >
                <span aria-hidden="true" class="text-lg">&larr;</span>
                <span>Back to Dashboard</span>
            </a>

            <div class="flex items-center gap-3 sm:gap-5">
                <span
                    id="saveStatus"
                    role="status"
                    aria-live="polite"
                    class="flex items-center gap-2 text-xs font-medium text-gray-500 sm:text-sm"
                >
                    <span class="editor-status-dot"></span>
                    <span id="saveStatusText">All changes saved</span>
                </span>

                <button
                    type="submit"
                    id="saveButton"
                    class="editor-save-button"
                >
                    <svg
                        aria-hidden="true"
                        width="15"
                        height="15"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Save Changes
                </button>
            </div>

        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">

        {{-- Page Heading --}}
        <header class="mb-8">
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <span class="editor-brand-mark" aria-hidden="true"></span>

                <span class="text-xs font-semibold uppercase tracking-widest text-gray-500">
                    Website Content
                </span>

                <span class="rounded-full border border-cyan-200 bg-cyan-50 px-2.5 py-1 text-xs font-semibold text-cyan-800">
                    Super Admin
                </span>
            </div>

            <h1 class="text-3xl font-bold tracking-tight text-gray-950 sm:text-4xl">
                About Page Editor
            </h1>

            <p class="mt-3 max-w-3xl text-sm leading-7 text-gray-500 sm:text-base">
                Manage your public About page using HTML, CSS, and JavaScript.
                Your saved changes will be applied to the public page immediately.
            </p>

            <div class="mt-5 flex flex-wrap items-center gap-5">
                <a
                    href="{{ route('about') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="editor-preview-link"
                >
                    <svg
                        aria-hidden="true"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M14 3h7v7"/>
                        <path d="M10 14 21 3"/>
                        <path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/>
                    </svg>
                    Open public About page
                </a>
            </div>
        </header>

        {{-- Save Feedback --}}
        <div
            id="editorMessage"
            class="editor-message hidden"
            role="alert"
            aria-live="assertive"
        ></div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-700">
                <p class="font-semibold">Unable to save your changes.</p>

                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="aboutPageForm">
            @csrf

            {{-- HTML Editor --}}
            <section class="editor-section">
                <div class="editor-section-header">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="editor-language">HTML</span>

                        <div class="min-w-0">
                            <h2 class="font-semibold text-gray-900">
                                HTML Content
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-gray-500">
                                Define the structure and content of your page.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="editor-section-body">
                    <textarea
                        name="html"
                        id="aboutHtml"
                        class="code-editor"
                        spellcheck="false"
                        autocomplete="off"
                        autocapitalize="off"
                        aria-label="About page HTML content"
                        placeholder="<section class=&quot;bd-about&quot;>&#10;    <h1>About Us</h1>&#10;    <p>Write your content here.</p>&#10;</section>"
                    >{{ old('html', $aboutPage?->html ?? '') }}</textarea>

                    <p class="editor-help">
                        Enter the HTML content for your public About page.
                        Keep your page inside a unique root element such as
                        <code>.bd-about</code>.
                    </p>
                </div>
            </section>

            {{-- CSS Editor --}}
            <section class="editor-section">
                <div class="editor-section-header">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="editor-language">CSS</span>

                        <div class="min-w-0">
                            <h2 class="font-semibold text-gray-900">
                                Custom CSS
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-gray-500">
                                Customize colors, spacing, typography, and layout.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="editor-section-body">
                    <textarea
                        name="css"
                        id="aboutCss"
                        class="code-editor"
                        spellcheck="false"
                        autocomplete="off"
                        autocapitalize="off"
                        aria-label="About page custom CSS"
                        placeholder=".bd-about {&#10;    color: #14243a;&#10;} "
                    >{{ old('css', $aboutPage?->css ?? '') }}</textarea>

                    <p class="editor-help">
                        Scope styles to your About page root, for example
                        <code>.bd-about h1</code>. Avoid global selectors that
                        could change the existing navbar or footer.
                    </p>
                </div>
            </section>

            {{-- JavaScript Editor --}}
            <section class="editor-section">
                <div class="editor-section-header">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="editor-language">JS</span>

                        <div class="min-w-0">
                            <h2 class="font-semibold text-gray-900">
                                JavaScript
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-gray-500">
                                Add interactive behavior to your About page.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="editor-section-body">
                    <textarea
                        name="javascript"
                        id="aboutJavascript"
                        class="code-editor"
                        spellcheck="false"
                        autocomplete="off"
                        autocapitalize="off"
                        aria-label="About page JavaScript"
                        placeholder="document.querySelector('.bd-about')?.classList.add('is-ready');"
                    >{{ old('javascript', $aboutPage?->javascript ?? '') }}</textarea>

                    <p class="editor-help">
                        JavaScript saved here executes on the public About page.
                        Only enter code you trust.
                    </p>
                </div>
            </section>

            {{-- Bottom Actions --}}
            <footer class="editor-footer">
                <div>
                    <p class="text-sm font-medium text-gray-700">
                        Changes apply immediately after saving
                    </p>

                    <p class="mt-1 text-xs leading-6 text-gray-500">
                        There is no separate draft or publish step.
                    </p>
                </div>

                <button
                    type="submit"
                    id="bottomSaveButton"
                    class="editor-save-button"
                >
                    Save Changes
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </footer>

        </form>

    </main>

    <script>
        (() => {
            const form = document.getElementById('aboutPageForm');
            const saveButton = document.getElementById('saveButton');
            const bottomSaveButton = document.getElementById('bottomSaveButton');
            const saveStatus = document.getElementById('saveStatus');
            const saveStatusText = document.getElementById('saveStatusText');
            const messageBox = document.getElementById('editorMessage');

            const fields = [
                document.getElementById('aboutHtml'),
                document.getElementById('aboutCss'),
                document.getElementById('aboutJavascript'),
            ];

            let isDirty = false;
            let isSaving = false;

            function setStatus(message, color = 'text-gray-500') {
                saveStatusText.textContent = message;

                saveStatus.className =
                    `flex items-center gap-2 text-xs font-medium sm:text-sm ${color}`;
            }

            function showMessage(message, type = 'success') {
                messageBox.textContent = message;
                messageBox.className = 'editor-message';

                messageBox.classList.add(
                    type === 'success'
                        ? 'editor-message-success'
                        : 'editor-message-error'
                );
            }

            function clearMessage() {
                messageBox.textContent = '';
                messageBox.className = 'editor-message hidden';
            }

            function markDirty() {
                isDirty = true;

                setStatus(
                    'Unsaved changes',
                    'text-amber-600'
                );

                clearMessage();
            }

            fields.forEach((field) => {
                field.addEventListener('input', markDirty);
            });

            async function saveChanges() {
                if (isSaving || !isDirty) {
                    if (!isSaving && !isDirty) {
                        setStatus('All changes saved', 'text-green-700');
                    }

                    return;
                }

                isSaving = true;
                clearMessage();

                setStatus('Saving...', 'text-cyan-700');

                [saveButton, bottomSaveButton].forEach((button) => {
                    button.disabled = true;
                });

                try {
                    const response = await fetch(
                        @json(route('admin.about-page.update')),
                        {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                            },
                            body: JSON.stringify({
                                html: fields[0].value,
                                css: fields[1].value,
                                javascript: fields[2].value,
                            }),
                        }
                    );

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            const validationMessages = Object.values(data.errors)
                                .flat()
                                .join(' ');

                            throw new Error(
                                validationMessages || 'Please check your input.'
                            );
                        }

                        if (response.status === 403) {
                            throw new Error(
                                'You do not have permission to edit this page.'
                            );
                        }

                        if (response.status === 419) {
                            throw new Error(
                                'Your session has expired. Refresh the page and try again.'
                            );
                        }

                        throw new Error(
                            data.message || 'Failed to save About page.'
                        );
                    }

                    isDirty = false;

                    setStatus(
                        'All changes saved',
                        'text-green-700'
                    );

                    showMessage(
                        data.message || 'About page saved successfully.'
                    );

                } catch (error) {
                    console.error('About Page save error:', error);

                    setStatus('Save failed', 'text-red-600');

                    showMessage(
                        error.message ||
                        'An unexpected error occurred while saving.',
                        'error'
                    );
                } finally {
                    isSaving = false;

                    [saveButton, bottomSaveButton].forEach((button) => {
                        button.disabled = false;
                    });
                }
            }

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                saveChanges();
            });

            saveButton.addEventListener('click', saveChanges);

            window.addEventListener('beforeunload', (event) => {
                if (isDirty && !isSaving) {
                    event.preventDefault();
                    event.returnValue = '';
                }
            });
        })();
    </script>

</body>
</html>
