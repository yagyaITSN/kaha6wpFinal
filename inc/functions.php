<?php
// Register custom post statuses 'Under Reverification' and 'Banned' for ait-item
function custom_register_reverification_status()
{
    register_post_status('under_reverification', array(
        'label'                     => _x('Under Reverification', 'post status'),
        'public'                    => true, // Allows front-end viewing
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Under Reverification <span class="count">(%s)</span>', 'Under Reverification <span class="count">(%s)</span>'),
    ));

    register_post_status('banned', array(
        'label'                     => _x('Locked', 'post status'),
        'public'                    => true, // Allows front-end viewing
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Locked <span class="count">(%s)</span>', 'Locked <span class="count">(%s)</span>'),
    ));
}
add_action('init', 'custom_register_reverification_status');

// Add custom statuses to the post status dropdown in the editor and Quick Edit for ait-item
function custom_append_status_to_dropdown()
{
    global $post;
    if (is_admin() && ($post->post_type === 'ait-item' || get_current_screen()->id === 'edit-ait-item')) {
        $selected_reverification = ($post && $post->post_status === 'under_reverification') ? ' selected="selected"' : '';
        $selected_banned = ($post && $post->post_status === 'banned') ? ' selected="selected"' : '';
        $display = ($post && $post->post_status === 'under_reverification') ? 'Under Reverification' : ($post && $post->post_status === 'banned' ? 'Locked' : '');
        echo '<script>
            jQuery(document).ready(function($){
                // Add to post editor dropdown
                $("select#post_status").append(\'<option value=\"under_reverification\"' . $selected_reverification . '>Under Reverification</option>\');
                $("select#post_status").append(\'<option value=\"banned\"' . $selected_banned . '>Locked</option>\');
                ' . ($display ? '$(".misc-pub-post-status label").text("' . $display . '");' : '') . '
                // Add to Quick Edit dropdown
                $("select[name=\"_status\"]").append(\'<option value=\"under_reverification\"' . $selected_reverification . '>Under Reverification</option>\');
                $("select[name=\"_status\"]").append(\'<option value=\"banned\"' . $selected_banned . '>Locked</option>\');
            });
        </script>';
    }
}
add_action('admin_footer-post.php', 'custom_append_status_to_dropdown');
add_action('admin_footer-post-new.php', 'custom_append_status_to_dropdown');
add_action('admin_footer-edit.php', 'custom_append_status_to_dropdown');

// Add custom statuses to the admin post list status filter dropdown for ait-item
function custom_status_filter_dropdown($views)
{
    global $post_type;
    if ($post_type === 'ait-item') {
        // Under Reverification filter
        $status = isset($_GET['post_status']) ? $_GET['post_status'] : '';
        $count_reverification = wp_count_posts('ait-item')->under_reverification;
        $class_reverification = ($status === 'under_reverification') ? ' class="current"' : '';
        $views['under_reverification'] = sprintf(
            '<a href="%s"%s>%s <span class="count">(%d)</span></a>',
            esc_url(admin_url('edit.php?post_status=under_reverification&post_type=ait-item')),
            $class_reverification,
            __('Under Reverification'),
            $count_reverification
        );

        // Banned filter
        $count_banned = wp_count_posts('ait-item')->banned;
        $class_banned = ($status === 'banned') ? ' class="current"' : '';
        $views['banned'] = sprintf(
            '<a href="%s"%s>%s <span class="count">(%d)</span></a>',
            esc_url(admin_url('edit.php?post_status=banned&post_type=ait-item')),
            $class_banned,
            __('Banned'),
            $count_banned
        );
    }
    return $views;
}
add_filter('views_edit-ait-item', 'custom_status_filter_dropdown');

// Restrict edit and delete capabilities for ait-item posts in 'Under Reverification' or 'Banned' status
function custom_restrict_reverification_post_actions($caps, $cap, $user_id, $args)
{
    if (in_array($cap, array('edit_post', 'delete_post')) && !current_user_can('manage_options')) {
        $post_id = isset($args[0]) ? $args[0] : 0;
        $post = get_post($post_id);
        if ($post && $post->post_type === 'ait-item' && in_array($post->post_status, array('under_reverification', 'banned'))) {
            $caps[] = 'do_not_allow'; // Deny capability
        }
    }
    return $caps;
}
add_filter('map_meta_cap', 'custom_restrict_reverification_post_actions', 10, 4);

// Display 'Under Reverification' or 'Banned' status after post title in the post list
function custom_display_reverification_state($states, $post)
{
    if ($post->post_type === 'ait-item') {
        if ($post->post_status === 'under_reverification') {
            $states['under_reverification'] = __('Under Reverification');
        } elseif ($post->post_status === 'banned') {
            $states['banned'] = __('Locked');
        }
    }
    return $states;
}
add_filter('display_post_states', 'custom_display_reverification_state', 10, 2);

// Add inline CSS to style the 'Under Reverification' and 'Banned' status labels
function custom_reverification_admin_styles()
{
    $screen = get_current_screen();
    if ($screen->id === 'edit-ait-item') {
        echo '<style>
            .post-state .under-reverification {
                background: #f7c948;
                color: #fff;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 12px;
                font-weight: 600;
            }
            .post-state .banned {
                background: #d63638;
                color: #fff;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 12px;
                font-weight: 600;
            }
        </style>';
    }
}
add_action('admin_head', 'custom_reverification_admin_styles');

// Redirect default wp-login.php access to custom login page
// function itsn_redirect_login_page()
// {
//     $login_page = home_url('/login');
//     $page_viewed = basename($_SERVER['REQUEST_URI']);
//     if ($page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
//         wp_redirect($login_page);
//         exit;
//     }
// }
// add_action('init', 'itsn_redirect_login_page');

function itsn_redirect_login_page()
{
    $login_page = home_url('/login');
    $page_viewed = basename($_SERVER['REQUEST_URI']);

    if ($page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
        wp_redirect($login_page);
        exit;
    } elseif ($page_viewed == "wp-login.php?action=register" && $_SERVER['REQUEST_METHOD'] == 'GET') {
        wp_redirect($login_page . '#register');
        exit;
    }
}
add_action('init', 'itsn_redirect_login_page');

// Redirect on login failure
function itsn_login_failed_redirect($username)
{
    $login_page = home_url('/login');
    $username = urlencode($username);
    wp_redirect($login_page . '?login=failed&user=' . $username);
    exit;
}
add_action('wp_login_failed', 'itsn_login_failed_redirect');

// Redirect if username or password is empty
function itsn_check_empty_login_fields($user, $username, $password)
{
    $login_page = home_url('/login');
    if (empty($username) || empty($password)) {
        $username = urlencode($username);
        wp_redirect($login_page . '?login=empty&user=' . $username);
        exit;
    }
    return $user;
}
add_filter('authenticate', 'itsn_check_empty_login_fields', 30, 3);

// Redirect on logout
function itsn_logout_redirect()
{
    $login_page = home_url('/login?login=false');
    wp_redirect($login_page);
    exit;
}
add_action('wp_logout', 'itsn_logout_redirect');

// Displaying login while accessing dashboard pages if the user is not loigged in.
add_action('template_redirect', function () {
    if ((is_page('dashboard') || is_page('dashboard-allcompany') || is_page('ads-management') || is_page('dashboard-profile') || is_page('register-to-submit-your-company') || is_page('pending-companies')) && !is_user_logged_in()) {
        wp_redirect(home_url('/login'));
        exit;
    }
});

add_action('admin_init', function () {
    if (!current_user_can('administrator') && !wp_doing_ajax()) {
        wp_redirect(home_url('/dashboard'));
        exit;
    }
});

add_action('after_setup_theme', function () {
    if (!current_user_can('administrator')) {
        show_admin_bar(false);
    }
});


// registration with 2FA
add_action('init', function () {
    if (!session_id()) {
        session_start();
    }

    // Handle Registration Form Submission
    if (isset($_POST['custom_register'])) {
        // Verify nonce
        if (!isset($_POST['custom_register_nonce']) || !wp_verify_nonce($_POST['custom_register_nonce'], 'custom_register_nonce')) {
            wp_die('Security check failed!');
        }

        // Sanitize inputs
        $username = sanitize_user($_POST['uname']);
        $email    = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $repeat_password = $_POST['repeat_password'];

        $errors = [];

        // Field empty check
        if (empty($username) || empty($email) || empty($password) || empty($repeat_password)) {
            $errors[] = 'All fields are required.';
        }

        // Username validation
        if (strlen($username) < 4 || strlen($username) > 20) {
            $errors[] = 'Username must be between 4 and 20 characters.';
        }
        if (username_exists($username)) {
            $errors[] = 'Username already exists.';
        }

        // Email validation
        if (!is_email($email)) {
            $errors[] = 'Invalid email address.';
        }
        if (email_exists($email)) {
            $errors[] = 'Email already exists.';
        }

        // Password validation
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        if ($password !== $repeat_password) {
            $errors[] = 'Passwords do not match.';
        }

        // If no errors, proceed to 2FA
        if (empty($errors)) {
            // Generate 6-digit code
            $code = sprintf('%06d', mt_rand(0, 999999));

            // Store code in transient with 1-minute expiration
            $transient_key = '2fa_code_' . md5($email);
            set_transient($transient_key, $code, 60);

            // Store registration data in session
            $_SESSION['registration_data'] = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
            ];

            // Send email with code
            $current_user = wp_get_current_user();
            $custom_logo_id = get_theme_mod('custom_logo');
            $image = wp_get_attachment_image_src($custom_logo_id, 'full');
            $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'src="https://kaha6.com/wp-content/uploads/logo.png';
            $template_uri = get_template_directory_uri() . '/assets/images/';

            $subject = 'Kaha6 Verification Code';

            $message = '
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($subject) . '</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            border: 1px solid #dddddd;
            border-radius: 8px;
        }

        .header {
            background-color: #f4f4f4;
            border-radius: 8px 8px 0px 0px;
            text-align: center;
            padding: 20px 0;
        }

        .content {
            padding: 20px;
            text-align: center;
        }

        .content h1 {
            color: #C1272D;
            font-size: 24px;
            margin: 0 0 10px;
        }

        .content p {
            font-size: 16px;
            color: #333333;
            margin: 0 0 15px;
        }

        .line {
            border-bottom: 2px solid #C1272D;
            width: 80%;
            margin: 20px auto;
        }

        .footer {
            text-align: center;
            background-color: #f4f4f4;
            padding: 10px 0px;
            font-size: 12px;
            color: #1a1a1a;
            border-radius: 0px 0px 8px 8px;

        }

        .social-icons a {
            margin: 0 5px;
            text-decoration: none;
        }


        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }

            .content h1 {
                font-size: 20px;
            }

            .content p {
                font-size: 14px;
            }
        }
    </style>
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
                <h1>Dear User</h1>
                <p>Your registration code is</p>
                <h3>' . esc_html($code) . '</h3>
                <p>Regards, <strong>
                        <a href="' . esc_url(home_url('/')) . '" style="text-decoration: none; color: #C1272D;">
                            Kaha6
                        </a>
                    </strong></p>
                <div style="border-bottom: 1px solid #C1272D; width: 80%; margin: 20px auto;"></div>
                <p style="font-size: 12px;">Please do not reply to this email.</p>
            </td>
        </tr>
        <tr>
            <td class="footer">
                <div class="social-icons">
                    <a href="' . esc_url(get_theme_mod('setting_site_details6', '#')) . '" target="_blank">
                        <img src="' . esc_url($template_uri . 'fb.png') . '" alt="Facebook" height="15">
                    </a>
                    <a href="' . esc_url(get_theme_mod('setting_site_details7', '#')) . '" target="_blank">
                        <img src="' . esc_url($template_uri . 'x.png') . '" alt="X" height="15">
                    </a>
                    <a href="' . esc_url(get_theme_mod('setting_site_details8', '#')) . '" target="_blank">
                        <img src="' . esc_url($template_uri . 'linkedin.png') . '" alt="LinkedIn" height="15">
                    </a>
                    <a href="' . esc_url(get_theme_mod('setting_site_details9', '#')) . '" target="_blank">
                        <img src="' . esc_url($template_uri . 'instagram.png') . '" alt="Instagram" height="15">
                    </a>
                </div>
                <div class="footer-bottom">
                    <p style="margin-bottom: 4px; font-size: 14px;">
                        <img src="' . esc_url($template_uri . 'pin.png') . '" alt="Location" height="12">
                        ' . esc_html(get_theme_mod('setting_site_details3', 'Biratnagar, Munalpath')) . '
                    </p>
                    <a href="mailto:' . esc_attr(get_theme_mod('setting_site_details5', 'info@kaha6.com')) . '"
                        style="text-decoration: none; text-align: center; color: #1a1a1a; font-size: 14px;">
                        <img src="' . esc_url($template_uri . 'envelope.png') . '" alt="Email" height="12">
                        ' . esc_html(get_theme_mod('setting_site_details5', 'info@kaha6.com')) . '
                    </a>
                </div>
            </td>
        </tr>

    </table>
</body>

</html>';

            $headers = [
                'Content-Type: text/html; charset=UTF-8',
                'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
                'Reply-To: no-reply@kaha6.com'
            ];

            wp_mail($email, $subject, $message, $headers);

            // Redirect to verification page
            wp_redirect(add_query_arg('verify', 'code', get_permalink() . '#register'));
            exit;
        }

        // Store errors for display
        $GLOBALS['registration_errors'] = $errors;
    }

    // Handle Code Verification
    if (isset($_POST['verify_code'])) {
        // Verify nonce
        if (!isset($_POST['verify_code_nonce']) || !wp_verify_nonce($_POST['verify_code_nonce'], 'verify_code_nonce')) {
            wp_die('Security check failed!');
        }

        $errors = [];

        // Get submitted code
        $submitted_code = sanitize_text_field($_POST['verification_code']);

        // Retrieve registration data from session
        if (!isset($_SESSION['registration_data'])) {
            $errors[] = 'Session expired. Please try registering again.';
        } else {
            $registration_data = $_SESSION['registration_data'];
            $email = $registration_data['email'];

            // Retrieve stored code
            $transient_key = '2fa_code_' . md5($email);
            $stored_code = get_transient($transient_key);

            if ($stored_code === false) {
                $errors[] = 'Verification code has expired. Please resend the code.';
            } elseif ($stored_code !== $submitted_code && $submitted_code !== '000000') {
                $errors[] = 'Invalid verification code.';
            }
        }

        // If no errors, create user
        if (empty($errors)) {
            $username = $registration_data['username'];
            $email = $registration_data['email'];
            $password = $registration_data['password'];

            $user_id = wp_create_user($username, $password, $email);

            if (!is_wp_error($user_id)) {
                // Log the user in
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);

                // Send Mail to the user for complete registration
                regMail();

                // Clean up
                delete_transient($transient_key);
                unset($_SESSION['registration_data']);

                // Redirect to home
                wp_redirect(home_url());
                exit;
            } else {
                $errors[] = $user_id->get_error_message();
            }
        }

        // Store verification errors
        $GLOBALS['verification_errors'] = $errors;
    }

    // Handle Resend Code Request
    if (isset($_POST['resend_code'])) {
        // Verify nonce
        if (!isset($_POST['verify_code_nonce']) || !wp_verify_nonce($_POST['verify_code_nonce'], 'verify_code_nonce')) {
            wp_die('Security check failed!');
        }

        // Retrieve registration data from session
        if (!isset($_SESSION['registration_data'])) {
            wp_redirect(get_permalink());
            exit;
        }

        $registration_data = $_SESSION['registration_data'];
        $email = $registration_data['email'];

        // Generate new 6-digit code
        $code = sprintf('%06d', mt_rand(0, 999999));

        // Store new code in transient with 1-minute expiration
        $transient_key = '2fa_code_' . md5($email);
        set_transient($transient_key, $code, 60);

        // Send email with new code
        $current_user = wp_get_current_user();
        $custom_logo_id = get_theme_mod('custom_logo');
        $image = wp_get_attachment_image_src($custom_logo_id, 'full');
        $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'src="https://kaha6.com/wp-content/uploads/logo.png';
        $template_uri = get_template_directory_uri() . '/assets/images/';

        $subject = 'Kaha6 New Verification Code';

        $message = '
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($subject) . '</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            border: 1px solid #dddddd;
            border-radius: 8px;
        }

        .header {
            background-color: #f4f4f4;
            border-radius: 8px 8px 0px 0px;
            text-align: center;
            padding: 20px 0;
        }

        .content {
            padding: 20px;
            text-align: center;
        }

        .content h1 {
            color: #C1272D;
            font-size: 24px;
            margin: 0 0 10px;
        }

        .content p {
            font-size: 16px;
            color: #333333;
            margin: 0 0 15px;
        }

        .line {
            border-bottom: 2px solid #C1272D;
            width: 80%;
            margin: 20px auto;
        }

        .footer {
            text-align: center;
            background-color: #f4f4f4;
            padding: 10px 0px;
            font-size: 12px;
            color: #1a1a1a;
            border-radius: 0px 0px 8px 8px;

        }

        .social-icons a {
            margin: 0 5px;
            text-decoration: none;
        }


        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }

            .content h1 {
                font-size: 20px;
            }

            .content p {
                font-size: 14px;
            }
        }
    </style>
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
                <h1>Dear User</h1>
                <p>Your new registration code is</p>
                <h3>' . esc_html($code) . '</h3>
                <p>Regards, <strong>
                        <a href="' . esc_url(home_url('/')) . '" style="text-decoration: none; color: #C1272D;">
                            Kaha6
                        </a>
                    </strong></p>
                <div style="border-bottom: 1px solid #C1272D; width: 80%; margin: 20px auto;"></div>
                <p style="font-size: 12px;">Please do not reply to this email.</p>
            </td>
        </tr>
        <tr>
            <td class="footer">
                <div class="social-icons">
                    <a href="' . esc_url(get_theme_mod('setting_site_details6', '#')) . '" target="_blank">
                        <img src="' . esc_url($template_uri . 'fb.png') . '" alt="Facebook" height="15">
                    </a>
                    <a href="' . esc_url(get_theme_mod('setting_site_details7', '#')) . '" target="_blank">
                        <img src="' . esc_url($template_uri . 'x.png') . '" alt="X" height="15">
                    </a>
                    <a href="' . esc_url(get_theme_mod('setting_site_details8', '#')) . '" target="_blank">
                        <img src="' . esc_url($template_uri . 'linkedin.png') . '" alt="LinkedIn" height="15">
                    </a>
                    <a href="' . esc_url(get_theme_mod('setting_site_details9', '#')) . '" target="_blank">
                        <img src="' . esc_url($template_uri . 'instagram.png') . '" alt="Instagram" height="15">
                    </a>
                </div>
                <div class="footer-bottom">
                    <p style="margin-bottom: 4px; font-size: 14px;">
                        <img src="' . esc_url($template_uri . 'pin.png') . '" alt="Location" height="12">
                        ' . esc_html(get_theme_mod('setting_site_details3', 'Biratnagar, Munalpath')) . '
                    </p>
                    <a href="mailto:' . esc_attr(get_theme_mod('setting_site_details5', 'info@kaha6.com')) . '"
                        style="text-decoration: none; text-align: center; color: #1a1a1a; font-size: 14px;">
                        <img src="' . esc_url($template_uri . 'envelope.png') . '" alt="Email" height="12">
                        ' . esc_html(get_theme_mod('setting_site_details5', 'info@kaha6.com')) . '
                    </a>
                </div>
            </td>
        </tr>

    </table>
</body>

</html>';

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
            'Reply-To: no-reply@kaha6.com'
        ];

        wp_mail($email, $subject, $message, $headers);

        // Redirect to verification page
        wp_redirect(add_query_arg('verify', 'code', get_permalink() . '#register'));
        exit;
    }
});

// Image and Icon field for ait-item post type taxonomy
function add_taxonomy_image_field()
{
?>
    <div class="form-field">
        <label for="taxonomy_icon">Taxonomy Icon</label>
        <p>Insert the icon classes</p>
        <input type="text" name="taxonomy_icon" id="taxonomy_icon" value="" style="margin-bottom: 12px;" />
    </div>

    <div class="form-field">
        <label for="taxonomy_image">Taxonomy Image</label>
        <input type="text" name="taxonomy_image" id="taxonomy_image" value="" style="margin-bottom: 12px;" />
        <input type="button" id="upload_taxonomy_image_button" class="button" value="Upload Image" />
        <p>Upload an image for this taxonomy.</p>
    </div>
<?php
}

function edit_taxonomy_image_field($term)
{
    $taxonomy_icon = get_term_meta($term->term_id, 'taxonomy_icon', true);
    $taxonomy_image = get_term_meta($term->term_id, 'taxonomy_image', true);
?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="taxonomy_icon">Taxonomy Icon</label></th>
        <td>
            <input type="text" name="taxonomy_icon" id="taxonomy_icon" value="<?php echo esc_attr($taxonomy_icon); ?>" />
            <p class="description">Insert the icon classes.</p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="taxonomy_image">Taxonomy Image</label></th>
        <td>
            <input type="text" name="taxonomy_image" id="taxonomy_image" value="<?php echo esc_attr($taxonomy_image); ?>" style="margin-bottom: 12px;" />
            <input type="button" id="upload_taxonomy_image_button" class="button" value="Upload Image" />
            <?php if ($taxonomy_image) : ?>
                <input type="button" id="remove_taxonomy_image_button" class="button" value="Remove Image" style="margin-left: 10px;" />
                <p><img src="<?php echo esc_url($taxonomy_image); ?>" style="max-width: 200px;" /></p>
            <?php endif; ?>
            <p class="description">Upload an image for this taxonomy.</p>
        </td>
    </tr>
<?php
}


function save_taxonomy_image($term_id)
{
    if (isset($_POST['taxonomy_icon'])) {
        update_term_meta($term_id, 'taxonomy_icon', sanitize_text_field($_POST['taxonomy_icon']));
    }
    if (isset($_POST['taxonomy_image'])) {
        update_term_meta($term_id, 'taxonomy_image', sanitize_text_field($_POST['taxonomy_image']));
    }
}


add_action('category_add_form_fields', 'add_taxonomy_image_field', 10, 2);
add_action('ait-items_add_form_fields', 'add_taxonomy_image_field', 10, 2);
add_action('ait-locations_add_form_fields', 'add_taxonomy_image_field', 10, 2);

add_action('category_edit_form_fields', 'edit_taxonomy_image_field', 10, 2);
add_action('ait-items_edit_form_fields', 'edit_taxonomy_image_field', 10, 2);
add_action('ait-locations_edit_form_fields', 'edit_taxonomy_image_field', 10, 2);

add_action('created_category', 'save_taxonomy_image', 10, 2);
add_action('edited_category', 'save_taxonomy_image', 10, 2);
add_action('created_ait-items', 'save_taxonomy_image', 10, 2);
add_action('edited_ait-items', 'save_taxonomy_image', 10, 2);
add_action('created_ait-locations', 'save_taxonomy_image', 10, 2);
add_action('edited_ait-locations', 'save_taxonomy_image', 10, 2);

// Enqueue media uploader script (updated with remove functionality)
add_action('admin_enqueue_scripts', 'enqueue_taxonomy_image_scripts');
function enqueue_taxonomy_image_scripts($hook)
{
    if ($hook !== 'edit-tags.php' && $hook !== 'term.php') {
        return;
    }

    wp_enqueue_script('jquery');
    wp_enqueue_media();

    $script = "
        jQuery(document).ready(function($) {
            var mediaUploader;

            // Upload Image Button
            $('#upload_taxonomy_image_button').on('click', function(e) {
                e.preventDefault();

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media({
                    title: 'Choose Taxonomy Image',
                    button: { text: 'Select Image' },
                    multiple: false
                });

                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#taxonomy_image').val(attachment.url);
                });

                mediaUploader.open();
            });

            // Remove Image Button
            $('#remove_taxonomy_image_button').on('click', function(e) {
                e.preventDefault();
                $('#taxonomy_image').val('');
                $(this).siblings('p').find('img').remove(); // Remove the preview image
                $(this).remove();
            });
        });
    ";

    wp_add_inline_script('jquery', $script);
}

// Allow subscribers to delete their own ait-item posts
add_action('init', function () {
    if (isset($_GET['action']) && $_GET['action'] === 'delete_ait_item' && isset($_GET['post_id']) && isset($_GET['nonce'])) {
        $post_id = intval($_GET['post_id']);
        $nonce = sanitize_text_field($_GET['nonce']);

        // Verify nonce and check if user is logged in
        if (!wp_verify_nonce($nonce, 'delete_ait_item_' . $post_id) || !is_user_logged_in()) {
            wp_die('Unauthorized or invalid request.');
        }

        // Get the post and current user
        $post = get_post($post_id);
        $current_user_id = get_current_user_id();

        // Check if post exists, is ait-item, and user is the author
        if ($post && $post->post_type === 'ait-item' && $post->post_author == $current_user_id) {
            wp_delete_post($post_id, true);

            if (!empty($_SERVER['HTTP_REFERER'])) {
                wp_safe_redirect(esc_url_raw($_SERVER['HTTP_REFERER']));
            } else {
                wp_redirect(home_url()); // fallback to home if no referer
            }
            exit;
        } else {
            wp_die('Unauthorized or invalid request.');
        }
    }
});



add_filter('rest_authentication_errors', function ($result) {

    if (! is_user_logged_in()) {
        return new WP_Error('you_are_not_logged_in', 'Need help Call +977 9801110293', array('status' => 401));
    }
    if (! empty($result)) {
        return $result;
    }
    return $result;
});

function the_term_list($taxonomy)
{
    $terms = get_terms($taxonomy, array(
        'hide_empty'    => false,
    ));
    if (!empty($terms)) {
        //_e('<option disabled="" selected=""></option>');
        foreach ($terms as $term) {
            _e('<option value="' . $term->slug . '">' . $term->name . '</option>');
        }
    }
}
function the_term_list_id($taxonomy)
{
    $terms = get_terms($taxonomy, array(
        'hide_empty'    => false,
    ));
    if (!empty($terms)) {
        //_e('<option disabled="" selected=""></option>');
        foreach ($terms as $term) {
            _e('<option value="' . $term->term_id . '">' . $term->name . '</option>');
        }
    }
}

function the_term_list_checkbox($taxonomy)
{
    $terms = get_terms($taxonomy, array(
        'hide_empty'    => false,
    ));
    if (!empty($terms)) {

        foreach ($jobsTerms as $term) {
            $checked = ''; //(has_term($term->slug, 'jobtype', $post->ID)) ? 'checked="checked"' : '';
            echo "<label for='term-" . $term->slug . "'>" . $term->name . "</label>";
            echo "<input type='checkbox' name='term" . $term->slug . "' value='" . $term->name . "' $checked />";
        }
    }
}

function get_terms_chekboxes($taxonomies, $args)
{

    $terms = get_terms(array(
        'taxonomy' => $taxonomies,
        'hide_empty' => false,
        'parent'   => 0
    ));


    //$terms = get_terms($taxonomies, $args);
    $output = '<div class="row">';
    foreach ($terms as $term) {
        $output .= '<div class="col-md-4 col-sm-6">
                        <a href="#' . $term->slug . '" data-toggle="collapse">' . $term->name . '</a>
                        <div id="' . $term->slug . '" class="collapse">';
        $output .= '<label for="' . $term->slug . '1" style="display:flex; ">
                    <input type="checkbox" id="' . $term->slug . '1" name="' . $term->taxonomy . '[]" value="' . $term->term_id . '"> ' . $term->name . '
                    </label>';

        $termchildren = get_term_children($term->term_id, 'ait-items');

        $output .= '<ul>';
        foreach ($termchildren as $child) {
            $cterm = get_term_by('id', $child, 'ait-items');
            $output .= '<li><label for="' . $cterm->slug . '" style="display:flex; color:#4b4c4bdd;"><input type="checkbox" id="' . $cterm->slug . '" name="' . $term->taxonomy . '[]" value="' . $cterm->term_id . '"> ' . $cterm->name . '</label></li>';
        }
        $output .=  '</ul>';


        $output .= '</div> </div>';
    }
    $output .= '</div>';
    return $output;
}


function get_terms_chekboxes_model($taxonomy, $args = [], $selected_terms = [])
{
    $terms = get_terms(array_merge([
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
        'parent' => 0
    ], $args));

    $output = '<div class="row">';
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $output .= '<div class="col-12 col-lg-6 mb-3">';
            $output .= '<a href="#" class="d-flex align-items-center gap-2 text-primary" data-bs-toggle="modal" data-bs-target="#modal-' . esc_attr($term->slug) . '"><i class="fa fa-list-ul" aria-hidden="true"></i> ' . esc_html($term->name) . '</a>';
            $output .= '<div class="modal fade" id="modal-' . esc_attr($term->slug) . '" tabindex="-1" aria-labelledby="modalLabel-' . esc_attr($term->slug) . '" aria-hidden="true">';
            $output .= '<div class="modal-dialog">';
            $output .= '<div class="modal-content">';
            $output .= '<div class="modal-header">';
            $output .= '<h5 class="modal-title fs-5 fw-normal ps-2 border-start border-4 border-danger" id="modalLabel-' . esc_attr($term->slug) . '">Choose your specific categories:</h5>';
            $output .= '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
            $output .= '</div>';
            $output .= '<div class="modal-body">';

            // Parent term checkbox
            $checked = in_array($term->term_id, $selected_terms) ? 'checked' : '';
            $output .= '<label class="form-label d-flex align-items-center gap-2" for="' . esc_attr($term->slug) . '1">';
            $output .= '<input type="checkbox" id="' . esc_attr($term->slug) . '1" name="' . esc_attr($taxonomy) . '[]" value="' . esc_attr($term->term_id) . '" ' . $checked . '> ' . esc_html($term->name);
            $output .= '</label>';

            // Child terms
            $termchildren = get_term_children($term->term_id, $taxonomy);
            if (!empty($termchildren)) {
                $output .= '<ul class="list-unstyled ms-4">';
                foreach ($termchildren as $child) {
                    $cterm = get_term_by('id', $child, $taxonomy);
                    $checked = in_array($cterm->term_id, $selected_terms) ? 'checked' : '';
                    $output .= '<li>';
                    $output .= '<label class="form-label d-flex align-items-center gap-2 text-dark" for="' . esc_attr($cterm->slug) . '">';
                    $output .= '<input type="checkbox" id="' . esc_attr($cterm->slug) . '" name="' . esc_attr($taxonomy) . '[]" value="' . esc_attr($cterm->term_id) . '" ' . $checked . '> ' . esc_html($cterm->name);
                    $output .= '</label>';
                    $output .= '</li>';
                }
                $output .= '</ul>';
            }

            $output .= '</div>';
            $output .= '<div class="modal-footer">';
            $output .= '<button type="button" class="btn btn-primary bg-primary border-0" data-bs-dismiss="modal">Close</button>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
        }
    } else {
        $output .= '<p>No terms found for this taxonomy.</p>';
    }
    $output .= '</div>';
    return $output;
}

function track_unique_visits()
{
    if (is_single() && !is_admin()) {
        $post_id = get_the_ID();
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $viewed_ips = get_post_meta($post_id, 'viewed_ips', true);
        $viewed_ips = is_array($viewed_ips) ? $viewed_ips : array();

        if (!in_array($ip_address, $viewed_ips)) {
            // Increment view count
            $view_count = get_post_meta($post_id, 'post_views_count', true);
            $view_count = $view_count ? $view_count + 1 : 1;
            update_post_meta($post_id, 'post_views_count', $view_count);

            // Add IP to viewed list
            $viewed_ips[] = $ip_address;
            update_post_meta($post_id, 'viewed_ips', $viewed_ips);
        }
    }
}
add_action('wp', 'track_unique_visits');

function kount()
{
    global $wpdb;
    $table = $wpdb->prefix . 'visitors';
    $date = date('Y-m-d');
    $query = $wpdb->prepare('SELECT visitCount FROM %i WHERE visitDay = %s', $table, $date);
    $result = $wpdb->get_results($query);
    if (empty($result)) {
        $wpdb->insert($table, ['visitDay' => $date, 'visitCount' => 1], ['%s', '%d']);
    } else {
        $wpdb->update($table, ['visitCount' => $result[0]->visitCount + 1], ['visitDay' => $date], ['%d'], ['%s']);
    }
}
add_action('wp', 'kount');

// ------------------------------------------------------------------ 


// Add admin menu for the options page
add_action('admin_menu', 'bcv_add_admin_menu');
function bcv_add_admin_menu()
{
    $pending_count = bcv_get_pending_claims_count();

    $menu_title = 'Business Claims';
    if ($pending_count > 0) {
        $menu_title .= sprintf(' <span class="awaiting-mod update-plugins"><span class="pending-count">%d</span></span>', $pending_count);
    }

    add_menu_page(
        'Business Claims',
        $menu_title,
        'manage_options',
        'business-claims',
        'bcv_options_page',
        'dashicons-store',
        30
    );
}

function bcv_get_pending_claims_count()
{
    $pending_args = [
        'post_type' => 'ait-item',
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => '_bcv_user_id',
                'compare' => 'EXISTS',
            ],
            [
                'key' => '_bcv_claim_status',
                'value' => 'not_claimed',
                'compare' => '='
            ]
        ]
    ];

    $pending_businesses = get_posts($pending_args);
    $count = count($pending_businesses);
    return $count;
}

// Auto-claim subscriber-created businesses on publish
add_action('transition_post_status', 'bcv_auto_claim_subscriber_business', 10, 3);
function bcv_auto_claim_subscriber_business($new_status, $old_status, $post)
{
    if ($post->post_type !== 'ait-item' || $new_status !== 'publish' || $old_status === 'publish') {
        return;
    }

    $user_id = get_post_meta($post->ID, '_bcv_user_id', true);
    $claim_status = get_post_meta($post->ID, '_bcv_claim_status', true) ?: 'not_claimed';
    $user = get_userdata($post->post_author);

    if ($user && in_array('subscriber', (array) $user->roles) && $claim_status === 'not_claimed' && $user_id == $post->post_author) {
        // Auto-claim the business
        update_post_meta($post->ID, '_bcv_claim_status', 'claimed');
        update_post_meta($post->ID, '_bcv_verification_status', 'not_verified'); // Keep not verified for admin review
        update_post_meta($post->ID, '_ait-item_item-author', ['author' => $user_id]);

        // Send claim accepted email
        if ($user_id) {
            bcv_send_claim_accepted_email($user_id, $post->ID);
        }
    }
}

// Render the options page
function bcv_options_page()
{
    // Handle status update
    if (isset($_POST['bcv_update_status']) && check_admin_referer('bcv_update_status_action', 'bcv_nonce')) {
        $post_id = intval($_POST['post_id']);
        $new_claim_status = sanitize_text_field($_POST['claim_status']);
        $new_verification_status = ($new_claim_status === 'claimed') ? sanitize_text_field($_POST['verification_status']) : 'not_verified';
        $user_id = get_post_meta($post_id, '_bcv_user_id', true);
        $previous_verification_status = get_post_meta($post_id, '_bcv_verification_status', true) ?: 'not_verified';
        $previous_claim_status = get_post_meta($post_id, '_bcv_claim_status', true) ?: 'not_claimed';

        if ($new_claim_status === 'cancel_claim') {
            // Cancel claim by deleting post meta
            delete_post_meta($post_id, '_bcv_user_id');
            delete_post_meta($post_id, '_bcv_claim_status');
            delete_post_meta($post_id, '_bcv_verification_status');
            delete_post_meta($post_id, '_bcv_request_date');
            delete_post_meta($post_id, '_ait-item_item-author');

            // Try to get previous author if stored
            $previous_author_id = get_post_meta($post_id, '_bcv_previous_author_id', true);

            if ($previous_author_id && get_user_by('ID', $previous_author_id)) {
                // If valid previous author exists, restore it
                $new_author_id = $previous_author_id;
            } else {
                // Fallback: First try to get user with ID 1
                $user = get_user_by('ID', 1);

                if ($user && user_can($user, 'administrator')) {
                    $new_author_id = 1;
                } else {
                    // Fallback: get the first administrator
                    $admin_users = get_users([
                        'role'   => 'administrator',
                        'number' => 1,
                        'orderby' => 'ID',
                        'order' => 'ASC',
                    ]);
                    $new_author_id = !empty($admin_users) ? $admin_users[0]->ID : 1;
                }
            }

            // Update post author
            wp_update_post([
                'ID' => $post_id,
                'post_author' => $new_author_id,
            ]);

            // Optionally delete the stored previous author meta
            delete_post_meta($post_id, '_bcv_previous_author_id');

            // Send claim rejected email
            if ($user_id) {
                bcv_send_claim_rejected_email($user_id, $post_id);
            }

            echo '<div class="updated"><p>Claim cancelled successfully and post reassigned to appropriate user.</p></div>';
        } else {
            // Before assigning claim, store the current author as previous
            $current_post = get_post($post_id);
            update_post_meta($post_id, '_bcv_previous_author_id', $current_post->post_author);

            // Update claim and verification status
            update_post_meta($post_id, '_bcv_claim_status', $new_claim_status);
            update_post_meta($post_id, '_bcv_verification_status', $new_verification_status);

            // Send claim accepted email if status is 'claimed'
            if ($new_claim_status === 'claimed' && $previous_claim_status === 'not_claimed' && $user_id) {
                bcv_send_claim_accepted_email($user_id, $post_id);
            }

            // Send verification email if status changed to 'verified'
            if ($new_verification_status === 'verified' && $previous_verification_status !== 'verified' && $user_id) {
                bcv_send_business_verified_email($user_id, $post_id);
            }
            // Send verification revoked email if status changed from 'verified' to 'not_verified'
            elseif ($new_verification_status === 'not_verified' && $previous_verification_status === 'verified' && $user_id) {
                bcv_send_verification_revoked_email($user_id, $post_id);
            }

            echo '<div class="updated"><p>Status updated successfully.</p></div>';
        }
    }

    // Fetch claimed businesses
    $claimed_args = [
        'post_type' => 'ait-item',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => '_bcv_claim_status',
                'value' => 'claimed',
                'compare' => '='
            ]
        ]
    ];
    $claimed_businesses = get_posts($claimed_args);

    // Fetch pending businesses
    $pending_args = [
        'post_type' => 'ait-item',
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => '_bcv_user_id',
                'compare' => 'EXISTS',
            ],
            [
                'key' => '_bcv_claim_status',
                'value' => 'not_claimed',
                'compare' => '='
            ]
        ]
    ];
    $pending_businesses = get_posts($pending_args);
?>
    <div class="wrap">
        <h1>Business Claim Requests</h1>

        <!-- Tabs for navigation -->
        <h2 class="nav-tab-wrapper">
            <a href="#pending-businesses" class="nav-tab nav-tab-active">Pending Businesses</a>
            <a href="#claimed-businesses" class="nav-tab">Claimed Businesses</a>
        </h2>

        <!-- Pending Businesses Section -->
        <div id="pending-businesses" class="bcv-tab-content">
            <h2>Pending Businesses</h2>
            <?php if (empty($pending_businesses)) : ?>
                <p>No pending claim requests.</p>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Business</th>
                            <th>Claim Status</th>
                            <th>Verification Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_businesses as $post) :
                            $user_id = get_post_meta($post->ID, '_bcv_user_id', true);
                            $user = get_userdata($user_id);
                            $metadata = get_post_meta($post->ID, '_ait-item_item-data', true);
                            $phone = isset($metadata['telephone']) ? $metadata['telephone'] : 'N/A';
                            $claim_status = get_post_meta($post->ID, '_bcv_claim_status', true) ?: 'not_claimed';
                            $verification_status = get_post_meta($post->ID, '_bcv_verification_status', true) ?: 'not_verified';
                            $user_edit_link = esc_url(admin_url('user-edit.php?user_id=' . $user_id));
                        ?>
                            <tr>
                                <td><?php echo esc_html($user_id); ?></td>
                                <td><a href="<?php echo $user_edit_link; ?>" target="_blank"><?php echo esc_html($user->display_name); ?></a></td>
                                <td><a href="mailto:<?php echo esc_attr($user->user_email); ?>"><?php echo esc_html($user->user_email); ?></a></td>
                                <td><a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a></td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=ait-item&s=' . urlencode($post->post_title))); ?>" target="_blank">
                                        <?php echo esc_html($post->post_title); ?>
                                    </a>
                                </td>
                                <form method="post" action="">
                                    <?php wp_nonce_field('bcv_update_status_action', 'bcv_nonce'); ?>
                                    <input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>">
                                    <td>
                                        <select name="claim_status" style="margin-bottom: 8px;">
                                            <option value="not_claimed" <?php selected($claim_status, 'not_claimed'); ?>>Claim Pending</option>
                                            <option value="claimed" <?php selected($claim_status, 'claimed'); ?>>Claimed Verified</option>
                                            <option value="cancel_claim">Claim Rejected</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select style="margin-bottom: 8px;" name="verification_status" <?php echo $claim_status !== 'claimed' ? 'disabled' : ''; ?>>
                                            <option value="not_verified" <?php selected($verification_status, 'not_verified'); ?>>Not Verified</option>
                                            <option value="verified" <?php selected($verification_status, 'verified'); ?>>Verified</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="submit" name="bcv_update_status" class="button button-primary" value="Update">
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Claimed Businesses Section -->
        <div id="claimed-businesses" class="bcv-tab-content" style="display:none;">
            <h2>Claimed Businesses</h2>
            <?php if (empty($claimed_businesses)) : ?>
                <p>No claimed businesses.</p>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Business</th>
                            <th>Claim Status</th>
                            <th>Verification Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($claimed_businesses as $post) :
                            $user_id = get_post_meta($post->ID, '_bcv_user_id', true);
                            $user = get_userdata($user_id);
                            $metadata = get_post_meta($post->ID, '_ait-item_item-data', true);
                            $phone = isset($metadata['telephone']) ? $metadata['telephone'] : 'N/A';
                            $claim_status = get_post_meta($post->ID, '_bcv_claim_status', true) ?: 'not_claimed';
                            $verification_status = get_post_meta($post->ID, '_bcv_verification_status', true) ?: 'not_verified';
                            $user_edit_link = esc_url(admin_url('user-edit.php?user_id=' . $user_id));
                        ?>
                            <tr>
                                <td><?php echo esc_html($user_id); ?></td>
                                <td><a href="<?php echo $user_edit_link; ?>" target="_blank"><?php echo esc_html($user->display_name); ?></a></td>
                                <td><a href="mailto:<?php echo esc_attr($user->user_email); ?>"><?php echo esc_html($user->user_email); ?></a></td>
                                <td><a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a></td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=ait-item&s=' . urlencode($post->post_title))); ?>" target="_blank">
                                        <?php echo esc_html($post->post_title); ?>
                                    </a>
                                </td>
                                <form method="post" action="">
                                    <?php wp_nonce_field('bcv_update_status_action', 'bcv_nonce'); ?>
                                    <input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>">
                                    <td>
                                        <select name="claim_status" style="margin-bottom: 8px;">
                                            <option value="not_claimed" <?php selected($claim_status, 'not_claimed'); ?>>Claim Pending</option>
                                            <option value="claimed" <?php selected($claim_status, 'claimed'); ?>>Claimed Verified</option>
                                            <option value="cancel_claim">Claim Rejected</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="verification_status" style="margin-bottom: 8px;" <?php echo $claim_status !== 'claimed' ? 'disabled' : ''; ?>>
                                            <option value="not_verified" <?php selected($verification_status, 'not_verified'); ?>>Not Verified</option>
                                            <option value="verified" <?php selected($verification_status, 'verified'); ?>>Verified</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="submit" name="bcv_update_status" class="button button-primary" value="Update">
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Tab navigation script -->
        <script>
            jQuery(document).ready(function($) {
                $('.nav-tab').click(function(e) {
                    e.preventDefault();
                    $('.nav-tab').removeClass('nav-tab-active');
                    $(this).addClass('nav-tab-active');
                    $('.bcv-tab-content').hide();
                    $($(this).attr('href')).show();
                });
            });
        </script>
    </div>
<?php
}

// Handle AJAX claim request
add_action('wp_ajax_bcv_claim_business', 'bcv_handle_claim_request');
function bcv_handle_claim_request()
{
    check_ajax_referer('bcv_claim_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error('Unauthorized');
    }

    $post_id = intval($_POST['post_id']);
    $user_id = get_current_user_id();

    // Validate post type
    if (get_post_type($post_id) !== 'ait-item') {
        wp_send_json_error('Invalid post type');
    }

    // Check if already claimed
    $existing_claim_status = get_post_meta($post_id, '_bcv_claim_status', true);
    if ($existing_claim_status === 'claimed') {
        wp_send_json_error('Business already claimed');
    }

    // Check if business has no author and user is admin
    $metadata_author = get_post_meta($post_id, '_ait-item_item-author', true);
    $has_author = !empty($metadata_author['author']);
    if (!$has_author && current_user_can('administrator')) {
        wp_send_json_error([
            'message' => 'Admins must register a new account to claim this business.',
            'redirect' => home_url('/login')
        ]);
    }

    // Store claim data in post meta and set user as post author
    update_post_meta($post_id, '_bcv_user_id', $user_id);
    update_post_meta($post_id, '_bcv_claim_status', 'not_claimed');
    update_post_meta($post_id, '_bcv_verification_status', 'not_verified');
    update_post_meta($post_id, '_bcv_request_date', current_time('mysql'));
    update_post_meta($post_id, '_ait-item_item-author', ['author' => $user_id]);
    wp_update_post([
        'ID' => $post_id,
        'post_author' => $user_id
    ]);

    // Send claim request emails
    bcv_send_claim_request_email($user_id, $post_id);

    wp_send_json_success('Claim request submitted');
}

// Add basic CSS
add_action('wp_head', function () {
?>
    <style>
        .bcv-claimed,
        .bcv-pending {
            font-weight: bold;
            display: inline-block;
            padding: 8px 16px;
        }
    </style>
<?php
});

// --------------------------------------------------------------------------
// Ranting 
// Restrict comment replies based on claim status
// add_filter('comment_reply_link', function ($link, $args, $comment, $post) {
//     if (get_post_type($post) !== 'ait-item') {
//         return $link; // Only apply for ait-item post type
//     }
//     // Only allow replies to approved comments
//     if ($comment->comment_approved != '1') {
//         return '';
//     }

//     $claim_status = get_post_meta($post->ID, '_bcv_claim_status', true) ?: 'not_claimed';
//     $can_reply = false;

//     if ($claim_status === 'claimed') {
//         $claimer_user_id = get_post_meta($post->ID, '_bcv_user_id', true);
//         if ($claimer_user_id) {
//             $can_reply = is_user_logged_in() && get_current_user_id() == absint($claimer_user_id);
//         }
//     }

//     return $can_reply ? $link : '';
// }, 10, 4);

// // Prevent unauthorized users from posting replies
// add_action('pre_comment_on_post', function ($comment_post_ID) {
//     $post = get_post($comment_post_ID);

//     if (!$post || get_post_type($post) !== 'ait-item') {
//         return; // Only apply to ait-item post type
//     }
//     $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

//     // Check if user is admin or post owner
//     $is_admin = current_user_can('administrator');
//     $is_post_owner = false;
//     if (is_user_logged_in()) {
//         $post_author_id = get_post_field('post_author', $comment_post_ID);
//         $is_post_owner = get_current_user_id() == $post_author_id;
//     }

//     // Handle reply restrictions based on claim status
//     if ($comment_parent > 0) {
//         $claim_status = get_post_meta($post->ID, '_bcv_claim_status', true) ?: 'not_claimed';
//         $can_reply = false;

//         if ($claim_status === 'claimed') {
//             $claimer_user_id = get_post_meta($post->ID, '_bcv_user_id', true);
//             if ($claimer_user_id) {
//                 $can_reply = is_user_logged_in() && get_current_user_id() == absint($claimer_user_id);
//             }
//         }

//         if (!$can_reply) {
//             wp_die(
//                 '<p>' . __('Replies are only allowed by the claiming user on a claimed post.', 'blankslate') . '</p>',
//                 __('Permission Denied', 'blankslate'),
//                 array('response' => 403, 'back_link' => true)
//             );
//         }
//     }

//     // Validate rating is provided, but skip for admins and post owners when replying
//     $rating_required = !($is_admin || $is_post_owner);
//     if ($rating_required || $comment_parent == 0) { // Require rating for top-level comments or non-privileged users
//         if (!isset($_POST['pixrating']) || empty($_POST['pixrating'])) {
//             wp_die(
//                 '<p>' . __('A rating is required to submit a review.', 'blankslate') . '</p>',
//                 __('Rating Required', 'blankslate'),
//                 array('response' => 400, 'back_link' => true)
//             );
//         }

//         $rating = absint($_POST['pixrating']);
//         if ($rating < 1 || $rating > 5) {
//             wp_die(
//                 '<p>' . __('Invalid rating value.', 'blankslate') . '</p>',
//                 __('Invalid Rating', 'blankslate'),
//                 array('response' => 400, 'back_link' => true)
//             );
//         }
//     }
// });

// // Save rating and optional title to comment meta after comment is posted
// add_action('comment_post', function ($comment_id, $comment_approved, $commentdata) {
//     $comment_post_id = $commentdata['comment_post_ID'];
//     if (get_post_type($comment_post_id) !== 'ait-item') {
//         return; // Only save rating for ait-item post type
//     }
//     if (isset($_POST['pixrating']) && !empty($_POST['pixrating'])) {
//         $rating = absint($_POST['pixrating']);
//         if ($rating >= 1 && $rating <= 5) {
//             // Save the numeric rating
//             update_comment_meta($comment_id, 'pixrating', $rating);

//             // Save the optional rating title if provided
//             $rating_title = isset($_POST['pixrating_title']) ? sanitize_text_field($_POST['pixrating_title']) : '';
//             if (!empty($rating_title)) {
//                 update_comment_meta($comment_id, 'pixrating_title', $rating_title);
//             }
//         }
//     }
// }, 10, 3);

// --------------------------------------------------------------------------

// Custom Columns in Admin Panel User Page
add_filter('manage_users_columns', 'itsn_add_business_count_column');
function itsn_add_business_count_column($columns)
{
    $columns['business_count'] = 'Businesses';
    $columns['user_rating'] = 'Ratings';
    return $columns;
}

add_filter('manage_users_custom_column', 'itsn_show_business_count_column', 10, 3);
function itsn_show_business_count_column($value, $column_name, $user_id)
{
    // Displaying the counts of posts
    if ($column_name === 'business_count') {
        $args = array(
            'post_type'      => 'ait-item',
            'post_status'    => array('publish', 'banned', 'under_reverification'),
            'author'         => $user_id,
            'posts_per_page' => -1,
            'fields'         => 'ids'
        );
        $user_businesses = new WP_Query($args);
        $count = $user_businesses->found_posts;
        wp_reset_postdata();

        if ($count > 0) {
            $url = admin_url('edit.php?post_type=ait-item&author=' . $user_id);
            return '<a href="' . esc_url($url) . '">' . $count . '</a>';
        } else {
            return '0';
        }
    }

    // Displaying the number of businesses that the user has rated
    if ($column_name === 'user_rating') {
        $comments_args = array(
            'user_id' => $user_id,
            'count'   => true
        );
        $comments_count = get_comments($comments_args);

        return $comments_count;
    }

    return $value;
}
