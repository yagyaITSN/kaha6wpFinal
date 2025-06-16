<?php
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

$mailStyles = '
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
';

// Registration mail
function regMail()
{
    global $mailStyles, $mailFooter;
    $current_user = wp_get_current_user();
    $user_email = $current_user->user_email;
    $user_name = $current_user->display_name;
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_image_src($custom_logo_id, 'full');
    $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'src="https://kaha6.com/wp-content/uploads/logo.png';

    $subject = 'Kaha6 Registration';

    $message = '
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($subject) . '</title>
    <style>
        ' . $mailStyles . '
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
                <h1>Dear ' . esc_html($user_name) . '</h1>
                <p>You have been registered successfully.</p>
                <p>Thank you for connecting with us.</p>
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
            ' . $mailFooter . '
        </tr>
    </table>
</body>

</html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
        'Reply-To: no-reply@kaha6.com'
    ];
    wp_mail($user_email, $subject, $message, $headers);
}

// Mail when user list business
function ait_item_submission_user_email($post_id, $post, $update)
{
    global $mailStyles, $mailFooter;
    // Check for ait-item post type
    if ($post->post_type !== 'ait-item' || $update) {
        return;
    }

    $current_user = wp_get_current_user();
    $user_email = $current_user->user_email;
    $post_title = get_the_title($post_id);
    $user_name = $current_user->display_name;
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_image_src($custom_logo_id, 'full');
    $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/logo.png';
    $business_email = get_post_meta($post_id, '_ait-item_item-data', true)['email'] ?? '';

    $subject = 'Kaha6 Business Listing';

    $message = '
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($subject) . '</title>
    <style>
        ' . $mailStyles . '
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
                <h1>Dear ' . esc_html($user_name) . '</h1>
                <p>Your business titled "<strong>' . esc_html($post_title) . '</strong>" has been submitted successfully and is now under review.</p>
                <p>We will notify you once it has been reviewed and published.</p>
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
            ' . $mailFooter . '
        </tr>
    </table>
</body>
</html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
        'Reply-To: no-reply@kaha6.com'
    ];
    $recipients = [$user_email];
    if (is_email($business_email)) {
        $recipients[] = $business_email;
    }
    wp_mail($recipients, $subject, $message, $headers);
}
add_action('wp_insert_post', 'ait_item_submission_user_email', 10, 3);

// Email to admin when company has been listed
function ait_item_submission_admin_email($post_id, $post, $update)
{
    global $mailStyles, $mailFooter;
    //   check for ait-item post type
    if ($post->post_type !== 'ait-item' || $update) {
        return;
    }

    $current_user = wp_get_current_user();
    $user_name = $current_user->display_name;
    $post_title = get_the_title($post_id);
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_image_src($custom_logo_id, 'full');
    $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/logo.png';

    $subject = 'New Business Listed : ' . esc_html($post_title);

    $message = '
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($subject) . '</title>
    <style>
        ' . $mailStyles . '
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
                <h1>New Business Listing Submitted</h1>
                <p>A new business has been listed titled "<strong>' . esc_html($post_title) . '</strong>" which has been submitted by ' . esc_html($user_name) . '.</p>
                <p>Please review the post in the admin dashboard and publish it if it meets the requirements.</p>
                <p><a href="' . esc_url(admin_url('post.php?post=' . $post_id . '&action=edit')) . '" style="text-decoration: none; color: white; background-color: #C1272D; padding: 8px 10px; border: none; border-radius: 4px;">Review Post</a></p>
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
            ' . $mailFooter . '
        </tr>
    </table>
</body>
</html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
        'Reply-To: no-reply@kaha6.com'
    ];

    // Get all admin emails
    // $admin_emails = [];
    // $users = get_users(['role' => 'administrator']);
    // foreach ($users as $user) {
    //     $admin_emails[] = $user->user_email;
    // }

    // if (!empty($admin_emails)) {
    //     wp_mail($admin_emails, $subject, $message, $headers);
    // }
    $admin_emails = array('a4ajayyadav77777@gmail.com', 'yagya.majhi@itservicenepal.com');
    wp_mail($admin_emails, $subject, $message, $headers);
}
add_action('wp_insert_post', 'ait_item_submission_admin_email', 10, 3);

// Email when the post has been published
function ait_item_published_user_email($new_status, $old_status, $post)
{
    global $mailStyles, $mailFooter;
    // check for post type ait-item
    if ($post->post_type !== 'ait-item' || $new_status !== 'publish' || in_array($old_status, ['publish', 'auto-draft'])) {
        return;
    }

    $user = get_user_by('ID', $post->post_author);
    $user_email = $user->user_email;
    $user_name = $user->display_name;
    $post_title = get_the_title($post->ID);
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_image_src($custom_logo_id, 'full');
    $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/logo.png';
    $business_email = get_post_meta($post->ID, '_ait-item_item-data', true)['email'] ?? '';

    $subject = 'Kaha6 Business listing Published';

    $message = '
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($subject) . '</title>
    <style>
        ' . $mailStyles . '
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
                <h1>Dear ' . esc_html($user_name) . '</h1>
                <p>Your business listing titled "<strong>' . esc_html($post_title) . '</strong>" has been reviewed and published successfully.</p>
                <p>View your listing</p>
                <p><a href="' . esc_url(get_permalink($post->ID)) . '" style="text-decoration: none; color: white; background-color: #C1272D; padding: 8px 10px; border: none; border-radius: 4px;">Here</a></p>
                <p>Thank you for your business listing</p>
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
            ' . $mailFooter . '
        </tr>
    </table>
</body>
</html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
        'Reply-To: no-reply@kaha6.com'
    ];
    $recipients = [$user_email];
    if (is_email($business_email)) {
        $recipients[] = $business_email;
    }

    wp_mail($recipients, $subject, $message, $headers);
}
add_action('transition_post_status', 'ait_item_published_user_email', 10, 3);

// Email when post is under under reverification
function ait_item_under_reverification_user_email($new_status, $old_status, $post)
{
    global $mailStyles, $mailFooter;
    // Check for post type of ait-item
    if ($post->post_type !== 'ait-item' || $new_status !== 'under_reverification' || $old_status === 'under_reverification') {
        return;
    }

    $user = get_user_by('ID', $post->post_author);
    $user_email = $user->user_email;
    $user_name = $user->display_name;
    $post_title = get_the_title($post->ID);
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_image_src($custom_logo_id, 'full');
    $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/logo.png';
    $business_email = get_post_meta($post->ID, '_ait-item_item-data', true)['email'] ?? '';


    $subject = 'Kaha6 Business listing Under Reverification';

    $message = '
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($subject) . '</title>
    <style>
        ' . $mailStyles . '
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
                <h1>Dear ' . esc_html($user_name) . '</h1>
                <p>Your business listing titled "<strong>' . esc_html($post_title) . '</strong>" is now under reverification.</p>
                <p>We are reviewing your listing to ensure it meets our guidelines. You will be notified once the reverification process is complete.</p>
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
            ' . $mailFooter . '
        </tr>
    </table>
</body>
</html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
        'Reply-To: no-reply@kaha6.com'
    ];
    $recipients = [$user_email];
    if (is_email($business_email)) {
        $recipients[] = $business_email;
    }
    wp_mail($recipients, $subject, $message, $headers);
}
add_action('transition_post_status', 'ait_item_under_reverification_user_email', 10, 3);

// Email when an ait-item is banned/locked
function ait_item_banned_user_email($new_status, $old_status, $post)
{
    global $mailStyles, $mailFooter;
    // Only trigger when post transitions to banned
    if ($post->post_type !== 'ait-item' || $new_status !== 'banned' || $old_status === 'banned') {
        return;
    }

    $user = get_user_by('ID', $post->post_author);
    $user_email = $user->user_email;
    $user_name = $user->display_name;
    $post_title = get_the_title($post->ID);
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_image_src($custom_logo_id, 'full');
    $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/logo.png';
    $business_email = get_post_meta($post->ID, '_ait-item_item-data', true)['email'] ?? '';


    $subject = 'Kaha6 Business Listing Locked';

    $message = '
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($subject) . '</title>
    <style>
        ' . $mailStyles . '
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
                <h1>Dear ' . esc_html($user_name) . '</h1>
                <p>Your business listing titled "<strong>' . esc_html($post_title) . '</strong>" has been locked.</p>
                <p>This action was taken due to a violation of our guidelines. Please contact us for further details.</p>
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
            ' . $mailFooter . '
        </tr>
    </table>
</body>
</html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
        'Reply-To: no-reply@kaha6.com'
    ];
    $recipients = [$user_email];
    if (is_email($business_email)) {
        $recipients[] = $business_email;
    }
    wp_mail($recipients, $subject, $message, $headers);
}
add_action('transition_post_status', 'ait_item_banned_user_email', 10, 3);


// Functinos to send mail on claim request
function bcv_send_claim_request_email($user_id, $post_id)
{
    global $mailStyles, $mailFooter;

    $user = get_userdata($user_id);
    $user_email = $user->user_email;
    $user_name = $user->display_name;
    $business = get_post($post_id);
    $business_title = $business->post_title;
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_image_src($custom_logo_id, 'full');
    $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/logo.png';
    $business_email = get_post_meta($post_id, '_ait-item_item-data', true)['email'] ?? '';


    // Email to user
    $subject_user = 'Kaha6 Business Claim Request Submitted';
    $message_user = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . esc_html($subject_user) . '</title>
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
                    <h1>Dear ' . esc_html($user_name) . '</h1>
                    <p>Your request to claim the business <strong>' . esc_html($business_title) . '</strong> has been submitted successfully.</p>
                    <p>Our team will review your request, and you will be notified once a decision is made.</p>
                    <p>Thank you for choosing Kaha6.</p>
                    <p>Regards, <strong>
                        <a href="' . esc_url(home_url('/')) . '" style="text-decoration: none; color: #C1272D;">
                            Kaha6
                        </a>
                    </strong></p>
                    <div style="border-bottom: 1px solid #C1272D; width: 80%; margin: 20px auto;"></div>
                    <p style="font-size: 12px;">Please do not reply to this email.</p>
                </td>
            </tr>
            <tr>' . $mailFooter . '</tr>
        </table>
    </body>
    </html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
        'Reply-To: no-reply@kaha6.com'
    ];
    $recipients = [$user_email];
    if (is_email($business_email)) {
        $recipients[] = $business_email;
    }
    wp_mail($recipients, $subject_user, $message_user, $headers);

    // Email to admins
    $post_id = get_queried_object_id();
    $post_title = get_the_title($post_id);
    $admin_emails = array('a4ajayyadav77777@gmail.com', 'yagya.majhi@itservicenepal.com');
    $subject_admin = 'Kaha6 New Business Claim Request for ' . esc_html($post_title);
    $message_admin = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . esc_html($subject_admin) . '</title>
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
                    <h1>New Business Claim Request</h1>
                    <p>A new claim request has been submitted for the business <strong>' . esc_html($business_title) . '</strong>.</p>
                    <p><strong>User:</strong> ' . esc_html($user_name) . ' (' . esc_html($user_email) . ')</p>
                    <p>Please review the claim by clicking</p>
                    <p><a href="' . esc_url(admin_url('admin.php?page=business-claims')) . '" style="text-decoration: none; color: white; background-color: #C1272D; padding: 8px 10px; border: none; border-radius: 4px;">Here</a></p>
                    <p>Regards, <strong>
                        <a href="' . esc_url(home_url('/')) . '" style="text-decoration: none; color: #C1272D;">
                            Kaha6
                        </a>
                    </strong></p>
                    <div style="border-bottom: 1px solid #C1272D; width: 80%; margin: 20px auto;"></div>
                    <p style="font-size: 12px;">Please do not reply to this email.</p>
                </td>
            </tr>
            <tr>' . $mailFooter . '</tr>
        </table>
    </body>
    </html>';

    wp_mail($admin_emails, $subject_admin, $message_admin, $headers);
}

// Function to send claim accepted email to user
function bcv_send_claim_accepted_email($user_id, $post_id)
{
    global $mailStyles, $mailFooter;

    $user = get_userdata($user_id);
    $user_email = $user->user_email;
    $user_name = $user->display_name;
    $business = get_post($post_id);
    $business_title = $business->post_title;
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_image_src($custom_logo_id, 'full');
    $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/logo.png';
    $business_email = get_post_meta($post_id, '_ait-item_item-data', true)['email'] ?? '';


    $subject = 'Kaha6 Business Claim Approved';
    $message = '
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
                    <h1>Dear ' . esc_html($user_name) . '</h1>
                    <p>Congratulations! Your claim for the business <strong>' . esc_html($business_title) . '</strong> has been approved.</p>
                    <p>You can now manage your business listing on Kaha6.</p>
                    <p>Thank you for being a part of our community.</p>
                    <p>Regards, <strong>
                        <a href="' . esc_url(home_url('/')) . '" style="text-decoration: none; color: #C1272D;">
                            Kaha6
                        </a>
                    </strong></p>
                    <div style="border-bottom: 1px solid #C1272D; width: 80%; margin: 20px auto;"></div>
                    <p style="font-size: 12px;">Please do not reply to this email.</p>
                </td>
            </tr>
            <tr>' . $mailFooter . '</tr>
        </table>
    </body>
    </html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
        'Reply-To: no-reply@kaha6.com'
    ];
    $recipients = [$user_email];
    if (is_email($business_email)) {
        $recipients[] = $business_email;
    }
    wp_mail($recipients, $subject, $message, $headers);
}

// Function to send claim rejected email to user
function bcv_send_claim_rejected_email($user_id, $post_id)
{
    global $mailStyles, $mailFooter;

    $user = get_userdata($user_id);
    $user_email = $user->user_email;
    $user_name = $user->display_name;
    $business = get_post($post_id);
    $business_title = $business->post_title;
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_image_src($custom_logo_id, 'full');
    $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/logo.png';
    $business_email = get_post_meta($post_id, '_ait-item_item-data', true)['email'] ?? '';


    $subject = 'Kaha6 Business Claim Rejected';
    $message = '
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
                    <h1>Dear ' . esc_html($user_name) . '</h1>
                    <p>We regret to inform you that your claim for the business <strong>' . esc_html($business_title) . '</strong> has been rejected.</p>
                    <p>Please contact our support team for more details or to resolve any issues.</p>
                    <p>Thank you for your interest in Kaha6.</p>
                    <p>Regards, <strong>
                        <a href="' . esc_url(home_url('/')) . '" style="text-decoration: none; color: #C1272D;">
                            Kaha6
                        </a>
                    </strong></p>
                    <div style="border-bottom: 1px solid #C1272D; width: 80%; margin: 20px auto;"></div>
                    <p style="font-size: 12px;">Please do not reply to this email.</p>
                </td>
            </tr>
            <tr>' . $mailFooter . '</tr>
        </table>
    </body>
    </html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
        'Reply-To: no-reply@kaha6.com'
    ];
    $recipients = [$user_email];
    if (is_email($business_email)) {
        $recipients[] = $business_email;
    }
    wp_mail($recipients, $subject, $message, $headers);
}

// Function to send business verified email to user
function bcv_send_business_verified_email($user_id, $post_id)
{
    global $mailStyles, $mailFooter;

    $user = get_userdata($user_id);
    $user_email = $user->user_email;
    $user_name = $user->display_name;
    $business = get_post($post_id);
    $business_title = $business->post_title;
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_image_src($custom_logo_id, 'full');
    $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/logo.png';
    $business_email = get_post_meta($post_id, '_ait-item_item-data', true)['email'] ?? '';


    $subject = 'Kaha6 Business Verification Approved';
    $message = '
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
                    <h1>Dear ' . esc_html($user_name) . '</h1>
                    <p>Congratulations! Your business <strong>' . esc_html($business_title) . '</strong> has been successfully verified.</p>
                    <p>Your verified status enhances the credibility of your listing on Kaha6.</p>
                    <p>Thank you for being a part of our community.</p>
                    <p>Regards, <strong>
                        <a href="' . esc_url(home_url('/')) . '" style="text-decoration: none; color: #C1272D;">
                            Kaha6
                        </a>
                    </strong></p>
                    <div style="border-bottom: 1px solid #C1272D; width: 80%; margin: 20px auto;"></div>
                    <p style="font-size: 12px;">Please do not reply to this email.</p>
                </td>
            </tr>
            <tr>' . $mailFooter . '</tr>
        </table>
    </body>
    </html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
        'Reply-To: no-reply@kaha6.com'
    ];
    $recipients = [$user_email];
    if (is_email($business_email)) {
        $recipients[] = $business_email;
    }
    wp_mail($recipients, $subject, $message, $headers);
}

// Function to send verification revoked email to user
function bcv_send_verification_revoked_email($user_id, $post_id)
{
    global $mailStyles, $mailFooter;

    $user = get_userdata($user_id);
    $user_email = $user->user_email;
    $user_name = $user->display_name;
    $business = get_post($post_id);
    $business_title = $business->post_title;
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_image_src($custom_logo_id, 'full');
    $logo_url = !empty($image[0]) ? esc_url($image[0]) : 'https://kaha6.com/wp-content/uploads/logo.png';
    $business_email = get_post_meta($post_id, '_ait-item_item-data', true)['email'] ?? '';


    $subject = 'Kaha6 Business Verification Revoked';
    $message = '
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
                    <h1>Dear ' . esc_html($user_name) . '</h1>
                    <p>We regret to inform you that the verification for your business <strong>' . esc_html($business_title) . '</strong> has been revoked.</p>
                    <p>Please contact our support team for more details or to resolve any issues.</p>
                    <p>Thank you for your understanding.</p>
                    <p>Regards, <strong>
                        <a href="' . esc_url(home_url('/')) . '" style="text-decoration: none; color: #C1272D;">
                            Kaha6
                        </a>
                    </strong></p>
                    <div style="border-bottom: 1px solid #C1272D; width: 80%; margin: 20px auto;"></div>
                    <p style="font-size: 12px;">Please do not reply to this email.</p>
                </td>
            </tr>
            <tr>' . $mailFooter . '</tr>
        </table>
    </body>
    </html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
        'Reply-To: no-reply@kaha6.com'
    ];
    $recipients = [$user_email];
    if (is_email($business_email)) {
        $recipients[] = $business_email;
    }
    wp_mail($recipients, $subject, $message, $headers);
}
