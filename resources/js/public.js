/**
 * Laravel Public/Landing Page JavaScript
 * This file imports all public-facing JavaScript modules
 */

// Import jQuery and make it globally available
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import Bootstrap JavaScript
import 'bootstrap';

// Import shared utilities
import './shared/utils.js';

// Import public-specific modules
import './public/landing.js';
import './public/navbar.js';
import './public/animations.js';
import './public/forms.js';
import './public/carousel.js';

// Initialize PublicApp global object
window.PublicApp = {
    init() {
        
        // Initialize all public modules
        if (window.PublicLanding) window.PublicLanding.init();
        if (window.PublicNavbar) window.PublicNavbar.init();
        if (window.PublicAnimations) window.PublicAnimations.init();
        if (window.PublicForms) window.PublicForms.init();
        if (window.PublicCarousel) window.PublicCarousel.init();
        
        this.bindGlobalEvents();
    },

    bindGlobalEvents() {
        // Global public event handlers can go here
    }
};

// Initialize public components
document.addEventListener('DOMContentLoaded', function() {    
    
    // Initialize public-specific functionality
    if (window.PublicApp) {
        window.PublicApp.init();
    }
});
