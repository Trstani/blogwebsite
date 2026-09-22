@props(['articles' => []])

<aside class="rounded-lg border border-gray-100 bg-white p-4">
    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-900">
        Trending This Week
    </h2>

    @if(count($articles) > 0)
        <div class="space-y-2.5">
            @foreach($articles as $index => $article)
                <a
                    href="/blog/{{ $article->slug ?? '#' }}"
                    class="group relative block min-h-[150px] overflow-hidden rounded-lg bg-[#0f2747]"
                >
                    @if($article->thumbnail ?? false)
                        <img
                            src="{{ imageUrl($article->thumbnail) }}"
                            alt=""
                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                    @else
                        <div class="absolute inset-0 bg-gradient-to-br from-[#0f2747] to-[#071525]"></div>
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/55 to-black/20"></div>

                    <div class="relative z-10 flex min-h-[150px] flex-col justify-between p-3.5">
                        <div class="flex items-start justify-between">
                            <span class="text-2xl font-bold leading-none text-white/70">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            @if($article->category ?? false)
                                <span class="rounded bg-white/15 px-2 py-1 text-[9px] font-semibold uppercase tracking-wider text-white/80 backdrop-blur-sm">
                                    {{ $article->category }}
                                </span>
                            @endif
                        </div>

                        <div>
                            <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-white transition-colors group-hover:text-gray-200">
                                {{ $article->title ?? 'Article Title' }}
                            </h3>

                            <div class="mt-1.5 flex items-center gap-2 text-[10px] text-white/60">
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
        <p class="py-6 text-center text-sm text-gray-400">
            Belum ada artikel trending minggu ini.
        </p>
    @endif
</aside>