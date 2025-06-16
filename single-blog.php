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

?>

<!-- Blog content Section -->
<section class="blog-content py-5">
    <div class="container">
        <h1 class="fs-1 fw-bold"><?php echo get_the_title(); ?></h1>
        <?php echo $category_tags; ?>
        <?php
        // Get the current post's URL and title
        $page_url = esc_url(get_permalink());
        $page_title = esc_html(get_the_title());
        ?>

        <div class="share-buttons d-flex align-items-center flex-wrap gap-3 my-4">
            <span class="fw-bold">Share:</span>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($page_url); ?>" target="_blank"
                class="btn btn-sm shadow-sm border-0 text-decoration-none rounded-4" title="Share on Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($page_url); ?>&text=<?php echo urlencode($page_title); ?>" target="_blank"
                class="btn btn-sm shadow-sm border-0 text-decoration-none rounded-4" title="Share on Twitter">
                <i class="fa-brands fa-x-twitter"></i>
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($page_url); ?>&title=<?php echo urlencode($page_title); ?>"
                target="_blank" class="btn btn-sm shadow-sm border-0 text-decoration-none rounded-4" title="Share on LinkedIn">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="https://wa.me/?text=<?php echo urlencode($page_title . ' ' . $page_url); ?>" target="_blank"
                class="btn btn-sm shadow-sm border-0 text-decoration-none rounded-4" title="Share on WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
            <a href="mailto:?subject=<?php echo urlencode($page_title); ?>&body=Check%20out%20this%20article:%20<?php echo urlencode($page_url); ?>"
                class="btn btn-sm shadow-sm border-0 text-decoration-none rounded-4" title="Share via Email">
                <i class="fas fa-envelope"></i>
            </a>
        </div>
        <div class="blog-tex-content d-flex justify-content-between flex-wrap gap-2">
            <?php
            // Get the post content and apply the_content filter for proper formatting
            $content = apply_filters('the_content', get_the_content());
            $image = wp_get_attachment_url(get_post_thumbnail_id());
            if (empty($image)) {
                $image = 'https://kaha6.com/wp-content/uploads/kaha6-no-image.png';
            }

            // Define ads using get_template_part (assuming template parts exist)
            ob_start();
            get_template_part('ads/blog-details/ad-blog-details', 'one'); // Desktop ad (728x90)
            $ad_desktop = ob_get_clean();

            ob_start();
            get_template_part('ads/blog-details/ad-blog-details', 'two'); // Mobile ad (300x250)
            $ad_mobile = ob_get_clean();

            ob_start();
            get_template_part('ads/blog-details/ad-blog-details', 'three'); // Desktop ad (728x90)
            $ad_desktop_two = ob_get_clean();

            ob_start();
            get_template_part('ads/blog-details/ad-blog-details', 'four'); // Mobile ad (300x250)
            $ad_mobile_two = ob_get_clean();

            ob_start();
            get_template_part('ads/blog-details/ad-blog-details', 'five'); // Paired ads (300x250)
            $ad_paired = ob_get_clean();

            // Define the sequence of ads
            $ads = [
                $ad_desktop,
                $ad_mobile,
                $ad_desktop_two,
                $ad_mobile_two,
                $ad_paired,
            ];

            // Split content into paragraphs using a regex to match <p> tags
            $paragraphs = [];
            if (!empty($content)) {
                preg_match_all('/<p[^>]*>.*?(<\/p>|$)/is', $content, $matches);
                $paragraphs = $matches[0];
            }

            // Initialize output
            $output = '<div class="blog-text">';
            $output .= '<img class="mb-3" src="' . esc_url($image) . '" alt="' . esc_attr(get_the_title()) . '">';

            // Insert ads after each paragraph or append serially
            if (!empty($paragraphs)) {
                foreach ($paragraphs as $index => $paragraph) {
                    $output .= $paragraph;
                    // Insert ad after each paragraph if available
                    if (isset($ads[$index])) {
                        $output .= $ads[$index];
                    }
                }
                // Append any remaining ads if there are fewer paragraphs than ads
                for ($i = count($paragraphs); $i < count($ads); $i++) {
                    $output .= $ads[$i];
                }
            } else {
                // If no paragraphs, output content as is and append all ads
                $output .= $content;
                foreach ($ads as $ad) {
                    $output .= $ad;
                }
            }
            $output .= '</div>';
            echo $output;
            ?>
            <div class="blog-tex-content-box">
                <div class="blog-tex-content-sticky">
                    <!-- Desktop -->
                    <!-- Banner badba -->
                    <?php get_template_part('ads/blog-details/ad-blog-details', 'six'); ?>
                    <!-- End Banner badba -->
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
        comments_template();
    }
    ?>

</section>
<!-- End Comment and Contact Details Section -->

<!-- Related Cards -->
<section class="related_card-section container py-5">
    <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 mb-4">Related Blogs</h2>
    <div class="related_card-slider swiper mySwiper">
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
                        $child_categories = get_categories(array(
                            'child_of' => $categories[0]->term_id,
                            'hide_empty' => false,
                        ));
                    }
            ?>
                    <a href="<?php the_permalink(); ?>"
                        class="swiper-slide card shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden">
                        <img src="<?php echo $image; ?>" class="card-img-top"
                            alt="<?php echo get_the_title(); ?>">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title text-dark text-capitalize"><?php the_title(); ?></h5>
                            <div class="d-flex justify-content-between align-items-center mb-2 card-location_rate">
                                <p class="text-muted"><?php echo esc_html($parent_category); ?></p>
                                <div class="d-flex align-items-center">
                                    <span class="text-dark"><?php echo get_comments_number(); ?></span>
                                    <span class="text-muted ms-1">Comments</span>
                                </div>
                            </div>
                            <div class="card-tags d-flex flex-wrap gap-2">
                                <?php
                                if (!empty($child_categories)) {
                                    foreach ($child_categories as $child) {
                                        echo '<span class="badge text-dark me-2 text-capitalize">' . esc_html($child->name) . '</span>';
                                    }
                                }
                                ?>
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
<!-- End Related Cards -->

<!-- Banner badba -->
<?php get_template_part('ads/blog-details/ad-blog-details', 'seven'); ?>
<!-- End Banner badba -->

<style>
    /* Comment Section Styles */
    .comments-list {
        margin-bottom: 2rem;
    }

    .comment {
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        background-color: var(--cl--secondary);
        border-left: 4px solid var(--cl--primary);
        box-shadow: 0 2px 4px rgba(193, 39, 45, 0.05);
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        font-size: 0.9rem;
        align-items: center;
    }

    .comment-author {
        font-weight: bold;
    }

    .comment-author.member {
        color: var(--cl--primary);
    }

    .comment-date {
        color: #6c757d;
        font-size: 0.65rem;
    }

    .comment-body {
        line-height: 1.6;
        margin-bottom: 0.5rem;
        white-space: pre-wrap;
    }

    .comment-empty {
        color: #6c757d;
        font-style: italic;
        text-align: center;
        padding: 2rem;
    }

    .form-message {
        margin-top: 1rem;
        padding: 0.75rem;
        border-radius: 4px;
        display: none;
    }

    .form-message.success {
        background-color: #d4edda;
        color: #155724;
        display: block;
    }

    .form-message.error {
        background-color: #f8d7da;
        color: #721c24;
        display: block;
    }

    /* Reply styling */
    .comment-replies {
        margin-left: 2.5rem;
        border-left: 2px solid var(--cl--primary);
        padding-left: 1.5rem;
        margin-top: 1rem;
    }

    .reply-form-container {
        margin: 1.5rem 0;
        padding: 1.25rem;
        background: var(--cl--secondary);
        border-radius: 6px;
    }

    .comment-reply-btn {
        background: none;
        border: none;
        color: #007bff;
        cursor: pointer;
        font-size: 0.85rem;
        padding: 0;
        margin-top: 0.5rem;
    }

    .comment-reply-btn:hover {
        text-decoration: underline;
    }

    /* Loading state */
    .loading-comments {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }

    .loading-spinner {
        border: 4px solid var(--cl--secondary);
        border-top: 4px solid var(--cl--primary);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .comment-replies {
            margin-left: 1rem;
            padding-left: 1rem;
        }

        .comment-form-ad {
            flex-direction: column;
        }

        .form-ad {
            max-width: 100% !important;
        }

        .ad-container {
            margin: 1rem auto !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageUrl = encodeURIComponent(window.location.href);
        const pageTitle = encodeURIComponent(document.title);

        document.querySelectorAll('.share-buttons a').forEach(link => {
            link.href = link.href.replace('[PAGE-URL]', pageUrl).replace('[PAGE-TITLE]', pageTitle);
        });
    });
</script>

<?php get_footer(); ?>