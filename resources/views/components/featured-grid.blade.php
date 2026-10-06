@props([
    'featured' => null,
    'articles' => collect(),
])

<section class="w-full">
    <div
        class="grid grid-cols-1 md:grid-cols-4 gap-4"
    >

        {{-- =========================================================
             FEATURED ARTICLE
             ========================================================= --}}
        @if($featured)
            <a
                href="/blog/{{ $featured->slug }}"
                class="group relative block overflow-hidden rounded-lg bg-gray-900
                       md:col-span-2 md:row-span-2
                       min-h-[420px] md:min-h-[464px]"
            >
                {{-- Image --}}
                @if($featured->thumbnail ?? false)
                    <img
                        src="{{ imageUrl($featured->thumbnail) }}"
                        alt="{{ $featured->title }}"
                        class="absolute inset-0 h-full w-full object-cover
                               transition-transform duration-700
                               group-hover:scale-105"
                    />
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-950"></div>
                @endif

                {{-- Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>

                {{-- Content --}}
                <div class="absolute inset-x-0 bottom-0 p-6 md:p-7">

                    @if($featured->category ?? false)
                        <span
                            class="inline-block rounded bg-white/15 px-2.5 py-1
                                   text-[10px] font-semibold uppercase tracking-[0.16em]
                                   text-white backdrop-blur-sm"
                        >
                            {{ $featured->category }}
                        </span>
                    @endif

                    <h2
                        class="mt-3 text-2xl font-bold leading-tight text-white
                               md:text-3xl
                               group-hover:text-gray-200 transition-colors"
                    >
                        {{ $featured->title }}
                    </h2>

                    @if($featured->excerpt ?? false)
                        <p
                            class="mt-3 max-w-xl text-sm leading-relaxed text-gray-300
                                   line-clamp-2"
                        >
                            {{ $featured->excerpt }}
                        </p>
                    @endif

                    <div class="mt-4 flex items-center gap-3 text-xs text-gray-400">
                        @if($featured->author ?? false)
                            <span class="font-medium text-gray-300">
                                {{ $featured->author }}
                            </span>
                        @endif

                        @if($featured->date ?? false)
                            <span>·</span>
                            <span>{{ $featured->date }}</span>
                        @endif
                    </div>
                </div>
            </a>

        @else

            {{-- Empty Featured Placeholder --}}
            <div
                class="relative flex min-h-[420px] items-center justify-center
                       overflow-hidden rounded-lg border-2 border-dashed
                       border-gray-200 bg-gray-100
                       md:col-span-2 md:row-span-2 md:min-h-[464px]"
            >
                <div class="text-center">
                    <svg
                        class="mx-auto mb-3 h-12 w-12 text-gray-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                        />
                    </svg>

                    <p class="text-sm text-gray-400">
                        No featured article yet
                    </p>

                    <p class="mt-1 text-xs text-gray-300">
                        Admin can feature articles from the dashboard
                    </p>
                </div>
            </div>

        @endif


        {{-- =========================================================
             ARTICLES 2–5
             ========================================================= --}}
        @if($articles->count() > 0)

            @foreach($articles->take(4) as $index => $article)

                @php
                    /*
                     * Desktop placement:
                     *
                     * Article 2 → right / row 1
                     * Article 3 → right / row 2
                     * Article 4 → bottom / left
                     * Article 5 → bottom / right
                     */
                    $articlePosition = match($index) {
                        0 => 'md:col-span-2 md:row-span-1',
                        1 => 'md:col-span-2 md:row-span-1',
                        2 => 'md:col-span-2 md:row-span-1',
                        3 => 'md:col-span-2 md:row-span-1',
                        default => '',
                    };
                @endphp

                <a
                    href="/blog/{{ $article->slug ?? '#' }}"
                    class="group relative block min-h-[220px] overflow-hidden
                           rounded-lg bg-gray-900 {{ $articlePosition }}"
                >

                    {{-- Image --}}
                    @if($article->thumbnail ?? false)

                        <img
                            src="{{ imageUrl($article->thumbnail) }}"
                            alt="{{ $article->title ?? '' }}"
                            class="absolute inset-0 h-full w-full object-cover
                                   transition-transform duration-700
                                   group-hover:scale-105"
                        />

                    @else

                        <div class="absolute inset-0 bg-gradient-to-br from-gray-700 to-gray-950">
                            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full border border-white/10"></div>
                            <div class="absolute -bottom-12 -left-8 h-36 w-36 rounded-full border border-white/10"></div>
                        </div>

                    @endif


                    {{-- Overlay --}}
                    <div
                        class="absolute inset-0 bg-gradient-to-t
                               from-black/85 via-black/25 to-transparent"
                    ></div>


                    {{-- Content --}}
                    <div class="absolute inset-x-0 bottom-0 p-4 md:p-5">

                        @if($article->category ?? false)
                            <span
                                class="inline-block rounded bg-white/15 px-2 py-0.5
                                       text-[9px] font-semibold uppercase
                                       tracking-[0.15em] text-white backdrop-blur-sm"
                            >
                                {{ $article->category }}
                            </span>
                        @endif

                        <h3
                            class="mt-2 max-w-xl text-base font-bold leading-snug
                                   text-white md:text-lg
                                   line-clamp-2
                                   group-hover:text-gray-200
                                   transition-colors"
                        >
                            {{ $article->title ?? 'Article Title' }}
                        </h3>

                        <div
                            class="mt-2 flex items-center gap-2
                                   text-[10px] text-gray-400"
                        >
                            @if($article->author ?? false)
                                <span class="font-medium text-gray-300">
                                    {{ $article->author }}
                                </span>
                            @endif

                            @if($article->date ?? false)
                                <span>·</span>
                                <span>{{ $article->date }}</span>
                            @endif
                        </div>

                    </div>

                </a>

            @endforeach

        @else

            {{-- Empty Placeholders --}}
            @for($i = 0; $i < 4; $i++)

                <div
                    class="flex min-h-[220px] items-center justify-center
                           rounded-lg border-2 border-dashed
                           border-gray-200 bg-gray-100
                           md:col-span-2"
                >
                    <div class="text-center">

                        <svg
                            class="mx-auto mb-2 h-8 w-8 text-gray-200"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"
                            />
                        </svg>

                        <p class="text-xs text-gray-300">
                            Empty slot
                        </p>

                    </div>
                </div>

            @endfor

        @endif

    </div>
</section>