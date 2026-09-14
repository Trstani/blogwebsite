<x-layouts.app title="Profile">

    <div class="max-w-6xl mx-auto px-6 py-12">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Avatar Section --}}
        <div class="flex flex-col items-center mb-10">
            <div class="relative group">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}"
                         alt="{{ $user->name }}"
                         class="w-28 h-28 rounded-full object-cover" />
                @else
                    <div class="w-28 h-28 bg-gray-100 rounded-full flex items-center justify-center">
                        <span class="text-4xl font-bold text-gray-400">
                            {{ substr($user->name, 0, 1) }}
                        </span>
                    </div>
                @endif

                {{-- Hover Overlay (OWNER ONLY) --}}
                @if($isOwner)
                    <div class="absolute inset-0 rounded-full bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        <label for="avatarInput" class="cursor-pointer px-3 py-1.5 text-xs font-medium text-white bg-white/20 backdrop-blur-sm rounded-md hover:bg-white/30 transition-colors">
                            Change
                        </label>
                        <input type="file" id="avatarInput" accept="image/*" class="hidden" onchange="handleAvatarUpload(this)" />

                        @if($user->avatar)
                            <form method="POST" action="{{ route('profile.avatar.delete', $user->slug) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Remove avatar?')"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-red-500/80 backdrop-blur-sm rounded-md hover:bg-red-600 transition-colors">
                                    Remove
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            <h1 class="mt-4 text-xl font-bold text-black">{{ $user->name }}</h1>
            <p class="text-sm text-gray-400">{{ $user->email }}</p>
        </div>

        {{-- Bio Section --}}
        @if($isOwner)
            {{-- Owner: Editable --}}
            <div class="bg-white border border-gray-100 rounded-lg p-6 mb-8">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">About Me</h2>
                <form method="POST" action="{{ route('profile.bio', $user->slug) }}">
                    @csrf
                    @method('PUT')
                    <textarea name="bio"
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors resize-none"
                              placeholder="Tell us about yourself...">{{ $user->bio }}</textarea>
                    <div class="flex justify-end mt-3">
                        <button type="submit"
                                class="px-5 py-2 text-sm font-medium text-white bg-black rounded-md hover:bg-gray-800 transition-colors">
                            Save Bio
                        </button>
                    </div>
                </form>
            </div>
        @elseif($user->bio)
            {{-- Visitor: Read only --}}
            <div class="bg-white border border-gray-100 rounded-lg p-6 mb-8">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">About</h2>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $user->bio }}</p>
            </div>
        @endif

        {{-- Articles --}}
        <div class="bg-white border border-gray-100 rounded-lg p-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                Articles by {{ $user->name }} ({{ $articles->count() }})
            </h2>

            @if($articles->count() > 0)
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach($articles as $article)
                        <x-maincomponents.article-card :article="(object)[
                            'id'          => $article->id,
                            'title'       => $article->title,
                            'slug'        => $article->slug,
                            'description' => $article->description,
                            'category'    => $article->category->name ?? '',
                            'author'      => $user->name,
                            'date'        => $article->created_at->format('M d, Y'),
                            'status'      => $article->status,
                            'thumbnail'   => $article->cover_image ?: null,
                        ]" />
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-8">No articles yet.</p>
            @endif
        </div>

    </div>

    <script>
        function getCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                throw new Error('CSRF token not found. Make sure meta[name="csrf-token"] is in your HTML head.');
            }
            return token;
        }

        function handleAvatarUpload(input) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            console.log('Avatar file selected:', file.name, file.size);

            // Upload to local storage
            const uploadFormData = new FormData();
            uploadFormData.append('file', file);

            const csrfToken = getCsrfToken();
            const slug = '{{ $user->slug }}';

            fetch('/local-upload/avatar', {
                method: 'POST',
                body: uploadFormData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            })
            .then(res => {
                console.log('Avatar upload response status:', res.status);
                if (!res.ok) {
                    // Server returned error (422, 500, etc)
                    return res.text().then(text => {
                        // Try to parse as JSON, fallback to text
                        try {
                            const json = JSON.parse(text);
                            throw new Error(json.message || 'Server error');
                        } catch {
                            throw new Error('Server error: ' + res.status);
                        }
                    });
                }
                return res.json();
            })
            .then(data => {
                console.log('Local upload response:', data);

                if (data.success && data.path) {
                    // Prepend /storage/ prefix for correct rendering
                    const avatarUrl = data.path.startsWith('/storage/') ? data.path : '/storage/' + data.path;
                    
                    // Send path to Laravel to save
                    return fetch(`/profile/${slug}/avatar`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            image_url: avatarUrl,
                            public_id: null,
                        }),
                    });
                } else {
                    throw new Error('Local upload failed: ' + (data.message || 'Unknown error'));
                }
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Failed to save avatar: HTTP ' + res.status);
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Avatar saved successfully');
                    location.reload();
                } else {
                    console.error('Failed to save avatar:', data);
                    alert('Failed to save avatar');
                }
            })
            .catch(err => {
                console.error('Avatar upload error:', err);
                alert('Avatar upload failed: ' + err.message);
            });
        }
    </script>

</x-layouts.app>
