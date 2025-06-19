<?php
get_header();
$keyword = get_search_query();
$business_type = isset($_GET['business-type']) ? sanitize_text_field($_GET['business-type']) : '';
$location = isset($_GET['location']) ? sanitize_text_field($_GET['location']) : '';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
?>

<!-- Hero Section -->
<section class="contact-header py-5">
  <div class="container">
    <h1 class="fs-1 fw-bold text-center">Nepal's Largest Business Directory</h1>
    <p class="lead text-muted fs-6 mb-4 text-center">Find answers to common questions about Kaha6 Business Directory</p>
    <div class="row px-lg-5">
      <div class="col px-lg-5">
        <?php get_template_part('parts/common/search', 'form'); ?>
      </div>
    </div>
  </div>
</section>
<!-- End Hero Section -->

<!-- Category Cards -->
<section class="category-section pb-5">
  <div class="container">
    <div class="category-section-title d-flex align-items-center justify-content-between gap-3">
      <?php if (!empty($keyword)) : ?>
        <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 m-0">Search Results for "<?php echo esc_html($keyword); ?>"</h2>
      <?php elseif (!empty($business_type) || !empty($location)) : ?>
        <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 m-0">Search Results for <span class="text-custom-red">"<?php echo $business_type ?>"</span> and <span class="text-custom-red">"<?php echo $location; ?>"</span></h2>
      <?php else : ?>
        <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 m-0">All Businesses</h2>
      <?php endif; ?>

      <!-- Filter -->
      <?php get_template_part('parts/common/rating', 'filter'); ?>

    </div>

    <!-- Banner  -->
    <?php get_template_part('ads/category/category', 'ad-one') ?>
    <!-- End Banner  -->

    <div class="category-cards mt-5">
      <?php
      $args = array(
        'post_type' => 'ait-item',
        'posts_per_page' => 24,
        'paged' => $paged,
        's' => $keyword,
      );
      $tax_query = array('relation' => 'AND');
      if (!empty($business_type)) {
        $tax_query[] = array(
          'taxonomy' => 'ait-items',
          'field' => 'slug',
          'terms' => $business_type,
        );
      }

      if (!empty($location)) {
        $tax_query[] = array(
          'taxonomy' => 'ait-locations',
          'field' => 'slug',
          'terms' => $location,
        );
      }

      if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
      }
      $query = new WP_Query($args);

      if ($query->have_posts()) : ?>
        <?php
        while ($query->have_posts()) : $query->the_post();
          $img = has_post_thumbnail() ? get_the_post_thumbnail_url() : 'https://kaha6.com/wp-content/uploads/kaha6-no-image.png';
          $meta = get_post_meta(get_the_ID(), '_ait-item_item-data', true);
          $address = isset($meta['map']['address']) ? $meta['map']['address'] : '';
          $content = get_the_content();

          // Get average rating for this post
          global $wpdb;
          $table_name = $wpdb->prefix . 'rating_review';
          $rating = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(rating) FROM $table_name WHERE company_id = %d AND parent_id = 0",
            get_the_ID()
          ));
          $rating = $rating ? round($rating, 1) : 0;

          // Get terms for ait-locations taxonomy
          $location_terms = get_the_terms(get_the_ID(), 'ait-locations');
          $location_parent = '';
          $location_child = '';

          if ($location_terms && !is_wp_error($location_terms)) {
            foreach ($location_terms as $term) {
              if ($term->parent == 0) {
                $location_parent = $term->name;
              } else {
                $location_child = $term->name;
                if ($term->parent != 0) {
                  $parent_term = get_term($term->parent, 'ait-locations');
                  if ($parent_term && !is_wp_error($parent_term)) {
                    $location_parent = $parent_term->name;
                  }
                }
                break;
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
                $company_parent = $term->name;
              } else {
                $company_child = $term->name;
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
          <div class="card position-relative shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden"
            data-rating="<?php echo esc_attr($rating); ?>">
            <img class="card-img-top pt-2" src="<?php echo esc_url($img); ?>"
              alt="<?php echo esc_attr(get_the_title()); ?>">
            <div class="card-body d-flex flex-column justify-content-between">
              <h5 class="card-title text-dark text-capitalize"><?php echo esc_attr(get_the_title()); ?>
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
      else : ?>
        <div class="card-not-found text-center" id="noResults">
          <h5 class="fs-5 fw-bold">No businesses found matching your search criteria</h5>
          <p>Please try different search terms or filters</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Pagination Start -->
    <div class="row pt-5 justify-content-center">
      <div class="col d-flex justify-content-center">
        <nav aria-label="Page navigation">
          <ul class="pagination mb-0 card-pagination">
            <?php
            $pagination_args = array(
              'total' => $query->max_num_pages,
              'current' => max(1, $paged),
              'prev_text' => '«',
              'next_text' => '»',
              'type' => 'array',
              'mid_size' => 0,
              'end_size' => 0,
              'add_args' => array(
                's' => $keyword,
                'business-type' => $business_type,
                'location' => $location,
              ),
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
<!-- End Category Cards -->

<!-- Banner -->
<?php get_template_part('ads/category/category', 'ad-two'); ?>
<!-- End Banner -->

<!-- Top Cards -->
<?php get_template_part('parts/home/business', 'slider-two'); ?>
<!-- End Top Cards -->

<?php get_footer(); ?>