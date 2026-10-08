<x-layouts.app title="Explore">

    {{-- Hero --}}
    <x-hero
        :title="$activeTag ? $activeTag->name : 'Explore Articles'"
        :subtitle="$activeTag
            ? 'Discover articles, ideas, and stories related to ' . $activeTag->name . '.'
            : 'Discover articles by topic, trend, or search.'"
    />

    {{-- Search + Filters --}}
    <section class="max-w-7xl mx-auto px-6 pb-8">

        {{-- Search Bar --}}
        <div class="max-w-xl mb-8">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="text"
                       id="searchInput"
                       placeholder="Search articles by title..."
                       class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors"
                       oninput="filterArticles()" />
            </div>
        </div>

        {{-- Filter Tabs + Category Chips --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

            {{-- Sort Tabs --}}
            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                <button onclick="setSort('latest')" id="tab-latest"
                        class="px-4 py-2 text-sm font-medium rounded-md bg-white text-black shadow-sm transition-all">
                    Latest
                </button>
                <button onclick="setSort('popular')" id="tab-popular"
                        class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-black transition-all">
                    Popular
                </button>
            </div>

            {{-- Category Chips (Dynamic) --}}
            <div class="flex items-center gap-2 flex-wrap" id="categoryChips">
                <button onclick="setCategory('all')" id="cat-all"
                        class="px-3 py-1.5 text-xs font-medium rounded-full bg-black text-white transition-colors">
                    All
                </button>
                @foreach($categories as $cat)
                    <button onclick="setCategory('{{ $cat->slug }}')" id="cat-{{ $cat->slug }}"
                            class="px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>

        </div>

    </section>

    {{-- Articles Grid --}}
    <section class="max-w-7xl mx-auto px-6 pb-16">
        <div id="articlesGrid" class="grid md:grid-cols-3 gap-8">
            {{-- Pre-rendered article cards using Blade component --}}
            @forelse($articles as $article)
            <div class="article-card-wrapper"
                data-article-id="{{ $article->id }}"
                data-article-title="{{ strtolower($article->title) }}"
                data-article-slug="{{ $article->slug }}"
                data-article-category="{{ $article->category }}"
                data-article-views="{{ $article->views ?? 0 }}"
                data-article-date="{{ strtotime($article->date) }}"
                data-search-text="{{ strtolower($article->title . ' ' . $article->slug . ' ' . ($article->description ?? '')) }}">

                <x-maincomponents.article-card :article="(object)[
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'category' => $article->categoryName,
                    'description' => $article->description,
                    'author' => $article->author,
                    'date' => $article->date,
                    'thumbnail' => $article->thumbnail,
                    'views' => $article->views,
                    'discussion_count' => $article->discussion_count,
                ]" />
            </div>
            @empty
                {{-- Empty state handled by JavaScript --}}
            @endforelse
        </div>

        {{-- Empty State --}}
        <div id="emptyState" class="hidden text-center py-16">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <p class="text-sm text-gray-400">No articles found.</p>
        </div>
    </section>

    <script>
        const activeTag = @json($activeTag);
        let currentSort = 'latest';
        let currentCategory = 'all';
        let searchQuery = '';

        /**
         * Filter and sort articles based on current state
         * Manipulates DOM directly - no HTML generation
         */
        function applyFilters() {
            const cards = document.querySelectorAll('.article-card-wrapper');
            let visibleCards = [];

            cards.forEach(card => {
                // Search filter (case-insensitive)
                const searchText = card.dataset.searchText;
                const matchesSearch = searchQuery === '' || searchText.includes(searchQuery.toLowerCase());

                // Category filter
                const cardCategory = card.dataset.articleCategory;
                const matchesCategory = currentCategory === 'all' || cardCategory === currentCategory;

                // Determine visibility
                const visible = matchesSearch && matchesCategory;
                card.style.display = visible ? '' : 'none';

                if (visible) {
                    visibleCards.push(card);
                }
            });

            // Apply sorting to visible cards
            applySorting(visibleCards);

            // Update empty state
            updateEmptyState();
        }

        /**
         * Sort visible cards and reorder DOM
         */
        function applySorting(visibleCards) {
            if (visibleCards.length === 0) return;

            const grid = document.getElementById('articlesGrid');

            if (currentSort === 'popular') {
                // Sort by views descending
                visibleCards.sort((a, b) => {
                    const viewsA = parseInt(a.dataset.articleViews) || 0;
                    const viewsB = parseInt(b.dataset.articleViews) || 0;
                    return viewsB - viewsA;
                });
            } else {
                // Sort by date descending (latest first)
                visibleCards.sort((a, b) => {
                    const dateA = parseInt(a.dataset.articleDate) || 0;
                    const dateB = parseInt(b.dataset.articleDate) || 0;
                    return dateB - dateA;
                });
            }

            // Reorder DOM by moving cards to end (maintains order)
            visibleCards.forEach(card => {
                grid.appendChild(card);
            });
        }

        /**
         * Update sort tab styling
         */
        function updateSortTabs() {
            const latestBtn = document.getElementById('tab-latest');
            const popularBtn = document.getElementById('tab-popular');

            if (currentSort === 'latest') {
                latestBtn.className = 'px-4 py-2 text-sm font-medium rounded-md bg-white text-black shadow-sm transition-all';
                popularBtn.className = 'px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-black transition-all';
            } else {
                latestBtn.className = 'px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-black transition-all';
                popularBtn.className = 'px-4 py-2 text-sm font-medium rounded-md bg-white text-black shadow-sm transition-all';
            }
        }

        /**
         * Update category button styling
         */
        function updateCategoryButtons() {
            const buttons = document.querySelectorAll('[id^="cat-"]');
            buttons.forEach(btn => {
                const btnCategory = btn.id.replace('cat-', '');
                if (btnCategory === currentCategory) {
                    btn.className = 'px-3 py-1.5 text-xs font-medium rounded-full bg-black text-white transition-colors';
                } else {
                    btn.className = 'px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors';
                }
            });
        }

        /**
         * Show/hide empty state based on visible articles
         */
        function updateEmptyState() {
            const visibleCount = document.querySelectorAll('.article-card-wrapper:not([style*="display: none"])').length;
            const emptyState = document.getElementById('emptyState');

            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }

        /**
         * Handle sort change
         */
        function setSort(sort) {
            currentSort = sort;
            updateSortTabs();
            applyFilters();
        }

        /**
         * Handle category filter change
         */
        function setCategory(cat) {
            currentCategory = cat;
            updateCategoryButtons();

            // Update URL while preserving tag filter if present
            const params = new URLSearchParams();
            if (activeTag) {
                params.set('tag', activeTag.slug);
            }
            if (cat !== 'all') {
                params.set('category', cat);
            }
            const newUrl = params.toString() ? `?${params.toString()}` : window.location.pathname;
            window.history.replaceState({}, '', newUrl);

            applyFilters();
        }

        /**
         * Handle search input
         */
        function filterArticles() {
            searchQuery = document.getElementById('searchInput').value;
            applyFilters();
        }

        // Initial setup on page load
        document.addEventListener('DOMContentLoaded', () => {
            updateSortTabs();
            updateCategoryButtons();
            applyFilters();
        });
    </script>

</x-layouts.app>