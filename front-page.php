<?php get_header(); ?>

<!-- Hero Section -->
<?php get_template_part('parts/common/hero', 'section'); ?>
<!-- End Hero Section -->

<!-- Featured Businesses -->
<?php get_template_part('parts/home/business', 'slider-one') ?>
<!-- End Featured Businesses -->

<!-- Banner -->
<?php get_template_part('ads/home/home', 'ad-one'); ?>
<!-- End Banner -->

<!-- Province Section -->
<?php get_template_part('parts/home/province', 'slider'); ?>
<!-- End Province Section -->

<!-- Simple Process -->
<?php get_template_part('parts/home/process'); ?>
<!-- End Simple Process -->

<!-- Most visited business -->
<?php get_template_part('parts/home/business', 'slider-two') ?>
<!-- End Most visited business -->

<!-- Banner -->
<?php get_template_part('ads/home/home', 'ad-two'); ?>
<!-- End Banner -->

<!-- About -->
<?php get_template_part('parts/home/home', 'about'); ?>
<!-- End About -->

<!-- Popup -->
<?php get_template_part('parts/home/home', 'modal'); ?>
<!-- End Popup -->

<?php get_footer(); ?>