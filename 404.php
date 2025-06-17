<?php get_header(); ?>

<!-- Hero Section -->
<?php get_template_part('parts/common/hero', 'section'); ?>
<!-- End Hero Section -->

<!-- Banner  -->
<?php get_template_part('ads/category/category', 'ad-one'); ?>
<!-- End Banner  -->

<!-- 404 Content -->
<section>
  <div class="container py-5 text-center">
    <img class="mb-4" src="<?php echo get_template_directory_uri(); ?>/assets/images/404.png" alt="Kaha6 404 Image" height="550">
    <p>Sorry, the page you're looking for doesn't exist or has been moved.</p>
    <a href="<?php echo home_url('/') ?>" class="btn btn-custom-red">Go to Homepage</a>
  </div>
</section>
<!-- End 404 Content -->

<!-- Banner -->
<?php get_template_part('ads/category/category', 'ad-two'); ?>
<!-- End Banner -->

<!-- Top Cards -->
<?php get_template_part('parts/home/business', 'slider-two'); ?>
<!-- End Top Cards -->

<?php get_footer(); ?>