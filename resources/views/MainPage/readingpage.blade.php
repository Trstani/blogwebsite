<x-layouts.app title="{{ $article->title }}">
    <style>
        /* ========================================
           Rich Article Content — Editorial + Responsive
           ======================================== */

        .rich-article-content {
            color: #27272a;              /* zinc-800 */
            font-size: 1rem;             /* 16px mobile */
            line-height: 1.75;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        @media (min-width: 768px) {
            .rich-article-content {
                font-size: 1.0625rem;    /* 17px desktop */
                line-height: 1.8;
            }
        }

        /* Paragraph */
        .rich-article-content p {
            margin-top: 0;
            margin-bottom: 1.25rem;
        }

        /* Headings */
        .rich-article-content h2 {
            font-size: 1.375rem;
            line-height: 1.3;
            font-weight: 700;
            color: #18181b;
            margin-top: 2rem;
            margin-bottom: 0.875rem;
            letter-spacing: -0.01em;
        }

        .rich-article-content h3 {
            font-size: 1.125rem;
            line-height: 1.4;
            font-weight: 700;
            color: #18181b;
            margin-top: 1.5rem;
            margin-bottom: 0.625rem;
            letter-spacing: -0.01em;
        }

        @media (min-width: 768px) {
            .rich-article-content h2 {
                font-size: 1.625rem;
                margin-top: 2.5rem;
                margin-bottom: 1rem;
            }
            .rich-article-content h3 {
                font-size: 1.25rem;
                margin-top: 1.75rem;
            }
        }

        /* Bold / Italic / Underline / Strike */
        .rich-article-content strong,
        .rich-article-content b {
            font-weight: 700;
            color: #111827;
        }

        .rich-article-content em,
        .rich-article-content i { font-style: italic; }

        .rich-article-content u {
            text-decoration: underline;
            text-decoration-thickness: 1px;
            text-underline-offset: 2px;
        }

        .rich-article-content s,
        .rich-article-content strike { text-decoration: line-through; }

        /* Links */
        .rich-article-content a {
            color: #0891b2;              /* cyan-600 */
            text-decoration: underline;
            text-decoration-thickness: 1px;
            text-underline-offset: 2px;
            transition: color 0.2s ease;
            overflow-wrap: anywhere;
        }

        .rich-article-content a:hover { color: #0e7490; }

        /* Lists */
        .rich-article-content ol,
        .rich-article-content ul {
            padding-left: 1.5rem;
            margin-top: 1rem;
            margin-bottom: 1.25rem;
        }

        .rich-article-content ol > li[data-list="bullet"]  { list-style-type: disc !important; }
        .rich-article-content ol > li[data-list="ordered"] { list-style-type: decimal !important; }
        .rich-article-content ul                           { list-style-type: disc !important; }
        .rich-article-content ol                           { list-style-type: decimal; }

        .rich-article-content li {
            padding-left: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .rich-article-content .ql-ui { display: none; }

        /* Blockquote */
        .rich-article-content blockquote {
            margin: 1.5rem 0;
            padding: 0.875rem 1.25rem;
            border-left: 3px solid #06b6d4;   /* cyan-500 accent */
            color: #52525b;
            font-style: italic;
            background-color: #fafafa;
            border-radius: 0 0.5rem 0.5rem 0;
        }

        @media (min-width: 768px) {
            .rich-article-content blockquote { padding: 1rem 1.5rem; }
        }

        /* Code */
        .rich-article-content .ql-syntax {
            display: block;
            padding: 1rem;
            margin: 1.25rem 0;
            overflow-x: auto;
            background: #18181b;
            color: #fafafa;
            border-radius: 0.5rem;
            font-family: ui-monospace, SFMono-Regular, monospace;
            font-size: 0.8125rem;
            line-height: 1.6;
        }

        @media (min-width: 768px) {
            .rich-article-content .ql-syntax { font-size: 0.875rem; }
        }

        /* Images inside rich content — never overflow */
        .rich-article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
        }

        /* Tables — scrollable on mobile */
        .rich-article-content table {
            width: 100%;
            display: block;
            overflow-x: auto;
            border-collapse: collapse;
        }

        /* ========================================
           Like / Bookmark — active icon fill
           (JS toggles text/bg/border classes, we use
            those to also fill the icon)
           ======================================== */
        .likeBookmarkBtn[data-action="like"].text-blue-700 svg,
        .likeBookmarkBtn[data-action="like"].bg-blue-50 svg {
            fill: currentColor;
        }

        .likeBookmarkBtn[data-action="bookmark"].text-yellow-700 svg,
        .likeBookmarkBtn[data-action="bookmark"].bg-yellow-50 svg {
            fill: currentColor;
        }
                /* ========================================
           Share Menu
           ======================================== */
        .share-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            border-radius: 0.5rem;
            padding: 0.625rem 0.75rem;
            font-size: 0.875rem;
            color: #3f3f46;
            text-align: left;
            transition: background-color 0.15s ease;
        }

        .share-option:hover {
            background-color: #fafafa;
        }

        .share-option svg {
            flex-shrink: 0;
            width: 1rem;
            height: 1rem;
        }

        /* Share menu animation — desktop */
        @keyframes shareFadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Share menu animation — mobile bottom sheet */
        @keyframes shareSlideUp {
            from { opacity: 0; transform: translateY(100%); }
            to   { opacity: 1; transform: translateY(0); }
        }

        #shareMenu:not(.hidden) {
            animation: shareFadeIn 0.15s ease-out;
        }

        @media (max-width: 639px) {
            #shareMenu:not(.hidden) {
                animation: shareSlideUp 0.2s ease-out;
            }
        }

        /* Share toast */
        @keyframes toastIn {
            from { opacity: 0; transform: translate(-50%, 8px); }
            to   { opacity: 1; transform: translate(-50%, 0); }
        }

        .share-toast {
            animation: toastIn 0.2s ease-out;
        }
    </style>

    {{-- =========================================================
         ARTICLE HEADER
         ========================================================= --}}
    <article class="mx-auto max-w-5xl px-5 pt-8 pb-6 sm:px-6 sm:pt-12 sm:pb-8 md:pt-16">

        {{-- Category --}}
        @if($article->category)
            <a href="/explore?category={{ $article->category->slug }}"
               class="inline-flex items-center gap-1.5 rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.08em] text-cyan-700 transition-colors hover:bg-cyan-100">
                {{ $article->category->name }}
            </a>
        @endif

        {{-- Title --}}
        <h1 class="mt-4 text-[1.75rem] font-bold leading-[1.15] tracking-tight text-zinc-900
                   sm:text-4xl sm:leading-[1.15]
                   md:text-[2.75rem] md:leading-[1.1]">
            {{ $article->title }}
        </h1>

        {{-- Description --}}
        @if($article->description)
            <p class="mt-3 text-base leading-relaxed text-zinc-500
                      sm:mt-4 sm:text-lg
                      md:text-xl md:leading-relaxed">
                {{ $article->description }}
            </p>
        @endif

        {{-- Meta — Author / Date / Views --}}
        <div class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-zinc-500 sm:mt-7">

            {{-- Author --}}
            <div class="flex min-w-0 items-center gap-2.5">
                <div class="h-8 w-8 shrink-0 overflow-hidden rounded-full bg-zinc-100">
                    @if($article->author->avatar ?? false)
                        <img src="{{ imageUrl($article->author->avatar) }}"
                             alt="{{ $article->author->name }}"
                             class="h-full w-full object-cover" />
                    @else
                        <div class="flex h-full w-full items-center justify-center">
                            <span class="text-xs font-bold text-zinc-400">
                                {{ strtoupper(substr($article->author->name ?? 'A', 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>

                <a href="/profile/{{ $article->author->slug ?? '#' }}"
                   class="truncate text-sm font-semibold text-zinc-900 transition-colors hover:text-cyan-700">
                    {{ $article->author->name ?? 'Unknown' }}
                </a>
            </div>

            {{-- Separator --}}
            <span class="hidden text-zinc-300 sm:inline">·</span>

            {{-- Date --}}
            <span class="whitespace-nowrap text-sm text-zinc-500">
                {{ fmtDate($article->published_at) ?: $article->created_at->format('M d, Y') }}
            </span>

            {{-- Separator --}}
            <span class="text-zinc-300">·</span>

            {{-- Views --}}
            <span class="inline-flex items-center gap-1 whitespace-nowrap text-sm text-zinc-500">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $article->views }} views
            </span>
        </div>

        {{-- Tags --}}
        @if($article->tags && $article->tags->count() > 0)
            <div class="mt-6 flex flex-wrap gap-2">
                @foreach($article->tags as $tag)
                    <a href="{{ route('explore', ['tag' => $tag->slug]) }}"
                       class="inline-flex items-center rounded-full bg-zinc-100 px-3 py-1.5 text-[11px] font-medium text-zinc-600 transition-colors hover:bg-cyan-100 hover:text-cyan-800">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

               {{-- Like / Bookmark / Share --}}
        <div class="mt-6 flex flex-wrap items-center gap-2.5 sm:mt-8">

            {{-- Like --}}
            <button
                id="likeBtn"
                type="button"
                class="likeBookmarkBtn inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 active:scale-[0.98] disabled:opacity-60"
                aria-label="Like this article"
                title="Like this article"
                data-action="like">
                <svg class="h-[18px] w-[18px] transition-transform duration-200"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                </svg>
                <span class="likeText">Like</span>
                <span class="likeCount text-xs font-semibold tabular-nums text-gray-500"></span>
            </button>

            {{-- Bookmark --}}
            <button
                id="bookmarkBtn"
                type="button"
                class="likeBookmarkBtn inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 active:scale-[0.98] disabled:opacity-60"
                aria-label="Bookmark this article"
                title="Bookmark this article"
                data-action="bookmark">
                <svg class="h-[18px] w-[18px]"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                </svg>
                <span class="bookmarkText">Bookmark</span>
            </button>

            {{-- Share — wrapper relative untuk positioning dropdown --}}
            <div class="relative">

                <button
                    id="shareBtn"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 active:scale-[0.98]"
                    aria-label="Share this article"
                    aria-haspopup="menu"
                    aria-expanded="false"
                    title="Share this article">
                    {{-- Share icon --}}
                    <svg class="h-[18px] w-[18px]"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/>
                    </svg>
                    <span>Share</span>
                </button>

                {{-- Backdrop — mobile only --}}
                <div
                    id="shareBackdrop"
                    class="hidden fixed inset-0 z-40 bg-black/40 sm:hidden"
                    onclick="closeShareMenu()">
                </div>

                {{-- Dropdown / bottom sheet --}}
                <div
                    id="shareMenu"
                    role="menu"
                    class="hidden fixed inset-x-0 bottom-0 z-50 sm:absolute sm:inset-x-auto sm:bottom-auto sm:right-0 sm:top-full sm:mt-2 sm:w-64">

                    <div class="rounded-t-2xl border-t border-zinc-200 bg-white p-4 pb-6 shadow-[0_-8px_30px_-10px_rgba(0,0,0,0.15)] sm:rounded-xl sm:border sm:p-2 sm:pb-2 sm:shadow-xl">

                        {{-- Mobile header --}}
                        <div class="mb-3 flex items-center justify-between sm:hidden">
                            <h3 class="text-sm font-semibold text-zinc-900">Share this article</h3>
                            <button type="button" onclick="closeShareMenu()"
                                    class="rounded-full p-1.5 text-zinc-400 transition-colors hover:bg-zinc-100 hover:text-zinc-600"
                                    aria-label="Close share menu">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Desktop header --}}
                        <p class="mb-1 hidden px-3 pt-1.5 text-[10px] font-semibold uppercase tracking-[0.14em] text-zinc-400 sm:block">
                            Share
                        </p>

                        <div class="space-y-0.5">

                            {{-- WhatsApp --}}
                            <a id="shareWhatsApp"
                               href="#"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="share-option"
                               role="menuitem">
                                <svg viewBox="0 0 24 24" fill="currentColor" class="text-[#25D366]">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                <span>WhatsApp</span>
                            </a>

                            {{-- X --}}
                            <a id="shareX"
                               href="#"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="share-option"
                               role="menuitem">
                                <svg viewBox="0 0 24 24" fill="currentColor" class="text-zinc-900">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                                <span>X</span>
                            </a>

                            {{-- Facebook --}}
                            <a id="shareFacebook"
                               href="#"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="share-option"
                               role="menuitem">
                                <svg viewBox="0 0 24 24" fill="currentColor" class="text-[#1877F2]">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span>Facebook</span>
                            </a>

                            {{-- Copy Link --}}
                            <button type="button"
                                    onclick="copyShareLink()"
                                    class="share-option"
                                    role="menuitem">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-zinc-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                                </svg>
                                <span>Copy Link</span>
                            </button>

                            {{-- Native Share (only if supported) --}}
                            <button type="button"
                                    id="nativeShareOption"
                                    onclick="nativeShare()"
                                    class="share-option hidden"
                                    role="menuitem">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-cyan-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25"/>
                                </svg>
                                <span>Share via...</span>
                            </button>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Loading (JS toggles visibility) --}}
            <div id="likeBookmarkLoading" class="hidden ml-1">
                <svg class="h-5 w-5 animate-spin text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>

        {{-- Cover Image --}}
        @if($article->cover_image)
            <figure class="mt-8 sm:mt-10">
                <div class="relative w-full overflow-hidden rounded-lg bg-zinc-100
                            aspect-[16/10] sm:aspect-[16/9] md:aspect-[2/1]">
                    <img src="{{ imageUrl($article->cover_image) }}"
                         alt="{{ $article->title }}"
                         class="absolute inset-0 h-full w-full object-cover object-center" />
                </div>
            </figure>
        @endif

    </article>


    {{-- =========================================================
         ARTICLE CONTENT
         ========================================================= --}}
    <div class="mx-auto max-w-5xl px-5 pb-4 sm:px-6 sm:pb-6">
        @forelse($article->sections->sortBy('order') as $section)

            @if($section->type === 'text')
                <div class="rich-article-content mb-7 sm:mb-8">
                    {!! $section->content !!}
                </div>

            @elseif($section->type === 'image')
                <figure class="mb-7 sm:mb-8">
                    <div class="relative w-full overflow-hidden rounded-lg bg-zinc-100
                                aspect-[16/9] sm:aspect-[2/1]">
                        <img src="{{ imageUrl($section->content) }}"
                             class="absolute inset-0 h-full w-full object-cover object-center"
                             alt="Gambar Artikel" />
                    </div>
                </figure>

            @elseif($section->type === 'video')
                @php
                    $videoHelper = app(\App\Services\VideoUrlHelper::class);
                    $embedHtml = $videoHelper->generateEmbedHtml($section->content);
                @endphp

                @if($embedHtml)
                    <div class="mb-7 sm:mb-8">
                        <div class="overflow-hidden rounded-lg bg-black">
                            {!! $embedHtml !!}
                        </div>
                    </div>
                @else
                    <div class="mb-7 rounded-lg border border-red-200 bg-red-50 p-4 sm:mb-8">
                        <p class="text-sm text-red-600">Invalid or unsupported video URL.</p>
                    </div>
                @endif

            @elseif($section->type === 'gif')
                <figure class="mb-7 sm:mb-8">
                    <div class="relative w-full overflow-hidden rounded-lg">
                        <img src="{{ imageUrl($section->content) }}"
                             class="h-auto w-full object-contain"
                             alt="GIF Animation" />
                    </div>
                </figure>
            @endif

        @empty
            <p class="py-8 text-center text-zinc-400">No content yet.</p>
        @endforelse
    </div>


    {{-- =========================================================
         BACK LINK
         ========================================================= --}}
    <div class="mx-auto max-w-5xl px-5 pb-12 sm:px-6 sm:pb-16">
        <a href="/explore"
           class="inline-flex items-center text-sm font-medium text-zinc-400 transition-colors hover:text-cyan-700">
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Explore
        </a>
    </div>


    {{-- =========================================================
         COMMENTS
         ========================================================= --}}
    @if($article->status === 'published')
        <div class="border-t border-zinc-100">
            <div class="mx-auto max-w-5xl px-5 py-10 sm:px-6 sm:py-12">

                <h2 id="commentHeading" class="text-xl font-bold text-zinc-900 sm:text-2xl">
                    Comments ({{ $article->comments->count() }})
                </h2>

                {{-- Comment Form --}}
                @if(auth()->check())
                    <div class="mt-6 rounded-xl border border-zinc-100 bg-zinc-50/60 p-4 sm:mt-8 sm:p-6">
                        <h3 class="mb-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-zinc-500">
                            Leave a Comment
                        </h3>
                        <form id="commentForm" class="space-y-3">
                            @csrf
                            <textarea
                                id="commentContent"
                                name="content"
                                rows="4"
                                placeholder="Share your thoughts..."
                                maxlength="1000"
                                class="w-full resize-none rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-800 placeholder:text-zinc-400 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100"
                                required></textarea>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-zinc-400"><span id="charCount">0</span>/1000</span>
                                <button type="submit"
                                        class="rounded-full bg-zinc-900 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-cyan-600">
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="mt-6 rounded-xl border border-zinc-100 bg-zinc-50/60 p-5 text-center sm:mt-8">
                        <p class="text-sm text-zinc-600">
                            <a href="{{ route('auth') }}" class="font-semibold text-cyan-700 hover:text-cyan-800 transition-colors">Log in</a>
                            to leave a comment.
                        </p>
                    </div>
                @endif

                {{-- Comments List --}}
                <div class="mt-8 space-y-6 sm:mt-10" id="commentsList">
                    @forelse($article->comments->where('parent_id', null)->sortByDesc('created_at') as $comment)
                        <div class="border-b border-zinc-100 pb-6 last:border-0">
                            <div class="flex items-start gap-3 sm:gap-4">

                                {{-- Avatar --}}
                                <div class="shrink-0">
                                    @if($comment->user->avatar ?? false)
                                        <img src="{{ imageUrl($comment->user->avatar) }}"
                                             alt="{{ $comment->user->name }}"
                                             class="h-9 w-9 rounded-full object-cover sm:h-10 sm:w-10" />
                                    @else
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-zinc-100 sm:h-10 sm:w-10">
                                            <span class="text-xs font-bold text-zinc-400">
                                                {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                        <a href="/profile/{{ $comment->user->slug ?? '#' }}"
                                           class="text-sm font-semibold text-zinc-900 transition-colors hover:text-cyan-700">
                                            {{ $comment->user->name }}
                                        </a>
                                        <span class="text-xs text-zinc-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>

                                    <p class="mt-1.5 text-sm leading-relaxed text-zinc-700 break-words">
                                        {{ $comment->content }}
                                    </p>

                                    {{-- Reply Button --}}
                                    @if(auth()->check())
                                        <button onclick="toggleReplyForm({{ $comment->id }})"
                                                class="mt-2 text-xs font-semibold text-zinc-500 transition-colors hover:text-cyan-700">
                                            Reply
                                        </button>
                                    @endif

                                    {{-- Reply Form --}}
                                    @if(auth()->check())
                                        <div id="replyForm_{{ $comment->id }}" class="mt-3 hidden border-t border-zinc-100 pt-3">
                                            <form class="replyForm" data-comment-id="{{ $comment->id }}">
                                                <textarea
                                                    class="replyContent w-full resize-none rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm placeholder:text-zinc-400 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100"
                                                    rows="3"
                                                    placeholder="Reply to {{ $comment->user->name }}..."
                                                    maxlength="1000"
                                                    required></textarea>
                                                <div class="mt-2 flex items-center justify-between">
                                                    <button type="button" onclick="toggleReplyForm({{ $comment->id }})"
                                                            class="text-xs text-zinc-500 hover:text-zinc-900">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                            class="rounded-full bg-zinc-900 px-3.5 py-1.5 text-xs font-medium text-white transition-colors hover:bg-cyan-600">
                                                        Reply
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                    {{-- Replies --}}
                                    @if($comment->replies && $comment->replies->count() > 0)
                                        <div class="mt-4 space-y-4 border-l-2 border-zinc-100 pl-4 ml-1 sm:pl-5">
                                            @foreach($comment->replies->sortBy('created_at') as $reply)
                                                <div class="flex items-start gap-3">
                                                    <div class="shrink-0">
                                                        @if($reply->user->avatar ?? false)
                                                            <img src="{{ imageUrl($reply->user->avatar) }}"
                                                                 alt="{{ $reply->user->name }}"
                                                                 class="h-7 w-7 rounded-full object-cover sm:h-8 sm:w-8" />
                                                        @else
                                                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-zinc-100 sm:h-8 sm:w-8">
                                                                <span class="text-[10px] font-bold text-zinc-400">
                                                                    {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                                            <a href="/profile/{{ $reply->user->slug ?? '#' }}"
                                                               class="text-sm font-semibold text-zinc-900 transition-colors hover:text-cyan-700">
                                                                {{ $reply->user->name }}
                                                            </a>
                                                            <span class="text-xs text-zinc-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <p class="mt-1 text-sm leading-relaxed text-zinc-700 break-words">
                                                            {{ $reply->content }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-zinc-400">
                            No comments yet. Be the first to share your thoughts!
                        </p>
                    @endforelse
                </div>

            </div>
        </div>
    @endif


    {{-- =========================================================
         AUTHOR CARD
         ========================================================= --}}
    <div class="border-t border-zinc-100">
        <div class="mx-auto max-w-5xl px-5 py-10 sm:px-6 sm:py-12">
            <div class="flex flex-col items-center text-center">

                <a href="/profile/{{ $article->author->slug ?? '#' }}" class="shrink-0">
                    @if($article->author->avatar ?? false)
                        <img src="{{ imageUrl($article->author->avatar) }}"
                             alt="{{ $article->author->name }}"
                             class="h-14 w-14 rounded-full object-cover sm:h-16 sm:w-16" />
                    @else
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-zinc-100 sm:h-16 sm:w-16">
                            <span class="text-lg font-bold text-zinc-400 sm:text-xl">
                                {{ strtoupper(substr($article->author->name ?? 'A', 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </a>

                <p class="mt-3 text-[10px] font-semibold uppercase tracking-[0.16em] text-cyan-600">
                    Written by
                </p>

                <a href="/profile/{{ $article->author->slug ?? '#' }}"
                   class="mt-1 text-base font-semibold text-zinc-900 transition-colors hover:text-cyan-700">
                    {{ $article->author->name ?? 'Unknown' }}
                </a>

                @if($article->author->bio ?? false)
                    <p class="mt-2 max-w-md text-sm leading-relaxed text-zinc-500">
                        {{ $article->author->bio }}
                    </p>
                @endif
            </div>
        </div>
    </div>


    {{-- =========================================================
         SCRIPTS — tidak diubah
         ========================================================= --}}
    @if($article->status === 'published')
        <script>
            // ========================================
            // Like & Bookmark Functionality
            // ========================================

            const articleId = {{ $article->id }};
            const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
            const authRoute = "{{ route('auth') }}";
            const currentArticleUrl = window.location.pathname;

            let likeBookmarkState = {
                liked: false,
                bookmarked: false,
                likeCount: 0,
                bookmarkCount: 0,
                isLoading: false
            };

            async function initializeLikeBookmarkStatus() {
                if (!isAuthenticated) return;

                try {
                    const response = await fetch(`/articles/${articleId}/like-bookmark-status`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    });

                    if (!response.ok) throw new Error('Failed to load status');

                    const data = await response.json();
                    if (data.success) {
                        likeBookmarkState = {
                            liked: data.liked,
                            bookmarked: data.bookmarked,
                            likeCount: data.likeCount,
                            bookmarkCount: data.bookmarkCount,
                            isLoading: false
                        };
                        updateUIState();
                    }
                } catch (error) {
                    console.error('Error loading like/bookmark status:', error);
                }
            }

            function updateUIState() {
                const likeBtn = document.getElementById('likeBtn');
                const bookmarkBtn = document.getElementById('bookmarkBtn');
                const likeCount = document.querySelector('.likeCount');

                if (likeBookmarkState.liked) {
                    likeBtn.classList.add('bg-blue-50', 'border-blue-300', 'text-blue-700');
                    likeBtn.classList.remove('border-gray-300', 'text-gray-700', 'hover:bg-gray-50');
                } else {
                    likeBtn.classList.remove('bg-blue-50', 'border-blue-300', 'text-blue-700');
                    likeBtn.classList.add('border-gray-300', 'text-gray-700', 'hover:bg-gray-50');
                }

                if (likeBookmarkState.bookmarked) {
                    bookmarkBtn.classList.add('bg-yellow-50', 'border-yellow-300', 'text-yellow-700');
                    bookmarkBtn.classList.remove('border-gray-300', 'text-gray-700', 'hover:bg-gray-50');
                } else {
                    bookmarkBtn.classList.remove('bg-yellow-50', 'border-yellow-300', 'text-yellow-700');
                    bookmarkBtn.classList.add('border-gray-300', 'text-gray-700', 'hover:bg-gray-50');
                }

                if (likeCount) {
                    likeCount.textContent = likeBookmarkState.likeCount > 0 ? likeBookmarkState.likeCount : '';
                }
            }

            async function handleLikeBookmarkToggle(action) {
                if (!isAuthenticated) {
                    window.location.href = authRoute;
                    return;
                }

                if (likeBookmarkState.isLoading) return;

                const previousState = { ...likeBookmarkState };
                likeBookmarkState.isLoading = true;
                showLoadingState();

                try {
                    const endpoint = action === 'like'
                        ? `/articles/${articleId}/like`
                        : `/articles/${articleId}/bookmark`;

                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    });

                    if (!response.ok) throw new Error('Request failed');

                    const data = await response.json();
                    if (data.success) {
                        if (action === 'like') {
                            likeBookmarkState.liked = data.liked;
                            likeBookmarkState.likeCount = data.count;
                        } else {
                            likeBookmarkState.bookmarked = data.bookmarked;
                            likeBookmarkState.bookmarkCount = data.count;
                        }
                        updateUIState();
                    } else {
                        throw new Error(data.message || 'Toggle failed');
                    }
                } catch (error) {
                    console.error(`Error toggling ${action}:`, error);
                    likeBookmarkState = previousState;
                    updateUIState();
                    showErrorMessage(`Failed to ${action} article`);
                } finally {
                    likeBookmarkState.isLoading = false;
                    hideLoadingState();
                }
            }

            function showLoadingState() {
                const loading = document.getElementById('likeBookmarkLoading');
                if (loading) loading.classList.remove('hidden');
                document.querySelectorAll('.likeBookmarkBtn').forEach(btn => {
                    btn.disabled = true;
                    btn.classList.add('opacity-60');
                });
            }

            function hideLoadingState() {
                const loading = document.getElementById('likeBookmarkLoading');
                if (loading) loading.classList.add('hidden');
                document.querySelectorAll('.likeBookmarkBtn').forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-60');
                });
            }

            function showErrorMessage(message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'fixed bottom-4 right-4 z-50 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm max-w-sm shadow-lg';
                errorDiv.textContent = message;
                document.body.appendChild(errorDiv);
                setTimeout(() => errorDiv.remove(), 3000);
            }

            document.getElementById('likeBtn')?.addEventListener('click', () => {
                handleLikeBookmarkToggle('like');
            });

            document.getElementById('bookmarkBtn')?.addEventListener('click', () => {
                handleLikeBookmarkToggle('bookmark');
            });

            document.addEventListener('DOMContentLoaded', () => {
                initializeLikeBookmarkStatus();
            });

                    // ========================================
        // Share Functionality
        // (Additive — tidak menyentuh logic Like/Bookmark)
        // ========================================
        (function() {
            const shareBtn      = document.getElementById('shareBtn');
            const shareMenu     = document.getElementById('shareMenu');
            const shareBackdrop = document.getElementById('shareBackdrop');
            const nativeOption  = document.getElementById('nativeShareOption');
            const waLink        = document.getElementById('shareWhatsApp');
            const xLink         = document.getElementById('shareX');
            const fbLink        = document.getElementById('shareFacebook');

            if (!shareBtn || !shareMenu) return;

            const shareUrl   = window.location.href;
            const shareTitle = document.title;

            // Build intent URLs
            if (waLink) waLink.href =
                'https://wa.me/?text=' + encodeURIComponent(shareTitle + ' — ' + shareUrl);
            if (xLink) xLink.href =
                'https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareTitle) +
                '&url=' + encodeURIComponent(shareUrl);
            if (fbLink) fbLink.href =
                'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(shareUrl);

            // Show native share option if supported
            if (nativeOption && typeof navigator.share === 'function') {
                nativeOption.classList.remove('hidden');
            }

            // ---- Open / Close ----
            function openShareMenu() {
                shareMenu.classList.remove('hidden');
                if (shareBackdrop) shareBackdrop.classList.remove('hidden');
                shareBtn.setAttribute('aria-expanded', 'true');
            }

            function closeShareMenu() {
                shareMenu.classList.add('hidden');
                if (shareBackdrop) shareBackdrop.classList.add('hidden');
                shareBtn.setAttribute('aria-expanded', 'false');
            }

            // Expose globally for inline onclick
            window.closeShareMenu = closeShareMenu;

            shareBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (shareMenu.classList.contains('hidden')) {
                    openShareMenu();
                } else {
                    closeShareMenu();
                }
            });

            // Close on outside click (desktop)
            document.addEventListener('click', function(e) {
                if (shareMenu.classList.contains('hidden')) return;
                if (shareMenu.contains(e.target)) return;
                if (shareBtn.contains(e.target)) return;
                closeShareMenu();
            });

            // Close on Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !shareMenu.classList.contains('hidden')) {
                    closeShareMenu();
                }
            });

            // ---- Copy Link ----
            window.copyShareLink = async function() {
                let ok = false;
                try {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(shareUrl);
                        ok = true;
                    } else {
                        const ta = document.createElement('textarea');
                        ta.value = shareUrl;
                        ta.style.position = 'fixed';
                        ta.style.opacity = '0';
                        document.body.appendChild(ta);
                        ta.select();
                        ok = document.execCommand('copy');
                        ta.remove();
                    }
                } catch (err) {
                    console.error('Copy failed:', err);
                }

                showShareToast(ok ? 'Link copied!' : 'Failed to copy');
                if (ok) closeShareMenu();
            };

            // ---- Native Share ----
            window.nativeShare = async function() {
                try {
                    await navigator.share({
                        title: shareTitle,
                        text: shareTitle,
                        url: shareUrl,
                    });
                    closeShareMenu();
                } catch (err) {
                    // User cancelled → ignore
                    if (err && err.name !== 'AbortError') {
                        console.error('Native share failed:', err);
                    }
                }
            };

            // ---- Toast ----
            function showShareToast(message) {
                const toast = document.createElement('div');
                toast.className = 'share-toast fixed bottom-6 left-1/2 z-[60] -translate-x-1/2 rounded-full bg-zinc-900 px-4 py-2 text-xs font-medium text-white shadow-lg';
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(function() {
                    toast.style.transition = 'opacity 0.25s ease';
                    toast.style.opacity = '0';
                    setTimeout(function() { toast.remove(); }, 300);
                }, 1800);
            }
        })();
            // ========================================
            // Comment Form Script
            // ========================================
            document.getElementById('commentForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const content = document.getElementById('commentContent').value.trim();

                if (!content) {
                    alert('Please enter a comment.');
                    return;
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Posting...';

                try {
                    const response = await fetch('/articles/{{ $article->id }}/comments', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ content }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        document.getElementById('commentContent').value = '';
                        document.getElementById('charCount').textContent = '0';

                        const commentsList = document.getElementById('commentsList');
                        const emptyMessage = commentsList.querySelector('p');
                        if (emptyMessage) emptyMessage.remove();

                        const newCommentHTML = `
                            <div class="border-b border-zinc-100 pb-6">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="shrink-0">
                                        ${data.comment.user.avatar
                                            ? `<img src="${data.comment.user.avatar}" alt="${data.comment.user.name}" class="h-9 w-9 sm:h-10 sm:w-10 rounded-full object-cover" />`
                                            : `<div class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-zinc-100">
                                                <span class="text-xs font-bold text-zinc-400">${data.comment.user.name.charAt(0)}</span>
                                            </div>`
                                        }
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                            <span class="text-sm font-semibold text-zinc-900">${data.comment.user.name}</span>
                                            <span class="text-xs text-zinc-400">just now</span>
                                        </div>
                                        <p class="mt-1.5 text-sm leading-relaxed text-zinc-700 whitespace-pre-wrap break-words">${data.comment.content}</p>
                                    </div>
                                </div>
                            </div>
                        `;

                        commentsList.insertAdjacentHTML('afterbegin', newCommentHTML);

                        const heading = document.getElementById('commentHeading');
                        if (heading) {
                            const match = heading.textContent.match(/\d+/);
                            const count = match ? parseInt(match[0], 10) + 1 : 1;
                            heading.textContent = `Comments (${count})`;
                        }
                    } else {
                        alert(data.message || 'Failed to post comment.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while posting your comment.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });

            document.getElementById('commentContent')?.addEventListener('input', function() {
                document.getElementById('charCount').textContent = this.value.length;
            });

            function toggleReplyForm(commentId) {
                const form = document.getElementById(`replyForm_${commentId}`);
                if (form) {
                    form.classList.toggle('hidden');
                    if (!form.classList.contains('hidden')) {
                        form.querySelector('textarea').focus();
                    }
                }
            }

            document.querySelectorAll('.replyForm').forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const content = this.querySelector('.replyContent').value.trim();
                    const commentId = this.dataset.commentId;

                    if (!content) {
                        alert('Please enter a reply.');
                        return;
                    }

                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.textContent;
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Posting...';

                    try {
                        const response = await fetch('/articles/{{ $article->id }}/comments', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                content: content,
                                parent_id: parseInt(commentId)
                            }),
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.reset();
                            toggleReplyForm(commentId);
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to post reply.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while posting your reply.');
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                });
            });
        </script>
    @endif

</x-layouts.app>