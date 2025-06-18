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
                                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <div class="row g-4 mb-5">
                            <!-- Listing Card -->
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="card stat-card border-0 itsn-shadow">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fs-3 text-primary fa-solid fa-list-check"></i>
                                        </div>
                                        <h6 class="text-muted mb-2">Total Listing</h6>
                                        <h4 class="mb-3">
                                            <?php
                                            $current_user_id = get_current_user_id();
                                            $args = array(
                                                'post_type'      => 'ait-item',
                                                'post_status'    => 'publish',
                                                'author'         => $current_user_id,
                                                'posts_per_page' => -1,
                                            );
                                            $user_items = new WP_Query($args);
                                            echo $user_items->found_posts;
                                            ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <?php /* 
                            <!-- Visit Count Card -->
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="card stat-card border-0 itsn-shadow">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fs-3 text-success fa-solid fa-eye"></i>
                                        </div>
                                        <h6 class="text-muted mb-2">Total Visit</h6>
                                        <h4 class="mb-3">
                                            <?php
                                            $current_user_id = get_current_user_id();
                                            $args = array(
                                                'post_type'      => 'ait-item',
                                                'post_status'    => 'publish',
                                                'author'         => $current_user_id,
                                                'posts_per_page' => -1,
                                            );
                                            $user_items = new WP_Query($args);
                                            echo $user_items->found_posts;
                                            ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <!-- Listing Card -->
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="card stat-card border-0 itsn-shadow">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fs-3 text-primary fa-solid fa-list-check"></i>
                                        </div>
                                        <h6 class="text-muted mb-2">Total Listing</h6>
                                        <h4 class="mb-3">
                                            <?php
                                            $current_user_id = get_current_user_id();
                                            $args = array(
                                                'post_type'      => 'ait-item',
                                                'post_status'    => 'publish',
                                                'author'         => $current_user_id,
                                                'posts_per_page' => -1,
                                            );
                                            $user_items = new WP_Query($args);
                                            echo $user_items->found_posts;
                                            ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <!-- Visit Count Card -->
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="card stat-card border-0 itsn-shadow">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fs-3 text-success fa-solid fa-eye"></i>
                                        </div>
                                        <h6 class="text-muted mb-2">Total Visit</h6>
                                        <h4 class="mb-3">
                                            <?php
                                            $current_user_id = get_current_user_id();
                                            $args = array(
                                                'post_type'      => 'ait-item',
                                                'post_status'    => 'publish',
                                                'author'         => $current_user_id,
                                                'posts_per_page' => -1,
                                            );
                                            $user_items = new WP_Query($args);
                                            echo $user_items->found_posts;
                                            ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            */ ?>
                        </div>

                        <div class="row row-cols-1 g-3 px-2 justify-content-center mb-5" id="business-listing">
                            <?php
                            $args = array(
                                'post_type'      => 'ait-item',
                                'posts_per_page' => 3,
                                'author'         => get_current_user_id(),
                                'post_status' => 'any',
                            );



                            $user_listings = new WP_Query($args);

                            if ($user_listings->have_posts()) :
                                while ($user_listings->have_posts()) : $user_listings->the_post();
                                    $ID = get_the_ID();
                                    $metadata = get_post_meta(get_the_ID(), '_ait-item_item-data', false);
                                    // $address = $metadata['0']['map']['address'];
                                    if (has_post_thumbnail($ID)) {
                                        $img = get_the_post_thumbnail_url($ID, 'thumbnail');
                                    } else {
                                        $img = get_template_directory_uri() . '/assets/images/banner.png';
                                    }
                                    $content = get_the_content();
                                    $post_status = get_post_status($ID);
                                    $claim_status = get_post_meta($ID, '_bcv_claim_status', true) ?: 'not_claimed';
                                    $verification_status = get_post_meta($ID, '_bcv_verification_status', true) ?: 'not_verified';

                            ?>
                                    <div class="itsn-shadow rounded p-2 h-100 position-relative">
                                        <!-- Claim and Status Badges Start -->
                                        <?php get_template_part('parts/common/status', 'badges'); ?>
                                        <!-- Claim and Status Badges End -->
                                        <div class="d-flex flex-column flex-lg-row gap-2">
                                            <?php if ($post_status == 'draft') : ?>
                                                <div class="d-flex justify-content-center">
                                                    <p class="badge fw-normal bg-warning border-0 fs-7 text-decoration-none mb-0">Your listing is under review.</p>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($claim_status !== 'claimed') : ?>
                                                <div class="d-flex justify-content-center">
                                                    <p class="badge fw-normal bg-warning border-0 fs-7 text-decoration-none mb-0">Claim your business to make changes.</p>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($verification_status !== 'verified') : ?>
                                                <div class="d-flex justify-content-center">
                                                    <p class="badge fw-normal bg-warning border-0 fs-7 text-decoration-none mb-0">Must be verified to delete business.</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="row g-0">
                                            <div class="col-4 d-flex justify-content-center align-items-center p-2">
                                                <a href="<?php echo ($post_status !== 'draft') ? get_permalink() : '#'; ?>">
                                                    <img class=" itsn-listing-img rounded img-fluid" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                                </a>
                                            </div>
                                            <div class="col-8 py-2 d-flex flex-column justify-content-center">
                                                <div>
                                                    <!-- Star Rating Display Start -->
                                                    <?php get_template_part('parts/common/star', 'rating'); ?>
                                                    <!-- Star Rating Display End -->
                                                    <a href="<?php echo ($post_status !== 'draft') ? get_permalink() : '#'; ?>">
                                                        <h6 class=" text-capitalize mb-1 overflow-hidden d-flex align-items-center gap-1">
                                                            <?php echo wp_trim_words(get_the_title(), 5, '...'); ?>
                                                            <?php get_template_part('parts/common/verification', 'badge'); ?>
                                                        </h6>
                                                    </a>
                                                    <p class="fs-7 lh-base mb-2 overflow-hidden"><?php echo esc_html(wp_trim_words($content, 12, '...')); ?></p>
                                                </div>

                                                <?php if ($post_status !== 'draft') : ?>
                                                    <?php if (!in_array($post_status, array('under_reverification', 'banned'))) : ?>
                                                        <?php if ($claim_status === 'claimed'): ?>
                                                            <hr class="my-2">
                                                        <?php endif; ?>
                                                        <div class="d-flex justify-content-end gap-3">
                                                            <?php if ($claim_status === 'claimed'): ?>
                                                                <div>
                                                                    <a href="<?php echo home_url('/register-to-submit-your-company/') ?>?edit_id=<?php echo get_the_ID(); ?>" class="btn text-success border-0" data-bs-toggle="tooltip" data-bs-title="Edit">
                                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                                    </a>
                                                                    <!-- No delete unless you are verified -->
                                                                    <?php
                                                                    $post_id = get_the_ID();
                                                                    $verification_status = get_post_meta($post_id, '_bcv_verification_status', true) ?: 'not_verified';
                                                                    if ($verification_status === 'verified') {
                                                                    ?>
                                                                        <a href="<?php echo esc_url(add_query_arg(array('action' => 'delete_ait_item', 'post_id' => $ID, 'nonce' => wp_create_nonce('delete_ait_item_' . $ID)), home_url())); ?>" class="btn text-primary border-0" onclick="return confirm('Are you sure you want to delete this listing?');" data-bs-toggle="tooltip" data-bs-title="Delete">
                                                                            <i class="fa-solid fa-trash"></i>
                                                                        </a>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                            <?php
                                endwhile;
                                wp_reset_postdata();
                            else :
                                echo '<p class="text-center lh-lg fs-6 py-4">No listings found.</p>';
                            endif;
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer() ?>