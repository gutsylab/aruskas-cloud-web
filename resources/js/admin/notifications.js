/**
 * Admin Notifications System
 * Handles toast notifications, alerts, and admin messaging
 */

// Admin notifications functionality
window.AdminNotifications = {
    init() {
        this.bindEvents();
        this.initExistingNotifications();
    },

    bindEvents() {
        // Handle notification close buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.notification-close, .alert-close')) {
                this.closeNotification(e.target.closest('.notification, .alert'));
            }
        });

        // Auto-close notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            this.autoCloseNotifications();
        });
    },

    initExistingNotifications() {
        const notifications = document.querySelectorAll('.notification, .alert');
        notifications.forEach(notification => {
            if (!notification.querySelector('.notification-close, .alert-close')) {
                this.addCloseButton(notification);
            }
        });
    },

    addCloseButton(notification) {
        const closeBtn = document.createElement('button');
        closeBtn.className = 'notification-close absolute top-2 right-2 text-gray-400 hover:text-gray-600';
        closeBtn.innerHTML = '&times;';
        closeBtn.setAttribute('aria-label', 'Close notification');
        notification.style.position = 'relative';
        notification.appendChild(closeBtn);
    },

    closeNotification(notification) {
        if (notification) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    },

    autoCloseNotifications() {
        const autoCloseNotifications = document.querySelectorAll('.notification[data-auto-close="true"], .alert[data-auto-close="true"]');
        autoCloseNotifications.forEach(notification => {
            setTimeout(() => {
                this.closeNotification(notification);
            }, 5000);
        });
    },

    show(message, type = 'info', autoClose = true) {
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${this.getTypeClasses(type)}`;
        notification.setAttribute('data-auto-close', autoClose);
        
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-1">${message}</div>
                <button class="notification-close ml-4 text-current opacity-70 hover:opacity-100">&times;</button>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 100);

        if (autoClose) {
            setTimeout(() => {
                this.closeNotification(notification);
            }, 5000);
        }

        return notification;
    },

    getTypeClasses(type) {
        const classes = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-white',
            info: 'bg-blue-500 text-white'
        };
        return classes[type] || classes.info;
    },

    success(message, autoClose = true) {
        return this.show(message, 'success', autoClose);
    },

    error(message, autoClose = false) {
        return this.show(message, 'error', autoClose);
    },

    warning(message, autoClose = true) {
        return this.show(message, 'warning', autoClose);
    },

    info(message, autoClose = true) {
        return this.show(message, 'info', autoClose);
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.AdminNotifications.init();
    });
} else {
    window.AdminNotifications.init();
}
