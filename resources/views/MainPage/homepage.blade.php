<x-layouts.app title="Home">

    <x-hero
        title="Latest Stories"
        subtitle="Thoughts, ideas, and stories from our team."
    />

    {{-- Search Area --}}
    <section class="max-w-6xl mx-auto px-6 py-6">
        <form method="GET" action="{{ route('home') }}" class="max-w-xl mx-auto">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Search articles by title..."
                       class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-medium text-gray-400 hover:text-black transition-colors">
                    Search
                </button>
            </div>
        </form>
        @if($search)
            <p class="text-center text-xs text-gray-400 mt-3">
                Search results for "<span class="font-medium">{{ $search }}</span>"
            </p>
        @endif
    </section>

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

    {{-- Featured Grid + Sidebars Section --}}
    <section class="max-w-6xl mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- LEFT: Featured Grid (3 columns) --}}
            <div class="lg:col-span-3">
                <x-featured-grid :featured="$featuredObj" :articles="$articlesMapped" />
            </div>

            {{-- RIGHT: Trending + Most Discussed Sidebars (1 column, stacked) --}}
            <div class="lg:col-span-1 space-y-6">
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
    <div class="max-w-6xl mx-auto px-6">
        <hr class="border-gray-100" />
    </div>

    {{-- Recent Articles --}}
    <section class="max-w-6xl mx-auto px-6 py-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Recent Articles</h2>
            <a href="/explore" class="text-sm text-gray-400 hover:text-black transition-colors">View all →</a>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            @forelse($recentArticles as $article)
                <x-maincomponents.article-card :article="(object)[
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'category' => $article->category->name ?? '',
                    'excerpt' => $article->description,
                    'author' => $article->author->name ?? '',
                    'date' => fmtDate($article->published_at) ?: $article->created_at->format('M d, Y'),
                    'thumbnail' => $article->cover_image ?: null,
                ]" />
            @empty
                <p class="text-sm text-gray-400 text-center py-8 col-span-3">Belum ada artikel published.</p>
            @endforelse
        </div>
    </section>

    {{-- CTA v2.0 --}}
    <section class="relative overflow-hidden bg-black text-white">
        {{-- Subtle cyan glow accent --}}
        <div class="pointer-events-none absolute -top-32 right-0 h-64 w-64 rounded-full bg-cyan-500/10 blur-3xl sm:h-80 sm:w-80"></div>
        <div class="pointer-events-none absolute -bottom-32 left-0 h-64 w-64 rounded-full bg-cyan-500/5 blur-3xl sm:h-80 sm:w-80"></div>

        <div class="relative mx-auto max-w-6xl px-5 py-12 sm:px-6 sm:py-14 md:py-16">

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3 md:items-center md:gap-10">

                {{-- LEFT: Main Copy --}}
                <div class="md:col-span-2">

                    {{-- Eyebrow --}}
                    <div class="mb-4 flex items-center gap-3 sm:mb-5">
                        <span class="h-px w-6 bg-cyan-400 sm:w-8"></span>

                        <span class="text-[9px] font-semibold uppercase tracking-[0.18em] text-cyan-300 sm:text-[10px] sm:tracking-[0.2em]">
                            Get Started
                        </span>
                    </div>

                    {{-- Heading --}}
                    <h2 class="max-w-2xl text-3xl font-bold leading-[1.1] tracking-tight text-white sm:text-4xl md:text-5xl">
                        Be part of
                        <span class="font-normal text-white/90">
                            Create <span class="text-cyan-400">E</span>ve.
                        </span>
                    </h2>

                    {{-- Description --}}
                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-white/60 sm:mt-5 sm:text-base">
                        Bergabunglah dengan komunitas kami dan mulai berbagi
                        cerita, ide, dan perspektif melalui tulisan.
                    </p>

                </div>


                {{-- RIGHT: Actions --}}
                <div class="flex w-full flex-col gap-3">

                    {{-- Login --}}
                    <a href="{{ route('auth') }}"
                    class="group flex w-full items-center justify-between rounded-lg bg-cyan-400 px-5 py-3.5 text-sm font-semibold text-black transition-colors duration-300 hover:bg-cyan-400">

                        <span>Login</span>

                        <span class="transition-transform duration-300 group-hover:translate-x-1">
                            →
                        </span>

                    </a>


                    {{-- Browse --}}
                    <a href="/explore"
                    class="group flex w-full items-center justify-between rounded-lg border border-white/20 px-5 py-3.5 text-sm font-medium text-white/80 transition-colors duration-300 hover:border-cyan-400/60 hover:text-cyan-400">

                        <span>Browse Articles</span>

                        <span class="transition-transform duration-300 group-hover:translate-x-1">
                            →
                        </span>

                    </a>

                </div>

            </div>


            {{-- Bottom Meta --}}
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