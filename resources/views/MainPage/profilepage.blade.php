<x-layouts.app title="Profile">
    @include('components.modals.modal-helpers')

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
                                        onclick="event.preventDefault(); showConfirm('Remove Avatar?', 'Are you sure you want to remove your avatar? This action cannot be undone.', () => this.form.submit(), 'Remove', 'Cancel', true);"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-red-500/80 backdrop-blur-sm rounded-md hover:bg-red-600 transition-colors">
                                    Remove
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            
            @php
                $editingName = $errors->has('name');
            @endphp

            <div class="mt-4 flex flex-col items-center text-center">
                {{-- Tampilan nama normal --}}
                <div
                    id="display-name-view"
                    class="{{ $editingName ? 'hidden' : 'flex' }} items-center gap-2"
                >
                    <h1
                        id="display-name-text"
                        class="text-xl font-bold text-black"
                    >{{ $user->name }}</h1>

                    @if ($isOwner)
                        <button
                            type="button"
                            id="edit-display-name"
                            class="rounded-full p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                            aria-label="Edit nama profil"
                            title="Edit nama"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="17"
                                height="17"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z" />
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Editor hanya muncul saat tombol pensil diklik --}}
                @if ($isOwner)
                    <form
                        id="display-name-form"
                        action="{{ route('profile.name', $user->slug) }}"
                        method="POST"
                        class="{{ $editingName ? '' : 'hidden' }} mt-2"
                    >
                        @csrf
                        @method('PUT')

                        <div class="flex max-w-md items-center gap-2">
                            <input
                                type="text"
                                id="display-name-input"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                maxlength="255"
                                required
                                class="min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                aria-label="Nama profil"
                            >

                            <button
                                type="submit"
                                class="shrink-0 rounded-lg bg-blue-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-800"
                            >
                                Simpan
                            </button>

                            <button
                                type="button"
                                id="cancel-display-name"
                                class="shrink-0 rounded-lg px-3 py-2 text-sm text-gray-500 transition hover:bg-gray-100"
                            >
                                Batal
                            </button>
                        </div>

                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </form>
                @endif

                <p class="mt-1 text-sm text-gray-400">{{ $user->email }}</p>
            </div>

            @if ($isOwner)
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const view = document.getElementById('display-name-view');
                        const form = document.getElementById('display-name-form');
                        const input = document.getElementById('display-name-input');
                        const editButton = document.getElementById('edit-display-name');
                        const cancelButton = document.getElementById('cancel-display-name');

                        if (!view || !form || !input || !editButton || !cancelButton) {
                            return;
                        }

                        function openEditor() {
                            view.classList.add('hidden');
                            form.classList.remove('hidden');
                            input.focus();
                            input.select();
                        }

                        function closeEditor() {
                            input.value = @json($user->name);
                            form.classList.add('hidden');
                            view.classList.remove('hidden');
                        }

                        editButton.addEventListener('click', openEditor);
                        cancelButton.addEventListener('click', closeEditor);

                        input.addEventListener('keydown', function (event) {
                            if (event.key === 'Escape') {
                                event.preventDefault();
                                closeEditor();
                            }
                        });
                    });
                </script>
            @endif

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
            {{-- Tab Navigation --}}
            <div class="mb-6 border-b border-gray-200">
                <div class="flex gap-6">
                    <a href="{{ route('profile', ['slug' => $user->slug, 'tab' => 'articles']) }}"
                       class="pb-4 text-sm font-semibold transition-colors {{ $tab === 'articles' ? 'text-black border-b-2 border-black' : 'text-gray-500 hover:text-gray-700' }}">
                        My Articles
                    </a>
                    @if($isOwner)
                        <a href="{{ route('profile', ['slug' => $user->slug, 'tab' => 'liked']) }}"
                           class="pb-4 text-sm font-semibold transition-colors {{ $tab === 'liked' ? 'text-black border-b-2 border-black' : 'text-gray-500 hover:text-gray-700' }}">
                            Liked Articles
                        </a>
                        <a href="{{ route('profile', ['slug' => $user->slug, 'tab' => 'bookmarked']) }}"
                           class="pb-4 text-sm font-semibold transition-colors {{ $tab === 'bookmarked' ? 'text-black border-b-2 border-black' : 'text-gray-500 hover:text-gray-700' }}">
                            Bookmarked Articles
                        </a>
                    @endif
                </div>
            </div>

            {{-- Tab Content --}}
            @if($articles->count() > 0)
                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    @foreach($articles as $article)
                        <x-maincomponents.article-card :article="(object)[
                            'id'          => $article->id,
                            'title'       => $article->title,
                            'slug'        => $article->slug,
                            'description' => $article->description,
                            'category'    => $article->category->name ?? '',
                            'author'      => $article->author->name ?? 'Unknown',
                            'date'        => $article->published_at ? $article->published_at->format('M d, Y') : $article->created_at->format('M d, Y'),
                            'status'      => $article->status,
                            'thumbnail'   => $article->cover_image ?: null,
                            'views'       => $article->views ?? 0,
                        ]" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $articles->appends(request()->query())->links() }}
                </div>
            @else
                {{-- Empty States --}}
                <div class="text-sm text-gray-400 text-center py-12">
                    @if($tab === 'articles')
                        <p>{{ $isOwner ? "You haven't published any articles yet." : "$user->name hasn't published any articles yet." }}</p>
                    @elseif($tab === 'liked')
                        <p>You haven't liked any articles yet.</p>
                    @elseif($tab === 'bookmarked')
                        <p>You haven't bookmarked any articles yet.</p>
                    @endif
                </div>
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
                    showAlert('Error', 'Failed to save avatar', 'error');
                }
            })
            .catch(err => {
                console.error('Avatar upload error:', err);
                showAlert('Upload Error', 'Avatar upload failed: ' + err.message, 'error');
            });
        }
    </script>

</x-layouts.app>
