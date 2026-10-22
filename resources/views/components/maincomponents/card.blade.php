
@props(['article'])

<div class="group flex flex-col gap-3 rounded-lg border border-gray-100 bg-white p-3 transition-colors hover:border-gray-300 sm:flex-row sm:gap-5 sm:p-4">
    {{-- Thumbnail --}}
    <a href="/blog/{{ $article->slug ?? '#' }}" class="block aspect-[16/9] w-full flex-shrink-0 overflow-hidden rounded-md bg-gray-100 sm:h-24 sm:w-32 sm:aspect-auto">
        @if($article->thumbnail ?? false)
            <img src="{{ imageUrl($article->thumbnail) }}" alt="{{ $article->title ?? 'Article' }}" class="h-full w-full object-cover transition-transform group-hover:scale-[1.02]" />
        @else
            <div class="flex h-full w-full items-center justify-center">
                <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                </svg>
            </div>
        @endif
    </a>

    {{-- Content --}}
    <a href="/blog/{{ $article->slug ?? '#' }}" class="block min-w-0 flex-1">
        @if($article->category ?? false)
            <span class="text-xs text-gray-400">{{ $article->category }}</span>
        @endif
        <h3 class="line-clamp-2 text-base font-semibold leading-snug text-black transition-colors group-hover:text-cyan-700 sm:truncate">{{ $article->title ?? 'Article Title' }}</h3>
        <p class="mt-1 line-clamp-2 text-sm leading-relaxed text-gray-500">{{ $article->description ?? '' }}</p>
        <div class="mt-2 flex items-center gap-3 text-xs text-gray-400">
            @if($article->author ?? false)
                <span>{{ $article->author }}</span><span>·</span>
            @endif
            @if($article->date ?? false)
                <span>{{ $article->date }}</span>
            @endif
        </div>
    </a>

    {{-- Status + Actions --}}
    <div class="flex flex-shrink-0 items-center justify-between gap-2 sm:flex-col sm:items-end sm:justify-start">
        @if(($article->status ?? '') === 'published')
            <span class="whitespace-nowrap rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Published</span>
        @elseif(($article->status ?? '') === 'pending')
            <span class="whitespace-nowrap rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-700">Pending</span>
        @elseif(($article->status ?? '') === 'rejected')
            <span class="whitespace-nowrap rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700">Rejected</span>
        @else
            <span class="whitespace-nowrap rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500">Draft</span>
        @endif

        <div class="flex items-center gap-2 sm:flex-col sm:items-end">
            <a href="/writer/write/{{ $article->id }}" class="inline-flex items-center gap-1 rounded-md border border-blue-100 bg-blue-50 px-2.5 py-1 text-[11px] font-medium text-blue-600 transition-all hover:bg-blue-100">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a2.12 2.12 0 113 3l-9.5 9.5a4 4 0 01-1.69 1.06l-3.11.89.89-3.11a4 4 0 011.06-1.69l7.663-7.962z"/>
                </svg>
                Edit
            </a>

            @if($article->id ?? false)
                <form method="POST" action="{{ route('articles.delete', $article->id) }}" onsubmit="event.preventDefault(); openDeleteConfirm(this, {{ Illuminate\Support\Js::from($article->title ?? 'this article') }});" class="sm:mt-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1 rounded-md border border-gray-100 bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-400 transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
