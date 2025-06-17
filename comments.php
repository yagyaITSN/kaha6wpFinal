<?php

/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @package WordPress
 */

// Do not allow direct access to this file.
if (!defined('ABSPATH')) {
  exit;
}
?>

<style>
  .logged-in-as {
    display: none;
  }
</style>

<!-- Comment and Contact Form -->
<?php if (comments_open()): ?>
  <div class="comment-form-itsn d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="col-md-6 col-12">

      <form action="<?php echo site_url('/wp-comments-post.php'); ?>" method="post" id="commentForm">
        <?php if (!is_user_logged_in()) : ?>
          <div class="form-group mb-3">
            <label for="name" class="form-label"><?php _e('Name*'); ?></label>
            <input type="text" class="form-control" id="name" name="author" required>
          </div>
          <div class="form-group mb-3">
            <label for="email" class="form-label"><?php _e('Email'); ?></label>
            <input type="email" class="form-control" id="email" name="email">
          </div>
        <?php endif; ?>

        <div class="form-group mb-3">
          <label for="message" class="form-label"><?php _e('Message*'); ?></label>
          <textarea class="form-control" id="message" name="comment" rows="4" required></textarea>
        </div>

        <?php comment_id_fields(); ?>
        <?php do_action('comment_form', get_the_ID()); ?>

        <button type="submit" class="btn btn-custom-red"><?php _e('Submit'); ?></button>

        <p class="form-text text-muted mt-2">
          <?php _e('Your comment will be visible after admin approval.'); ?>
        </p>
      </form>
    </div>

    <div class="col-12 col-md-6 form-itsn d-flex flex-column justify-content-center gap-3"
      style="max-width: max-content;">

      <!-- Banner -->
      <?php get_template_part('ads/comment/blog-details/common/common', 'ad-one'); ?>
      <!-- End Banner -->

      <!-- Banner -->
      <?php get_template_part('ads/comment/blog-details/md/md', 'ad-one'); ?>
      <!-- End Banner -->

    </div>
  </div>
<?php endif; ?>
<!-- Comments Display -->
<div class="comments-container mt-4" id="commentsContainer">
  <?php if (have_comments()) : ?>
    <?php
    // Get current user's email if logged in
    $current_user = wp_get_current_user();
    $current_user_id = $current_user->ID;

    // Filter comments based on moderation status (only top-level comments)
    $comments = get_comments(array(
      'post_id' => get_the_ID(),
      'status'  => 'approve',
      'parent'  => 0, // Only fetch top-level comments
    ));

    // Include pending comments only for the commenter or admin
    $pending_comments = array();
    if (is_user_logged_in() || current_user_can('manage_options')) {
      $pending_comments = get_comments(array(
        'post_id' => get_the_ID(),
        'status'  => 'hold',
        'parent'  => 0,
      ));
    }

    $all_comments = array_merge($comments, $pending_comments);

    if ($all_comments) :
      wp_list_comments(array(
        'style'       => 'div',
        'short_ping'  => true,
        'avatar_size' => 40,
        'callback'    => 'custom_comment_callback',
      ), $all_comments);
    ?>
      <?php if (get_comment_pages_count($all_comments) > 1 && get_option('page_comments')) : ?>
        <nav class="comment-navigation" role="navigation">
          <div class="nav-previous"><?php previous_comments_link(__('Older Comments')); ?></div>
          <div class="nav-next"><?php next_comments_link(__('Newer Comments')); ?></div>
        </nav>
      <?php endif; ?>
    <?php else : ?>
      <p class="no-comments"><?php _e('No comments yet.'); ?></p>
    <?php endif; ?>
  <?php else : ?>
    <p class="no-comments"><?php _e('No comments yet.'); ?></p>
  <?php endif; ?>

  <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
    <p class="no-comments"><?php _e('Comments are closed.'); ?></p>
  <?php endif; ?>
</div>

<?php
// Custom comment callback function with updated layout and proper threading
function custom_comment_callback($comment, $args, $depth)
{
  $GLOBALS['comment'] = $comment;

  // Check if comment is pending moderation
  $is_pending = $comment->comment_approved === '0';
  $current_user = wp_get_current_user();
  $current_user_id = $current_user->ID;
  $is_author = $current_user_id && $current_user_id == $comment->user_id;
  $is_admin = current_user_can('manage_options');

  // Display comment only if approved, or if pending and the commenter or admin
  if (!$is_pending || ($is_pending && ($is_author || $is_admin))) :
    // Custom avatar logic
    $user_id = $comment->user_id;
    $gravatar_url = get_avatar_url($user_id, array('size' => $depth > 1 ? 30 : 40));
    $is_default_gravatar = strpos($gravatar_url, 'd=') !== false || strpos($gravatar_url, 'gravatar.com/avatar/0000') !== false;
    $profile_photo_id = get_user_meta($user_id, 'profile_photo', true);
    $profile_photo_url = $profile_photo_id ? wp_get_attachment_url($profile_photo_id) : '';
    $default_image = get_template_directory_uri() . '/assets/images/profile.png';
    $image_url = (!$is_default_gravatar && $gravatar_url) ? $gravatar_url : ($profile_photo_url ?: $default_image);
    $avatar_size = $depth > 1 ? 30 : 40; // 30px for replies, 40px for comments
?>
    <div class="comment-item mb-4<?php echo $depth > 1 ? ' ms-' . ($depth * 2) : ''; ?>" id="comment-<?php comment_ID(); ?>">
      <div class="d-flex">
        <div class="flex-shrink-0">
          <img src="<?php echo esc_url($image_url); ?>" class="rounded-circle" width="<?php echo $avatar_size; ?>" height="<?php echo $avatar_size; ?>" alt="User">
        </div>
        <div class="flex-grow-1 ms-3">
          <div class="d-flex align-items-center mb-1">
            <h5 class="mb-0 me-2"><?php comment_author(); ?></h5>
            <small class="text-muted"><?php printf(__('%s ago'), human_time_diff(get_comment_time('U'), current_time('timestamp'))); ?></small>
          </div>
          <p class="mb-2"><?php comment_text(); ?></p>
          <?php if ($is_pending && ($is_author || $is_admin)) : ?>
            <p class="text-muted mb-2"><?php _e('Your comment is awaiting moderation.'); ?></p>
          <?php endif; ?>
          <?php
          comment_reply_link(array_merge($args, array(
            'depth'      => $depth,
            'max_depth'  => $args['max_depth'],
            'reply_text' => __('Reply'),
            'before'     => '<button class="btn btn-sm btn-outline-secondary reply-comment-btn">',
            'after'      => '</button>',
          )));
          ?>

          <!-- Reply form -->
          <div class="reply-comment-form mt-2" style="display: none;">
            <form action="<?php echo site_url('/wp-comments-post.php'); ?>" method="post" class="comment-form" id="reply-form-<?php comment_ID(); ?>">
              <?php if (!is_user_logged_in()) : ?>
                <div class="mb-3">
                  <label for="reply-author-<?php comment_ID(); ?>" class="form-label"><?php _e('Name*'); ?></label>
                  <input type="text" class="form-control" id="reply-author-<?php comment_ID(); ?>" name="author" required>
                </div>
                <div class="mb-3">
                  <label for="reply-email-<?php comment_ID(); ?>" class="form-label"><?php _e('Email'); ?></label>
                  <input type="email" class="form-control" id="reply-email-<?php comment_ID(); ?>" name="email">
                </div>
              <?php endif; ?>
              <div class="mb-3">
                <label for="reply-comment-<?php comment_ID(); ?>" class="form-label"><?php _e('Reply*'); ?></label>
                <textarea class="form-control" id="reply-comment-<?php comment_ID(); ?>" name="comment" rows="2" placeholder="Write your reply..." required></textarea>
              </div>
              <button type="submit" class="btn btn-sm btn-custom-red submit-comment-reply"><?php _e('Submit'); ?></button>
              <button type="button" class="btn btn-sm btn-outline-secondary cancel-comment-reply ms-2"><?php _e('Cancel'); ?></button>
              <?php comment_id_fields(); ?>
              <input type="hidden" name="comment_parent" value="<?php comment_ID(); ?>">
              <input type="hidden" name="comment_post_ID" value="<?php echo get_the_ID(); ?>">
            </form>
          </div>

          <!-- Replies -->
          <div class="comment-replies mt-3 ps-3 border-start">
            <?php
            // Fetch and display replies
            $replies = get_comments(array(
              'parent'  => $comment->comment_ID,
              'status'  => 'approve',
            ));

            // Include pending replies only for the commenter or admin
            if (is_user_logged_in() || current_user_can('manage_options')) {
              $pending_replies = get_comments(array(
                'parent'       => $comment->comment_ID,
                'status'       => 'hold',
                'author_email' => $current_user->user_email,
              ));
              $replies = array_merge($replies, $pending_replies);
            }

            foreach ($replies as $reply) :
              $reply_pending = $reply->comment_approved === '0';
              $is_reply_author = $current_user_id && $current_user_id == $reply->user_id;
              $is_reply_admin = current_user_can('manage_options');
              if (!$reply_pending || ($reply_pending && ($is_reply_author || $is_reply_admin))) :
                $reply_avatar_url = get_avatar_url($reply->user_id, array('size' => 30));
                $reply_is_default_gravatar = strpos($reply_avatar_url, 'd=') !== false || strpos($reply_avatar_url, 'gravatar.com/avatar/0000') !== false;
                $reply_profile_photo_id = get_user_meta($reply->user_id, 'profile_photo', true);
                $reply_profile_photo_url = $reply_profile_photo_id ? wp_get_attachment_url($reply_profile_photo_id) : '';
                $reply_image_url = (!$reply_is_default_gravatar && $reply_avatar_url) ? $reply_avatar_url : ($reply_profile_photo_url ?: $default_image);
            ?>
                <div class="comment-reply-item mb-3">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <img src="<?php echo esc_url($reply_image_url); ?>" class="rounded-circle" width="30" height="30" alt="User">
                    </div>
                    <div class="flex-grow-1 ms-2">
                      <div class="d-flex align-items-center mb-1">
                        <h6 class="mb-0 me-2"><?php echo esc_html($reply->comment_author); ?></h6>
                        <small class="text-muted"><?php printf(__('%s ago'), human_time_diff(strtotime($reply->comment_date), current_time('timestamp'))); ?></small>
                      </div>
                      <p class="mb-0 small"><?php echo esc_html($reply->comment_content); ?></p>
                      <?php if ($reply_pending && ($is_reply_author || $is_reply_admin)) : ?>
                        <p class="text-muted mb-0 small"><?php _e('Your comment is awaiting moderation.'); ?></p>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
            <?php
              endif;
            endforeach;
            ?>
          </div>
        </div>
      </div>
    </div>
<?php
  endif;
}
?>
