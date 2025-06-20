<?php
add_action('admin_menu', 'custom_mails_by_admin');

function custom_mails_by_admin()
{
    add_menu_page(
        'Custom Email',
        'Custom Email',
        'manage_options',
        'custom-email',
        'devTools',
        'dashicons-admin-tools',
        30
    );
}

// Enqueue Bootstrap and Chosen.js for styling and enhanced select inputs
add_action('admin_enqueue_scripts', 'enqueue_dev_tools_scripts');
function enqueue_dev_tools_scripts($hook)
{
    if ($hook !== 'toplevel_page_custom-email') {
        return;
    }
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css');
    wp_enqueue_style('chosen', 'https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css');
    wp_enqueue_script('chosen', 'https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js', ['jquery'], null, true);
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.bundle.min.js', [], null, true);
}

// Email queue processing action
add_action('kaha6_process_email_queue', 'kaha6_process_email_queue_callback');
function kaha6_process_email_queue_callback()
{
    $queue = get_transient('kaha6_email_queue');
    if (empty($queue)) {
        wp_clear_scheduled_hook('kaha6_process_email_queue');
        return;
    }

    // Process the first email in the queue
    $email = array_shift($queue);
    wp_mail($email['recipients'], $email['subject'], $email['message'], $email['headers']);

    // Update the queue
    if (empty($queue)) {
        delete_transient('kaha6_email_queue');
        wp_clear_scheduled_hook('kaha6_process_email_queue');
    } else {
        set_transient('kaha6_email_queue', $queue, HOUR_IN_SECONDS);
    }
}

// Function to get hierarchical terms with indentation
function get_hierarchical_terms($taxonomy, $parent = 0, $level = 0)
{
    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'parent' => $parent,
        'hide_empty' => false,
    ]);
    $output = [];
    foreach ($terms as $term) {
        $output[] = [
            'id' => $term->term_id,
            'name' => str_repeat('--', $level) . ' ' . $term->name,
        ];
        $child_terms = get_hierarchical_terms($taxonomy, $term->term_id, $level + 1);
        $output = array_merge($output, $child_terms);
    }
    return $output;
}

// Register custom schedule for 5-second intervals
add_filter('cron_schedules', 'kaha6_add_five_seconds_schedule');
function kaha6_add_five_seconds_schedule($schedules)
{
    $schedules['kaha6_five_seconds'] = [
        'interval' => 5,
        'display' => __('Every 5 Seconds', 'kaha6'),
    ];
    return $schedules;
}

// Main Dev Tools page function
function devTools()
{
    $current_user = wp_get_current_user();
    //     echo '<pre>Current User Data: ';
    //     print_r($current_user);
    //     echo '</pre>';

    $name = $current_user->display_name;
    $message = '';

    // Fetch data for dropdowns before form rendering
    $users = get_users();
    //     echo '<pre>All Users for Dropdown: ';
    //     print_r($users);
    //     echo '</pre>';

    $args = [
        'post_type' => 'ait-item',
        'posts_per_page' => -1,
        'post_status' => ['publish', 'draft', 'under_reverification'],
    ];
    //     $businesses = get_posts($args);
    //     echo '<pre>All Businesses for Dropdown: ';
    // print_r($businesses);
    //     echo '</pre>';

    $categories = get_hierarchical_terms('ait-items');
    //     echo '<pre>Categories for Dropdown: ';
    //     print_r($categories);
    //     echo '</pre>';

    $locations = get_hierarchical_terms('ait-locations');
    //     echo '<pre>Locations for Dropdown: ';
    //     print_r($locations);
    //     echo '</pre>';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_email']) && check_admin_referer('send_email_nonce')) {
        $subject = sanitize_text_field($_POST['email_subject']);
        $content = wp_kses_post($_POST['email_content']);
        $recipient_type = sanitize_text_field($_POST['recipient_type']);
        $queue = get_transient('kaha6_email_queue') ?: [];
        $recipients = [];

        // Get logo for email
        $custom_logo_id = get_theme_mod('custom_logo');
        $image = wp_get_attachment_image_src($custom_logo_id, 'full');
        $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/logo.png';

        // Global email styles and footer
        global $mailStyles, $mailFooter;
        $mailStyles = $mailStyles ?: '
            body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
            .container { max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 8px; }
            .header { text-align: center; padding: 10px 0; }
            .content { padding: 20px; }
            a { color: #C1272D; }
        ';
        $mailFooter = '
            <td class="footer">
                <div class="social-icons">
                    <a href="' . esc_url(get_theme_mod('setting_site_details6', '#')) . '" target="_blank">
                        <img src="' . esc_url(get_template_directory_uri() . '/assets/images/fb.png') . '" alt="Facebook" height="15">
                    </a>
                    <a href="' . esc_url(get_theme_mod('setting_site_details7', '#')) . '" target="_blank">
                        <img src="' . esc_url(get_template_directory_uri() . '/assets/images/x.png') . '" alt="X" height="15">
                    </a>
                    <a href="' . esc_url(get_theme_mod('setting_site_details8', '#')) . '" target="_blank">
                        <img src="' . esc_url(get_template_directory_uri() . '/assets/images/linkedin.png') . '" alt="LinkedIn" height="15">
                    </a>
                    <a href="' . esc_url(get_theme_mod('setting_site_details9', '#')) . '" target="_blank">
                        <img src="' . esc_url(get_template_directory_uri() . '/assets/images/instagram.png') . '" alt="Instagram" height="15">
                    </a>
                </div>
                <div class="footer-bottom">
                    <p style="margin-bottom: 4px; font-size: 14px;">
                        <img src="' . esc_url(get_template_directory_uri() . '/assets/images/pin.png') . '" alt="Location" height="12">
                        ' . esc_html(get_theme_mod('setting_site_details3', 'Biratnagar, Munalpath')) . '
                    </p>
                    <a href="mailto:' . esc_attr(get_theme_mod('setting_site_details5', 'info@kaha6.com')) . '"
                        style="text-decoration: none; text-align: center; color: #1a1a1a; font-size: 14px;">
                        <img src="' . esc_url(get_template_directory_uri() . '/assets/images/envelope.png') . '" alt="Email" height="12">
                        ' . esc_html(get_theme_mod('setting_site_details5', 'info@kaha6.com')) . '
                    </a>
                </div>
            </td>';

        // Build email headers
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
            'Reply-To: no-reply@kaha6.com'
        ];

        // Build email template
        $message_template = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . esc_html($subject) . '</title>
            <style>' . $mailStyles . '</style>
        </head>
        <body>
            <table class="container" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="header">
                        <img src="' . $logo_url . '" alt="Logo" style="display: block; margin: 10px auto; max-width: 100px">
                    </td>
                </tr>
                <tr>
                    <td class="content">
                        <h1>Dear {USER_NAME}</h1>
                        ' . $content . '
                        <p>Regards, <strong>
                            <a href="' . esc_url(home_url('/')) . '" style="text-decoration: none; color: #C1272D;">
                                Kaha6
                            </a>
                        </strong></p>
                        <div style="border-bottom: 1px solid #C1272D; width: 80%; margin: 20px auto;"></div>
                    </td>
                </tr>
                <tr>' . $mailFooter . '</tr>
            </table>
        </body>
        </html>';

        // Determine recipients and queue emails
        switch ($recipient_type) {
            case 'multiple_users':
                $user_ids = isset($_POST['user_ids']) ? array_map('intval', (array)$_POST['user_ids']) : [];
                if ($user_ids) {
                    foreach ($user_ids as $user_id) {
                        $user = get_userdata($user_id);
                        if ($user) {
                            $temp_message = str_replace('{USER_NAME}', esc_html($user->display_name), $message_template);
                            $queue[] = [
                                'recipients' => [$user->user_email],
                                'subject' => $subject,
                                'message' => $temp_message,
                                'headers' => $headers,
                            ];
                            $recipients[] = $user->user_email;
                        }
                    }
                }
                break;

            case 'multiple_businesses':
                $post_ids = isset($_POST['post_ids']) ? array_map('intval', (array)$_POST['post_ids']) : [];
                if ($post_ids) {
                    $args = [
                        'post_type' => 'ait-item',
                        'posts_per_page' => -1,
                        'post_status' => ['publish', 'draft', 'under_reverification'],
                        'post__in' => $post_ids,
                    ];
                    $businesses = get_posts($args);
                    foreach ($businesses as $business) {
                        $user = get_userdata($business->post_author);
                        $business_email = get_post_meta($business->ID, '_ait-item_item-data', true)['email'] ?? '';
                        $email_recipients = [];
                        if ($user) {
                            $email_recipients[] = $user->user_email;
                            $recipients[] = $user->user_email;
                        }
                        if (is_email($business_email)) {
                            $email_recipients[] = $business_email;
                            $recipients[] = $business_email;
                        }
                        if ($email_recipients) {
                            $temp_message = str_replace('{USER_NAME}', esc_html($user->display_name ?? 'Business Owner'), $message_template);
                            $queue[] = [
                                'recipients' => array_unique($email_recipients),
                                'subject' => $subject,
                                'message' => $temp_message,
                                'headers' => $headers,
                            ];
                        }
                    }
                }
                break;

            case 'all_users':
                $users = get_users();
                foreach ($users as $user) {
                    $temp_message = str_replace('{USER_NAME}', esc_html($user->display_name), $message_template);
                    $queue[] = [
                        'recipients' => [$user->user_email],
                        'subject' => $subject,
                        'message' => $temp_message,
                        'headers' => $headers,
                    ];
                    $recipients[] = $user->user_email;
                }
                break;

            case 'all_businesses':
                $args = [
                    'post_type' => 'ait-item',
                    'posts_per_page' => -1,
                    'post_status' => ['publish', 'draft', 'under_reverification'],
                ];
                $businesses = get_posts($args);
                foreach ($businesses as $business) {
                    $user = get_userdata($business->post_author);
                    $business_email = get_post_meta($business->ID, '_ait-item_item-data', true)['email'] ?? '';
                    $email_recipients = [];
                    if ($user) {
                        $email_recipients[] = $user->user_email;
                        $recipients[] = $user->user_email;
                    }
                    if (is_email($business_email)) {
                        $email_recipients[] = $business_email;
                        $recipients[] = $business_email;
                    }
                    if ($email_recipients) {
                        $temp_message = str_replace('{USER_NAME}', esc_html($user->display_name ?? 'Business Owner'), $message_template);
                        $queue[] = [
                            'recipients' => array_unique($email_recipients),
                            'subject' => $subject,
                            'message' => $temp_message,
                            'headers' => $headers,
                        ];
                    }
                }
                break;

            case 'category_businesses':
                $category_ids = isset($_POST['category_ids']) ? array_map('intval', (array)$_POST['category_ids']) : [];
                if ($category_ids) {
                    $args = [
                        'post_type' => 'ait-item',
                        'posts_per_page' => -1,
                        'post_status' => ['publish', 'draft', 'under_reverification'],
                        'tax_query' => [
                            [
                                'taxonomy' => 'ait-items',
                                'field' => 'term_id',
                                'terms' => $category_ids,
                            ],
                        ],
                    ];
                    $businesses = get_posts($args);
                    foreach ($businesses as $business) {
                        $user = get_userdata($business->post_author);
                        $business_email = get_post_meta($business->ID, '_ait-item_item-data', true)['email'] ?? '';
                        $email_recipients = [];
                        if ($user) {
                            $email_recipients[] = $user->user_email;
                            $recipients[] = $user->user_email;
                        }
                        if (is_email($business_email)) {
                            $email_recipients[] = $business_email;
                            $recipients[] = $business_email;
                        }
                        if ($email_recipients) {
                            $temp_message = str_replace('{USER_NAME}', esc_html($user->display_name ?? 'Business Owner'), $message_template);
                            $queue[] = [
                                'recipients' => array_unique($email_recipients),
                                'subject' => $subject,
                                'message' => $temp_message,
                                'headers' => $headers,
                            ];
                        }
                    }
                }
                break;

            case 'location_businesses':
                $location_ids = isset($_POST['location_ids']) ? array_map('intval', (array)$_POST['location_ids']) : [];
                if ($location_ids) {
                    $args = [
                        'post_type' => 'ait-item',
                        'posts_per_page' => -1,
                        'post_status' => ['publish', 'draft', 'under_reverification'],
                        'tax_query' => [
                            [
                                'taxonomy' => 'ait-locations',
                                'field' => 'term_id',
                                'terms' => $location_ids,
                            ],
                        ],
                    ];
                    $businesses = get_posts($args);
                    foreach ($businesses as $business) {
                        $user = get_userdata($business->post_author);
                        $business_email = get_post_meta($business->ID, '_ait-item_item-data', true)['email'] ?? '';
                        $email_recipients = [];
                        if ($user) {
                            $email_recipients[] = $user->user_email;
                            $recipients[] = $user->user_email;
                        }
                        if (is_email($business_email)) {
                            $email_recipients[] = $business_email;
                            $recipients[] = $business_email;
                        }
                        if ($email_recipients) {
                            $temp_message = str_replace('{USER_NAME}', esc_html($user->display_name ?? 'Business Owner'), $message_template);
                            $queue[] = [
                                'recipients' => array_unique($email_recipients),
                                'subject' => $subject,
                                'message' => $temp_message,
                                'headers' => $headers,
                            ];
                        }
                    }
                }
                break;

            case 'category_location_businesses':
                $category_ids = isset($_POST['category_ids']) ? array_map('intval', (array)$_POST['category_ids']) : [];
                $location_ids = isset($_POST['location_ids']) ? array_map('intval', (array)$_POST['location_ids']) : [];
                if ($category_ids && $location_ids) {
                    $args = [
                        'post_type' => 'ait-item',
                        'posts_per_page' => -1,
                        'post_status' => ['publish', 'draft', 'under_reverification'],
                        'tax_query' => [
                            'relation' => 'AND',
                            [
                                'taxonomy' => 'ait-items',
                                'field' => 'term_id',
                                'terms' => $category_ids,
                            ],
                            [
                                'taxonomy' => 'ait-locations',
                                'field' => 'term_id',
                                'terms' => $location_ids,
                            ],
                        ],
                    ];
                    $businesses = get_posts($args);
                    foreach ($businesses as $business) {
                        $user = get_userdata($business->post_author);
                        $business_email = get_post_meta($business->ID, '_ait-item_item-data', true)['email'] ?? '';
                        $email_recipients = [];
                        if ($user) {
                            $email_recipients[] = $user->user_email;
                            $recipients[] = $user->user_email;
                        }
                        if (is_email($business_email)) {
                            $email_recipients[] = $business_email;
                            $recipients[] = $business_email;
                        }
                        if ($email_recipients) {
                            $temp_message = str_replace('{USER_NAME}', esc_html($user->display_name ?? 'Business Owner'), $message_template);
                            $queue[] = [
                                'recipients' => array_unique($email_recipients),
                                'subject' => $subject,
                                'message' => $temp_message,
                                'headers' => $headers,
                            ];
                        }
                    }
                }
                break;
        }

        // Handle queue and schedule processing
        if (!empty($queue)) {
            set_transient('kaha6_email_queue', $queue, HOUR_IN_SECONDS);
            if (!wp_next_scheduled('kaha6_process_email_queue')) {
                wp_schedule_event(time(), 'kaha6_five_seconds', 'kaha6_process_email_queue');
            }
            $message = '<div class="alert alert-success">Emails have been queued and are being sent with a 5-second delay between each.</div>';
        } else {
            $message = '<div class="alert alert-warning">No valid recipients selected or found.</div>';
        }
    }

?>
    <div class="wrap">
        <h1>Send Custom Emails</h1>
        <p class="fs-6 lh-lg">Welcome, <?php echo esc_html($name); ?>!</p>
        <?php if ($message) echo $message; ?>
        <form method="post" class="p-4 bg-white rounded shadow">
            <?php wp_nonce_field('send_email_nonce'); ?>
            <div class="mb-3">
                <label for="email_subject" class="form-label"><strong>Email Subject</strong></label>
                <input type="text" class="form-control" id="email_subject" name="email_subject" placeholder="Enter email subject" required>
            </div>
            <div class="mb-3">
                <label for="email_content" class="form-label"><strong>Email Content</strong></label>
                <?php
                wp_editor(
                    '',
                    'email_content',
                    [
                        'textarea_name' => 'email_content',
                        'media_buttons' => false,
                        'textarea_rows' => 10,
                        'editor_class'  => 'form-control',
                        'teeny'         => false,
                        'quicktags'     => true,
                    ]
                );
                ?>
            </div>
            <div class="mb-3">
                <label for="recipient_type" class="form-label"><strong>Recipient Type</strong></label>
                <select class="form-control chosen-select" id="recipient_type" name="recipient_type" required>
                    <option value="">Select Recipient Type</option>
                    <option value="multiple_users">Multiple Users</option>
                    <option value="multiple_businesses">Multiple Businesses</option>
                    <option value="all_users">All Users</option>
                    <option value="all_businesses">All Businesses</option>
                    <option value="category_businesses">Category-wise Businesses</option>
                    <option value="location_businesses">Location-wise Businesses</option>
                    <option value="category_location_businesses">Category & Location-wise Businesses</option>
                </select>
            </div>
            <div class="mb-3 recipient-option" id="multiple_users_option" style="display: none;">
                <label for="user_ids" class="form-label"><strong>Select Users</strong></label>
                <select class="form-control chosen-select" id="user_ids" name="user_ids[]" multiple>
                    <?php
                    foreach ($users as $user) {
                        echo '<option value="' . esc_attr($user->ID) . '">' . esc_html($user->display_name) . ' (' . esc_html($user->user_email) . ')</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3 recipient-option" id="multiple_businesses_option" style="display: none;">
                <label for="post_ids" class="form-label"><strong>Select Businesses</strong></label>
                <select class="form-control chosen-select" id="post_ids" name="post_ids[]" multiple>
                    <?php
                    // Optimized query for all businesses
                    $business_args = [
                        'post_type'      => 'ait-item',
                        'posts_per_page' => -1,  // Get all posts
                        'post_status'    => ['publish', 'draft', 'under_reverification'],
                        'fields'         => 'ids', // Only get IDs to reduce memory usage
                        'no_found_rows'  => true, // Skip counting total rows for performance
                    ];

                    $business_ids = get_posts($business_args);

                    if (!empty($business_ids)) {
                        // Process in chunks to avoid memory overload
                        $chunks = array_chunk($business_ids, 200); // Process 200 at a time

                        foreach ($chunks as $chunk) {
                            // Get just the titles for this chunk
                            global $post;
                            $posts = get_posts([
                                'post__in'       => $chunk,
                                'post_type'      => 'ait-item',
                                'posts_per_page' => -1,
                                'orderby'        => 'title',
                                'order'          => 'ASC',
                                'fields'         => 'all' // Get full post objects
                            ]);

                            foreach ($posts as $post) {
                                setup_postdata($post);
                                echo '<option value="' . esc_attr($post->ID) . '">'
                                    . esc_html(get_the_title())
                                    . '</option>';
                            }
                            wp_reset_postdata();
                        }
                    } else {
                        echo '<option value="">No businesses found</option>';
                    }
                    ?>
                </select>
                <small class="text-muted"><?php echo count($business_ids); ?> businesses loaded</small>
            </div>
            <div class="mb-3 recipient-option" id="category_businesses_option" style="display: none;">
                <label for="category_ids" class="form-label"><strong>Select Categories</strong></label>
                <select class="form-control chosen-select" id="category_ids" name="category_ids[]" multiple>
                    <?php
                    foreach ($categories as $category) {
                        echo '<option value="' . esc_attr($category['id']) . '">' . esc_html($category['name']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3 recipient-option" id="location_businesses_option" style="display: none;">
                <label for="location_ids" class="form-label"><strong>Select Locations</strong></label>
                <select class="form-control chosen-select" id="location_ids" name="location_ids[]" multiple>
                    <?php
                    foreach ($locations as $location) {
                        echo '<option value="' . esc_attr($location['id']) . '">' . esc_html($location['name']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3 recipient-option" id="category_location_businesses_option" style="display: none;">
                <label for="category_ids_cl" class="form-label"><strong>Select Categories</strong></label>
                <select class="form-control chosen-select" id="category_ids_cl" name="category_ids[]" multiple>
                    <?php
                    foreach ($categories as $category) {
                        echo '<option value="' . esc_attr($category['id']) . '">' . esc_html($category['name']) . '</option>';
                    }
                    ?>
                </select>
                <label for="location_ids_cl" class="form-label mt-3"><strong>Select Locations</strong></label>
                <select class="form-control chosen-select" id="location_ids_cl" name="location_ids[]" multiple>
                    <?php
                    foreach ($locations as $location) {
                        echo '<option value="' . esc_attr($location['id']) . '">' . esc_html($location['name']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="send_email" class="btn btn-primary">Send Email</button>
        </form>
    </div>
    <script>
        jQuery(document).ready(function($) {
            // Initialize Chosen.js
            $('.chosen-select').chosen({
                width: '100%'
            });

            // Show/hide recipient options based on recipient_type
            $('#recipient_type').on('change', function() {
                $('.recipient-option').hide();
                const value = $(this).val();
                if (value === 'multiple_users') {
                    $('#multiple_users_option').show();
                } else if (value === 'multiple_businesses') {
                    $('#multiple_businesses_option').show();
                } else if (value === 'category_businesses') {
                    $('#category_businesses_option').show();
                } else if (value === 'location_businesses') {
                    $('#location_businesses_option').show();
                } else if (value === 'category_location_businesses') {
                    $('#category_location_businesses_option').show();
                }
            });
        });
    </script>
<?php
}
?>