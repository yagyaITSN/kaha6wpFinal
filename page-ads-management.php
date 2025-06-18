<?php get_header(); ?>

<section class="container py-5">
    <div class="col-12">
        <div class="card border-0 itsn-shadow">
            <div class="card-body p-0">
                <div class="row g-0">

                    <!-- Business Details Start -->
                    <?php get_template_part('parts/dashboard/pg', 'offcanvas'); ?>
                    <!-- Business Details End -->

                    <!-- Content Area -->
                    <div class="col-lg-9 p-4 mb-">
                        <div class="py-4 d-md-flex justify-content-md-between align-items-md-center">
                            <h3 class="fw-bold text-capitalize"><?php echo wp_get_current_user()->display_name; ?>'s Dashboard</h3>
                            <div>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a class="text-custom-red" href="<?php echo home_url('/dashboard'); ?>">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Ad Management</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <?php get_template_part('parts/dashboard/ad', 'tabs'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer() ?>