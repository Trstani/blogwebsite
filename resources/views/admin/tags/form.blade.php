<div>
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">
            {{ isset($tag) ? 'Edit Tag' : 'Create Tag' }}
        </h2>
        <p class="text-xs text-gray-400 mt-1">
            {{ isset($tag) ? 'Update tag information.' : 'Create a new tag to organize articles.' }}
        </p>
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

    {{-- Form --}}
    <div class="bg-white rounded-lg border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ isset($tag) ? route('admin.tags.update', $tag) : route('admin.tags.store') }}">
            @csrf
            @if(isset($tag))
                @method('PUT')
            @endif

            {{-- Tag Name Field --}}
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">
                    Tag Name <span class="text-red-600">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $tag->name ?? '') }}"
                    placeholder="e.g., Artificial Intelligence"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors @error('name') border-red-500 @enderror"
                    required
                />
                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug Field --}}
            <div class="mb-6">
                <label for="slug" class="block text-sm font-medium text-gray-900 mb-2">
                    Slug <span class="text-gray-400 font-normal">(optional — auto-generated if empty)</span>
                </label>
                <input
                    type="text"
                    id="slug"
                    name="slug"
                    value="{{ old('slug', $tag->slug ?? '') }}"
                    placeholder="e.g., artificial-intelligence"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors @error('slug') border-red-500 @enderror"
                />
                <p class="text-xs text-gray-400 mt-1">
                    Leave empty to auto-generate from tag name.
                </p>
                @error('slug')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center gap-3 pt-4">
                <button
                    type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-black rounded-md hover:bg-gray-800 transition-colors">
                    {{ isset($tag) ? 'Update Tag' : 'Create Tag' }}
                </button>
                <a href="{{ route('admin.tags.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-md hover:border-gray-300 hover:text-gray-700 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
