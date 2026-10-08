<x-layouts.app title="Writer Dashboard">

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8">

        {{-- =========================================================
             HEADER
             ========================================================= --}}
        <div class="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-black sm:text-3xl">My Articles</h1>
                <p class="mt-1 text-sm text-gray-500">Manage and create your articles</p>
            </div>

            <button onclick="openModal()"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-black px-5 py-3 text-sm font-medium text-white transition-colors hover:bg-gray-800 sm:w-auto sm:py-2.5">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Article
            </button>
        </div>

        {{-- =========================================================
             STATS
             ========================================================= --}}
        <div class="mb-8 grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-6">
            <div class="rounded-lg border border-gray-100 bg-white p-4 sm:p-6">
                <p class="text-xs text-gray-500 sm:text-sm">Total Articles</p>
                <p class="mt-1 text-2xl font-bold text-black sm:text-3xl">{{ $totalArticles }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-white p-4 sm:p-6">
                <p class="text-xs text-gray-500 sm:text-sm">Published</p>
                <p class="mt-1 text-2xl font-bold text-green-600 sm:text-3xl">{{ $published }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-white p-4 sm:p-6">
                <p class="text-xs text-gray-500 sm:text-sm">Pending Review</p>
                <p class="mt-1 text-2xl font-bold text-yellow-600 sm:text-3xl">{{ $pending }}</p>
            </div>
        </div>

        {{-- =========================================================
             ARTICLES LIST
             ========================================================= --}}
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
                <p class="py-8 text-center text-sm text-gray-400">Belum ada artikel. Klik "New Article" untuk mulai menulis!</p>
            @endforelse
        </div>

    </div>

    {{-- =========================================================
         NEW ARTICLE MODAL
         ========================================================= --}}
    <div id="newArticleModal" class="hidden fixed inset-0 z-50 items-center justify-center">
        <div class="absolute inset-0 bg-black/40" onclick="closeModal()"></div>

        <div class="relative mx-4 w-full max-w-lg rounded-xl bg-white p-5 shadow-xl sm:p-8">

            <h2 class="text-lg font-bold text-black sm:text-xl">Create New Article</h2>
            <p class="mt-1 text-sm text-gray-500">Fill in the details before writing</p>

            <form id="articleForm" onsubmit="return submitArticle(event)" class="mt-5 sm:mt-6">

                {{-- Title --}}
                <div class="mb-4">
                    <label class="mb-1 block text-xs font-medium text-gray-700">Title *</label>
                    <input type="text"
                           name="title"
                           class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm transition-colors focus:border-black focus:outline-none"
                           placeholder="Enter article title..."
                           required />
                </div>

                {{-- Category --}}
                <div class="mb-4">
                    <label class="mb-1 block text-xs font-medium text-gray-700">Category *</label>
                    <select name="category"
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm transition-colors focus:border-black focus:outline-none"
                            required>
                        <option value="" disabled selected>Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Cover Image --}}
                <div class="mb-4">
                    <label class="mb-1 block text-xs font-medium text-gray-700">Cover Image</label>
                    <div id="coverPreview"
                         class="flex h-32 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-200 transition-colors hover:border-gray-400 sm:h-40"
                         onclick="document.getElementById('coverInput').click()">
                        <svg class="mb-1 h-7 w-7 text-gray-300 sm:h-8 sm:w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <p class="px-3 text-center text-xs text-gray-400">Click to upload cover image</p>
                    </div>
                    <input type="file" id="coverInput" accept="image/*" class="hidden" onchange="previewCover(this)" />
                </div>

                {{-- Description --}}
                <div class="mb-6">
                    <label class="mb-1 block text-xs font-medium text-gray-700">Short Description</label>
                    <textarea name="description"
                              rows="2"
                              class="w-full resize-none rounded-lg border border-gray-200 px-4 py-2.5 text-sm transition-colors focus:border-black focus:outline-none"
                              placeholder="Brief description..."></textarea>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end sm:gap-3">
                    <button type="button"
                            onclick="closeModal()"
                            class="w-full rounded-lg border border-gray-200 px-5 py-2.5 text-sm text-gray-500 transition-colors hover:border-black hover:text-black sm:w-auto">
                        Cancel
                    </button>
                    <button type="submit"
                            class="w-full rounded-lg bg-black px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-800 sm:w-auto">
                        Start Writing →
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- =========================================================
         DELETE CONFIRMATION MODAL
         ========================================================= --}}
    <div id="deleteConfirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" onclick="closeDeleteConfirm()"></div>

        <div class="relative mx-4 w-full max-w-sm rounded-xl bg-white p-5 shadow-xl sm:p-6">

            <div class="mb-3 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-50">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-black sm:text-lg">Delete Article?</h3>
            </div>

            <p id="deleteMessage" class="mb-6 text-sm text-gray-600">
                Are you sure you want to delete this article? This action cannot be undone.
            </p>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end sm:gap-3">
                <button onclick="closeDeleteConfirm()"
                        class="w-full rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 sm:w-auto">
                    Cancel
                </button>
                <button id="confirmDeleteBtn"
                        onclick="confirmDelete()"
                        class="w-full rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-red-600 sm:w-auto">
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
                        return res.text().then(text => {
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