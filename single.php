<?php get_header(); ?>

<!-- <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v9.0&appId=586037251967831&autoLogAppEvents=1" nonce="2uGxMnPr"></script> -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBOvF0bRAeot5g2eFBG6Wji2aHw5Tct9g8&callback=initMap"></script>

<?php
// Get post data
$post_id = get_the_ID();
$post = get_post($post_id);
$is_valid = ($post && $post->post_type === 'ait-item');
$title = $is_valid ? $post->post_title : '';
$introduction = $is_valid ? $post->post_content : '';
// $logo_url = $is_valid && has_post_thumbnail($post_id) ? wp_get_attachment_url(get_post_thumbnail_id($post_id)) : 'https://kaha6.com/wp-content/uploads/kaha6-no-image.png';
$metadata = $is_valid ? get_post_meta($post_id, '_ait-item_item-data', true) : [];
$metadata_author = $is_valid ? get_post_meta($post_id, '_ait-item_item-author', true) : [];
$subtitle = $metadata['subtitle'] ?? '';
$phone = $metadata['telephone'] ?? '';
$postalCode = $metadata['postalCode'] ?? '';
$additional_phones = array_filter([
  $metadata['telephoneAdditional'][0]['number'] ?? '',
  $metadata['telephoneAdditional'][1]['number'] ?? '',
  $metadata['telephoneAdditional'][2]['number'] ?? '',
]);
$additional_phones_str = !empty($additional_phones) ? implode(', ', $additional_phones) : 'N/A';

$company_services = array_filter($metadata['companyServices'] ?? [], function ($service) {
  return !empty($service['services']) || !empty($service['servicesDesc']) || !empty($service['servicesPrice']) || !empty($service['servicesImgId']);
});

// $company_services_str = !empty($company_services) ? implode(', ', $company_services) : 'N/A';

$email = $metadata['email'] ?? 'N/A';
$website = $metadata['web'] ?? 'N/A';
$address = $metadata['map']['address'] ?? 'N/A';
$latitude = $metadata['map']['latitude'] ?? '';
$longitude = $metadata['map']['longitude'] ?? '';
$ait_items = $is_valid ? wp_get_post_terms($post_id, 'ait-items', ['fields' => 'names']) : [];
$ait_items_str = !empty($ait_items) ? implode(', ', $ait_items) : 'N/A';
$ait_locations = $is_valid ? wp_get_post_terms($post_id, 'ait-locations', ['fields' => 'names']) : [];
$ait_locations_str = !empty($ait_locations) ? implode(', ', $ait_locations) : 'N/A';
$ait_locations_str_hierarchy = 'N/A';

if ($is_valid) {
  $terms = wp_get_post_terms($post_id, 'ait-locations', ['orderby' => 'parent', 'order' => 'ASC']);

  if (!empty($terms) && !is_wp_error($terms)) {
    // Sort terms by hierarchy
    $sorted = [];

    // Helper to walk hierarchy
    $term_map = [];
    foreach ($terms as $term) {
      $term_map[$term->term_id] = $term;
    }

    // Recursive function to walk and collect names
    $added = [];

    function walk_terms($term, &$term_map, &$sorted, &$added)
    {
      if ($term->parent && isset($term_map[$term->parent])) {
        walk_terms($term_map[$term->parent], $term_map, $sorted, $added);
      }
      if (!in_array($term->term_id, $added)) {
        $sorted[] = $term->name;
        $added[] = $term->term_id;
      }
    }

    foreach ($terms as $term) {
      walk_terms($term, $term_map, $sorted, $added);
    }

    $ait_locations_str_hierarchy = implode(', ', array_unique($sorted));
  }
}

$social_icons = $metadata['socialIcons'] ?? [];
$opening_hours = [];
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
foreach ($days as $day) {
  $opening_hours[$day] = $metadata['openingHours' . $day] ?? 'Closed';
}
$opening_hours_note = $metadata['openingHoursNote'] ?? 'N/A';
$gallery = $metadata['gallery'] ?? [];
// $author = $metadata_author['author'] ? get_userdata($metadata_author['author'])->display_name : 'N/A';
// $author = (!empty($metadata_author) && is_array($metadata_author) && !empty($metadata_author['author']))
//     ? get_userdata($metadata_author['author'])->display_name
//     : 'N/A';
$post_author_id = get_post_field('post_author', $post_id);
$author = get_the_author_meta('display_name', $post_author_id);
// $featured = $metadata['featuredItem'] ? 'Yes' : 'No';

// Breadcrumb for service categories

$service_breadcrumbs = '';
if (!empty($ait_items)) {
  $terms = wp_get_post_terms($post_id, 'ait-items');
  if (!is_wp_error($terms) && !empty($terms)) {
    $service_breadcrumbs .= '<nav aria-label="breadcrumb">';
    $service_breadcrumbs .= '<ol class="breadcrumb">';
    $service_breadcrumbs .= '<li class="breadcrumb-item fs-7"><a class="custom-red" style="font-size:14px;" href="' . esc_url(home_url()) . '">Home</a></li>';
    foreach ($terms as $term) {
      $service_breadcrumbs .= '<li class="breadcrumb-item fs-7"><a class="custom-red fs-7" style="font-size:14px;" href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a></li>';
    }
    $service_breadcrumbs .= '<li class="breadcrumb-item active fs-7" aria-current="page">' . esc_html(get_the_title($post_id)) . '</li>';
    $service_breadcrumbs .= '</ol>';
    $service_breadcrumbs .= '</nav>';
  }
}

$location_breadcrumbs = '';
if (!empty($ait_locations)) {
  $terms = wp_get_post_terms($post_id, 'ait-locations');
  if (!is_wp_error($terms) && !empty($terms)) {
    $term = $terms[0];
    $hierarchy = array();
    while ($term) {
      array_unshift($hierarchy, $term); // Prepend to hierarchy
      if ($term->parent) {
        $term = get_term($term->parent, 'ait-locations');
      } else {
        $term = null;
      }
    }
    $location_breadcrumbs .= '<nav aria-label="breadcrumb" class="text-muted fs-7">';
    $location_breadcrumbs .= '<ol class="breadcrumb mb-2 lh-lg">';

    foreach ($hierarchy as $term_item) {
      $location_breadcrumbs .= '<li class="breadcrumb-item fs-7"><a class="text-muted" href="' . esc_url(get_term_link($term_item)) . '">' . esc_html($term_item->name) . '</a></li>';
    }
    $location_breadcrumbs .= '<li class="breadcrumb-item active fs-7" aria-current="page">' . esc_html(get_the_title($post_id)) . '</li>';
    $location_breadcrumbs .= '</ol>';
    $location_breadcrumbs .= '</nav>';
  }
}

$post_status = get_post_status($post_id);
$current_user_id = get_current_user_id();

// $set_count = setPostViews(get_the_ID());
// $visitor_count = getPostViews(get_the_ID());
$views = get_post_meta(get_the_ID(), 'post_views_count', true);
?>

<!-- Hero Section Single Page -->
<section class="single_page_hero-section d-flex align-items-center">
  <div class="single_page_hero-section-slider swiper mySwiper">
    <div class="swiper-wrapper">
      <?php
      $fallback_image = 'https://kaha6.com/wp-content/uploads/kaha6-no-image.png';
      $counter = 1;
      foreach ($gallery as $item) :
        $image_id = $item['image_id'] ?? 0;
        $image_url = $image_id ? wp_get_attachment_url($image_id) : $fallback_image;
        $thumbnail = $image_id ? wp_get_attachment_image_src($image_id, 'medium')[0] : $fallback_image;
      ?>
        <div class="swiper-slide"><img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($item['title'] ?? 'Gallery Image'); ?>"></div>
      <?php
        $counter++;
      endforeach; ?>
    </div>
  </div>
  <div
    class="container single_page_hero-content py-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="single_page_hero-title fs-1 fw-bold border-start border-4 border-danger ps-3 text-white">
        <?php echo esc_html(get_the_title()); ?>
      </h1>
      <p class="single_page_hero-description fs-6 text-capitalize text-white mt-3">
        <?php echo $subtitle; ?>
      </p>
      <div class="d-flex align-items-center gap-2 mb-3">
        <a href="tel:<?php echo esc_html($phone); ?>" class="btn btn-custom-red text-capitalize mt-3">Call now</a>
        <?php
        $claim_status = get_post_meta($post_id, '_bcv_claim_status', true) ?: 'not_claimed';
        $user_id = get_post_meta($post_id, '_bcv_user_id', true);
        $verification_status = get_post_meta($post_id, '_bcv_verification_status', true) ?: 'not_verified';
        $metadata_author = get_post_meta($post_id, '_ait-item_item-author', true);
        $has_author = !empty($metadata_author['author']);
        ?>

        <?php if ($claim_status === 'claimed') : ?>
          <p class="btn btn-success text-capitalize bcv-claimed text-white" style="padding:6px 12px; margin-bottom: -16px;"><i class="fa-solid fa-check"></i> Claimed!</p>
        <?php elseif (!current_user_can('administrator')) : ?>
          <?php if (!is_user_logged_in()) : ?>
            <a href="<?php echo esc_url(home_url('/login')); ?>" class="btn btn-custom-red text-capitalize mt-3" style="padding:6px 12px">Login to Claim</a>
          <?php elseif (!$user_id) : ?>
            <?php if (!$has_author && current_user_can('administrator')) : ?>
              <a href="<?php echo esc_url(home_url('/login')); ?>" class="btn btn-custom-red text-capitalize mt-3" style="padding:6px 12px">Login to Claim</a>
            <?php else : ?>
              <button class="btn btn-custom-red text-capitalize mt-3 bcv-claim-button" style="padding:6px 12px" data-post-id="<?php echo esc_attr($post_id); ?>">Claim</button>
            <?php endif; ?>
          <?php elseif ($user_id && $claim_status === 'not_claimed' && $verification_status !== 'verified') : ?>
            <button class="btn btn-warning text-capitalize mt-3 bcv-pending" style="padding:6px 12px">Claim on Pending!</button>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <!-- Owner Profile -->
      <?php if ($author) : ?>
        <div class="business-profile-owner d-flex align-items-center gap-2">
          <h5 class="mb-0 text-white fs-6">Business Owner :</h5>
          <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="business-profile-owner text-white fs-6 fw-medium">
            <?php echo esc_html($author); ?>
          </a>
        </div>
      <?php endif; ?>

      <?php if (current_user_can('administrator')): ?>
        <!-- Views -->
        <h5 class="mb-0 text-white fs-6 mt-3">Views: <?php echo $views ? $views : '0' ?></h5>
      <?php endif; ?>
    </div>
    <!-- Company Logo -->

    <div class="single_page_hero-logo text-center shadow-sm border-0 rounded-4 p-3">
      <?php
      $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
      $image_url = $image ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/kaha6-no-image.png';
      ?>
      <img src="<?php echo $image_url; ?>" alt="<?php echo esc_attr(get_the_title()); ?>"
        class="img-fluid rounded-4" width="150">
    </div>
  </div>
</section>
<!-- End Hero Section Single Page -->

<!-- About and Schedule Section Single Page -->
<section class="single-page-about-schedule-section py-5">
  <div class="container">
    <div
      class="single-page-about-schedule-details d-flex flex-wrap gap-3 justify-content-between align-items-start flex-lg-nowrap">
      <div class="single-page-about-details col-12 col-lg-8 d-flex flex-column justify-content-center gap-3">
        <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3">Welcome to <?php echo $title; ?></h2>

        <?php if ($introduction): ?>
          <p>
            <?php
            $introduction_no_links = preg_replace('/<a[^>]+>(.*?)<\/a>/i', '$1', $introduction);
            echo wp_kses_post($introduction_no_links);
            ?>
          </p>
        <?php endif; ?>

        <?php if ((!empty($phone) || !empty($email) || !empty($website) || !empty($address)) || !empty($social_icons)): ?>
          <div class="d-flex flex-column flex-md-row gap-md-5 gap-3">
            <?php if (!empty($phone) || !empty($email) || !empty($website) || !empty($address)): ?>
              <!-- Contact Details -->
              <div class="contact-details mb-4">
                <h3 class="fw-bold fs-5 mb-3">Contact Details</h3>

                <?php if ($phone) : ?>
                  <p class="d-flex align-items-center mb-3">
                    <i class="fas fa-phone-alt me-3 text-danger"></i>
                    <?php
                    if ($phone !== 'N/A') {
                      echo '<a class="text-decoration-none text-dark" href="tel:' . esc_attr(preg_replace('/\D+/', '', $phone)) . '">' . esc_html($phone) . '</a>';
                    }
                    if (!empty($additional_phones)) {
                      foreach ($additional_phones as $number) {
                        if ($number) {
                          echo ', <a class="text-decoration-none text-dark" href="tel:' . esc_attr(preg_replace('/\D+/', '', $number)) . '">' . esc_html($number) . '</a>';
                        }
                      }
                    }
                    ?>
                  </p>
                <?php endif; ?>

                <?php if ($email) : ?>
                  <p class="d-flex align-items-center mb-3">
                    <i class="fas fa-envelope me-3 text-danger"></i>
                    <a href="mailto:<?php echo esc_html($email); ?>"
                      class="text-decoration-none text-dark d-inline-block text-wrap"><?php echo esc_html($email); ?></a>
                  </p>
                <?php endif; ?>

                <?php if ($website) : ?>
                  <p class="d-flex align-items-center mb-3">
                    <i class="fas fa-globe me-3 text-danger"></i>
                    <a href="<?php echo esc_html($website); ?>?ref=kaha6.com"
                      class="text-decoration-none text-dark"><?php echo esc_html($website); ?></a>
                  </p>
                <?php endif; ?>

                <?php if ($address) : ?>
                  <p class="d-flex align-items-center">
                    <i class="fas fa-map-marker-alt me-3 text-danger"></i>
                    <span><?php echo esc_html($ait_locations_str_hierarchy . ", " . $address . (!empty($postalCode) ? ', ' . $postalCode : '')); ?></span>
                  </p>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <?php
            $has_valid_social_icons = false;
            if (!empty($social_icons)) {
              foreach ($social_icons as $icon) {
                if (!empty($icon['link']) && !empty($icon['icon'])) {
                  $has_valid_social_icons = true;
                  break;
                }
              }
            }
            if ($has_valid_social_icons) : ?>
              <div class="social-media mb-4">
                <h3 class="fw-bold fs-5 mb-3">Follow Us</h3>
                <div class="d-flex gap-3">
                  <?php for ($i = 0; $i <= 5; $i++) : ?>
                    <?php if (!empty($social_icons[$i]['link']) && !empty($social_icons[$i]['icon'])) : ?>
                      <a href="<?php echo esc_url($social_icons[$i]['link']); ?>/?ref=kaha6.com" class="text-decoration-none" target="_blank">
                        <i class="<?php echo esc_attr($social_icons[$i]['icon']); ?> d-inline fs-4"></i>
                      </a>
                    <?php endif; ?>
                  <?php endfor; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Opening Hours Start -->
      <?php
      $has_opening_hours = false;
      if (!empty($opening_hours) && is_array($opening_hours)) {
        foreach ($opening_hours as $day => $hours) {
          if (!empty($hours) && strtolower($hours) !== 'closed') {
            $has_opening_hours = true;
            break;
          }
        }
      }
      $has_note = !empty($opening_hours_note) && strtolower($opening_hours_note) !== 'n/a';
      ?>
      <?php if ($has_opening_hours || $has_note) : ?>
        <div class="col-12 col-lg-4">
          <div class="single-page-schedule-details bg-white p-4 d-flex flex-column align-items-center gap-3 shadow-sm border-0 rounded-4">
            <h3 class="text-capitalize fs-5">Opening Schedule</h3>
            <div class="d-flex flex-column gap-3">
              <?php foreach ($opening_hours as $day => $hours) : ?>
                <?php if (!empty($hours) && strtolower($hours) !== 'closed') : ?>
                  <div class="d-flex align-items-center justify-content-between flex-column flex-sm-row gap-2">
                    <h5 class="text-capitalize mb-0"><?php echo esc_html($day); ?></h5>
                    <div class="d-flex align-items-center text-muted gap-2">
                      <i class="fas fa-clock"></i>
                      <span><?php echo esc_html($hours); ?></span>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
              <?php if ($has_note) : ?>
                <hr class="my-1">
                <div class="d-flex align-items-center  flex-column flex-sm-row gap-3">
                  <h5 class="text-capitalize mb-0">Opening Hour Note: </h5>
                  <div class="d-flex align-items-center text-muted gap-2">
                    <span><?php echo esc_html($opening_hours_note); ?></span>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <!-- Opening Hours End -->

    </div>
  </div>
</section>
<!-- End About and Schedule Section Single Page -->

<!-- Banner -->
<?php get_template_part('ads/single/single', 'ad-one'); ?>
<!-- End Banner -->

<!-- Service Section Single Page -->
<?php if (!empty($company_services)): ?>
  <section class="service-section py-5">
    <div class="container">
      <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 mb-4">Our Services</h2>
      <div class="service-section-cards d-flex flex-wrap gap-3">
        <!-- card with background Image -->
        <?php foreach ($company_services as $service): ?>
          <div class="card h-100 shadow-sm border-0 rounded-4">
            <?php
            $image_url = !empty($service['servicesImgId']) ? wp_get_attachment_url($service['servicesImgId']) : get_template_directory_uri() . '/assets/images/fallback.png';
            if ($image_url):
            ?>
              <?php if (!empty($image_url)): ?>
                <img src="<?php echo esc_url($image_url); ?>" class="card-img-top" alt="<?php echo esc_html(get_the_title()); ?> Service Image">
              <?php endif; ?>
            <?php endif; ?>
            <div class="card-body d-flex flex-column justify-content-center">
              <?php if (!empty($service['services'])): ?>
                <h5 class="card-title text-white"><?php echo esc_html($service['services'] ?? ''); ?></h5>
              <?php endif; ?>
              <?php if (!empty($service['servicesDesc'])): ?>
                <p class="card-text"><?php echo esc_html($service['servicesDesc'] ?? ''); ?></p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
  </section>
<?php endif; ?>
<!-- End Service Section Single Page -->

<!-- Banner -->
<?php get_template_part('ads/single/single', 'ad-two'); ?>
<!-- End Banner -->

<!-- Google Map -->
<?php if ($latitude && $longitude): ?>
  <section class="google-map py-5">
    <div class="container">
      <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 mb-4 text-capitalize">Find Us</h2>
      <?php if ($location_breadcrumbs): ?>
        <?php echo $location_breadcrumbs; ?>
      <?php endif; ?>
      <div class="map-responsive">
        <iframe
          src="https://maps.google.it/maps?q=<?php echo $latitude; ?>, <?php echo $longitude; ?>&output=embed"
          width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>
    </div>
  </section>
<?php endif; ?>
<!-- End Google Map -->

<!-- Comment and Contact Details Section -->
<section class="comment-contact-details container">
  <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 mb-4">Rate Us</h2>

  <!-- Rating Section -->
  <?php get_template_part('parts/single/rating'); ?>
  <!-- End Rating Section -->

  <!-- Banner -->
  <?php get_template_part('ads/single/single', 'ad-three'); ?>
  <!-- End Banner -->

  <!-- Comment and Contact Form -->
  <section class="comment-contact-details container py-5">
    <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 mb-4">Leave a Comment</h2>
    <?php
    if (comments_open() || get_comments_number()) {
      comments_template();
    }
    ?>
  </section>

</section>
<!-- End Comment and Contact Details Section -->

<!-- Related Cards -->
<?php get_template_part('parts/common/related', 'companies'); ?>
<!-- End Related Cards -->

<!-- Banner -->
<?php get_template_part('ads/single/single', 'ad-four'); ?>
<!-- End Banner -->

<?php get_footer(); ?>