<?php include 'header.php'; ?>

<!-- Header Section -->
<?php include 'parts/common/header-section.php' ?>

<!-- Registration Form -->
<section class="container mb-5">
    <div class="registration-container shadow-sm border-0 text-decoration-none rounded-4 p-4">
        <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3">Company Registration Form</h2>
        <form class="mt-5 needs-validation" id="businessRegistrationForm" novalidate>
            <!-- General Information Section -->
            <div class="section" id="general-info">
                <h3 class="fs-5 fw-bold">General Information</h3>
                <div class="form-grid">
                    <div class="row">
                        <div class="col-md-6 form-item">
                            <label for="businessName" class="form-label mb-1">Business Name *</label>
                            <input type="text" class="form-control" id="businessName"
                                placeholder="Enter your business name" included>
                            <div class="invalid-feedback">Please provide a business name.</div>
                        </div>
                        <div class="col-md-6 form-item">
                            <label for="subTitle" class="form-label mb-1">Sub Title</label>
                            <input type="text" class="form-control" id="subTitle" placeholder="Business subtitle">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-item">
                            <label for="businessCategory" class="form-label mb-1">Business Category *</label>
                            <select class="form-select" id="businessCategory" included>
                                <option value="" selected disabled>Select category</option>
                                <option>Restaurant</option>
                                <option>Retail</option>
                                <option>Service</option>
                                <option>Manufacturing</option>
                                <option>Other</option>
                            </select>
                            <div class="invalid-feedback">Please select a business category.</div>
                        </div>
                        <div class="col-md-6 form-item">
                            <label for="businessLogo" class="form-label mb-1">Business Logo</label>
                            <img id="logoPreview" class="logo-preview" alt="Logo preview">
                            <input type="file" class="form-control" id="businessLogo" accept="image/*"
                                onchange="previewLogo(this)">
                            <p class="form-note">JPG/PNG, &lt; 500KB</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="section" id="address-info">
                <h3 class="fs-5 fw-bold">Address Information</h3>
                <div class="form-grid">
                    <div class="row">
                        <div class="col-md-6 form-item">
                            <label for="streetAddress" class="form-label mb-1">Street Address *</label>
                            <input type="text" class="form-control" id="streetAddress"
                                placeholder="Enter street address" included>
                            <div class="invalid-feedback">Please provide a street address.</div>
                        </div>
                        <div class="col-md-6 form-item">
                            <label for="city" class="form-label mb-1">City *</label>
                            <input type="text" class="form-control" id="city" placeholder="Enter city" included>
                            <div class="invalid-feedback">Please provide a city.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-item">
                            <label for="province" class="form-label mb-1">Province</label>
                            <input type="text" class="form-control" id="province" placeholder="Enter province">
                        </div>
                        <div class="col-md-6 form-item">
                            <label for="postalCode" class="form-label mb-1">Postal Code</label>
                            <input type="text" class="form-control" id="postalCode" placeholder="Enter postal code">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="section" id="contact-info">
                <h3 class="fs-5 fw-bold">Contact Information</h3>
                <div class="form-grid">
                    <!-- Phone Numbers -->
                    <div class="row" id="contactFields">
                        <div class="col-md-6 form-item">
                            <div class="contact-item">
                                <button type="button" class="remove-btn" onclick="removeField(this)"
                                    aria-label="Remove phone number">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                <label class="form-label mb-1">Phone *</label>
                                <input type="tel" class="form-control" name="phone[]"
                                    placeholder="Enter phone number" included pattern="[0-9]{10,15}">
                                <div class="invalid-feedback">Please provide a valid phone number (10-15 digits).
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-custom-red add-more-btn" onclick="addContactField()"
                        id="addPhoneBtn">
                        <i class="bi bi-plus"></i> Add Another Phone Number
                    </button>

                    <!-- Emails -->
                    <div class="row" id="emailFields">
                        <div class="col-md-6 form-item">
                            <div class="email-item">
                                <button type="button" class="remove-btn" onclick="removeField(this)"
                                    aria-label="Remove email">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                <label class="form-label mb-1">Email *</label>
                                <input type="email" class="form-control" name="email[]"
                                    placeholder="Enter email address" included>
                                <div class="invalid-feedback">Please provide a valid email address.</div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-custom-red add-more-btn" onclick="addEmailField()"
                        id="addEmailBtn">
                        <i class="bi bi-plus"></i> Add Another Email
                    </button>
                </div>
            </div>

            <!-- Opening Hours -->
            <div class="section" id="opening-hours">
                <h3 class="fs-5 fw-bold mb-4">Opening Hours</h3>

                <div class="my-3">
                    <label for="openingNote" class="form-label">Opening Hour Note</label>
                    <input type="text" class="form-control" id="openingNote"
                        placeholder="E.g., Closed on public holidays">
                </div>

                <div class="row g-3" id="openingHoursGrid">
                    <!-- Days will be added dynamically here -->
                </div>
            </div>

            <!-- Social Profiles -->
            <div class="section" id="social-profiles">
                <h3 class="fs-5 fw-bold">Social Profiles</h3>
                <div class="form-grid">
                    <div class="row" id="socialFields">
                        <div class="col-md-6 form-item">
                            <div class="social-item">
                                <button type="button" class="remove-btn" onclick="removeField(this)"
                                    aria-label="Remove social media">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                <label class="form-label mb-1">Social Media</label>
                                <select class="form-select mb-2" name="socialMediaType[]">
                                    <option selected disabled>Select platform</option>
                                    <option>Facebook</option>
                                    <option>Instagram</option>
                                    <option>Twitter</option>
                                    <option>LinkedIn</option>
                                    <option>YouTube</option>
                                </select>
                                <input type="url" class="form-control" name="socialMediaUrl[]"
                                    placeholder="Enter profile URL" pattern="https?://.+">
                                <div class="invalid-feedback">Please provide a valid URL (starting with http:// or
                                    https://).</div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-custom-red add-more-btn" onclick="addSocialField()"
                        id="addSocialBtn">
                        <i class="bi bi-plus"></i> Add Another Social Media
                    </button>
                </div>
            </div>

            <!-- Gallery -->
            <div class="section" id="business-gallery">
                <h3 class="fs-5 fw-bold">Gallery (Max 5 images)</h3>
                <div class="form-grid">
                    <div class="row" id="galleryFields">
                        <div class="col-md-4 form-item">
                            <div class="gallery-item">
                                <button type="button" class="remove-btn" onclick="removeField(this)"
                                    aria-label="Remove image">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                <label class="form-label mb-1">Image Title</label>
                                <input type="text" class="form-control mb-2" name="galleryTitle[]"
                                    placeholder="Image title">
                                <img class="gallery-preview" alt="Gallery preview" style="display: none;">
                                <input type="file" class="form-control" name="galleryImage[]" accept="image/*"
                                    onchange="previewImage(this)">
                                <p class="form-note">JPG/PNG, &lt; 500KB</p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-custom-red add-more-btn" onclick="addGalleryField()"
                        id="addGalleryBtn">
                        <i class="bi bi-plus"></i> Add Another Image
                    </button>
                </div>
            </div>

            <!-- Terms and Submit -->
            <div class="mt-4">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="termsCheck" included>
                    <label class="form-check-label" for="termsCheck">
                        I agree to the <a href="#" class="d-inline-block" data-bs-toggle="modal"
                            data-bs-target="#termsModal">Terms and
                            Conditions</a> *
                    </label>
                    <div class="invalid-feedback">You must agree to the terms and conditions.</div>
                </div>
                <button type="submit" class="btn btn-custom-red submit-btn">
                    <span id="submitText">Submit Business</span>
                    <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status"
                        aria-hidden="true"></span>
                </button>
            </div>
        </form>
        <!-- Notification Container -->
        <div class="notification-container" id="notificationContainer"></div>
    </div>
</section>

<!-- Terms Modal -->
<?php include 'parts/business-register/modal.php' ?>

<?php include 'footer.php'; ?>