@props(['articles' => []])

<aside class="bg-white p-5">
    <div class="mb-4">
        <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-900">
            Trending This Week
        </h2>

        <div class="mt-2 h-0.5 w-8 bg-cyan-400"></div>
    </div>

    @if(count($articles) > 0)
        <ol class="divide-y divide-zinc-100 rounded-2xl border border-zinc-200 p-4">

            @foreach($articles as $index => $article)
                <li>
                    <a
                        href="/blog/{{ $article->slug ?? '#' }}"
                        class="group flex items-center gap-3.5 py-3.5 first:pt-1 last:pb-1"
                    >

                        {{-- Rank Number --}}
                        <span
                            class="shrink-0 text-2xl font-bold leading-none
                                   tabular-nums tracking-tight text-zinc-200
                                   transition-colors group-hover:text-cyan-400"
                        >
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>


                        {{-- Small Thumbnail --}}
                        <div
                            class="relative h-12 w-12 shrink-0 overflow-hidden
                                   rounded-lg bg-zinc-100"
                        >
                            @if($article->thumbnail ?? false)
                                <img
                                    src="{{ imageUrl($article->thumbnail) }}"
                                    alt=""
                                    class="h-full w-full object-cover
                                           transition-transform duration-500
                                           group-hover:scale-110"
                                />
                            @else
                                <div
                                    class="h-full w-full
                                           bg-gradient-to-br from-zinc-200 to-zinc-300"
                                ></div>
                            @endif
                        </div>


                        {{-- Content --}}
                        <div class="min-w-0 flex-1">

                            {{-- Title --}}
                            <h3
                                class="line-clamp-2 text-sm font-semibold leading-snug
                                       text-zinc-900 transition-colors
                                       group-hover:text-cyan-600"
                            >
                                {{ $article->title ?? 'Article Title' }}
                            </h3>


                            {{-- Metadata --}}
                            <div
                                class="mt-1.5 flex items-center justify-between gap-3
                                       text-[10px] leading-none"
                            >

                                {{-- Author --}}
                                @if($article->author ?? false)
                                    <span class="min-w-0 truncate text-zinc-400">
                                        {{ $article->author }}
                                    </span>
                                @endif


                                {{-- Views --}}
                                @if($article->views ?? false)
                                    <span
                                        class="inline-flex shrink-0 items-center gap-1
                                               font-semibold text-cyan-600"
                                    >
                                        {{-- Eye Icon --}}
                                        <svg
                                            class="h-3.5 w-3.5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                        </svg>

                                        <span>
                                            {{ $article->views }}
                                        </span>
                                    </span>
                                @endif

                            </div>

                        </div>

                    </a>
                </li>
            @endforeach

        </ol>
    @else
        <p class="py-6 text-center text-sm text-zinc-400">
            Belum ada artikel trending minggu ini.
        </p>
    @endif
</aside>