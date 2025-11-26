/**
 * Public Navbar JavaScript
 * Handles public navbar functionality and interactions
 */

window.PublicNavbar = {
    isScrolled: false,
    
    init: function() {
        this.bindEvents();
        this.initScrollEffect();
        this.initMobileMenu();
    },

    bindEvents: function() {
        // Handle scroll effects
        window.addEventListener('scroll', Utils.throttle(() => {
            this.handleScroll();
        }, 16));

        // Handle mobile menu toggle
        const navbarToggler = document.querySelector('.navbar-toggler');
        if (navbarToggler) {
            navbarToggler.addEventListener('click', () => this.toggleMobileMenu());
        }

        // Handle dropdown menus
        const dropdownToggles = document.querySelectorAll('.navbar .dropdown-toggle');
        dropdownToggles.forEach(toggle => {
            // Desktop hover effect
            if (window.innerWidth >= 992) {
                toggle.parentElement.addEventListener('mouseenter', () => {
                    const dropdown = new bootstrap.Dropdown(toggle);
                    dropdown.show();
                });

                toggle.parentElement.addEventListener('mouseleave', () => {
                    const dropdown = new bootstrap.Dropdown(toggle);
                    dropdown.hide();
                });
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            const navbar = document.querySelector('.navbar-collapse');
            const toggler = document.querySelector('.navbar-toggler');
            
            if (navbar && navbar.classList.contains('show') && 
                !navbar.contains(e.target) && 
                !toggler.contains(e.target)) {
                this.closeMobileMenu();
            }
        });

        // Handle window resize
        window.addEventListener('resize', Utils.throttle(() => {
            this.handleResize();
        }, 250));
    },

    handleScroll: function() {
        const navbar = document.querySelector('.navbar-public');
        if (!navbar) return;

        const scrollTop = window.pageYOffset;
        const shouldBeScrolled = scrollTop > 50;

        if (shouldBeScrolled !== this.isScrolled) {
            this.isScrolled = shouldBeScrolled;
            
            if (this.isScrolled) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        // Hide/show navbar on scroll (optional)
        if (navbar.dataset.autoHide === 'true') {
            this.handleAutoHide(scrollTop);
        }
    },

    handleAutoHide: function(scrollTop) {
        const navbar = document.querySelector('.navbar-public');
        if (!navbar) return;

        if (!this.lastScrollTop) this.lastScrollTop = 0;

        if (scrollTop > this.lastScrollTop && scrollTop > 100) {
            // Scrolling down
            navbar.style.transform = 'translateY(-100%)';
        } else {
            // Scrolling up
            navbar.style.transform = 'translateY(0)';
        }

        this.lastScrollTop = scrollTop;
    },

    initScrollEffect: function() {
        const navbar = document.querySelector('.navbar-public');
        if (navbar) {
            // Add transition for smooth scroll effects
            navbar.style.transition = 'all 0.3s ease-in-out';
        }
    },

    initMobileMenu: function() {
        const navbarCollapse = document.querySelector('.navbar-collapse');
        if (navbarCollapse) {
            // Add custom mobile menu styling
            navbarCollapse.addEventListener('show.bs.collapse', () => {
                document.body.style.overflow = 'hidden';
            });

            navbarCollapse.addEventListener('hide.bs.collapse', () => {
                document.body.style.overflow = '';
            });
        }
    },

    toggleMobileMenu: function() {
        const navbarCollapse = document.querySelector('.navbar-collapse');
        if (navbarCollapse) {
            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                toggle: true
            });
        }
    },

    closeMobileMenu: function() {
        const navbarCollapse = document.querySelector('.navbar-collapse');
        if (navbarCollapse && navbarCollapse.classList.contains('show')) {
            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                toggle: false
            });
            bsCollapse.hide();
        }
    },

    handleResize: function() {
        // Close mobile menu on desktop resize
        if (window.innerWidth >= 992) {
            this.closeMobileMenu();
        }

        // Re-initialize dropdown behavior
        this.bindEvents();
    },

    // Method to highlight active nav item based on current page
    setActiveNavItem: function() {
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        const currentPath = window.location.pathname;

        navLinks.forEach(link => {
            const linkPath = new URL(link.href).pathname;
            
            if (linkPath === currentPath) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    },

    // Method to show/hide CTA button based on conditions
    toggleCTAButton: function(show = true) {
        const ctaButton = document.querySelector('.navbar .btn-cta');
        if (ctaButton) {
            ctaButton.style.display = show ? 'inline-block' : 'none';
        }
    },

    // Method to update navbar brand or logo
    updateBrand: function(text, logoUrl = null) {
        const brand = document.querySelector('.navbar-brand');
        if (brand) {
            if (logoUrl) {
                brand.innerHTML = `<img src="${logoUrl}" alt="${text}" height="32" class="me-2">${text}`;
            } else {
                brand.textContent = text;
            }
        }
    },

    // Method to add notification badge to nav items
    addNotificationBadge: function(navItemSelector, count) {
        const navItem = document.querySelector(navItemSelector);
        if (navItem && count > 0) {
            let badge = navItem.querySelector('.badge');
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'badge bg-danger badge-notification';
                badge.style.cssText = `
                    position: absolute;
                    top: -5px;
                    right: -10px;
                    font-size: 0.75rem;
                    border-radius: 50%;
                    min-width: 18px;
                    height: 18px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                `;
                navItem.style.position = 'relative';
                navItem.appendChild(badge);
            }
            badge.textContent = count > 99 ? '99+' : count;
        }
    },

    // Method to remove notification badge
    removeNotificationBadge: function(navItemSelector) {
        const navItem = document.querySelector(navItemSelector);
        if (navItem) {
            const badge = navItem.querySelector('.badge-notification');
            if (badge) {
                badge.remove();
            }
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.navbar-public')) {
        window.PublicNavbar.init();
        window.PublicNavbar.setActiveNavItem();
    }
});

export default window.PublicNavbar;
