<footer class="bg-white border-t border-gray-100 mt-24">
    <div class="max-w-6xl mx-auto px-6 py-14">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 lg:gap-20">

            {{-- Column 1: Brand --}}
            <div>
                <a href="/" class="inline-flex items-center">
                    <img
                        src="{{ asset('logo/createeve.png') }}"
                        alt="Create Eve"
                        class="w-auto h-12 object-contain"
                    >
                </a>

                <p class="mt-8 max-w-[220px] text-lg font-bold text-gray-900 leading-tight">
                    Ultimate digital solution by <br>
                    Digital Excellence
                </p>

                {{-- Social Media --}}
                <div class="mt-8 flex items-center gap-4">

                    {{-- Instagram --}}
                    <a href="https://www.instagram.com/create.eve.agency/"
                       aria-label="Instagram"
                       class="w-10 h-10 rounded-full bg-cyan-50 flex items-center justify-center text-cyan-500 hover:bg-cyan-100 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5a4.25 4.25 0 0 0 4.25 4.25h8.5a4.25 4.25 0 0 0 4.25-4.25v-8.5a4.25 4.25 0 0 0-4.25-4.25h-8.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Zm5.25-2.25a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25Z"/>
                        </svg>
                    </a>

                    {{-- Facebook --}}
                    <a href="#"
                       aria-label="Facebook"
                       class="w-10 h-10 rounded-full bg-cyan-50 flex items-center justify-center text-cyan-500 hover:bg-cyan-100 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13.5 21v-8h2.75l.5-3h-3.25V8.05c0-.87.24-1.55 1.57-1.55H17V3.8c-.33-.04-1.27-.12-2.42-.12-2.4 0-4.05 1.46-4.05 4.14V10H8v3h2.53v8h2.97Z"/>
                        </svg>
                    </a>

                    {{-- LinkedIn --}}
                    <a href="#"
                       aria-label="LinkedIn"
                       class="w-10 h-10 rounded-full bg-cyan-50 flex items-center justify-center text-cyan-500 hover:bg-cyan-100 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6.5 8.25A1.75 1.75 0 1 1 6.5 4.75a1.75 1.75 0 0 1 0 3.5ZM5 9.75h3V19H5V9.75Zm4.75 0h2.88v1.27h.04c.4-.76 1.38-1.56 2.84-1.56 3.04 0 3.6 2 3.6 4.6V19h-3v-4.37c0-1.04-.02-2.38-1.45-2.38-1.45 0-1.67 1.13-1.67 2.3V19h-3V9.75Z"/>
                        </svg>
                    </a>

                    {{-- YouTube --}}
                    <a href="#"
                       aria-label="YouTube"
                       class="w-10 h-10 rounded-full bg-cyan-50 flex items-center justify-center text-cyan-500 hover:bg-cyan-100 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21.58 7.19a2.75 2.75 0 0 0-1.93-1.94C17.95 4.75 12 4.75 12 4.75s-5.95 0-7.65.5a2.75 2.75 0 0 0-1.93 1.94C1.92 8.9 1.92 12 1.92 12s0 3.1.5 4.81a2.75 2.75 0 0 0 1.93 1.94c1.7.5 7.65.5 7.65.5s5.95 0 7.65-.5a2.75 2.75 0 0 0 1.93-1.94c.5-1.71.5-4.81.5-4.81s0-3.1-.5-4.81ZM10 15.5v-7l6 3.5-6 3.5Z"/>
                        </svg>
                    </a>

                </div>
            </div>


            {{-- Column 2: Details --}}
            <div>
                <h3 class="text-lg font-bold text-gray-900">
                    Details
                </h3>

                <div class="mt-7 flex flex-col space-y-4">
                    <a href="/legal"
                       class="text-sm text-gray-700 hover:text-black transition-colors">
                        Legal Notice
                    </a>

                    <a href="/privacy"
                       class="text-sm text-gray-700 hover:text-black transition-colors">
                        Privacy Policy
                    </a>

                    <span class="text-sm text-gray-700">
                        All rights reserved
                    </span>
                </div>

                {{-- Main Editorial Office --}}
                <div class="mt-10">

                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">
                        Main Editorial Office
                    </h4>

                    <div class="mt-5 text-sm leading-relaxed">
                        <p class="font-bold text-gray-900">
                            Indonesia
                        </p>

                        <div class="mt-4 text-gray-600">
                            <p>
                                Jakarta Garden City, Jalan Matana 3 No.50,<br>
                                Cakung Timur, Jakarta Timur,<br>
                                DKI Jakarta 13910
                            </p>

                            <p class="mt-4">
                                Jl. Elang 1, Talang Jauh,<br>
                                Jelutung, Jambi
                            </p>

                            <p class="mt-4">
                                Paddington Heights, Alam Sutera,<br>
                                Tangerang, Banten
                            </p>
                        </div>
                    </div>

                </div>
            </div>


            {{-- Column 3: Contact --}}
            <div>
                <h3 class="text-lg font-bold text-gray-900">
                    Contact
                </h3>

                <div class="mt-7 flex flex-col space-y-4">

                    <a href="tel:08888111881"
                       class="text-sm text-gray-700 hover:text-black transition-colors">
                        08888-111-881
                    </a>

                    <a href="mailto:admin@dinamikapublika.id"
                       class="text-sm text-gray-700 hover:text-black transition-colors">
                        admin@create-eve.com
                    </a>

                    <span class="text-sm text-gray-700">
                        08:00 AM - 07:00 PM
                    </span>

                </div>

                {{-- Editorial Offices --}}
                <div class="mt-10">

                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">
                        Editorial Offices
                    </h4>

                    <div class="mt-5 text-sm leading-relaxed">

                        <p class="font-bold text-gray-900">
                            Australia
                        </p>

                        <p class="mt-1 text-gray-600">
                            The Oak Building, 253 Waverley Road<br>
                            Malvern East Victoria, 3154.
                        </p>


                        <p class="mt-5 font-bold text-gray-900">
                            Malaysia
                        </p>

                        <p class="mt-1 text-gray-600">
                            Jalan Batai 7, Taman Sri Pulai,<br>
                            Johor Bahru 81110.
                        </p>


                        <p class="mt-5 font-bold text-gray-900">
                            Singapore
                        </p>

                        <p class="mt-1 text-gray-600">
                            Building 504 Woodland Drive 14 #03 130,<br>
                            Block 504, 730504.
                        </p>

                    </div>

                </div>
            </div>

        </div>

        {{-- Bottom --}}
        <div class="mt-14 pt-6 border-t border-gray-100">
            <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} Create Eve. All rights reserved.
            </p>
        </div>

    </div>
</footer>