<x-layouts.app title="Explore">

    {{-- Hero --}}
    <x-hero
        title="Explore Articles"
        subtitle="Discover articles by topic, trend, or search."
    />

    {{-- Search + Filters --}}
    <section class="max-w-6xl mx-auto px-6 pb-8">

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
    <section class="max-w-6xl mx-auto px-6 pb-16">
        <div id="articlesGrid" class="grid md:grid-cols-3 gap-8">
            {{-- Articles rendered by JS --}}
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
        const articles = @json($articles);
        let currentSort = 'latest';
        let currentCategory = 'all';
        let searchQuery = '';

        function renderArticles() {
            let filtered = [...articles];

            // Search filter
            if (searchQuery) {
                const q = searchQuery.toLowerCase();
                filtered = filtered.filter(a =>
                    a.title.toLowerCase().includes(q) ||
                    a.slug.toLowerCase().includes(q)
                );
            }

            // Category filter
            if (currentCategory !== 'all') {
                filtered = filtered.filter(a => a.category === currentCategory);
            }

            // Sort
            if (currentSort === 'popular') {
                filtered.sort((a, b) => b.views - a.views);
            } else {
                filtered.sort((a, b) => new Date(b.date) - new Date(a.date));
            }

            const grid = document.getElementById('articlesGrid');
            const empty = document.getElementById('emptyState');

            if (filtered.length === 0) {
                grid.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }

            empty.classList.add('hidden');
            grid.innerHTML = filtered.map(article => `
                <a href="/blog/${article.slug}" class="group block">
                    <article class="bg-white">
                        ${article.thumbnail
                            ? `<div class="aspect-[16/10] overflow-hidden bg-gray-100 rounded-sm">
                                <img src="${article.thumbnail}" alt="${article.title}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                               </div>`
                            : `<div class="aspect-[16/10] bg-gray-100 rounded-sm flex items-center justify-center">
                                <span class="text-gray-300 text-4xl font-light">—</span>
                               </div>`
                        }
                        <div class="mt-4">
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">
                                ${article.categoryName}
                            </span>
                            <h3 class="mt-1 text-lg font-semibold text-black leading-snug group-hover:text-gray-600 transition-colors">
                                ${article.title}
                            </h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed line-clamp-2">
                                ${article.description || ''}
                            </p>
                            <div class="mt-3 flex items-center space-x-3 text-xs text-gray-400">
                                <span>${article.author}</span>
                                <span>·</span>
                                <span>${article.date}</span>
                                <span>·</span>
                                <span>${article.views} views</span>
                            </div>
                        </div>
                    </article>
                </a>
            `).join('');
        }

        function setSort(sort) {
            currentSort = sort;
            document.getElementById('tab-latest').className = sort === 'latest'
                ? 'px-4 py-2 text-sm font-medium rounded-md bg-white text-black shadow-sm transition-all'
                : 'px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-black transition-all';
            document.getElementById('tab-popular').className = sort === 'popular'
                ? 'px-4 py-2 text-sm font-medium rounded-md bg-white text-black shadow-sm transition-all'
                : 'px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-black transition-all';
            renderArticles();
        }

        function setCategory(cat) {
            currentCategory = cat;

            // Update all category buttons
            const allBtns = document.querySelectorAll('[id^="cat-"]');
            allBtns.forEach(btn => {
                const btnCat = btn.id.replace('cat-', '');
                if (btnCat === cat) {
                    btn.className = 'px-3 py-1.5 text-xs font-medium rounded-full bg-black text-white transition-colors';
                } else {
                    btn.className = 'px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors';
                }
            });

            renderArticles();
        }

        function filterArticles() {
            searchQuery = document.getElementById('searchInput').value;
            renderArticles();
        }

        // Initial render
        document.addEventListener('DOMContentLoaded', renderArticles);
    </script>

</x-layouts.app>