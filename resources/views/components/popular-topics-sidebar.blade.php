@props(['tags' => []])

<aside class="bg-white px-5">

    {{-- Header --}}
    <div class="mb-4">
        <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-900">
            Popular Topics
        </h2>

        <div class="mt-2 h-0.5 w-8 bg-cyan-400"></div>
    </div>


    @if(count($tags) > 0)

        {{-- Topic Chips --}}
        <div class="flex flex-wrap gap-2 divide-y divide-zinc-100 rounded-2xl border border-zinc-200 p-4">

            @foreach($tags as $tag)

                <a
                    href="{{ route('explore', ['tag' => $tag->slug]) }}"
                    class="group inline-flex max-w-full items-center gap-2
                           rounded-full border border-zinc-200
                           bg-white px-3 py-2
                           transition-all duration-200
                           hover:border-cyan-400
                           hover:bg-cyan-50"
                >

                    {{-- Topic Name --}}
                    <span
                        class="truncate text-xs font-medium text-zinc-700
                               transition-colors group-hover:text-cyan-700"
                    >
                        {{ $tag->name }}
                    </span>


                    {{-- Article Count --}}
                    <span
                        class="shrink-0 text-[10px] font-semibold text-zinc-400
                               transition-colors group-hover:text-cyan-600"
                    >
                        {{ $tag->articles_count }}
                    </span>

                </a>

            @endforeach

        </div>

    @else

        <p class="py-5 text-center text-sm text-zinc-400">
            No topics yet.
        </p>

    @endif

</aside>