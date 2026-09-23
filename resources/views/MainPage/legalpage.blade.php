<x-layouts.app :title="$legalPage->title">

    <div class="min-h-screen bg-white text-black">

        {{-- Hero --}}
        <section class="relative overflow-hidden border-b border-black/10">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -right-32 -top-32 h-80 w-80 rounded-full bg-[#54DCE3]/20 blur-3xl"></div>
                <div class="absolute left-0 bottom-0 h-40 w-40 rounded-full bg-black/[0.03] blur-2xl"></div>
            </div>

            <div class="relative mx-auto max-w-6xl px-6 py-20 sm:px-8 lg:px-12 lg:py-28">

                <div class="mb-8 flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em]">
                    <span class="text-[#18AAB3]">Legal Document</span>
                    <span class="h-px w-8 bg-black/20"></span>
                    <span class="text-black/40">
                        {{ $legalPage->type === 'privacy_policy' ? 'Privacy' : 'Legal' }}
                    </span>
                </div>

                <div class="grid gap-10 lg:grid-cols-[1fr_280px] lg:items-end">

                    <div>
                        <h1 class="max-w-4xl text-5xl font-black tracking-[-0.04em] sm:text-6xl lg:text-7xl">
                            {{ $legalPage->title }}
                        </h1>

                        <p class="mt-7 max-w-2xl text-base leading-7 text-black/60 sm:text-lg">
                            Please read this document carefully to understand the policies,
                            terms, and information governing your use of Create Eve.
                        </p>
                    </div>

                    <div class="border-l-2 border-[#54DCE3] pl-5">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-black/40">
                            Last updated
                        </p>

                        <p class="mt-2 text-sm font-semibold">
                            {{ optional($legalPage->updated_at)->format('F j, Y') }}
                        </p>

                        <p class="mt-5 text-xs leading-5 text-black/40">
                            This page contains the latest version of this document
                            published by Create Eve.
                        </p>
                    </div>

                </div>
            </div>
        </section>


        {{-- Document --}}
        <main class="mx-auto max-w-6xl px-6 py-14 sm:px-8 lg:px-12 lg:py-20">

            <div class="grid gap-12 lg:grid-cols-[220px_minmax(0,760px)] lg:gap-16">

                {{-- Side label --}}
                <aside class="hidden lg:block">
                    <div class="sticky top-10">
                        <div class="border-t-2 border-black pt-4">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em]">
                                Create Eve
                            </p>

                            <p class="mt-2 text-xs leading-5 text-black/40">
                                Official legal documentation.
                            </p>
                        </div>

                        <div class="mt-10 space-y-3 text-xs text-black/40">
                            <div class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#54DCE3]"></span>
                                <span>Current version</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-black/20"></span>
                                <span>Public document</span>
                            </div>
                        </div>
                    </div>
                </aside>


                {{-- Dynamic content --}}
                <article
                    class="
                        legal-content
                        max-w-none
                        text-[15px]
                        leading-7
                        text-black/75
                        sm:text-base
                    "
                >
                    {!! $legalPage->content !!}
                </article>

            </div>

        </main>


        {{-- Footer note --}}
        <section class="border-t border-black/10 bg-black text-white">
            <div class="mx-auto max-w-6xl px-6 py-12 sm:px-8 lg:px-12">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#54DCE3]">
                            Questions?
                        </p>

                        <p class="mt-2 text-sm text-white/50">
                            If you have questions regarding this document, please contact us.
                        </p>
                    </div>

                    <a
                        href="mailto:admin@dinamikapublika.id"
                        class="inline-flex w-fit items-center gap-2 border border-white/20 px-5 py-3 text-xs font-bold uppercase tracking-wider transition hover:border-[#54DCE3] hover:text-[#54DCE3]"
                    >
                        Contact us
                        <span aria-hidden="true">↗</span>
                    </a>

                </div>
            </div>
        </section>

    </div>


    {{-- Legal document typography --}}
    <style>
        .legal-content h1,
        .legal-content h2,
        .legal-content h3,
        .legal-content h4 {
            color: #000;
            font-weight: 800;
            letter-spacing: -0.025em;
            line-height: 1.2;
        }

        .legal-content h1 {
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        .legal-content h2 {
            margin-top: 3rem;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .legal-content h3 {
            margin-top: 2rem;
            margin-bottom: .75rem;
            font-size: 1.2rem;
        }

        .legal-content p {
            margin-bottom: 1.1rem;
        }

        .legal-content ul,
        .legal-content ol {
            margin: 1rem 0 1.5rem;
            padding-left: 1.5rem;
        }

        .legal-content ul {
            list-style-type: disc;
        }

        .legal-content ol {
            list-style-type: decimal;
        }

        .legal-content li {
            margin-bottom: .45rem;
            padding-left: .2rem;
        }

        .legal-content strong {
            color: #000;
            font-weight: 700;
        }

        .legal-content a {
            color: #159AA3;
            text-decoration: underline;
            text-decoration-thickness: 1px;
            text-underline-offset: 3px;
        }

        .legal-content a:hover {
            color: #000;
        }

        .legal-content blockquote {
            margin: 2rem 0;
            border-left: 3px solid #54DCE3;
            padding: .5rem 0 .5rem 1.25rem;
            color: rgba(0, 0, 0, .55);
        }

        @media (max-width: 640px) {
            .legal-content h2 {
                margin-top: 2.25rem;
                font-size: 1.3rem;
            }
        }
    </style>

</x-layouts.app>