@props(['transparent' => false])

<nav class="{{ $transparent ? 'absolute top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-white/20 shadow-lg shadow-black/5' : 'sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-gray-200/60 shadow-sm' }}">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">

        {{-- Logo --}}
        <a href="/" class="flex items-center group">
            <img
                src="{{ asset('logo/createeve.png') }}"
                alt="Create Eve"
                class="h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-[1.02]"
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
</script>