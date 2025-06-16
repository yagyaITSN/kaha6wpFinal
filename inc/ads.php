<?php
// Ensure uploads/ads directory exists
add_action('init', 'create_ads_upload_directory');
function create_ads_upload_directory()
{
    $upload_dir = wp_upload_dir();
    $ads_dir = $upload_dir['basedir'] . '/ads';
    if (!file_exists($ads_dir)) {
        wp_mkdir_p($ads_dir);
    }
}

// Create wp_user_ads table
function create_user_ads_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_ads';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        ad_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        image_path VARCHAR(255) NOT NULL,
        title VARCHAR(255) NOT NULL,
        redirect_url VARCHAR(255) DEFAULT '',
        start_time DATETIME NOT NULL,
        expire_time DATETIME NOT NULL,
        status ENUM('active', 'expired') NOT NULL DEFAULT 'active',
        clicks BIGINT UNSIGNED NOT NULL DEFAULT 0,
        displayed BIGINT UNSIGNED NOT NULL DEFAULT 0,
        author_id BIGINT UNSIGNED NOT NULL,
        PRIMARY KEY (ad_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $result = dbDelta($sql);

    // Log table creation result
    if (!empty($result)) {
        error_log('wp_user_ads table creation result: ' . print_r($result, true));
    } else {
        error_log('wp_user_ads table creation: No changes or errors detected.');
    }

    // Verify table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        error_log('Error: wp_user_ads table was not created.');
    } else {
        error_log('Success: wp_user_ads table exists.');
    }
}

// Trigger table creation on theme activation and admin init
add_action('after_switch_theme', 'check_and_create_user_ads_table');
add_action('admin_init', 'check_and_create_user_ads_table');
function check_and_create_user_ads_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_ads';

    // Only create table if it doesn't exist
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        create_user_ads_table();
        error_log('wp_user_ads table creation triggered: Table did not exist.');
    } else {
        error_log('wp_user_ads table creation skipped: Table already exists.');
    }

    // Manual trigger for table creation via admin URL
    if (isset($_GET['action']) && $_GET['action'] === 'create_user_ads_table' && current_user_can('manage_options')) {
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            create_user_ads_table();
            wp_redirect(admin_url('admin.php?page=user-ads&table_created=1'));
        } else {
            wp_redirect(admin_url('admin.php?page=user-ads&table_exists=1'));
        }
        exit;
    }
}

// Handle form submission
add_action('init', 'handle_ad_form_submission');
function handle_ad_form_submission()
{
    global $wpdb;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'submit_ad_form') {
        return;
    }

    $error_message = '';
    $success_message = '';
    $table_name = $wpdb->prefix . 'user_ads';

    // Check if table exists, create if not
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        create_user_ads_table();
        error_log('wp_user_ads table created: Triggered during form submission.');
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $error_message = 'Failed to create database table. Please contact the administrator.';
            error_log('Error: wp_user_ads table creation failed during form submission.');
            set_transient('ad_form_error', $error_message, 30);
            wp_redirect(add_query_arg('ad_form_submitted', 'error', wp_get_referer()));
            exit;
        }
    }

    // Verify nonce
    if (!isset($_POST['ad_form_nonce']) || !wp_verify_nonce($_POST['ad_form_nonce'], 'ad_form_submission')) {
        $error_message = 'Security check failed.';
        error_log('Ad form submission failed: Invalid nonce.');
        set_transient('ad_form_error', $error_message, 30);
        wp_redirect(add_query_arg('ad_form_submitted', 'error', wp_get_referer()));
        exit;
    }

    // Check if user is logged in
    if (!is_user_logged_in()) {
        $error_message = 'You must be logged in to submit an ad.';
        error_log('Ad form submission failed: User not logged in.');
        set_transient('ad_form_error', $error_message, 30);
        wp_redirect(add_query_arg('ad_form_submitted', 'error', wp_get_referer()));
        exit;
    }

    $user_id = get_current_user_id();
    $max_ads = get_option('max_ads_per_user', 1);

    // Check ad count
    $ad_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE author_id = %d AND status = 'active'",
        $user_id
    ));

    if ($ad_count >= $max_ads) {
        $error_message = 'You have reached the maximum number of active ads allowed.';
        error_log('Ad form submission failed: Max ads limit reached for user ' . $user_id);
        set_transient('ad_form_error', $error_message, 30);
        wp_redirect(add_query_arg('ad_form_submitted', 'error', wp_get_referer()));
        exit;
    }

    // Validate inputs
    $ad_title = sanitize_text_field($_POST['adTitle']);
    $ad_url = !empty($_POST['adURL']) ? esc_url_raw($_POST['adURL']) : '';
    $file = $_FILES['adImage'];

    if (empty($ad_title)) {
        $error_message = 'Ad title is required.';
        error_log('Ad form submission failed: Empty title.');
    } elseif (empty($file['name'])) {
        $error_message = 'Ad image is required.';
        error_log('Ad form submission failed: No image uploaded.');
    } else {
        // Validate file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 500 * 1024; // 500KB
        if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
            $error_message = 'Invalid file type or size. Only JPG, PNG, GIF up to 500KB allowed.';
            error_log('Ad form submission failed: Invalid file type or size.');
        } else {
            // Handle file upload
            $upload_dir = wp_upload_dir();
            $ads_dir = $upload_dir['basedir'] . '/ads/';
            $file_name = sanitize_file_name($file['name']);
            $file_path = $ads_dir . $file_name;

            // Ensure unique filename
            $i = 1;
            $base_name = pathinfo($file_name, PATHINFO_FILENAME);
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            while (file_exists($file_path)) {
                $file_name = $base_name . '-' . $i . '.' . $ext;
                $file_path = $ads_dir . $file_name;
                $i++;
            }

            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $image_url = $upload_dir['baseurl'] . '/ads/' . $file_name;
                $default_expiry_days = get_option('ad_expiry_days', 1);
                $start_time = current_time('mysql');
                $expire_time = date('Y-m-d H:i:s', strtotime($start_time . ' + ' . $default_expiry_days . ' days'));

                // Insert into database
                $result = $wpdb->insert(
                    $table_name,
                    [
                        'image_path' => $image_url,
                        'title' => $ad_title,
                        'redirect_url' => $ad_url,
                        'start_time' => $start_time,
                        'expire_time' => $expire_time,
                        'status' => 'active',
                        'author_id' => $user_id,
                    ],
                    ['%s', '%s', '%s', '%s', '%s', '%s', '%d']
                );

                if ($result === false) {
                    $error_message = 'Failed to save ad to database.';
                    error_log('Ad form submission failed: Database insert error - ' . $wpdb->last_error);
                } else {
                    $success_message = 'Ad submitted successfully!';
                    error_log('Ad form submission successful for user ' . $user_id);
                    set_transient('ad_form_success', $success_message, 30);
                    wp_redirect(add_query_arg('ad_form_submitted', 'success', wp_get_referer()));
                    exit;
                }
            } else {
                $error_message = 'Failed to upload image.';
                error_log('Ad form submission failed: Image upload error.');
            }
        }
    }

    set_transient('ad_form_error', $error_message, 30);
    wp_redirect(add_query_arg('ad_form_submitted', 'error', wp_get_referer()));
    exit;
}

// Admin menu for ad management
add_action('admin_menu', 'register_ads_admin_menu');
function register_ads_admin_menu()
{
    add_menu_page(
        'User Ads',
        'User Ads',
        'manage_options',
        'user-ads',
        'render_ads_admin_page',
        'dashicons-images-alt2',
        30
    );
    add_submenu_page(
        'user-ads',
        'Ad Settings',
        'Settings',
        'manage_options',
        'user-ads-settings',
        'render_ads_settings_page'
    );
}

// Render admin ads list page
function render_ads_admin_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_ads';
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $ad_id = isset($_GET['ad_id']) ? intval($_GET['ad_id']) : 0;

    // Show table creation/existence notices
    if (isset($_GET['table_created'])) {
        echo '<div class="notice notice-success"><p>Table creation attempted. Check debug log for details.</p></div>';
    } elseif (isset($_GET['table_exists'])) {
        echo '<div class="notice notice-info"><p>Table already exists. No action taken.</p></div>';
    }

    // Handle delete
    if ($action === 'delete' && $ad_id) {
        $wpdb->delete($table_name, ['ad_id' => $ad_id], ['%d']);
        echo '<div class="notice notice-success"><p>Ad deleted successfully!</p></div>';
    }

    // Handle edit form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit' && isset($_POST['ad_id'])) {
        $ad_id = intval($_POST['ad_id']);
        $title = sanitize_text_field($_POST['ad_title']);
        $redirect_url = esc_url_raw($_POST['ad_url']);
        $start_time = sanitize_text_field($_POST['start_time']);
        $expire_time = sanitize_text_field($_POST['expire_time']);
        $status = in_array($_POST['status'], ['active', 'expired']) ? $_POST['status'] : 'active';

        $wpdb->update(
            $table_name,
            [
                'title' => $title,
                'redirect_url' => $redirect_url,
                'start_time' => $start_time,
                'expire_time' => $expire_time,
                'status' => $status,
            ],
            ['ad_id' => $ad_id],
            ['%s', '%s', '%s', '%s', '%s'],
            ['%d']
        );
        echo '<div class="notice notice-success"><p>Ad updated successfully!</p></div>';
    }

    // Edit form
    if ($action === 'edit' && $ad_id) {
        $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE ad_id = %d", $ad_id));
        if ($ad) {
?>
            <div class="wrap">
                <h1>Edit Ad</h1>
                <form method="post">
                    <input type="hidden" name="ad_id" value="<?php echo esc_attr($ad->ad_id); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="ad_title">Ad Title</label></th>
                            <td><input type="text" name="ad_title" id="ad_title" value="<?php echo esc_attr($ad->title); ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="ad_url">Redirect URL</label></th>
                            <td><input type="url" name="ad_url" id="ad_url" value="<?php echo esc_attr($ad->redirect_url); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="start_time">Start Time</label></th>
                            <td><input type="datetime-local" name="start_time" id="start_time" value="<?php echo esc_attr(date('Y-m-d\TH:i', strtotime($ad->start_time))); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="expire_time">Expire Time</label></th>
                            <td><input type="datetime-local" name="expire_time" id="expire_time" value="<?php echo esc_attr(date('Y-m-d\TH:i', strtotime($ad->expire_time))); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="status">Status</label></th>
                            <td>
                                <select name="status" id="status">
                                    <option value="active" <?php selected($ad->status, 'active'); ?>>Active</option>
                                    <option value="expired" <?php selected($ad->status, 'expired'); ?>>Expired</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Update Ad'); ?>
                </form>
            </div>
    <?php
            return;
        }
    }

    // Pagination and search
    $per_page = 20;
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($paged - 1) * $per_page;
    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    $where = "WHERE 1=1";
    if ($search) {
        $where .= $wpdb->prepare(" AND (title LIKE %s OR redirect_url LIKE %s)", '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%');
    }

    $total_ads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where");
    $ads = $wpdb->get_results($wpdb->prepare(
        "SELECT a.*, u.display_name FROM $table_name a LEFT JOIN {$wpdb->users} u ON a.author_id = u.ID $where ORDER BY a.ad_id DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));

    $total_pages = ceil($total_ads / $per_page);
    ?>
    <div class="wrap">
        <h1>User Ads</h1>
        <p><a href="<?php echo admin_url('admin.php?page=user-ads&action=create_user_ads_table'); ?>" class="button">Manually Create Ads Table</a></p>
        <form method="get">
            <input type="hidden" name="page" value="user-ads">
            <p class="search-box">
                <label class="screen-reader-text" for="ad-search-input">Search Ads:</label>
                <input type="search" id="ad-search-input" name="s" value="<?php echo esc_attr($search); ?>">
                <input type="submit" class="button" value="Search Ads">
            </p>
        </form>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Ad ID</th>
                    <th>Title</th>
                    <th>Image Path</th>
                    <th>Status</th>
                    <th>Redirect URL</th>
                    <th>Clicks</th>
                    <th>Displayed</th>
                    <th>Author</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ads) : foreach ($ads as $ad) : ?>
                        <tr>
                            <td><?php echo esc_html($ad->ad_id); ?></td>
                            <td><?php echo esc_html($ad->title); ?></td>
                            <td><a href="<?php echo esc_url($ad->image_path); ?>" target="_blank">View Image</a></td>
                            <td><?php echo esc_html($ad->status); ?></td>
                            <td><?php echo esc_url($ad->redirect_url); ?></td>
                            <td><?php echo esc_html($ad->clicks); ?></td>
                            <td><?php echo esc_html($ad->displayed); ?></td>
                            <td><?php echo esc_html($ad->display_name ?: $ad->author_id); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=user-ads&action=edit&ad_id=' . $ad->ad_id); ?>" class="button">Edit</a>
                                <a href="<?php echo admin_url('admin.php?page=user-ads&action=delete&ad_id=' . $ad->ad_id); ?>" class="button" onclick="return confirm('Are you sure you want to delete this ad?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach;
                else : ?>
                    <tr>
                        <td colspan="9">No ads found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
        // Pagination
        if ($total_pages > 1) {
            echo '<div class="tablenav"><div class="tablenav-pages">';
            echo paginate_links([
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'total' => $total_pages,
                'current' => $paged,
                'prev_text' => '«',
                'next_text' => '»',
            ]);
            echo '</div></div>';
        }
        ?>
    </div>
<?php
}

// Render settings page
function render_ads_settings_page()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ad_settings_nonce']) && wp_verify_nonce($_POST['ad_settings_nonce'], 'ad_settings_save')) {
        update_option('ad_expiry_days', max(1, intval($_POST['ad_expiry_days'])));
        update_option('max_ads_per_user', max(1, intval($_POST['max_ads_per_user'])));
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }

    $ad_expiry_days = get_option('ad_expiry_days', 1);
    $max_ads_per_user = get_option('max_ads_per_user', 1);
?>
    <div class="wrap">
        <h1>Ad Settings</h1>
        <form method="post">
            <?php wp_nonce_field('ad_settings_save', 'ad_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="ad_expiry_days">Default Ad Expiry Days</label></th>
                    <td><input type="number" name="ad_expiry_days" id="ad_expiry_days" value="<?php echo esc_attr($ad_expiry_days); ?>" min="1" class="small-text"></td>
                </tr>
                <tr>
                    <th><label for="max_ads_per_user">Max Ads Per User</label></th>
                    <td><input type="number" name="max_ads_per_user" id="max_ads_per_user" value="<?php echo esc_attr($max_ads_per_user); ?>" min="1" class="small-text"></td>
                </tr>
            </table>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

add_filter('cron_schedules', 'add_five_minute_cron_schedule');
function add_five_minute_cron_schedule($schedules)
{
    $schedules['every_five_minutes'] = array(
        'interval' => 300, // 300 seconds = 5 minutes
        'display'  => __('Every 5 Minutes', 'kaha6'),
    );
    return $schedules;
}

// Schedule cron job on theme activation
add_action('after_switch_theme', 'schedule_expired_ads_cron');
function schedule_expired_ads_cron()
{
    if (!wp_next_scheduled('update_expired_ads_event')) {
        wp_schedule_event(time(), 'every_five_minutes', 'update_expired_ads_event');
        error_log('Cron job scheduled: update_expired_ads_event');
    }
}

// Function to update expired ads
add_action('update_expired_ads_event', 'update_expired_ads');
function update_expired_ads()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_ads';

    $result = $wpdb->query("UPDATE $table_name SET status = 'expired' WHERE expire_time < NOW() AND status = 'active'");
    if ($result === false) {
        error_log('Cron: Failed to update expired ads - ' . $wpdb->last_error);
    } else {
        error_log('Cron: Updated expired ads - ' . $result . ' rows affected');
    }
}

// Shortcode to display random ad
add_shortcode('display_random_ad', 'display_random_ad');
function display_random_ad()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_ads';

    $ad = $wpdb->get_row("SELECT * FROM $table_name WHERE status = 'active' AND expire_time > NOW() ORDER BY RAND() LIMIT 1");

    if ($ad) {
        $result = $wpdb->update($table_name, ['displayed' => $ad->displayed + 1], ['ad_id' => $ad->ad_id], ['%d'], ['%d']);
        if ($result === false) {
            error_log('Failed to increment display count for ad_id ' . $ad->ad_id . ': ' . $wpdb->last_error);
        }

        $click_url = admin_url('admin-ajax.php?action=track_ad_click&ad_id=' . $ad->ad_id);
        ob_start();
    ?>
        <div class="random-ad">
            <a href="<?php echo esc_url($click_url); ?>" target="_blank" class="ad-link">
                <img src="<?php echo esc_url($ad->image_path); ?>" alt="<?php echo esc_attr($ad->title); ?>" style="max-width: 100%;">
            </a>
        </div>
<?php
        return ob_get_clean();
    }
    return '<div class="alert alert-info text-center">No active ads available.</div>';
}


// AJAX handler for tracking ad clicks
add_action('wp_ajax_track_ad_click', 'track_ad_click');
add_action('wp_ajax_nopriv_track_ad_click', 'track_ad_click');
function track_ad_click()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_ads';
    $ad_id = isset($_GET['ad_id']) ? intval($_GET['ad_id']) : 0;

    if ($ad_id) {
        $ad = $wpdb->get_row($wpdb->prepare("SELECT redirect_url FROM $table_name WHERE ad_id = %d", $ad_id));
        if ($ad) {
            $result = $wpdb->update($table_name, ['clicks' => $wpdb->get_var($wpdb->prepare("SELECT clicks FROM $table_name WHERE ad_id = %d", $ad_id)) + 1], ['ad_id' => $ad_id], ['%d'], ['%d']);
            if ($result === false) {
                error_log('Failed to increment clicks for ad_id ' . $ad_id . ': ' . $wpdb->last_error);
            }
            wp_redirect($ad->redirect_url ?: home_url());
            exit;
        }
    }
    wp_redirect(home_url());
    exit;
}

// AJAX handler for reactivating an ad
add_action('wp_ajax_reactivate_ad', 'reactivate_ad');
function reactivate_ad()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_ads';
    $ad_id = isset($_POST['ad_id']) ? intval($_POST['ad_id']) : 0;
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

    error_log('Reactivate ad attempt: ad_id=' . $ad_id . ', nonce=' . $nonce);

    if (!wp_verify_nonce($nonce, 'reactivate_ad_' . $ad_id)) {
        error_log('Reactivate ad failed: Invalid nonce');
        wp_send_json_error(['message' => 'Security check failed.']);
    }

    if (!is_user_logged_in()) {
        error_log('Reactivate ad failed: User not logged in');
        wp_send_json_error(['message' => 'You must be logged in.']);
    }

    $user_id = get_current_user_id();
    $max_ads = get_option('max_ads_per_user', 1);
    $ad_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE author_id = %d AND status = 'active'",
        $user_id
    ));

    if ($ad_count >= $max_ads) {
        error_log('Reactivate ad failed: Max ads limit reached for user ' . $user_id);
        wp_send_json_error(['message' => 'You have reached the maximum number of active ads allowed.']);
    }

    $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE ad_id = %d AND author_id = %d", $ad_id, $user_id));

    if (!$ad) {
        error_log('Reactivate ad failed: Ad not found or no permission for ad_id ' . $ad_id);
        wp_send_json_error(['message' => 'Ad not found or you do not have permission.']);
    }

    if ($ad->status !== 'expired') {
        error_log('Reactivate ad failed: Ad is not expired for ad_id ' . $ad_id);
        wp_send_json_error(['message' => 'Ad is not expired.']);
    }

    $default_expiry_days = get_option('ad_expiry_days', 1);
    $new_expire_time = date('Y-m-d H:i:s', strtotime(current_time('mysql') . ' + ' . $default_expiry_days . ' days'));

    $result = $wpdb->update(
        $table_name,
        [
            'status' => 'active',
            'expire_time' => $new_expire_time,
        ],
        ['ad_id' => $ad_id, 'author_id' => $user_id],
        ['%s', '%s'],
        ['%d', '%d']
    );

    if ($result === false) {
        error_log('Reactivate ad failed: Database error for ad_id ' . $ad_id . ' - ' . $wpdb->last_error);
        wp_send_json_error(['message' => 'Failed to reactivate ad.']);
    } else {
        error_log('Ad reactivated successfully: ad_id ' . $ad_id . ' by user ' . $user_id);
        wp_send_json_success(['message' => 'Ad reactivated successfully!']);
    }
}

// AJAX handler for deleting an ad
add_action('wp_ajax_delete_ad', 'delete_ad');
function delete_ad()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_ads';
    $ad_id = isset($_POST['ad_id']) ? intval($_POST['ad_id']) : 0;
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

    error_log('Delete ad attempt: ad_id=' . $ad_id . ', nonce=' . $nonce);

    if (!wp_verify_nonce($nonce, 'delete_ad_' . $ad_id)) {
        error_log('Delete ad failed: Invalid nonce');
        wp_send_json_error(['message' => 'Security check failed.']);
    }

    if (!is_user_logged_in()) {
        error_log('Delete ad failed: User not logged in');
        wp_send_json_error(['message' => 'You must be logged in.']);
    }

    $user_id = get_current_user_id();
    $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE ad_id = %d AND author_id = %d", $ad_id, $user_id));

    if (!$ad) {
        error_log('Delete ad failed: Ad not found or no permission for ad_id ' . $ad_id);
        wp_send_json_error(['message' => 'Ad not found or you do not have permission.']);
    }

    $result = $wpdb->delete($table_name, ['ad_id' => $ad_id, 'author_id' => $user_id], ['%d', '%d']);

    if ($result === false) {
        error_log('Delete ad failed: Database error for ad_id ' . $ad_id . ' - ' . $wpdb->last_error);
        wp_send_json_error(['message' => 'Failed to delete ad.']);
    } else {
        error_log('Ad deleted successfully: ad_id ' . $ad_id . ' by user ' . $user_id);
        wp_send_json_success(['message' => 'Ad deleted successfully!']);
    }
}
?>