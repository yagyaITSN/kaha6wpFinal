<?php get_header();

$author = get_queried_object();
$author_id = $author->ID;

// $user_notes = get_user_meta($author_id, 'notes', true);
$user_phone = get_user_meta($author_id, 'phone', true);
$user_twitter = get_user_meta($author_id, 'twitter', true);
$user_facebook = get_user_meta($author_id, 'facebook', true);
$user_linkedin = get_user_meta($author_id, 'linkedin', true);
$user_instagram = get_user_meta($author_id, 'instagram', true);
$user_tiktok = get_user_meta($author_id, 'tiktok', true);
$user_youtube = get_user_meta($author_id, 'youtube', true);

$profile_photo_id = get_user_meta($author_id, 'profile_photo', true);
$profile_photo_url = $profile_photo_id ? wp_get_attachment_url($profile_photo_id) : get_template_directory_uri() . '/assets/images/profile.png';

// Function to mask email
function mask_email($email)
{
  $parts = explode('@', $email);
  $name = substr($parts[0], 0, 2) . str_repeat('*', max(0, strlen($parts[0]) - 2));
  return $name . '@' . $parts[1];
}
?>

<!-- End Author Hero Section -->
<section class="author-profile py-5">
  <div class="container">
    <div class="author-profile-content d-flex flex-wrap align-items-center gap-3 flex-sm-nowrap">
      <div
        class="author-profile-content-details d-flex flex-column align-items-center justify-content-center text-center gap-3 shadow-sm border-0 rounded-4 overflow-hidden p-3">
        <div class="author-img">
          <img src="<?php echo esc_url($profile_photo_url); ?>" alt="<?php echo $author->display_name ?>'s Profile Image">
        </div>
        <div class="author-details">
          <h5 class="fw-bold"><?php echo $author->display_name ?></h5>
          <?php if (!current_user_can('administrator')): ?>
            <h4 class="m-0"><?php echo esc_html(mask_email($author->user_email)); ?></h4>
          <?php else: ?>
            <h4 class="m-0">Email: <a class="text-dark" href="mailto:<?php echo esc_html($author->user_email); ?>"><?php echo esc_html($author->user_email); ?></a></h4>
            <h4 class="mt-2">Phone: <a class="text-dark" href="tel:<?php echo esc_html($user_phone); ?>"><?php echo esc_html($user_phone); ?></a></h4>
            <div class="d-flex gap-3">
              <?php if ($user_facebook): ?>
                <a href="<?php echo $user_facebook; ?>?ref=kaha6.com" class="text-decoration-none" target="_blank">
                  <i class="fa-brands fa-facebook-f fb-color-code d-inline"></i>
                </a>
              <?php endif; ?>

              <?php if ($user_twitter): ?>
                <a href="<?php echo $user_twitter; ?>?ref=kaha6.com" class="text-decoration-none" target="_blank">
                  <i class="fa-brands fa-x-twitter x-color-code d-inline"></i>
                </a>
              <?php endif; ?>

              <?php if ($user_instagram): ?>
                <a href="<?php echo $user_instagram; ?>?ref=kaha6.com" class="text-decoration-none" target="_blank">
                  <i class="fa-brands fa-instagram insta-color-code d-inline"></i>
                </a>
              <?php endif; ?>

              <?php if ($user_tiktok): ?>
                <a href="<?php echo $user_tiktok; ?>?ref=kaha6.com" class="text-decoration-none" target="_blank">
                  <i class="fa-brands fa-tiktok tiktok-color-code d-inline"></i>
                </a>
              <?php endif; ?>

              <?php if ($user_linkedin): ?>
                <a href="<?php echo $user_linkedin; ?>?ref=kaha6.com" class="text-decoration-none" target="_blank">
                  <i class="fa-brands fa-linkedin-in linkedin-color-code d-inline"></i>
                </a>
              <?php endif; ?>

              <?php if ($user_youtube): ?>
                <a href="<?php echo $user_youtube; ?>?ref=kaha6.com" class="text-decoration-none" target="_blank">
                  <i class="fa-brands fa-youtube yt-color-code d-inline"></i>
                </a>
              <?php endif; ?>
            </div>

          <?php endif; ?>
        </div>
      </div>
      <!-- Banner  -->
      <?php include 'ads/author/author-ad-one.php' ?>
      <!-- End Banner  -->
    </div>
  </div>
</section>

<!-- Author Added Businesses -->
<section class="author-added-businesses py-5">
  <div class="container">
    <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3"><?php echo $author->display_name; ?>'s Added Businesses</h2>
    <div class="author-added-cards mt-5">
      <?php
      $author_id = get_query_var('author');
      $current_locations = wp_get_post_terms(get_the_ID(), 'ait-locations', array('fields' => 'slugs'));
      $current_items = wp_get_post_terms(get_the_ID(), 'ait-items', array('fields' => 'slugs'));
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

      $args = array(
        'post_type' => 'ait-item',
        'author' => $author_id,
        'posts_per_page' => 25,
        'paged' => $paged,
        'post__not_in' => array(get_the_ID()),
      );

      $query = new WP_Query($args);

      if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();

          $metadata = get_post_meta(get_the_ID(), '_ait-item_item-data', false);

          $image_id = get_post_thumbnail_id();
          $image_data = wp_get_attachment_image_src($image_id, 'custom_grid_img_size');

          if (!empty($image_data)) {
            $image = $image_data[0];
          } else {
            $image = 'https://kaha6.com/wp-content/uploads/kaha6-no-image.png';
          }

          // Get terms for ait-locations taxonomy
          $location_terms = get_the_terms(get_the_ID(), 'ait-locations');
          $location_parent = '';
          $location_child = '';

          if ($location_terms && !is_wp_error($location_terms)) {
            foreach ($location_terms as $term) {
              if ($term->parent == 0) {
                // Parent term (main category) for ait-locations
                $location_parent = $term->name;
              } else {
                // Get the first child term
                $location_child = $term->name;
                // Ensure parent of child term is retrieved
                if ($term->parent != 0) {
                  $parent_term = get_term($term->parent, 'ait-locations');
                  if ($parent_term && !is_wp_error($parent_term)) {
                    $location_parent = $parent_term->name;
                  }
                }
                break; // Only take the first child term
              }
            }
          }
          // Get terms for ait-items (Company Type) taxonomy
          $company_terms = get_the_terms(get_the_ID(), 'ait-items');
          $company_parent = '';
          $company_child = '';

          if ($company_terms && !is_wp_error($company_terms)) {
            foreach ($company_terms as $term) {
              if ($term->parent == 0) {
                // Parent term (main category) for ait-items
                $company_parent = $term->name;
              } else {
                // Get the first child term
                $company_child = $term->name;
                // Ensure parent of child term is retrieved
                if ($term->parent != 0) {
                  $parent_term = get_term($term->parent, 'ait-items');
                  if ($parent_term && !is_wp_error($parent_term)) {
                    $company_parent = $parent_term->name;
                  }
                }
                break;
              }
            }
          }
      ?>
          <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden">
            <img class="card-img-top pt-2" src="<?php echo esc_url($image); ?>"
              alt="<?php echo get_the_title(); ?>">
            <div class="card-body d-flex flex-column justify-content-between">
              <h5 class="card-title text-dark text-capitalize"><?php echo get_the_title(); ?>
              </h5>
              <div class="d-flex justify-content-between align-items-center mb-2 card-location_rate">
                <?php if (!empty($location_child)) : ?>
                  <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i><?php echo esc_html($location_child); ?></p>
                <?php endif; ?>
                <div class="d-flex align-items-center">
                  <span class="text-warning me-1">★</span>
                  <?php get_template_part('parts/common/slider', 'rating'); ?>
                </div>
              </div>
              <div class="card-tags d-flex flex-wrap">
                <?php if (!empty($location_parent)) : ?>
                  <span class="badge text-dark me-2 text-capitalize mb-1"><?php echo esc_html($location_parent); ?></span>
                <?php endif; ?>
                <?php if (!empty($company_parent)) : ?>
                  <span class="badge text-dark me-2 text-capitalize mb-1"><?php echo esc_html($company_parent); ?></span>
                <?php endif; ?>
                <?php if (!empty($company_child)) : ?>
                  <span class="badge text-dark text-capitalize mb-1"><?php echo esc_html($company_child); ?></span>
                <?php endif; ?>
              </div>
            </div>
            <div class="author-added-btn position-absolute d-flex align-items-center justify-content-center">
              <a class="btn btn-custom-red" href="<?php the_permalink(); ?>">View</a>
            </div>
          </div>
      <?php
        endwhile;
        wp_reset_postdata();
      else :
        echo '<p class="text-center">No Businesses found.</p>';
      endif;
      ?>
    </div>

    <!-- Pagination Start -->
    <div class="row pt-5 justify-content-center">
      <div class="col d-flex justify-content-center">
        <nav aria-label="Page navigation">
          <ul class="pagination mb-0 card-pagination">
            <?php
            $big = 999999999; // need an unlikely integer
            $pagination_args = array(
              'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
              'format' => '?paged=%#%',
              'total' => $query->max_num_pages,
              'current' => max(1, $paged),
              'prev_text' => '«',
              'next_text' => '»',
              'type' => 'array',
              'mid_size' => 0,
              'end_size' => 0,
            );

            $paginate_links = paginate_links($pagination_args);
            if ($paginate_links) {
              foreach ($paginate_links as $link) {
                $is_current = strpos($link, 'current') !== false ? ' active_pgnation' : '';
                preg_match('/href=["\'](.*?)["\']/i', $link, $href);
                preg_match('/>(.*?)</', $link, $text);
                $href = isset($href[1]) ? $href[1] : '#';
                $text = isset($text[1]) ? $text[1] : '';
                if ($text === '…') {
                  $text = '...';
                }
                echo '<li class="page-item' . $is_current . '"><a class="page-link" href="' . esc_url($href) . '">' . esc_html($text) . '</a></li>';
              }
            }
            ?>
          </ul>
        </nav>
      </div>
    </div>
    <!-- Pagination End -->

  </div>
</section>
<!-- End Author Added Businesses -->

<!-- Banner  -->
<?php get_template_part('ads/author/author', 'ad-two'); ?>
<!-- End Banner  -->

<?php get_footer(); ?>