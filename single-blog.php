<?php get_header();

$image = wp_get_attachment_url(get_post_thumbnail_id());
if (empty($image)) {
    $image = 'https://kaha6.com/wp-content/uploads/kaha6-no-image.png';
}

// Get all categories for the post
$categories = get_the_category();
$category_tags = '';

if (!empty($categories)) {
    // Use the first category as the parent
    $parent_category = $categories[0];
    $category_tags .= '<div class="blog-tags my-3 d-flex flex-wrap">';
    $category_tags .= '<span class="badge text-dark me-2 text-capitalize"><a href="' . esc_url(get_term_link($parent_category)) . '" class="text-dark text-decoration-none">' . esc_html($parent_category->name) . '</a></span>';

    // Get subcategories of the first category
    $child_categories = get_categories(array(
        'child_of' => $parent_category->term_id,
        'hide_empty' => false,
    ));

    if (!empty($child_categories)) {
        foreach ($child_categories as $child) {
            $category_tags .= '<span class="badge text-dark me-2 text-capitalize"><a href="' . esc_url(get_term_link($child)) . '" class="text-dark text-decoration-none">' . esc_html($child->name) . '</a></span>';
        }
    }
    $category_tags .= '</div>';
} else {
    // Fallback for no categories
    $category_tags .= '<div class="blog-tags my-3 d-flex flex-wrap">';
    $category_tags .= '<span class="badge text-dark me-2 text-capitalize">Uncategorized</span>';
    $category_tags .= '</div>';
}

$image = wp_get_attachment_url(get_post_thumbnail_id());
if (empty($image)) {
    $image = 'https://kaha6.com/wp-content/uploads/kaha6-no-image.png';
}

$views = get_post_meta(get_the_ID(), 'post_views_count', true);
?>

<!-- Blog content Section -->
<section class="blog-content py-5">
    <div class="container">
        <h1 class="fs-1 fw-bold"><?php echo get_the_title(); ?>
        </h1>
        <?php echo $category_tags; ?>
        <?php /*
        <div class="blog-tags my-3 d-flex flex-wrap">
            <span class="badge text-dark me-2 text-capitalize">province-no-1</span>
            <span class="badge text-dark me-2 text-capitalize">Medical</span>
            <span class="badge text-dark text-capitalize">Hospital</span>
        </div>
        */ ?>
        <div class="blog-share-meta d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div class="share-buttons d-flex align-items-center flex-wrap gap-3">
                <span class="fw-bold d-none d-sm-block">Share:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($page_url); ?>" target="_blank"
                    class="btn btn-sm shadow-sm border-0 text-decoration-none rounded-4 " title="Share on Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($page_url); ?>&text=<?php echo urlencode($page_title); ?>" target="_blank"
                    class="btn btn-sm shadow-sm border-0 text-decoration-none rounded-4 " title="Share on Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($page_url); ?>&title=<?php echo urlencode($page_title); ?>"
                    target="_blank" class="btn btn-sm shadow-sm border-0 text-decoration-none rounded-4 "
                    title="Share on LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://wa.me/?text=<?php echo urlencode($page_title . ' ' . $page_url); ?>" target="_blank"
                    class="btn btn-sm shadow-sm border-0 text-decoration-none rounded-4 " title="Share on WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="mailto:?subject=<?php echo urlencode($page_title); ?>&body=Check%20out%20this%20article:%20<?php echo urlencode($page_url); ?>"
                    class="btn btn-sm shadow-sm border-0 text-decoration-none rounded-4" title="Share via Email">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
            <div class="blog-meta d-flex align-items-center flex-wrap gap-3">
                <span class="text-muted"><i class="fas fa-calendar-alt me-1"></i>Published on: <span
                        class="text-dark"><?php echo get_the_date('Y-m-d'); ?></span>
                </span>
                <span class="text-muted"><i class="fas fa-user me-1"></i>Author: <span class="text-dark">
                        <?php
                        $author_id = get_post_field('post_author', get_the_ID());
                        echo get_the_author_meta('display_name', $author_id);
                        ?>
                    </span>
                </span>
                <span class="text-muted"><i class="fas fa-eye me-1"></i>Views: <span
                        class="text-dark"><?php echo $views ? $views : '0' ?></span></span>
                <span class="text-muted"><i class="fas fa-comments me-1"></i>Comments: <span class="text-dark"><?php echo get_comments_number(); ?></span></span>
            </div>
        </div>

        <!-- Banner  -->
        <?php get_template_part('ads/blog-details/md/md', 'ad-one'); ?>
        <!-- End Banner  -->

        <div class="blog-tex-content d-flex flex-column flex-lg-row gap-3">
            <div class="blog-text content-box flex-fill">
                <img class="mb-3" src="<?php echo $image; ?>"
                    alt="<?php echo get_the_title(); ?>">

                <?php
                $content = apply_filters('the_content', get_the_content());

                // Function to capture template part output
                function capture_template_part($slug, $name = '')
                {
                    ob_start();
                    get_template_part($slug, $name);
                    return ob_get_clean();
                }

                // Define ads
                $ads = [
                    capture_template_part('ads/blog-details/md/md', 'ad-two'),
                    capture_template_part('ads/blog-details/sm/sm', 'ad-one'),
                    capture_template_part('ads/blog-details/md/md', 'ad-three'),
                    capture_template_part('ads/blog-details/sm/sm', 'ad-two'),
                    '<div class="d-flex flex-warp justify-content-center gap-3 my-3">' .
                        capture_template_part('ads/blog-details/common/common', 'ad-one') .
                        capture_template_part('ads/blog-details/md/md', 'ad-four') .
                        '</div>',
                ];

                // Split content into words
                $words = preg_split('/\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
                $word_count = count($words);
                $modified_content = '';
                $current_chunk = '';
                $ad_count = count($ads);

                // Calculate intervals for ad placement
                $interval = $word_count > 0 ? max(1, ceil($word_count / ($ad_count + 1))) : 1;
                $ad_index = 0;

                // Process words and insert ads
                for ($i = 0; $i < $word_count; $i++) {
                    $current_chunk .= $words[$i] . ' ';
                    if (($i + 1) % $interval == 0 || $i == $word_count - 1) {
                        $modified_content .= $current_chunk;
                        if ($ad_index < $ad_count && ($i < $word_count - 1 || $word_count <= $interval)) {
                            $modified_content .= $ads[$ad_index];
                            $ad_index++;
                        }
                        $current_chunk = '';
                    }
                }

                // Ensure all remaining ads are displayed if not already placed
                while ($ad_index < $ad_count) {
                    $modified_content .= $ads[$ad_index];
                    $ad_index++;
                }

                // Output modified content
                echo $modified_content;
                ?>

            </div>

            <div class="blog-tex-content-box">
                <div class="blog-tex-content-sticky">

                    <!-- Banner  -->
                    <?php get_template_part('ads/blog-details/common/common', 'ad-two'); ?>
                    <!-- End Banner  -->

                    <!-- Banner  -->
                    <?php get_template_part('ads/blog-details/common/common', 'ad-three'); ?>
                    <!-- End Banner  -->

                </div>
            </div>

        </div>
    </div>
</section>
<!-- End Blog content Section -->

<!-- Comment and Contact Details Section -->
<section class="comment-contact-details container py-5">
    <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 mb-4">Leave a Comment</h2>
    <?php
    if (comments_open() || get_comments_number()) {
        comments_template('/comments-blog.php');
    }
    ?>
</section>
<!-- End Comment and Contact Details Section -->

<!-- Related Blogs -->
<section class="related-card-section container py-5">
    <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 mb-4">Related Businesses</h2>
    <div class="related-card-slider swiper mySwiper">
        <div class="swiper-wrapper py-5">

            <?php
            $current_post_id = get_the_ID();
            $categories = wp_get_post_categories($current_post_id, array('fields' => 'ids'));

            // Define the query arguments
            $args = array(
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => 10,
                'order'          => 'DESC',
                'orderby'        => 'date',
                'category__in'   => $categories,
                'post__not_in'   => array($current_post_id),
            );

            $blog_query = new WP_Query($args);

            if ($blog_query->have_posts()) :
                while ($blog_query->have_posts()) : $blog_query->the_post();
                    $image = wp_get_attachment_url(get_post_thumbnail_id());
                    if (empty($image)) {
                        $image = 'https://kaha6.com/wp-content/uploads/kaha6-no-image.png';
                    }
                    $content = get_the_content();

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
                            <div class="d-flex justify-content-between align-items-center mb-2 card-location_rate">
                                <p class="text-muted"><?php echo esc_html($parent_category); ?></p>
                                <span class="badge text-dark text-capitalize">Comments(<?php echo get_comments_number(); ?>)</span>
                            </div>
                        </div>
                    </a>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p class="fs-6 lh-lg text-center">Business not found<br/>Try another options</p>';
            endif;
            ?>
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>
<!-- End Related Blogs -->

<!-- Banner  -->
<?php get_template_part('ads/blog-details/common/common', 'ad-four') ?>
<!-- End Banner  -->


<?php get_footer(); ?>