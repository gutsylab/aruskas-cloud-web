/**
 * Admin Modal Management
 * Handles modal initialization and interactions for admin panel
 */

// Admin modal functionality
window.AdminModals = {
    init() {
        this.bindEvents();
    },

    bindEvents() {
        // Handle modal open/close
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-modal-toggle]')) {
                const modalId = e.target.dataset.modalToggle;
                this.toggleModal(modalId);
            }
        });

        // Handle modal backdrop clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('.modal-backdrop')) {
                this.closeModal(e.target.closest('.modal'));
            }
        });

        // Handle ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    },

    toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (modal.classList.contains('hidden')) {
                this.openModal(modal);
            } else {
                this.closeModal(modal);
            }
        }
    },

    openModal(modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('modal-open');
    },

    closeModal(modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('modal-open');
    },

    closeAllModals() {
        const modals = document.querySelectorAll('.modal:not(.hidden)');
        modals.forEach(modal => this.closeModal(modal));
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.AdminModals.init();
    });
} else {
    window.AdminModals.init();
}
