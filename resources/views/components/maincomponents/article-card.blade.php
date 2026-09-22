@props(['article'])

<a href="/blog/{{ $article->slug ?? '#' }}" class="group block h-full">
    <article class="flex h-full flex-col bg-white">

        {{-- Thumbnail --}}
        @if($article->thumbnail ?? false)
            <div class="relative aspect-[16/10] shrink-0 overflow-hidden rounded-lg bg-gray-100">
                <img
                    src="{{ imageUrl($article->thumbnail) }}"
                    alt="{{ $article->title ?? 'Article' }}"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                />

                {{-- Image fallback --}}
                <div class="absolute inset-0 hidden overflow-hidden bg-[#0f2747]">
                    <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full border border-white/10"></div>
                    <div class="absolute -right-4 top-16 h-20 w-20 rounded-full border border-white/10"></div>
                    <div class="absolute -bottom-12 -left-8 h-36 w-36 rounded-full border border-white/10"></div>

                    <div class="relative flex h-full flex-col justify-between p-5">
                        @if($article->category ?? false)
                            <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-white/60">
                                {{ $article->category }}
                            </span>
                        @endif

                        <span class="text-4xl font-bold uppercase text-white/10">
                            {{ substr($article->title ?? 'A', 0, 1) }}
                        </span>
                    </div>
                </div>
            </div>
        @else
            {{-- Editorial fallback --}}
            <div class="relative aspect-[16/10] shrink-0 overflow-hidden rounded-lg bg-[#0f2747]">
                <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full border border-white/10"></div>
                <div class="absolute right-6 top-12 h-16 w-16 rounded-full border border-white/10"></div>
                <div class="absolute -bottom-12 -left-8 h-36 w-36 rounded-full border border-white/10"></div>

                <div class="relative flex h-full flex-col justify-between p-5">
                    <div class="flex items-center justify-between">
                        @if($article->category ?? false)
                            <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-white/60">
                                {{ $article->category }}
                            </span>
                        @endif

                        <span class="text-xs text-white/30">
                            ARTICLE
                        </span>
                    </div>

                    <div>
                        <span class="block text-5xl font-bold uppercase leading-none text-white/10">
                            {{ substr($article->title ?? 'A', 0, 1) }}
                        </span>
                        <div class="mt-2 h-px w-10 bg-white/30"></div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Content --}}
        <div class="flex flex-1 flex-col pt-3">

            {{-- Category --}}
            @if($article->category ?? false)
                <span class="text-[10px] font-semibold uppercase tracking-[0.16em] text-[#0f2747]">
                    {{ $article->category }}
                </span>
            @endif

            {{-- Title --}}
            <h3 class="mt-1 min-h-[3.25rem] text-lg font-semibold leading-snug text-black transition-colors group-hover:text-[#0f2747]">
                {{ $article->title ?? 'Article Title' }}
            </h3>

            {{-- Description --}}
            @if($article->description ?? false)
                <p class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-gray-500">
                    {{ $article->description }}
                </p>
            @endif

            {{-- Meta --}}
            <div class="mt-auto flex items-center justify-between gap-3 pt-3">
                <div class="flex min-w-0 items-center gap-2 text-xs text-gray-400">
                    @if($article->author ?? false)
                        <span class="truncate font-medium text-gray-600">
                            {{ $article->author }}
                        </span>
                    @endif

                    @if(($article->author ?? false) && ($article->date ?? false))
                        <span>·</span>
                    @endif

                    @if($article->date ?? false)
                        <span class="whitespace-nowrap">
                            {{ $article->date }}
                        </span>
                    @endif
                </div>

                {{-- Read indicator --}}
                <span class="shrink-0 text-xs font-medium text-gray-300 transition-colors group-hover:text-[#0f2747]">
                    Read →
                </span>
            </div>

        </div>
    </article>
</a>