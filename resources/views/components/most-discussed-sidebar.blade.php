@props(['articles' => []])

<aside class="bg-white px-5 mt-2">
    {{-- Header --}}
    <div class="mb-5 flex items-start justify-between">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-900">
                Most Discussed
            </h2>

            <div class="mt-2 h-0.5 w-8 bg-cyan-400"></div>
        </div>

        {{-- Comment icon --}}
        <svg
            class="h-5 w-5 text-cyan-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
            />
        </svg>
    </div>

    @if(count($articles) > 0)
        <ol class="divide-y divide-zinc-100 rounded-2xl border border-zinc-200 p-4">
            @foreach($articles as $index => $article)
                <li class="border-b border-zinc-100 last:border-b-0">
                    <a
                        href="/blog/{{ $article->slug ?? '#' }}"
                        class="group block py-4 first:pt-1 last:pb-1"
                    >
                        <div class="flex gap-4">
                            {{-- Rank --}}
                            <span
                                class="shrink-0 pt-0.5 text-3xl font-semibold leading-none
                                       tabular-nums tracking-tight text-zinc-200
                                       transition-colors duration-300
                                       group-hover:text-cyan-200"
                            >
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            {{-- Article Content --}}
                            <div class="min-w-0 flex-1">
                                {{-- Title --}}
                                <h3
                                    class="line-clamp-3 text-sm font-semibold leading-snug
                                           text-zinc-900 transition-colors duration-300
                                           group-hover:text-cyan-700"
                                >
                                    {{ $article->title ?? 'Article Title' }}
                                </h3>

                                {{-- Metadata --}}
                                <div
                                    class="mt-3 flex items-center justify-between gap-3
                                           text-[10px] leading-none"
                                >
                                    {{-- Author --}}
                                    @if($article->author ?? false)
                                        <span class="min-w-0 truncate text-zinc-400">
                                            {{ $article->author }}
                                        </span>
                                    @endif

                                    {{-- Discussion count --}}
                                    @if($article->discussion_count ?? false)
                                        <span
                                            class="inline-flex shrink-0 items-center gap-1
                                                   font-semibold text-cyan-600"
                                        >
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
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                                />
                                            </svg>

                                            <span>
                                                {{ $article->discussion_count }}
                                            </span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        </ol>
    @else
        <p class="py-6 text-center text-sm text-zinc-400">
            Belum ada artikel dengan diskusi.
        </p>
    @endif
</aside>