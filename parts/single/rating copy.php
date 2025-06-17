<?php
// Create the rating and review table if it doesn't exist
global $wpdb;
$table_name = $wpdb->prefix . 'rating_review';
$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    rating INT NOT NULL,
    review TEXT NOT NULL,
    company_id BIGINT(20) UNSIGNED NOT NULL,
    parent_id BIGINT(20) UNSIGNED DEFAULT 0,
    date_time DATETIME NOT NULL,
    PRIMARY KEY (id)
) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

// Handle review submission
if (isset($_POST['submit_review']) && is_user_logged_in()) {
   $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
   $review = isset($_POST['review_text']) ? sanitize_textarea_field($_POST['review_text']) : '';
   $company_id = get_the_ID();
   $user_id = get_current_user_id();
   $parent_id = 0; // For main review
   $error = '';

   if ($rating < 1 || $rating > 5) {
      $error = 'Please select a rating between 1 and 5 stars.';
   } elseif (empty($review)) {
      $error = 'Please enter your review.';
   } else {
      $wpdb->insert(
         $table_name,
         [
            'user_id' => $user_id,
            'rating' => $rating,
            'review' => $review,
            'company_id' => $company_id,
            'parent_id' => $parent_id,
            'date_time' => current_time('mysql')
         ],
         ['%d', '%d', '%s', '%d', '%d', '%s']
      );
      if ($wpdb->last_error) {
         $error = 'Error submitting review. Please try again.';
      } else {
         $success = 'Review submitted successfully!';
      }
   }
}

// Handle reply submission
if (isset($_POST['submit_reply']) && is_user_logged_in() && get_current_user_id() == get_the_author_meta('ID')) {
   $review_id = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;
   $reply_text = isset($_POST['reply_text']) ? sanitize_textarea_field($_POST['reply_text']) : '';
   $company_id = get_the_ID();
   $user_id = get_current_user_id();
   $error = '';

   $existing_reply = $wpdb->get_var($wpdb->prepare(
      "SELECT COUNT(*) FROM $table_name WHERE parent_id = %d",
      $review_id
   ));

   if ($existing_reply > 0) {
      $error = 'A reply already exists for this review.';
   } elseif (empty($reply_text)) {
      $error = 'Please enter your reply.';
   } else {
      $wpdb->insert(
         $table_name,
         [
            'user_id' => $user_id,
            'rating' => 0,
            'review' => $reply_text,
            'company_id' => $company_id,
            'parent_id' => $review_id,
            'date_time' => current_time('mysql')
         ],
         ['%d', '%d', '%s', '%d', '%d', '%s']
      );
      if ($wpdb->last_error) {
         $error = 'Error submitting reply. Please try again.';
      } else {
         $success = 'Reply submitted successfully!';
      }
   }
}

// Handle review/reply edit
if (isset($_POST['edit_review']) && is_user_logged_in()) {
   $review_id = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;
   $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
   $review_text = isset($_POST['review_text']) ? sanitize_textarea_field($_POST['review_text']) : '';
   $is_reply = isset($_POST['is_reply']) && $_POST['is_reply'] == '1';
   $error = '';

   $review_data = $wpdb->get_row($wpdb->prepare("SELECT user_id, parent_id FROM $table_name WHERE id = %d", $review_id));
   if ($review_data && ($review_data->user_id == get_current_user_id() || (get_current_user_id() == get_the_author_meta('ID') && $review_data->parent_id > 0))) {
      if ($is_reply) {
         if (empty($review_text)) {
            $error = 'Please enter your reply.';
         } else {
            $wpdb->update(
               $table_name,
               ['review' => $review_text],
               ['id' => $review_id],
               ['%s'],
               ['%d']
            );
            if ($wpdb->last_error) {
               $error = 'Error updating reply. Please try again.';
            } else {
               $success = 'Reply updated successfully!';
            }
         }
      } else {
         if ($rating < 1 || $rating > 5) {
            $error = 'Please select a rating between 1 and 5 stars.';
         } elseif (empty($review_text)) {
            $error = 'Please enter your review.';
         } else {
            $wpdb->update(
               $table_name,
               ['rating' => $rating, 'review' => $review_text],
               ['id' => $review_id],
               ['%d', '%s'],
               ['%d']
            );
            if ($wpdb->last_error) {
               $error = 'Error updating review. Please try again.';
            } else {
               $success = 'Review updated successfully!';
            }
         }
      }
   } else {
      $error = 'You are not authorized to edit this review.';
   }
}

// Fetch reviews and calculate rating breakdown
$company_id = get_the_ID();
$reviews = $wpdb->get_results($wpdb->prepare(
   "SELECT * FROM $table_name WHERE company_id = %d AND parent_id = 0 ORDER BY date_time DESC",
   $company_id
));
$replies = $wpdb->get_results($wpdb->prepare(
   "SELECT * FROM $table_name WHERE company_id = %d AND parent_id > 0",
   $company_id
));

// Calculate average rating and breakdown
$total_reviews = count($reviews);
$rating_sum = 0;
$rating_counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
foreach ($reviews as $review) {
   $rating_sum += $review->rating;
   if ($review->rating >= 1 && $review->rating <= 5) {
      $rating_counts[$review->rating]++;
   }
}
$average_rating = $total_reviews > 0 ? round($rating_sum / $total_reviews, 1) : 0;
$rating_percentages = [];
foreach ($rating_counts as $star => $count) {
   $rating_percentages[$star] = $total_reviews > 0 ? ($count / $total_reviews) * 100 : 0;
}

// Check if current user has already reviewed
$user_id = is_user_logged_in() ? get_current_user_id() : 0;
$has_user_reviewed = $wpdb->get_var($wpdb->prepare(
   "SELECT COUNT(*) FROM $table_name WHERE company_id = %d AND user_id = %d AND parent_id = 0",
   $company_id,
   $user_id
)) > 0;
?>

<!-- Rating Section -->
<div class="d-flex mb-4 flex-wrap flex-md-nowrap gap-3 justify-content-between">
   <div class="col-12 col-md-6 mb-4 mb-md-0">
      <h3 class="fs-5 mb-3">Rate Us</h3>
      <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
         <span class="text-dark fs-4 fw-bold"><?php echo esc_html($average_rating); ?></span>
         <div class="star-rating-display">
            <?php
            $full_stars = floor($average_rating);
            $has_half_star = ($average_rating - $full_stars) >= 0.5;

            for ($i = 1; $i <= 5; $i++) {
               if ($i <= $full_stars) {
                  echo '<i class="bi bi-star-fill text-warning"></i>';
               } elseif ($i == $full_stars + 1 && $has_half_star) {
                  echo '<i class="bi bi-star-half text-warning"></i>';
               } else {
                  echo '<i class="bi bi-star-fill text-muted"></i>';
               }
            }
            ?>
         </div>
         <span class="text-muted">(<?php echo esc_html($total_reviews); ?> reviews)</span>
      </div>

      <!-- Review Text Section -->
      <div class="review-section mt-3">
         <button class="btn btn-custom-red btn-sm" id="toggleReviewBtn" <?php echo $has_user_reviewed ? 'disabled' : ''; ?>>
            <i class="bi bi-pencil-square"></i> Add Review
         </button>
         <?php if ($has_user_reviewed): ?>
            <p class="fs-7 my-1 text-muted">
               You have already rated. You can edit your previous review.
            </p>
         <?php endif; ?>
         <form method="post" id="reviewForm" style="display: none;">
            <div class="star-rating mb-2">
               <?php for ($i = 5; $i >= 1; $i--) : ?>
                  <input type="radio" id="new-star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo $has_user_reviewed ? 'disabled' : ''; ?>>
                  <label for="new-star<?php echo $i; ?>"><i class="bi bi-star-fill"></i></label>
               <?php endfor; ?>
            </div>
            <textarea class="form-control" rows="3" placeholder="Share your experience..." name="review_text" id="reviewText"></textarea>
            <div id="reviewTextError" class="text-danger small mt-1" style="display: none;">
               Please enter your review
            </div>
            <div id="ratingError" class="text-danger small mb-2" style="display: none;">
               Please select a rating
            </div>
            <button type="submit" class="btn btn-custom-red btn-sm mt-2" name="submit_review" id="submitReviewBtn">Submit Review</button>
         </form>
      </div>

      <!-- Reviews Display -->
      <div class="reviews-container mt-3 p-3 rounded-2" id="reviewsContainer">
         <?php foreach ($reviews as $review) : ?>
            <?php
            $user_info = get_userdata($review->user_id);
            $user_name = $user_info ? esc_html($user_info->display_name) : 'Anonymous';
            $is_editable = is_user_logged_in() && (get_current_user_id() == $review->user_id);
            $has_reply = $wpdb->get_var($wpdb->prepare(
               "SELECT COUNT(*) FROM $table_name WHERE parent_id = %d",
               $review->id
            )) > 0;
            ?>
            <div class="review-item mb-4" data-user-id="<?php echo esc_attr($review->user_id); ?>" data-review-id="<?php echo esc_attr($review->id); ?>">
               <div class="d-flex">
                  <div class="flex-shrink-0">
                     <img src="<?php echo esc_url(get_avatar_url($review->user_id, ['size' => 40])); ?>" class="rounded-circle" width="40" height="40" alt="User">
                  </div>
                  <div class="flex-grow-1 ms-3">
                     <div class="d-flex align-items-center mb-1">
                        <h5 class="mb-0 me-2 fs-6"><?php echo $user_name; ?></h5>
                        <small class="text-muted"><?php echo human_time_diff(strtotime($review->date_time), current_time('timestamp')); ?> ago</small>
                     </div>
                     <div class="star-rating-display mb-2">
                        <?php echo str_repeat('<i class="bi bi-star-fill text-warning"></i>', $review->rating); ?>
                        <?php echo str_repeat('<i class="bi bi-star-fill text-muted"></i>', 5 - $review->rating); ?>
                     </div>
                     <p class="mb-2"><?php echo esc_html($review->review); ?></p>

                     <?php if ($is_editable) : ?>
                        <div class="review-actions mt-2">
                           <button class="btn btn-sm btn-outline-secondary edit-review-btn">Edit</button>
                           <form method="post" class="edit-review-form mt-2" style="display: none;">
                              <input type="hidden" name="review_id" value="<?php echo esc_attr($review->id); ?>">
                              <input type="hidden" name="is_reply" value="0">
                              <div class="star-rating-edit mb-2">
                                 <?php for ($i = 5; $i >= 1; $i--) : ?>
                                    <input type="radio" id="edit-star<?php echo $review->id . '-' . $i; ?>" name="rating" value="<?php echo $i; ?>" <?php checked($i, $review->rating); ?>>
                                    <label for="edit-star<?php echo $review->id . '-' . $i; ?>"><i class="bi bi-star-fill"></i></label>
                                 <?php endfor; ?>
                              </div>
                              <textarea class="form-control mb-2" rows="3" name="review_text"><?php echo esc_textarea($review->review); ?></textarea>
                              <button type="submit" class="btn btn-sm btn-custom-red" name="edit_review">Save Changes</button>
                              <button type="button" class="btn btn-sm btn-outline-secondary cancel-edit-btn ms-2">Cancel</button>
                           </form>
                        </div>
                     <?php endif; ?>

                     <!-- Replies -->
                     <div class="replies mt-3 ps-3 border-start">
                        <?php
                        foreach ($replies as $reply) :
                           if ($reply->parent_id == $review->id) :
                              $reply_user_info = get_userdata($reply->user_id);
                              $reply_user_name = $reply_user_info ? esc_html($reply_user_info->display_name) : 'Anonymous';
                              $is_reply_editable = is_user_logged_in() && (get_current_user_id() == $reply->user_id);
                              $post_title = esc_html(get_the_title($company_id));
                        ?>
                              <div class="reply-item mb-3" data-user-id="<?php echo esc_attr($reply->user_id); ?>" data-reply-id="<?php echo esc_attr($reply->id); ?>">
                                 <div class="d-flex">
                                    <div class="flex-shrink-0">
                                       <img src="<?php echo esc_url(get_avatar_url($reply->user_id, ['size' => 30])); ?>" class="rounded-circle" width="30" height="30" alt="User">
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                       <div class="d-flex align-items-center mb-1">
                                          <h6 class="mb-0 me-2"><?php echo $post_title; ?></h6>
                                          <small class="text-muted"><?php echo human_time_diff(strtotime($reply->date_time), current_time('timestamp')); ?> ago</small>
                                       </div>
                                       <p class="mb-0 small"><?php echo esc_html($reply->review); ?></p>

                                       <?php if ($is_reply_editable) : ?>
                                          <button class="btn btn-sm btn-outline-secondary edit-reply-btn mt-1">Edit</button>
                                          <form method="post" class="reply-actions mt-2" style="display: none;">
                                             <input type="hidden" name="review_id" value="<?php echo esc_attr($reply->id); ?>">
                                             <input type="hidden" name="is_reply" value="1">
                                             <textarea class="form-control mb-2 mt-2" rows="2" name="review_text"><?php echo esc_textarea($reply->review); ?></textarea>
                                             <button type="submit" class="btn btn-sm btn-custom-red" name="edit_review">Save</button>
                                             <button type="button" class="btn btn-sm btn-outline-secondary cancel-reply-btn ms-2">Cancel</button>
                                          </form>
                                       <?php endif; ?>
                                    </div>
                                 </div>
                              </div>
                        <?php endif;
                        endforeach; ?>

                        <?php if (is_user_logged_in() && get_current_user_id() == get_the_author_meta('ID') && !$has_reply) : ?>
                           <form method="post" class="reply-form mt-2" style="display: none;">
                              <input type="hidden" name="review_id" value="<?php echo esc_attr($review->id); ?>">
                              <textarea class="form-control mb-2" rows="2" placeholder="Write your reply..." name="reply_text"></textarea>
                              <div class="text-danger small mt-1 reply-error" style="display: none;">Please enter your reply.</div>
                              <button type="submit" class="btn btn-sm btn-custom-red" name="submit_reply">Submit</button>
                              <button type="button" class="btn btn-sm btn-outline-secondary cancel-reply-btn ms-2">Cancel</button>
                           </form>
                           <button class="btn btn-sm btn-outline-secondary reply-btn" data-review-id="<?php echo esc_attr($review->id); ?>">Reply</button>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            </div>
         <?php endforeach; ?>
      </div>
   </div>

   <div class="col-12 col-md-6">
      <h4 class="fs-5 mb-3">Rating Breakdown</h4>
      <div class="rating-progress">
         <?php for ($i = 5; $i >= 1; $i--) : ?>
            <div class="d-flex align-items-center mb-2">
               <span class="text-dark me-2" style="width: 20px;"><?php echo $i; ?></span>
               <i class="bi bi-star-fill text-warning me-2"></i>
               <div class="progress flex-grow-1" style="height: 10px;">
                  <div class="progress-bar bg-warning"
                     role="progressbar"
                     style="width: <?php echo number_format($rating_percentages[$i], 2); ?>%"
                     aria-valuenow="<?php echo number_format($rating_percentages[$i], 2); ?>"
                     aria-valuemin="0" aria-valuemax="100"></div>
               </div>
               <span class="text-muted ms-2"><?php echo number_format($rating_percentages[$i], 2); ?>%</span>
            </div>
         <?php endfor; ?>
      </div>
   </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header">
            <p class="modal-title text-capitalize fs-6" id="loginModalLabel">Choose your preferred login method</p>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <div class="d-grid gap-3">
               <a class="btn btn-danger" href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">
                  <i class="fas fa-sign-in-alt me-2"></i>Login
               </a>
               <a class="btn btn-danger" href="<?php echo esc_url(wp_registration_url()); ?>">
                  <i class="fas fa-user-plus me-2"></i>Register
               </a>
            </div>
         </div>
      </div>
   </div>
</div>

<?php if (!empty($error)) : ?>
   <div class="alert alert-danger alert-dismissible fade show alert-fixed-top" role="alert">
      <?php echo esc_html($error); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
   </div>
<?php endif; ?>
<?php if (!empty($success)) : ?>
   <div class="alert alert-success alert-dismissible fade show alert-fixed-top" role="alert">
      <?php echo esc_html($success); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
   </div>
<?php endif; ?>

<script>
   document.addEventListener('DOMContentLoaded', function() {
      const toggleReviewBtn = document.getElementById('toggleReviewBtn');
      const reviewForm = document.getElementById('reviewForm');
      const submitReviewBtn = document.getElementById('submitReviewBtn');
      const reviewText = document.getElementById('reviewText');
      const ratingError = document.getElementById('ratingError');
      const reviewTextError = document.getElementById('reviewTextError');
      let selectedRating = 0;

      // Handle star rating clicks
      document.querySelectorAll('#reviewForm input[name="rating"]').forEach(radio => {
         radio.addEventListener('change', function() {
            selectedRating = parseInt(this.value);
            ratingError.style.display = 'none';
         });
      });

      // Toggle review form
      if (toggleReviewBtn) {
         toggleReviewBtn.addEventListener('click', function() {
            if (reviewForm.style.display === 'none') {
               <?php if (!is_user_logged_in()) : ?>
                  const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                  loginModal.show();
                  return;
               <?php endif; ?>
               reviewForm.style.display = 'block';
               toggleReviewBtn.innerHTML = '<i class="bi bi-x"></i> Cancel';
               reviewText.value = '';
               selectedRating = 0;
               document.querySelectorAll('#reviewForm input[name="rating"]').forEach(radio => radio.checked = false);
            } else {
               reviewForm.style.display = 'none';
               toggleReviewBtn.innerHTML = '<i class="bi bi-pencil-square"></i> Add Review';
               ratingError.style.display = 'none';
               reviewTextError.style.display = 'none';
            }
         });
      }

      // Handle review form submission
      if (submitReviewBtn) {
         submitReviewBtn.addEventListener('click', function(e) {
            const rating = document.querySelector('#reviewForm input[name="rating"]:checked');
            if (!rating) {
               ratingError.style.display = 'block';
               e.preventDefault();
               return;
            } else {
               ratingError.style.display = 'none';
            }
            if (reviewText.value.trim() === '') {
               reviewTextError.style.display = 'block';
               e.preventDefault();
               return;
            } else {
               reviewTextError.style.display = 'none';
            }
         });
      }

      // Handle reply buttons
      document.querySelectorAll('.reply-btn').forEach(button => {
         button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            const reviewItem = this.closest('.review-item');
            const replyForm = reviewItem.querySelector('.reply-form');
            replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
            this.style.display = 'none';
         });
      });

      // Handle cancel reply
      document.querySelectorAll('.cancel-reply-btn').forEach(button => {
         button.addEventListener('click', function() {
            const replyForm = this.closest('.reply-form');
            replyForm.style.display = 'none';
            replyForm.closest('.replies').querySelector('.reply-btn').style.display = 'block';
         });
      });

      // Handle edit review buttons
      document.querySelectorAll('.edit-review-btn').forEach(button => {
         button.addEventListener('click', function() {
            const reviewItem = this.closest('.review-item');
            const editForm = reviewItem.querySelector('.edit-review-form');
            editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
            this.style.display = 'none';
         });
      });

      // Handle cancel edit review
      document.querySelectorAll('.cancel-edit-btn').forEach(button => {
         button.addEventListener('click', function() {
            const editForm = this.closest('.edit-review-form');
            editForm.style.display = 'none';
            editForm.closest('.review-actions').querySelector('.edit-review-btn').style.display = 'block';
         });
      });

      // Highlight stars on hover for edit forms
      document.querySelectorAll('.star-rating-edit label').forEach(label => {
         label.addEventListener('mouseover', function() {
            const starValue = parseInt(this.htmlFor.split('-')[1]);
            const stars = this.closest('.star-rating-edit').querySelectorAll('label i');
            stars.forEach((star, index) => {
               star.style.color = index < starValue ? 'var(--cl--primary)' : 'rgba(193, 39, 45, 0.1)';
            });
         });
      });

      // Handle edit reply buttons
      document.querySelectorAll('.edit-reply-btn').forEach(button => {
         button.addEventListener('click', function() {
            const replyItem = this.closest('.reply-item');
            const editForm = replyItem.querySelector('.reply-actions');
            editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
            this.style.display = 'none';
         });
      });

      // Handle cancel edit reply
      document.querySelectorAll('.cancel-reply-btn').forEach(button => {
         button.addEventListener('click', function() {
            const editForm = this.closest('.reply-actions');
            editForm.style.display = 'none';
            editForm.closest('.reply-item').querySelector('.edit-reply-btn').style.display = 'block';
         });
      });
   });

   // Avoids asking for resubmission of the form when page reload
   if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
   }
</script>