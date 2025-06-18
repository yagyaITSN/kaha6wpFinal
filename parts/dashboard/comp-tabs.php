<div class="row row-cols-1 g-3 px-2 justify-content-center mb-5">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-tab-pane" type="button"
                role="tab" aria-controls="all-tab-pane" aria-selected="true"><i class="fa-solid fa-table-list"></i> All Companies</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-tab-pane" type="button"
                role="tab" aria-controls="pending-tab-pane" aria-selected="false"><i class="fa-solid fa-hourglass-half"></i> Pending Claims</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="reverification-tab" data-bs-toggle="tab" data-bs-target="#reverification-tab-pane" type="button"
                role="tab" aria-controls="reverification-tab-pane" aria-selected="false"><i class="fa-solid fa-check-double"></i> Company Reverification</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="locked-tab" data-bs-toggle="tab" data-bs-target="#locked-tab-pane" type="button"
                role="tab" aria-controls="locked-tab-pane" aria-selected="false"><i class="fa-solid fa-lock"></i> Company locked</button>
        </li>
    </ul>
    <div class="tab-content py-4" id="myTabContent">
        <!-- All Start -->
        <div class="tab-pane fade show active" id="all-tab-pane" role="tabpanel" aria-labelledby="all-tab" tabindex="0">
            <div class="row row-cols-1 g-3 px-2 justify-content-center mb-5" id="business-listing">
                <?php
                $args = array(
                    'post_type'      => 'ait-item',
                    'posts_per_page' => -1,
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
                                        <img class="itsn-listing-img rounded img-fluid" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    </a>
                                </div>
                                <div class="col-8 py-2 d-flex flex-column justify-content-center">
                                    <div>
                                        <!-- Star Rating Display Start -->
                                        <?php get_template_part('parts/common/star', 'rating'); ?>
                                        <!-- Star Rating Display End -->
                                        <a href="<?php echo ($post_status !== 'draft') ? get_permalink() : '#'; ?>">
                                            <h6 class="text-capitalize mb-1 overflow-hidden d-flex align-items-center gap-1">
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
                                                        <a href="<?php echo home_url('/register-to-submit-your-company/') ?>?edit_id=<?php echo get_the_ID(); ?>" class="btn text-success border-0">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                        <!-- No delete unless you are verified -->
                                                        <?php
                                                        $post_id = get_the_ID();
                                                        $verification_status = get_post_meta($post_id, '_bcv_verification_status', true) ?: 'not_verified';
                                                        if ($verification_status === 'verified') {
                                                        ?>
                                                            <a href="<?php echo esc_url(add_query_arg(array('action' => 'delete_ait_item', 'post_id' => $ID, 'nonce' => wp_create_nonce('delete_ait_item_' . $ID)), home_url())); ?>" class="btn text-primary border-0" onclick="return confirm('Are you sure you want to delete this listing?');">
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
        <!-- All End -->

        <!-- Pending Start -->
        <div class="tab-pane fade" id="pending-tab-pane" role="tabpanel" aria-labelledby="pending-tab" tabindex="0">
            <div class="row row-cols-1 g-3 px-2 justify-content-center mb-5" id="business-listing">
                <?php
                $args = array(
                    'post_type'      => 'ait-item',
                    'posts_per_page' => -1,
                    'author'         => get_current_user_id(),
                    'meta_query'     => array(
                        array(
                            'key'     => '_bcv_claim_status',
                            'value'   => 'not_claimed',
                            'compare' => '='
                        )
                    )
                );

                $user_listings = new WP_Query($args);

                if ($user_listings->have_posts()) :
                    while ($user_listings->have_posts()) : $user_listings->the_post();
                        $ID = get_the_ID();
                        $metadata = get_post_meta(get_the_ID(), '_ait-item_item-data', false);
                        $address = $metadata['0']['map']['address'];
                        if (has_post_thumbnail($ID)) {
                            $img = get_the_post_thumbnail_url($ID, 'thumbnail');
                        } else {
                            $img = get_template_directory_uri() . '/assets/images/banner.png';
                        }
                        $content = get_the_content();
                        $post_status = get_post_status($ID);
                ?>
                        <div class="itsn-shadow rounded p-2 h-100 position-relative">
                            <!-- Claim and Status Badges Start -->
                            <?php get_template_part('parts/common/status', 'badges'); ?>
                            <!-- Claim and Status Badges End -->

                            <div class="row g-0">
                                <div class="col-4 d-flex justify-content-center align-items-center p-2">
                                    <a href="<?php echo ($post_status !== 'draft') ? get_permalink() : '#'; ?>">
                                        <img class="itsn-listing-img rounded img-fluid" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    </a>
                                </div>
                                <div class="col-8 py-2 d-flex flex-column justify-content-center">
                                    <div>
                                        <!-- Star Rating Display Start -->
                                        <?php get_template_part('parts/common/star', 'rating'); ?>
                                        <!-- Star Rating Display End -->
                                        <a href="<?php echo ($post_status !== 'draft') ? get_permalink() : '#'; ?>">
                                            <h6 class="text-capitalize mb-1 overflow-hidden d-flex align-items-center gap-1">
                                                <?php echo wp_trim_words(get_the_title(), 5, '...'); ?>
                                                <?php get_template_part('parts/common/verification', 'badge'); ?>
                                            </h6>
                                        </a>
                                        <p class="fs-7 lh-base mb-2 overflow-hidden"><?php echo esc_html(wp_trim_words($content, 12, '...')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="text-center lh-lg fs-6 py-4">No Pending Claims found.</p>';
                endif;
                ?>
            </div>
        </div>
        <!-- Pending End -->

        <!-- Reverification Start -->
        <div class="tab-pane fade" id="reverification-tab-pane" role="tabpanel" aria-labelledby="reverification-tab" tabindex="0">
            <div class="row row-cols-1 g-3 px-2 justify-content-center mb-5" id="business-listing">
                <?php
                $args = array(
                    'post_type'      => 'ait-item',
                    'posts_per_page' => -1,
                    'author'         => get_current_user_id(),
                    'post_status' => array('under_reverification')
                );

                $user_listings = new WP_Query($args);

                if ($user_listings->have_posts()) :
                    while ($user_listings->have_posts()) : $user_listings->the_post();
                        $ID = get_the_ID();
                        $metadata = get_post_meta(get_the_ID(), '_ait-item_item-data', false);
                        $address = $metadata['0']['map']['address'];
                        if (has_post_thumbnail($ID)) {
                            $img = get_the_post_thumbnail_url($ID, 'thumbnail');
                        } else {
                            $img = get_template_directory_uri() . '/assets/images/banner.png';
                        }
                        $content = get_the_content();
                        $post_status = get_post_status($ID);
                ?>
                        <div class="itsn-shadow rounded p-2 h-100 position-relative">
                            <!-- Claim and Status Badges Start -->
                            <?php get_template_part('parts/common/status', 'badges'); ?>
                            <!-- Claim and Status Badges End -->
                            <div class="row g-0">
                                <div class="col-4 d-flex justify-content-center align-items-center p-2">
                                    <a href="<?php echo ($post_status !== 'draft') ? get_permalink() : '#'; ?>">
                                        <img class="itsn-listing-img rounded img-fluid" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    </a>
                                </div>
                                <div class="col-8 py-2 d-flex flex-column justify-content-center">
                                    <div>
                                        <!-- Star Rating Display Start -->
                                        <?php get_template_part('parts/common/star', 'rating'); ?>
                                        <!-- Star Rating Display End -->
                                        <a href="<?php echo ($post_status !== 'draft') ? get_permalink() : '#'; ?>">
                                            <h6 class="text-capitalize mb-1 overflow-hidden d-flex align-items-center gap-1">
                                                <?php echo wp_trim_words(get_the_title(), 5, '...'); ?>
                                                <?php get_template_part('parts/common/verification', 'badge'); ?>
                                            </h6>
                                        </a>
                                        <p class="fs-7 lh-base mb-2 overflow-hidden"><?php echo esc_html(wp_trim_words($content, 12, '...')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="text-center lh-lg fs-6 py-4">No Companies for Reverification.</p>';
                endif;
                ?>
            </div>
        </div>
        <!-- Reverification End -->

        <!-- Locked Start -->
        <div class="tab-pane fade" id="locked-tab-pane" role="tabpanel" aria-labelledby="locked-tab" tabindex="0">
            <div class="row row-cols-1 g-3 px-2 justify-content-center mb-5" id="business-listing">
                <?php
                $args = array(
                    'post_type'      => 'ait-item',
                    'posts_per_page' => -1,
                    'author'         => get_current_user_id(),
                    'post_status' => array('banned')
                );

                $user_listings = new WP_Query($args);

                if ($user_listings->have_posts()) :
                    while ($user_listings->have_posts()) : $user_listings->the_post();
                        $ID = get_the_ID();
                        $metadata = get_post_meta(get_the_ID(), '_ait-item_item-data', false);
                        $address = $metadata['0']['map']['address'];
                        if (has_post_thumbnail($ID)) {
                            $img = get_the_post_thumbnail_url($ID, 'thumbnail');
                        } else {
                            $img = get_template_directory_uri() . '/assets/images/banner.png';
                        }
                        $content = get_the_content();
                        $post_status = get_post_status($ID);
                ?>
                        <div class="itsn-shadow rounded p-2 h-100 position-relative">
                            <!-- Claim and Status Badges Start -->
                            <?php get_template_part('parts/common/status', 'badges'); ?>
                            <!-- Claim and Status Badges End -->
                            <div class="row g-0">
                                <div class="col-4 d-flex justify-content-center align-items-center p-2">
                                    <a href="<?php echo ($post_status !== 'draft') ? get_permalink() : '#'; ?>">
                                        <img class="itsn-listing-img rounded img-fluid" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    </a>
                                </div>
                                <div class="col-8 py-2 d-flex flex-column justify-content-center">
                                    <div>
                                        <!-- Star Rating Display Start -->
                                        <?php get_template_part('parts/common/star', 'rating'); ?>
                                        <!-- Star Rating Display End -->
                                        <a href="<?php echo ($post_status !== 'draft') ? get_permalink() : '#'; ?>">
                                            <h6 class="text-capitalize mb-1 overflow-hidden d-flex align-items-center gap-1">
                                                <?php echo wp_trim_words(get_the_title(), 5, '...'); ?>
                                                <?php get_template_part('parts/common/verification', 'badge'); ?>
                                            </h6>
                                        </a>
                                        <p class="fs-7 lh-base mb-2 overflow-hidden"><?php echo esc_html(wp_trim_words($content, 12, '...')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="text-center lh-lg fs-6 py-4">No Locked Companies found.</p>';
                endif;
                ?>
            </div>
        </div>
        <!-- Locked End -->
    </div>
</div>