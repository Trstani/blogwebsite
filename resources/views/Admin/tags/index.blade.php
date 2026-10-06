<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">
                Tags
            </h2>
            <p class="text-xs text-gray-400 mt-1">
                Manage the topics used to organize articles.
            </p>
        </div>
        <a href="{{ route('admin.tags.create') }}"
           class="px-4 py-2 text-sm font-medium text-white bg-black rounded-md hover:bg-gray-800 transition-colors">
            + New Tag
        </a>
    </div>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm font-medium text-red-700 mb-2">Please fix the following errors:</p>
            <ul class="text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tags Table --}}
    @if($tags->count() > 0)
        <div class="bg-white rounded-lg border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-6 py-4 text-left font-medium text-gray-900">Tag Name</th>
                            <th class="px-6 py-4 text-left font-medium text-gray-900">Slug</th>
                            <th class="px-6 py-4 text-left font-medium text-gray-900">Articles</th>
                            <th class="px-6 py-4 text-left font-medium text-gray-900">Created</th>
                            <th class="px-6 py-4 text-right font-medium text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tags as $tag)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-black">{{ $tag->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <code class="text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded">{{ $tag->slug }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-600">
                                        {{ $tag->articles_count }}
                                        {{ $tag->articles_count === 1 ? 'article' : 'articles' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $tag->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.tags.edit', $tag) }}"
                                           class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                            Edit
                                        </a>
                                        <button type="button"
                                                onclick="openDeleteModal({{ $tag->id }}, '{{ addslashes($tag->name) }}', {{ $tag->articles_count }})"
                                                class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-md hover:bg-red-100 transition-colors">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg border border-gray-100 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <p class="text-gray-900 font-medium">No tags yet</p>
            <p class="text-sm text-gray-500 mt-1 mb-4">Create your first tag to organize articles.</p>
            <a href="{{ route('admin.tags.create') }}"
               class="inline-block px-4 py-2 text-sm font-medium text-white bg-black rounded-md hover:bg-gray-800 transition-colors">
                Create Tag
            </a>
        </div>
    @endif
</div>

{{-- Delete Modal --}}
<div id="deleteModal" class="hidden fixed inset-0 z-50 items-center justify-center">
    <div class="absolute inset-0 bg-black/40" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
        <h2 class="text-lg font-bold text-black mb-2">Delete this tag?</h2>
        <p id="deleteMessage" class="text-sm text-gray-600 mb-6"></p>
        <div class="flex justify-end gap-3">
            <button type="button"
                    onclick="closeDeleteModal()"
                    class="px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-md hover:border-gray-300 hover:text-gray-700 transition-colors">
                Cancel
            </button>
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                    Delete Tag
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteModal(tagId, tagName, articleCount) {
    const message = articleCount > 0
        ? `This tag is currently used by ${articleCount} ${articleCount === 1 ? 'article' : 'articles'}. Deleting it will remove the tag from those articles.`
        : 'Are you sure you want to delete this tag?';
    
    document.getElementById('deleteMessage').textContent = message;
    document.getElementById('deleteForm').action = '/admin/tags/' + tagId;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}
</script>
