<footer class="mt-20 border-t border-gray-100 bg-white">
    <div class="mx-auto max-w-6xl px-6 py-12">

        <div class="grid grid-cols-1 gap-10 md:grid-cols-3 lg:gap-16">

            {{-- Brand --}}
            <div>
                <a href="/" class="inline-flex items-center">
                    <img
                        src="{{ asset('logo/createeve.png') }}"
                        alt="Create Eve"
                        class="h-12 w-auto object-contain"
                    >
                </a>

                <p class="mt-5 max-w-[220px] text-base font-bold leading-tight text-gray-900">
                    Ultimate digital solution by
                    <br>
                    Digital Excellence
                </p>

                {{-- Social Media --}}
                <div class="mt-6 flex items-center gap-3">

                    {{-- Instagram --}}
                    <a
                        href="https://www.instagram.com/create.eve.agency/"
                        aria-label="Instagram"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-50 text-cyan-500 transition-colors hover:bg-cyan-100"
                    >
                        <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5a4.25 4.25 0 0 0 4.25 4.25h8.5a4.25 4.25 0 0 0 4.25-4.25v-8.5a4.25 4.25 0 0 0-4.25-4.25h-8.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Zm5.25-2.25a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25Z"/>
                        </svg>
                    </a>

                    {{-- Facebook --}}
                    <a
                        href="#"
                        aria-label="Facebook"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-50 text-cyan-500 transition-colors hover:bg-cyan-100"
                    >
                        <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13.5 21v-8h2.75l.5-3h-3.25V8.05c0-.87.24-1.55 1.57-1.55H17V3.8c-.33-.04-1.27-.12-2.42-.12-2.4 0-4.05 1.46-4.05 4.14V10H8v3h2.53v8h2.97Z"/>
                        </svg>
                    </a>

                    {{-- LinkedIn --}}
                    <a
                        href="#"
                        aria-label="LinkedIn"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-50 text-cyan-500 transition-colors hover:bg-cyan-100"
                    >
                        <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6.5 8.25A1.75 1.75 0 1 1 6.5 4.75a1.75 1.75 0 0 1 0 3.5ZM5 9.75h3V19H5V9.75Zm4.75 0h2.88v1.27h.04c.4-.76 1.38-1.56 2.84-1.56 3.04 0 3.6 2 3.6 4.6V19h-3v-4.37c0-1.04-.02-2.38-1.45-2.38-1.45 0-1.67 1.13-1.67 2.3V19h-3V9.75Z"/>
                        </svg>
                    </a>

                    {{-- YouTube --}}
                    <a
                        href="#"
                        aria-label="YouTube"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-50 text-cyan-500 transition-colors hover:bg-cyan-100"
                    >
                        <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21.58 7.19a2.75 2.75 0 0 0-1.93-1.94C17.95 4.75 12 4.75 12 4.75s-5.95 0-7.65.5a2.75 2.75 0 0 0-1.93 1.94C1.92 8.9 1.92 12 1.92 12s0 3.1.5 4.81a2.75 2.75 0 0 0 1.93 1.94c1.7.5 7.65.5 7.65.5s5.95 0 7.65-.5a2.75 2.75 0 0 0 1.93-1.94c.5-1.71.5-4.81.5-4.81s0-3.1-.5-4.81ZM10 15.5v-7l6 3.5-6 3.5Z"/>
                        </svg>
                    </a>

                </div>
            </div>

            {{-- Details --}}
            <div>
                <h3 class="text-base font-bold text-gray-900">
                    Details
                </h3>

                <div class="mt-4 space-y-3">
                    <a
                        href="{{ route('legal-notice') }}"
                        class="block text-sm text-gray-600 transition-colors hover:text-black"
                    >
                        Legal Notice
                    </a>

                    <a
                        href="{{ route('privacy-policy') }}"
                        class="block text-sm text-gray-600 transition-colors hover:text-black"
                    >
                        Privacy Policy
                    </a>

                    <span class="block text-sm text-gray-600">
                        All rights reserved
                    </span>
                </div>

                {{-- Main Editorial Office --}}
                <div class="mt-7">
                    <h4 class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-400">
                        Main Editorial Office
                    </h4>

                    <div class="mt-3 space-y-3 text-sm text-gray-600">

                        {{-- Jakarta --}}
                        <div class="flex items-start gap-2.5">
                            <svg
                                class="mt-0.5 h-4 w-4 flex-shrink-0 text-cyan-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.7"
                                    d="M12 21s7-5.25 7-11a7 7 0 1 0-14 0c0 5.75 7 11 7 11Z"
                                />
                                <circle
                                    cx="12"
                                    cy="10"
                                    r="2.25"
                                    stroke-width="1.7"
                                />
                            </svg>

                            <p class="leading-relaxed">
                                Jakarta Garden City, Jalan Matana 3 No.50,<br>
                                Cakung Timur, Jakarta Timur,<br>
                                DKI Jakarta 13910
                            </p>
                        </div>

                        {{-- Jambi --}}
                        <div class="flex items-start gap-2.5">
                            <svg
                                class="mt-0.5 h-4 w-4 flex-shrink-0 text-cyan-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.7"
                                    d="M12 21s7-5.25 7-11a7 7 0 1 0-14 0c0 5.75 7 11 7 11Z"
                                />
                                <circle
                                    cx="12"
                                    cy="10"
                                    r="2.25"
                                    stroke-width="1.7"
                                />
                            </svg>

                            <p class="leading-relaxed">
                                Jl. Elang 1, Talang Jauh,<br>
                                Jelutung, Jambi
                            </p>
                        </div>

                        {{-- Tangerang --}}
                        <div class="flex items-start gap-2.5">
                            <svg
                                class="mt-0.5 h-4 w-4 flex-shrink-0 text-cyan-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.7"
                                    d="M12 21s7-5.25 7-11a7 7 0 1 0-14 0c0 5.75 7 11 7 11Z"
                                />
                                <circle
                                    cx="12"
                                    cy="10"
                                    r="2.25"
                                    stroke-width="1.7"
                                />
                            </svg>

                            <p class="leading-relaxed">
                                Paddington Heights, Alam Sutera,<br>
                                Tangerang, Banten
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div>
                <h3 class="text-base font-bold text-gray-900">
                    Contact
                </h3>

                <div class="mt-4 space-y-3">

                {{-- Phone --}}
                <a
                    href="tel:08888111881"
                    class="group flex items-center gap-2.5 text-sm text-gray-600 transition-colors hover:text-black"
                >
                    <svg
                        class="h-4 w-4 flex-shrink-0 text-cyan-500 transition-colors group-hover:text-black"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M5 4.5A1.5 1.5 0 0 1 6.5 3h2l1.5 4-2 1.5a12 12 0 0 0 5.5 5.5l1.5-2 4 1.5v2A1.5 1.5 0 0 1 17.5 17C10.596 17 5 11.404 5 4.5Z"
                        />
                    </svg>

                    <span>+62 8888-111-881</span>
                </a>

                {{-- Email --}}
                <a
                    href="mailto:admin@dinamikapublika.id"
                    class="group flex items-center gap-2.5 text-sm text-gray-600 transition-colors hover:text-black"
                >
                    <svg
                        class="h-4 w-4 flex-shrink-0 text-cyan-500 transition-colors group-hover:text-black"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <rect
                            x="3"
                            y="5"
                            width="18"
                            height="14"
                            rx="2"
                            stroke-width="1.7"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="m4 7 8 6 8-6"
                        />
                    </svg>

                    <span>admin@create-eve.com</span>
                </a>

                {{-- Office Hours --}}
                <div class="flex items-center gap-2.5 text-sm text-gray-600">
                    <svg
                        class="h-4 w-4 flex-shrink-0 text-cyan-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="8.5"
                            stroke-width="1.7"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M12 7v5l3 2"
                        />
                    </svg>

                    <span>08:00 AM - 07:00 PM</span>
                </div>

            </div>

               {{-- Editorial Offices --}}
                <div class="mt-7">
                    <h4 class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-400">
                        Editorial Offices
                    </h4>

                    <div class="mt-3 space-y-3 text-sm text-gray-600">

                        {{-- Australia --}}
                        <div class="flex items-start gap-2.5">
                            <svg
                                class="mt-0.5 h-4 w-4 flex-shrink-0 text-cyan-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.7"
                                    d="M12 21s7-5.25 7-11a7 7 0 1 0-14 0c0 5.75 7 11 7 11Z"
                                />
                                <circle
                                    cx="12"
                                    cy="10"
                                    r="2.25"
                                    stroke-width="1.7"
                                />
                            </svg>

                            <div>
                                <p class="font-semibold text-gray-900">
                                    Australia
                                </p>
                                <p class="mt-1 leading-relaxed">
                                    The Oak Building, 253 Waverley Road<br>
                                    Malvern East Victoria, 3154.
                                </p>
                            </div>
                        </div>

                        {{-- Malaysia --}}
                        <div class="flex items-start gap-2.5">
                            <svg
                                class="mt-0.5 h-4 w-4 flex-shrink-0 text-cyan-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.7"
                                    d="M12 21s7-5.25 7-11a7 7 0 1 0-14 0c0 5.75 7 11 7 11Z"
                                />
                                <circle
                                    cx="12"
                                    cy="10"
                                    r="2.25"
                                    stroke-width="1.7"
                                />
                            </svg>

                            <div>
                                <p class="font-semibold text-gray-900">
                                    Malaysia
                                </p>
                                <p class="mt-1 leading-relaxed">
                                    Jalan Batai 7, Taman Sri Pulai,<br>
                                    Johor Bahru 81110.
                                </p>
                            </div>
                        </div>

                        {{-- Singapore --}}
                        <div class="flex items-start gap-2.5">
                            <svg
                                class="mt-0.5 h-4 w-4 flex-shrink-0 text-cyan-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.7"
                                    d="M12 21s7-5.25 7-11a7 7 0 1 0-14 0c0 5.75 7 11 7 11Z"
                                />
                                <circle
                                    cx="12"
                                    cy="10"
                                    r="2.25"
                                    stroke-width="1.7"
                                />
                            </svg>

                            <div>
                                <p class="font-semibold text-gray-900">
                                    Singapore
                                </p>
                                <p class="mt-1 leading-relaxed">
                                    Building 504 Woodland Drive 14 #03 130,<br>
                                    Block 504, 730504.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- Copyright --}}
        <div class="mt-10 border-t border-gray-100 pt-5">
            <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} Create Eve. All rights reserved.
            </p>
        </div>

    </div>
</footer>