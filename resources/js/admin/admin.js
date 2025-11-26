/**
 * Admin Layout JavaScript
 * Handles sidebar functionality, floating dropdowns, and responsive behavior
 */

// Global variables for sidebar functionality
let hoverTimeout;
let leaveTimeout;
let sidebar, content, body;

/**
 * Toggle sidebar collapsed/expanded state
 */
function toggleSidebar() {
    if (!sidebar) return; // Safety check
    
    if (window.innerWidth > 768) {
        // Clear any hover timeouts
        clearTimeout(hoverTimeout);
        clearTimeout(leaveTimeout);
        
        // Toggle collapsed class
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('sidebar-collapsed');
        body.classList.toggle('sidebar-collapsed');
        
        // Track manual expansion state
        if (sidebar.classList.contains('collapsed')) {
            sidebar.removeAttribute('data-manually-expanded');
            sidebar.removeAttribute('data-hover-expanded');
        } else {
            sidebar.setAttribute('data-manually-expanded', 'true');
            sidebar.removeAttribute('data-hover-expanded');
        }
    } else {
        // Mobile: Show/hide sidebar
        sidebar.classList.toggle('show');
    }
}

/**
 * Create floating dropdown for collapsed sidebar with hover
 * @param {Element} clickedElement - The element that triggered the dropdown
 * @param {string} targetId - The ID of the target collapse element
 * @param {number} level - The nesting level of the dropdown
 */
function createFloatingDropdown(clickedElement, targetId, level = 0) {
    // Remove any existing floating dropdown at the same level or higher
    const existingFloating = document.querySelectorAll('.floating-dropdown');
    existingFloating.forEach(dropdown => {
        const dropdownLevel = parseInt(dropdown.getAttribute('data-level') || '0');
        if (dropdownLevel >= level) {
            dropdown.remove();
        }
    });

    // Get the target collapse element
    const targetElement = document.querySelector(targetId);
    if (!targetElement) return;

    // Create floating dropdown container
    const floatingDropdown = document.createElement('div');
    floatingDropdown.className = 'floating-dropdown';
    floatingDropdown.setAttribute('data-level', level);
    
    const leftPosition = 310 + (level * 220); // Adjusted for floating sidebar (15px margin + 280px width + 15px gap)
    const topPosition = level === 0 ? clickedElement.getBoundingClientRect().top : 
                       clickedElement.getBoundingClientRect().top - 10;
    
    floatingDropdown.style.cssText = `
        position: fixed;
        left: ${leftPosition}px;
        top: ${topPosition}px;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        z-index: ${1050 + level};
        min-width: 200px;
        width: auto;
        padding: 8px 0;
    `;

    // Create content container
    const contentContainer = document.createElement('div');

    // Get direct children of the target element (only first level items)
    const directChildren = targetElement.children;
    for (let i = 0; i < directChildren.length; i++) {
        const child = directChildren[i].cloneNode(true);
        
        // If this is a nav container, get its children
        if (child.classList.contains('nav')) {
            const navItems = child.children;
            for (let j = 0; j < navItems.length; j++) {
                const navItem = navItems[j].cloneNode(true);
                const link = navItem.querySelector('a');
                
                if (link) {
                    // Style the link
                    link.style.cssText = `
                        display: flex;
                        align-items: center;
                        padding: 8px 16px;
                        color: #4a5568;
                        text-decoration: none;
                        font-size: 14px;
                        transition: all 0.2s ease;
                        border: none;
                        background: none;
                        position: relative;
                    `;
                    
                    // Check if this link has nested dropdown
                    const nestedTarget = link.getAttribute('data-bs-target');
                    if (nestedTarget) {
                        // Add arrow indicator for nested items
                        const arrow = document.createElement('i');
                        arrow.className = 'fas fa-chevron-right';
                        arrow.style.cssText = `
                            margin-left: auto;
                            font-size: 12px;
                            color: #9ca3af;
                        `;
                        link.appendChild(arrow);
                        
                        // Remove Bootstrap attributes
                        link.removeAttribute('data-bs-toggle');
                        link.removeAttribute('data-bs-target');
                        link.removeAttribute('aria-expanded');
                        
                        let hoverTimeout;
                        
                        // Add hover handlers for nested dropdown
                        link.addEventListener('mouseenter', function(e) {
                            this.style.backgroundColor = '#f7fafc';
                            this.style.color = '#2b6cb0';
                            
                            // Clear any existing timeout
                            clearTimeout(hoverTimeout);
                            
                            // Show nested dropdown after short delay
                            hoverTimeout = setTimeout(() => {
                                createFloatingDropdown(this, nestedTarget, level + 1);
                            }, 100);
                        });
                        
                        link.addEventListener('mouseleave', function(e) {
                            this.style.backgroundColor = 'transparent';
                            this.style.color = '#4a5568';
                            
                            // Clear timeout if mouse leaves before delay
                            clearTimeout(hoverTimeout);
                        });
                    } else {
                        // Regular menu item hover
                        link.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = '#f7fafc';
                            this.style.color = '#2b6cb0';
                        });
                        link.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = 'transparent';
                            this.style.color = '#4a5568';
                        });
                    }
                    
                    contentContainer.appendChild(navItem);
                }
            }
        } else {
            // Handle direct menu items
            const link = child.querySelector('a');
            if (link) {
                link.style.cssText = `
                    display: flex;
                    align-items: center;
                    padding: 8px 16px;
                    color: #4a5568;
                    text-decoration: none;
                    font-size: 14px;
                    transition: all 0.2s ease;
                    border: none;
                    background: none;
                    position: relative;
                `;
                
                // Check if this link has nested dropdown
                const nestedTarget = link.getAttribute('data-bs-target');
                if (nestedTarget) {
                    // Add arrow indicator
                    const arrow = document.createElement('i');
                    arrow.className = 'fas fa-chevron-right';
                    arrow.style.cssText = `
                        margin-left: auto;
                        font-size: 12px;
                        color: #9ca3af;
                    `;
                    link.appendChild(arrow);
                    
                    link.removeAttribute('data-bs-toggle');
                    link.removeAttribute('data-bs-target');
                    link.removeAttribute('aria-expanded');
                    
                    let hoverTimeout;
                    
                    link.addEventListener('mouseenter', function(e) {
                        this.style.backgroundColor = '#f7fafc';
                        this.style.color = '#2b6cb0';
                        
                        clearTimeout(hoverTimeout);
                        hoverTimeout = setTimeout(() => {
                            createFloatingDropdown(this, nestedTarget, level + 1);
                        }, 100);
                    });
                    
                    link.addEventListener('mouseleave', function(e) {
                        this.style.backgroundColor = 'transparent';
                        this.style.color = '#4a5568';
                        clearTimeout(hoverTimeout);
                    });
                } else {
                    link.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = '#f7fafc';
                        this.style.color = '#2b6cb0';
                    });
                    link.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = 'transparent';
                        this.style.color = '#4a5568';
                    });
                }
            }
            contentContainer.appendChild(child);
        }
    }
    
    floatingDropdown.appendChild(contentContainer);
    
    // Add to body
    document.body.appendChild(floatingDropdown);

    // Add hover handlers to keep dropdown open
    let closeTimeout;
    
    floatingDropdown.addEventListener('mouseenter', function() {
        clearTimeout(closeTimeout);
    });
    
    floatingDropdown.addEventListener('mouseleave', function() {
        closeTimeout = setTimeout(() => {
            // Remove this dropdown and any higher level dropdowns
            const allDropdowns = document.querySelectorAll('.floating-dropdown');
            allDropdowns.forEach(dropdown => {
                const dropdownLevel = parseInt(dropdown.getAttribute('data-level') || '0');
                if (dropdownLevel >= level) {
                    dropdown.remove();
                }
            });
        }, 300);
    });

    return floatingDropdown;
}

/**
 * Initialize sidebar functionality
 */
function initializeSidebar() {
    // Initialize global elements
    sidebar = document.getElementById('sidebar');
    content = document.querySelector('.content-with-sidebar');
    body = document.body;
    
    if (!sidebar || !content || !body) {
        console.warn('Admin sidebar elements not found');
        return;
    }
    
    // Sidebar starts expanded by default, no initialization needed
    
    const dropdownToggles = document.querySelectorAll('#sidebar [data-bs-toggle="collapse"]');
    
    dropdownToggles.forEach(function(toggle) {
        let toggleHoverTimeout;
        let clickTimeout;
        
        // Handle hover events when sidebar is collapsed - DISABLED
        // Dropdown hover functionality removed for cleaner UX
        
        // Handle click events for both collapsed and expanded states
        toggle.addEventListener('click', function(e) {
            // If sidebar is collapsed on desktop, prevent default and show dropdown
            if (window.innerWidth > 768 && sidebar.classList.contains('collapsed')) {
                e.preventDefault();
                e.stopPropagation();
                
                // Clear hover timeout
                clearTimeout(toggleHoverTimeout);
                
                const targetId = toggle.getAttribute('data-bs-target');
                createFloatingDropdown(toggle, targetId, 0);
            }
            // If sidebar is not collapsed, let bootstrap handle normally
        });
    });
    
    // Add hover functionality to expand/collapse sidebar
    sidebar.addEventListener('mouseenter', function(e) {
        // Hover expand only works if sidebar is currently collapsed
        if (window.innerWidth > 768 && sidebar.classList.contains('collapsed')) {
            // Clear any pending leave timeout
            clearTimeout(leaveTimeout);
            
            // Expand sidebar on hover with delay
            hoverTimeout = setTimeout(() => {
                if (sidebar.classList.contains('collapsed')) {
                    sidebar.classList.remove('collapsed');
                    content.classList.remove('sidebar-collapsed');
                    body.classList.remove('sidebar-collapsed');
                    sidebar.setAttribute('data-hover-expanded', 'true');
                    
                    // Remove floating dropdowns since we're expanding
                    const allDropdowns = document.querySelectorAll('.floating-dropdown');
                    allDropdowns.forEach(dropdown => dropdown.remove());
                }
            }, 10); // Faster hover delay - reduced from 200ms to 100ms
        }
    });
    
    sidebar.addEventListener('mouseleave', function(e) {
        if (window.innerWidth > 768) {
            // Clear hover timeout
            clearTimeout(hoverTimeout);
            
            // Collapse sidebar after delay (only if hover-expanded and not manually set)
            leaveTimeout = setTimeout(() => {
                if (sidebar.hasAttribute('data-hover-expanded') && !sidebar.hasAttribute('data-manually-expanded')) {
                    sidebar.classList.add('collapsed');
                    content.classList.add('sidebar-collapsed');
                    body.classList.add('sidebar-collapsed');
                    sidebar.removeAttribute('data-hover-expanded');
                }
                
                // Clean up floating dropdowns
                const allDropdowns = document.querySelectorAll('.floating-dropdown');
                allDropdowns.forEach(dropdown => {
                    if (!dropdown.matches(':hover')) {
                        dropdown.remove();
                    }
                });
            }, 50);
        }
    });
}

/**
 * Handle mobile sidebar auto-close when clicking outside
 */
function initializeMobileHandlers() {
    // Auto-close sidebar on mobile when clicking outside
    document.addEventListener('click', function(event) {
        if (!sidebar || window.innerWidth > 768) return;
        
        const toggleButton = document.querySelector('.sidebar-toggle-btn');
        
        if (!sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
            sidebar.classList.remove('show');
        }
    });
}

/**
 * Handle window resize events
 */
function initializeResizeHandlers() {
    window.addEventListener('resize', function() {
        if (!sidebar) return; // Safety check
        
        // Remove all floating dropdowns on resize
        const existingFloating = document.querySelectorAll('.floating-dropdown');
        existingFloating.forEach(dropdown => dropdown.remove());
        
        // Clear any pending timeouts
        clearTimeout(hoverTimeout);
        clearTimeout(leaveTimeout);
        
        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
            // Reset manual expansion attributes on resize
            sidebar.removeAttribute('data-hover-expanded');
        } else {
            // Remove desktop-specific classes on mobile
            sidebar.classList.remove('collapsed');
            content.classList.remove('sidebar-collapsed');
            body.classList.remove('sidebar-collapsed');
            sidebar.removeAttribute('data-manually-expanded');
            sidebar.removeAttribute('data-hover-expanded');
        }
    });
}

/**
 * Initialize all admin layout functionality
 */
function initializeAdminLayout() {
    initializeSidebar();
    initializeMobileHandlers();
    initializeResizeHandlers();
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeAdminLayout);

// Make functions available globally for use in Blade templates
window.toggleSidebar = toggleSidebar;
window.createFloatingDropdown = createFloatingDropdown;
