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

    {{-- Featured Articles Grid --}}
    <x-featured-grid :featured="$featuredObj" :articles="$articlesMapped" />

    {{-- Divider --}}
    <div class="max-w-6xl mx-auto px-6">
        <hr class="border-gray-100" />
    </div>

    {{-- Recent Articles + Trending --}}
    <section class="max-w-6xl mx-auto px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3">
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
            </div>
            <div class="lg:col-span-1">
               <x-trending-sidebar :articles="$trendingArticles->map(fn($a) => (object)[
                    'title' => $a->title,
                    'slug' => $a->slug,
                    'category' => $a->category->name ?? '',
                    'author' => $a->author->name ?? '',
                    'views' => $a->views,
                    'thumbnail' => $a->cover_image ?: null,
                ])->values()" />
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative bg-black text-white overflow-hidden">
        {{-- thin cyan top rule --}}
        <div class="absolute top-0 left-0 w-full h-[2px] bg-[#54DCE3]"></div>

        {{-- subtle dot grid accent (top-right) --}}
        <div class="pointer-events-none absolute top-8 right-8 hidden lg:grid grid-cols-6 gap-[6px] opacity-30">
            @for ($i = 0; $i < 24; $i++)
                <span class="h-[2px] w-[2px] bg-[#54DCE3]"></span>
            @endfor
        </div>

        <div class="relative mx-auto max-w-[1400px] px-6 sm:px-8 lg:px-12 py-16 lg:py-24">
            <div class="grid grid-cols-12 items-stretch gap-8 lg:gap-0">

                {{-- LEFT: content on black --}}
                <div class="col-span-12 lg:col-span-8 lg:pr-16 py-4 flex flex-col justify-center">

                    <div class="flex items-center gap-3 text-[10px] tracking-[0.3em] uppercase text-[#54DCE3] mb-8">
                        <span class="inline-block h-[6px] w-[6px] rounded-full bg-[#54DCE3]"></span>
                        <span>Get Started</span>
                    </div>

                    <h2 class="text-[clamp(2.25rem,5.5vw,4.5rem)] font-light tracking-[-0.03em] leading-[1]">
                        Start Writing<br>
                        <span class="italic font-normal">Today.</span>
                    </h2>

                    <p class="mt-7 max-w-md text-white/60 font-light text-base leading-relaxed">
                        Bergabunglah dengan tim kami dan mulai menulis artikel.
                    </p>

                    <div class="mt-10 flex flex-col sm:flex-row items-stretch sm:items-center gap-4 sm:gap-6">
                        <a href="{{ route('auth') }}"
                        class="group inline-flex items-center justify-between sm:justify-center gap-3 bg-[#54DCE3] text-black px-7 py-4 text-sm tracking-[0.15em] uppercase hover:bg-white transition-colors duration-300">
                            <span>Login</span>
                            <span class="inline-block transition-transform duration-300 group-hover:translate-x-1">→</span>
                        </a>

                        <a href="/explore"
                        class="group inline-flex items-center justify-between sm:justify-center gap-3 text-sm tracking-[0.15em] uppercase text-white/70 hover:text-white border border-white/20 hover:border-white/60 px-7 py-4 transition-colors duration-300">
                            <span>Browse Articles</span>
                            <span class="inline-block transition-transform duration-300 group-hover:translate-x-1">→</span>
                        </a>
                    </div>

                    {{-- small meta row --}}
                    <div class="mt-12 flex flex-wrap items-center gap-x-8 gap-y-3 text-[10px] tracking-[0.3em] uppercase text-white/40">
                        <span>Open Call — 2025</span>
                        <span class="h-[1px] w-6 bg-white/20"></span>
                        <span>Editorial Contributors</span>
                        <span class="h-[1px] w-6 bg-white/20 hidden sm:inline-block"></span>
                        <span class="hidden sm:inline-block">Remote / Jakarta</span>
                    </div>
                </div>

                {{-- RIGHT: cyan panel --}}
                <div class="col-span-12 lg:col-span-4 relative">
                    <div class="bg-[#54DCE3] text-black h-full p-8 lg:p-10 flex flex-col justify-between min-h-[300px] lg:min-h-0 relative overflow-hidden">

                        {{-- panel header --}}
                        <div class="flex justify-between items-start text-[10px] tracking-[0.3em] uppercase">
                            <span>N° 01</span>
                            <span>Join / Publish</span>
                        </div>

                        {{-- oversized arrow --}}
                        <div class="my-10 lg:my-16">
                            <div class="text-[clamp(4rem,10vw,8rem)] leading-none font-light tracking-tighter">→</div>
                        </div>

                        {{-- panel footer copy --}}
                        <div>
                            <p class="text-sm leading-relaxed font-normal max-w-xs">
                                Become part of Create Eve's editorial team — write, review, and shape the stories we publish.
                            </p>
                            <div class="mt-6 h-[2px] w-12 bg-black"></div>
                        </div>

                        {{-- corner tick --}}
                        <span class="absolute top-0 right-0 h-10 w-10 border-t-2 border-r-2 border-black/20"></span>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-layouts.app>