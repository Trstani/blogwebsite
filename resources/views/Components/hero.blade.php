@props(['title' => 'Latest Stories', 'subtitle' => ''])

<section class="pt-10 pb-8 md:pt-14 md:pb-10">
    <div class="max-w-6xl mx-auto px-6">

        {{-- Editorial Header --}}
        <div class="flex items-end justify-between gap-8 border-b border-gray-200 pb-7">

            {{-- Main Introduction --}}
            <div class="max-w-2xl">

                {{-- Eyebrow --}}
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-8 h-px bg-[#0f2747]"></span>

                    <span class="text-[10px] font-semibold uppercase tracking-[0.2em] text-[#0f2747]">
                        Latest Stories
                    </span>
                </div>

                {{-- Main Title --}}
                <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-black leading-[1.05]">
                    {{ $title }}
                </h1>

                {{-- Subtitle --}}
                @if($subtitle)
                    <p class="mt-4 max-w-xl text-base md:text-lg text-gray-500 leading-relaxed">
                        {{ $subtitle }}
                    </p>
                @endif

            </div>

            {{-- Editorial Meta --}}
            <div class="hidden md:block flex-shrink-0 text-right pb-1">

                <span class="block text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-400">
                    Create Eve
                </span>

                <span class="block mt-1 text-xs text-gray-400">
                    Stories · Ideas · Perspectives
                </span>

            </div>

        </div>

    </div>
</section>