<?php get_header(); ?>

<!-- Hero Section -->
<?php get_template_part('parts/common/hero', 'section'); ?>
<!-- End Hero Section -->

<!-- Featured Businesses -->
<?php require 'parts/home/business-slider-one.php';








get_template_part('parts/home/business', 'slider-one')



?>
<!-- End Featured Businesses -->

<!-- Banner -->
<?php require 'ads/home/home-ad-one.php'; ?>
<!-- End Banner -->

<!-- Province Section -->
<?php require 'parts/home/province-slider.php'; ?>
<!-- End Province Section -->

<!-- Simple Process -->
<?php require 'parts/home/process.php'; ?>
<!-- End Simple Process -->

<!-- Most visited business -->
<?php require 'parts/home/business-slider-two.php'; ?>
<!-- End Most visited business -->

<!-- Banner -->
<?php require 'ads/home/home-ad-two.php'; ?>
<!-- End Banner -->

<!-- About -->
<?php require 'parts/home/home-about.php'; ?>
<!-- End About -->

<!-- Popup -->
<?php require 'parts/home/modal.php'; ?>
<!-- End Popup -->


<?php get_footer(); ?>