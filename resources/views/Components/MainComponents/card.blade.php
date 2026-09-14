@props(['article'])
<a href="/writer/write/{{ $article->id }}" class="block group">
    <div class="flex gap-5 p-4 bg-white border border-gray-100 rounded-lg hover:border-gray-300 transition-colors">

        {{-- Thumbnail --}}
        <div class="flex-shrink-0 w-32 h-24 bg-gray-100 rounded-md overflow-hidden">
            @if($article->thumbnail ?? false)
                <img src="{{ imageUrl($article->thumbnail) }}" alt="{{ $article->title }}" class="w-full h-full object-cover" />
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            @if($article->category ?? false)
                <span class="text-xs text-gray-400">{{ $article->category }}</span>
            @endif
            <h3 class="text-base font-semibold text-black leading-snug truncate">{{ $article->title ?? 'Article Title' }}</h3>
            <p class="text-sm text-gray-500 mt-1 line-clamp-2 leading-relaxed">{{ $article->description ?? '' }}</p>
            <div class="mt-2 flex items-center space-x-3 text-xs text-gray-400">
                @if($article->author ?? false)
                    <span>{{ $article->author }}</span>
                    <span>·</span>
                @endif
                @if($article->date ?? false)
                    <span>{{ $article->date }}</span>
                @endif
            </div>
        </div>

        {{-- Status + Delete --}}
        <div class="flex-shrink-0 flex flex-col items-end gap-2">
            {{-- Status Badge --}}
            @if(($article->status ?? '') === 'published')
                <span class="text-xs font-medium text-green-700 bg-green-50 px-2.5 py-1 rounded-full">Published</span>
            @elseif(($article->status ?? '') === 'pending')
                <span class="text-xs font-medium text-yellow-700 bg-yellow-50 px-2.5 py-1 rounded-full">Pending</span>
            @elseif(($article->status ?? '') === 'rejected')
                <span class="text-xs font-medium text-red-700 bg-red-50 px-2.5 py-1 rounded-full">Rejected</span>
            @else
                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">Draft</span>
            @endif

            {{-- Delete Button --}}
            @if($article->id ?? false)
                <form method="POST" action="{{ route('articles.delete', $article->id) }}"
                    onsubmit="event.preventDefault(); openDeleteConfirm(this, '{{ $article->title }}');" class="mt-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium text-gray-400 bg-gray-50 border border-gray-100 rounded-md hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-all">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </form>
            @endif
        </div>

    </div>
</a>