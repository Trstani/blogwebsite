@props(['unreadCount' => 0, 'notifications' => []])

<div class="relative group">
    {{-- Bell Icon Button --}}
    <button class="relative flex items-center justify-center p-2 text-gray-600 hover:text-black hover:bg-gray-100/80 rounded-full transition-all duration-200"
            title="Notifications">
        {{-- Bell Icon --}}
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        {{-- Unread Badge --}}
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-max h-5 px-1.5 bg-red-600 text-white text-xs font-bold rounded-full">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Menu --}}
    <div class="absolute right-0 mt-2 w-80 bg-white border border-gray-100 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
        
        {{-- Dropdown Header --}}
        <div class="border-b border-gray-100 px-4 py-3 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-as-read') }}" class="inline mark-all-form">
                    @csrf
                    <button type="submit" class="text-xs text-gray-500 hover:text-black transition-colors">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        {{-- Notifications List --}}
        <div class="max-h-96 overflow-y-auto">
            @if(count($notifications) > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($notifications as $notification)
                        <div class="px-4 py-3 hover:bg-gray-50 transition-colors {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }} notification-item" data-notification-id="{{ $notification->id }}">
                            
                            {{-- Notification Item --}}
                            <div class="flex items-start justify-between gap-3">
                                
                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    {{-- Type Badge --}}
                                    <span class="inline-block px-2 py-0.5 text-xs font-medium text-white rounded-full mb-1
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
                                            Pending
                                        @elseif(strpos($notification->type, 'ArticleApproved') !== false)
                                            Approved
                                        @elseif(strpos($notification->type, 'ArticleRejected') !== false)
                                            Rejected
                                        @elseif(strpos($notification->type, 'NewComment') !== false)
                                            Comment
                                        @elseif(strpos($notification->type, 'CommentReply') !== false)
                                            Reply
                                        @else
                                            Alert
                                        @endif
                                    </span>

                                    {{-- Message --}}
                                    <p class="text-xs text-gray-700 line-clamp-2 mb-1">
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

                                {{-- Unread Indicator --}}
                                @if(is_null($notification->read_at))
                                    <span class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-1"></span>
                                @endif

                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty State --}}
                <div class="px-4 py-8 text-center">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-xs text-gray-400">No notifications</p>
                </div>
            @endif
        </div>

        {{-- Footer with "View All" Link --}}
        <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
            <a href="{{ route('notifications.index') }}" class="block text-center text-xs font-medium text-gray-600 hover:text-black transition-colors">
                View all notifications →
            </a>
        </div>

    </div>

</div>

<script>
(function() {
    // Handle "Mark all as read" form submission in dropdown
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('mark-all-form')) {
            e.preventDefault();
            const form = e.target;
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
                    // Update all notifications in dropdown
                    const dropdown = form.closest('[class*="absolute"][class*="right-0"]') || form.closest('div').parentElement;
                    
                    // Mark all notifications as read visually
                    document.querySelectorAll('.notification-item').forEach(item => {
                        item.classList.remove('bg-blue-50');
                        const indicator = item.querySelector('[class*="w-2"][class*="h-2"][class*="bg-blue"]');
                        if (indicator) indicator.remove();
                    });
                    
                    // Hide the mark all button
                    form.style.display = 'none';
                }
            })
            .catch(error => console.error('Error marking notifications as read:', error));
        }
    });
})();
</script>
