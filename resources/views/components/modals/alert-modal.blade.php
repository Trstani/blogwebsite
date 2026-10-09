@props([
    'id' => 'alertModal',
    'title' => 'Alert',
    'message' => '',
    'type' => 'info', // info, success, error, warning
    'buttonText' => 'OK'
])

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="modal-backdrop absolute inset-0 bg-black/40" onclick="closeModal('{{ $id }}')"></div>
    
    <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-5 sm:p-6">
        <div class="flex items-center gap-3 mb-3">
            @if($type === 'error')
                <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            @elseif($type === 'success')
                <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            @elseif($type === 'warning')
                <div class="w-10 h-10 bg-yellow-50 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            @else
                <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            @endif
            
            <h3 class="text-base font-semibold text-black sm:text-lg">{{ $title }}</h3>
        </div>
        
        @if($message)
            <p class="text-sm text-gray-600 mb-6">{{ $message }}</p>
        @else
            <div class="text-sm text-gray-600 mb-6">{{ $slot }}</div>
        @endif
        
        <div class="flex justify-end">
            <button
                onclick="closeModal('{{ $id }}')"
                class="px-5 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors">
                {{ $buttonText }}
            </button>
        </div>
    </div>
</div>
