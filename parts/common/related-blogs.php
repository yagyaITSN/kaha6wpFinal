<section class="related-card-section container py-5">
   <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 mb-4">Related Blogs</h2>
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
            echo '<p class="fs-6 lh-lg text-center">No Related Blogs Found<br/>Try another options</p>';
         endif;
         ?>
      </div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
   </div>
</section>