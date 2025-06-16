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

    // Category Page Filter
    function setupCategoryFilter() {
        const businessCardsContainer = document.getElementById('businessCards');
        if (!businessCardsContainer) return;

        const allCards = Array.from(businessCardsContainer.querySelectorAll('.card'));
        const ratingFilter = document.getElementById('ratingFilter');
        const noResults = document.getElementById('noResults');

        if (!ratingFilter) return;

        function applyFilter() {
            const filter = ratingFilter.value;
            let filteredCards = [...allCards];

            if (filter !== 'all') {
                filteredCards.sort((a, b) => {
                    const ratingA = parseFloat(a.getAttribute('data-rating'));
                    const ratingB = parseFloat(b.getAttribute('data-rating'));
                    return filter === 'asc' ? ratingA - ratingB : ratingB - ratingA;
                });
            }

            businessCardsContainer.innerHTML = '';
            filteredCards.forEach(card => businessCardsContainer.appendChild(card));

            if (noResults) {
                noResults.classList.toggle('d-none', filteredCards.length > 0);
            }
        }

        ratingFilter.addEventListener('change', applyFilter);
        applyFilter();
    }

    // Rating and Review System
    (function () {
        const config = {
            currentUser: null,
            isBusinessOwner: false,
            reviewData: {
                averageRating: 4.8,
                totalReviews: 234,
                reviews: [{
                    id: 1,
                    userId: 'user123',
                    userName: 'John Doe',
                    rating: 5,
                    text: 'Great service! Would definitely recommend.',
                    date: '2 days ago',
                    replies: [{
                        id: 1,
                        userId: 'owner456',
                        userName: 'Business Owner',
                        text: 'Thank you for your feedback!',
                        date: '1 day ago'
                    }]
                }]
            }
        };

        const domElements = {
            toggleReviewBtn: document.getElementById('toggleReviewBtn'),
            reviewTextarea: document.getElementById('reviewTextarea'),
            submitReviewBtn: document.getElementById('submitReviewBtn'),
            reviewsContainer: document.getElementById('reviewsContainer'),
            ratingError: document.getElementById('ratingError'),
            reviewTextError: document.getElementById('reviewTextError'),
            averageRating: document.getElementById('averageRating'),
            reviewCount: document.getElementById('reviewCount'),
            loginModal: document.getElementById('loginModal') ? new bootstrap.Modal(document.getElementById('loginModal')) : null,
            modalLoginBtn: document.getElementById('modalLoginBtn'),
            loginEmail: document.getElementById('loginEmail'),
            loginPassword: document.getElementById('loginPassword')
        };

        function init() {
            if (!domElements.reviewsContainer) return;
            setupEventListeners();
            renderReviews();
            updateRatingDisplay();
        }

        function setupEventListeners() {
            if (domElements.toggleReviewBtn) {
                domElements.toggleReviewBtn.addEventListener('click', handleToggleReview);
            }
            if (domElements.submitReviewBtn) {
                domElements.submitReviewBtn.addEventListener('click', handleSubmitReview);
            }
            if (domElements.modalLoginBtn) {
                domElements.modalLoginBtn.addEventListener('click', handleModalLogin);
            }
            if (domElements.reviewsContainer) {
                domElements.reviewsContainer.addEventListener('click', handleReviewActions);
            }
        }

        function handleToggleReview() {
            if (!config.currentUser && domElements.loginModal) {
                domElements.loginModal.show();
                document.getElementById('reviewText').value = '';
                document.querySelectorAll('input[name="new-rating"]').forEach(radio => radio.checked = false);
                domElements.ratingError.style.display = 'none';
                domElements.reviewTextError.style.display = 'none';
                return;
            }

            const isVisible = domElements.reviewTextarea.style.display === 'block';
            domElements.reviewTextarea.style.display = isVisible ? 'none' : 'block';
            domElements.toggleReviewBtn.innerHTML = isVisible ?
                '<i class="bi bi-pencil-square"></i> Add Review' :
                '<i class="bi bi-x"></i> Cancel';
        }

        function handleSubmitReview() {
            const rating = document.querySelector('input[name="new-rating"]:checked');
            const reviewText = document.getElementById('reviewText').value.trim();

            if (!rating) {
                domElements.ratingError.style.display = 'block';
                return;
            } else {
                domElements.ratingError.style.display = 'none';
            }

            if (!reviewText) {
                domElements.reviewTextError.style.display = 'block';
                return;
            } else {
                domElements.reviewTextError.style.display = 'none';
            }

            createNewReview(config.currentUser, rating.value, reviewText);
            document.getElementById('reviewText').value = '';
            document.querySelectorAll('input[name="new-rating"]').forEach(radio => radio.checked = false);
            domElements.reviewTextarea.style.display = 'none';
            domElements.toggleReviewBtn.innerHTML = '<i class="bi bi-pencil-square"></i> Add Review';
        }

        function handleModalLogin() {
            const email = domElements.loginEmail.value;
            const password = domElements.loginPassword.value;

            if (!email || !password) {
                showAlert('Please enter both email and password', 'error');
                return;
            }

            simulateLogin();
            if (domElements.loginModal) domElements.loginModal.hide();
            showAlert('Logged in successfully!', 'success');
        }

        function handleReviewActions(e) {
            const reviewItem = e.target.closest('.review-item');
            const replyItem = e.target.closest('.reply-item');

            if (e.target.classList.contains('edit-review-btn')) {
                toggleEditForm(reviewItem);
            } else if (e.target.classList.contains('save-edit-btn')) {
                saveEditedReview(reviewItem);
            } else if (e.target.classList.contains('cancel-edit-btn')) {
                closeEditForm(reviewItem);
            } else if (e.target.closest('.star-rating-edit label')) {
                const label = e.target.closest('.star-rating-edit label');
                const rating = label.htmlFor.replace('edit-star', '').split('-')[0];
                highlightEditStars(reviewItem, rating);
            } else if (e.target.classList.contains('edit-reply-btn')) {
                toggleEditReplyForm(replyItem);
            } else if (e.target.classList.contains('save-reply-btn')) {
                saveEditedReply(replyItem);
            } else if (e.target.classList.contains('delete-reply-btn')) {
                if (confirm('Are you sure you want to delete this reply?')) {
                    deleteReply(replyItem);
                }
            } else if (e.target.classList.contains('cancel-reply-btn')) {
                closeEditReplyForm(replyItem);
            }
        }

        // Review management functions
        function createNewReview(userId, rating, text) {
            const newReview = {
                id: Date.now(),
                userId: userId,
                userName: 'You',
                rating: parseInt(rating),
                text: text,
                date: 'Just now',
                replies: []
            };

            config.reviewData.reviews.unshift(newReview);
            config.reviewData.totalReviews++;
            renderReviews();
            updateRatingDisplay();
            showAlert('Review submitted successfully!', 'success');
        }

        function toggleEditForm(reviewItem) {
            const reviewId = reviewItem.dataset.reviewId;
            const editForm = reviewItem.querySelector('.edit-review-form');
            editForm.style.display = editForm.style.display === 'block' ? 'none' : 'block';

            if (editForm.style.display === 'block') {
                const review = findReviewById(reviewId);
                if (review) {
                    editForm.querySelector('textarea').value = review.text;
                    editForm.querySelector(`input[value="${review.rating}"]`).checked = true;
                    highlightEditStars(reviewItem, review.rating);
                }
            }
        }

        function saveEditedReview(reviewItem) {
            const reviewId = reviewItem.dataset.reviewId;
            const editForm = reviewItem.querySelector('.edit-review-form');
            const textarea = editForm.querySelector('textarea');
            const ratingInput = editForm.querySelector('input[name^="edit-rating"]:checked');

            if (!ratingInput || textarea.value.trim() === '') {
                showAlert('Please complete both rating and review text', 'error');
                return;
            }

            const review = findReviewById(reviewId);
            if (review) {
                review.rating = parseInt(ratingInput.value);
                review.text = textarea.value.trim();
                review.date = 'Edited ' + formatDate(new Date());

                renderReviews();
                updateRatingDisplay();
                closeEditForm(reviewItem);
                showAlert('Review updated successfully!', 'success');
            }
        }

        function toggleEditReplyForm(replyItem) {
            const replyActions = replyItem.querySelector('.reply-actions');
            const isEditing = replyActions.style.display === 'block';

            if (isEditing) {
                replyActions.style.display = 'none';
            } else {
                const currentText = replyItem.querySelector('p').textContent;
                replyActions.querySelector('textarea').value = currentText;
                replyActions.style.display = 'block';
            }
        }

        function saveEditedReply(replyItem) {
            const replyId = replyItem.dataset.replyId;
            const newText = replyItem.querySelector('.reply-actions textarea').value.trim();

            if (!newText) {
                showAlert('Please enter your reply text', 'error');
                return;
            }

            const reply = findReplyById(replyId);
            if (reply) {
                reply.text = newText;
                reply.date = 'Edited ' + formatDate(new Date());
                renderReviews();
                closeEditReplyForm(replyItem);
                showAlert('Reply updated successfully!', 'success');
            }
        }

        function deleteReply(replyItem) {
            const replyId = replyItem.dataset.replyId;
            config.reviewData.reviews.forEach(review => {
                review.replies = review.replies.filter(reply => reply.id != replyId);
            });
            renderReviews();
            showAlert('Reply deleted successfully!', 'success');
        }

        // Helper functions
        function findReviewById(id) {
            return config.reviewData.reviews.find(review => review.id == id);
        }

        function findReplyById(id) {
            for (const review of config.reviewData.reviews) {
                const reply = review.replies.find(r => r.id == id);
                if (reply) return reply;
            }
            return null;
        }

        function closeEditForm(reviewItem) {
            reviewItem.querySelector('.edit-review-form').style.display = 'none';
        }

        function closeEditReplyForm(replyItem) {
            replyItem.querySelector('.reply-actions').style.display = 'none';
        }

        function highlightEditStars(reviewItem, rating) {
            const stars = reviewItem.querySelectorAll('.star-rating-edit label i');
            stars.forEach((star, index) => {
                star.style.color = index < rating ? 'var(--cl--primary)' : 'rgba(193, 39, 45, 0.1)';
            });
        }

        function simulateLogin() {
            config.currentUser = 'user123';
            config.isBusinessOwner = false;
            renderReviews();
        }

        function updateRatingDisplay() {
            if (domElements.averageRating) {
                domElements.averageRating.textContent = config.reviewData.averageRating.toFixed(1);
            }
            if (domElements.reviewCount) {
                domElements.reviewCount.textContent = `(${config.reviewData.totalReviews} reviews)`;
            }
        }

        function renderReviews() {
            if (!domElements.reviewsContainer) return;

            domElements.reviewsContainer.innerHTML = '';

            config.reviewData.reviews.forEach(review => {
                const reviewElement = document.createElement('div');
                reviewElement.className = 'review-item mb-4';
                reviewElement.dataset.userId = review.userId;
                reviewElement.dataset.reviewId = review.id;

                const starsHtml = Array(5).fill(0).map((_, i) =>
                    `<i class="bi bi-star-fill ${i < review.rating ? 'text-warning' : 'text-muted'}"></i>`
                ).join('');

                let reviewHtml = `
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <img src="./kaha6_about.png" class="rounded-circle" width="40" height="40" alt="User">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex align-items-center mb-1">
                                <h5 class="mb-0 me-2">${review.userName}</h5>
                                <small class="text-muted">${review.date}</small>
                            </div>
                            <div class="star-rating-display mb-2">
                                ${starsHtml}
                            </div>
                            <p class="mb-2">${review.text}</p>
                `;

                if (config.currentUser && review.userId === config.currentUser) {
                    reviewHtml += `
                        <div class="review-actions mt-2">
                            <button class="btn btn-sm btn-outline-secondary edit-review-btn">Edit</button>
                            <div class="edit-review-form" style="display: none;">
                                <div class="star-rating-edit mb-2">
                                    ${[5, 4, 3, 2, 1].map(r => `
                                        <input type="radio" id="edit-star${r}-${review.id}" name="edit-rating-${review.id}" value="${r}">
                                        <label for="edit-star${r}-${review.id}"><i class="bi bi-star-fill"></i></label>
                                    `).join('')}
                                </div>
                                <textarea class="form-control mb-2" rows="3">${review.text}</textarea>
                                <button class="btn btn-sm btn-custom-red save-edit-btn">Save Changes</button>
                                <button class="btn btn-sm btn-outline-secondary cancel-edit-btn ms-2">Cancel</button>
                            </div>
                        </div>
                    `;
                }

                reviewHtml += `</div></div>`;

                if (review.replies.length > 0) {
                    reviewHtml += `<div class="replies mt-3 ps-3 border-start">`;

                    review.replies.forEach(reply => {
                        const isOwnerReply = config.isBusinessOwner && reply.userId === config.currentUser;

                        reviewHtml += `
                            <div class="reply-item mb-3" data-user-id="${reply.userId}" data-reply-id="${reply.id}">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="./kaha6_about.png" class="rounded-circle" width="30" height="30" alt="User">
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <div class="d-flex align-items-center mb-1">
                                            <h6 class="mb-0 me-2">${reply.userName}</h6>
                                            <small class="text-muted">${reply.date}</small>
                                        </div>
                                        <p class="mb-0 small">${reply.text}</p>
                        `;

                        if (isOwnerReply) {
                            reviewHtml += `
                                <div class="reply-actions mt-1" style="display: none;">
                                    <textarea class="form-control mb-2 mt-2" rows="2">${reply.text}</textarea>
                                    <button class="btn btn-sm btn-custom-red save-reply-btn">Save</button>
                                    <button class="btn btn-sm btn-outline-danger delete-reply-btn ms-2">Delete</button>
                                    <button class="btn btn-sm btn-outline-secondary cancel-reply-btn ms-2">Cancel</button>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary edit-reply-btn mt-1">Edit</button>
                            `;
                        }

                        reviewHtml += `</div></div></div>`;
                    });

                    reviewHtml += `</div>`;
                }

                reviewElement.innerHTML = reviewHtml;
                domElements.reviewsContainer.appendChild(reviewElement);
            });
        }

        function formatDate(date) {
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 3000);
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();

    // Login/Register functionality
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
        setupPasswordToggle('toggleLoginPassword', 'loginPassword');
        setupPasswordToggle('toggleRegisterPassword', 'registerPassword');
        setupPasswordToggle('toggleRegisterPasswordConfirm', 'registerPasswordConfirm');

        // Switch to login tab from register link
        const switchToLogin = document.getElementById('switchToLogin');
        if (switchToLogin) {
            switchToLogin.addEventListener('click', function (e) {
                e.preventDefault();
                const loginTab = new bootstrap.Tab(document.getElementById('login-tab'));
                loginTab.show();
            });
        }

        // Form submission
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function (e) {
                e.preventDefault();
                console.log('Login submitted');
            });
        }

        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function (e) {
                e.preventDefault();
                console.log('Register submitted');
            });
        }
    }

    // Home Page Popup
    function setupHomePopup() {
        const homepageModalEl = document.getElementById('homepageModal');
        if (homepageModalEl) {
            const homepageModal = new bootstrap.Modal(homepageModalEl);
            homepageModal.show();
        }
    }

    // Initialize all functionality when DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {
        setupMobileMenu();
        initializeSliders();
        setupCategoryFilter();
        setupAuthForms();
        setupHomePopup();
    });

})();