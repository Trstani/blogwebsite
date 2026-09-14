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
    <section class="max-w-6xl mx-auto px-6 py-16">
        <div class="bg-black rounded-sm p-12 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-white leading-tight">Start Writing Today</h2>
            <p class="mt-3 text-gray-400 max-w-lg mx-auto">Bergabunglah dengan tim kami dan mulai menulis artikel.</p>
            <div class="mt-6 flex items-center justify-center space-x-4">
                <a href="{{ route('auth') }}" class="px-6 py-2.5 bg-white text-black text-sm font-medium rounded-sm hover:bg-gray-100 transition-colors">Login</a>
                <a href="/explore" class="px-6 py-2.5 border border-gray-600 text-gray-300 text-sm font-medium rounded-sm hover:border-white hover:text-white transition-colors">Browse Articles</a>
            </div>
        </div>
    </section>

</x-layouts.app>