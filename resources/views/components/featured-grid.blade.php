@props([
    'featured' => null,
    'articles' => collect(),
])

<section class="max-w-6xl mx-auto px-6 pb-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" style="min-height: 480px;">

        {{-- FEATURED (Large Left Card) --}}
        @if($featured)
            <a href="/blog/{{ $featured->slug }}"
               class="group relative block overflow-hidden rounded-lg bg-gray-900"
               style="min-height: 480px;">
                @if($featured->thumbnail ?? false)
                    <img src="{{ imageUrl($featured->thumbnail) }}"
                         alt="{{ $featured->title }}"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-950"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8">
                    @if($featured->category ?? false)
                        <span class="inline-block px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-white bg-white/20 backdrop-blur-sm rounded mb-3">
                            {{ $featured->category }}
                        </span>
                    @endif
                    <h2 class="text-2xl md:text-3xl font-bold text-white leading-tight group-hover:text-gray-200 transition-colors">
                        {{ $featured->title }}
                    </h2>
                    @if($featured->excerpt ?? false)
                        <p class="mt-3 text-sm text-gray-300 leading-relaxed line-clamp-2">
                            {{ $featured->excerpt }}
                        </p>
                    @endif
                    <div class="mt-4 flex items-center space-x-3 text-xs text-gray-400">
                        @if($featured->author ?? false)
                            <span class="font-medium text-gray-300">{{ $featured->author }}</span>
                        @endif
                        @if($featured->date ?? false)
                            <span>· {{ $featured->date }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @else
            {{-- Empty Placeholder --}}
            <div class="relative block overflow-hidden rounded-lg bg-gray-100 border-2 border-dashed border-gray-200 flex items-center justify-center" style="min-height: 480px;">
                <div class="text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    <p class="text-sm text-gray-400">No featured article yet</p>
                    <p class="text-xs text-gray-300 mt-1">Admin can feature articles from the dashboard</p>
                </div>
            </div>
        @endif

        {{-- RIGHT GRID (4 Small Cards - 2x2) --}}
        <div class="grid grid-cols-2 gap-4" style="min-height: 480px;">
            @if($articles->count() > 0)
                @foreach($articles as $index => $article)
                    @if($index < 4)
                        <a href="/blog/{{ $article->slug ?? '#' }}"
                           class="group relative block overflow-hidden rounded-lg bg-gray-900">
                            @if($article->thumbnail ?? false)
                                <img src="{{ imageUrl($article->thumbnail) }}"
                                     alt="{{ $article->title ?? '' }}"
                                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-700 to-gray-900"></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-4">
                                @if($article->category ?? false)
                                    <span class="inline-block px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wider text-white bg-white/20 backdrop-blur-sm rounded mb-2">
                                        {{ $article->category }}
                                    </span>
                                @endif
                                <h3 class="text-sm md:text-base font-bold text-white leading-snug line-clamp-2 group-hover:text-gray-200 transition-colors">
                                    {{ $article->title ?? 'Article Title' }}
                                </h3>
                                <div class="mt-2 flex items-center space-x-2 text-[10px] text-gray-400">
                                    @if($article->author ?? false)
                                        <span class="font-medium text-gray-300">{{ $article->author }}</span>
                                    @endif
                                    @if($article->date ?? false)
                                        <span>· {{ $article->date }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            @else
                {{-- Empty Placeholders --}}
                @for($i = 0; $i < 4; $i++)
                    <div class="relative block overflow-hidden rounded-lg bg-gray-100 border-2 border-dashed border-gray-200 flex items-center justify-center">
                        <div class="text-center p-4">
                            <svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                            </svg>
                            <p class="text-xs text-gray-300">Empty slot</p>
                        </div>
                    </div>
                @endfor
            @endif
        </div>

    </div>
</section>