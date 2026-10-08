@props([
    'featured' => null,
    'articles' => collect(),
])

<section class="w-full">

    {{-- Section Eyebrow --}}
    <div class="mb-5 flex items-center gap-3">
        <span class="h-px w-8 bg-cyan-500"></span>

        <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-cyan-500">
            FEATURED
        </h2>
    </div>


    <div class="grid grid-cols-1 gap-4 md:grid-cols-12 md:gap-5">


        {{-- =========================================================
             FEATURED — DOMINANT HERO
             ========================================================= --}}
        @if($featured)
            <a
                href="/blog/{{ $featured->slug }}"
                class="group relative block overflow-hidden rounded-2xl bg-black
                       md:col-span-7 md:row-span-2
                       min-h-[400px] md:min-h-[440px]"
            >

                {{-- Cover Image --}}
                @if($featured->thumbnail ?? false)
                    <img
                        src="{{ imageUrl($featured->thumbnail) }}"
                        alt="{{ $featured->title }}"
                        class="absolute inset-0 h-full w-full object-cover
                               transition-transform duration-700
                               group-hover:scale-[1.03]"
                    />
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-zinc-800 via-zinc-900 to-black"></div>
                @endif


                {{-- Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/55 to-transparent"></div>


                {{-- Cyan Accent --}}
                <div class="absolute left-0 top-0 h-[3px] w-20 bg-cyan-400"></div>


                {{-- Content --}}
                <div class="absolute inset-x-0 bottom-0 p-5 md:p-6">

                    @if($featured->category ?? false)
                        <div class="mb-2 flex items-center gap-3">
                            <span class="text-[10px] font-semibold uppercase tracking-[0.2em] text-cyan-400">
                                {{ $featured->category }}
                            </span>

                            <span class="h-px w-5 bg-cyan-400/40"></span>
                        </div>
                    @endif


                    <h2
                        class="max-w-2xl text-2xl font-bold leading-[1.1] tracking-tight
                               text-white transition-colors
                               md:text-3xl
                               group-hover:text-cyan-50"
                    >
                        {{ $featured->title }}
                    </h2>


                    @if($featured->excerpt ?? false)
                        <p
                            class="mt-2 max-w-xl text-sm leading-relaxed text-white/70
                                   line-clamp-2"
                        >
                            {{ $featured->excerpt }}
                        </p>
                    @endif


                    <div class="mt-4 flex items-center gap-3 text-xs text-white/60">

                        @if($featured->author ?? false)
                            <span class="font-medium text-white/90">
                                {{ $featured->author }}
                            </span>
                        @endif

                        @if($featured->date ?? false)
                            <span class="text-cyan-400/60">·</span>

                            <span>
                                {{ $featured->date }}
                            </span>
                        @endif

                    </div>

                </div>

            </a>
        @else

            {{-- Empty Featured State --}}
            <div
                class="relative flex min-h-[400px] items-center justify-center
                       overflow-hidden rounded-2xl border-2 border-dashed
                       border-zinc-200 bg-zinc-50
                       md:col-span-7 md:row-span-2 md:min-h-[440px]"
            >
                <div class="text-center">

                    <svg
                        class="mx-auto mb-3 h-12 w-12 text-zinc-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-1.125 1.125-1.125V11.25a9 9 0 00-9-9z"
                        />
                    </svg>

                    <p class="text-sm text-zinc-400">
                        No featured article yet
                    </p>

                    <p class="mt-1 text-xs text-zinc-300">
                        Admin can feature articles from the dashboard
                    </p>

                </div>
            </div>

        @endif


        {{-- =========================================================
             SUPPORTING ARTICLES — 2 & 3
             ========================================================= --}}
        @foreach($articles->take(2) as $article)

            <a
                href="/blog/{{ $article->slug ?? '#' }}"
                class="group relative block overflow-hidden rounded-2xl bg-black
                       md:col-span-5
                       min-h-[210px]"
            >

                {{-- Cover Image --}}
                @if($article->thumbnail ?? false)
                    <img
                        src="{{ imageUrl($article->thumbnail) }}"
                        alt="{{ $article->title ?? '' }}"
                        class="absolute inset-0 h-full w-full object-cover
                               transition-transform duration-700
                               group-hover:scale-105"
                    />
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-zinc-800 via-zinc-900 to-black"></div>
                @endif


                {{-- Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/55 to-transparent"></div>


                {{-- Cyan Accent --}}
                <div class="absolute left-0 top-0 h-[3px] w-16 bg-cyan-400"></div>


                {{-- Content --}}
                <div class="absolute inset-x-0 bottom-0 p-4 md:p-5">

                    @if($article->category ?? false)
                        <div class="mb-2 flex items-center gap-2">
                            <span class="text-[9px] font-semibold uppercase tracking-[0.18em] text-cyan-400">
                                {{ $article->category }}
                            </span>

                            <span class="h-px w-4 bg-cyan-400/40"></span>
                        </div>
                    @endif


                    <h3
                        class="line-clamp-2 text-base font-bold leading-snug tracking-tight
                               text-white transition-colors
                               md:text-lg
                               group-hover:text-cyan-50"
                    >
                        {{ $article->title ?? 'Article Title' }}
                    </h3>


                    <div class="mt-2 flex items-center gap-2 text-[11px] text-white/60">

                        @if($article->author ?? false)
                            <span class="font-medium text-white/90">
                                {{ $article->author }}
                            </span>
                        @endif

                        @if($article->date ?? false)
                            <span class="text-cyan-400/60">·</span>

                            <span>
                                {{ $article->date }}
                            </span>
                        @endif

                    </div>

                </div>

            </a>

        @endforeach


       {{-- =========================================================
            SUPPORTING ARTICLES — 4 & 5
            ========================================================= --}}
        @foreach($articles->slice(2, 2) as $article)

        <a
            href="/blog/{{ $article->slug ?? '#' }}"
            class="group relative block overflow-hidden rounded-2xl bg-black
                {{ $loop->first ? 'md:col-span-7' : 'md:col-span-5' }}
                min-h-[210px]"
        >

                {{-- Cover Image --}}
                @if($article->thumbnail ?? false)
                    <img
                        src="{{ imageUrl($article->thumbnail) }}"
                        alt="{{ $article->title ?? '' }}"
                        class="absolute inset-0 h-full w-full object-cover
                            transition-transform duration-700
                            group-hover:scale-105"
                    />
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-zinc-800 via-zinc-900 to-black"></div>
                @endif

                {{-- Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/55 to-transparent"></div>

                {{-- Cyan Accent --}}
                <div class="absolute left-0 top-0 h-[3px] w-16 bg-cyan-400"></div>

                {{-- Content --}}
                <div class="absolute inset-x-0 bottom-0 p-4 md:p-5">

                    @if($article->category ?? false)
                        <div class="mb-2 flex items-center gap-2">
                            <span class="text-[9px] font-semibold uppercase tracking-[0.18em] text-cyan-400">
                                {{ $article->category }}
                            </span>

                            <span class="h-px w-4 bg-cyan-400/40"></span>
                        </div>
                    @endif

                    <h3
                        class="line-clamp-2 text-base font-bold leading-snug tracking-tight
                            text-white transition-colors
                            md:text-lg
                            group-hover:text-cyan-50"
                    >
                        {{ $article->title ?? 'Article Title' }}
                    </h3>

                    <div class="mt-2 flex items-center gap-2 text-[11px] text-white/60">

                        @if($article->author ?? false)
                            <span class="font-medium text-white/90">
                                {{ $article->author }}
                            </span>
                        @endif

                        @if($article->date ?? false)
                            <span class="text-cyan-400/60">·</span>

                            <span>
                                {{ $article->date }}
                            </span>
                        @endif

                    </div>

                </div>

            </a>

        @endforeach

    </div>

</section>