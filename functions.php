<?php
require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';
require 'inc/customizer.php';

// Start the session
function start_session()
{
  if (!session_id()) {
    session_start();
  }
}
add_action('init', 'start_session', 1);

// Save the referer URL to the session
function save_referer_url()
{
  if (!is_user_logged_in()) {
    $_SESSION['referer_url'] = wp_get_referer();
  } else {
    session_destroy();
  }
}
add_action('template_redirect', 'save_referer_url');

// Redirect to the referer URL after login
function login_redirect($redirect_to, $requested_redirect_to, $user)
{
  if (isset($_SESSION['referer_url'])) {
    $redirect_to = $_SESSION['referer_url'];
    unset($_SESSION['referer_url']);
  }
  return $redirect_to;
}
add_filter('login_redirect', 'login_redirect', 10, 3);


add_action('after_setup_theme', 'blankslate_setup');
function blankslate_setup()
{
  load_theme_textdomain('blankslate', get_template_directory() . '/languages');
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_image_size('custom_grid_img_size', 245, 150, false);
  add_theme_support('responsive-embeds');
  add_theme_support('automatic-feed-links');
  add_theme_support('html5', array('search-form', 'navigation-widgets'));
  add_theme_support('appearance-tools');
  add_theme_support('woocommerce');
  add_theme_support('custom-logo');
  global $content_width;
  if (!isset($content_width)) {
    $content_width = 1920;
  }
  register_nav_menus(array('header_menu' => esc_html__('Header Menu', 'blankslate')));
  register_nav_menus(array('footer_one' => esc_html__('Footer Menu One', 'blankslate')));
  register_nav_menus(array('footer_two' => esc_html__('Footer Menu Two', 'blankslate')));
}

add_action('wp_enqueue_scripts', 'blankslate_enqueue');
function blankslate_enqueue()
{
  wp_enqueue_style('blankslate-style', get_stylesheet_uri());
  wp_enqueue_script('jquery');
  wp_script_add_data('html5hiv', 'conditional', 'lt IE 9');
  wp_enqueue_script(
    'bcv-claim-script',
    get_theme_file_uri('/assets/js/bcv-claim.js'),
    ['jquery'],
    '112.0',
    true
  );
  wp_localize_script(
    'bcv-claim-script',
    'bcv_ajax',
    [
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('bcv_claim_nonce')
    ]
  );
}
add_action('wp_footer', 'blankslate_footer');
function blankslate_footer()
{
?>
  <script>
    jQuery(document).ready(function($) {
      var deviceAgent = navigator.userAgent.toLowerCase();
      if (deviceAgent.match(/(iphone|ipod|ipad)/)) {
        $("html").addClass("ios");
        $("html").addClass("mobile");
      }
      if (deviceAgent.match(/(Android)/)) {
        $("html").addClass("android");
        $("html").addClass("mobile");
      }
      if (navigator.userAgent.search("MSIE") >= 0) {
        $("html").addClass("ie");
      } else if (navigator.userAgent.search("Chrome") >= 0) {
        $("html").addClass("chrome");
      } else if (navigator.userAgent.search("Firefox") >= 0) {
        $("html").addClass("firefox");
      } else if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
        $("html").addClass("safari");
      } else if (navigator.userAgent.search("Opera") >= 0) {
        $("html").addClass("opera");
      }
    });
  </script>
<?php
}
add_filter('document_title_separator', 'blankslate_document_title_separator');
function blankslate_document_title_separator($sep)
{
  $sep = esc_html('|');
  return $sep;
}
add_filter('the_title', 'blankslate_title');
function blankslate_title($title)
{
  if ($title == '') {
    return esc_html('...');
  } else {
    return wp_kses_post($title);
  }
}
function blankslate_schema_type()
{
  $schema = 'https://schema.org/';
  if (is_single()) {
    $type = "Article";
  } elseif (is_author()) {
    $type = 'ProfilePage';
  } elseif (is_search()) {
    $type = 'SearchResultsPage';
  } else {
    $type = 'WebPage';
  }
  echo 'itemscope itemtype="' . esc_url($schema) . esc_attr($type) . '"';
}
add_filter('nav_menu_link_attributes', 'blankslate_schema_url', 10);
function blankslate_schema_url($atts)
{
  $atts['itemprop'] = 'url';
  return $atts;
}
if (!function_exists('blankslate_wp_body_open')) {
  function blankslate_wp_body_open()
  {
    do_action('wp_body_open');
  }
}
add_action('wp_body_open', 'blankslate_skip_link', 5);
function blankslate_skip_link()
{
  echo '<a href="#content" class="skip-link screen-reader-text">' . esc_html__('Skip to the content', 'blankslate') . '</a>';
}
add_filter('the_content_more_link', 'blankslate_read_more_link');
function blankslate_read_more_link()
{
  if (!is_admin()) {
    return ' <a href="' . esc_url(get_permalink()) . '" class="more-link">' . sprintf(__('...%s', 'blankslate'), '<span class="screen-reader-text">  ' . esc_html(get_the_title()) . '</span>') . '</a>';
  }
}
add_filter('excerpt_more', 'blankslate_excerpt_read_more_link');
function blankslate_excerpt_read_more_link($more)
{
  if (!is_admin()) {
    global $post;
    return ' <a href="' . esc_url(get_permalink($post->ID)) . '" class="more-link">' . sprintf(__('...%s', 'blankslate'), '<span class="screen-reader-text">  ' . esc_html(get_the_title()) . '</span>') . '</a>';
  }
}
add_filter('big_image_size_threshold', '__return_false');
add_filter('intermediate_image_sizes_advanced', 'blankslate_image_insert_override');
function blankslate_image_insert_override($sizes)
{
  unset($sizes['medium_large']);
  unset($sizes['1536x1536']);
  unset($sizes['2048x2048']);
  return $sizes;
}
add_action('widgets_init', 'blankslate_widgets_init');
function blankslate_widgets_init()
{
  register_sidebar(array(
    'name' => esc_html__('Sidebar Widget Area', 'blankslate'),
    'id' => 'primary-widget-area',
    'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
    'after_widget' => '</li>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>',
  ));
}
add_action('wp_head', 'blankslate_pingback_header');
function blankslate_pingback_header()
{
  if (is_singular() && pings_open()) {
    printf('<link rel="pingback" href="%s">' . "\n", esc_url(get_bloginfo('pingback_url')));
  }
}
add_action('comment_form_before', 'blankslate_enqueue_comment_reply_script');
function blankslate_enqueue_comment_reply_script()
{
  if (get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
}
function blankslate_custom_pings($comment)
{
?>
  <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo esc_url(comment_author_link()); ?></li>
<?php
}
add_filter('get_comments_number', 'blankslate_comment_count', 0);
function blankslate_comment_count($count)
{
  if (!is_admin()) {
    global $id;
    $get_comments = get_comments('status=approve&post_id=' . $id);
    $comments_by_type = separate_comments($get_comments);
    return count($comments_by_type['comment']);
  } else {
    return $count;
  }
}

add_filter('single_template', function ($single_template) {
  global $post;
  if ($post->post_type === 'post') {
    $locate_template = locate_template('single-blog.php');
    if ($locate_template) {
      return $locate_template;
    }
  }
  return $single_template;
});

require get_template_directory() . '/inc/posttype.php';
require get_template_directory() . '/inc/customfield.php';
require_once 'inc/functions.php';
require_once 'inc/mailfunc.php';
require_once 'inc/custom-mail.php';
require_once 'inc/ads.php';


// Register the admin settings page
function adrotate_custom_admin_menu()
{
  add_options_page(
    'Custom Ads',
    'Custom Ads',
    'manage_options',
    'adrotate-custom-banners',
    'adrotate_custom_settings_page'
  );
}
add_action('admin_menu', 'adrotate_custom_admin_menu');

// Register settings
function adrotate_custom_register_settings()
{
  register_setting(
    'adrotate_custom_options_group',
    'adrotate_custom_options',
    'adrotate_custom_sanitize_options'
  );

  add_settings_section(
    'adrotate_custom_main_section',
    'Banner ID Settings',
    'adrotate_custom_section_callback',
    'adrotate-custom-banners'
  );

  for ($i = 1; $i <= 10; $i++) {
    add_settings_field(
      "custom_ad_$i",
      "Banner ID $i",
      'adrotate_custom_field_callback',
      'adrotate-custom-banners',
      'adrotate_custom_main_section',
      array('field_id' => "custom_ad_$i")
    );
  }
}
add_action('admin_init', 'adrotate_custom_register_settings');

// Section callback
function adrotate_custom_section_callback()
{
  echo '<p>Enter the AdRotate banner IDs for each position. Separate multiple IDs with commas.</p>';
  echo "To display the ad in the frontend use the code below. Use the snippet where you want them to be displayed:<br><pre>&lt;?php
\$options = get_option('adrotate_custom_options');
\$adid = isset(\$options['custom_ad_1']) ? \$options['custom_ad_1'] : '';
if (\$adid) {
    render_adrotate_banners(\$adid);
}
?&gt;</pre>";
}

// Field callback
function adrotate_custom_field_callback($args)
{
  $options = get_option('adrotate_custom_options');
  $field_id = $args['field_id'];
  $value = isset($options[$field_id]) ? esc_attr($options[$field_id]) : '';
  echo "<input type='text' name='adrotate_custom_options[$field_id]' value='$value' class='regular-text' />";
}

// Sanitize inputs
function adrotate_custom_sanitize_options($input)
{
  $sanitized_input = array();
  for ($i = 1; $i <= 10; $i++) {
    $field_id = "custom_ad_$i";
    if (isset($input[$field_id])) {
      // Sanitize as comma-separated numbers
      $value = sanitize_text_field($input[$field_id]);
      // Ensure only numbers and commas
      $value = preg_replace('/[^0-9,]/', '', $value);
      $sanitized_input[$field_id] = $value;
    }
  }
  return $sanitized_input;
}

// Settings page render
function adrotate_custom_settings_page()
{
?>
  <div class="wrap">
    <h1>Custom Ads</h1>
    <form method="post" action="options.php">
      <?php
      settings_fields('adrotate_custom_options_group');
      do_settings_sections('adrotate-custom-banners');
      submit_button();
      ?>
    </form>
  </div>
  <?php
}

// Render function
function render_adrotate_banners($ids)
{
  $id_array = explode(',', $ids);
  foreach ($id_array as $id) {
    $id = trim($id);
    $ad_content = do_shortcode('[adrotate banner="' . $id . '"]');
    if (!empty($ad_content)) {
  ?>
      <!-- badba -->
      <section class="py-2 py-md-3 container px-0">
        <?php echo $ad_content; ?>
      </section>
<?php
    }
  }
}




// Handle rating submission
add_action('wp_ajax_submit_rating', 'handle_rating_submission');
add_action('wp_ajax_nopriv_submit_rating', 'handle_rating_submission');

function handle_rating_submission()
{
  if (!isset($_POST['rating_nonce']) || !wp_verify_nonce($_POST['rating_nonce'], 'submit_rating_nonce')) {
    wp_send_json_error('Invalid nonce');
  }

  if (!is_user_logged_in()) {
    wp_send_json_error('You must be logged in to submit a rating');
  }

  $post_id = intval($_POST['post_id']);
  $rating = intval($_POST['pixrating']);
  $title = sanitize_text_field($_POST['pixrating_title']);
  $user = wp_get_current_user();

  $comment_data = array(
    'comment_post_ID' => $post_id,
    'comment_author' => $user->display_name,
    'comment_author_email' => $user->user_email,
    'comment_content' => '', // We're not storing the comment content
    'comment_type' => '',
    'comment_parent' => 0,
    'user_id' => $user->ID,
    'comment_approved' => 1,
  );

  $comment_id = wp_insert_comment($comment_data);

  if ($comment_id) {
    update_comment_meta($comment_id, 'pixrating', $rating);
    update_comment_meta($comment_id, 'pixrating_title', $title);
    wp_send_json_success();
  } else {
    wp_send_json_error('Failed to submit rating');
  }
}

// Handle reply submission
add_action('wp_ajax_submit_reply', 'handle_reply_submission');
add_action('wp_ajax_nopriv_submit_reply', 'handle_reply_submission');

function handle_reply_submission()
{
  if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'submit_reply_nonce')) {
    wp_send_json_error('Invalid nonce');
  }

  if (!is_user_logged_in()) {
    wp_send_json_error('You must be logged in to reply');
  }

  $comment_id = intval($_POST['comment_id']);
  $reply_text = sanitize_text_field($_POST['reply_text']);
  $post_id = intval($_POST['post_id']);
  $user = wp_get_current_user();

  // Only allow post author to reply
  if ($user->ID != get_post_field('post_author', $post_id)) {
    wp_send_json_error('Only the post author can reply');
  }

  $comment_data = array(
    'comment_post_ID' => $post_id,
    'comment_author' => $user->display_name,
    'comment_author_email' => $user->user_email,
    'comment_content' => $reply_text,
    'comment_type' => '',
    'comment_parent' => $comment_id,
    'user_id' => $user->ID,
    'comment_approved' => 1,
  );

  $reply_id = wp_insert_comment($comment_data);

  if ($reply_id) {
    wp_send_json_success();
  } else {
    wp_send_json_error('Failed to submit reply');
  }
}
