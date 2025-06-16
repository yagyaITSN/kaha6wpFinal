<?php

/**
 * Register meta boxes.
 */
function itsn_meta_boxes()
{
	add_meta_box('hcf-1', __('Company Meta Data', 'ITSN'), 'itsn_display_callback', 'ait-item');
}
add_action('add_meta_boxes', 'itsn_meta_boxes');

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function itsn_display_callback($post)
{
	$metadata = get_post_meta($post->ID, '_ait-item_item-data', true);
	$metadata_author = get_post_meta($post->ID, '_ait-item_item-author', true);
	$ait_items = wp_get_post_terms($post->ID, 'ait-items', ['fields' => 'ids']);
	$ait_locations = wp_get_post_terms($post->ID, 'ait-locations', ['fields' => 'ids']);
?>
	<div class="hcf_box">
		<style scoped>
			/* .hcf_box {
				display: flex;
				flex-direction: column;
				gap: 4px;
			}

			.hcf_field {
				display: flex;
				flex-direction: row;
				align-items: center;
				gap: 10px;
				width: 100%;
				max-width: 600px;
				margin: 8px 0 0 0;
			}

			

			.hcf_field label {
				min-width: 150px;
				font-weight: bold;
			}

			.hcf_field input,
			.hcf_field select {
				flex: 1;
				min-width: 0;
			}

			.hcf_field-group {
				display: flex;
				flex-direction: row;
				gap: 10px;
				align-items: center;
				flex: 1;
			}

			.hcf_field-group input,
			.hcf_field-group select {
				width: 100%;
			}

			.hcf_section {
				display: flex;
				flex-direction: column;
				gap: 10px;
			}

			.hcf_section h4 {
				margin: 10px 0 5px !important;
				font-size: 1.2em;
			}

			.hcf_section hr {
				margin: 5px 0;
				border: 0;
				border-top: 1px solid #ddd;
			}

			.hcf_label {
				font-size: 0.9em;
				margin-bottom: 2px;
			}
			

			.hcf_gallery-grid {
				display: grid;
				grid-template-columns: 1fr 1fr;
				gap: 15px;
				max-width: 100%;
			}

			.hcf_gallery-grid .hcf_field {
				max-width: none;
			} */
			.hcf_field {
				margin: 20px 0;
			}
		</style>

		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[subtitle]">Sub Title</label>
			<input id="_ait-item_item-data[subtitle]" type="text" name="_ait-item_item-data[subtitle]" value="<?php echo esc_attr($metadata['subtitle'] ?? ''); ?>">
		</p>
		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[telephone]">Phone</label>
			<input id="_ait-item_item-data[telephone]" type="text" name="_ait-item_item-data[telephone]" value="<?php echo esc_attr($metadata['telephone'] ?? ''); ?>">
		</p>
		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[telephoneAdditional][0][number]">Additional Phone 1</label>
			<input id="_ait-item_item-data[telephoneAdditional][0][number]" type="text" name="_ait-item_item-data[telephoneAdditional][0][number]" value="<?php echo esc_attr($metadata['telephoneAdditional'][0]['number'] ?? ''); ?>">
		</p>
		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[telephoneAdditional][1][number]">Additional Phone 2</label>
			<input id="_ait-item_item-data[telephoneAdditional][1][number]" type="text" name="_ait-item_item-data[telephoneAdditional][1][number]" value="<?php echo esc_attr($metadata['telephoneAdditional'][1]['number'] ?? ''); ?>">
		</p>
		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[telephoneAdditional][2][number]">Additional Phone 3</label>
			<input id="_ait-item_item-data[telephoneAdditional][2][number]" type="text" name="_ait-item_item-data[telephoneAdditional][2][number]" value="<?php echo esc_attr($metadata['telephoneAdditional'][2]['number'] ?? ''); ?>">
		</p>

		<?php for ($i = 0; $i < 5; $i++): ?>
			<div class="meta-options hcf_field">
				<label for="_ait-item_item-data[companyServices][<?php echo $i; ?>][services]">Company Services <?php echo $i + 1; ?></label>
				<input id="_ait-item_item-data[companyServices][<?php echo $i; ?>][services]" type="text" name="_ait-item_item-data[companyServices][<?php echo $i; ?>][services]" value="<?php echo esc_attr($metadata['companyServices'][$i]['services'] ?? ''); ?>" placeholder="Company service">

				<input id="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesDesc]" type="text" name="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesDesc]" value="<?php echo esc_attr($metadata['companyServices'][$i]['servicesDesc'] ?? ''); ?>" placeholder="Company Desc">

				<input id="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesPrice]" type="text" name="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesPrice]" value="<?php echo esc_attr($metadata['companyServices'][$i]['servicesPrice'] ?? ''); ?>" placeholder="Service Price Range">

				<input id="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesImg]" type="file" name="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesImg]" accept="image/jpeg,image/png">

				<input type="hidden" name="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesImgId]" value="<?php echo esc_attr($metadata['companyServices'][$i]['servicesImgId'] ?? ''); ?>">
				<?php
				$image_id = $metadata['companyServices'][$i]['servicesImgId'] ?? '';
				$image_url = $image_id ? wp_get_attachment_url($image_id) : '';
				if ($image_url) : ?>
					<div class="image-preview" style="margin-top: 10px;">
						<img src="<?php echo esc_url($image_url); ?>" style="max-width: 100px; max-height: 100px;" alt="Preview">
						<button type="button" class="remove-image" data-index="<?php echo $i; ?>">Remove Image</button>
					</div>
				<?php endif; ?>
			</div>
		<?php endfor; ?>

		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[email]">Email</label>
			<input id="_ait-item_item-data[email]" type="email" name="_ait-item_item-data[email]" value="<?php echo esc_attr($metadata['email'] ?? ''); ?>">
		</p>
		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[web]">Web</label>
			<input id="_ait-item_item-data[web]" type="url" name="_ait-item_item-data[web]" value="<?php echo esc_attr($metadata['web'] ?? ''); ?>">
		</p>
		<h4>Map</h4>
		<hr>
		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[map][address]">Address</label>
			<input id="_ait-item_item-data[map][address]" type="text" name="_ait-item_item-data[map][address]" value="<?php echo esc_attr($metadata['map']['address'] ?? ''); ?>">
		</p>
		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[postalCode]">Postal Code</label>
			<input id="_ait-item_item-data[postalCode]" type="number" name="_ait-item_item-data[postalCode]" value="<?php echo esc_attr($metadata['postalCode'] ?? ''); ?>">
		</p>
		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[map][latitude]">Latitude</label>
			<input id="_ait-item_item-data[map][latitude]" type="text" name="_ait-item_item-data[map][latitude]" value="<?php echo esc_attr($metadata['map']['latitude'] ?? ''); ?>">
		</p>
		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[map][longitude]">Longitude</label>
			<input id="_ait-item_item-data[map][longitude]" type="text" name="_ait-item_item-data[map][longitude]" value="<?php echo esc_attr($metadata['map']['longitude'] ?? ''); ?>">
		</p>
		<h4>Social Icons</h4>
		<hr>
		<?php for ($i = 0; $i <= 5; $i++) : ?>
			<p class="meta-options hcf_field">
				<label for="_ait-item_item-data[socialIcons][<?php echo $i; ?>][link]">Social Link <?php echo $i + 1; ?></label>
				<input id="_ait-item_item-data[socialIcons][<?php echo $i; ?>][link]" type="url" name="_ait-item_item-data[socialIcons][<?php echo $i; ?>][link]" value="<?php echo esc_attr($metadata['socialIcons'][$i]['link'] ?? ''); ?>">
				<label for="_ait-item_item-data[socialIcons][<?php echo $i; ?>][icon]">Social Icon <?php echo $i + 1; ?></label>
				<select id="_ait-item_item-data[socialIcons][<?php echo $i; ?>][icon]" name="_ait-item_item-data[socialIcons][<?php echo $i; ?>][icon]">
					<option value="">Select</option>
					<option value="fa-brands fa-facebook-f fb-color-code" <?php selected($metadata['socialIcons'][$i]['icon'] ?? '', 'fa-brands fa-facebook-f fb-color-code'); ?>>Facebook</option>
					<option value="fa-brands fa-x-twitter x-color-code" <?php selected($metadata['socialIcons'][$i]['icon'] ?? '', 'fa-brands fa-x-twitter x-color-code'); ?>>X</option>
					<option value="fa-brands fa-instagram insta-color-code" <?php selected($metadata['socialIcons'][$i]['icon'] ?? '', 'fa-brands fa-instagram insta-color-code'); ?>>Instagram</option>
					<option value="fa-brands fa-tiktok tiktok-color-code" <?php selected($metadata['socialIcons'][$i]['icon'] ?? '', 'fa-brands fa-tiktok tiktok-color-code'); ?>>TikTok</option>
					<option value="fa-brands fa-linkedin-in linkedin-color-code" <?php selected($metadata['socialIcons'][$i]['icon'] ?? '', 'fa-brands fa-linkedin-in linkedin-color-code'); ?>>LinkedIn</option>
					<option value="fa-brands fa-youtube yt-color-code" <?php selected($metadata['socialIcons'][$i]['icon'] ?? '', 'fa-brands fa-youtube yt-color-code'); ?>>YouTube</option>
				</select>
			</p>
		<?php endfor; ?>
		<h4>Opening Hours</h4>
		<hr>
		<p class="meta-options hcf_field">
			<label for="_ait-item_item-data[openingHoursNote]">Opening Hours Note</label>
			<input id="_ait-item_item-data[openingHoursNote]" type="text" name="_ait-item_item-data[openingHoursNote]" value="<?php echo esc_attr($metadata['openingHoursNote'] ?? ''); ?>" placeholder="Public holiday, festivals may affect the timing.">
		</p>

		<?php
		$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

		foreach ($days as $day) {
			$hours = $metadata["openingHours$day"] ?? '';
			$from_time = '';
			$to_time = '';

			if ($hours && strpos($hours, '-') !== false) {
				$parts = array_map('trim', explode('-', $hours));
				if (count($parts) === 2 && strtotime($parts[0]) !== false && strtotime($parts[1]) !== false) {
					$from_time = date('H:i', strtotime($parts[0]));
					$to_time = date('H:i', strtotime($parts[1]));
				} else {
					error_log("Invalid opening hours format for $day: $hours");
				}
			}
		?>

			<div class="meta-options hcf_field" style="display: flex; align-items:center; gap:16px">
				<p>Opening Hours <?php echo $day; ?></p>
				<div style="display: flex;">
					<div>
						<label for="_ait-item_item-data[openingHours][<?php echo $day; ?>][from]" class="hcf_label">From</label>
						<input id="_ait-item_item-data[openingHours][<?php echo $day; ?>][from]"
							type="time"
							name="_ait-item_item-data[openingHours][<?php echo $day; ?>][from]"
							value="<?php echo esc_attr($from_time); ?>"
							class="hcf_input form-control"
							aria-label="Opening time for <?php echo $day; ?> from">
					</div>
					<div>
						<label for="_ait-item_item-data[openingHours][<?php echo $day; ?>][to]" class="hcf_label">To</label>
						<input id="_ait-item_item-data[openingHours][<?php echo $day; ?>][to]"
							type="time"
							name="_ait-item_item-data[openingHours][<?php echo $day; ?>][to]"
							value="<?php echo esc_attr($to_time); ?>"
							class="hcf_input form-control"
							aria-label="Opening time for <?php echo $day; ?> to">
					</div>
				</div>
			</div>

		<?php } ?>

		<?php /* 
        <h4>For Admins</h4>
        <hr>
        <p class="meta-options hcf_field">
            <label for="_ait-item_item-data[featuredItem]">Featured Item</label>
            <input id="_ait-item_item-data[featuredItem]" type="text" name="_ait-item_item-data[featuredItem]" value="<?php echo esc_attr($metadata['featuredItem'] ?? ''); ?>" placeholder="0 or 1">
        </p>
        <p class="meta-options hcf_field">
            <label for="_ait-item_item-author[author]">Author</label>
            <input id="_ait-item_item-author[author]" type="text" name="_ait-item_item-author[author]" value="<?php echo esc_attr($metadata_author['author'] ?? ''); ?>">
        </p>
        */ ?>
		<div class="hcf_section">
			<h4>Gallery</h4>
			<hr>
			<div class="hcf_gallery-grid">
				<?php for ($i = 0; $i < 5; $i++) : ?>
					<div class="hcf_field">
						<label>Image <?php echo $i + 1; ?></label>
						<div class="hcf_field-group">
							<div>
								<label for="_ait-item_item-data[gallery][<?php echo $i; ?>][title]" class="hcf_label">Title</label>
								<input id="_ait-item_item-data[gallery][<?php echo $i; ?>][title]" type="text" name="_ait-item_item-data[gallery][<?php echo $i; ?>][title]" value="<?php echo esc_attr($metadata['gallery'][$i]['title'] ?? ''); ?>">
							</div>
							<div>
								<label for="_ait-item_item-data[gallery][<?php echo $i; ?>][image]" class="hcf_label">Image (JPG/PNG, < 500KB)</label>
										<?php
										$image_id = $metadata['gallery'][$i]['image_id'] ?? '';
										$image_url = $image_id ? wp_get_attachment_url($image_id) : '';
										?>
										<input id="_ait-item_item-data[gallery][<?php echo $i; ?>][image]" type="file" name="_ait-item_item-data[gallery][<?php echo $i; ?>][image]" accept="image/jpeg,image/png">
										<input type="hidden" name="_ait-item_item-data[gallery][<?php echo $i; ?>][image_id]" value="<?php echo esc_attr($image_id); ?>">
										<?php if ($image_url) : ?>
											<div class="image-preview" style="margin-top: 10px;">
												<img src="<?php echo esc_url($image_url); ?>" style="max-width: 100px; max-height: 100px;" alt="Preview">
												<button type="button" class="remove-image" data-index="<?php echo $i; ?>">Remove Image</button>
											</div>
										<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const fileInputs = document.querySelectorAll('input[type="file"]');
			fileInputs.forEach(input => {
				input.addEventListener('change', function(e) {
					const file = e.target.files[0];
					if (file) {
						// Check file size (500KB = 512000 bytes)
						if (file.size > 512000) {
							alert('File size must be less than 500KB.');
							e.target.value = '';
							return;
						}
						const previewContainer = e.target.parentElement.querySelector('.image-preview') || document.createElement('div');
						previewContainer.className = 'image-preview';
						previewContainer.style.marginTop = '10px';
						const img = previewContainer.querySelector('img') || document.createElement('img');
						img.src = URL.createObjectURL(file);
						img.style.maxWidth = '100px';
						img.style.maxHeight = '100px';
						img.alt = 'Preview';
						const removeButton = document.createElement('button');
						removeButton.type = 'button';
						removeButton.className = 'remove-image';
						removeButton.textContent = 'Remove Image';
						removeButton.dataset.index = e.target.name.match(/\d+/)[0];
						previewContainer.appendChild(img);
						previewContainer.appendChild(removeButton);
						e.target.parentElement.appendChild(previewContainer);
					}
				});
			});

			document.addEventListener('click', function(e) {
				if (e.target.classList.contains('remove-image')) {
					const index = e.target.dataset.index;
					const container = e.target.closest('.hcf_field, .hcf_field-group');
					const fileInput = container.querySelector(`input[name*="[${index}][servicesImg]"], input[name*="[${index}][image]"]`);
					const hiddenInput = container.querySelector(`input[name*="[${index}][servicesImgId]"], input[name*="[${index}][image_id]"]`);
					const previewContainer = container.querySelector('.image-preview');
					fileInput.value = '';
					hiddenInput.value = '';
					if (previewContainer) previewContainer.remove();
				}
			});
		});
	</script>
<?php
}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function hcf_save_meta_box($post_id)
{
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if ($parent_id = wp_is_post_revision($post_id)) {
		$post_id = $parent_id;
	}

	// Initialize meta data
	$meta_data = isset($_POST['_ait-item_item-data']) ? $_POST['_ait-item_item-data'] : [];
	$meta_author = isset($_POST['_ait-item_item-author']) ? $_POST['_ait-item_item-author'] : [];

	// Process company services
	if (isset($meta_data['companyServices']) && is_array($meta_data['companyServices'])) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		foreach ($meta_data['companyServices'] as $index => &$service) {
			$service['services'] = sanitize_text_field($service['services'] ?? '');
			$service['servicesDesc'] = sanitize_text_field($service['servicesDesc'] ?? '');
			$service['servicesPrice'] = sanitize_text_field($service['servicesPrice'] ?? '');
			$image_id = isset($service['servicesImgId']) ? intval($service['servicesImgId']) : 0;

			// Handle new file upload for servicesImg
			if (!empty($_FILES['_ait-item_item-data']['name']['companyServices'][$index]['servicesImg'])) {
				$file = [
					'name' => $_FILES['_ait-item_item-data']['name']['companyServices'][$index]['servicesImg'],
					'type' => $_FILES['_ait-item_item-data']['type']['companyServices'][$index]['servicesImg'],
					'tmp_name' => $_FILES['_ait-item_item-data']['tmp_name']['companyServices'][$index]['servicesImg'],
					'error' => $_FILES['_ait-item_item-data']['error']['companyServices'][$index]['servicesImg'],
					'size' => $_FILES['_ait-item_item-data']['size']['companyServices'][$index]['servicesImg'],
				];

				// Validate file type and size
				$allowed_types = ['image/jpeg', 'image/png'];
				if (in_array($file['type'], $allowed_types) && $file['size'] <= 512000) {
					$upload = wp_upload_bits($file['name'], null, file_get_contents($file['tmp_name']));
					if (!$upload['error']) {
						$filename = $upload['file'];
						$wp_filetype = wp_check_filetype($filename, null);
						$attachment = [
							'post_mime_type' => $wp_filetype['type'],
							'post_parent' => $post_id,
							'post_title' => sanitize_file_name($file['name']),
							'post_content' => '',
							'post_status' => 'inherit',
						];
						$new_image_id = wp_insert_attachment($attachment, $filename, $post_id);
						$attach_data = wp_generate_attachment_metadata($new_image_id, $filename);
						wp_update_attachment_metadata($new_image_id, $attach_data);
						$service['servicesImgId'] = $new_image_id;
					} else {
						$service['servicesImgId'] = '';
					}
				} else {
					$service['servicesImgId'] = '';
				}
			} else {
				$service['servicesImgId'] = $image_id;
			}
		}
		unset($service); // Unset reference to avoid issues
	}

	// Process gallery images
	if (isset($meta_data['gallery']) && is_array($meta_data['gallery'])) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		foreach ($meta_data['gallery'] as $index => &$item) {
			$item['title'] = sanitize_text_field($item['title'] ?? '');
			$image_id = isset($item['image_id']) ? intval($item['image_id']) : 0;

			// Handle new file upload
			if (!empty($_FILES['_ait-item_item-data']['name']['gallery'][$index]['image'])) {
				$file = [
					'name' => $_FILES['_ait-item_item-data']['name']['gallery'][$index]['image'],
					'type' => $_FILES['_ait-item_item-data']['type']['gallery'][$index]['image'],
					'tmp_name' => $_FILES['_ait-item_item-data']['tmp_name']['gallery'][$index]['image'],
					'error' => $_FILES['_ait-item_item-data']['error']['gallery'][$index]['image'],
					'size' => $_FILES['_ait-item_item-data']['size']['gallery'][$index]['image'],
				];

				// Validate file type and size
				$allowed_types = ['image/jpeg', 'image/png'];
				if (!in_array($file['type'], $allowed_types) || $file['size'] > 512000) {
					$item['image_id'] = '';
					continue;
				}

				$upload = wp_upload_bits($file['name'], null, file_get_contents($file['tmp_name']));
				if (!$upload['error']) {
					$filename = $upload['file'];
					$wp_filetype = wp_check_filetype($filename, null);
					$attachment = [
						'post_mime_type' => $wp_filetype['type'],
						'post_parent' => $post_id,
						'post_title' => sanitize_file_name($file['name']),
						'post_content' => '',
						'post_status' => 'inherit',
					];
					$new_image_id = wp_insert_attachment($attachment, $filename, $post_id);
					$attach_data = wp_generate_attachment_metadata($new_image_id, $filename);
					wp_update_attachment_metadata($new_image_id, $attach_data);
					$item['image_id'] = $new_image_id;
				} else {
					$item['image_id'] = '';
				}
			} else {
				$item['image_id'] = $image_id;
			}
		}
		unset($item); // Unset reference to avoid issues
	}


	// Process opening hours
	$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
	if (isset($meta_data['openingHours']) && is_array($meta_data['openingHours'])) {
		foreach ($days as $day) {
			$from = isset($meta_data['openingHours'][$day]['from']) ? sanitize_text_field($meta_data['openingHours'][$day]['from']) : '';
			$to = isset($meta_data['openingHours'][$day]['to']) ? sanitize_text_field($meta_data['openingHours'][$day]['to']) : '';

			if ($from && $to) {
				// Convert to AM/PM format
				$from_time = date('h:i A', strtotime($from));
				$to_time = date('h:i A', strtotime($to));
				$meta_data['openingHours' . $day] = "$from_time - $to_time";
			} else {
				// Store empty string if no hours are provided
				$meta_data['openingHours' . $day] = '';
			}
		}
		// Remove the temporary openingHours array to avoid storing it
		unset($meta_data['openingHours']);
	}

	// Save meta data
	if (!empty($meta_data)) {
		update_post_meta($post_id, '_ait-item_item-data', $meta_data);
	}
	if (!empty($meta_author)) {
		update_post_meta($post_id, '_ait-item_item-author', $meta_author);
	}

	// Save taxonomies
	if (isset($_POST['ait-items'])) {
		wp_set_post_terms($post_id, array_map('intval', $_POST['ait-items']), 'ait-items');
	}
	if (isset($_POST['ait-locations'])) {
		wp_set_post_terms($post_id, array_map('intval', $_POST['ait-locations']), 'ait-locations');
	}
}
add_action('save_post', 'hcf_save_meta_box');
?>