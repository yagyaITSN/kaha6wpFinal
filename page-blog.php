<?php get_header(); ?>

<!-- Banner -->
<?php include 'ads/blog-list/bloglist-ad-one.php' ?>
<!-- End Banner -->

<!-- Blog Listed Cards -->
<section class="blog-section py-5">
    <div class="container">
        <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 m-0">All Blogs</h2>
        <div class="blog-cards mt-5" id="blogCards">
            <?php
            // Define custom query arguments
            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'order' => 'DESC',
                'orderby' => 'date',
            );

            // Create custom query
            $blog_query = new WP_Query($args);

            if ($blog_query->have_posts()) :
                while ($blog_query->have_posts()) : $blog_query->the_post();
                    $image = wp_get_attachment_url(get_post_thumbnail_id());
                    if (empty($image)) {
                        $image = 'https://kaha6.com/wp-content/uploads/kaha6-no-image.png';
                    }
                    $content = get_the_content();

                    // Get all categories for the post
                    $categories = get_the_category();
                    $parent_category = 'Uncategorized';

                    if (!empty($categories)) {
                        $parent_category = $categories[0]->name;
                    }
            ?>
                    <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden">
                        <img class="card-img-top pt-2" src="<?php echo $image; ?>"
                            alt="<?php the_title(); ?>">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title text-dark text-capitalize"><?php the_title(); ?>
                            </h5>
                            <div class="card-tags d-flex justify-content-between flex-wrap gap-2">
                                <span class="badge text-dark text-capitalize"><?php echo esc_html($parent_category); ?></span>
                                <span class="badge text-dark text-capitalize">Comments(<?php echo get_comments_number(); ?>)</span>
                            </div>
                        </div>
                        <div class="blog-btn position-absolute d-flex align-items-center justify-content-center">
                            <a class="btn btn-custom-red" href="<?php the_permalink(); ?>">View</a>
                        </div>
                    </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<h5 class="fs-5 fw-bold lh-lg text-center">Business not found<br/>Try another options</h5>';
            endif;
            ?>
        </div>
        <div class="card-pagination" id="pagination">
        </div>
    </div>
</section>
<!-- End Blog listed Cards -->

<!-- Banner -->
<?php include 'ads/blog-list/bloglist-ad-two.php' ?>
<!-- End Banner -->

<!-- Latest Blogs -->
<section class="related-card-section container py-5">
    <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 mb-4">Related Blogs</h2>
    <div class="related-card-slider swiper mySwiper">
        <div class="swiper-wrapper py-5">
            <?php
            // Define custom query arguments
            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 10,
                'order' => 'DESC',
                'orderby' => 'date',
            );

            // Create custom query
            $blog_query = new WP_Query($args);

            if ($blog_query->have_posts()) :
                while ($blog_query->have_posts()) : $blog_query->the_post();
                    $image = wp_get_attachment_url(get_post_thumbnail_id());
                    if (empty($image)) {
                        $image = 'https://kaha6.com/wp-content/uploads/kaha6-no-image.png';
                    }
                    $content = get_the_content();

                    // Get all categories for the post
                    $categories = get_the_category();
                    $parent_category = 'Uncategorized';
                    $child_categories = [];

                    if (!empty($categories)) {
                        $parent_category = $categories[0]->name;
                    }
            ?>
                    <a href="<?php the_permalink(); ?>"
                        class="swiper-slide card shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden">
                        <img src="<?php echo $image; ?>" class="card-img-top"
                            alt="<?php echo get_the_title(); ?>">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title text-dark text-capitalize"><?php echo get_the_title(); ?></h5>
                            <div class="card-tags d-flex justify-content-between flex-wrap gap-2">
                                <span class="badge text-dark text-capitalize"><?php echo esc_html($parent_category); ?></span>
                                <span class="badge text-dark text-capitalize">Comments(<?php echo get_comments_number(); ?>)</span>
                            </div>
                        </div>
                    </a>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<h5 class="fs-5 fw-bold lh-lg text-center">Business not found<br/>Try another options</h5>';
            endif;
            ?>
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>
<!-- End Latest Blogs -->

<?php get_footer(); ?>