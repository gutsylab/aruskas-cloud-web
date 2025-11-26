/**
 * Admin Sidebar JavaScript
 * Handles sidebar functionality and navigation
 */

window.AdminSidebar = {
    isCollapsed: false,
    
    init: function() {
        this.bindEvents();
        this.loadState();
        this.setupHoverExpansion();
    },

    bindEvents: function() {
        // Toggle sidebar
        const toggleBtn = document.querySelector('.sidebar-toggle-btn');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggle());
        }

        // Handle submenu toggles
        const submenuToggles = document.querySelectorAll('.sidebar .nav-link[data-bs-toggle="collapse"]');
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => this.handleSubmenuToggle(e));
        });

        // Handle window resize
        window.addEventListener('resize', Utils.throttle(() => this.handleResize(), 250));
        
        // Handle mobile backdrop click
        document.addEventListener('click', (e) => this.handleBackdropClick(e));
    },

    toggle: function() {
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content-with-sidebar');
        const isMobile = window.innerWidth < 768;
        
        if (sidebar && content) {
            if (isMobile) {
                // Mobile: Toggle show/hide
                sidebar.classList.toggle('show');
            } else {
                // Desktop: Toggle collapsed state
                this.isCollapsed = !this.isCollapsed;
                
                if (this.isCollapsed) {
                    sidebar.classList.add('collapsed');
                    content.classList.add('sidebar-collapsed');
                } else {
                    sidebar.classList.remove('collapsed');
                    content.classList.remove('sidebar-collapsed');
                }
                
                this.saveState();
            }
        }
    },

    handleSubmenuToggle: function(e) {
        if (this.isCollapsed) {
            e.preventDefault();
            return;
        }
        
        const target = e.currentTarget;
        const submenu = document.querySelector(target.getAttribute('data-bs-target'));
        
        if (submenu) {
            // Close other open submenus
            const otherSubmenus = document.querySelectorAll('.sidebar .collapse.show');
            otherSubmenus.forEach(menu => {
                if (menu !== submenu) {
                    const collapse = new bootstrap.Collapse(menu, { toggle: false });
                    collapse.hide();
                }
            });
        }
    },

    handleBackdropClick: function(e) {
        const isMobile = window.innerWidth < 768;
        if (!isMobile) return;
        
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('.sidebar-toggle-btn');
        
        // Don't close if clicking on sidebar or toggle button
        if (!sidebar || !sidebar.classList.contains('show')) return;
        if (sidebar.contains(e.target) || toggleBtn?.contains(e.target)) return;
        
        // Close sidebar on mobile when clicking outside
        sidebar.classList.remove('show');
    },

    handleResize: function() {
        const width = window.innerWidth;
        const sidebar = document.querySelector('.sidebar');
        
        if (width < 768) {
            // Mobile: Hide sidebar by default
            if (sidebar) {
                sidebar.classList.remove('show');
            }
        } else {
            // Desktop: Show sidebar
            if (sidebar) {
                sidebar.classList.add('show');
            }
        }
    },

    setupHoverExpansion: function() {
        const sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        
        let hoverTimeout = null;
        let isHoverExpanded = false;
        
        // Add hover listeners to sidebar
        sidebar.addEventListener('mouseenter', () => {
            // Only expand on hover if sidebar is collapsed and not on mobile
            if (this.isCollapsed && window.innerWidth >= 768) {
                clearTimeout(hoverTimeout);
                this.expandOnHover();
                isHoverExpanded = true;
            }
        });
        
        sidebar.addEventListener('mouseleave', () => {
            // Only collapse if it was expanded by hover
            if (isHoverExpanded && window.innerWidth >= 768) {
                hoverTimeout = setTimeout(() => {
                    this.collapseFromHover();
                    isHoverExpanded = false;
                }, 300); // Small delay to prevent flickering
            }
        });
        
        // Reset hover state when manually toggling
        const originalToggle = this.toggle.bind(this);
        this.toggle = function() {
            isHoverExpanded = false;
            clearTimeout(hoverTimeout);
            originalToggle();
        };
    },
    
    expandOnHover: function() {
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content-with-sidebar');
        
        if (sidebar && content) {
            sidebar.classList.add('hover-expanded');
            sidebar.classList.remove('collapsed');
            content.classList.remove('sidebar-collapsed');
        }
    },
    
    collapseFromHover: function() {
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content-with-sidebar');
        
        if (sidebar && content && this.isCollapsed) {
            sidebar.classList.remove('hover-expanded');
            sidebar.classList.add('collapsed');
            content.classList.add('sidebar-collapsed');
        }
    },

    saveState: function() {
        Utils.storage.set('sidebar-collapsed', this.isCollapsed);
    },

    loadState: function() {
        const saved = Utils.storage.get('sidebar-collapsed', false);
        if (saved !== this.isCollapsed) {
            this.toggle();
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.sidebar')) {
        window.AdminSidebar.init();
    }
});

// Global function for backward compatibility
window.toggleSidebar = function() {
    if (window.AdminSidebar) {
        window.AdminSidebar.toggle();
    }
};

export default window.AdminSidebar;
