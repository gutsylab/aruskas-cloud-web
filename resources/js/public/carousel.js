/**
 * Public Carousel Component
 * Handles carousel/slider functionality for the public landing pages
 */

// Public carousel functionality
window.PublicCarousel = {
    carousels: {},
    
    init() {
        this.initializeCarousels();
    },

    initializeCarousels() {
        const carousels = document.querySelectorAll('[data-carousel]');
        carousels.forEach(carousel => {
            const carouselId = carousel.id || `carousel-${Date.now()}`;
            if (!carousel.id) {
                carousel.id = carouselId;
            }
            this.setupCarousel(carouselId);
        });
    },

    setupCarousel(carouselId) {
        const carousel = document.getElementById(carouselId);
        if (!carousel) return;

        const slides = carousel.querySelectorAll('[data-carousel-item]');
        const prevBtn = carousel.querySelector('[data-carousel-prev]');
        const nextBtn = carousel.querySelector('[data-carousel-next]');
        const indicators = carousel.querySelectorAll('[data-carousel-slide-to]');

        if (slides.length === 0) return;

        const carouselConfig = {
            currentSlide: 0,
            totalSlides: slides.length,
            autoPlay: carousel.dataset.autoplay === 'true',
            interval: parseInt(carousel.dataset.interval) || 5000,
            slides: slides,
            prevBtn: prevBtn,
            nextBtn: nextBtn,
            indicators: indicators,
            intervalId: null
        };

        this.carousels[carouselId] = carouselConfig;

        // Setup event listeners
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.prevSlide(carouselId));
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextSlide(carouselId));
        }

        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => this.goToSlide(carouselId, index));
        });

        // Setup touch/swipe events for mobile
        this.setupTouchEvents(carouselId);

        // Initialize first slide
        this.goToSlide(carouselId, 0);

        // Start autoplay if enabled
        if (carouselConfig.autoPlay) {
            this.startAutoPlay(carouselId);
        }

        // Pause autoplay on hover
        carousel.addEventListener('mouseenter', () => this.pauseAutoPlay(carouselId));
        carousel.addEventListener('mouseleave', () => {
            if (carouselConfig.autoPlay) {
                this.startAutoPlay(carouselId);
            }
        });
    },

    goToSlide(carouselId, slideIndex) {
        const config = this.carousels[carouselId];
        if (!config) return;

        // Update current slide
        config.currentSlide = slideIndex;

        // Hide all slides
        config.slides.forEach((slide, index) => {
            slide.classList.add('hidden');
            slide.classList.remove('block');
            if (index === slideIndex) {
                slide.classList.remove('hidden');
                slide.classList.add('block');
            }
        });

        // Update indicators
        config.indicators.forEach((indicator, index) => {
            indicator.classList.remove('active');
            if (index === slideIndex) {
                indicator.classList.add('active');
            }
        });

        // Update navigation buttons
        if (config.prevBtn) {
            config.prevBtn.disabled = slideIndex === 0;
        }
        if (config.nextBtn) {
            config.nextBtn.disabled = slideIndex === config.totalSlides - 1;
        }
    },

    nextSlide(carouselId) {
        const config = this.carousels[carouselId];
        if (!config) return;

        const nextIndex = (config.currentSlide + 1) % config.totalSlides;
        this.goToSlide(carouselId, nextIndex);
    },

    prevSlide(carouselId) {
        const config = this.carousels[carouselId];
        if (!config) return;

        const prevIndex = config.currentSlide === 0 ? config.totalSlides - 1 : config.currentSlide - 1;
        this.goToSlide(carouselId, prevIndex);
    },

    startAutoPlay(carouselId) {
        const config = this.carousels[carouselId];
        if (!config) return;

        this.pauseAutoPlay(carouselId); // Clear existing interval
        config.intervalId = setInterval(() => {
            this.nextSlide(carouselId);
        }, config.interval);
    },

    pauseAutoPlay(carouselId) {
        const config = this.carousels[carouselId];
        if (!config) return;

        if (config.intervalId) {
            clearInterval(config.intervalId);
            config.intervalId = null;
        }
    },

    setupTouchEvents(carouselId) {
        const carousel = document.getElementById(carouselId);
        if (!carousel) return;

        let startX = 0;
        let endX = 0;

        carousel.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });

        carousel.addEventListener('touchmove', (e) => {
            e.preventDefault();
        });

        carousel.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            const diff = startX - endX;

            if (Math.abs(diff) > 50) { // Minimum swipe distance
                if (diff > 0) {
                    this.nextSlide(carouselId);
                } else {
                    this.prevSlide(carouselId);
                }
            }
        });
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.PublicCarousel.init();
    });
} else {
    window.PublicCarousel.init();
}
