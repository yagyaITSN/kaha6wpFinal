<?php

/**
 * Send email notification to post author when a review is added or updated
 *
 * @param int $review_id The ID of the review
 * @param string $action 'add' or 'update' depending on the action
 * @param array $review_data The review data including rating, message, etc.
 */
function send_review_notification_email($review_id, $action, $review_data)
{
  // Get the post information
  $post_id = $review_data['company_id'];
  $post = get_post($post_id);
  $post_title = get_the_title($post_id);
  $post_url = get_permalink($post_id);

  // Get the author information
  $author_id = $post->post_author;
  $author = get_userdata($author_id);
  $author_name = $author->display_name;
  $author_email = $author->user_email;

  // Get the reviewer information
  $reviewer = get_userdata($review_data['user_id']);
  $reviewer_name = $reviewer ? $reviewer->display_name : 'Anonymous';

  // Get logo URL (replace with your actual logo path)
  $logo_url = get_template_directory_uri() . '/assets/images/logo.png';

  // Prepare email subject and content based on action
  if ($action === 'add') {
    $subject = 'New Review on Your Post: ' . $post_title;
    $content = sprintf(
      '<p>You have received a new %d-star rating on your post <strong>"%s"</strong>.</p>
<div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #C1272D; margin: 15px 0;">
  <p><strong>Review:</strong> %s</p>
  <p><strong>Reviewer:</strong> %s</p>
</div>
<p>Please <a href="%s">visit your post</a> to reply to this review.</p>',
      $review_data['rating'],
      $post_title,
      nl2br(esc_html($review_data['review'])),
      esc_html($reviewer_name),
      esc_url($post_url)
    );
  } else { // update
    $subject = 'Review Updated on Your Post: ' . $post_title;
    $content = sprintf(
      '<p>A review on your post <strong>"%s"</strong> has been updated to a %d-star rating.</p>
<div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #C1272D; margin: 15px 0;">
  <p><strong>Updated Review:</strong> %s</p>
  <p><strong>Reviewer:</strong> %s</p>
</div>
<p>Please <a href="%s">visit your post</a> to view the changes.</p>',
      $post_title,
      $review_data['rating'],
      nl2br(esc_html($review_data['review'])),
      esc_html($reviewer_name),
      esc_url($post_url)
    );
  }

  // Build the HTML email template
  $message = '
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>' . esc_html($subject) . '</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      color: #333;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
    }

    .header {
      text-align: center;
      padding: 10px 0;
    }

    .content {
      padding: 20px;
    }

    a {
      color: #C1272D;
    }
  </style>
</head>

<body>
  <table class="container" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td class="header">
        <img src="' . esc_url($logo_url) . '" alt="Logo" style="display: block; margin: 10px auto; max-width: 100px">
      </td>
    </tr>
    <tr>
      <td class="content">
        <h1>Dear ' . esc_html($author_name) . '</h1>
        ' . $content . '
        <p>Regards, <strong>
            <a href="' . esc_url(home_url('/')) . '" style="text-decoration: none; color: #C1272D;">
              Kaha6
            </a>
          </strong></p>
        <div style="border-bottom: 1px solid #C1272D; width: 80%; margin: 20px auto;"></div>
      </td>
    </tr>
    <tr>
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
      </td>
    </tr>
  </table>
</body>

</html>';

  // Set the email headers
  $headers = array(
    'Content-Type: text/html; charset=UTF-8',
    'From: KAHA6 Nepali Business Directory <no-reply@kaha6.com>',
    'Reply-To: no-reply@kaha6.com'
  );

  // Send the email
  wp_mail($author_email, $subject, $message, $headers);
}

// Hook into review submission and update
add_action('review_submitted', 'handle_review_notification', 10, 2);
function handle_review_notification($review_id, $review_data)
{
  send_review_notification_email($review_id, 'add', $review_data);
}

add_action('review_updated', 'handle_review_update_notification', 10, 2);
function handle_review_update_notification($review_id, $review_data)
{
  send_review_notification_email($review_id, 'update', $review_data);
}
