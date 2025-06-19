(function () {
    'use strict';

    // Mobile menu functionality
    function setupMobileMenu() {
        // Mobile submenu toggle
        document.querySelectorAll('.dropdown-submenu > .dropdown-toggle').forEach(item => {
            item.addEventListener('click', function (e) {
                const isMobile = window.innerWidth < 992;
                if (isMobile) {
                    e.preventDefault();
                    e.stopPropagation();
                    const submenu = this.nextElementSibling;
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';

                    document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(menu => {
                        if (menu !== submenu) {
                            menu.style.display = 'none';
                            menu.previousElementSibling.setAttribute('aria-expanded', 'false');
                        }
                    });

                    submenu.style.display = isExpanded ? 'none' : 'block';
                    this.setAttribute('aria-expanded', !isExpanded);
                } else {
                    // Prevent desktop subdropdown from closing mega menu
                    e.stopPropagation();
                }
            });
        });

        // Close submenus when clicking elsewhere (mobile only)
        document.addEventListener('click', function (e) {
            if (window.innerWidth < 992 && !e.target.closest('.dropdown-submenu')) {
                document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(menu => {
                    menu.style.display = 'none';
                    menu.previousElementSibling.setAttribute('aria-expanded', 'false');
                });
            }

            // Close all dropdowns when clicking outside
            if (!e.target.closest('.dropdown') && !e.target.closest('.dropdown-menu')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
                document.querySelectorAll('.dropdown-toggle[aria-expanded="true"]').forEach(toggle => {
                    toggle.setAttribute('aria-expanded', 'false');
                });
            }
        });

        // Handle submenu positioning on desktop
        function positionSubmenus() {
            if (window.innerWidth >= 992) {
                document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(menu => {
                    const submenuRect = menu.getBoundingClientRect();
                    const viewportWidth = window.innerWidth;
                    menu.classList.toggle('right-aligned', submenuRect.right > viewportWidth);
                });
            }
        }

        positionSubmenus();
        window.addEventListener('resize', positionSubmenus);
    }

    // Initialize all sliders with error handling
    function initializeSliders() {
        // Helper function to safely initialize sliders
        function initSlider(selector, config) {
            try {
                const element = document.querySelector(selector);
                if (element) new Swiper(selector, config);
            } catch (e) {
                console.error(`Failed to initialize slider ${selector}:`, e);
            }
        }

        // Featured Businesses
        initSlider(".featured-section-slider", {
            slidesPerView: 1,
            spaceBetween: 20,
            freeMode: true,
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: true
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev"
            },
            breakpoints: {
                500: {
                    slidesPerView: 2
                },
                1000: {
                    slidesPerView: 3
                },
                1200: {
                    slidesPerView: 4
                },
                1430: {
                    slidesPerView: 5
                }
            }
        });

        // Provinces
        initSlider(".province-section-slider", {
            slidesPerView: 1,
            spaceBetween: 10,
            freeMode: true,
            loop: true,
            autoplay: {
                delay: 1500,
                disableOnInteraction: true,
                pauseOnMouseEnter: true
            },
            breakpoints: {
                500: {
                    slidesPerView: 3
                },
                1024: {
                    slidesPerView: 4
                }
            }
        });

        // Top Cards
        initSlider(".top-visited-slider", {
            slidesPerView: 1,
            spaceBetween: 20,
            freeMode: true,
            loop: true,
            autoplay: {
                delay: 7500,
                disableOnInteraction: true
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev"
            },
            breakpoints: {
                500: {
                    slidesPerView: 2
                },
                1000: {
                    slidesPerView: 3
                },
                1200: {
                    slidesPerView: 4
                },
                1430: {
                    slidesPerView: 5
                }
            }
        });

        // Popup Featured Businesses
        initSlider(".popup-featured-section-slider", {
            slidesPerView: 1,
            spaceBetween: 20,
            autoplay: {
                delay: 3500,
                disableOnInteraction: true
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev"
            },
            breakpoints: {
                500: {
                    slidesPerView: 1.5
                },
                1180: {
                    slidesPerView: 2
                },
                1440: {
                    slidesPerView: 2.5
                },
                1700: {
                    slidesPerView: 3
                }
            }
        });

        // Single Page Hero Section
        initSlider(".single_page_hero-section-slider", {
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false
            }
        });

        // Related Cards
        initSlider(".related-card-slider", {
            slidesPerView: 1,
            spaceBetween: 20,
            freeMode: true,
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: true
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev"
            },
            breakpoints: {
                500: {
                    slidesPerView: 2
                },
                1000: {
                    slidesPerView: 3
                },
                1200: {
                    slidesPerView: 4
                },
                1430: {
                    slidesPerView: 5
                }
            }
        });
    }

    // Initialize all functionality when DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {
        setupMobileMenu();
        initializeSliders();
        setupCategoryFilter();
    });

})();

function setupAuthForms() {
    // Toggle password visibility
    function setupPasswordToggle(iconId, inputId) {
        const icon = document.getElementById(iconId);
        const input = document.getElementById(inputId);

        if (!icon || !input) return;

        icon.addEventListener('click', function () {
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    }

    // Initialize password toggles
    setupPasswordToggle('toggleLoginPassword', 'pwd');
    setupPasswordToggle('toggleRegisterPassword', 'password');
    setupPasswordToggle('toggleRegisterPasswordConfirm', 'repeat_password');

    // Switch to login tab from register link
    const switchToLogin = document.getElementById('switchToLogin');
    if (switchToLogin) {
        switchToLogin.addEventListener('click', function (e) {
            e.preventDefault();
            const loginTabButton = document.getElementById('login-tab');
            if (loginTabButton) {
                const loginTab = new bootstrap.Tab(loginTabButton);
                loginTab.show();

                // Update URL without reload
                const url = new URL(window.location.href);
                url.searchParams.delete('action');
                url.searchParams.delete('verify');
                window.history.pushState({}, '', url);
            }
        });
    }
}
setupAuthForms();

document.addEventListener('DOMContentLoaded', function () {
    // Handle tab state based on URL parameters on page load
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'register' || urlParams.get('verify') === 'code') {
        const registerTabButton = document.getElementById('register-tab');
        if (registerTabButton) {
            const registerTab = new bootstrap.Tab(registerTabButton);
            registerTab.show();
        }
    }
});

// Home Page Popup
function setupHomePopup() {
    const homepageModalEl = document.getElementById('homepageModal');
    if (homepageModalEl) {
        const homepageModal = new bootstrap.Modal(homepageModalEl);
        homepageModal.show();
    }
}
setupHomePopup();