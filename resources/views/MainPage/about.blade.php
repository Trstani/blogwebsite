<x-layouts.app title="about">


<div class="min-h-screen bg-white text-gray-900">

 <!-- ============================================================
         HERO
         ============================================================ -->
    <section class="relative border-b border-black/10">
        {{-- subtle grid --}}
        <div class="pointer-events-none absolute inset-0 opacity-[0.06]"
             style="background-image: linear-gradient(to right, #000 1px, transparent 1px), linear-gradient(to bottom, #000 1px, transparent 1px); background-size: 80px 80px;">
        </div>

        <div class="relative mx-auto max-w-[1400px] px-6 sm:px-8 lg:px-12 pt-16 pb-20 lg:pt-24 lg:pb-32">

            {{-- top meta row --}}
            <div class="flex items-center justify-between text-[11px] tracking-[0.28em] uppercase text-black/60 mb-14 lg:mb-20">
                <div class="flex items-center gap-3">
                    <span class="inline-block h-[6px] w-[6px] rounded-full bg-[#54DCE3]"></span>
                    <span>About Create Eve</span>
                </div>
                <div class="hidden sm:block">
                    <span>Est. 2020 — Jakarta</span>
                </div>
                <div class="hidden md:block">
                    <span>Editorial / Index</span>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-8 lg:gap-12">

                {{-- LEFT RAIL: vertical cyan line + label --}}
                <div class="hidden lg:flex col-span-1 flex-col items-start pt-3">
                    <div class="h-40 w-[2px] bg-[#54DCE3]"></div>
                    <span class="mt-4 text-[10px] tracking-[0.3em] uppercase text-black/50 -rotate-90 origin-top-left translate-y-24">
                        N° 00 / Intro
                    </span>
                </div>

                {{-- MAIN HEADLINE --}}
                <div class="col-span-12 lg:col-span-8">
                    <h1 class="font-light tracking-[-0.03em] leading-[0.92] text-[clamp(2.6rem,8.5vw,7.5rem)]">
                        Ideas, insights,<br>
                        and digital<br>
                        <span class="relative inline-block">
                            perspectives.
                            <span class="absolute left-0 -bottom-2 h-[3px] w-full bg-[#54DCE3]"></span>
                        </span>
                    </h1>

                    <div class="mt-10 lg:mt-14 grid grid-cols-12 gap-6 max-w-3xl">
                        <div class="col-span-12 sm:col-span-7">
                            <p class="text-lg sm:text-xl text-black/75 leading-relaxed font-light">
                                Welcome to the Create Eve Blog — a space where digital knowledge, technology, creativity, and business come together.
                            </p>
                        </div>
                        <div class="col-span-12 sm:col-span-5 sm:pl-6 sm:border-l border-black/10">
                            <p class="text-xs tracking-[0.2em] uppercase text-black/50 mb-3">Publication</p>
                            <p class="text-sm text-black/70 font-light">
                                A blogs on digital presence, MSME growth, and modern craft.
                            </p>
                        </div>
                    </div>

                    {{-- CTAs --}}
                    <div class="mt-12 flex flex-col sm:flex-row items-start sm:items-center gap-5 sm:gap-8">
                        <a href="/explore"
                           class="group inline-flex items-center gap-3 bg-black text-white px-7 py-4 text-sm tracking-[0.15em] uppercase hover:bg-[#54DCE3] hover:text-black transition-colors duration-300">
                            <span>Explore the Blog</span>
                            <span class="inline-block transition-transform duration-300 group-hover:translate-x-1">→</span>
                        </a>
                        <a href="#journey"
                           class="group inline-flex items-center gap-3 text-sm tracking-[0.15em] uppercase text-black/70 hover:text-black transition-colors">
                            <span class="relative">
                                Discover Our Story
                                <span class="absolute left-0 -bottom-1 h-[1px] w-full bg-black/30 group-hover:bg-[#54DCE3] transition-colors"></span>
                            </span>
                        </a>
                    </div>
                </div>

                {{-- RIGHT RAIL: logo placeholder + metadata --}}
                <div class="col-span-12 lg:col-span-3 flex flex-col justify-between lg:pl-6 lg:border-l border-black/10">
                    {{-- LOGO PLACEHOLDER (replace with <img src="{{ asset('logo/createeve.png') }}">) --}}
                    <div class="w-full aspect-square border border-black/10 flex items-center justify-center bg-[#F5F5F5] relative">
                        <div class="absolute top-0 left-0 h-[3px] w-10 bg-[#54DCE3]"></div>
                        <div class="text-center">
                            <img src="{{ asset('logo/createeve.png') }}">
                        </div>
                    </div>

                    <div class="mt-8 space-y-4 text-xs">
                        <div class="flex justify-between border-t border-black/10 pt-3">
                            <span class="tracking-[0.2em] uppercase text-black/50">Field</span>
                            <span class="text-black/80">Digital / MSME</span>
                        </div>
                        <div class="flex justify-between border-t border-black/10 pt-3">
                            <span class="tracking-[0.2em] uppercase text-black/50">Base</span>
                            <span class="text-black/80">Jakarta, ID</span>
                        </div>
                        <div class="flex justify-between border-t border-black/10 pt-3">
                            <span class="tracking-[0.2em] uppercase text-black/50">Format</span>
                            <span class="text-black/80">Journal</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- scroll indicator --}}
            <div class="mt-20 lg:mt-28 flex items-center gap-4">
                <div class="w-[1px] h-14 bg-black/20 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1/2 bg-[#54DCE3] animate-[scroll_2.4s_ease-in-out_infinite]"></div>
                </div>
                <span class="text-[10px] tracking-[0.35em] uppercase text-black/50">Scroll</span>
            </div>
        </div>

        <style>
            @keyframes scroll {
                0%   { transform: translateY(-100%); }
                50%  { transform: translateY(0%); }
                100% { transform: translateY(100%); }
            }
        </style>
    </section>


    <!-- ============================================================
         WHO WE ARE
         ============================================================ -->
    <section id="about" class="border-b border-black/10">
        <div class="mx-auto max-w-[1400px] px-6 sm:px-8 lg:px-12 py-20 lg:py-32">

            <div class="grid grid-cols-12 gap-8 lg:gap-12">

                {{-- LEFT: big number --}}
                <div class="col-span-12 lg:col-span-2">
                    <div class="text-[10px] tracking-[0.3em] uppercase text-black/40 mb-3">Section</div>
                    <div class="text-[clamp(4rem,10vw,9rem)] leading-none font-light text-[#54DCE3]">01</div>
                </div>

                {{-- CENTER: statement --}}
                <div class="col-span-12 lg:col-span-6">
                    <h2 class="text-[clamp(1.75rem,3.6vw,3rem)] font-light tracking-[-0.02em] leading-[1.05]">
                        Born to bridge<br>
                        the <span class="italic font-normal">digital gap.</span>
                    </h2>
                    <div class="mt-8 h-[2px] w-24 bg-[#54DCE3]"></div>
                </div>

                {{-- RIGHT: paragraph --}}
                <div class="col-span-12 lg:col-span-4 lg:pl-6 lg:border-l border-black/10">
                    <p class="text-base text-black/75 leading-relaxed font-light">
                        Create Eve is an innovative agency born to bridge the digital gap for Micro, Small, and Medium Enterprises (MSMEs). We understand that the biggest challenge for MSMEs is not willingness, but limitations in time, budget, and technical resources to get started.
                    </p>
                </div>
            </div>

        </div>
    </section>


    <!-- ============================================================
         WHY THIS BLOG EXISTS
         ============================================================ -->
    <section class="border-b border-black/10 bg-[#0A0A0A] text-white">
        <div class="mx-auto max-w-[1400px] px-6 sm:px-8 lg:px-12 py-20 lg:py-32">

            <div class="grid grid-cols-12 gap-8 lg:gap-12 mb-16 lg:mb-24">
                <div class="col-span-12 lg:col-span-3">
                    <div class="text-[10px] tracking-[0.3em] uppercase text-white/40 mb-3">Section 02</div>
                    <div class="text-[#54DCE3] text-[10px] tracking-[0.3em] uppercase">Manifesto</div>
                </div>
                <div class="col-span-12 lg:col-span-9">
                    <h2 class="text-[clamp(2rem,5vw,4.5rem)] font-light tracking-[-0.02em] leading-[1.02]">
                        More than a<br>
                        company <span class="italic font-normal text-[#54DCE3]">blog.</span>
                    </h2>
                    <p class="mt-8 max-w-2xl text-white/70 leading-relaxed font-light text-base sm:text-lg">
                        The digital landscape continues to evolve — rapidly, relentlessly, and often confusingly. This platform exists as a space to explore ideas, share knowledge, and make digital topics easier to understand for businesses and creatives alike.
                    </p>
                </div>
            </div>

            {{-- three horizontal editorial rows --}}
            <div class="border-t border-white/15">

                @php
                    $items = [
                        ['n' => '01', 't' => 'Insights', 'd' => 'Perspectives and ideas about the digital world.', 'tag' => 'Opinion'],
                        ['n' => '02', 't' => 'Guides',   'd' => 'Practical knowledge that readers can apply.',   'tag' => 'Tactical'],
                        ['n' => '03', 't' => 'Stories',  'd' => 'Experiences, creativity, and ideas behind the work.', 'tag' => 'Narrative'],
                    ];
                @endphp

                @foreach ($items as $it)
                    <div class="group border-b border-white/15 py-8 lg:py-12 cursor-pointer transition-all duration-500 hover:pl-4 lg:hover:pl-8 relative">
                        {{-- cyan accent line --}}
                        <span class="absolute left-0 top-0 h-full w-[2px] bg-[#54DCE3] scale-y-0 origin-top group-hover:scale-y-100 transition-transform duration-500"></span>

                        <div class="grid grid-cols-12 gap-4 lg:gap-8 items-baseline">
                            <div class="col-span-2 lg:col-span-1 text-2xl lg:text-4xl font-light text-[#54DCE3]">
                                {{ $it['n'] }}
                            </div>
                            <div class="col-span-10 lg:col-span-3 text-3xl lg:text-5xl font-light tracking-tight group-hover:text-[#54DCE3] transition-colors duration-300">
                                {{ $it['t'] }}
                            </div>
                            <div class="col-span-12 lg:col-span-6 text-white/60 group-hover:text-white/90 transition-colors duration-300 font-light text-base lg:text-lg pl-0 lg:pl-4">
                                {{ $it['d'] }}
                            </div>
                            <div class="col-span-12 lg:col-span-2 flex lg:justify-end items-center gap-3">
                                <span class="text-[10px] tracking-[0.3em] uppercase text-white/40 group-hover:text-[#54DCE3] transition-colors">{{ $it['tag'] }}</span>
                                <span class="text-white/40 group-hover:text-[#54DCE3] group-hover:translate-x-1 transition-all duration-300">→</span>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>


    <!-- ============================================================
         OUR JOURNEY
         ============================================================ -->
    <section id="journey" class="border-b border-black/10">
        <div class="mx-auto max-w-[1400px] px-6 sm:px-8 lg:px-12 py-20 lg:py-32">

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8 mb-16 lg:mb-24">
                <div>
                    <div class="text-[10px] tracking-[0.3em] uppercase text-black/40 mb-3">Section 03 / Timeline</div>
                    <h2 class="text-[clamp(2rem,5vw,4.5rem)] font-light tracking-[-0.02em] leading-[1.02]">
                        Our <span class="italic font-normal">journey.</span>
                    </h2>
                </div>
                <p class="max-w-md text-black/70 font-light leading-relaxed">
                    A short look at where we began — and the direction we're still moving toward.
                </p>
            </div>

            {{-- timeline --}}
            <div class="relative">

                {{-- 2020 node --}}
                <div class="grid grid-cols-12 gap-6 items-start">
                    <div class="col-span-12 lg:col-span-3">
                        <div class="text-[clamp(3rem,7vw,6rem)] leading-none font-light tracking-tight">
                            2020
                        </div>
                        <div class="mt-3 flex items-center gap-3">
                            <span class="h-[2px] w-8 bg-[#54DCE3]"></span>
                            <span class="text-[10px] tracking-[0.3em] uppercase text-black/50">Origin</span>
                        </div>
                    </div>

                    <div class="col-span-12 lg:col-span-4 lg:pl-8 lg:border-l border-black/10">
                        <h3 class="text-2xl lg:text-3xl font-light tracking-tight mb-2">
                            Founded in Jakarta
                        </h3>
                        <p class="text-sm tracking-[0.15em] uppercase text-black/50">
                            By Benediktus Rolando
                        </p>
                    </div>

                    <div class="col-span-12 lg:col-span-5">
                        <p class="text-base text-black/75 leading-relaxed font-light">
                            Create Eve was founded by Benediktus Rolando. It began as a small consultancy serving local businesses in Jakarta with a team of three passionate individuals.
                        </p>
                    </div>
                </div>

                {{-- continuation line --}}
                <div class="mt-12 lg:mt-16 grid grid-cols-12 gap-6 items-center">
                    <div class="col-span-3 flex items-center gap-3">
                        <div class="h-[2px] w-8 bg-[#54DCE3]"></div>
                        <span class="text-[10px] tracking-[0.3em] uppercase text-black/50">Now</span>
                    </div>

                    <div class="col-span-9">
                        <div class="relative h-[2px] w-full bg-black/10 overflow-hidden">
                            <div class="absolute top-0 left-0 h-full w-1/3 bg-[#54DCE3] animate-[journey_4s_ease-in-out_infinite]"></div>
                        </div>
                        <p class="mt-4 text-xs tracking-[0.25em] uppercase text-black/40">
                            The journey continues — a story still being written.
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <style>
            @keyframes journey {
                0%   { transform: translateX(-30%); opacity: 0.4; }
                50%  { transform: translateX(120%); opacity: 1; }
                100% { transform: translateX(320%); opacity: 0.4; }
            }
        </style>
    </section>


    <!-- ============================================================
         MISSION & VISION
         ============================================================ -->
    <section id="mission" class="border-b border-black/10 bg-[#F5F5F5]">
        <div class="mx-auto max-w-[1400px] px-6 sm:px-8 lg:px-12 py-20 lg:py-32">

            <div class="text-[10px] tracking-[0.3em] uppercase text-black/40 mb-6">Section 04 / Direction</div>

            {{-- Tab switcher --}}
            <div class="flex items-center gap-8 lg:gap-14 mb-12 lg:mb-20 border-b border-black/15">
                <button data-tab="mission"
                        class="tab-btn relative pb-5 text-[clamp(1.5rem,3.4vw,2.75rem)] font-light tracking-tight text-black transition-colors">
                    Mission
                </button>
                <button data-tab="vision"
                        class="tab-btn relative pb-5 text-[clamp(1.5rem,3.4vw,2.75rem)] font-light tracking-tight text-black/30 hover:text-black/60 transition-colors">
                    Vision
                </button>
            </div>

            {{-- Content --}}
            <div class="grid grid-cols-12 gap-8 lg:gap-12 min-h-[220px]">

                <div class="col-span-12 lg:col-span-3 hidden lg:block">
                    <div id="tab-number" class="text-[clamp(4rem,8vw,7rem)] leading-none font-light text-[#54DCE3]">M</div>
                    <div class="mt-3 text-[10px] tracking-[0.3em] uppercase text-black/40" id="tab-label">Mission Statement</div>
                </div>

                <div class="col-span-12 lg:col-span-9">
                    <p id="tab-text"
                       class="text-[clamp(1.25rem,2.4vw,2rem)] font-light leading-[1.35] tracking-[-0.01em] text-black transition-opacity duration-500">
                        At Create Eve, our mission is to provide a digital presence foundation (WhatsApp Business, Instagram Business, Google Maps) for MSMEs instantly, easily, and affordably through smart automation technology.
                    </p>
                </div>
            </div>

        </div>
    </section>


    <!-- ============================================================
         PROFESSIONAL TEAM
         ============================================================ -->
    <section id="team" class="border-b border-black/10">
        <div class="mx-auto max-w-[1400px] px-6 sm:px-8 lg:px-12 py-20 lg:py-32">

            <div class="grid grid-cols-12 gap-8 lg:gap-12 mb-16 lg:mb-24">
                <div class="col-span-12 lg:col-span-3">
                    <div class="text-[10px] tracking-[0.3em] uppercase text-black/40 mb-3">Section 05</div>
                    <div class="text-[#54DCE3] text-[10px] tracking-[0.3em] uppercase">People</div>
                </div>
                <div class="col-span-12 lg:col-span-9">
                    <h2 class="text-[clamp(2rem,5vw,4.5rem)] font-light tracking-[-0.02em] leading-[1.02] mb-6">
                        Meet the Create Eve<br>
                        <span class="italic font-normal">Professional Team.</span>
                    </h2>
                    <p class="max-w-2xl text-black/70 leading-relaxed font-light text-base sm:text-lg">
                        Our team of innovative marketers, data analysts, and creative minds come together to transform brands into experiences.
                    </p>
                </div>
            </div>

            @php
                $team = [
                    [
                        'n' => '01',
                        'name' => 'Benediktus Rolando, B.IB., M.EInv., M.Sc., MBA., CSA., CRA., QWP.',
                        'role' => 'CEO & Executive Director, Dinamika Publika',
                    ],
                    [
                        'n' => '02',
                        'name' => 'Alberta Ingriana, B.Bus, M.Bus',
                        'role' => 'Marketing Director',
                    ],
                    [
                        'n' => '03',
                        'name' => 'Marcellina Laurensia, M.PP',
                        'role' => 'Reviewer & Editor',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-black/10 border border-black/10">

                @foreach ($team as $m)
                    <div class="group bg-white relative overflow-hidden">
                        {{-- image placeholder --}}
                        <div class="relative aspect-[4/5] bg-[#F5F5F5] overflow-hidden">
                            <div class="absolute inset-0 flex items-center justify-center transition-transform duration-700 group-hover:scale-105">
                                <div class="text-center">
                                    <div class="text-[10px] tracking-[0.3em] uppercase text-black/30 mb-2">Portrait</div>
                                    <div class="text-3xl font-light text-black/20">{{ $m['n'] }}</div>
                                </div>
                            </div>
                            {{-- cyan line hover --}}
                            <span class="absolute bottom-0 left-0 h-[3px] w-full bg-[#54DCE3] scale-x-0 origin-left group-hover:scale-x-100 transition-transform duration-500"></span>
                        </div>

                        <div class="p-6 lg:p-7 border-t border-black/10">
                            <div class="text-[10px] tracking-[0.3em] uppercase text-black/40 mb-4 transition-transform duration-500 group-hover:-translate-y-[2px]">
                                N° {{ $m['n'] }}
                            </div>
                            <h3 class="text-lg lg:text-xl font-normal tracking-tight leading-snug mb-3">
                                {{ $m['name'] }}
                            </h3>
                            <p class="text-xs tracking-[0.2em] uppercase text-black/50 group-hover:text-[#54DCE3] transition-colors duration-300">
                                {{ $m['role'] }}
                            </p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>


    <!-- ============================================================
         LATEST FROM CREATE EVE
         ============================================================ -->
    <section id="latest" class="border-b border-black/10">
        <div class="mx-auto max-w-[1400px] px-6 sm:px-8 lg:px-12 py-20 lg:py-32">

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8 mb-14 lg:mb-20">
                <div>
                    <div class="text-[10px] tracking-[0.3em] uppercase text-black/40 mb-3">
                        Section 06 / Journal
                    </div>
                    <h2 class="text-[clamp(2rem,5vw,4.5rem)] font-light tracking-[-0.02em] leading-[1.02]">
                        Editor's <br>
                        <span class="italic font-normal">Picks</span>
                    </h2>
                </div>

                <a href="{{ route('explore') }}"
                class="group inline-flex items-center gap-3 text-sm tracking-[0.15em] uppercase text-black/70 hover:text-black transition-colors">
                    <span class="relative">
                        View all articles
                        <span class="absolute left-0 -bottom-1 h-[1px] w-full bg-black/30 group-hover:bg-[#54DCE3] transition-colors"></span>
                    </span>
                    <span class="transition-transform group-hover:translate-x-1">→</span>
                </a>
            </div>

            @if ($featured)

                <div class="grid grid-cols-12 gap-8 lg:gap-12">

                    {{-- FEATURED ARTICLE --}}
                    <a href="{{ route('article.read', $featured->slug) }}"
                    class="group col-span-12 lg:col-span-7 block">

                        <div class="relative aspect-[16/11] bg-[#F5F5F5] overflow-hidden mb-6 border border-black/10">

                            @if ($featured->cover_image)
                                <img
                                    src="{{ imageUrl($featured->cover_image) }}"
                                    alt="{{ $featured->title }}"
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03]"
                                >
                            @else
                                <div class="absolute inset-0 flex items-center justify-center transition-transform duration-700 group-hover:scale-[1.03]">
                                    <div class="text-center">
                                        <div class="text-[10px] tracking-[0.3em] uppercase text-black/30 mb-2">
                                            Featured Image
                                        </div>
                                        <div class="text-4xl font-light text-black/20">
                                            01
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <span class="absolute top-4 left-4 bg-black text-white text-[10px] tracking-[0.25em] uppercase px-3 py-1.5">
                                Featured
                            </span>
                        </div>

                        <div class="flex items-center gap-4 text-[10px] tracking-[0.3em] uppercase text-black/50 mb-4 flex-wrap">
                            @if ($featured->category)
                                <span class="text-[#54DCE3]">
                                    {{ $featured->category->name }}
                                </span>

                                <span class="h-[1px] w-6 bg-black/20"></span>
                            @endif

                            <span>
                                {{ \Carbon\Carbon::parse($featured->published_at ?? $featured->created_at)->format('M d, Y') }}
                            </span>

                            @if ($featured->author)
                                <span class="h-[1px] w-6 bg-black/20"></span>

                                <span>
                                    {{ $featured->author->name }}
                                </span>
                            @endif
                        </div>

                        <h3 class="text-[clamp(1.5rem,3vw,2.5rem)] font-light tracking-[-0.02em] leading-[1.1] mb-4 group-hover:text-[#54DCE3] transition-colors duration-300">
                            {{ $featured->title }}
                        </h3>

                        @if ($featured->description)
                            <p class="text-black/70 leading-relaxed font-light mb-6 max-w-2xl">
                                {{ $featured->description }}
                            </p>
                        @endif

                        <span class="inline-flex items-center gap-3 text-xs tracking-[0.25em] uppercase text-black group-hover:text-[#54DCE3] transition-colors">
                            Read article
                            <span class="transition-transform group-hover:translate-x-1">→</span>
                        </span>
                    </a>


                    {{-- SMALLER ARTICLES --}}
                    <div class="col-span-12 lg:col-span-5 lg:pl-8 lg:border-l border-black/10">

                        <div class="border-t border-black/10">

                            @forelse ($articles as $article)

                                <a href="{{ route('article.read', $article->slug) }}"
                                class="group block py-7 border-b border-black/10">

                                    <div class="flex items-start gap-5">

                                        {{-- THUMBNAIL --}}
                                        <div class="shrink-0 w-20 h-20 sm:w-24 sm:h-24 bg-[#F5F5F5] border border-black/10 relative overflow-hidden">

                                            @if ($article->cover_image)

                                                <img
                                                    src="{{ imageUrl($article->cover_image) }}"
                                                    alt="{{ $article->title }}"
                                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                                >

                                            @else

                                                <div class="absolute inset-0 flex items-center justify-center text-black/25 text-xs tracking-[0.2em]">
                                                    {{ str_pad($loop->iteration + 1, 2, '0', STR_PAD_LEFT) }}
                                                </div>

                                            @endif

                                        </div>


                                        {{-- CONTENT --}}
                                        <div class="flex-1 min-w-0">

                                            <div class="flex items-center gap-3 text-[10px] tracking-[0.25em] uppercase text-black/50 mb-2 flex-wrap">

                                                @if ($article->category)
                                                    <span class="text-[#54DCE3]">
                                                        {{ $article->category->name }}
                                                    </span>

                                                    <span class="h-[1px] w-4 bg-black/20"></span>
                                                @endif

                                                <span>
                                                    {{ \Carbon\Carbon::parse($article->published_at ?? $article->created_at)->format('M d, Y') }}
                                                </span>

                                            </div>

                                            <h4 class="text-base sm:text-lg font-normal tracking-tight leading-snug mb-2 group-hover:text-[#54DCE3] transition-colors duration-300">
                                                {{ $article->title }}
                                            </h4>

                                            @if ($article->description)
                                                <p class="text-xs text-black/60 font-light leading-relaxed line-clamp-2">
                                                    {{ $article->description }}
                                                </p>
                                            @endif

                                            @if ($article->author)
                                                <div class="mt-3 text-[10px] tracking-[0.25em] uppercase text-black/40">
                                                    {{ $article->author->name }}
                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </a>

                            @empty

                                <div class="py-12 text-center border-b border-black/10">
                                    <p class="text-sm text-black/40 font-light">
                                        No featured articles available yet.
                                    </p>
                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            @else

                {{-- NO FEATURED ARTICLE --}}
                <div class="border border-black/10 bg-[#F5F5F5] py-20 px-6 text-center">
                    <div class="text-[10px] tracking-[0.3em] uppercase text-black/30 mb-3">
                        Journal
                    </div>

                    <h3 class="text-2xl font-light tracking-tight mb-3">
                        No featured article yet.
                    </h3>

                    <p class="text-sm text-black/50 font-light">
                        Featured articles will appear here once they are published.
                    </p>
                </div>

            @endif

        </div>
    </section>


    <!-- ============================================================
         FINAL CTA
         ============================================================ -->
    <section class="bg-black text-white relative overflow-hidden">
        {{-- grid overlay --}}
        <div class="pointer-events-none absolute inset-0 opacity-[0.06]"
             style="background-image: linear-gradient(to right, #fff 1px, transparent 1px), linear-gradient(to bottom, #fff 1px, transparent 1px); background-size: 80px 80px;">
        </div>

        <div class="relative mx-auto max-w-[1400px] px-6 sm:px-8 lg:px-12 py-24 lg:py-40">

            <div class="grid grid-cols-12 gap-8 lg:gap-12 items-end">

                <div class="col-span-12 lg:col-span-8">
                    <div class="text-[10px] tracking-[0.3em] uppercase text-white/40 mb-6">Stay curious</div>
                    <h2 class="text-[clamp(2.5rem,7vw,6rem)] font-light tracking-[-0.03em] leading-[0.98]">
                        Stay curious.<br>
                        <span class="italic font-normal text-[#54DCE3]">Keep learning.</span>
                    </h2>
                    <p class="mt-8 max-w-xl text-white/70 font-light text-base sm:text-lg leading-relaxed">
                        Explore ideas, insights, and stories from Create Eve.
                    </p>

                    <div class="mt-12">
                        <a href="#latest"
                           class="group inline-flex items-center gap-4 bg-[#54DCE3] text-black px-8 py-5 text-sm tracking-[0.2em] uppercase hover:bg-white transition-colors duration-300">
                            <span>Explore the Blog</span>
                            <span class="transition-transform group-hover:translate-x-1">→</span>
                        </a>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-4 lg:pl-8 lg:border-l border-white/15">
                    <div class="space-y-5 text-xs">
                        <div class="flex justify-between border-t border-white/15 pt-4">
                            <span class="tracking-[0.25em] uppercase text-white/40">Journal</span>
                            <span class="text-white/80">Create Eve</span>
                        </div>
                        <div class="flex justify-between border-t border-white/15 pt-4">
                            <span class="tracking-[0.25em] uppercase text-white/40">Field</span>
                            <span class="text-white/80">Digital / MSME</span>
                        </div>
                        <div class="flex justify-between border-t border-white/15 pt-4">
                            <span class="tracking-[0.25em] uppercase text-white/40">Since</span>
                            <span class="text-white/80">2020</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ============================================================
         INLINE SCRIPT — Mission / Vision tab + tab underline
         ============================================================ --}}
    <script>
        (function () {
            const missionText = "At Create Eve, our mission is to provide a digital presence foundation (WhatsApp Business, Instagram Business, Google Maps) for MSMEs instantly, easily, and affordably through smart automation technology.";
            const visionText  = "At Create Eve, our vision is to become the leading digital onboarding platform that empowers millions of MSMEs in Indonesia to grow and compete in the digital era.";

            const data = {
                mission: { text: missionText, letter: 'M', label: 'Mission Statement' },
                vision:  { text: visionText,  letter: 'V', label: 'Vision Statement'  },
            };

            const tabs   = document.querySelectorAll('.tab-btn');
            const textEl = document.getElementById('tab-text');
            const numEl  = document.getElementById('tab-number');
            const labEl  = document.getElementById('tab-label');

            function setActive(key) {
                tabs.forEach(t => {
                    if (t.dataset.tab === key) {
                        t.classList.remove('text-black/30', 'hover:text-black/60');
                        t.classList.add('text-black');
                        // underline
                        let u = t.querySelector('.tab-underline');
                        if (!u) {
                            u = document.createElement('span');
                            u.className = 'tab-underline absolute left-0 bottom-0 h-[3px] w-full bg-[#54DCE3] origin-left scale-x-0 transition-transform duration-500';
                            t.appendChild(u);
                        }
                        requestAnimationFrame(() => u.classList.remove('scale-x-0'));
                    } else {
                        t.classList.add('text-black/30', 'hover:text-black/60');
                        t.classList.remove('text-black');
                        const u = t.querySelector('.tab-underline');
                        if (u) u.classList.add('scale-x-0');
                    }
                });

                // fade text
                if (textEl) {
                    textEl.style.opacity = '0';
                    setTimeout(() => {
                        textEl.textContent = data[key].text;
                        textEl.style.opacity = '1';
                    }, 180);
                }
                if (numEl) numEl.textContent = data[key].letter;
                if (labEl) labEl.textContent = data[key].label;
            }

            tabs.forEach(t => {
                t.addEventListener('click', () => setActive(t.dataset.tab));
            });

            setActive('mission');
        })();
    </script>

</x-layouts.app>