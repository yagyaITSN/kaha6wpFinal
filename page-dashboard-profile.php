<?php
ob_start();
get_header();

// Get current user data
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Fetch user meta
$user_phone = get_user_meta($user_id, 'phone', true);
$user_notes = get_user_meta($user_id, 'notes', true);
$user_twitter = get_user_meta($user_id, 'twitter', true);
$user_facebook = get_user_meta($user_id, 'facebook', true);
$user_linkedin = get_user_meta($user_id, 'linkedin', true);
$user_instagram = get_user_meta($user_id, 'instagram', true);

// Handle profile update
if (isset($_POST['update_profile'])) {
	$user_data = ['ID' => $user_id];
	if (isset($_POST['fname']) && !empty($_POST['fname'])) {
		$user_data['display_name'] = sanitize_text_field($_POST['fname']);
		$user_data['first_name'] = sanitize_text_field($_POST['fname']);
	}
	if (isset($_POST['email']) && !empty($_POST['email'])) {
		if (email_exists($_POST['email']) && $_POST['email'] !== $current_user->user_email) {
			echo '<div class="error" style="text-align:center;color:red;padding:8px 0px 16px 0px;">Email already in use.</div>';
		} else {
			$user_data['user_email'] = sanitize_email($_POST['email']);
		}
	}
	if (isset($_POST['notes'])) {
		$user_data['description'] = sanitize_textarea_field($_POST['notes']);
	}
	if (!empty($user_data['display_name']) || !empty($user_data['user_email']) || !empty($user_data['description'])) {
		wp_update_user($user_data);
	}

	update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
	update_user_meta($user_id, 'twitter', esc_url_raw($_POST['twitter']));
	update_user_meta($user_id, 'facebook', esc_url_raw($_POST['facebook']));
	update_user_meta($user_id, 'linkedin', esc_url_raw($_POST['linkedin']));
	update_user_meta($user_id, 'instagram', esc_url_raw($_POST['instagram']));

	// Handle profile photo upload
	if (!empty($_FILES['profile_photo']['name'])) {
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');

		$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
		$max_size = 200 * 1024; // 200KB in bytes

		// Validate file type
		if (!in_array($_FILES['profile_photo']['type'], $allowed_types)) {
			$errors[] = 'Invalid file type. Only JPEG, PNG, or GIF are allowed.';
		}

		// Validate file size
		if ($_FILES['profile_photo']['size'] > $max_size) {
			$errors[] = 'File size exceeds 200KB limit.';
		}

		// Proceed with upload if no errors
		if (empty($errors)) {
			$uploaded = media_handle_upload('profile_photo', 0, [], [
				'test_form' => false,
				'mimes' => [
					'jpg|jpeg' => 'image/jpeg',
					'png' => 'image/png',
					'gif' => 'image/gif',
				],
			]);

			if (is_wp_error($uploaded)) {
				$errors[] = 'Photo upload failed: ' . $uploaded->get_error_message();
			} else {
				// Delete old profile photo if exists
				$old_photo_id = get_user_meta($user_id, 'profile_photo', true);
				if ($old_photo_id) {
					wp_delete_attachment($old_photo_id, true);
				}
				update_user_meta($user_id, 'profile_photo', $uploaded);
			}
		}
	}

	if (!empty($errors)) {
		echo '<div class="error" style="text-align:center;color:red;padding:8px 0px 16px 0px;">' . implode('<br>', array_map('esc_html', $errors)) . '</div>';
	} else {
		echo '<div class="updated" style="text-align:center;color:green;padding:8px 0px 16px 0px;">Profile Updated Successfully!</div>';
	}
}

// Handle password change
if (isset($_POST['change_password'])) {
	$current_password = $_POST['current_password'];
	$new_password = $_POST['new_password'];
	$confirm_password = $_POST['confirm_password'];

	if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
		if ($new_password === $confirm_password) {
			if (wp_check_password($current_password, $current_user->user_pass, $user_id)) {
				wp_set_password($new_password, $user_id);
				wp_logout();
				wp_safe_redirect(home_url('/'));
				exit;
			} else {
				echo '<div class="error" style="text-align:center;color:red;padding:8px 0px 16px 0px;">Current password is incorrect.</div>';
			}
		} else {
			echo '<div class="error" style="text-align:center;color:red;padding:8px 0px 16px 0px;">New passwords do not match.</div>';
		}
	} else {
		echo '<div class="error" style="text-align:center;color:red;padding:8px 0px 16px 0px;">Please fill all password fields.</div>';
	}
}

// Get user profile image
$profile_photo_id = get_user_meta($user_id, 'profile_photo', true);
$profile_photo_url = $profile_photo_id ? wp_get_attachment_url($profile_photo_id) : get_template_directory_uri() . '/assets/images/profile.png';
?>

<section class="container py-5">
	<div class="col-12">
		<div class="card border-0 itsn-shadow">
			<div class="card-body p-0">
				<div class="row g-0">

					<!-- Business Details Start -->
					<?php get_template_part('parts/dashboard/pg', 'offcanvas'); ?>
					<!-- Business Details End -->

					<!-- Content Area -->
					<div class="col-lg-9 p-4 mb-4">
						<div class="py-4 d-md-flex justify-content-md-between align-items-md-center">
							<h3 class="fw-bold">My Profile</h3>
							<div>
								<nav aria-label="breadcrumb">
									<ol class="breadcrumb">
										<li class="breadcrumb-item"><a href="<?php echo home_url('/dashboard'); ?>">Dashboard</a></li>
										<li class="breadcrumb-item active" aria-current="page">My Profile</li>
									</ol>
								</nav>
							</div>
						</div>
						<div>
							<form method="post" enctype="multipart/form-data" name="profile-form">
								<h5 class="fw-bold">Profile Details</h5>
								<div class="edit-profile-photo mb-3">
									<div class="d-flex justify-content-center">
										<img src="<?php echo esc_url($profile_photo_url); ?>" alt="Profile Photo" class="h-25 w-25 rounded-circle py-3">
									</div>
									<div class="change-photo-btn">
										<div class="photoUpload">
											<span><i class="fa fa-upload"></i> Upload Photo</span>
											<input type="file" class="upload form-control" name="profile_photo" id="profile_photo" accept="image/jpeg,image/png,image/gif" />
										</div>
									</div>
								</div>
								<div class="row">
									<div class="mb-3 col-sm-12 col-md-4">
										<label for="fname" class="form-label">Your Name</label>
										<input type="text" value="<?php echo esc_attr($current_user->display_name); ?>" name="fname" class="form-control" id="fname">
										<span class="error-message" id="name_error"></span>
									</div>
									<div class="mb-3 col-sm-12 col-md-4">
										<label for="phone" class="form-label">Phone</label>
										<input type="text" value="<?php echo esc_attr($user_phone); ?>" name="phone" class="form-control" id="phone">
										<span class="error-message" id="phone_error"></span>
									</div>
									<div class="mb-3 col-sm-12 col-md-4">
										<label for="email" class="form-label">Email</label>
										<input type="text" value="<?php echo esc_attr($current_user->user_email); ?>" name="email" class="form-control" id="email">
										<span class="error-message" id="email_error"></span>
									</div>
									<div class="mb-3 col-sm-12">
										<label for="notes" class="form-label">About Yourself</label>
										<textarea class="form-control" id="notes" rows="5"><?php echo esc_textarea($current_user->user_description); ?></textarea>
										<span class="error-message" id="notes_error"></span>
									</div>
									<div class="mb-3 col-sm-12 col-md-4">
										<label for="facebook" class="form-label"><i class="text-primary fa-brands fa-facebook-f"></i> Facebook</label>
										<input id="facebook" placeholder="Facebook Profile URL" type="url" name="facebook" value="<?php echo esc_url($user_facebook); ?>" class="form-control">
										<span class="error-message" id="facebook_error"></span>
									</div>
									<div class="mb-3 col-sm-12 col-md-4">
										<label for="twitter" class="form-label"><i class="text-primary fa-brands fa-x-twitter"></i> X</label>
										<input id="twitter" placeholder="X Profile URL" type="url" name="twitter" value="<?php echo esc_url($user_twitter); ?>" class="form-control">
										<span class="error-message" id="twitter_error"></span>
									</div>
									<div class="mb-3 col-sm-12 col-md-4">
										<label for="linkedin" class="form-label"><i class="text-primary fa-brands fa-linkedin-in"></i> LinkedIn</label>
										<input id="linkedin" placeholder="LinkedIn Profile URL" type="url" name="linkedin" value="<?php echo esc_url($user_linkedin); ?>" class="form-control">
										<span class="error-message" id="linkedin_error"></span>
									</div>
									<div class="mb-3 col-sm-12 col-md-4">
										<label for="instagram" class="form-label"><i class="text-primary fa-brands fa-instagram"></i> Instagram</label>
										<input id="instagram" placeholder="Instagram Profile URL" type="url" name="instagram" value="<?php echo esc_url($user_instagram); ?>" class="form-control">
										<span class="error-message" id="instagram_error"></span>
									</div>
								</div>
								<button type="submit" class="btn btn-primary bg-primary border-0" name="update_profile">Save Changes</button>
								<hr>
								<h5 class="fw-bold">Change Password</h5>
								<hr>
								<div class="row row-cols-1 row-cols-md-3 justify-content-center">
									<div class="mb-3 col">
										<label for="current_password" class="form-label">Current Password</label>
										<input type="password" name="current_password" class="form-control" id="current_password">
									</div>
									<div class="mb-3 col">
										<label for="new_password" class="form-label">New Password</label>
										<input type="password" name="new_password" class="form-control" id="new_password">
									</div>
									<div class="mb-3 col">
										<label for="confirm_password" class="form-label">Confirm Password</label>
										<input type="password" name="confirm_password" class="form-control" id="confirm_password">
									</div>

								</div>
								<button type="submit" class="btn btn-primary bg-primary border-0" name="change_password">Change Password</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<?php get_footer(); ?>


<script>
	document.addEventListener('DOMContentLoaded', function() {
		const form = document.querySelector('form[name="profile-form"]');
		const photoInput = document.getElementById('profile_photo');
		const profileImage = document.querySelector('.edit-profile-photo img');
		const maxSize = 200 * 1024; // 200KB in bytes
		const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

		photoInput.addEventListener('change', function(e) {
			const file = e.target.files[0];
			const errorSpan = document.createElement('span');
			errorSpan.className = 'error-message';
			errorSpan.style.color = 'red';
			errorSpan.style.display = 'block';
			errorSpan.style.marginTop = '5px';

			// Remove previous error messages
			const existingError = photoInput.parentElement.querySelector('.error-message');
			if (existingError) {
				existingError.remove();
			}

			if (file) {
				// Validate file type
				if (!allowedTypes.includes(file.type)) {
					errorSpan.textContent = 'Invalid file type. Only JPEG, PNG, or GIF are allowed.';
					photoInput.parentElement.appendChild(errorSpan);
					photoInput.value = ''; // Clear the input
					profileImage.src = '<?php echo esc_url($profile_photo_url); ?>'; // Reset to original image
					return;
				}

				// Validate file size
				if (file.size > maxSize) {
					errorSpan.textContent = 'File size exceeds 200KB limit.';
					photoInput.parentElement.appendChild(errorSpan);
					photoInput.value = ''; // Clear the input
					profileImage.src = '<?php echo esc_url($profile_photo_url); ?>'; // Reset to original image
					return;
				}

				// Display preview
				const reader = new FileReader();
				reader.onload = function(event) {
					profileImage.src = event.target.result;
				};
				reader.readAsDataURL(file);
			}
		});

		form.addEventListener('submit', function(e) {
			let hasErrors = false;
			const errorSpans = form.querySelectorAll('.error-message');
			errorSpans.forEach(span => {
				if (span.textContent) {
					hasErrors = true;
				}
			});

			if (hasErrors) {
				e.preventDefault();
				alert('Please fix the errors before submitting.');
			}
		});
	});
</script>