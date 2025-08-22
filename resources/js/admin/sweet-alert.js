/**
 * SweetAlert2 Wrapper Functions
 * Simplified interface for SweetAlert2 functionality
 */

// SweetAlert2 wrapper object
const sweetAlert = {
    // Check if Swal is available before using
    checkSwal() {
        if (typeof window.Swal === 'undefined') {
            console.error('SweetAlert2 is not loaded. Please ensure Swal is imported in admin.js');
            return false;
        }
        return true;
    },

    // Basic success alert
    success(title, text = '') {
        if (!this.checkSwal()) return Promise.resolve();
        return window.Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            confirmButtonColor: '#28a745'
        });
    },

    // Basic error alert
    error(title, text = '') {
        if (!this.checkSwal()) return Promise.resolve();
        return window.Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            confirmButtonColor: '#dc3545'
        });
    },

    // Basic warning alert
    warning(title, text = '') {
        if (!this.checkSwal()) return Promise.resolve();
        return window.Swal.fire({
            icon: 'warning',
            title: title,
            text: text,
            confirmButtonColor: '#ffc107'
        });
    },

    // Basic info alert
    info(title, text = '') {
        if (!this.checkSwal()) return Promise.resolve();
        return window.Swal.fire({
            icon: 'info',
            title: title,
            text: text,
            confirmButtonColor: '#17a2b8'
        });
    },

    // Confirmation dialog
    confirm(title, text = '', confirmButtonText = 'Yes') {
        if (!this.checkSwal()) return Promise.resolve({ isConfirmed: false });
        return window.Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Cancel'
        });
    },

    // Delete confirmation dialog
    confirmDelete(title = 'Are you sure?', text = 'This action cannot be undone!') {
        if (!this.checkSwal()) return Promise.resolve({ isConfirmed: false });
        return window.Swal.fire({
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

    // Loading dialog
    loading(title = 'Loading...', text = 'Please wait...') {
        if (!this.checkSwal()) return Promise.resolve();
        return window.Swal.fire({
            title: title,
            text: text,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            didOpen: () => {
                window.Swal.showLoading();
            }
        });
    },

    // Close any open Swal dialog
    close() {
        if (!this.checkSwal()) return Promise.resolve();
        return window.Swal.close();
    },

    // Toast notification
    toast(title, icon = 'info', position = 'top-end') {
        if (!this.checkSwal()) return Promise.resolve();
        return window.Swal.fire({
            toast: true,
            position: position,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon: icon,
            title: title,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', window.Swal.stopTimer);
                toast.addEventListener('mouseleave', window.Swal.resumeTimer);
            }
        });
    },

    // Input dialog
    input(title, inputType = 'text', inputPlaceholder = '') {
        if (!this.checkSwal()) return Promise.resolve({ isConfirmed: false });
        return window.Swal.fire({
            title: title,
            input: inputType,
            inputPlaceholder: inputPlaceholder,
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to write something!';
                }
            }
        });
    },

    // Custom Swal with options
    fire(options) {
        if (!this.checkSwal()) return Promise.resolve();
        return window.Swal.fire(options);
    }
};

// Make sweetAlert globally available
window.sweetAlert = sweetAlert;

// Also provide as SweetAlert for consistency
window.SweetAlert = sweetAlert;

export default sweetAlert;
