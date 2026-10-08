@props(['transparent' => false])

<nav class="{{ $transparent ? 'absolute top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-white/20 shadow-lg shadow-black/5' : 'sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-gray-200/60 shadow-sm' }}">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

        {{-- Logo --}}
        <a href="/" class="flex items-center group">
            <img
                src="{{ asset('logo/createeve.png') }}"
                alt="Create Eve"
                class="h-12 w-auto object-contain transition-transform duration-300 group-hover:scale-[1.02]"
            >
        </a>

        {{-- Center Links --}}
        <div class="hidden md:flex items-center space-x-1">
            <a href="/explore" class="relative px-4 py-2 text-sm font-medium text-gray-600 hover:text-black transition-all duration-200 rounded-full hover:bg-gray-100/80 group">
                Explore
                <span class="absolute left-1/2 -bottom-0.5 h-0.5 w-0 group-hover:w-1/2 group-hover:left-1/2 transform -translate-x-1/2 bg-black transition-all duration-300"></span>
            </a>
            <a href="/about" class="relative px-4 py-2 text-sm font-medium text-gray-600 hover:text-black transition-all duration-200 rounded-full hover:bg-gray-100/80 group">
                About
                <span class="absolute left-1/2 -bottom-0.5 h-0.5 w-0 group-hover:w-1/2 group-hover:left-1/2 transform -translate-x-1/2 bg-black transition-all duration-300"></span>
            </a>

            {{-- Navbar Search --}}
            <form id="navbarSearchForm" method="GET" action="{{ route('explore') }}" class="flex items-center relative hidden md:flex">
                <div class="relative w-full">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input type="text"
                           id="navbarSearch"
                           name="search"
                           placeholder="Search..."
                           class="pl-10 pr-12 py-2 text-sm bg-gray-100 border border-gray-200 rounded-full text-gray-700 placeholder-gray-500 focus:outline-none focus:border-black focus:bg-white transition-all duration-200 w-full"
                           onkeypress="handleNavbarSearchKeypress(event)"
                           oninput="handleAutocompleteInput(event)"
                           onfocus="openSearchPanel()"
                           autocomplete="off" />

                    {{-- Search Button --}}
                    <button type="submit"
                            class="absolute right-1.5 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    {{-- Search Discovery Panel (Dropdown) --}}
                    <div id="searchPanel" class="absolute top-full left-0 right-0 mt-2 hidden z-50 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden md:w-80 md:max-w-sm" style="width: calc(100vw - 32px); max-height: 70vh;">
                        
                        {{-- Panel Content --}}
                        <div class="p-3 md:p-4 overflow-y-auto" style="max-height: 70vh;">

                            {{-- Autocomplete Results Section (shown when typing) --}}
                            <div id="autocompleteResults" class="hidden mb-6">
                                
                                {{-- Loading State --}}
                                <div id="autocompleteLoading" class="hidden py-4 text-center">
                                    <div class="inline-flex items-center gap-2 text-gray-600">
                                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <span class="text-xs font-medium">Searching...</span>
                                    </div>
                                </div>

                                {{-- No Results State --}}
                                <div id="autocompleteNoResults" class="hidden py-4 text-center">
                                    <p class="text-xs text-gray-500">No articles or tags found</p>
                                </div>

                                {{-- Articles Section --}}
                                <div id="articlesSection" class="hidden mb-4">
                                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Artikel</h3>
                                    <div id="articlesCandidates" class="space-y-2"></div>
                                </div>

                                {{-- Tags Section --}}
                                <div id="tagsSection" class="hidden">
                                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Tags</h3>
                                    <div id="tagsCandidates" class="flex flex-wrap gap-1.5"></div>
                                </div>

                            </div>

                            {{-- Popular Tags Section --}}
                            @if($popularTags && $popularTags->count() > 0)
                                <div id="popularTagsSection" class="mb-6">
                                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Yang sedang ramai dicari</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($popularTags as $tag)
                                            <a href="{{ route('explore', ['tag' => $tag->slug]) }}"
                                               class="inline-flex px-2.5 md:px-3 py-1 md:py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-black transition-all duration-150 whitespace-nowrap">
                                                {{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Recent Searches Section --}}
                            <div id="recentSearchesContainer">
                                {{-- Populated by JavaScript --}}
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right Section --}}
        <div class="hidden md:flex items-center space-x-3">

            {{-- Guest (belum login) --}}
            @guest
                <a href="{{ route('auth') }}"
                   class="relative inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-gray-900 to-black rounded-full shadow-md hover:shadow-lg hover:scale-[1.03] active:scale-95 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2">
                    Login
                </a>
            @endguest

            {{-- Logged in (writer) --}}
            @auth
                {{-- Write Button --}}
                <a href="/writer/dashboard"
                   class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-black hover:bg-gray-100/80 rounded-full transition-all duration-200"
                   title="Write Article">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                    Write
                </a>

                {{-- Notification Bell --}}
                @php
                    $user = auth()->user();
                    $unreadCount = $user->notifications()->whereNull('read_at')->count();
                    $notifications = $user->notifications()->latest()->take(10)->get();
                @endphp
                <x-notification-dropdown :unreadCount="$unreadCount" :notifications="$notifications" />

                {{-- User name dengan avatar --}}
                <a href="{{ route('profile', auth()->user()->slug) }}" 
                   class="flex items-center gap-2 pl-1.5 pr-4 py-1.5 text-sm font-medium text-gray-800 hover:bg-gray-100/80 rounded-full transition-all duration-200">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}"
                             alt="{{ auth()->user()->name }}"
                             class="w-7 h-7 rounded-full object-cover" />
                    @else
                        <span class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center text-xs font-bold text-gray-600">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    @endif
                    {{ auth()->user()->name }}
                </a>

                {{-- Admin Link (kalau role admin/superadmin) --}}
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                    <a href="/admin/dashboard"
                       class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-black hover:bg-gray-100/80 rounded-full transition-all duration-200">
                        Admin
                    </a>
                @endif

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="px-3 py-2 text-sm font-medium text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-all duration-200">
                        Logout
                    </button>
                </form>
            @endauth

        </div>

        {{-- Mobile Menu Button --}}
        <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-900 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="hidden md:hidden px-6 pb-6 border-t border-gray-100 bg-white/95 backdrop-blur-xl transition-all duration-300">
        <div class="flex flex-col space-y-1 pt-4">
            <a href="/explore" class="px-4 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition-colors">Explore</a>
            <a href="/about" class="px-4 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition-colors">About</a>

            @guest
                <a href="{{ route('auth') }}" class="mt-2 text-sm bg-black text-white px-4 py-3 rounded-xl text-center font-medium hover:bg-gray-800 transition-colors">Login</a>
            @endguest

            @auth
                <a href="/writer/dashboard"
                class="px-4 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-gray-100 transition-colors">
                    Write
                </a>

                <a href="{{ route('notifications.index') }}"
                class="px-4 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-gray-100 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Notifications
                    @php
                        $mobileUnreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
                    @endphp
                    @if($mobileUnreadCount > 0)
                        <span class="ml-auto bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $mobileUnreadCount > 99 ? '99+' : $mobileUnreadCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('profile', auth()->user()->slug) }}"
                class="px-4 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-gray-100 transition-colors">
                    Profile
                </a>

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                    <a href="/admin/dashboard"
                    class="px-4 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-gray-100 transition-colors">
                        Admin
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-3 text-sm font-medium text-gray-400 rounded-xl hover:bg-red-50 hover:text-red-500 transition-colors">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        document.getElementById('mobile-menu')?.classList.toggle('hidden');
    });

    /**
     * Search Panel Management
     */
    const searchPanel = document.getElementById('searchPanel');
    const navbarSearch = document.getElementById('navbarSearch');
    const recentSearchesContainer = document.getElementById('recentSearchesContainer');
    const RECENT_SEARCHES_KEY = 'navbar_recent_searches';
    const MAX_RECENT_SEARCHES = 5;

    // Autocomplete state
    let autocompleteDebounceTimer = null;
    const AUTOCOMPLETE_DEBOUNCE_MS = 300;
    const MIN_QUERY_LENGTH = 2;

    /**
     * Open search panel
     */
    function openSearchPanel() {
        searchPanel.classList.remove('hidden');
        renderRecentSearches();
    }

    /**
     * Fetch autocomplete suggestions from API
     */
    function fetchAutocompleteSuggestions(query) {
        const autocompleteResults = document.getElementById('autocompleteResults');
        const autocompleteLoading = document.getElementById('autocompleteLoading');
        const autocompleteNoResults = document.getElementById('autocompleteNoResults');
        const articlesSection = document.getElementById('articlesSection');
        const tagsSection = document.getElementById('tagsSection');
        const popularTagsSection = document.getElementById('popularTagsSection');
        const recentSearchesContainer = document.getElementById('recentSearchesContainer');

        if (query.trim().length < MIN_QUERY_LENGTH) {
            // Query too short - show discovery state (popular tags + recent searches)
            autocompleteResults.classList.add('hidden');
            popularTagsSection.classList.remove('hidden');
            recentSearchesContainer.classList.remove('hidden');
            renderRecentSearches();
            return;
        }

        // Query long enough - hide discovery state, show autocomplete
        popularTagsSection.classList.add('hidden');
        recentSearchesContainer.classList.add('hidden');
        
        // Show loading state
        autocompleteResults.classList.remove('hidden');
        autocompleteLoading.classList.remove('hidden');
        autocompleteNoResults.classList.add('hidden');
        articlesSection.classList.add('hidden');
        tagsSection.classList.add('hidden');

        fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                autocompleteLoading.classList.add('hidden');

                const hasArticles = data.articles && data.articles.length > 0;
                const hasTags = data.tags && data.tags.length > 0;

                if (!hasArticles && !hasTags) {
                    autocompleteNoResults.classList.remove('hidden');
                    articlesSection.classList.add('hidden');
                    tagsSection.classList.add('hidden');
                    return;
                }

                // Render articles
                if (hasArticles) {
                    const articlesCandidates = document.getElementById('articlesCandidates');
                    articlesCandidates.innerHTML = data.articles.map(article => `
                        <a href="/blog/${article.slug}"
                           class="flex items-start gap-2 px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm text-gray-700 hover:bg-gray-100 rounded transition-colors duration-150"
                           onclick="addRecentSearch('${article.title.replace(/'/g, "\\'")}')">
                            ${article.thumbnail ? `<img src="${article.thumbnail}" alt="${article.title}" class="w-6 h-6 md:w-8 md:h-8 rounded object-cover flex-shrink-0" />` : ''}
                            <div class="flex-1 min-w-0">
                                <div class="font-medium truncate">${escapeHtml(article.title)}</div>
                                <div class="text-gray-500 text-xs">${escapeHtml(article.category || 'Uncategorized')}</div>
                            </div>
                        </a>
                    `).join('');
                    articlesSection.classList.remove('hidden');
                } else {
                    articlesSection.classList.add('hidden');
                }

                // Render tags
                if (hasTags) {
                    const tagsCandidates = document.getElementById('tagsCandidates');
                    tagsCandidates.innerHTML = data.tags.map(tag => `
                        <a href="/explore?tag=${tag.slug}"
                           class="inline-flex px-2 md:px-2.5 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors duration-150">
                            ${escapeHtml(tag.name)}
                        </a>
                    `).join('');
                    tagsSection.classList.remove('hidden');
                } else {
                    tagsSection.classList.add('hidden');
                }

                autocompleteNoResults.classList.add('hidden');
            })
            .catch(error => {
                console.error('Autocomplete error:', error);
                autocompleteLoading.classList.add('hidden');
                autocompleteNoResults.classList.remove('hidden');
            });
    }

    /**
     * Handle input with debounce
     */
    function handleAutocompleteInput(event) {
        clearTimeout(autocompleteDebounceTimer);
        const query = event.target.value;
        autocompleteDebounceTimer = setTimeout(() => {
            fetchAutocompleteSuggestions(query);
        }, AUTOCOMPLETE_DEBOUNCE_MS);
    }

    /**
     * Close search panel
     */
    function closeSearchPanel() {
        searchPanel.classList.add('hidden');
    }

    /**
     * Get recent searches from localStorage
     */
    function getRecentSearches() {
        const stored = localStorage.getItem(RECENT_SEARCHES_KEY);
        return stored ? JSON.parse(stored) : [];
    }

    /**
     * Add search to recent searches
     */
    function addRecentSearch(query) {
        if (!query || query.trim() === '') return;

        let searches = getRecentSearches();
        const cleanQuery = query.trim();

        // Remove duplicate if exists
        searches = searches.filter(s => s !== cleanQuery);

        // Add to top
        searches.unshift(cleanQuery);

        // Keep only max items
        searches = searches.slice(0, MAX_RECENT_SEARCHES);

        localStorage.setItem(RECENT_SEARCHES_KEY, JSON.stringify(searches));
        renderRecentSearches();
    }

    /**
     * Clear all recent searches
     */
    function clearRecentSearches() {
        localStorage.removeItem(RECENT_SEARCHES_KEY);
        renderRecentSearches();
    }

    /**
     * Render recent searches in panel
     */
    function renderRecentSearches() {
        const searches = getRecentSearches();
        
        if (searches.length === 0) {
            recentSearchesContainer.innerHTML = '';
            return;
        }

        let html = `
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pencarian terbaru</h3>
                    <button type="button" onclick="clearRecentSearches()" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                        Clear
                    </button>
                </div>
                <div class="space-y-1.5 md:space-y-2">
        `;

        searches.forEach(search => {
            const encodedSearch = encodeURIComponent(search);
            html += `
                <a href="/explore?search=${encodedSearch}"
                   class="flex items-center gap-2 px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm text-gray-700 hover:bg-gray-100 rounded transition-colors duration-150">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="truncate">${escapeHtml(search)}</span>
                </a>
            `;
        });

        html += `
                </div>
            </div>
        `;

        recentSearchesContainer.innerHTML = html;
    }

    /**
     * Escape HTML special characters
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Handle search form submission on Enter key or button click
     */
    function handleNavbarSearchKeypress(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            submitNavbarSearch();
        }
    }

    /**
     * Submit navbar search (called from Enter key or button click)
     */
    function submitNavbarSearch() {
        const searchValue = document.getElementById('navbarSearch').value.trim();
        if (searchValue) {
            addRecentSearch(searchValue);
            document.getElementById('navbarSearchForm').submit();
        }
    }

    /**
     * Attach click handler to search button
     */
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('navbarSearchForm');
        const searchBtn = searchForm.querySelector('button[type="submit"]');
        if (searchBtn) {
            searchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                submitNavbarSearch();
            });
        }
        renderRecentSearches();
    });

    /**
     * Close panel when clicking outside
     */
    document.addEventListener('click', function(event) {
        if (!navbarSearch.contains(event.target) && !searchPanel.contains(event.target)) {
            closeSearchPanel();
        }
    });

    /**
     * Close panel on Escape key
     */
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeSearchPanel();
        }
    });

    /**
     * Prevent panel from closing when clicking inside it
     */
    searchPanel.addEventListener('click', function(event) {
        event.stopPropagation();
    });
</script>