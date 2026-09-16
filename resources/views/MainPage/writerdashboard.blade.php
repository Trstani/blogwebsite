<x-layouts.app title="Writer Dashboard">

    <div class="max-w-6xl mx-auto px-6 py-8">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-black">My Articles</h1>
                <p class="text-sm text-gray-500 mt-1">Manage and create your articles</p>
            </div>
            <button onclick="openModal()" class="bg-black text-white px-5 py-2.5 rounded-md text-sm font-medium hover:bg-gray-800 transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Article
            </button>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="bg-white border border-gray-100 rounded-lg p-6">
                <p class="text-sm text-gray-500">Total Articles</p>
                <p class="text-3xl font-bold text-black mt-1">{{ $totalArticles }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-lg p-6">
                <p class="text-sm text-gray-500">Published</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $published }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-lg p-6">
                <p class="text-sm text-gray-500">Pending Review</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $pending }}</p>
            </div>
        </div>

        {{-- Articles List --}}
        <div class="space-y-3">
            @forelse($articles as $article)
                <x-maincomponents.card :article="(object)[
                    'id'          => $article->id,
                    'title'       => $article->title,
                    'description' => $article->description,
                    'category'    => $article->category->name ?? '',
                    'author'      => $article->author->name,
                    'date'        => $article->created_at->format('M d, Y'),
                    'status'      => $article->status,
                    'thumbnail'   => $article->cover_image ?: null,
                ]" />
            @empty
                <p class="text-sm text-gray-400 text-center py-8">Belum ada artikel. Klik "New Article" untuk mulai menulis!</p>
            @endforelse
        </div>

    </div>

    {{-- New Article Modal --}}
    <div id="newArticleModal" class="hidden fixed inset-0 z-50 items-center justify-center">
        <div class="absolute inset-0 bg-black/40" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-8">
            <h2 class="text-xl font-bold text-black mb-1">Create New Article</h2>
            <p class="text-sm text-gray-500 mb-6">Fill in the details before writing</p>
            <form id="articleForm" onsubmit="return submitArticle(event)">
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors" placeholder="Enter article title..." required />
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors bg-white" required>
                        <option value="" disabled selected>Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Cover Image</label>
                    <div id="coverPreview" class="w-full h-40 border-2 border-dashed border-gray-200 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:border-gray-400 transition-colors" onclick="document.getElementById('coverInput').click()">
                        <svg class="w-8 h-8 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <p class="text-xs text-gray-400">Click to upload cover image</p>
                    </div>
                    <input type="file" id="coverInput" accept="image/*" class="hidden" onchange="previewCover(this)" />
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Short Description</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors resize-none" placeholder="Brief description..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-sm text-gray-500 border border-gray-200 rounded-lg hover:border-black hover:text-black transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors">Start Writing →</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteConfirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" onclick="closeDeleteConfirm()"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-black">Delete Article?</h3>
            </div>
            <p id="deleteMessage" class="text-sm text-gray-600 mb-6">Are you sure you want to delete this article? This action cannot be undone.</p>
            <div class="flex gap-3 justify-end">
                <button onclick="closeDeleteConfirm()" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button id="confirmDeleteBtn" onclick="confirmDelete()" class="px-4 py-2.5 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        let pendingDeleteForm = null;

        function openDeleteConfirm(form, articleTitle = '') {
            pendingDeleteForm = form;
            const modal = document.getElementById('deleteConfirmModal');
            const message = document.getElementById('deleteMessage');
            
            if (articleTitle) {
                message.textContent = `Are you sure you want to delete "${articleTitle}"? This action cannot be undone.`;
            }
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteConfirm() {
            const modal = document.getElementById('deleteConfirmModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pendingDeleteForm = null;
        }

        function confirmDelete() {
            if (pendingDeleteForm) {
                pendingDeleteForm.submit();
            }
            closeDeleteConfirm();
        }

        function openModal() {
            const modal = document.getElementById('newArticleModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('newArticleModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function previewCover(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('coverPreview').innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover rounded-lg" />';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function submitArticle(e) {
            e.preventDefault();

            const form = document.getElementById('articleForm');
            const formData = new FormData(form);

            const title = formData.get('title');
            const category = formData.get('category');
            const description = formData.get('description');
            const coverFileInput = document.getElementById('coverInput');
            const coverFile = coverFileInput.files[0];

            const categoryId = formData.get('category');

            const selectedCategory = form.querySelector(
                'select[name="category"] option:checked'
            );

            const categoryName = selectedCategory?.textContent.trim() || '';

            sessionStorage.setItem('articleTitle', title);
            sessionStorage.setItem('articleCategory', categoryName);
            sessionStorage.setItem('articleCategoryId', categoryId);
            sessionStorage.setItem('articleDescription', description);

            // Upload cover image to local storage
            if (coverFile) {
                const uploadFormData = new FormData();
                uploadFormData.append('file', coverFile);

                console.log('Uploading cover to local storage...', {
                    fileName: coverFile.name,
                    fileSize: coverFile.size,
                    fileType: coverFile.type,
                });

                fetch('/local-upload/cover', {
                    method: 'POST',
                    body: uploadFormData,
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                    },
                })
                .then(res => {
                    console.log('Cover upload response status:', res.status);
                    if (!res.ok) {
                        // Server returned error (422, 500, etc)
                        return res.text().then(text => {
                            // Try to parse as JSON, fallback to text
                            try {
                                const json = JSON.parse(text);
                                throw new Error(json.message || 'Server error');
                            } catch {
                                throw new Error('Server error: ' + res.status + ' - ' + text.substring(0, 100));
                            }
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    console.log('Local upload response:', data);
                    if (data.success && data.path) {
                        console.log('Cover uploaded successfully:', data.path);
                        sessionStorage.setItem('articleCover', data.path);
                        sessionStorage.setItem('articleCoverPublicId', null);
                        window.location.href = '/writer/write';
                    } else {
                        console.error('Upload failed:', data);
                        alert('Upload failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(err => {
                    console.error('Cover upload error:', err);
                    alert('Upload error: ' + err.message);
                });
            } else {
                console.log('No cover file selected, proceeding without cover');
                window.location.href = '/writer/write';
            }
        }

        function getCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                throw new Error('CSRF token not found. Make sure meta[name="csrf-token"] is in your HTML head.');
            }
            return token;
        }
    </script>

</x-layouts.app>