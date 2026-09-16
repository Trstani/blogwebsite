<footer class="bg-white border-t border-gray-100 mt-24">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="grid md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 bg-black rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-xs">D</span>
                    </div>
                    <span class="text-sm font-semibold tracking-tight text-black">
                        Dinamika Publika
                    </span>
                </div>
                <p class="mt-3 text-sm text-gray-400 leading-relaxed">
                    Platform tulisan dan artikel dari tim Dinamika Publika.
                </p>
            </div>
            <div>
                <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Navigation</h4>
                <div class="mt-3 flex flex-col space-y-2">
                    <a href="/" class="text-sm text-gray-500 hover:text-black transition-colors">Home</a>
                    <a href="/blog" class="text-sm text-gray-500 hover:text-black transition-colors">Blog</a>
                    <a href="/about" class="text-sm text-gray-500 hover:text-black transition-colors">About</a>
                </div>
            </div>
            <div>
                <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Contact</h4>
                <div class="mt-3 flex flex-col space-y-2">
                    <a href="mailto:info@dinamikapublika.id" class="text-sm text-gray-500 hover:text-black transition-colors">
                        info@dinamikapublika.id
                    </a>
                </div>
            </div>
        </div>
        <div class="mt-12 pt-6 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between">
            <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} Dinamika Publika. All rights reserved.
            </p>
        </div>
    </div>
</footer>