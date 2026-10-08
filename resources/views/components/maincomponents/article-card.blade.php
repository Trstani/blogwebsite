@props(['article'])

<a
    href="/blog/{{ $article->slug ?? '#' }}"
    class="group block h-full"
>
    <article
        class="flex h-full flex-col overflow-hidden rounded-xl border border-zinc-200
               bg-white transition-all duration-300
               group-hover:-translate-y-0.5
               group-hover:border-cyan-400/60
               group-hover:shadow-[0_6px_20px_-10px_rgba(6,182,212,0.25)]"
    >

        {{-- =========================================================
             THUMBNAIL
             ========================================================= --}}
        @if($article->thumbnail ?? false)

            <div class="relative aspect-[16/9] shrink-0 overflow-hidden bg-zinc-100">

                <img
                    src="{{ imageUrl($article->thumbnail) }}"
                    alt="{{ $article->title ?? 'Article' }}"
                    class="h-full w-full object-cover transition-transform duration-500
                           group-hover:scale-105"
                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                />

                {{-- Image fallback --}}
                <div class="absolute inset-0 hidden overflow-hidden bg-zinc-950">

                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full border border-white/5"></div>
                    <div class="absolute -bottom-10 -left-6 h-28 w-28 rounded-full border border-white/5"></div>

                    <div class="relative flex h-full flex-col justify-between p-3.5">

                        @if($article->category ?? false)
                            <span class="text-[9px] font-semibold uppercase tracking-[0.18em] text-cyan-400/70">
                                {{ $article->category }}
                            </span>
                        @endif

                        <span class="text-3xl font-bold uppercase text-white/5">
                            {{ substr($article->title ?? 'A', 0, 1) }}
                        </span>

                    </div>
                </div>

            </div>

        @else

            {{-- Editorial fallback --}}
            <div class="relative aspect-[16/9] shrink-0 overflow-hidden bg-zinc-950">

                <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full border border-white/5"></div>
                <div class="absolute -bottom-10 -left-6 h-28 w-28 rounded-full border border-white/5"></div>

                {{-- Cyan accent line --}}
                <div class="absolute left-0 top-0 h-[2px] w-12 bg-cyan-400"></div>

                <div class="relative flex h-full flex-col justify-between p-3.5">

                    <div class="flex items-center justify-between">

                        @if($article->category ?? false)
                            <span class="text-[9px] font-semibold uppercase tracking-[0.18em] text-cyan-400/70">
                                {{ $article->category }}
                            </span>
                        @endif

                        <span class="text-[9px] text-white/20">
                            ARTICLE
                        </span>

                    </div>

                    <div>
                        <span class="block text-4xl font-bold uppercase leading-none text-white/5">
                            {{ substr($article->title ?? 'A', 0, 1) }}
                        </span>

                        <div class="mt-1.5 h-px w-8 bg-cyan-400/40"></div>
                    </div>

                </div>

            </div>

        @endif


        {{-- =========================================================
             CONTENT
             ========================================================= --}}
        <div class="flex flex-1 flex-col p-3">

            {{-- Category + Engagement --}}
            <div class="flex items-center justify-between gap-2">

                {{-- Category --}}
                <div class="min-w-0">
                    @if($article->category ?? false)
                        <span
                            class="text-[9px] font-semibold uppercase tracking-[0.16em]
                                   text-cyan-600"
                        >
                            {{ $article->category }}
                        </span>
                    @endif
                </div>

                {{-- Engagement --}}
                <div
                    class="flex shrink-0 items-center gap-2
                           text-[10px] font-medium text-zinc-400"
                >

                    {{-- Views --}}
                    @if(isset($article->views))
                        <span
                            class="inline-flex items-center gap-0.5
                                   transition-colors
                                   group-hover:text-cyan-600"
                        >
                            <svg
                                class="h-3 w-3"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                            </svg>

                            <span>{{ $article->views ?? 0 }}</span>
                        </span>
                    @endif

                    {{-- Comments --}}
                    @if(isset($article->discussion_count))
                        <span
                            class="inline-flex items-center gap-0.5
                                   transition-colors
                                   group-hover:text-cyan-600"
                        >
                            <svg
                                class="h-3 w-3"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                />
                            </svg>

                            <span>{{ $article->discussion_count ?? 0 }}</span>
                        </span>
                    @endif

                </div>
            </div>


            {{-- Title --}}
            <h3
                class="mt-1.5 line-clamp-2 text-sm font-semibold leading-snug
                       text-zinc-900 transition-colors
                       group-hover:text-cyan-700"
            >
                {{ $article->title ?? 'Article Title' }}
            </h3>


            {{-- Description --}}
            @if($article->description ?? false)
                <p
                    class="mt-1 line-clamp-2 text-xs leading-relaxed text-zinc-500"
                >
                    {{ $article->description }}
                </p>
            @endif


            {{-- =====================================================
                AUTHOR + DATE + READ
                ===================================================== --}}
            <div
                class="mt-auto flex items-center justify-between gap-2 pt-2.5"
            >

                {{-- Author + Date --}}
                <div
                    class="flex min-w-0 items-center gap-1.5 text-[10px] text-zinc-400"
                >
                    @if($article->author ?? false)
                        <span class="truncate font-medium text-zinc-700">
                            {{ $article->author }}
                        </span>
                    @endif

                    @if(($article->author ?? false) && ($article->date ?? false))
                        <span class="text-cyan-400/60">·</span>
                    @endif

                    @if($article->date ?? false)
                        <span class="whitespace-nowrap">
                            {{ $article->date }}
                        </span>
                    @endif
                </div>


                {{-- Read --}}
                <span
                    class="inline-flex shrink-0 items-center gap-0.5 text-[10px] font-semibold text-cyan-600
                           transition-transform duration-200
                           group-hover:translate-x-0.5"
                >
                    Read
                    <span>→</span>
                </span>

            </div>

        </div>

    </article>
</a>