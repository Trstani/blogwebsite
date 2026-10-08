<x-layouts.app title="Home">

    <x-hero
        title="Latest Stories"
        subtitle="Thoughts, ideas, and stories from our team."
    />

    @php
        $featuredObj = null;
        if ($featured) {
            $featuredObj = (object)[
                'title' => $featured->title,
                'slug' => $featured->slug,
                'category' => $featured->category->name ?? '',
                'excerpt' => $featured->description,
                'author' => $featured->author->name ?? '',
                'date' => fmtDate($featured->published_at) ?: $featured->created_at->format('M d, Y'),
                'thumbnail' => $featured->cover_image ?: null,
            ];
        }

        $articlesMapped = $articles->map(fn($a) => (object)[
            'title' => $a->title,
            'slug' => $a->slug,
            'category' => $a->category->name ?? '',
            'excerpt' => $a->description,
            'author' => $a->author->name ?? '',
            'date' => fmtDate($a->published_at) ?: $a->created_at->format('M d, Y'),
            'thumbnail' => $a->cover_image ?: null,
        ])->values();
    @endphp


    {{-- =========================================================
         FEATURED + SIDEBARS
         ========================================================= --}}
   <section class="max-w-7xl mx-auto px-6 pt-12 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

            {{-- LEFT: Featured Grid --}}
            <div class="lg:col-span-8">
                <x-featured-grid :featured="$featuredObj" :articles="$articlesMapped" />
                 {{-- From Create Eve --}}
                <div class="relative mt-6 overflow-hidden rounded-2xl border border-cyan-100 bg-gradient-to-br from-white via-white to-cyan-50/70">
                    
                    {{-- Decorative background --}}
                    <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-cyan-300/10 blur-3xl"></div>
                    <div class="pointer-events-none absolute -bottom-20 right-16 h-40 w-40 rounded-full bg-cyan-200/20 blur-3xl"></div>

                    {{-- Abstract decorative rings --}}
                    <div class="pointer-events-none absolute -right-8 bottom-[-70px] h-48 w-48 rounded-full border border-cyan-200/50"></div>
                    <div class="pointer-events-none absolute -right-2 bottom-[-56px] h-36 w-36 rounded-full border border-cyan-200/40"></div>
                    <div class="pointer-events-none absolute right-5 bottom-[-42px] h-24 w-24 rounded-full border border-cyan-300/30"></div>

                    <div class="relative grid grid-cols-1 items-center gap-6 px-2 py-7 sm:px-8 sm:py-8 md:grid-cols-[1fr_auto] md:gap-8">

                        {{-- Content --}}
                        <div class="max-w-xl">

                            {{-- Eyebrow --}}
                            <div class="mb-3 flex items-center gap-3">
                                <span class="h-px w-7 bg-cyan-400"></span>

                                <span class="text-[9px] font-semibold uppercase tracking-[0.2em] text-cyan-600">
                                    From Create Eve
                                </span>
                            </div>

                            {{-- Heading --}}
                            <h3 class="max-w-lg text-2xl font-bold leading-tight tracking-tight text-zinc-900 sm:text-3xl">
                                Digital ideas, technology,
                                <span class="font-normal text-zinc-600">
                                    and perspectives for a better tomorrow.
                                </span>
                            </h3>

                            {{-- Description --}}
                            <p class="mt-3 max-w-lg text-sm leading-relaxed text-zinc-500">
                                Create Eve is a space for curious minds, where ideas meet technology,
                                business, and perspectives for a better digital future.
                            </p>

                            {{-- CTA --}}
                            <div class="mt-5">
                                <a
                                    href="/about"
                                    class="group inline-flex items-center gap-2 rounded-full bg-zinc-900 px-5 py-2.5 text-xs font-semibold text-white transition-all duration-300 hover:bg-cyan-400 hover:text-black"
                                >
                                    <span>Learn more about us</span>

                                    <span class="transition-transform duration-300 group-hover:translate-x-1">
                                        →
                                    </span>
                                </a>
                            </div>

                        </div>

                        {{-- Decorative brand mark --}}
                        <div class="relative hidden h-32 w-32 shrink-0 items-center justify-center md:flex lg:h-36 lg:w-36">

                            {{-- Outer rings --}}
                            <div class="absolute inset-0 rounded-full border border-cyan-200/60"></div>
                            <div class="absolute inset-3 rounded-full border border-cyan-200/50"></div>
                            <div class="absolute inset-7 rounded-full border border-cyan-300/40"></div>

                            {{-- Center --}}
                            <div class="relative flex h-20 w-20 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-cyan-100">
                                <div class="text-center">
                                    <div class="text-lg font-bold tracking-tight text-zinc-900">
                                        Create
                                    </div>

                                    <div class="-mt-1 text-lg font-light tracking-tight text-zinc-900">
                                        <span>E</span><span class="text-cyan-400">ve</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
           

            {{-- RIGHT: Sidebar stack --}}
            <div class="lg:col-span-4 space-y-6">

                <x-trending-sidebar :articles="$trendingArticles->map(fn($a) => (object)[
                    'title' => $a->title,
                    'slug' => $a->slug,
                    'category' => $a->category->name ?? '',
                    'author' => $a->author->name ?? '',
                    'views' => $a->views,
                    'thumbnail' => $a->cover_image ?: null,
                ])->values()" />

                <x-popular-topics-sidebar :tags="$popularTags" />

                <x-most-discussed-sidebar :articles="$mostDiscussedArticles->map(fn($a) => (object)[
                    'title' => $a->title,
                    'slug' => $a->slug,
                    'category' => $a->category->name ?? '',
                    'author' => $a->author->name ?? '',
                    'discussion_count' => $a->discussion_count,
                    'thumbnail' => $a->cover_image ?: null,
                ])->values()" />

            </div>
        </div>
    </section>


    {{-- Divider --}}
    <div class="max-w-7xl mx-auto px-6">
        <hr class="border-zinc-100" />
    </div>


    {{-- =========================================================
         RECENT ARTICLES (with integrated Search)
         ========================================================= --}}
    <section class="max-w-7xl mx-auto px-6 py-16">

        {{-- Header + Search --}}
        <div class="mb-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">

            <div>
                <div class="mb-3 flex items-center gap-3">
                    <span class="h-px w-8 bg-cyan-500"></span>
                    <span class="text-[10px] font-semibold uppercase tracking-[0.2em] text-cyan-600">
                        Browse
                    </span>
                </div>

                <h2 class="text-3xl font-bold tracking-tight text-zinc-900 md:text-4xl">
                    Recent Articles
                </h2>
            </div>

            {{-- Search — utility tool for Recent Articles --}}
            <form method="GET" action="{{ route('home') }}" class="w-full md:max-w-sm">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search articles..."
                        class="w-full rounded-full border border-zinc-200 bg-white py-2.5 pl-11 pr-24
                               text-sm text-zinc-900 placeholder:text-zinc-400
                               transition-all focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100"
                    />

                    <button
                        type="submit"
                        class="absolute right-1.5 top-1/2 -translate-y-1/2 rounded-full
                               bg-zinc-900 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-wider
                               text-white transition-colors hover:bg-cyan-500 hover:text-black"
                    >
                        Search
                    </button>
                </div>

                @if($search)
                    <p class="mt-3 text-xs text-zinc-500">
                        Hasil untuk "<span class="font-medium text-zinc-900">{{ $search }}</span>"
                    </p>
                @endif
            </form>

        </div>


        {{-- Articles Grid --}}
        <div class="grid md:grid-cols-3 gap-8">
            @forelse($recentArticles as $article)
                <x-maincomponents.article-card :article="(object)[
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'category' => $article->category->name ?? '',
                    'description' => $article->description,
                    'author' => $article->author->name ?? '',
                    'date' => fmtDate($article->published_at) ?: $article->created_at->format('M d, Y'),
                    'thumbnail' => $article->cover_image ?: null,
                    'views' => $article->views,
                    'discussion_count' => $article->discussion_count,
                ]" />
            @empty
                <p class="col-span-3 py-8 text-center text-sm text-zinc-400">
                    Belum ada artikel published.
                </p>
            @endforelse
        </div>


        {{-- View all --}}
        <div class="mt-12 text-center">
            <a href="/explore"
               class="group inline-flex items-center gap-2 text-sm font-medium text-zinc-500 transition-colors hover:text-cyan-600">
                View all articles
                <span class="transition-transform group-hover:translate-x-1">→</span>
            </a>
        </div>

    </section>


    {{-- =========================================================
         CTA v2.0
         ========================================================= --}}
    <section class="relative overflow-hidden bg-black text-white">
        <div class="pointer-events-none absolute -top-32 right-0 h-64 w-64 rounded-full bg-cyan-500/10 blur-3xl sm:h-80 sm:w-80"></div>
        <div class="pointer-events-none absolute -bottom-32 left-0 h-64 w-64 rounded-full bg-cyan-500/5 blur-3xl sm:h-80 sm:w-80"></div>

        <div class="relative mx-auto max-w-7xl px-5 py-12 sm:px-6 sm:py-14 md:py-16">

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3 md:items-center md:gap-10">

                <div class="md:col-span-2">
                    <div class="mb-4 flex items-center gap-3 sm:mb-5">
                        <span class="h-px w-6 bg-cyan-400 sm:w-8"></span>
                        <span class="text-[9px] font-semibold uppercase tracking-[0.18em] text-cyan-300 sm:text-[10px] sm:tracking-[0.2em]">
                            Get Started
                        </span>
                    </div>

                    <h2 class="max-w-2xl text-3xl font-bold leading-[1.1] tracking-tight text-white sm:text-4xl md:text-5xl">
                        Be part of
                        <span class="font-normal text-white/90">
                            Create <span class="text-cyan-400">E</span>ve.
                        </span>
                    </h2>

                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-white/60 sm:mt-5 sm:text-base">
                        Bergabunglah dengan komunitas kami dan mulai berbagi
                        cerita, ide, dan perspektif melalui tulisan.
                    </p>
                </div>

                <div class="flex w-full flex-col gap-3">
                    <a href="{{ route('auth') }}"
                       class="group flex w-full items-center justify-between rounded-lg bg-cyan-400 px-5 py-3.5 text-sm font-semibold text-black transition-colors duration-300 hover:bg-cyan-300">
                        <span>Login</span>
                        <span class="transition-transform duration-300 group-hover:translate-x-1">→</span>
                    </a>

                    <a href="/explore"
                       class="group flex w-full items-center justify-between rounded-lg border border-white/20 px-5 py-3.5 text-sm font-medium text-white/80 transition-colors duration-300 hover:border-cyan-400/60 hover:text-cyan-400">
                        <span>Browse Articles</span>
                        <span class="transition-transform duration-300 group-hover:translate-x-1">→</span>
                    </a>
                </div>

            </div>

            <div class="mt-10 flex flex-wrap items-center gap-x-3 gap-y-2 border-t border-white/10 pt-5 text-[9px] uppercase tracking-[0.14em] text-white/35 sm:mt-12 sm:gap-x-4 sm:text-[10px] sm:tracking-[0.16em]">
                <span>Stories</span>
                <span class="text-cyan-400/60">·</span>
                <span>Ideas</span>
                <span class="text-cyan-400/60">·</span>
                <span>Perspectives</span>
            </div>

        </div>
    </section>

</x-layouts.app>