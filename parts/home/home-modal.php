<section class="popup-section">
    <div class="modal fade custom-modal" id="homepageModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-4">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex justify-content-center align-items-center">
                    <div>
                        <h4 class="fs-4 text-center text-dark mb-4 text-capitalize">Find trusted businesses near you
                            with ease.</h4>
                        <?php get_template_part('parts/home/modal', 'form')
                        ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Premium Cards -->
                    <div class="featured-section container">
                        <h5 class="fs-5 fw-bold text-center mb-4">Featured Businesses</h5>
                        <?php get_template_part('parts/home/modal', 'slider'); ?>
                    </div>
                    <!-- End Premium Cards -->
                </div>
            </div>
        </div>
    </div>
</section>