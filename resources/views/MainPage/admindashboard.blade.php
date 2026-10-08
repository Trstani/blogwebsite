<x-layouts.app title="Admin Dashboard">

    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 sm:py-8">

        <div class="flex items-center justify-between mb-6 sm:mb-8">
            <div>
                <h1 class="text-2xl font-bold text-black sm:text-3xl">Admin Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Manage articles, users, taxonomy, and settings</p>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- =========================================================
             TAB NAVIGATION — horizontal scroll slider on mobile
             ========================================================= --}}
        <div class="mb-6 border-b border-gray-200 sm:mb-8">
            <div class="overflow-x-auto scroll-smooth
                        [&::-webkit-scrollbar]:hidden
                        [-ms-overflow-style:none]
                        [scrollbar-width:none]">
                <div class="flex min-w-max items-center gap-6 snap-x snap-mandatory sm:gap-8">

                    <a href="{{ route('admin.dashboard', ['tab' => 'overview']) }}"
                       class="tab-button snap-start whitespace-nowrap pb-4 text-sm font-medium transition-colors {{ $activeTab === 'overview' ? 'text-gray-900 border-b-2 border-black' : 'text-gray-400 border-b-2 border-transparent hover:text-black' }}"
                       data-tab="overview">
                        Overview
                    </a>
                    <a href="{{ route('admin.dashboard', ['tab' => 'content']) }}"
                       class="tab-button snap-start whitespace-nowrap pb-4 text-sm font-medium transition-colors {{ $activeTab === 'content' ? 'text-gray-900 border-b-2 border-black' : 'text-gray-400 border-b-2 border-transparent hover:text-black' }}"
                       data-tab="content">
                        Content
                    </a>
                    <a href="{{ route('admin.dashboard', ['tab' => 'taxonomy']) }}"
                       class="tab-button snap-start whitespace-nowrap pb-4 text-sm font-medium transition-colors {{ $activeTab === 'taxonomy' ? 'text-gray-900 border-b-2 border-black' : 'text-gray-400 border-b-2 border-transparent hover:text-black' }}"
                       data-tab="taxonomy">
                        Taxonomy
                    </a>
                    @if(auth()->user()->role === 'super_admin')
                    <a href="{{ route('admin.dashboard', ['tab' => 'users']) }}"
                       class="tab-button snap-start whitespace-nowrap pb-4 text-sm font-medium transition-colors {{ $activeTab === 'users' ? 'text-gray-900 border-b-2 border-black' : 'text-gray-400 border-b-2 border-transparent hover:text-black' }}"
                       data-tab="users">
                        Users
                    </a>
                    @endif
                    <a href="{{ route('admin.dashboard', ['tab' => 'settings']) }}"
                       class="tab-button snap-start whitespace-nowrap pb-4 text-sm font-medium transition-colors {{ $activeTab === 'settings' ? 'text-gray-900 border-b-2 border-black' : 'text-gray-400 border-b-2 border-transparent hover:text-black' }}"
                       data-tab="settings">
                        Settings
                    </a>

                </div>
            </div>
        </div>

        {{-- ========== OVERVIEW TAB ========== --}}
        <div id="overview" class="tab-content {{ $activeTab !== 'overview' ? 'hidden' : '' }}">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-lg p-4 sm:p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Total Users</p>
                    <p class="text-xl sm:text-2xl font-bold text-black mt-1">{{ $totalUsers }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-lg p-4 sm:p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Published</p>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600 mt-1">{{ $published }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-lg p-4 sm:p-5 col-span-2 md:col-span-1">
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Pending Review</p>
                    <p class="text-xl sm:text-2xl font-bold text-yellow-600 mt-1">{{ $pending }}</p>
                </div>
            </div>

            {{-- Pending by Category --}}
            <div class="bg-white rounded-lg border border-gray-100 p-4 sm:p-6 mb-8">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                    Pending by Category
                </h2>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.dashboard', ['tab' => 'overview']) }}"
                    class="px-4 py-2 text-xs font-medium rounded-full {{ !$filterCategory ? 'bg-black text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">
                        All ({{ $pending }})
                    </a>
                    @foreach($categories as $cat)
                        @php
                            $count = $pendingByCategory->get($cat->name, collect())->count();
                        @endphp
                        <a href="{{ route('admin.dashboard', ['tab' => 'overview', 'category' => $cat->slug]) }}"
                        class="px-4 py-2 text-xs font-medium rounded-full {{ ($filterCategory ?? '') === $cat->slug ? 'bg-black text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">
                            {{ $cat->name }} ({{ $count }})
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Search Pending Articles --}}
            <div class="mb-6">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-col gap-2 sm:flex-row">
                    <input type="hidden" name="tab" value="overview">
                    <input type="text"
                           name="article_search"
                           value="{{ $articleSearch }}"
                           placeholder="Search articles by title..."
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-black transition-colors sm:flex-1">
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium bg-black text-white rounded-lg hover:bg-gray-800 transition-colors sm:flex-initial">
                            Search
                        </button>
                        @if($articleSearch)
                            <a href="{{ route('admin.dashboard', ['tab' => 'overview']) }}" class="flex-1 text-center px-4 py-2.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors sm:flex-initial">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Pending Articles List --}}
            <div class="bg-white rounded-lg border border-gray-100 p-4 sm:p-6 mb-8">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                    Articles Awaiting Review
                </h2>

                @if($pendingArticles->count() > 0)
                    <div class="space-y-4">
                        @foreach($pendingArticles as $article)
                            <div class="flex flex-col gap-3 p-3 border border-gray-100 rounded-lg hover:border-gray-200 transition-colors sm:flex-row sm:items-start sm:gap-4 sm:p-4">

                                {{-- Thumbnail + Content --}}
                                <div class="flex gap-3 flex-1 min-w-0 sm:gap-4">
                                    <div class="flex-shrink-0 w-20 sm:w-24 aspect-[4/3] bg-gray-100 rounded-md overflow-hidden">
                                        @if($article->cover_image)
                                            <img src="{{ imageUrl($article->cover_image) }}" class="w-full h-full object-cover" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <span class="text-gray-300 text-2xl">—</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">
                                                {{ $article->category->name ?? 'Uncategorized' }}
                                            </span>
                                            <span class="text-[10px] font-medium text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full">
                                                Pending
                                            </span>
                                        </div>
                                        <h3 class="text-base font-semibold text-black leading-snug">
                                            <a href="/blog/{{ $article->slug }}" target="_blank" class="hover:text-blue-600 transition-colors">
                                                {{ $article->title }}
                                                <svg class="inline w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $article->description ?? 'No description' }}</p>
                                        <div class="mt-2 flex items-center space-x-3 text-xs text-gray-400">
                                            <span>{{ $article->author->name ?? 'Unknown' }}</span>
                                            <span>·</span>
                                            <span>{{ $article->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2 flex-shrink-0 sm:self-start">
                                    <form method="POST" action="{{ route('admin.approve', $article) }}" class="flex-1 sm:flex-initial">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-4 py-2 text-xs font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                    <button type="button"
                                            onclick="openRejectModal({{ $article->id }}, '{{ addslashes($article->title) }}')"
                                            class="flex-1 sm:flex-initial px-4 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pending Articles Pagination --}}
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-gray-400">
                            Showing <span class="font-medium">{{ $pendingArticles->firstItem() }}</span> to <span class="font-medium">{{ $pendingArticles->lastItem() }}</span> of <span class="font-medium">{{ $pendingArticles->total() }}</span> pending articles
                        </p>
                        <div class="flex items-center gap-2">
                            @if($pendingArticles->onFirstPage())
                                <span class="px-3 py-2 text-xs text-gray-300 border border-gray-200 rounded-lg">← Previous</span>
                            @else
                                <a href="{{ $pendingArticles->appends(request()->query())->appends(['tab' => 'overview'])->previousPageUrl() }}" class="px-3 py-2 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors">← Previous</a>
                            @endif

                            @if($pendingArticles->hasMorePages())
                                <a href="{{ $pendingArticles->appends(request()->query())->appends(['tab' => 'overview'])->nextPageUrl() }}" class="px-3 py-2 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors">Next →</a>
                            @else
                                <span class="px-3 py-2 text-xs text-gray-300 border border-gray-200 rounded-lg">Next →</span>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-8">
                        @if($articleSearch || $filterCategory)
                            No pending articles found matching your filters.
                        @else
                            No articles pending review. All caught up!
                        @endif
                    </p>
                @endif
            </div>

        </div>

        {{-- ========== CONTENT TAB ========== --}}
        <div id="content" class="tab-content {{ $activeTab !== 'content' ? 'hidden' : '' }}">

            {{-- Search + Filter Bar for Published Articles --}}
            <div class="mb-6">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-col gap-2 sm:flex-row">
                    <input type="hidden" name="tab" value="content">
                    <input type="text"
                           name="article_search"
                           value="{{ $articleSearch }}"
                           placeholder="Search published articles by title..."
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-black transition-colors sm:flex-1">
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium bg-black text-white rounded-lg hover:bg-gray-800 transition-colors sm:flex-initial">
                            Search
                        </button>
                        @if($articleSearch)
                            <a href="{{ route('admin.dashboard', ['tab' => 'content']) }}" class="flex-1 text-center px-4 py-2.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors sm:flex-initial">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Published Articles (for featuring) --}}
            <div class="bg-white rounded-lg border border-gray-100 p-4 sm:p-6 mb-8">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                    Published Articles — Feature Management
                </h2>

                @if($publishedArticles->count() > 0)
                    <div class="space-y-3">
                        @foreach($publishedArticles as $article)
                            <div class="flex flex-col gap-3 p-3 border border-gray-100 rounded-lg sm:flex-row sm:items-center sm:justify-between sm:p-4">

                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-16 h-12 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                        @if($article->cover_image)
                                            <img src="{{ imageUrl($article->cover_image) }}" class="w-full h-full object-cover" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <span class="text-gray-300 text-lg">—</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-medium text-black line-clamp-1">{{ $article->title }}</h3>
                                        <p class="text-xs text-gray-400 truncate">{{ $article->category->name ?? '' }} · {{ $article->author->name ?? '' }}</p>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.feature', $article) }}" class="flex-shrink-0">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-3 py-1.5 text-xs font-medium rounded-md transition-colors sm:w-auto
                                                {{ $article->is_featured ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        {{ $article->is_featured ? '★ Featured' : '☆ Feature' }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    {{-- Published Articles Pagination --}}
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-gray-400">
                            Showing <span class="font-medium">{{ $publishedArticles->firstItem() }}</span> to <span class="font-medium">{{ $publishedArticles->lastItem() }}</span> of <span class="font-medium">{{ $publishedArticles->total() }}</span> published articles
                        </p>
                        <div class="flex items-center gap-2">
                            @if($publishedArticles->onFirstPage())
                                <span class="px-3 py-2 text-xs text-gray-300 border border-gray-200 rounded-lg">← Previous</span>
                            @else
                                <a href="{{ $publishedArticles->appends(request()->query())->appends(['tab' => 'content'])->previousPageUrl() }}" class="px-3 py-2 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors">← Previous</a>
                            @endif

                            @if($publishedArticles->hasMorePages())
                                <a href="{{ $publishedArticles->appends(request()->query())->appends(['tab' => 'content'])->nextPageUrl() }}" class="px-3 py-2 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors">Next →</a>
                            @else
                                <span class="px-3 py-2 text-xs text-gray-300 border border-gray-200 rounded-lg">Next →</span>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-8">
                        @if($articleSearch)
                            No published articles found matching your search.
                        @else
                            No published articles yet.
                        @endif
                    </p>
                @endif
            </div>

        </div>

        {{-- ========== TAXONOMY TAB ========== --}}
        <div id="taxonomy" class="tab-content {{ $activeTab !== 'taxonomy' ? 'hidden' : '' }}">

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                <div class="bg-white rounded-lg border border-gray-100 p-4 sm:p-6">

                    {{-- Header --}}
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">
                                Tag Management
                            </h2>
                            <p class="text-xs text-gray-400 mt-1">
                                Manage article tags and topics.
                            </p>
                        </div>
                        <button onclick="openTagModal()"
                           class="w-full px-4 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors sm:w-auto">
                            + Add Tag
                        </button>
                    </div>

                    {{-- Tags List --}}
                    <div id="tagsContainer">
                        @if($tags->count() > 0)
                            <div class="space-y-3">
                                @foreach($tags as $tag)
                                    <div class="tag-item flex items-center justify-between gap-3 p-3 border border-gray-100 rounded-lg hover:border-gray-200 transition-colors sm:p-4" data-tag-id="{{ $tag->id }}">

                                        {{-- Tag Info --}}
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-black truncate">
                                                {{ $tag->name }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ $tag->articles_count }} article{{ $tag->articles_count !== 1 ? 's' : '' }}
                                            </p>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <button type="button" onclick="editTag({{ $tag->id }}, '{{ addslashes($tag->name) }}')"
                                               class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                                Edit
                                            </button>

                                            <button type="button" onclick="deleteTag({{ $tag->id }})"
                                                    class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 text-center py-8">
                                No tags yet. <button type="button" onclick="openTagModal()" class="text-black hover:underline">Create one</button>.
                            </p>
                        @endif
                    </div>

                </div>
            @endif

            {{-- Tag Modal --}}
            <div id="tagModal" class="fixed inset-0 z-50 hidden items-center justify-center">
                <div class="absolute inset-0 bg-black/40" onclick="closeTagModal()"></div>
                <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-5 sm:p-8">
                    <h2 class="text-xl font-bold text-black mb-4" id="tagModalTitle">Create Tag</h2>

                    <form id="tagForm" onsubmit="submitTagForm(event)">
                        @csrf
                        <input type="hidden" id="tagId" name="tag_id" value="">

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-900 mb-2">Tag Name</label>
                            <input type="text"
                                   id="tagName"
                                   name="name"
                                   placeholder="e.g., Artificial Intelligence"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors"
                                   required>
                            <p id="tagNameError" class="text-xs text-red-600 mt-2 hidden"></p>
                        </div>

                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" onclick="closeTagModal()"
                                    class="w-full px-5 py-2.5 text-sm text-gray-500 border border-gray-200 rounded-lg hover:border-black hover:text-black transition-colors sm:w-auto">
                                Cancel
                            </button>
                            <button type="submit" id="tagSubmitBtn"
                                    class="w-full px-5 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto">
                                Create Tag
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- ========== USERS TAB ========== --}}
        <div id="users" class="tab-content {{ $activeTab !== 'users' ? 'hidden' : '' }}">

            @if(auth()->user()->role === 'super_admin')
                <div class="bg-white rounded-lg border border-gray-100 p-4 sm:p-6">

                    {{-- Header --}}
                    <div class="mb-5">
                        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">
                            User Management
                        </h2>
                        <p class="text-xs text-gray-400 mt-1">
                            View administrators and users on the website.
                        </p>
                    </div>

                    {{-- Nested Sub-tabs --}}
                    <div class="border-b border-gray-100 mb-5">
                        <div class="flex items-center min-w-max">
                            <a href="{{ route('admin.dashboard', ['tab' => 'users', 'user_type' => 'users']) }}"
                               class="user-type-tab whitespace-nowrap px-4 py-3 text-xs font-medium border-b-2 transition-colors {{ $userType === 'users' ? 'text-black border-b-black' : 'text-gray-400 border-b-transparent hover:text-black' }}"
                               data-type="users">
                                Users
                                <span class="ml-1.5 text-gray-400">
                                    ({{ $writers->total() }})
                                </span>
                            </a>

                            <a href="{{ route('admin.dashboard', ['tab' => 'users', 'user_type' => 'admins']) }}"
                               class="user-type-tab whitespace-nowrap px-4 py-3 text-xs font-medium border-b-2 transition-colors {{ $userType === 'admins' ? 'text-black border-b-black' : 'text-gray-400 border-b-transparent hover:text-black' }}"
                               data-type="admins">
                                Admins
                                <span class="ml-1.5 text-gray-400">
                                    ({{ $admins->total() }})
                                </span>
                            </a>
                        </div>
                    </div>

                    {{-- USERS SUB-TAB --}}
                    <div id="usersSubTab" class="user-type-content {{ $userType !== 'users' ? 'hidden' : '' }}">
                        <div class="mb-5">
                            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <input type="hidden" name="tab" value="users">
                                <input type="hidden" name="user_type" value="users">
                                <input type="text"
                                       name="user_search"
                                       value="{{ $userSearch }}"
                                       placeholder="Search by name, email, or username..."
                                       class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-black transition-colors sm:flex-1">
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium bg-black text-white rounded-lg hover:bg-gray-800 transition-colors sm:flex-initial">
                                        Search
                                    </button>
                                    @if($userSearch)
                                        <a href="{{ route('admin.dashboard', ['tab' => 'users', 'user_type' => 'users']) }}" class="flex-1 text-center px-4 py-2.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors sm:flex-initial">
                                            Clear
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>

                        @if($writers->count() > 0)
                            <div class="space-y-3">
                                @foreach($writers as $writer)
                                    <div class="flex flex-col gap-3 p-3 border border-gray-100 rounded-lg hover:border-gray-200 transition-colors sm:flex-row sm:items-center sm:justify-between sm:p-4">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                                @if($writer->avatar)
                                                    <img src="{{ imageUrl($writer->avatar) }}" alt="{{ $writer->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-sm font-medium text-gray-600">
                                                        {{ strtoupper(substr($writer->name, 0, 1)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-black truncate">
                                                    {{ $writer->name }}
                                                </p>
                                                <p class="text-xs text-gray-400 truncate">
                                                    {{ $writer->email }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 flex-wrap sm:flex-shrink-0 sm:flex-nowrap">
                                            <span class="text-xs text-gray-400 hidden sm:block">
                                                {{ $writer->articles()->count() }} articles
                                            </span>
                                            <span class="text-[10px] font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                                User
                                            </span>
                                            @if(auth()->user()->role === 'super_admin')
                                                <form method="POST" action="{{ route('admin.promote', $writer) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-black rounded-md hover:bg-gray-800 transition-colors">
                                                        Promote to Admin
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Users Pagination --}}
                            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs text-gray-400">
                                    Showing <span class="font-medium">{{ $writers->firstItem() }}</span> to <span class="font-medium">{{ $writers->lastItem() }}</span> of <span class="font-medium">{{ $writers->total() }}</span> users
                                </p>
                                <div class="flex items-center gap-2">
                                    @if($writers->onFirstPage())
                                        <span class="px-3 py-2 text-xs text-gray-300 border border-gray-200 rounded-lg">← Previous</span>
                                    @else
                                        <a href="{{ $writers->appends(request()->query())->appends(['tab' => 'users', 'user_type' => 'users'])->previousPageUrl() }}" class="px-3 py-2 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors">← Previous</a>
                                    @endif

                                    @if($writers->hasMorePages())
                                        <a href="{{ $writers->appends(request()->query())->appends(['tab' => 'users', 'user_type' => 'users'])->nextPageUrl() }}" class="px-3 py-2 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors">Next →</a>
                                    @else
                                        <span class="px-3 py-2 text-xs text-gray-300 border border-gray-200 rounded-lg">Next →</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-400 text-center py-8">
                                @if($userSearch)
                                    No users found matching your search.
                                @else
                                    No users found.
                                @endif
                            </p>
                        @endif
                    </div>

                    {{-- ADMINS SUB-TAB --}}
                    <div id="adminsSubTab" class="user-type-content {{ $userType !== 'admins' ? 'hidden' : '' }}">
                        <div class="mb-5">
                            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <input type="hidden" name="tab" value="users">
                                <input type="hidden" name="user_type" value="admins">
                                <input type="text"
                                       name="admin_search"
                                       value="{{ $adminSearch }}"
                                       placeholder="Search by name, email, or username..."
                                       class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-black transition-colors sm:flex-1">
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium bg-black text-white rounded-lg hover:bg-gray-800 transition-colors sm:flex-initial">
                                        Search
                                    </button>
                                    @if($adminSearch)
                                        <a href="{{ route('admin.dashboard', ['tab' => 'users', 'user_type' => 'admins']) }}" class="flex-1 text-center px-4 py-2.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors sm:flex-initial">
                                            Clear
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>

                        @if($admins->count() > 0)
                            <div class="space-y-3">
                                @foreach($admins as $admin)
                                    <div class="flex flex-col gap-3 p-3 border border-gray-100 rounded-lg hover:border-gray-200 transition-colors sm:flex-row sm:items-center sm:justify-between sm:p-4">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                                @if($admin->avatar)
                                                    <img src="{{ imageUrl($admin->avatar) }}" alt="{{ $admin->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-sm font-medium text-gray-600">
                                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <p class="text-sm font-medium text-black truncate">
                                                        {{ $admin->name }}
                                                    </p>
                                                    @if($admin->id === auth()->id())
                                                        <span class="text-[10px] font-medium text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">
                                                            You
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-gray-400 truncate">
                                                    {{ $admin->email }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 flex-wrap sm:flex-shrink-0 sm:flex-nowrap">
                                            <span class="text-xs text-gray-400 hidden sm:block">
                                                {{ $admin->articles()->count() }} articles
                                            </span>
                                            <span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">
                                                Admin
                                            </span>
                                            @if(auth()->user()->role === 'super_admin' && auth()->id() !== $admin->id)
                                                <form method="POST" action="{{ route('admin.demote', $admin) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                                                        Demote to User
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Admins Pagination --}}
                            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs text-gray-400">
                                    Showing <span class="font-medium">{{ $admins->firstItem() }}</span> to <span class="font-medium">{{ $admins->lastItem() }}</span> of <span class="font-medium">{{ $admins->total() }}</span> admins
                                </p>
                                <div class="flex items-center gap-2">
                                    @if($admins->onFirstPage())
                                        <span class="px-3 py-2 text-xs text-gray-300 border border-gray-200 rounded-lg">← Previous</span>
                                    @else
                                        <a href="{{ $admins->appends(request()->query())->appends(['tab' => 'users', 'user_type' => 'admins'])->previousPageUrl() }}" class="px-3 py-2 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors">← Previous</a>
                                    @endif

                                    @if($admins->hasMorePages())
                                        <a href="{{ $admins->appends(request()->query())->appends(['tab' => 'users', 'user_type' => 'admins'])->nextPageUrl() }}" class="px-3 py-2 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors">Next →</a>
                                    @else
                                        <span class="px-3 py-2 text-xs text-gray-300 border border-gray-200 rounded-lg">Next →</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-400 text-center py-8">
                                @if($adminSearch)
                                    No admins found matching your search.
                                @else
                                    No admins found.
                                @endif
                            </p>
                        @endif
                    </div>

                </div>
            @endif

        </div>

        {{-- ========== SETTINGS TAB ========== --}}
        <div id="settings" class="tab-content {{ $activeTab !== 'settings' ? 'hidden' : '' }}">

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                <div class="bg-white rounded-lg border border-gray-100 p-4 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">
                                Legal Pages
                            </h2>
                            <p class="text-xs text-gray-400 mt-1">
                                Manage the website's legal and privacy documents.
                            </p>
                        </div>

                        <span class="self-start text-[10px] font-medium text-[#159AA3] bg-cyan-50 px-2.5 py-1 rounded-full sm:self-auto">
                            Admin Only
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        {{-- Privacy Policy --}}
                        <a
                            href="{{ route('admin.legal-pages.edit', 'privacy_policy') }}"
                            class="group flex items-center justify-between border border-gray-100 rounded-lg p-4 hover:border-cyan-200 hover:bg-cyan-50/30 transition-all"
                        >
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-[#159AA3] transition-colors">
                                    Privacy Policy
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    Manage privacy and data protection information.
                                </p>
                            </div>

                            <span class="text-lg text-gray-300 group-hover:text-[#159AA3] transition-colors flex-shrink-0 ml-3">
                                →
                            </span>
                        </a>

                        {{-- Legal Notice --}}
                        <a
                            href="{{ route('admin.legal-pages.edit', 'legal_notice') }}"
                            class="group flex items-center justify-between border border-gray-100 rounded-lg p-4 hover:border-cyan-200 hover:bg-cyan-50/30 transition-all"
                        >
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-[#159AA3] transition-colors">
                                    Legal Notice
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    Manage legal information and website notices.
                                </p>
                            </div>

                            <span class="text-lg text-gray-300 group-hover:text-[#159AA3] transition-colors flex-shrink-0 ml-3">
                                →
                            </span>
                        </a>

                    </div>
                </div>
            @endif

        </div>

    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="hidden fixed inset-0 z-50 items-center justify-center">
        <div class="absolute inset-0 bg-black/40" onclick="closeRejectModal()"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-5 sm:p-8">
            <h2 class="text-xl font-bold text-black mb-1">Reject Article</h2>
            <p class="text-sm text-gray-500 mb-4">Article: <strong id="rejectArticleTitle"></strong></p>

            <form method="POST" id="rejectForm">
                @csrf
                <textarea name="admin_notes"
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors resize-none mb-4"
                        placeholder="Tuliskan alasan reject & hal yang perlu diperbaiki..."
                        required></textarea>
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeRejectModal()"
                            class="w-full px-5 py-2.5 text-sm text-gray-500 border border-gray-200 rounded-lg hover:border-black hover:text-black transition-colors sm:w-auto">
                        Cancel
                    </button>
                    <button type="submit"
                            class="w-full px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors sm:w-auto">
                        Reject Article
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
            });

            // Show selected tab
            document.getElementById(tab).classList.remove('hidden');

            // Update tab button styles
            document.querySelectorAll('.tab-button').forEach(btn => {
                if (btn.dataset.tab === tab) {
                    btn.classList.add('border-black', 'text-gray-900');
                    btn.classList.remove('border-transparent', 'text-gray-400');
                } else {
                    btn.classList.add('border-transparent', 'text-gray-400');
                    btn.classList.remove('border-black', 'text-gray-900');
                }
            });
        }

        function switchUserTab(tab) {
            const adminsContent = document.getElementById('adminsContent');
            const usersContent = document.getElementById('usersContent');

            const adminsTab = document.getElementById('adminsTab');
            const usersTab = document.getElementById('usersTab');

            if (tab === 'admins') {
                adminsContent.classList.remove('hidden');
                usersContent.classList.add('hidden');

                adminsTab.classList.add('border-black', 'text-black');
                adminsTab.classList.remove('border-transparent', 'text-gray-400');

                usersTab.classList.add('border-transparent', 'text-gray-400');
                usersTab.classList.remove('border-black', 'text-black');
            }

            if (tab === 'users') {
                usersContent.classList.remove('hidden');
                adminsContent.classList.add('hidden');

                usersTab.classList.add('border-black', 'text-black');
                usersTab.classList.remove('border-transparent', 'text-gray-400');

                adminsTab.classList.add('border-transparent', 'text-gray-400');
                adminsTab.classList.remove('border-black', 'text-black');
            }
        }

        function openRejectModal(id, title) {
            document.getElementById('rejectArticleTitle').textContent = title;
            document.getElementById('rejectForm').action = '/admin/articles/' + id + '/reject';
            const modal = document.getElementById('rejectModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // ========== TAG MODAL FUNCTIONS ==========

        /**
         * Open tag modal for creating a new tag
         */
        function openTagModal() {
            const modal = document.getElementById('tagModal');
            const form = document.getElementById('tagForm');
            const tagId = document.getElementById('tagId');
            const tagName = document.getElementById('tagName');
            const title = document.getElementById('tagModalTitle');
            const submitBtn = document.getElementById('tagSubmitBtn');
            const error = document.getElementById('tagNameError');

            // Reset form for create mode
            form.reset();
            tagId.value = '';
            title.textContent = 'Create Tag';
            submitBtn.textContent = 'Create Tag';
            submitBtn.disabled = false;
            error.classList.add('hidden');
            tagName.focus();

            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        /**
         * Close tag modal
         */
        function closeTagModal() {
            const modal = document.getElementById('tagModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        /**
         * Edit existing tag - populate modal with data
         */
        function editTag(tagId, tagName) {
            const modal = document.getElementById('tagModal');
            const form = document.getElementById('tagForm');
            const tagIdInput = document.getElementById('tagId');
            const tagNameInput = document.getElementById('tagName');
            const title = document.getElementById('tagModalTitle');
            const submitBtn = document.getElementById('tagSubmitBtn');
            const error = document.getElementById('tagNameError');

            // Populate form
            tagIdInput.value = tagId;
            tagNameInput.value = tagName;
            title.textContent = 'Edit Tag';
            submitBtn.textContent = 'Save Changes';
            submitBtn.disabled = false;
            error.classList.add('hidden');
            tagNameInput.focus();

            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        /**
         * Submit tag form (create or update) via AJAX
         */
        function submitTagForm(event) {
            event.preventDefault();

            const form = document.getElementById('tagForm');
            const tagId = document.getElementById('tagId').value;
            const tagName = document.getElementById('tagName').value.trim();
            const submitBtn = document.getElementById('tagSubmitBtn');
            const error = document.getElementById('tagNameError');

            // Validate
            if (!tagName) {
                error.textContent = 'Tag name is required.';
                error.classList.remove('hidden');
                return;
            }

            error.classList.add('hidden');
            submitBtn.disabled = true;

            // Determine endpoint: create or update
            const url = tagId ? `/admin/tags/${tagId}` : '/admin/tags';
            const method = tagId ? 'PUT' : 'POST';

            // Prepare form data
            const formData = new FormData();
            formData.append('name', tagName);
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            if (tagId) {
                formData.append('_method', 'PUT');
            }

            // Make AJAX request
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw data;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    closeTagModal();
                    refreshTagsList();
                } else {
                    error.textContent = data.message || 'An error occurred.';
                    error.classList.remove('hidden');
                }
            })
            .catch(err => {
                // Handle validation errors
                if (err.errors && err.errors.name) {
                    error.textContent = err.errors.name[0];
                } else {
                    error.textContent = err.message || 'An error occurred. Please try again.';
                }
                error.classList.remove('hidden');
            })
            .finally(() => {
                submitBtn.disabled = false;
            });
        }

        /**
         * Delete a tag with confirmation
         */
        function deleteTag(tagId) {
            if (!confirm('Are you sure you want to delete this tag?')) {
                return;
            }

            const csrfToken = document.querySelector('input[name="_token"]').value;

            fetch(`/admin/tags/${tagId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw data;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    refreshTagsList();
                } else {
                    alert(data.message || 'Failed to delete tag.');
                }
            })
            .catch(err => {
                alert(err.message || 'An error occurred while deleting the tag.');
            });
        }

        /**
         * Refresh tags list from the server
         */
        function refreshTagsList() {
            // Reload the page to refresh all data and preserve session
            location.reload();
        }
    </script>

</x-layouts.app>