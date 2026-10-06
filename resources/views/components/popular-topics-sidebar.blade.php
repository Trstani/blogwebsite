@props(['tags' => []])

<aside class="rounded-lg border border-gray-100 bg-white p-4">
    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-900">
        Popular Topics
    </h2>

    @if(count($tags) > 0)
        <div class="space-y-2">
            @foreach($tags as $tag)
                <a
                    href="{{ route('explore', ['tag' => $tag->slug]) }}"
                    class="group flex items-center justify-between rounded-lg border border-gray-100 px-3 py-2.5 transition-all hover:border-black hover:bg-gray-50"
                >
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 group-hover:text-black truncate">
                            {{ $tag->name }}
                        </p>
                    </div>
                    <span class="ml-2 flex-shrink-0 text-xs font-medium text-gray-400 group-hover:text-gray-600">
                        {{ $tag->articles_count }}
                    </span>
                </a>
            @endforeach
        </div>
    @else
        <p class="py-6 text-center text-sm text-gray-400">
            No topics yet.
        </p>
    @endif
</aside>
