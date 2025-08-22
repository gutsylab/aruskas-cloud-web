/**
 * AJAX Helper Functions for Laravel Admin Panel
 * Provides utility functions for making AJAX requests and handling notifications
 */

// Toast notification system
let toastContainer = null;

/**
 * Show notification toast
 * @param {string} message - The message to display
 * @param {string} type - Type of notification (success, error, warning, info)
 */
function showNotification(message, type = 'info') {
    // Create toast container if it doesn't exist
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '1055';
        document.body.appendChild(toastContainer);
    }

    // Type mapping for Bootstrap classes
    const typeMapping = {
        success: 'success',
        error: 'danger',
        warning: 'warning',
        info: 'info'
    };

    const bgClass = typeMapping[type] || 'info';
    const toastId = 'toast-' + Date.now();

    // Create toast HTML
    const toastHtml = `
        <div id="${toastId}" class="toast show" role="alert" aria-live="assertive" aria-atomic="true" style="animation: slideInRight 0.3s ease-out;">
            <div class="toast-header bg-${bgClass} text-white">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close btn-close-white" onclick="closeToast('${toastId}')" aria-label="Close">&times;</button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    // Add toast to container
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const toastElement = document.getElementById(toastId);
        if (toastElement) {
            closeToast(toastId);
        }
    }, 5000);
}

/**
 * Close specific toast
 * @param {string} toastId - ID of the toast to close
 */
function closeToast(toastId) {
    const toastElement = document.getElementById(toastId);
    if (toastElement) {
        toastElement.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            toastElement.remove();
        }, 300);
    }
}

/**
 * Toggle loading state for an element
 * @param {boolean} isLoading - Whether to show loading state
 * @param {string} selector - CSS selector of the element
 */
function toggleLoading(isLoading, selector) {
    const element = document.querySelector(selector);
    if (!element) return;
    
    if (isLoading) {
        element.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0 text-muted">Processing request...</p>
            </div>
        `;
    }
    // Note: Loading will be replaced by the actual result content
}

/**
 * Get CSRF token from meta tag
 * @returns {string} CSRF token
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

/**
 * Get base URL with protocol and domain
 * @returns {string} Base URL
 */
function getBaseUrl() {
    return window.location.origin;
}

// AJAX Helper Functions

/**
 * Make AJAX GET request
 * @param {string} url - Request URL
 * @returns {Promise} Response promise
 */
async function ajaxGet(url) {
    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        }
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => ({ message: 'Network error' }));
        throw new Error(errorData.message || `HTTP ${response.status}`);
    }

    return await response.json();
}

/**
 * Make AJAX POST request
 * @param {string} url - Request URL
 * @param {object} data - Data to send
 * @returns {Promise} Response promise
 */
async function ajaxPost(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(data)
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => ({ message: 'Network error' }));
        throw new Error(errorData.message || `HTTP ${response.status}`);
    }

    return await response.json();
}

/**
 * Make AJAX PUT request
 * @param {string} url - Request URL
 * @param {object} data - Data to send
 * @returns {Promise} Response promise
 */
async function ajaxPut(url, data) {
    const response = await fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(data)
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => ({ message: 'Network error' }));
        throw new Error(errorData.message || `HTTP ${response.status}`);
    }

    return await response.json();
}

/**
 * Make AJAX PATCH request
 * @param {string} url - Request URL
 * @param {object} data - Data to send
 * @returns {Promise} Response promise
 */
async function ajaxPatch(url, data) {
    const response = await fetch(url, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(data)
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => ({ message: 'Network error' }));
        throw new Error(errorData.message || `HTTP ${response.status}`);
    }

    return await response.json();
}

/**
 * Make AJAX DELETE request
 * @param {string} url - Request URL
 * @returns {Promise} Response promise
 */
async function ajaxDelete(url) {
    const response = await fetch(url, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        }
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => ({ message: 'Network error' }));
        throw new Error(errorData.message || `HTTP ${response.status}`);
    }

    return await response.json();
}

/**
 * Make AJAX form request (for file uploads)
 * @param {string} url - Request URL
 * @param {FormData} formData - Form data including files
 * @param {string} method - HTTP method (default: POST)
 * @returns {Promise} Response promise
 */
async function ajaxForm(url, formData, method = 'POST') {
    const response = await fetch(url, {
        method: method,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: formData
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => ({ message: 'Network error' }));
        throw new Error(errorData.message || `HTTP ${response.status}`);
    }

    return await response.json();
}

// Global scope assignments for backward compatibility
window.showNotification = showNotification;
window.closeToast = closeToast;
window.toggleLoading = toggleLoading;
window.ajaxGet = ajaxGet;
window.ajaxPost = ajaxPost;
window.ajaxPut = ajaxPut;
window.ajaxPatch = ajaxPatch;
window.ajaxDelete = ajaxDelete;
window.ajaxForm = ajaxForm;
window.getCsrfToken = getCsrfToken;
window.getBaseUrl = getBaseUrl;
