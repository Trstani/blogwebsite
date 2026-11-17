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
                <form id="searchForm" method="GET" action="{{ route('explore') }}" class="contents">
                    <input type="text"
                           id="searchInput"
                           name="search"
                           placeholder="Search articles by title..."
                           value="{{ $searchQuery ?? '' }}"
                           class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors"
                           onkeypress="handleSearchKeypress(event)" />
                    
                    {{-- Preserve tag and category filters --}}
                    @if($activeTag)
                        <input type="hidden" name="tag" value="{{ $activeTag->slug }}" />
                    @endif
                    @if(request('category') && request('category') !== 'all')
                        <input type="hidden" name="category" value="{{ request('category') }}" />
                    @endif
                </form>
            </div>
        </div>

        {{-- Filter Tabs + Category Chips --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

            {{-- Sort Tabs --}}
            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                <button onclick="navigateWithSort('latest')" id="tab-latest"
                        class="px-4 py-2 text-sm font-medium rounded-md bg-white text-black shadow-sm transition-all">
                    Latest
                </button>
                <button onclick="navigateWithSort('popular')" id="tab-popular"
                        class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-black transition-all">
                    Popular
                </button>
            </div>

            {{-- Category Chips (Dynamic) --}}
            <div class="flex items-center gap-2 flex-wrap" id="categoryChips">
                <button onclick="navigateWithCategory('all')" id="cat-all"
                        class="px-3 py-1.5 text-xs font-medium rounded-full bg-black text-white transition-colors">
                    All
                </button>
                @foreach($categories as $cat)
                    <button onclick="navigateWithCategory('{{ $cat->slug }}')" id="cat-{{ $cat->slug }}"
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
            {{-- Article cards rendered here --}}
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

        {{-- View More Button --}}
        @if($articlesPaginated->hasMorePages())
        <div class="mt-12 text-center">
            <button id="viewMoreBtn" onclick="loadMoreArticles()" 
                    class="px-6 py-3 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors">
                View More
            </button>
            <div id="loadingSpinner" class="hidden mt-4">
                <div class="inline-block animate-spin">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        {{-- Empty State --}}
        <div id="emptyState" class="hidden text-center py-16">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <p class="text-sm text-gray-400">No articles found.</p>
        </div>
    </section>

    <script>
        let currentSort = 'latest';
        let currentCategory = 'all';
        let isLoadingMore = false;
        let currentPage = 1;

        /**
         * Handle search form submission on Enter key
         */
        function handleSearchKeypress(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                document.getElementById('searchForm').submit();
            }
        }

        /**
         * Navigate with new sort parameter
         */
        function navigateWithSort(sort) {
            currentSort = sort;
            currentPage = 1;
            
            const params = new URLSearchParams();
            if (@json($activeTag) !== null) {
                params.set('tag', @json($activeTag->slug ?? null));
            }
            if (currentCategory && currentCategory !== 'all') {
                params.set('category', currentCategory);
            }
            const searchValue = document.getElementById('searchInput').value;
            if (searchValue) {
                params.set('search', searchValue);
            }
            
            const url = params.toString() ? `{{ route('explore') }}?${params.toString()}` : '{{ route('explore') }}';
            window.location.href = url;
        }

        /**
         * Navigate with new category parameter
         */
        function navigateWithCategory(cat) {
            currentCategory = cat;
            currentPage = 1;

            const params = new URLSearchParams();
            if (@json($activeTag) !== null) {
                params.set('tag', @json($activeTag->slug ?? null));
            }
            if (cat !== 'all') {
                params.set('category', cat);
            }
            const searchValue = document.getElementById('searchInput').value;
            if (searchValue) {
                params.set('search', searchValue);
            }
            
            const url = params.toString() ? `{{ route('explore') }}?${params.toString()}` : '{{ route('explore') }}';
            window.location.href = url;
        }

        /**
         * Load more articles via AJAX
         */
        function loadMoreArticles() {
            if (isLoadingMore) return; // Prevent duplicate requests
            
            isLoadingMore = true;
            const viewMoreBtn = document.getElementById('viewMoreBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            viewMoreBtn.style.display = 'none';
            loadingSpinner.classList.remove('hidden');

            const nextPage = currentPage + 1;
            const params = new URLSearchParams();
            params.set('page', nextPage);
            
            if (@json($activeTag) !== null) {
                params.set('tag', @json($activeTag->slug ?? null));
            }
            if (currentCategory && currentCategory !== 'all') {
                params.set('category', currentCategory);
            }
            const searchValue = document.getElementById('searchInput').value;
            if (searchValue) {
                params.set('search', searchValue);
            }

            fetch(`{{ route('explore') }}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load articles');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.articles.length > 0) {
                    currentPage = nextPage;
                    appendArticles(data.articles);
                    
                    // Update View More button visibility
                    if (data.has_more) {
                        viewMoreBtn.style.display = 'block';
                    } else {
                        viewMoreBtn.style.display = 'none';
                    }
                } else {
                    viewMoreBtn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error loading more articles:', error);
                showAlert('Error', 'Failed to load more articles. Please try again.', 'error');
                viewMoreBtn.style.display = 'block';
            })
            .finally(() => {
                loadingSpinner.classList.add('hidden');
                isLoadingMore = false;
            });
        }

        /**
         * Append new articles to grid
         */
        function appendArticles(articles) {
            const grid = document.getElementById('articlesGrid');
            
            articles.forEach(article => {
                // Create wrapper div
                const wrapper = document.createElement('div');
                wrapper.className = 'article-card-wrapper';
                wrapper.dataset.articleId = article.id;
                wrapper.dataset.articleTitle = article.title.toLowerCase();
                wrapper.dataset.articleSlug = article.slug;
                wrapper.dataset.articleCategory = article.category;
                wrapper.dataset.articleViews = article.views || 0;
                wrapper.dataset.articleDate = Math.floor(new Date(article.date).getTime() / 1000);
                wrapper.dataset.searchText = (article.title + ' ' + article.slug + ' ' + (article.description || '')).toLowerCase();

                // Create card HTML using the same structure as the component
                wrapper.innerHTML = `
                    <a href="/blog/${article.slug}" class="group cursor-pointer">
                        <div class="relative w-full aspect-[4/3] bg-gray-100 rounded-lg overflow-hidden mb-4">
                            <img src="${article.thumbnail}" alt="${article.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" decoding="async" />
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-medium text-gray-400 uppercase">${article.categoryName}</span>
                            </div>
                            <h3 class="text-base font-semibold text-black group-hover:text-blue-600 transition-colors line-clamp-2">${article.title}</h3>
                            <p class="text-sm text-gray-500 line-clamp-2">${article.description || ''}</p>
                            <div class="pt-2 flex items-center justify-between text-xs text-gray-400">
                                <span>${article.author}</span>
                                <span>${article.views} views</span>
                            </div>
                        </div>
                    </a>
                `;

                grid.appendChild(wrapper);
            });
        }

        /**
         * Show/hide empty state
         */
        function updateEmptyState() {
            const visibleCount = document.querySelectorAll('.article-card-wrapper').length;
            const emptyState = document.getElementById('emptyState');

            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
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
         * Update sort button styling
         */
        function updateSortButtons() {
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

        // Initial setup on page load
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const categoryParam = urlParams.get('category') || 'all';
            currentCategory = categoryParam;
            currentPage = parseInt(urlParams.get('page')) || 1;
            
            updateSortButtons();
            updateCategoryButtons();
            updateEmptyState();
        });
    </script>

</x-layouts.app>