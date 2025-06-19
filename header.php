<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap 5 CSS -->
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/common.css?l=0">
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/itsn.css?l=1">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>
  <!-- Header Navigation -->
  <header>
    <nav class="navbar navbar-expand-lg">
      <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="<?php echo esc_url(home_url('/')); ?>">
          <?php if (has_custom_logo()) : ?>
            <?php
            $custom_logo_id = get_theme_mod('custom_logo');
            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
            ?>
            <img src="<?php echo esc_url($logo[0]); ?>" alt="<?php bloginfo('name'); ?>" height="40">
          <?php else : ?>
            <h3 class="text-primary">
              <?php bloginfo('name'); ?>
            </h3>
          <?php endif; ?>
        </a>

        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas"
          data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end offcanvas-nav" tabindex="-1" id="offcanvasNavbar">
          <div class="offcanvas-header">
            <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="offcanvas"
              aria-label="Close"></button>
          </div>

          <div class="offcanvas-body text-dark">
            <?php
            // Get top-level categories for ait-items taxonomy
            $top_level_terms = get_terms(array(
              'taxonomy' => 'ait-items',
              'hide_empty' => false,
              'parent' => 0,
            ));

            // Store all terms in a single array
            $cat = array();

            foreach ($top_level_terms as $term) {
              $cat[] = $term;
            }

            // Dynamically split terms into four groups for display
            $total_terms = count($cat);
            $terms_per_column = ceil($total_terms / 4);
            $columns = array_chunk($cat, $terms_per_column);

            // Get the current URL and queried term
            $current_url = rtrim(home_url(add_query_arg(array())), '/');
            $queried_object = get_queried_object();
            $current_term_slug = is_a($queried_object, 'WP_Term') && $queried_object->taxonomy === 'ait-items' ? $queried_object->slug : '';

            // Check if Business Type or Info dropdowns should be active
            $is_business_type_active = !empty($current_term_slug); // Active if any ait-items term is active
            $is_info_active = is_page(['about', 'privacy-policy', 'faq', 'contact']); // Active if any Info page is active

            // Define Bootstrap order classes for each column
            $column_orders = [
              0 => 'order-md-0',
              1 => 'order-md-2 order-lg-1',
              2 => 'order-md-1 order-lg-2',
              3 => 'order-md-3',
            ];

            // Helper function to normalize URLs for comparison
            function normalize_url($url)
            {
              return rtrim($url, '/');
            }
            ?>

            <ul class="navbar-nav justify-content-end text-center gap-3 flex-grow-1 itsn-header-nav">
              <!-- Home Menu Item -->
              <li class="nav-item">
                <a class="nav-link text-dark fw-medium <?php echo (is_front_page() || is_home()) ? 'active-menu-item2' : ''; ?>" href="<?php echo esc_url(home_url('/')); ?>">Home</a>
              </li>

              <!-- Mega Menu for Business Type -->
              <li class="nav-item dropdown dropdown-mega position-static">
                <a class="nav-link dropdown-toggle text-dark fw-medium <?php echo $is_business_type_active ? 'active-menu-item2' : ''; ?>" href="#" role="button"
                  data-bs-toggle="dropdown" aria-expanded="false" id="megaMenuDropdown">
                  Business Type
                </a>
                <div class="dropdown-menu w-100 mt-0 border-top-0 rounded-0 shadow-lg">
                  <div class="container py-4">
                    <div class="row">
                      <?php foreach ($columns as $index => $column) : ?>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0 <?php echo esc_attr($column_orders[$index]); ?>">
                          <ul class="list-unstyled mb-0">
                            <?php foreach ($column as $term) :
                              $term_link = normalize_url(get_term_link($term));
                              $taxonomy_icon = get_term_meta($term->term_id, 'taxonomy_icon', true);
                              $sub_terms = get_terms(array(
                                'taxonomy' => 'ait-items',
                                'hide_empty' => false,
                                'parent' => $term->term_id,
                              ));
                              // Check if the current term or any of its sub-terms is active
                              $is_active = ($current_term_slug === $term->slug || ($sub_terms && in_array($current_term_slug, array_column($sub_terms, 'slug'))) || strpos($current_url, '/cat/' . $term->slug . '/') !== false) ? 'active-menu-item' : '';
                            ?>
                              <?php if (!empty($sub_terms)) : ?>
                                <li class="dropdown-submenu">
                                  <a class="dropdown-item dropdown-toggle <?php echo $is_active; ?>" href="#"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php if (!empty($taxonomy_icon)) : ?>
                                      <i class="<?php echo esc_attr($taxonomy_icon); ?> me-2"></i>
                                    <?php endif; ?>
                                    <?php echo esc_html($term->name); ?>
                                  </a>
                                  <ul class="dropdown-menu">
                                    <!-- Parent category link at the top, without active class -->
                                    <li>
                                      <a class="dropdown-item" href="<?php echo esc_url($term_link); ?>">
                                        <?php if (!empty($taxonomy_icon)) : ?>
                                          <i class="<?php echo esc_attr($taxonomy_icon); ?> me-2"></i>
                                        <?php endif; ?>
                                        <?php echo esc_html($term->name); ?>
                                      </a>
                                    </li>
                                    <!-- Subcategories with ps-2 for indentation -->
                                    <?php foreach ($sub_terms as $sub_term) :
                                      $sub_term_link = normalize_url(get_term_link($sub_term));
                                      $sub_is_active = ($current_term_slug === $sub_term->slug || strpos($current_url, '/cat/' . $sub_term->slug . '/') !== false) ? 'active-menu-item' : '';
                                    ?>
                                      <li>
                                        <a class="dropdown-item ps-4 <?php echo $sub_is_active; ?>" href="<?php echo esc_url($sub_term_link); ?>">
                                          <?php echo esc_html($sub_term->name); ?>
                                        </a>
                                      </li>
                                    <?php endforeach; ?>
                                  </ul>
                                </li>
                              <?php else : ?>
                                <li>
                                  <a class="dropdown-item <?php echo $is_active; ?>" href="<?php echo esc_url($term_link); ?>">
                                    <?php if (!empty($taxonomy_icon)) : ?>
                                      <i class="<?php echo esc_attr($taxonomy_icon); ?> me-2"></i>
                                    <?php endif; ?>
                                    <?php echo esc_html($term->name); ?>
                                  </a>
                                </li>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </ul>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              </li>

              <!-- Blog Menu Item -->
              <li class="nav-item">
                <a class="nav-link text-dark fw-medium <?php echo is_page('blog') ? 'active-menu-item2' : ''; ?>" href="<?php echo esc_url(home_url('/blog')); ?>">Blog</a>
              </li>

              <!-- Info Dropdown Menu -->
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fw-medium <?php echo $is_info_active ? 'active-menu-item2' : ''; ?>" href="#" role="button"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  Info
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item <?php echo is_page('about') ? 'active-menu-item2' : ''; ?>" href="<?php echo esc_url(home_url('/about/')); ?>">
                      <i class="fas fa-info-circle me-2"></i>About</a></li>
                  <li><a class="dropdown-item <?php echo is_page('privacy-policy') ? 'active-menu-item2' : ''; ?>" href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">
                      <i class="fas fa-shield-alt me-2"></i>Privacy Policy</a></li>
                  <li><a class="dropdown-item <?php echo is_page('faq') ? 'active-menu-item2' : ''; ?>" href="<?php echo esc_url(home_url('/faq/')); ?>">
                      <i class="fas fa-question-circle me-2"></i>FAQ</a></li>
                  <li><a class="dropdown-item <?php echo is_page('contact') ? 'active-menu-item2' : ''; ?>" href="<?php echo esc_url(home_url('/contact/')); ?>">
                      <i class="fas fa-user me-2"></i>Contact</a></li>
                </ul>
              </li>

              <!-- Register Button -->
              <li class="nav-item" id="itsn_reg">
                <a href="<?php echo esc_url(home_url('/register-to-submit-your-company')); ?>" class="btn btn-custom-red">
                  Add Your Business
                </a>
              </li>
            </ul>

            <!-- User Menu -->
            <div class="user__menu-icon dropdown d-lg-none mt-3 text-center">
              <?php get_template_part('parts/common/profile', 'avatar'); ?>
            </div>
          </div>
        </div>

        <!-- User Menu (Desktop) -->
        <div class="user__menu-icon dropdown d-none d-lg-block">
          <?php get_template_part('parts/common/profile', 'avatar'); ?>
        </div>
      </div>
    </nav>
  </header>
  <!-- End Header Navigation -->
  <main>