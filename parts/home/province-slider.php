<section class="province-section container py-5">
   <h2 class="fs-2 fw-bold mb-4 border-start border-4 border-danger ps-3">Browse by Province</h2>
   <div class="row g-2 align-items-center">
      <div class="col-12 col-md-6 col-lg-9">
         <div class="province-section-slider swiper mySwiper">
            <div class="swiper-wrapper my-5">
               <?php
               $terms = get_terms(array(
                  'taxonomy' => 'ait-locations',
                  'hide_empty' => false,
                  'parent'   => 0
               ));

               foreach ($terms as $term) {
                  $term_link = get_term_link($term);
                  $termID = $term->term_id;
                  $cat_img_key = 'ait-locations_category_' . $termID;
                  $cat_meta = get_option($cat_img_key);
                  // $cat_img = $cat_meta['icon'];
                  $taxonomy_image = get_term_meta($termID, 'taxonomy_image', true);

               ?>
                  <a class="swiper-slide p-2" href="<?php echo esc_url($term_link); ?>">
                     <img src="<?php echo esc_url($taxonomy_image); ?>" alt="<?php echo esc_html($term->name); ?>">
                  </a>
               <?php
               }
               ?>
            </div>
         </div>
      </div>
      <div class="province_side_data col-12 col-md-6 col-lg-3 shadow-sm border-0 rounded-4 bg-white p-4">
         <h3 class="display-7 fw-bold">Business Data</h3>
         <ul>
            <li>Total Businesses: 20,000+</li>
            <li>Verified Businesses: 6,000+</li>
            <li>New Listings This Month: 900+</li>
         </ul>
      </div>
   </div>
</section>