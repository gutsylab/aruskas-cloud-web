import './bootstrap';

// Import Bootstrap JavaScript
import 'bootstrap';

// Import jQuery first and make it globally available
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// CSRF Token untuk Laravel
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Default headers untuk semua request AJAX
const defaultHeaders = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
};

/**
 * AJAX GET Request
 * @param {string} url - URL endpoint
 * @param {Object} options - Additional options (headers, etc.)
 * @returns {Promise}
 */
window.ajaxGet = async function(url, options = {}) {
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                ...defaultHeaders,
                ...options.headers
            },
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        }
        return await response.text();
    } catch (error) {
        console.error('AJAX GET Error:', error);
        throw error;
    }
};

/**
 * AJAX POST Request
 * @param {string} url - URL endpoint
 * @param {Object} data - Data to send
 * @param {Object} options - Additional options (headers, etc.)
 * @returns {Promise}
 */
window.ajaxPost = async function(url, data = {}, options = {}) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                ...defaultHeaders,
                ...options.headers
            },
            body: JSON.stringify(data),
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        }
        return await response.text();
    } catch (error) {
        console.error('AJAX POST Error:', error);
        throw error;
    }
};

/**
 * AJAX PUT Request
 * @param {string} url - URL endpoint
 * @param {Object} data - Data to send
 * @param {Object} options - Additional options (headers, etc.)
 * @returns {Promise}
 */
window.ajaxPut = async function(url, data = {}, options = {}) {
    try {
        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                ...defaultHeaders,
                ...options.headers
            },
            body: JSON.stringify(data),
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        }
        return await response.text();
    } catch (error) {
        console.error('AJAX PUT Error:', error);
        throw error;
    }
};

/**
 * AJAX PATCH Request
 * @param {string} url - URL endpoint
 * @param {Object} data - Data to send
 * @param {Object} options - Additional options (headers, etc.)
 * @returns {Promise}
 */
window.ajaxPatch = async function(url, data = {}, options = {}) {
    try {
        const response = await fetch(url, {
            method: 'PATCH',
            headers: {
                ...defaultHeaders,
                ...options.headers
            },
            body: JSON.stringify(data),
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        }
        return await response.text();
    } catch (error) {
        console.error('AJAX PATCH Error:', error);
        throw error;
    }
};

/**
 * AJAX DELETE Request
 * @param {string} url - URL endpoint
 * @param {Object} options - Additional options (headers, etc.)
 * @returns {Promise}
 */
window.ajaxDelete = async function(url, options = {}) {
    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                ...defaultHeaders,
                ...options.headers
            },
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        }
        return await response.text();
    } catch (error) {
        console.error('AJAX DELETE Error:', error);
        throw error;
    }
};

/**
 * AJAX Form Submit (handles FormData for file uploads)
 * @param {string} url - URL endpoint
 * @param {FormData|Object} formData - Form data or object
 * @param {string} method - HTTP method (POST, PUT, PATCH)
 * @param {Object} options - Additional options
 * @returns {Promise}
 */
window.ajaxForm = async function(url, formData, method = 'POST', options = {}) {
    try {
        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }),
            ...options.headers
        };

        // Don't set Content-Type for FormData, let browser set it with boundary
        if (!(formData instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
            formData = JSON.stringify(formData);
        }

        const response = await fetch(url, {
            method: method.toUpperCase(),
            headers: headers,
            body: formData,
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        }
        return await response.text();
    } catch (error) {
        console.error('AJAX Form Error:', error);
        throw error;
    }
};

/**
 * Helper function untuk menampilkan loading state
 * @param {boolean} show - Show or hide loading
 * @param {string} target - Target element selector (optional)
 */
window.toggleLoading = function(show = true, target = null) {
    if (target) {
        const element = document.querySelector(target);
        if (element) {
            if (show) {
                element.classList.add('loading');
                element.style.opacity = '0.6';
                element.style.pointerEvents = 'none';
            } else {
                element.classList.remove('loading');
                element.style.opacity = '';
                element.style.pointerEvents = '';
            }
        }
    } else {
        // Global loading overlay
        let overlay = document.getElementById('global-loading');
        if (!overlay && show) {
            overlay = document.createElement('div');
            overlay.id = 'global-loading';
            overlay.innerHTML = `
                <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                     style="background: rgba(0,0,0,0.5); z-index: 9999;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);
        } else if (overlay && !show) {
            overlay.remove();
        }
    }
};

/**
 * Helper function untuk menampilkan notifikasi
 * @param {string} message - Message to show
 * @param {string} type - Type of notification (success, error, warning, info)
 * @param {number} duration - Duration in milliseconds (default: 3000)
 */
window.showNotification = function(message, type = 'info', duration = 3000) {
    // Remove existing notifications
    const existingToasts = document.querySelectorAll('.ajax-toast');
    existingToasts.forEach(toast => toast.remove());

    // Map type to Bootstrap classes
    const typeMapping = {
        success: 'success',
        error: 'danger',
        warning: 'warning',
        info: 'info'
    };
    
    const bgClass = typeMapping[type] || 'info';

    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div class="toast ajax-toast show position-fixed top-0 end-0 m-3" 
             id="${toastId}" 
             role="alert" 
             style="z-index: 10000;">
            <div class="toast-header bg-${bgClass} text-white">
                <i class="fas fa-${getNotificationIcon(type)} me-2"></i>
                <strong class="me-auto">${getNotificationTitle(type)}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', toastHtml);

    // Auto remove after duration
    setTimeout(() => {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.remove();
        }
    }, duration);
};

// Helper functions for notifications
function getNotificationIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function getNotificationTitle(type) {
    const titles = {
        success: 'Success',
        error: 'Error',
        warning: 'Warning',
        info: 'Information'
    };
    return titles[type] || 'Notification';
}

/**
 * ==========================================
 * UTILITY FUNCTIONS FOR EXTERNAL LIBRARIES
 * ==========================================
 */

/**
 * SweetAlert2 Utility Functions
 */
window.sweetAlert = {
    // Success alert
    success: function(title, text = '') {
        return Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            confirmButtonColor: '#28a745',
            timer: 3000,
            timerProgressBar: true
        });
    },

    // Error alert
    error: function(title, text = '') {
        return Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            confirmButtonColor: '#dc3545'
        });
    },

    // Warning alert
    warning: function(title, text = '') {
        return Swal.fire({
            icon: 'warning',
            title: title,
            text: text,
            confirmButtonColor: '#ffc107'
        });
    },

    // Info alert
    info: function(title, text = '') {
        return Swal.fire({
            icon: 'info',
            title: title,
            text: text,
            confirmButtonColor: '#17a2b8'
        });
    },

    // Confirmation dialog
    confirm: function(title, text = '', confirmButtonText = 'Yes', cancelButtonText = 'Cancel') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText
        });
    },

    // Delete confirmation
    confirmDelete: function(title = 'Are you sure?', text = 'You won\'t be able to revert this!') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        });
    },

    // Loading alert
    loading: function(title = 'Loading...') {
        return Swal.fire({
            title: title,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },

    // Close loading
    close: function() {
        Swal.close();
    }
};