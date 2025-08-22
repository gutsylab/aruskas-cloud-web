/**
 * Laravel Admin Panel JavaScript
 * This file imports all admin-specific JavaScript modules
 */

// Import jQuery and make it globally available
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import Bootstrap JavaScript
import 'bootstrap';

// Import SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Import shared utilities first
import './shared/utils.js';

// Import admin-specific modules in correct order
import './admin/sidebar.js';
import './admin/forms.js';
import './admin/modals.js';
import './admin/notifications.js';
import './admin/charts.js';
import './admin/filemanager.js';
import './admin/ajax-helpers.js';
import './admin/ajax-demo.js';

// Import UI component modules
import sweetAlert from './admin/sweet-alert.js';
import uiComponents from './admin/ui-components.js';

// Ensure modules are globally available
window.sweetAlert = sweetAlert;
window.uiComponents = uiComponents;

// Initialize AdminApp global object
window.AdminApp = {
    init() {
        
        // Initialize all admin modules
        if (window.AdminSidebar) window.AdminSidebar.init();
        if (window.AdminForms) window.AdminForms.init();
        if (window.AdminModals) window.AdminModals.init();
        if (window.AdminNotifications) window.AdminNotifications.init();
        if (window.AdminCharts) window.AdminCharts.init();
        if (window.AdminFileManager) window.AdminFileManager.init();
        
        this.bindGlobalEvents();
    },

    bindGlobalEvents() {
    }
};

// Initialize admin components
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit to ensure all modules are loaded
    setTimeout(() => {
        // Initialize admin-specific functionality
        if (window.AdminApp) {
            window.AdminApp.init();
        }
        
        // Ensure UI components are initialized
        if (window.uiComponents) {
            window.uiComponents.init();
        }
    }, 100);
});