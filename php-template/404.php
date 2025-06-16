<?php include 'header.php'; ?>

<!-- Hero Section -->
<?php include 'parts/common/hero-section.php' ?>
<!-- End Hero Section -->

<!-- Banner  -->
<?php include 'ads/category/category-ad-one.php' ?>
<!-- End Banner  -->

<!-- 404 Content -->
<section>
  <div class="container py-5 text-center">
    <img class="mb-4" src="assets/images/404.png" alt="Kaha6 404 Image" height="550">
    <p>Sorry, the page you're looking for doesn't exist or has been moved.</p>
    <a href="<?php echo home_url('/') ?>" class="btn btn-custom-red">Go to Homepage</a>
  </div>
</section>
<!-- End 404 Content -->

<!-- Banner -->
<?php include 'ads/category/category-ad-two.php' ?>
<!-- End Banner -->

<!-- Top Cards -->
<?php include 'parts/home/business-slider-two.php' ?>
<!-- End Top Cards -->

<?php include 'footer.php'; ?>