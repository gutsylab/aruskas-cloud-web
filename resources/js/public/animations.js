/**
 * Public Animations JavaScript
 * Handles various animations for public pages
 */

window.PublicAnimations = {
    init: function() {
        this.initScrollReveal();
        this.initTypingEffect();
        this.initCounterAnimations();
        this.initParticleEffects();
        this.initMorphingShapes();
    },

    initScrollReveal: function() {
        // Create intersection observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);

        // Observe elements with animation classes
        const animatedElements = document.querySelectorAll(`
            .fade-in-up,
            .fade-in-down,
            .fade-in-left,
            .fade-in-right,
            .zoom-in,
            .zoom-out,
            .flip-in-x,
            .flip-in-y,
            .bounce-in,
            .slide-in-up,
            .slide-in-down
        `);

        animatedElements.forEach(element => {
            observer.observe(element);
        });

        // Add staggered animation delays for grouped elements
        this.addStaggeredDelays();
    },

    addStaggeredDelays: function() {
        const groups = document.querySelectorAll('.stagger-children');
        
        groups.forEach(group => {
            const children = group.children;
            const delay = parseInt(group.dataset.staggerDelay) || 100;
            
            Array.from(children).forEach((child, index) => {
                child.style.animationDelay = `${index * delay}ms`;
            });
        });
    },

    initTypingEffect: function() {
        const typingElements = document.querySelectorAll('.typing-effect');
        
        typingElements.forEach(element => {
            this.createTypingEffect(element);
        });
    },

    createTypingEffect: function(element) {
        const text = element.textContent;
        const speed = parseInt(element.dataset.typeSpeed) || 50;
        const delay = parseInt(element.dataset.typeDelay) || 0;
        
        element.textContent = '';
        element.style.borderRight = '2px solid currentColor';
        
        setTimeout(() => {
            let i = 0;
            const typeInterval = setInterval(() => {
                if (i < text.length) {
                    element.textContent += text.charAt(i);
                    i++;
                } else {
                    clearInterval(typeInterval);
                    // Remove cursor after typing is complete
                    setTimeout(() => {
                        element.style.borderRight = 'none';
                    }, 1000);
                }
            }, speed);
        }, delay);
    },

    initCounterAnimations: function() {
        const counters = document.querySelectorAll('.animated-counter');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    this.animateCounter(entry.target);
                    entry.target.classList.add('counted');
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => {
            observer.observe(counter);
        });
    },

    animateCounter: function(element) {
        const target = parseInt(element.dataset.count) || 0;
        const duration = parseInt(element.dataset.duration) || 2000;
        const easing = element.dataset.easing || 'easeOutCubic';
        
        const startTime = performance.now();
        const startValue = 0;

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const easedProgress = this.applyEasing(progress, easing);
            const currentValue = Math.floor(startValue + (target - startValue) * easedProgress);
            
            element.textContent = currentValue.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    },

    applyEasing: function(t, easing) {
        switch (easing) {
            case 'easeOutCubic':
                return 1 - Math.pow(1 - t, 3);
            case 'easeInOutCubic':
                return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
            case 'easeOutBounce':
                const n1 = 7.5625;
                const d1 = 2.75;
                if (t < 1 / d1) {
                    return n1 * t * t;
                } else if (t < 2 / d1) {
                    return n1 * (t -= 1.5 / d1) * t + 0.75;
                } else if (t < 2.5 / d1) {
                    return n1 * (t -= 2.25 / d1) * t + 0.9375;
                } else {
                    return n1 * (t -= 2.625 / d1) * t + 0.984375;
                }
            default:
                return t; // linear
        }
    },

    initParticleEffects: function() {
        const particleContainers = document.querySelectorAll('.particles-container');
        
        particleContainers.forEach(container => {
            this.createParticleEffect(container);
        });
    },

    createParticleEffect: function(container) {
        const particleCount = parseInt(container.dataset.particleCount) || 50;
        const particleColor = container.dataset.particleColor || 'rgba(255, 255, 255, 0.5)';
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 4 + 1}px;
                height: ${Math.random() * 4 + 1}px;
                background: ${particleColor};
                border-radius: 50%;
                top: ${Math.random() * 100}%;
                left: ${Math.random() * 100}%;
                animation: particle-float ${Math.random() * 20 + 10}s linear infinite;
                animation-delay: ${Math.random() * 5}s;
            `;
            container.appendChild(particle);
        }

        // Add particle animation keyframes
        if (!document.querySelector('#particle-keyframes')) {
            const style = document.createElement('style');
            style.id = 'particle-keyframes';
            style.textContent = `
                @keyframes particle-float {
                    0% {
                        transform: translateY(100vh) rotate(0deg);
                        opacity: 0;
                    }
                    10% {
                        opacity: 1;
                    }
                    90% {
                        opacity: 1;
                    }
                    100% {
                        transform: translateY(-100vh) rotate(360deg);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    },

    initMorphingShapes: function() {
        const morphingShapes = document.querySelectorAll('.morphing-shape');
        
        morphingShapes.forEach(shape => {
            this.createMorphingAnimation(shape);
        });
    },

    createMorphingAnimation: function(element) {
        const shapes = [
            'M20,20 L80,20 L80,80 L20,80 Z', // Square
            'M50,10 L90,90 L10,90 Z', // Triangle
            'M50,10 A40,40 0 1,1 50,90 A40,40 0 1,1 50,10', // Circle
            'M25,25 L75,25 L90,50 L75,75 L25,75 L10,50 Z', // Hexagon
        ];
        
        let currentShape = 0;
        const svg = element.querySelector('svg path');
        
        if (!svg) return;
        
        setInterval(() => {
            currentShape = (currentShape + 1) % shapes.length;
            svg.style.transition = 'all 2s ease-in-out';
            svg.setAttribute('d', shapes[currentShape]);
        }, 3000);
    },

    // Utility method to add animation class with delay
    addAnimationClass: function(element, className, delay = 0) {
        setTimeout(() => {
            element.classList.add(className);
        }, delay);
    },

    // Method to create custom animation sequence
    createAnimationSequence: function(elements, className, staggerDelay = 100) {
        elements.forEach((element, index) => {
            this.addAnimationClass(element, className, index * staggerDelay);
        });
    },

    // Method to pause/resume animations
    pauseAnimations: function() {
        document.documentElement.style.animationPlayState = 'paused';
    },

    resumeAnimations: function() {
        document.documentElement.style.animationPlayState = 'running';
    },

    // Method to reduce motion for accessibility
    respectReducedMotion: function() {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        
        if (prefersReducedMotion.matches) {
            // Disable animations for users who prefer reduced motion
            const style = document.createElement('style');
            style.textContent = `
                *, *::before, *::after {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                }
            `;
            document.head.appendChild(style);
        }
    }
};

// Add CSS for animations
const animationStyles = document.createElement('style');
animationStyles.textContent = `
    /* Fade animations */
    .fade-in-up { opacity: 0; transform: translateY(30px); transition: all 0.6s ease; }
    .fade-in-up.animated { opacity: 1; transform: translateY(0); }
    
    .fade-in-down { opacity: 0; transform: translateY(-30px); transition: all 0.6s ease; }
    .fade-in-down.animated { opacity: 1; transform: translateY(0); }
    
    .fade-in-left { opacity: 0; transform: translateX(-30px); transition: all 0.6s ease; }
    .fade-in-left.animated { opacity: 1; transform: translateX(0); }
    
    .fade-in-right { opacity: 0; transform: translateX(30px); transition: all 0.6s ease; }
    .fade-in-right.animated { opacity: 1; transform: translateX(0); }
    
    /* Zoom animations */
    .zoom-in { opacity: 0; transform: scale(0.8); transition: all 0.6s ease; }
    .zoom-in.animated { opacity: 1; transform: scale(1); }
    
    .zoom-out { opacity: 0; transform: scale(1.2); transition: all 0.6s ease; }
    .zoom-out.animated { opacity: 1; transform: scale(1); }
    
    /* Flip animations */
    .flip-in-x { opacity: 0; transform: rotateX(90deg); transition: all 0.6s ease; }
    .flip-in-x.animated { opacity: 1; transform: rotateX(0); }
    
    .flip-in-y { opacity: 0; transform: rotateY(90deg); transition: all 0.6s ease; }
    .flip-in-y.animated { opacity: 1; transform: rotateY(0); }
    
    /* Bounce animation */
    .bounce-in { opacity: 0; transform: scale(0.3); transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
    .bounce-in.animated { opacity: 1; transform: scale(1); }
    
    /* Slide animations */
    .slide-in-up { opacity: 0; transform: translateY(100%); transition: all 0.6s ease; }
    .slide-in-up.animated { opacity: 1; transform: translateY(0); }
    
    .slide-in-down { opacity: 0; transform: translateY(-100%); transition: all 0.6s ease; }
    .slide-in-down.animated { opacity: 1; transform: translateY(0); }
`;
document.head.appendChild(animationStyles);

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.PublicAnimations.init();
    window.PublicAnimations.respectReducedMotion();
});

export default window.PublicAnimations;
