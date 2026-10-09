<script>
/**
 * Global Modal Helpers
 * Provides utility functions for showing/hiding modals across the application
 */

// Store for pending confirmation actions
let modalConfirmCallbacks = {};

/**
 * Show a modal by ID
 */
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
    }
}

/**
 * Close a modal by ID
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
    // Clear any pending callback for this modal
    delete modalConfirmCallbacks[modalId];
}

/**
 * Show an alert modal with message and type
 * @param {string} title - Modal title
 * @param {string} message - Alert message
 * @param {string} type - Type: info, success, error, warning
 */
function showAlert(title, message, type = 'info') {
    // Find or create an alert modal
    let modal = document.getElementById('defaultAlertModal');
    
    if (!modal) {
        // Create a dynamic alert modal if none exists
        const container = document.createElement('div');
        container.innerHTML = `
            <div id="defaultAlertModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                <div class="modal-backdrop absolute inset-0 bg-black/40" onclick="closeModal('defaultAlertModal')"></div>
                <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-5 sm:p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div id="alertIcon" class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 id="alertTitle" class="text-base font-semibold text-black sm:text-lg">Alert</h3>
                    </div>
                    <p id="alertMessage" class="text-sm text-gray-600 mb-6"></p>
                    <div class="flex justify-end">
                        <button onclick="closeModal('defaultAlertModal')" class="px-5 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors">OK</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(container);
        modal = document.getElementById('defaultAlertModal');
    }
    
    // Update modal content
    document.getElementById('alertTitle').textContent = title;
    document.getElementById('alertMessage').textContent = message;
    
    // Update icon and styling based on type
    const iconDiv = document.getElementById('alertIcon');
    const icons = {
        error: '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
        success: '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        warning: '<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
        info: '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    };
    
    const bgColors = {
        error: 'bg-red-50',
        success: 'bg-green-50',
        warning: 'bg-yellow-50',
        info: 'bg-blue-50'
    };
    
    iconDiv.className = `w-10 h-10 ${bgColors[type] || 'bg-blue-50'} rounded-full flex items-center justify-center shrink-0`;
    iconDiv.innerHTML = icons[type] || icons.info;
    
    showModal('defaultAlertModal');
}

/**
 * Show a confirmation modal
 * @param {string} title - Modal title
 * @param {string} message - Confirmation message
 * @param {function} onConfirm - Callback when user confirms
 * @param {string} confirmText - Confirm button text
 * @param {string} cancelText - Cancel button text
 * @param {boolean} isDangerous - Whether to show as dangerous action
 */
function showConfirm(title, message, onConfirm, confirmText = 'Confirm', cancelText = 'Cancel', isDangerous = false) {
    // Find or create a confirm modal
    let modal = document.getElementById('defaultConfirmModal');
    
    if (!modal) {
        const container = document.createElement('div');
        container.innerHTML = `
            <div id="defaultConfirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                <div class="modal-backdrop absolute inset-0 bg-black/40" onclick="closeModal('defaultConfirmModal')"></div>
                <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-5 sm:p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div id="confirmIcon" class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 id="confirmTitle" class="text-base font-semibold text-black sm:text-lg">Confirm</h3>
                    </div>
                    <p id="confirmMessage" class="text-sm text-gray-600 mb-6"></p>
                    <div class="flex flex-col gap-2 sm:flex-row sm:justify-end sm:gap-3">
                        <button type="button" onclick="closeModal('defaultConfirmModal')" class="w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors sm:w-auto" id="confirmCancelBtn">Cancel</button>
                        <button type="button" onclick="executeModalConfirm('defaultConfirmModal')" class="w-full px-4 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors sm:w-auto" id="confirmConfirmBtn">Confirm</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(container);
        modal = document.getElementById('defaultConfirmModal');
    }
    
    // Update modal content
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmConfirmBtn').textContent = confirmText;
    document.getElementById('confirmCancelBtn').textContent = cancelText;
    
    // Update icon for dangerous actions
    const iconDiv = document.getElementById('confirmIcon');
    if (isDangerous) {
        iconDiv.className = 'w-10 h-10 bg-red-50 rounded-full flex items-center justify-center shrink-0';
        iconDiv.innerHTML = '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
        document.getElementById('confirmConfirmBtn').className = 'w-full px-4 py-2.5 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors sm:w-auto';
    } else {
        iconDiv.className = 'w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center shrink-0';
        iconDiv.innerHTML = '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        document.getElementById('confirmConfirmBtn').className = 'w-full px-4 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors sm:w-auto';
    }
    
    // Store callback
    modalConfirmCallbacks['defaultConfirmModal'] = onConfirm;
    
    showModal('defaultConfirmModal');
}

/**
 * Execute pending confirmation callback
 */
function executeModalConfirm(modalId) {
    const callback = modalConfirmCallbacks[modalId];
    if (callback && typeof callback === 'function') {
        callback();
    }
    closeModal(modalId);
}

/**
 * Execute named confirmation action (for onclick handlers)
 */
function confirmAction(modalId) {
    executeModalConfirm(modalId);
}

/**
 * Polyfill for confirm - returns a Promise that resolves to true/false
 * Usage: if (await modalConfirm("Delete?")) { ... }
 */
function modalConfirm(message, title = 'Confirm', confirmText = 'Confirm', cancelText = 'Cancel', isDangerous = false) {
    return new Promise((resolve) => {
        showConfirm(title, message, () => resolve(true), confirmText, cancelText, isDangerous);
        // Also close on cancel/backdrop click
        const originalClose = window.closeModal;
        window.closeModal = function(modalId) {
            originalClose(modalId);
            if (modalId === 'defaultConfirmModal' && !modalConfirmCallbacks[modalId]) {
                resolve(false);
            }
        };
    });
}
</script>
