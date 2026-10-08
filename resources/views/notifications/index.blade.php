<x-layouts.app title="Notifications">

    <div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 sm:py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6 sm:mb-8">
            <div>
                <h1 class="text-2xl font-bold text-black sm:text-3xl">Notifications</h1>
                <p class="text-sm text-gray-500 mt-1">
                    @if($unreadCount > 0)
                        You have {{ $unreadCount }} unread notification{{ $unreadCount !== 1 ? 's' : '' }}
                    @else
                        All caught up!
                    @endif
                </p>
            </div>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-as-read') }}" class="mark-all-form">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-black rounded-md hover:bg-gray-800 transition-colors">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        {{-- Notifications List --}}
        @if($notifications->count() > 0)
            <div class="space-y-2">
                @foreach($notifications as $notification)
                    <div class="group bg-white border {{ is_null($notification->read_at) ? 'border-blue-200 bg-blue-50' : 'border-gray-100' }} rounded-lg p-4 hover:border-gray-200 transition-colors sm:p-5 notification-item" data-notification-id="{{ $notification->id }}">
                        
                        <div class="flex items-start justify-between gap-4">
                            
                            {{-- Notification Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    {{-- Notification Type Badge --}}
                                    <span class="inline-block px-2.5 py-0.5 text-xs font-medium text-white rounded-full
                                        @if(strpos($notification->type, 'ArticleSubmittedForReview') !== false)
                                            bg-yellow-600
                                        @elseif(strpos($notification->type, 'ArticleApproved') !== false)
                                            bg-green-600
                                        @elseif(strpos($notification->type, 'ArticleRejected') !== false)
                                            bg-red-600
                                        @elseif(strpos($notification->type, 'NewComment') !== false)
                                            bg-blue-600
                                        @elseif(strpos($notification->type, 'CommentReply') !== false)
                                            bg-purple-600
                                        @else
                                            bg-gray-600
                                        @endif
                                    ">
                                        @if(strpos($notification->type, 'ArticleSubmittedForReview') !== false)
                                            Pending Review
                                        @elseif(strpos($notification->type, 'ArticleApproved') !== false)
                                            Approved
                                        @elseif(strpos($notification->type, 'ArticleRejected') !== false)
                                            Rejected
                                        @elseif(strpos($notification->type, 'NewComment') !== false)
                                            New Comment
                                        @elseif(strpos($notification->type, 'CommentReply') !== false)
                                            New Reply
                                        @else
                                            Notification
                                        @endif
                                    </span>

                                    {{-- Unread Indicator --}}
                                    @if(is_null($notification->read_at))
                                        <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                    @endif
                                </div>

                                {{-- Notification Message --}}
                                <p class="text-sm text-gray-900 mb-1 line-clamp-2">
                                    @php
                                        $message = 'You have a new notification';
                                        $data = $notification->data;
                                        
                                        if (strpos($notification->type, 'ArticleSubmittedForReview') !== false) {
                                            $message = sprintf(
                                                '%s submitted "%s" for review',
                                                $data['author_name'] ?? 'A user',
                                                $data['article_title'] ?? 'Untitled'
                                            );
                                        } elseif (strpos($notification->type, 'ArticleApproved') !== false) {
                                            $message = sprintf(
                                                'Your article "%s" has been approved',
                                                $data['article_title'] ?? 'Untitled'
                                            );
                                        } elseif (strpos($notification->type, 'ArticleRejected') !== false) {
                                            $message = sprintf(
                                                'Your article "%s" has been rejected',
                                                $data['article_title'] ?? 'Untitled'
                                            );
                                        } elseif (strpos($notification->type, 'NewComment') !== false) {
                                            $message = sprintf(
                                                '%s commented on "%s"',
                                                $data['commenter_name'] ?? 'A user',
                                                $data['article_title'] ?? 'Untitled'
                                            );
                                        } elseif (strpos($notification->type, 'CommentReply') !== false) {
                                            $message = sprintf(
                                                '%s replied to your comment',
                                                $data['replier_name'] ?? 'A user'
                                            );
                                        }
                                    @endphp
                                    {{ $message }}
                                </p>

                                {{-- Timestamp --}}
                                <p class="text-xs text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Mark as Read Button --}}
                            @if(is_null($notification->read_at))
                                <form method="POST" action="{{ route('notifications.mark-as-read', $notification->id) }}" class="mark-as-read-form flex-shrink-0" data-notification-id="{{ $notification->id }}">
                                    @csrf
                                    <button type="submit" 
                                            class="text-xs font-medium text-gray-500 hover:text-black transition-colors px-3 py-1.5 rounded hover:bg-gray-100">
                                        Mark as read
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($notifications->hasPages())
                <div class="mt-8 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        Showing <span class="font-medium">{{ $notifications->firstItem() }}</span> to <span class="font-medium">{{ $notifications->lastItem() }}</span> of <span class="font-medium">{{ $notifications->total() }}</span> notifications
                    </p>
                    <div class="flex items-center gap-2">
                        @if($notifications->onFirstPage())
                            <span class="px-3 py-2 text-xs text-gray-300 border border-gray-200 rounded-lg">← Previous</span>
                        @else
                            <a href="{{ $notifications->previousPageUrl() }}" class="px-3 py-2 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors">← Previous</a>
                        @endif

                        @if($notifications->hasMorePages())
                            <a href="{{ $notifications->nextPageUrl() }}" class="px-3 py-2 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-black transition-colors">Next →</a>
                        @else
                            <span class="px-3 py-2 text-xs text-gray-300 border border-gray-200 rounded-lg">Next →</span>
                        @endif
                    </div>
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="bg-white border border-gray-100 rounded-lg p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No notifications yet</h3>
                <p class="text-sm text-gray-500">
                    When you receive notifications about articles, comments, and reviews, they'll appear here.
                </p>
            </div>
        @endif

    </div>

</x-layouts.app>

<script>
(function() {
    // Handle notification form submissions via Fetch API
    document.addEventListener('submit', function(e) {
        const form = e.target;
        
        // Handle "Mark all as read" form
        if (form.classList.contains('mark-all-form')) {
            e.preventDefault();
            const token = form.querySelector('input[name="_token"]').value;
            const url = form.getAttribute('action');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mark all notifications as read
                    document.querySelectorAll('.notification-item').forEach(item => {
                        item.classList.remove('bg-blue-50', 'border-blue-200');
                        item.classList.add('border-gray-100');
                        
                        // Remove unread indicator dot
                        const indicator = item.querySelector('[class*="w-2"][class*="h-2"][class*="bg-blue"]');
                        if (indicator) indicator.remove();
                        
                        // Hide mark as read button
                        const form = item.querySelector('.mark-as-read-form');
                        if (form) form.style.display = 'none';
                    });
                    
                    // Update unread count message
                    const countMsg = document.querySelector('p.text-sm.text-gray-500');
                    if (countMsg) {
                        countMsg.textContent = 'All caught up!';
                    }
                }
            })
            .catch(error => console.error('Error marking notifications as read:', error));
        }
        
        // Handle individual "Mark as read" forms
        if (form.classList.contains('mark-as-read-form')) {
            e.preventDefault();
            const token = form.querySelector('input[name="_token"]').value;
            const url = form.getAttribute('action');
            const notificationId = form.getAttribute('data-notification-id');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the specific notification item
                    const notifItem = document.querySelector(`.notification-item[data-notification-id="${notificationId}"]`);
                    if (notifItem) {
                        notifItem.classList.remove('bg-blue-50', 'border-blue-200');
                        notifItem.classList.add('border-gray-100');
                        
                        // Remove unread indicator
                        const indicator = notifItem.querySelector('[class*="w-2"][class*="h-2"][class*="bg-blue"]');
                        if (indicator) indicator.remove();
                        
                        // Hide the form
                        form.style.display = 'none';
                    }
                    
                    // Update unread count if needed
                    const unreadCount = document.querySelectorAll('.notification-item.bg-blue-50').length;
                    if (unreadCount === 0) {
                        const countMsg = document.querySelector('p.text-sm.text-gray-500');
                        if (countMsg) {
                            countMsg.textContent = 'All caught up!';
                        }
                        
                        // Hide mark all button
                        const markAllForm = document.querySelector('.mark-all-form');
                        if (markAllForm) markAllForm.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }
    });
})();
</script>
