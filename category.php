<?php get_header(); ?>

<!-- Hero Section -->
<?php include 'parts/common/hero-section.php' ?>
<!-- End Hero Section -->

<!-- Banner  -->
<?php get_template_part('ads/category/category', 'ad-one'); ?>
<!-- End Banner  -->

<!-- Category Cards -->
<section class="category-section py-5">
  <div class="container">
    <div class="category-section-title d-flex align-items-center justify-content-between gap-3">
      <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 m-0">All Businesses</h2>
      <!-- Filter -->
      <div class="category_filter">
        <select id="ratingFilter">
          <option value="all">All Ratings</option>
          <option value="asc">Low to High</option>
          <option value="desc">High to Low</option>
        </select>
      </div>
    </div>
    <div class="category-cards mt-5">
      <?php
      $author_id = get_query_var('author');
      $current_locations = wp_get_post_terms(get_the_ID(), 'ait-locations', array('fields' => 'slugs'));
      $current_items = wp_get_post_terms(get_the_ID(), 'ait-items', array('fields' => 'slugs'));
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      $offset = ($paged - 1) * $posts_per_page;

      $args = array(
        'post_type' => 'ait-item',
        'author' => $author_id,
        'posts_per_page' => 24,
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
                break; // Only take the first child term
              }
            }
          }
      ?>
          <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden"
            data-rating="<?php echo esc_attr($rating); ?>">
            <img class="card-img-top pt-2" src="<?php echo esc_url($image); ?>"
              alt="<?php echo get_the_title(); ?>">
            <div class="card-body d-flex flex-column justify-content-between">
              <h5 class="card-title text-dark text-capitalize">
                <?php echo get_the_title(); ?>
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
            <div class="category-btn position-absolute d-flex align-items-center justify-content-center">
              <a class="btn btn-custom-red" href="<?php the_permalink(); ?>">View</a>
            </div>
          </div>
      <?php
        endwhile;
        wp_reset_postdata();
      else :
        echo '<p class="text-center">No companies found.</p>';
      endif;
      ?>
    </div>
    <!-- Pagination Start -->
    <div class="row pt-5 justify-content-center">
      <div class="col d-flex justify-content-center">
        <nav aria-label="Page navigation">
          <ul class="pagination mb-0">
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
              'mid_size' => 1,
              'end_size' => 1,
            );

            $paginate_links = paginate_links($pagination_args);
            if ($paginate_links) {
              foreach ($paginate_links as $link) {
                $is_current = strpos($link, 'current') !== false ? ' active' : '';
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
<!-- End Category Cards -->

<!-- Banner -->
<?php get_template_part('ads/category/category', 'ad-two'); ?>
<!-- End Banner -->

<!-- Top Cards -->
<section class="top-visited-section container py-5">
  <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 mb-4">Top Visited Businesses</h2>
  <div class="top-visited-slider swiper mySwiper">
    <div class="swiper-wrapper py-5">
      <?php
      $args = array(
        'post_type' => 'ait-item',
        'posts_per_page' => 15,
        'meta_key' => 'post_views_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
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
                break; // Only take the first child term
              }
            }
          }
      ?>
          <a href="<?php the_permalink(); ?>"
            class="swiper-slide card shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden">
            <img src="<?php echo esc_url($image); ?>" class="card-img-top"
              alt="<?php echo esc_html(get_the_title()); ?>">
            <div class="card-body d-flex flex-column justify-content-between">
              <h5 class="card-title text-dark text-capitalize"><?php echo esc_html(get_the_title()); ?></h5>
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
          </a>
      <?php
        endwhile;
        wp_reset_postdata();
      else :
        echo '<p class="text-center lh-lg fs-6">No companies found.</p>';
      endif;
      ?>
    </div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  </div>
</section>
<!-- End Top Cards -->

<?php get_footer(); ?>