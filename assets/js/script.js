// DE VOLPE Wedding & Events - Interactive JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Navigation scroll effect
    const navbar = document.getElementById('navbar');
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');

    // Homepage hero slider
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        const slides = Array.from(heroSlider.querySelectorAll('.hero-slide'));
        const dots = Array.from(heroSlider.querySelectorAll('.hero-dot'));
        const heroContent = heroSlider.querySelector('.hero-content');
        const heroHeading = heroSlider.querySelector('.hero-heading');
        const heroSubtitle = heroSlider.querySelector('.hero-subtitle');
        const heroDescription = heroSlider.querySelector('.hero-description');
        const progressFill = heroSlider.querySelector('.hero-progress-fill');
        const slideCopy = [
            {
                heading: 'DE VOLPE Wedding Venue in eMondlo',
                subtitle: 'Celebrate Your Story at DE VOLPE',
                description: 'Bring your loved ones together in a setting made for meaningful moments, joyful celebrations, and memories you will treasure for years to come.'
            },
            {
                heading: 'Every Detail, Beautifully Yours',
                subtitle: 'A Venue Made for Your Vision',
                description: 'From the first welcome to the final dance, our elegant spaces give your celebration a warm, distinctive setting that feels uniquely yours.'
            },
            {
                heading: 'Where Dreams Come True',
                subtitle: 'Celebrating Love at DE VOLPE',
                description: 'Experience the magic of your special day at our breathtaking wedding and events venue nestled in the heart of eMondlo, KwaZulu-Natal. From intimate ceremonies to grand celebrations, we create unforgettable moments that last a lifetime.'
            },
            {
                heading: 'Gather, Celebrate, Remember',
                subtitle: 'Moments Made Magical at DE VOLPE',
                description: 'Share the laughter, the happy tears, and the little moments in between in a venue designed to make every part of your day feel special.'
            },
            {
                heading: 'Your Celebration, Your Way',
                subtitle: 'Make DE VOLPE Yours',
                description: 'Whether your gathering is intimate or grand, our beautiful venue is ready to become the backdrop to your next unforgettable occasion.'
            }
        ];
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        let activeSlide = 0;
        let autoplayTimer;

        const showSlide = (index) => {
            const nextSlide = (index + slides.length) % slides.length;
            const copyChanged = nextSlide !== activeSlide;
            activeSlide = nextSlide;
            slides.forEach((slide, slideIndex) => {
                const isActive = slideIndex === activeSlide;
                slide.classList.toggle('is-active', isActive);
                slide.setAttribute('aria-hidden', String(!isActive));
            });
            dots.forEach((dot, dotIndex) => {
                const isActive = dotIndex === activeSlide;
                dot.classList.toggle('is-active', isActive);
                if (isActive) dot.setAttribute('aria-current', 'true');
                else dot.removeAttribute('aria-current');
            });
            if (copyChanged && slideCopy[activeSlide]) {
                const copy = slideCopy[activeSlide];
                heroContent.classList.remove('is-copy-changing');
                heroHeading.textContent = copy.heading;
                heroSubtitle.textContent = copy.subtitle;
                heroDescription.textContent = copy.description;
                void heroContent.offsetWidth;
                heroContent.classList.add('is-copy-changing');
            }
            if (progressFill) {
                progressFill.classList.remove('is-running');
                void progressFill.offsetWidth;
                progressFill.classList.add('is-running');
            }
        };

        const stopAutoplay = () => window.clearInterval(autoplayTimer);
        const startAutoplay = () => {
            stopAutoplay();
            if (!reducedMotion && !document.hidden) {
                autoplayTimer = window.setInterval(() => showSlide(activeSlide + 1), 6000);
            }
        };

        heroSlider.querySelector('.hero-arrow-prev').addEventListener('click', () => {
            showSlide(activeSlide - 1);
            startAutoplay();
        });
        heroSlider.querySelector('.hero-arrow-next').addEventListener('click', () => {
            showSlide(activeSlide + 1);
            startAutoplay();
        });
        dots.forEach((dot, index) => dot.addEventListener('click', () => {
            showSlide(index);
            startAutoplay();
        }));
        heroSlider.addEventListener('mouseenter', stopAutoplay);
        heroSlider.addEventListener('mouseleave', startAutoplay);
        heroSlider.addEventListener('focusin', stopAutoplay);
        heroSlider.addEventListener('focusout', (event) => {
            if (!heroSlider.contains(event.relatedTarget)) startAutoplay();
        });
        document.addEventListener('visibilitychange', () => document.hidden ? stopAutoplay() : startAutoplay());
        showSlide(0);
        startAutoplay();
    }

    // Add scroll effect to navbar
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Mobile menu toggle
    menuToggle.addEventListener('click', function() {
        navMenu.classList.toggle('active');
        menuToggle.classList.toggle('active');
    });

    // Close mobile menu when clicking on links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
            menuToggle.classList.remove('active');
        });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Form validation and enhancement
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();

            if (!name || !email || !message) {
                e.preventDefault();
                showNotification('Please fill in all required fields.', 'error');
                return;
            }

            if (!isValidEmail(email)) {
                e.preventDefault();
                showNotification('Please enter a valid email address.', 'error');
                return;
            }

            // Show loading state
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;

            // Re-enable button after form submission (will be handled by page reload)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });
    }

    // Check for reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Gallery modal functionality
    const galleryItems = document.querySelectorAll('.gallery-item img');
    galleryItems.forEach(img => {
        img.addEventListener('click', function() {
            openImageModal(this.src, this.alt);
        });
    });

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.startsWith('27')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                value = '+27' + value.substring(1);
            }
            this.value = value;
        });
    }

    // Note: Card hover effects handled by CSS for better performance

    // Enhanced Parallax effect for hero sections (respects reduced motion)
    if (!prefersReducedMotion) {
        let ticking = false;
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    const heroSections = document.querySelectorAll('.hero');
                    
                    heroSections.forEach(hero => {
                        const rect = hero.getBoundingClientRect();
                        if (rect.top < window.innerHeight && rect.bottom > 0) {
                            const rate = scrolled * -0.3;
                            hero.style.transform = `translate3d(0, ${rate}px, 0)`;
                        }
                    });
                    
                    ticking = false;
                });
                
                ticking = true;
            }
        });
    }
    
    // Scroll Progress Indicator (respects reduced motion)
    if (!prefersReducedMotion) {
        const createScrollIndicator = () => {
            const indicator = document.createElement('div');
            indicator.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                height: 3px;
                background: linear-gradient(90deg, var(--gold-primary), var(--gold-light));
                z-index: 10001;
                transition: width 0.1s ease;
            `;
            document.body.appendChild(indicator);
            
            window.addEventListener('scroll', () => {
                const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
                indicator.style.width = scrollPercent + '%';
            });
        };
        createScrollIndicator();
    }
    
    // Consolidated scroll reveal with stagger (respects reduced motion)
    const revealElements = document.querySelectorAll('.fade-in-up, .card, .feature-item');
    
    if (prefersReducedMotion) {
        // Skip animations for reduced motion users - make visible immediately
        revealElements.forEach(el => {
            el.classList.add('reveal-in');
        });
    } else {
        // Create index map for deterministic stagger based on DOM order
        const elementIndexMap = new Map();
        revealElements.forEach((el, index) => {
            elementIndexMap.set(el, index);
            el.classList.add('reveal-init'); // Add initial state via CSS class
        });
        
        // Apply reveal animations with stagger
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const domIndex = elementIndexMap.get(entry.target);
                    setTimeout(() => {
                        entry.target.classList.remove('reveal-init');
                        entry.target.classList.add('reveal-in');
                    }, domIndex * 100);
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -100px 0px'
        });
        
        revealElements.forEach(el => {
            revealObserver.observe(el);
        });
    }
    
    // Mouse movement parallax on hero (respects reduced motion)
    if (!prefersReducedMotion) {
        const hero = document.querySelector('.hero');
        if (hero) {
            document.addEventListener('mousemove', (e) => {
                const mouseX = e.clientX / window.innerWidth;
                const mouseY = e.clientY / window.innerHeight;
                
                const moveX = (mouseX - 0.5) * 2;
                const moveY = (mouseY - 0.5) * 2;
                
                hero.style.backgroundPosition = `${50 + moveX}% ${40 + moveY}%`;
            });
        }
    }
    
    // Add shimmer to section titles on hover
    const sectionTitles = document.querySelectorAll('.section-title');
    sectionTitles.forEach(title => {
        title.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        title.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});

// Utility Functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 5px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        max-width: 300px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    `;
    
    switch(type) {
        case 'success':
            notification.style.background = '#28a745';
            break;
        case 'error':
            notification.style.background = '#dc3545';
            break;
        case 'warning':
            notification.style.background = '#ffc107';
            notification.style.color = '#000';
            break;
        default:
            notification.style.background = '#17a2b8';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}

function openImageModal(src, alt) {
    const modal = document.createElement('div');
    modal.className = 'image-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        cursor: pointer;
    `;
    
    const img = document.createElement('img');
    img.src = src;
    img.alt = alt;
    img.style.cssText = `
        max-width: 90%;
        max-height: 90%;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
    `;
    
    modal.appendChild(img);
    document.body.appendChild(modal);
    
    modal.addEventListener('click', function() {
        document.body.removeChild(modal);
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (document.body.contains(modal)) {
                document.body.removeChild(modal);
            }
        }
    });
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .menu-toggle.active span:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
    }
    
    .menu-toggle.active span:nth-child(2) {
        opacity: 0;
    }
    
    .menu-toggle.active span:nth-child(3) {
        transform: rotate(-45deg) translate(7px, -6px);
    }
    
    .image-modal {
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
`;
document.head.appendChild(style);
