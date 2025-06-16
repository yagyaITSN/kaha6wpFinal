<?php include 'header.php'; ?>

<!-- Hero Section -->
<?php include 'parts/common/hero-section.php' ?>
<!-- End Hero Section -->

<!-- Heading Section -->
<?php include 'parts/common/header-section.php' ?>
<!-- End Heading Section -->

<!-- Banner  -->
<?php include 'ads/category/category-ad-one.php' ?>
<!-- End Banner  -->

<!-- Category Cards -->
<section class="category-section py-5">
    <div class="container">
        <div class="category-section-title d-flex align-items-center justify-content-between gap-3">
            <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 m-0">Related Searches</h2>
            <!-- Filter -->
            <div class="category_filter">
                <select id="ratingFilter">
                    <option value="all">All Ratings</option>
                    <option value="asc">Low to High</option>
                    <option value="desc">High to Low</option>
                </select>
            </div>
        </div>
        <div class="category-cards mt-5" id="businessCards">
            <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden"
                data-rating="4.8">
                <img class="card-img-top pt-2" src="https://kaha6.com/wp-content/uploads/bhc-no-bg-circle-logo.png"
                    alt="Birgunj Health Care hospital">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title text-dark text-capitalize">Birgunj Health Care Hospital
                    </h5>
                    <div class="d-flex justify-content-between align-items-center mb-2 card-location_rate">
                        <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Birgunj
                        </p>
                        <div class="d-flex align-items-center">
                            <span class="text-warning me-1">★</span>
                            <span class="text-dark">4.8</span>
                            <span class="text-muted ms-1">(234 reviews)</span>
                        </div>
                    </div>
                    <div class="card-tags d-flex flex-wrap">
                        <span class="badge text-dark me-2 text-capitalize">province-no-1</span>
                        <span class="badge text-dark me-2 text-capitalize">Medical</span>
                        <span class="badge text-dark text-capitalize">Hospital</span>
                    </div>
                </div>
                <div class="category-btn position-absolute d-flex align-items-center justify-content-center">
                    <a class="btn btn-custom-red" href="./single_page.html">View</a>
                </div>
            </div>
            <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden"
                data-rating="4.8">
                <img class="card-img-top pt-2" src="https://kaha6.com/wp-content/uploads/bhc-no-bg-circle-logo.png"
                    alt="Birgunj Health Care hospital">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title text-dark text-capitalize">Birgunj Health Care Hospital
                    </h5>
                    <div class="d-flex justify-content-between align-items-center mb-2 card-location_rate">
                        <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Birgunj
                        </p>
                        <div class="d-flex align-items-center">
                            <span class="text-warning me-1">★</span>
                            <span class="text-dark">4.8</span>
                            <span class="text-muted ms-1">(234 reviews)</span>
                        </div>
                    </div>
                    <div class="card-tags d-flex flex-wrap">
                        <span class="badge text-dark me-2 text-capitalize">province-no-1</span>
                        <span class="badge text-dark me-2 text-capitalize">Medical</span>
                        <span class="badge text-dark text-capitalize">Hospital</span>
                    </div>
                </div>
                <div class="category-btn position-absolute d-flex align-items-center justify-content-center">
                    <a class="btn btn-custom-red" href="./single.php">View</a>
                </div>
            </div>
            <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden"
                data-rating="4.0">
                <img class="card-img-top pt-2" src="https://kaha6.com/wp-content/uploads/bhc-no-bg-circle-logo.png"
                    alt="Birgunj Health Care hospital">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title text-dark text-capitalize">Kathmandu Medical Center
                    </h5>
                    <div class="d-flex justify-content-between align-items-center mb-2 card-location_rate">
                        <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Kathmandu
                        </p>
                        <div class="d-flex align-items-center">
                            <span class="text-warning me-1">★</span>
                            <span class="text-dark">4.0</span>
                            <span class="text-muted ms-1">(189 reviews)</span>
                        </div>
                    </div>
                    <div class="card-tags d-flex flex-wrap">
                        <span class="badge text-dark me-2 text-capitalize">province-no-3</span>
                        <span class="badge text-dark me-2 text-capitalize">Medical</span>
                        <span class="badge text-dark text-capitalize">Clinic</span>
                    </div>
                </div>
                <div class="category-btn position-absolute d-flex align-items-center justify-content-center">
                    <a class="btn btn-custom-red" href="./single.php">View</a>
                </div>
            </div>
            <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden"
                data-rating="2.8">
                <img class="card-img-top pt-2" src="https://kaha6.com/wp-content/uploads/bhc-no-bg-circle-logo.png"
                    alt="Birgunj Health Care hospital">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title text-dark text-capitalize">Pokhara General Hospital
                    </h5>
                    <div class="d-flex justify-content-between align-items-center mb-2 card-location_rate">
                        <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Pokhara
                        </p>
                        <div class="d-flex align-items-center">
                            <span class="text-warning me-1">★</span>
                            <span class="text-dark">2.8</span>
                            <span class="text-muted ms-1">(76 reviews)</span>
                        </div>
                    </div>
                    <div class="card-tags d-flex flex-wrap">
                        <span class="badge text-dark me-2 text-capitalize">province-no-4</span>
                        <span class="badge text-dark me-2 text-capitalize">Medical</span>
                        <span class="badge text-dark text-capitalize">Hospital</span>
                    </div>
                </div>
                <div class="category-btn position-absolute d-flex align-items-center justify-content-center">
                    <a class="btn btn-custom-red" href="./single.php">View</a>
                </div>
            </div>
            <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden"
                data-rating="3.5">
                <img class="card-img-top pt-2" src="https://kaha6.com/wp-content/uploads/bhc-no-bg-circle-logo.png"
                    alt="Birgunj Health Care hospital">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title text-dark text-capitalize">Chitwan Medical College
                    </h5>
                    <div class="d-flex justify-content-between align-items-center mb-2 card-location_rate">
                        <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Chitwan
                        </p>
                        <div class="d-flex align-items-center">
                            <span class="text-warning me-1">★</span>
                            <span class="text-dark">3.5</span>
                            <span class="text-muted ms-1">(142 reviews)</span>
                        </div>
                    </div>
                    <div class="card-tags d-flex flex-wrap">
                        <span class="badge text-dark me-2 text-capitalize">province-no-2</span>
                        <span class="badge text-dark me-2 text-capitalize">Medical</span>
                        <span class="badge text-dark text-capitalize">College</span>
                    </div>
                </div>
                <div class="category-btn position-absolute d-flex align-items-center justify-content-center">
                    <a class="btn btn-custom-red" href="./single.php">View</a>
                </div>
            </div>
            <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden"
                data-rating="4.2">
                <img class="card-img-top pt-2" src="https://kaha6.com/wp-content/uploads/bhc-no-bg-circle-logo.png"
                    alt="Birgunj Health Care hospital">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title text-dark text-capitalize">Dharan Dental Clinic
                    </h5>
                    <div class="d-flex justify-content-between align-items-center mb-2 card-location_rate">
                        <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Dharan
                        </p>
                        <div class="d-flex align-items-center">
                            <span class="text-warning me-1">★</span>
                            <span class="text-dark">4.2</span>
                            <span class="text-muted ms-1">(98 reviews)</span>
                        </div>
                    </div>
                    <div class="card-tags d-flex flex-wrap">
                        <span class="badge text-dark me-2 text-capitalize">province-no-1</span>
                        <span class="badge text-dark me-2 text-capitalize">Dental</span>
                        <span class="badge text-dark text-capitalize">Clinic</span>
                    </div>
                </div>
                <div class="category-btn position-absolute d-flex align-items-center justify-content-center">
                    <a class="btn btn-custom-red" href="./single.php">View</a>
                </div>
            </div>
            <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden"
                data-rating="3.9">
                <img class="card-img-top pt-2" src="https://kaha6.com/wp-content/uploads/bhc-no-bg-circle-logo.png"
                    alt="Birgunj Health Care hospital">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title text-dark text-capitalize">Butwal Eye Hospital
                    </h5>
                    <div class="d-flex justify-content-between align-items-center mb-2 card-location_rate">
                        <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Butwal
                        </p>
                        <div class="d-flex align-items-center">
                            <span class="text-warning me-1">★</span>
                            <span class="text-dark">3.9</span>
                            <span class="text-muted ms-1">(112 reviews)</span>
                        </div>
                    </div>
                    <div class="card-tags d-flex flex-wrap">
                        <span class="badge text-dark me-2 text-capitalize">province-no-5</span>
                        <span class="badge text-dark me-2 text-capitalize">Eye Care</span>
                        <span class="badge text-dark text-capitalize">Hospital</span>
                    </div>
                </div>
                <div class="category-btn position-absolute d-flex align-items-center justify-content-center">
                    <a class="btn btn-custom-red" href="./single.php">View</a>
                </div>
            </div>
            <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden"
                data-rating="4.5">
                <img class="card-img-top pt-2" src="https://kaha6.com/wp-content/uploads/bhc-no-bg-circle-logo.png"
                    alt="Birgunj Health Care hospital">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title text-dark text-capitalize">Nepalgunj Medical College
                    </h5>
                    <div class="d-flex justify-content-between align-items-center mb-2 card-location_rate">
                        <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Nepalgunj
                        </p>
                        <div class="d-flex align-items-center">
                            <span class="text-warning me-1">★</span>
                            <span class="text-dark">4.5</span>
                            <span class="text-muted ms-1">(201 reviews)</span>
                        </div>
                    </div>
                    <div class="card-tags d-flex flex-wrap">
                        <span class="badge text-dark me-2 text-capitalize">province-no-5</span>
                        <span class="badge text-dark me-2 text-capitalize">Medical</span>
                        <span class="badge text-dark text-capitalize">College</span>
                    </div>
                </div>
                <div class="category-btn position-absolute d-flex align-items-center justify-content-center">
                    <a class="btn btn-custom-red" href="./single.php">View</a>
                </div>
            </div>
        </div>
        <div class="card-not-found text-center d-none" id="noResults">
            <h5 class="fs-5 fw-bold">Business not found</h5>
            <p>Try another options</p>
        </div>
        <div class="card-pagination" id="pagination">
        </div>
    </div>
</section>
<!-- End Category Cards -->

<!-- Banner -->
<?php include 'ads/category/category-ad-two.php' ?>
<!-- End Banner -->

<!-- Top Cards -->
<?php include 'parts/home/business-slider-two.php' ?>
<!-- End Top Cards -->

<?php include 'footer.php'; ?>