<?php
// Fetch approved comments with ratings
$post_id = get_the_ID();
$args = array(
    'post_id' => $post_id,
    'status'  => 'approve',
);
$comments = get_comments($args);

// Extract ratings from comment meta
$ratings = array();
foreach ($comments as $comment) {
    $rating = get_comment_meta($comment->comment_ID, 'pixrating', true);
    if ($rating) {
        $ratings[] = floatval($rating);
    }
}

// Calculate average rating
$average_rating = !empty($ratings) ? array_sum($ratings) / count($ratings) : 0;
$rating_count = count($ratings);

// Calculate stars
$full_stars = floor($average_rating);
$half_star = ($average_rating - $full_stars >= 0.25 && $average_rating - $full_stars < 0.75) ? 1 : 0;
if ($average_rating - $full_stars >= 0.75) {
    $full_stars++;
    $half_star = 0;
}
?>

<span class="text-dark"><?php echo number_format($average_rating, 1); ?></span>
<span class="text-muted ms-1">(<?php echo $rating_count; ?> reviews)</span>