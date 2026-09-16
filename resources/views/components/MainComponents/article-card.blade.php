@props(['article'])

<a href="/blog/{{ $article->slug ?? '#' }}" class="group block">

    <article class="bg-white">

        {{-- Thumbnail --}}
        @if($article->thumbnail ?? false)

            <div class="relative aspect-[16/10] overflow-hidden rounded-lg bg-gray-100">

                <img
                    src="{{ imageUrl($article->thumbnail) }}"
                    alt="{{ $article->title ?? 'Article' }}"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                />

                {{-- Image fallback --}}
                <div class="hidden absolute inset-0 overflow-hidden bg-[#0f2747]">

                    <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full border border-white/10"></div>
                    <div class="absolute -right-4 top-16 w-20 h-20 rounded-full border border-white/10"></div>
                    <div class="absolute -left-8 -bottom-12 w-36 h-36 rounded-full border border-white/10"></div>

                    <div class="relative h-full flex flex-col justify-between p-5">

                        @if($article->category ?? false)
                            <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-white/60">
                                {{ $article->category }}
                            </span>
                        @endif

                        <div>
                            <span class="block text-4xl font-bold text-white/10 uppercase">
                                {{ substr($article->title ?? 'A', 0, 1) }}
                            </span>
                        </div>

                    </div>
                </div>

            </div>

        @else

            {{-- Editorial fallback when article has no thumbnail --}}
            <div class="relative aspect-[16/10] overflow-hidden rounded-lg bg-[#0f2747]">

                {{-- Decorative elements --}}
                <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full border border-white/10"></div>
                <div class="absolute right-6 top-12 w-16 h-16 rounded-full border border-white/10"></div>
                <div class="absolute -left-8 -bottom-12 w-36 h-36 rounded-full border border-white/10"></div>

                <div class="relative h-full flex flex-col justify-between p-5">

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

                        <span class="block text-5xl font-bold text-white/10 uppercase leading-none">
                            {{ substr($article->title ?? 'A', 0, 1) }}
                        </span>

                        <div class="mt-2 h-px w-10 bg-white/30"></div>

                    </div>

                </div>

            </div>

        @endif


        {{-- Content --}}
        <div class="pt-4">

            {{-- Category --}}
            @if($article->category ?? false)

                <span class="text-[10px] font-semibold text-[#0f2747] uppercase tracking-[0.16em]">
                    {{ $article->category }}
                </span>

            @endif


            {{-- Title --}}
            <h3 class="mt-1.5 text-lg font-semibold text-black leading-snug group-hover:text-[#0f2747] transition-colors">
                {{ $article->title ?? 'Article Title' }}
            </h3>


            {{-- Description --}}
            @if($article->description ?? false)

                <p class="mt-2 text-sm text-gray-500 leading-relaxed line-clamp-2">
                    {{ $article->description }}
                </p>

            @endif


            {{-- Meta --}}
            <div class="mt-3 flex items-center justify-between gap-3">

                <div class="flex items-center gap-2 text-xs text-gray-400 min-w-0">

                    @if($article->author ?? false)

                        <span class="font-medium text-gray-600 truncate">
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
                <span class="flex-shrink-0 text-xs font-medium text-gray-300 group-hover:text-[#0f2747] transition-colors">
                    Read →
                </span>

            </div>

        </div>

    </article>

</a>