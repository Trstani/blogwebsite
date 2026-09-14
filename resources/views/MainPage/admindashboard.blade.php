<x-layouts.app title="Admin Dashboard">

    <div class="max-w-6xl mx-auto px-6 py-8">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-black">Admin Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Manage and view user activity</p>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white border border-gray-100 rounded-lg p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Total Users</p>
                <p class="text-2xl font-bold text-black mt-1">{{ $totalUsers }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-lg p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Online Now</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $onlineUsers }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-lg p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Published</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $published }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-lg p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Pending Review</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $pending }}</p>
            </div>
        </div>

        {{-- Pending Articles by Category --}}
        <div class="bg-white rounded-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                Pending by Category
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="/admin/dashboard"
                class="px-4 py-2 text-xs font-medium rounded-full {{ !$filterCategory ? 'bg-black text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">
                    All ({{ $pending }})
                </a>
                @foreach($categories as $cat)
                    @php
                        $count = $pendingByCategory->get($cat->name, collect())->count();
                    @endphp
                    <a href="/admin/dashboard?category={{ $cat->slug }}"
                    class="px-4 py-2 text-xs font-medium rounded-full {{ ($filterCategory ?? '') === $cat->slug ? 'bg-black text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">
                        {{ $cat->name }} ({{ $count }})
                    </a>
                @endforeach
            </div>
        </div>
        {{-- Pending Articles List --}}
        <div class="bg-white rounded-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                Articles to Review
            </h2>

            @if($pendingArticles->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingArticles as $article)
                        <div class="flex items-start gap-4 p-4 border border-gray-100 rounded-lg hover:border-gray-200 transition-colors">
                            <div class="flex-shrink-0 w-24 h-18 bg-gray-100 rounded-md overflow-hidden">
                                @if($article->cover_image)
                                    <img src="{{ imageUrl($article->cover_image) }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-gray-300 text-2xl">—</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
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

                            {{-- Actions --}}
                            <div class="flex-shrink-0 flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.approve', $article) }}">
                                    @csrf
                                    <button type="submit"
                                            class="px-4 py-2 text-xs font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors">
                                        Approve
                                    </button>
                                </form>
                                <button type="button"
                                        onclick="openRejectModal({{ $article->id }}, '{{ addslashes($article->title) }}')"
                                        class="px-4 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                    Reject
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-8">
                    @if($filterCategory)
                        No pending articles in this category.
                    @else
                        No articles pending review. All caught up!
                    @endif
                </p>
            @endif
        </div>
        {{-- Published Articles (for featuring) --}}
        <div class="bg-white rounded-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                Published Articles — Feature Management
            </h2>

            @php
                $publishedArticles = \App\Models\Article::where('status', 'published')
                    ->with('category', 'author')
                    ->latest()
                    ->get();
            @endphp

            @if($publishedArticles->count() > 0)
                <div class="space-y-3">
                    @foreach($publishedArticles as $article)
                        <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-12 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                    @if($article->cover_image)
                                        <img src="{{ imageUrl($article->cover_image) }}" class="w-full h-full object-cover" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="text-gray-300 text-lg">—</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-black">{{ $article->title }}</h3>
                                    <p class="text-xs text-gray-400">{{ $article->category->name ?? '' }} · {{ $article->author->name ?? '' }}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.feature', $article) }}">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors
                                            {{ $article->is_featured ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $article->is_featured ? '★ Featured' : '☆ Feature' }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-8">No published articles yet.</p>
            @endif
        </div>

        {{-- User Management --}}
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
            <div class="bg-white rounded-lg border border-gray-100 p-6">

                {{-- Header --}}
                <div class="mb-5">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">
                        User Management
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">
                        View administrators and users on the website.
                    </p>
                </div>

                {{-- Tabs --}}
                <div class="flex items-center border-b border-gray-100 mb-5">
                    <button
                        type="button"
                        id="adminsTab"
                        onclick="switchUserTab('admins')"
                        class="user-tab px-4 py-3 text-xs font-medium border-b-2 border-black text-black transition-colors"
                    >
                        Admins
                        <span class="ml-1.5 text-gray-400">
                            ({{ $admins->count() }})
                        </span>
                    </button>

                    <button
                        type="button"
                        id="usersTab"
                        onclick="switchUserTab('users')"
                        class="user-tab px-4 py-3 text-xs font-medium border-b-2 border-transparent text-gray-400 hover:text-black transition-colors"
                    >
                        Users
                        <span class="ml-1.5 text-gray-400">
                            ({{ $writers->count() }})
                        </span>
                    </button>
                </div>

                {{-- ==================== ADMINS TAB ==================== --}}
                <div id="adminsContent">

                    @if($admins->count() > 0)

                        <div class="space-y-3">

                            @foreach($admins as $admin)

                                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:border-gray-200 transition-colors">

                                    {{-- User Info --}}
                                    <div class="flex items-center gap-3 min-w-0">

                                        {{-- Avatar --}}
                                        <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden flex-shrink-0 flex items-center justify-center">

                                            @if($admin->avatar)
                                                <img
                                                    src="{{ imageUrl($admin->avatar) }}"
                                                    alt="{{ $admin->name }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @else
                                                <span class="text-sm font-medium text-gray-600">
                                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                                </span>
                                            @endif

                                        </div>

                                        {{-- Name + Email --}}
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

                                    {{-- Right Side --}}
                                    <div class="flex items-center gap-3 flex-shrink-0">

                                        <span class="text-xs text-gray-400 hidden sm:block">
                                            {{ $admin->articles()->count() }} articles
                                        </span>

                                        {{-- Role badge --}}
                                        <span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">
                                            Admin
                                        </span>

                                        {{-- SUPER ADMIN ONLY --}}
                                        @if(auth()->user()->role === 'super_admin' && auth()->id() !== $admin->id)

                                            <form
                                                method="POST"
                                                action="{{ route('admin.demote', $admin) }}"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-400 transition-colors"
                                                >
                                                    Demote to User
                                                </button>
                                            </form>

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <p class="text-sm text-gray-400 text-center py-8">
                            No admins found.
                        </p>

                    @endif

                </div>


                {{-- ==================== USERS TAB ==================== --}}
                <div id="usersContent" class="hidden">

                    @if($writers->count() > 0)

                        <div class="space-y-3">

                            @foreach($writers as $writer)

                                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:border-gray-200 transition-colors">

                                    {{-- User Info --}}
                                    <div class="flex items-center gap-3 min-w-0">

                                        {{-- Avatar --}}
                                        <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden flex-shrink-0 flex items-center justify-center">

                                            @if($writer->avatar)
                                                <img
                                                    src="{{ imageUrl($writer->avatar) }}"
                                                    alt="{{ $writer->name }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @else
                                                <span class="text-sm font-medium text-gray-600">
                                                    {{ strtoupper(substr($writer->name, 0, 1)) }}
                                                </span>
                                            @endif

                                        </div>

                                        {{-- Name + Email --}}
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-black truncate">
                                                {{ $writer->name }}
                                            </p>

                                            <p class="text-xs text-gray-400 truncate">
                                                {{ $writer->email }}
                                            </p>
                                        </div>

                                    </div>

                                    {{-- Right Side --}}
                                    <div class="flex items-center gap-3 flex-shrink-0">

                                        <span class="text-xs text-gray-400 hidden sm:block">
                                            {{ $writer->articles()->count() }} articles
                                        </span>

                                        {{-- Role badge --}}
                                        <span class="text-[10px] font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                            User
                                        </span>

                                        {{-- SUPER ADMIN ONLY --}}
                                        @if(auth()->user()->role === 'super_admin')

                                            <form
                                                method="POST"
                                                action="{{ route('admin.promote', $writer) }}"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="px-3 py-1.5 text-xs font-medium text-white bg-black rounded-md hover:bg-gray-800 transition-colors"
                                                >
                                                    Promote to Admin
                                                </button>
                                            </form>

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <p class="text-sm text-gray-400 text-center py-8">
                            No users found.
                        </p>

                    @endif

                </div>

            </div>
        @endif

    </div>
    {{-- Reject Modal --}}
        <div id="rejectModal" class="hidden fixed inset-0 z-50 items-center justify-center">
            <div class="absolute inset-0 bg-black/40" onclick="closeRejectModal()"></div>
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-8">
                <h2 class="text-xl font-bold text-black mb-1">Reject Article</h2>
                <p class="text-sm text-gray-500 mb-4">Article: <strong id="rejectArticleTitle"></strong></p>

                <form method="POST" id="rejectForm">
                    @csrf
                    <textarea name="admin_notes"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors resize-none mb-4"
                            placeholder="Tuliskan alasan reject & hal yang perlu diperbaiki..."
                            required></textarea>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeRejectModal()"
                                class="px-5 py-2.5 text-sm text-gray-500 border border-gray-200 rounded-lg hover:border-black hover:text-black transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                            Reject Article
                        </button>
                    </div>
                </form>
            </div>
        </div>

    <script>
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
    </script>

</x-layouts.app>