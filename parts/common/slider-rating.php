<?php
// Ensure WordPress database object is available
global $wpdb;
$table_name = $wpdb->prefix . 'rating_review';

// Get the current company/post ID
$post_id = get_the_ID();

// Fetch parent reviews (where parent_id = 0) for the current company
$reviews = $wpdb->get_results($wpdb->prepare(
    "SELECT rating FROM $table_name WHERE company_id = %d AND parent_id = 0",
    $post_id
));

// Extract ratings and count parent reviews
$ratings = array();
foreach ($reviews as $review) {
    if ($review->rating) {
        $ratings[] = floatval($review->rating);
    }
}

// Calculate average rating and review count
$average_rating = !empty($ratings) ? array_sum($ratings) / count($ratings) : 0;
$rating_count = count($ratings);

// Calculate stars (for display, same logic as your original code)
$full_stars = floor($average_rating);
$half_star = ($average_rating - $full_stars >= 0.25 && $average_rating - $full_stars < 0.75) ? 1 : 0;
if ($average_rating - $full_stars >= 0.75) {
    $full_stars++;
    $half_star = 0;
}
?>

<span class="text-dark"><?php echo number_format($average_rating, 1); ?></span>
<span class="text-muted ms-1">(<?php echo esc_html($rating_count); ?> reviews)</span>