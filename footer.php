</main>
<!-- Footer -->
<footer>
  <div class="container-fluid bg-dark text-white pt-5 pb-4">
    <div class="container">
      <div class="footer-content">
        <div class="footer-logo-content text-center text-md-start">
          <a class="d-inline-block" href="<?php echo home_url('/'); ?>">
            <?php if (has_custom_logo()) : ?>
              <?php
              $custom_logo_id = get_theme_mod('custom_logo');
              $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
              ?>
              <img src="<?php echo esc_url($logo[0]); ?>" alt="<?php bloginfo('name'); ?>" height="70">
            <?php else : ?>
              <h3 class="text-primary">
                <?php bloginfo('name'); ?>
              </h3>
            <?php endif; ?>
          </a>
          <p class="mt-2 fs-6"><?php echo esc_html(get_theme_mod('setting_site_details11')); ?></p>
        </div>
        <div>
          <h5><?php echo esc_html(get_theme_mod('setting_site_details0')); ?></h5>
          <?php
          wp_nav_menu(array(
            'theme_location' => 'footer_one',
            'container' => false,
            'menu_class' => 'list-unstyled mt-4 d-flex gap-2 flex-column',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'link_class' => 'd-inline-block',
          ));
          ?>
        </div>
        <div>
          <h5><?php echo esc_html(get_theme_mod('setting_site_details1')); ?></h5>
          <ul class="list-unstyled d-flex gap-2 flex-column mt-4">
            <li class="d-flex align-items-center">
              <i class="fas fa-envelope me-2"></i><a href="mailto:<?php echo esc_html(get_theme_mod('setting_site_details5')); ?>"
                class="text-white d-inline-block"><?php echo esc_html(get_theme_mod('setting_site_details5')); ?></a>
            </li>
            <li class="d-flex align-items-center">
              <i class="fas fa-map-marker-alt me-2"></i>
              <p class="mb-0"><?php echo esc_html(get_theme_mod('setting_site_details3')); ?></p>
            </li>
          </ul>
        </div>
        <div>
          <h5>Follow Us</h5>
          <ul class="list-unstyled d-flex gap-3 flex-wrap mt-4">
            <li><a href="<?php echo esc_url(get_theme_mod('setting_site_details6')); ?>?ref=kaha6.com"><i
                  class="fab fs-3 fa-facebook-f"></i></a></li>
            <li><a href="<?php echo esc_url(get_theme_mod('setting_site_details7')); ?>?ref=kaha6.com"><i class="fab fs-3 fa-twitter"></i></a>
            </li>
            <li><a href="<?php echo esc_url(get_theme_mod('setting_site_details9')); ?>?ref=kaha6.com"><i class="fab fs-3 fa-instagram"></i></a>
            </li>
            <li><a href="<?php echo esc_url(get_theme_mod('setting_site_details8')); ?>?ref=kaha6.com"><i class="fab fs-3 fa-linkedin-in"></i></a>
            </li>
          </ul>
        </div>
      </div>
      <hr class="bg-secondary">
      <div class="d-flex justify-content-md-between gap-3 flex-wrap align-items-center copy-right">
        <p class="text-center mb-0">&copy; <?php echo date('Y'); ?> <?php echo esc_html(get_theme_mod('setting_site_details10')); ?></p>
        <p class="text-center mb-0">Designed & Developed by <a
            href="https://itservicenepal.com/?ref=kaha6.com" target="_blank" class="d-inline-block fw-bold"
            style="color: #C1272D;">ITSN</a></p>
      </div>
    </div>
  </div>
</footer>
<!-- End Footer -->

<!-- Bootstrap JS Bundle with Popper -->
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap.bundle.min.js"></script>
<!-- Swiper Js -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- select2js  -->
<script>
  jQuery(document).ready(function($) {
    $('#business-type').select2({
      placeholder: 'Business Type',
      allowClear: true,
      width: '100%'
    });

    $('#location').select2({
      placeholder: 'Location',
      allowClear: true,
      width: '100%'
    });
  });

  jQuery(document).ready(function($) {
    $('#homepageModal').on('shown.bs.modal', function() {
      $('#business-type-unique').select2({
        placeholder: 'Business Type',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#homepageModal')
      });

      $('#location-unique').select2({
        placeholder: 'Location',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#homepageModal')
      });
    });

    $('#homepageModal').on('hidden.bs.modal', function() {
      $('#business-type-unique').select2('destroy');
      $('#location-unique').select2('destroy');
    });
  });
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/common.js"></script>
<?php wp_footer(); ?>
</body>

</html>