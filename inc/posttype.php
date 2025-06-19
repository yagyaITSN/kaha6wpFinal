<?php
// Register Custom Post Type
add_action('init', 'custom_itsn_post');

function custom_itsn_post()
{
    $args = array(
        'labels' => array(
            'name' => __('Companies', 'itsn'),
            'singular_name' => __('Company', 'itsn'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_position' => 3,
        'menu_icon' => 'dashicons-analytics',
        'rewrite' => array('slug' => 'company'),
        'supports' => array('thumbnail', 'editor', 'title', 'custom-fields', 'comments', 'revisions', 'author', 'trackbacks', 'excerpt', 'page-attributes', 'post-formats'),
        'show_in_rest' => true, // Enable REST API for post type
        'show_ui' => true, // Ensure UI is enabled
        'show_in_menu' => true, // Ensure it appears in admin menu
        'show_in_nav_menus' => true,
        'taxonomies' => array('ait-items', 'ait-locations', 'itsn-devfilter'), // Explicitly associate taxonomies
    );

    register_post_type('ait-item', $args);
}

// Register Taxonomies
add_action('init', 'itsn_custom_tax', 0);

function itsn_custom_tax()
{
    // Taxonomy: Company Type
    register_taxonomy('ait-items', 'ait-item', array(
        'label' => __('Company Type', 'itsn'),
        'rewrite' => array('slug' => 'cat'),
        'hierarchical' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_in_rest' => true, // Enable REST API for taxonomy
        'show_admin_column' => true, // Display in admin post type table
    ));

    // Taxonomy: Location
    register_taxonomy('ait-locations', 'ait-item', array(
        'label' => __('Location', 'itsn'),
        'rewrite' => array('slug' => 'loc'),
        'hierarchical' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_in_rest' => true, // Enable REST API for taxonomy
        'show_admin_column' => true, // Display in admin post type table
    ));

    // Taxonomy: Dev Filter
    register_taxonomy('itsn-devfilter', 'ait-item', array(
        'label' => __('Dev Filter', 'itsn'),
        'rewrite' => array('slug' => 'devfilter'),
        'hierarchical' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_in_rest' => true, // Enable REST API for taxonomy
        'show_admin_column' => true, // Display in admin post type table
    ));
}

// Custom Columns for Post Type Admin Table
add_filter('manage_ait-item_posts_columns', 'my_custom_columns_list');

function my_custom_columns_list($columns)
{
    // Modify columns as needed, ensure taxonomies are not hidden
    $columns['author'] = __('Author', 'itsn');
    // Optionally add taxonomy columns if not added by show_admin_column
    $columns['taxonomy-ait-items'] = __('Company Type', 'itsn');
    $columns['taxonomy-ait-locations'] = __('Location', 'itsn');
    $columns['taxonomy-itsn-devfilter'] = __('Dev Filter', 'itsn');

    return $columns;
}

// Add Custom Meta Field to Taxonomy Add Form
add_action('ait-items_add_form_fields', 'mj_taxonomy_add_custom_meta_field', 10, 2);

function mj_taxonomy_add_custom_meta_field()
{
?>
    <div class="form-field">
        <label for="term_meta[class_term_meta]"><?php _e('Icon URL:', 'itsn'); ?></label>
        <input type="text" name="term_meta[class_term_meta]" id="term_meta[class_term_meta]" value="">
        <p class="description"><?php _e('Enter a value for this field', 'itsn'); ?></p>
    </div>
<?php
}

// Save Custom Meta Field (Optional, if not already implemented)
add_action('created_ait-items', 'mj_taxonomy_save_custom_meta', 10, 2);
add_action('edited_ait-items', 'mj_taxonomy_save_custom_meta', 10, 2);

function mj_taxonomy_save_custom_meta($term_id)
{
    if (isset($_POST['term_meta']['class_term_meta'])) {
        update_term_meta($term_id, 'class_term_meta', sanitize_text_field($_POST['term_meta']['class_term_meta']));
    }
}
?>