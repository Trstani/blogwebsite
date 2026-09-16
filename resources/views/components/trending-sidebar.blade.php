@props(['articles' => []])

<aside class="bg-white border border-gray-100 rounded-lg">

    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
        Trending This Week
    </h2>

    @if(count($articles) > 0)

        <div class="space-y-3 pr-3 pt-3">

            @foreach($articles as $index => $article)

                <a
                    href="/blog/{{ $article->slug ?? '#' }}"
                    class="group relative block min-h-[150px] overflow-hidden rounded-lg bg-[#0f2747]"
                >

                    {{-- Background image --}}
                    @if($article->thumbnail ?? false)

                        <img
                            src="{{ imageUrl($article->thumbnail) }}"
                            alt=""
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />

                    @else

                        {{-- Fallback background --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-[#0f2747] to-[#071525]">
                        </div>

                    @endif


                    {{-- Dark overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/55 to-black/20">
                    </div>


                    {{-- Content --}}
                    <div class="relative z-10 h-full min-h-[150px] p-4 flex flex-col justify-between">

                        {{-- Top --}}
                        <div class="flex items-start justify-between">

                            <span class="text-2xl font-bold text-white/70 leading-none">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            @if($article->category ?? false)

                                <span class="px-2 py-1 text-[9px] font-semibold uppercase tracking-wider text-white/80 bg-white/15 backdrop-blur-sm rounded">
                                    {{ $article->category }}
                                </span>

                            @endif

                        </div>


                        {{-- Bottom --}}
                        <div>

                            <h3 class="text-sm font-semibold text-white leading-snug line-clamp-2 group-hover:text-gray-200 transition-colors">
                                {{ $article->title ?? 'Article Title' }}
                            </h3>


                            <div class="mt-2 flex items-center gap-2 text-[10px] text-white/60">

                                @if($article->author ?? false)
                                    <span>{{ $article->author }}</span>
                                @endif

                                @if(($article->author ?? false) && ($article->views ?? false))
                                    <span>·</span>
                                @endif

                                @if($article->views ?? false)
                                    <span>{{ $article->views }} views</span>
                                @endif

                            </div>

                        </div>

                    </div>

                </a>

            @endforeach

        </div>

    @else

        <p class="text-sm text-gray-400 text-center py-8">
            Belum ada artikel trending minggu ini.
        </p>

    @endif

</aside>