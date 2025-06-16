<section class="featured-section container py-5">
   <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3">Featured Businesses</h2>
      <a href="./category_page.html" class="btn btn-custom-red">View All Businesses</a>
   </div>
   <div class="featured-section-slider swiper mySwiper">
      <div class="swiper-wrapper py-5">

         <?php
         $args = array(
            'post_type' => 'ait-item',
            'posts_per_page' => 15
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
                     alt="<?php echo esc_attr(get_the_title()); ?>">
                  <div class="card-body d-flex flex-column justify-content-between">
                     <h5 class="card-title text-dark text-capitalize"><?php echo esc_attr(get_the_title()); ?></h5>
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