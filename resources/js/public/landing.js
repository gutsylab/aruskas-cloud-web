/**
 * Public Landing Page JavaScript
 * Handles landing page interactions and animations
 */

window.PublicLanding = {
    init: function() {
        this.initScrollAnimations();
        this.initCounters();
        this.initParallax();
        this.initSmoothScroll();
        this.bindEvents();
    },

    initScrollAnimations: function() {
        // Intersection Observer for scroll animations
        const animatedElements = document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right, .scale-in');
        
        if (animatedElements.length > 0) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            animatedElements.forEach(element => {
                observer.observe(element);
            });
        }
    },

    initCounters: function() {
        const counters = document.querySelectorAll('.stats-number[data-count]');
        
        const countUp = (element) => {
            const target = parseInt(element.dataset.count);
            const duration = parseInt(element.dataset.duration) || 2000;
            const increment = target / (duration / 16); // 60 FPS
            let current = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString();
            }, 16);
        };

        // Trigger counters when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    entry.target.classList.add('counted');
                    countUp(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => {
            observer.observe(counter);
        });
    },

    initParallax: function() {
        const parallaxElements = document.querySelectorAll('.parallax');
        
        if (parallaxElements.length > 0) {
            window.addEventListener('scroll', Utils.throttle(() => {
                const scrolled = window.pageYOffset;
                
                parallaxElements.forEach(element => {
                    const speed = element.dataset.speed || 0.5;
                    const yPos = -(scrolled * speed);
                    element.style.transform = `translateY(${yPos}px)`;
                });
            }, 16));
        }
    },

    initSmoothScroll: function() {
        // Smooth scroll for anchor links
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        
        anchorLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                const targetId = link.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    const offsetTop = targetElement.getBoundingClientRect().top + window.pageYOffset;
                    const navHeight = document.querySelector('.navbar-public')?.offsetHeight || 70;
                    
                    window.scrollTo({
                        top: offsetTop - navHeight,
                        behavior: 'smooth'
                    });
                }
            });
        });
    },

    bindEvents: function() {
        // Newsletter form submission
        const newsletterForm = document.querySelector('.newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', (e) => this.handleNewsletterSubmit(e));
        }

        // Contact form submission
        const contactForm = document.querySelector('.contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', (e) => this.handleContactSubmit(e));
        }

        // Testimonial carousel (if using custom carousel)
        this.initTestimonialCarousel();

        // Floating shapes animation
        this.initFloatingShapes();

        // Scroll to top button
        this.initScrollToTop();
    },

    handleNewsletterSubmit: function(event) {
        event.preventDefault();
        
        const form = event.target;
        const email = form.querySelector('input[type="email"]').value;
        const button = form.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        
        // Basic email validation
        if (!email || !this.isValidEmail(email)) {
            Utils.showToast('Please enter a valid email address', 'warning');
            return;
        }

        // Show loading state
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // Simulate API call (replace with actual endpoint)
        setTimeout(() => {
            Utils.showToast('Thank you for subscribing to our newsletter!', 'success');
            form.reset();
            button.disabled = false;
            button.innerHTML = originalText;
        }, 1000);
    },

    handleContactSubmit: function(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const button = form.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        
        // Validate required fields
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                Utils.showFieldError(field, 'This field is required');
                isValid = false;
            } else {
                Utils.clearFieldError(field);
            }
        });

        if (!isValid) return;

        // Show loading state
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

        // Submit form
        Utils.ajax(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            Utils.showToast(response.message || 'Message sent successfully!', 'success');
            form.reset();
        })
        .catch(error => {
            Utils.showToast('Failed to send message. Please try again.', 'danger');
            console.error('Contact form error:', error);
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        });
    },

    initTestimonialCarousel: function() {
        // Custom testimonial carousel implementation
        const carousel = document.querySelector('.testimonials-carousel');
        if (!carousel) return;

        const slides = carousel.querySelectorAll('.testimonial-slide');
        if (slides.length <= 1) return;

        let currentSlide = 0;
        const totalSlides = slides.length;

        // Create navigation dots
        const dotsContainer = document.createElement('div');
        dotsContainer.className = 'carousel-dots text-center mt-4';
        
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('button');
            dot.className = `carousel-dot ${i === 0 ? 'active' : ''}`;
            dot.addEventListener('click', () => this.goToSlide(i));
            dotsContainer.appendChild(dot);
        }
        
        carousel.appendChild(dotsContainer);

        // Auto-advance slides
        setInterval(() => {
            currentSlide = (currentSlide + 1) % totalSlides;
            this.goToSlide(currentSlide);
        }, 5000);
    },

    goToSlide: function(slideIndex) {
        const slides = document.querySelectorAll('.testimonial-slide');
        const dots = document.querySelectorAll('.carousel-dot');
        
        slides.forEach((slide, index) => {
            slide.style.display = index === slideIndex ? 'block' : 'none';
        });
        
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === slideIndex);
        });
    },

    initFloatingShapes: function() {
        // Create floating shapes dynamically
        const hero = document.querySelector('.hero-landing');
        if (!hero) return;

        const shapesCount = 5;
        for (let i = 0; i < shapesCount; i++) {
            const shape = document.createElement('div');
            shape.className = 'floating-shape';
            shape.style.cssText = `
                position: absolute;
                width: ${20 + Math.random() * 60}px;
                height: ${20 + Math.random() * 60}px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                top: ${Math.random() * 100}%;
                left: ${Math.random() * 100}%;
                animation: float ${3 + Math.random() * 3}s ease-in-out infinite;
                animation-delay: ${Math.random() * 2}s;
            `;
            hero.appendChild(shape);
        }
    },

    initScrollToTop: function() {
        // Create scroll to top button
        const scrollBtn = document.createElement('button');
        scrollBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
        scrollBtn.className = 'scroll-to-top btn btn-primary';
        scrollBtn.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: none;
            z-index: 1000;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        `;
        
        document.body.appendChild(scrollBtn);

        // Show/hide button based on scroll position
        window.addEventListener('scroll', Utils.throttle(() => {
            if (window.pageYOffset > 300) {
                scrollBtn.style.display = 'block';
            } else {
                scrollBtn.style.display = 'none';
            }
        }, 100));

        // Smooth scroll to top
        scrollBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    },

    isValidEmail: function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.hero-landing') || document.querySelector('[data-page="landing"]')) {
        window.PublicLanding.init();
    }
});

export default window.PublicLanding;
