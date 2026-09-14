<x-layouts.app title="{{ $article->title }}">
    <style>
        /* ========================================
        Rich Article Content
        ======================================== */

        .rich-article-content {
            color: #1f2937;
            line-height: 1.5;
        }

        /* Paragraph */
        .rich-article-content p {
            margin-top: 0;
        }

        /* Headings */
        .rich-article-content h2 {
            font-size: 1.75rem;
            line-height: 1.3;
            font-weight: 700;
            color: #111827;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .rich-article-content h3 {
            font-size: 1.375rem;
            line-height: 1.4;
            font-weight: 700;
            color: #111827;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        /* Bold */
        .rich-article-content strong,
        .rich-article-content b {
            font-weight: 700;
            color: #111827;
        }

        /* Italic */
        .rich-article-content em,
        .rich-article-content i {
            font-style: italic;
        }

        /* Underline */
        .rich-article-content u {
            text-decoration: underline;
            text-decoration-thickness: 1px;
            text-underline-offset: 2px;
        }

        /* Strike */
        .rich-article-content s,
        .rich-article-content strike {
            text-decoration: line-through;
        }

        /* Links */
        .rich-article-content a {
            color: #2563eb;
            text-decoration: underline;
            text-decoration-thickness: 1px;
            text-underline-offset: 2px;
            transition: color 0.2s ease;
        }

        .rich-article-content a:hover {
            color: #1d4ed8;
        }

        /* ========================================
            Lists - Quill 2
        ======================================== */

            .rich-article-content ol,
            .rich-article-content ul {
                padding-left: 1.75rem;
                margin-top: 1rem;
                margin-bottom: 1rem;
            }

            /* Quill bullet list */
            .rich-article-content ol > li[data-list="bullet"] {
                list-style-type: disc !important;
            }

            /* Quill ordered list */
            .rich-article-content ol > li[data-list="ordered"] {
                list-style-type: decimal !important;
            }

            /* Normal unordered list */
            .rich-article-content ul {
                list-style-type: disc !important;
            }

            /* Normal ordered list */
            .rich-article-content ol {
                list-style-type: decimal;
            }

            /* List item */
            .rich-article-content li {
                padding-left: 0.25rem;
                margin-bottom: 0.35rem;
            }

            /* Hide Quill's editor-only UI element on Reading Page */
            .rich-article-content .ql-ui {
                display: none;
            }

        /* Blockquote */
        .rich-article-content blockquote {
            margin: 1.5rem 0;
            padding: 0.75rem 1.25rem;
            border-left: 4px solid #9ca3af;
            color: #4b5563;
            font-style: italic;
            background-color: #f9fafb;
            border-radius: 0 0.375rem 0.375rem 0;
        }

        /* Clean Quill code output if it exists */
        .rich-article-content .ql-syntax {
            display: block;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
            background: #111827;
            color: #f9fafb;
            border-radius: 0.5rem;
            font-family: monospace;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        /* Prevent long links/words from breaking the layout */
        .rich-article-content a {
            overflow-wrap: anywhere;
        }
    </style>

    {{-- Article Header --}}
    <article class="max-w-5xl mx-auto px-6 pt-16 pb-8">

        {{-- Category --}}
        @if($article->category)
            <a href="/explore?category={{ $article->category->slug }}"
               class="inline-block px-3 py-1 rounded-md text-xs font-medium bg-gray-300 text-gray-700 mb-3 hover:bg-gray-200 transition-colors">
                {{ $article->category->name }}
            </a>
        @endif

        {{-- Title --}}
        <h1 class="text-3xl md:text-4xl font-bold text-black leading-tight">
            {{ $article->title }}
        </h1>

        {{-- Description --}}
        @if($article->description)
            <p class="mt-4 text-lg text-gray-500 leading-relaxed">
                {{ $article->description }}
            </p>
        @endif

        {{-- Meta --}}
        <div class="my-6 flex items-center gap-4 text-sm text-gray-400">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                    @if($article->author->avatar ?? false)
                    <img src="{{ imageUrl($article->author->avatar) }}"
                        alt="{{ $article->author->name }}"
                        class="w-8 h-8 rounded-full object-cover" />
                @else
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <span class="text-xl font-bold text-gray-400">
                            {{ substr($article->author->name ?? 'A', 0, 1) }}
                        </span>
                    </div>
                @endif
                </div>
                 <a href="/profile/{{ $article->author->slug ?? '#' }}"
                    class="text-base font-semibold text-black hover:text-gray-600 transition-colors">
                    {{ $article->author->name ?? 'Unknown' }}
            </a>
            </div>
            <span>·</span>
            <span>{{ fmtDate($article->published_at) ?: $article->created_at->format('M d, Y') }}</span>
            <span>·</span>
            <span>{{ $article->views }} views</span>
        </div>

        {{-- Cover Image --}}
        @if($article->cover_image)
            <div class="mb-8">
                <div class="relative w-full aspect-[3/1] overflow-hidden rounded-lg">
                    <img src="{{ imageUrl($article->cover_image) }}"
                        alt="{{ $article->title }}"
                        class="absolute inset-0 w-full h-full object-cover object-center" />
                </div>
            </div>
        @endif

    </article>

    {{-- Article Content (Sections) --}}
    <div class="max-w-5xl mx-auto px-6 py-8">
        @forelse($article->sections->sortBy('order') as $section)
           @if($section->type === 'text')
                <div class="rich-article-content prose prose-lg max-w-none mb-8 text-gray-800 leading-relaxed">
                    {!! $section->content !!}
                </div>
            @elseif($section->type === 'image')
                <div class="mb-8">
                    <div class="relative w-full aspect-[2/1] overflow-hidden rounded-lg">
                        <img src="{{ imageUrl($section->content) }}"
                            class="absolute inset-0 w-full h-full object-cover object-[center_center]"
                            alt="Gambar Artikel" />
                    </div>
                </div>
            @elseif($section->type === 'video')
                @php
                    $videoHelper = app(\App\Services\VideoUrlHelper::class);
                    $embedHtml = $videoHelper->generateEmbedHtml($section->content);
                @endphp
                @if($embedHtml)
                    <div class="mb-8">
                        <div class="rounded-lg overflow-hidden bg-black">
                            {!! $embedHtml !!}
                        </div>
                    </div>
                @else
                    <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-600">Invalid or unsupported video URL.</p>
                    </div>
                @endif
            @elseif($section->type === 'gif')
                <div class="mb-8">
                    <div class="relative w-full overflow-hidden rounded-lg">
                        <img src="{{ imageUrl($section->content) }}"
                            class="w-full h-auto object-contain"
                            alt="GIF Animation" />
                    </div>
                </div>
            @endif
                    @empty
            <p class="text-gray-400 text-center py-8">No content yet.</p>
        @endforelse
    </div>

    {{-- Back Link --}}
    <div class="max-w-5xl mx-auto px-6 pb-16">
        <a href="/explore" class="inline-flex items-center text-sm text-gray-400 hover:text-black transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Explore
        </a>
    </div>

    {{-- Comments Section (Published Articles Only) --}}
    @if($article->status === 'published')
        <div class="max-w-5xl mx-auto px-6 py-12 border-t border-gray-100">
            <h2 class="text-2xl font-bold text-black mb-8">Comments ({{ $article->comments->count() }})</h2>

            {{-- Comment Form --}}
            @if(auth()->check())
                <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Leave a Comment</h3>
                    <form id="commentForm" class="space-y-4">
                        @csrf
                        <textarea
                            id="commentContent"
                            name="content"
                            rows="4"
                            placeholder="Share your thoughts..."
                            maxlength="1000"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black transition-colors resize-none"
                            required></textarea>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400"><span id="charCount">0</span>/1000</span>
                            <button
                                type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-black rounded-md hover:bg-gray-800 transition-colors">
                                Post Comment
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="mb-8 p-6 bg-gray-50 rounded-lg text-center">
                    <p class="text-sm text-gray-600 mb-4">
                        <a href="{{ route('auth') }}" class="text-black font-semibold hover:text-gray-600 transition-colors">Log in</a>
                        to leave a comment.
                    </p>
                </div>
            @endif

            {{-- Comments List --}}
            <div class="space-y-6" id="commentsList">
                @forelse($article->comments->where('parent_id', null)->sortByDesc('created_at') as $comment)
                    <div class="pb-6 border-b border-gray-100 last:border-0">
                        <div class="flex items-start gap-4">
                            {{-- User Avatar --}}
                            <div class="flex-shrink-0">
                                @if($comment->user->avatar ?? false)
                                    <img src="{{ imageUrl($comment->user->avatar) }}"
                                        alt="{{ $comment->user->name }}"
                                        class="w-10 h-10 rounded-full object-cover" />
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-400">
                                            {{ substr($comment->user->name ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Comment Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <a href="/profile/{{ $comment->user->slug ?? '#' }}"
                                        class="text-sm font-semibold text-black hover:text-gray-600 transition-colors">
                                        {{ $comment->user->name }}
                                    </a>
                                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-700 break-words mb-3">
                                    {{ $comment->content }}
                                </p>
                                
                                {{-- Reply Button (for authenticated users) --}}
                                @if(auth()->check())
                                    <button onclick="toggleReplyForm({{ $comment->id }})" class="text-xs font-semibold text-gray-600 hover:text-black transition-colors">
                                        Reply
                                    </button>
                                @endif

                                {{-- Reply Form (hidden by default) --}}
                                @if(auth()->check())
                                    <div id="replyForm_{{ $comment->id }}" class="hidden mt-3 pt-3 border-t border-gray-100">
                                        <form class="replyForm" data-comment-id="{{ $comment->id }}">
                                            <textarea
                                                class="replyContent w-full px-3 py-2 border border-gray-200 rounded text-sm focus:outline-none focus:border-black transition-colors resize-none"
                                                rows="3"
                                                placeholder="Reply to {{ $comment->user->name }}..."
                                                maxlength="1000"
                                                required></textarea>
                                            <div class="flex items-center justify-between mt-2">
                                                <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="text-xs text-gray-600 hover:text-black">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-black rounded hover:bg-gray-800 transition-colors">
                                                    Reply
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                {{-- Replies (if any) --}}
                                @if($comment->replies && $comment->replies->count() > 0)
                                    <div class="mt-4 ml-6 space-y-4 border-l border-gray-400 pl-4">
                                        @foreach($comment->replies->sortBy('created_at') as $reply)
                                            <div class="flex items-start gap-3">
                                                {{-- Reply Avatar --}}
                                                <div class="flex-shrink-0">
                                                    @if($reply->user->avatar ?? false)
                                                        <img src="{{ imageUrl($reply->user->avatar) }}"
                                                            alt="{{ $reply->user->name }}"
                                                            class="w-8 h-8 rounded-full object-cover" />
                                                    @else
                                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <span class="text-xs font-bold text-gray-400">
                                                                {{ substr($reply->user->name ?? 'U', 0, 1) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Reply Content --}}
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <a href="/profile/{{ $reply->user->slug ?? '#' }}"
                                                            class="text-sm font-semibold text-black hover:text-gray-600 transition-colors">
                                                            {{ $reply->user->name }}
                                                        </a>
                                                        <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-sm text-gray-700 break-words">
                                                        {{ $reply->content }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-8 text-gray-400">No comments yet. Be the first to share your thoughts!</p>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Author Section --}}
    <div class="max-w-5xl mx-auto px-6 py-8 border-t border-gray-100">
        <div class="flex flex-col items-center text-center">
            {{-- Avatar --}}
            <a href="/profile/{{ $article->author->slug ?? '#' }}">
                @if($article->author->avatar ?? false)
                    <img src="{{ imageUrl($article->author->avatar) }}"
                        alt="{{ $article->author->name }}"
                        class="w-16 h-16 rounded-full object-cover" />
                @else
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <span class="text-xl font-bold text-gray-400">
                            {{ substr($article->author->name ?? 'A', 0, 1) }}
                        </span>
                    </div>
                @endif
            </a>

            {{-- Name --}}
            <a href="/profile/{{ $article->author->slug ?? '#' }}"
            class="mt-3 text-base font-semibold text-black hover:text-gray-600 transition-colors">
                {{ $article->author->name ?? 'Unknown' }}
            </a>

            {{-- Bio --}}
            @if($article->author->bio ?? false)
                <p class="mt-1 text-sm text-gray-500 max-w-md">
                    {{ $article->author->bio }}
                </p>
            @endif
        </div>
    </div>

    {{-- Comment Form Script --}}
    @if(auth()->check() && $article->status === 'published')
        <script>
            document.getElementById('commentForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const content = document.getElementById('commentContent').value.trim();

                if (!content) {
                    alert('Please enter a comment.');
                    return;
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Posting...';

                try {
                    const response = await fetch('/articles/{{ $article->id }}/comments', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ content }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Clear form
                        document.getElementById('commentContent').value = '';
                        document.getElementById('charCount').textContent = '0';

                        // Add new comment to list
                        const commentsList = document.getElementById('commentsList');
                        const emptyMessage = commentsList.querySelector('p');
                        if (emptyMessage) emptyMessage.remove();

                        const newCommentHTML = `
                            <div class="pb-6 border-b border-gray-100">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        ${data.comment.user.avatar
                                            ? `<img src="${data.comment.user.avatar}" alt="${data.comment.user.name}" class="w-10 h-10 rounded-full object-cover" />`
                                            : `<div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-bold text-gray-400">${data.comment.user.name.charAt(0)}</span>
                                            </div>`
                                        }
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-semibold text-black">${data.comment.user.name}</span>
                                            <span class="text-xs text-gray-400">just now</span>
                                        </div>
                                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap break-words">${data.comment.content}</p>
                                    </div>
                                </div>
                            </div>
                        `;

                        commentsList.insertAdjacentHTML('afterbegin', newCommentHTML);

                        // Update comment count
                        const heading = document.querySelector('h2');
                        if (heading) {
                            const count = parseInt(heading.textContent.match(/\d+/)[0]) + 1;
                            heading.textContent = `Comments (${count})`;
                        }
                    } else {
                        alert(data.message || 'Failed to post comment.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while posting your comment.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });

            // Character counter
            document.getElementById('commentContent')?.addEventListener('input', function() {
                document.getElementById('charCount').textContent = this.value.length;
            });

            // Toggle reply form visibility
            function toggleReplyForm(commentId) {
                const form = document.getElementById(`replyForm_${commentId}`);
                if (form) {
                    form.classList.toggle('hidden');
                    if (!form.classList.contains('hidden')) {
                        form.querySelector('textarea').focus();
                    }
                }
            }

            // Handle reply form submissions
            document.querySelectorAll('.replyForm').forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const content = this.querySelector('.replyContent').value.trim();
                    const commentId = this.dataset.commentId;

                    if (!content) {
                        alert('Please enter a reply.');
                        return;
                    }

                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.textContent;
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Posting...';

                    try {
                        const response = await fetch('/articles/{{ $article->id }}/comments', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ 
                                content: content,
                                parent_id: parseInt(commentId)
                            }),
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Clear form and hide it
                            this.reset();
                            toggleReplyForm(commentId);

                            // Reload the page to show new reply (simple approach)
                            // In a more complex app, we'd dynamically insert the reply
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to post reply.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while posting your reply.');
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                });
            });
        </script>
    @endif

</x-layouts.app>