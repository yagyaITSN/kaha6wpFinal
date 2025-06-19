<?php

/**
 * Template Name: Register Page
 *
 * @package WordPress
 */
get_header();
?>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBOvF0bRAeot5g2eFBG6Wji2aHw5Tct9g8&callback=initMap" async defer></script>

<?php
// Check if editing an existing post
$post_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$current_user_id = get_current_user_id();
$is_editing = $post_id > 0;

// Fetch existing post data if editing
$business_name = '';
$intro = '';
$metadata = [];
$metadata_author = [];
$ait_items = [];
$ait_locations = [];
$logo_url = '';
$terms_accepted = '';

if ($is_editing) {
    $post = get_post($post_id);
    if ($post && $post->post_type === 'ait-item' && ($post->post_author == $current_user_id || current_user_can('administrator'))) {
        $business_name = $post->post_title;
        $intro = $post->post_content;
        $metadata = get_post_meta($post_id, '_ait-item_item-data', true) ?: [];
        $metadata_author = get_post_meta($post_id, '_ait-item_item-author', true) ?: [];
        $ait_items = wp_get_post_terms($post_id, 'ait-items', ['fields' => 'ids']) ?: [];
        $ait_locations = wp_get_post_terms($post_id, 'ait-locations', ['fields' => 'ids']) ?: [];
        $logo_url = has_post_thumbnail($post_id) ? wp_get_attachment_url(get_post_thumbnail_id($post_id)) : '';
        $terms_accepted = get_post_meta($post_id, '_terms_accepted', true) ?: '';
    } else {
        $is_editing = false;
        $post_id = 0;
        echo '<section class="container py-4"><div class="bg-white my-3"><h5 style="color:red;">Error: Invalid post or permission denied.</h5></div></section>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_user_logged_in()) {
        echo '<section class="container py-4"><div class="bg-white my-3"><h5 style="color:red;">Error: You must be logged in to submit a business.</h5></div></section>';
    } else {
        $business_name = sanitize_text_field($_POST['bname']);
        $intro = wp_kses_post($_POST['intro']);
        $btype = isset($_POST['ait-items']) ? array_map('intval', $_POST['ait-items']) : [];
        $bloc = isset($_POST['ait-locations']) ? array_map('intval', $_POST['ait-locations']) : [];
        $terms_accepted = isset($_POST['termsCheck']) ? '1' : '0';

        // Prepare post data
        $post_data = [
            'ID' => $post_id,
            'post_title' => $business_name,
            'post_status' => $is_editing ? 'under_reverification' : 'draft',
            'post_content' => $intro,
            'post_type' => 'ait-item',
            'post_author' => $current_user_id,
            'comment_status' => 'open'
        ];
        $post_id = wp_insert_post($post_data, true);

        if (!is_wp_error($post_id)) {
            // Save meta data
            $meta_fields = [
                '_ait-item_item-author' => ['author' => $current_user_id],
                '_ait-item_item-data' => [
                    'subtitle' => sanitize_text_field($_POST['_ait-item_item-data']['subtitle'] ?? ''),
                    'postalCode' => sanitize_text_field($_POST['_ait-item_item-data']['postalCode'] ?? ''),
                    'telephone' => sanitize_text_field($_POST['_ait-item_item-data']['telephone'] ?? ''),
                    'telephoneAdditional' => [],
                    'companyServices' => array_map(function ($i) use ($post_id) {
                        $image_id = isset($_POST['_ait-item_item-data']['companyServices'][$i]['servicesImgId']) ? intval($_POST['_ait-item_item-data']['companyServices'][$i]['servicesImgId']) : 0;

                        if (!empty($_FILES['_ait-item_item-data']['name']['companyServices'][$i]['servicesImg']) && $_FILES['_ait-item_item-data']['error']['companyServices'][$i]['servicesImg'] === 0) {
                            $file = [
                                'name' => $_FILES['_ait-item_item-data']['name']['companyServices'][$i]['servicesImg'],
                                'type' => $_FILES['_ait-item_item-data']['type']['companyServices'][$i]['servicesImg'],
                                'tmp_name' => $_FILES['_ait-item_item-data']['tmp_name']['companyServices'][$i]['servicesImg'],
                                'error' => $_FILES['_ait-item_item-data']['error']['companyServices'][$i]['servicesImg'],
                                'size' => $_FILES['_ait-item_item-data']['size']['companyServices'][$i]['servicesImg'],
                            ];

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
                                    $image_id = wp_insert_attachment($attachment, $filename, $post_id);
                                    require_once ABSPATH . 'wp-admin/includes/image.php';
                                    $attach_data = wp_generate_attachment_metadata($image_id, $filename);
                                    wp_update_attachment_metadata($image_id, $attach_data);
                                }
                            }
                        }

                        return [
                            'services' => sanitize_text_field($_POST['_ait-item_item-data']['companyServices'][$i]['services'] ?? ''),
                            'servicesDesc' => sanitize_text_field($_POST['_ait-item_item-data']['companyServices'][$i]['servicesDesc'] ?? ''),
                            'servicesPrice' => sanitize_text_field($_POST['_ait-item_item-data']['companyServices'][$i]['servicesPrice'] ?? ''),
                            'servicesImgId' => $image_id,
                        ];
                    }, array_keys($_POST['_ait-item_item-data']['companyServices'] ?? [])),
                    'email' => sanitize_email($_POST['_ait-item_item-data']['email'] ?? ''),
                    'web' => esc_url_raw($_POST['_ait-item_item-data']['web'] ?? ''),
                    'map' => [
                        'address' => sanitize_text_field($_POST['_ait-item_item-data']['map']['address'] ?? ''),
                        'latitude' => sanitize_text_field($_POST['_ait-item_item-data']['map']['latitude'] ?? ''),
                        'longitude' => sanitize_text_field($_POST['_ait-item_item-data']['map']['longitude'] ?? ''),
                    ],
                    'socialIcons' => array_map(function ($i) {
                        return [
                            'link' => esc_url_raw($_POST['_ait-item_item-data']['socialIcons'][$i]['link'] ?? ''),
                            'icon' => sanitize_text_field($_POST['_ait-item_item-data']['socialIcons'][$i]['icon'] ?? ''),
                        ];
                    }, array_keys($_POST['_ait-item_item-data']['socialIcons'] ?? [])),
                    'openingHoursNote' => sanitize_text_field($_POST['_ait-item_item-data']['openingHoursNote'] ?? ''),
                    'gallery' => [],
                    'featuredItem' => isset($_POST['_ait-item_item-data']['featuredItem']) ? intval($_POST['_ait-item_item-data']['featuredItem']) : 0,
                ],
                '_terms_accepted' => $terms_accepted,
            ];

            // Handle telephoneAdditional
            $additionalPhones = $_POST['_ait-item_item-data']['telephoneAdditional'] ?? [];
            foreach ($additionalPhones as $index => $phone) {
                if (!empty($phone['number'])) {
                    $meta_fields['_ait-item_item-data']['telephoneAdditional'][] = [
                        'number' => sanitize_text_field($phone['number']),
                    ];
                }
            }

            // Handle gallery images
            for ($i = 0; $i < 5; $i++) {
                $image_id = isset($_POST['_ait-item_item-data']['gallery'][$i]['image_id']) ? intval($_POST['_ait-item_item-data']['gallery'][$i]['image_id']) : 0;
                $title = sanitize_text_field($_POST['_ait-item_item-data']['gallery'][$i]['title'] ?? '');

                if (!empty($_FILES['_ait-item_item-data']['name']['gallery'][$i]['image']) && $_FILES['_ait-item_item-data']['error']['gallery'][$i]['image'] === 0) {
                    $file = [
                        'name' => $_FILES['_ait-item_item-data']['name']['gallery'][$i]['image'],
                        'type' => $_FILES['_ait-item_item-data']['type']['gallery'][$i]['image'],
                        'tmp_name' => $_FILES['_ait-item_item-data']['tmp_name']['gallery'][$i]['image'],
                        'error' => $_FILES['_ait-item_item-data']['error']['gallery'][$i]['image'],
                        'size' => $_FILES['_ait-item_item-data']['size']['gallery'][$i]['image'],
                    ];

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
                            $image_id = wp_insert_attachment($attachment, $filename, $post_id);
                            $attach_data = wp_generate_attachment_metadata($image_id, $filename);
                            wp_update_attachment_metadata($image_id, $attach_data);
                        }
                    }
                }

                $meta_fields['_ait-item_item-data']['gallery'][$i] = [
                    'title' => $title,
                    'image_id' => $image_id,
                ];
            }

            // Handle opening hours
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            foreach ($days as $day) {
                $from = sanitize_text_field($_POST['_ait-item_item-data']['openingHours'][$day]['from'] ?? '');
                $to = sanitize_text_field($_POST['_ait-item_item-data']['openingHours'][$day]['to'] ?? '');
                if ($from && $to) {
                    $from_time = date('h:i A', strtotime($from));
                    $to_time = date('h:i A', strtotime($to));
                    $meta_fields['_ait-item_item-data']['openingHours' . $day] = "$from_time - $to_time";
                } else {
                    $meta_fields['_ait-item_item-data']['openingHours' . $day] = '';
                }
            }

            // Set claim meta for new businesses created by subscribers
            if (!$is_editing && in_array('subscriber', (array) wp_get_current_user()->roles)) {
                $meta_fields['_bcv_user_id'] = $current_user_id;
                $meta_fields['_bcv_claim_status'] = 'not_claimed';
                $meta_fields['_bcv_verification_status'] = 'not_verified';
                $meta_fields['_bcv_request_date'] = current_time('mysql');
            }

            foreach ($meta_fields as $key => $value) {
                update_post_meta($post_id, $key, $value);
            }

            // Handle logo upload
            if (!empty($_FILES['businessimage']['name']) && $_FILES['businessimage']['error'] === 0) {
                $allowed_types = ['image/jpeg', 'image/png'];
                if (in_array($_FILES['businessimage']['type'], $allowed_types) && $_FILES['businessimage']['size'] <= 512000) {
                    $upload = wp_upload_bits($_FILES['businessimage']['name'], null, file_get_contents($_FILES['businessimage']['tmp_name']));
                    if (!$upload['error']) {
                        $filename = $upload['file'];
                        $wp_filetype = wp_check_filetype($filename, null);
                        $attachment = [
                            'post_mime_type' => $wp_filetype['type'],
                            'post_parent' => $post_id,
                            'post_title' => sanitize_file_name($_FILES['businessimage']['name']),
                            'post_content' => '',
                            'post_status' => 'inherit',
                        ];
                        $attach_id = wp_insert_attachment($attachment, $filename, $post_id);
                        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        set_post_thumbnail($post_id, $attach_id);
                    } else {
                        echo '<section class="container py-4"><div class="bg-white my-3"><h5 class="text-primary">Unable to upload logo: ' . esc_html($upload['error']) . '</h5></div></section>';
                    }
                } else {
                    echo '<section class="container py-4"><div class="bg-white my-3"><h5 class="text-primary">Invalid logo file type or size exceeds 500KB.</h5></div></section>';
                }
            }

            // Set taxonomy terms
            if ($btype) {
                wp_set_post_terms($post_id, $btype, 'ait-items');
            }
            if ($bloc) {
                wp_set_post_terms($post_id, $bloc, 'ait-locations');
            }

            // Display success message and redirect
            echo '<section class="container"><div class="bg-white my-3"><h5 class="title px-4 py-4 text-center" style="color:green;">' . ($is_editing ? 'Your listing has been updated and is under review.' : 'Thank You! Your Company has been registered Successfully and is under review.') . '</h5></div><script>setTimeout(function() { window.location.href = \'' . esc_url(home_url('/dashboard')) . '\'; }, 2000);</script></section>';
        } else {
            echo '<section class="container py-4"><div class="bg-white my-3"><h5 class="text-primary">Error saving business: ' . esc_html($post_id->get_error_message()) . '</h5></div></section>';
        }
    }
}
$verification_status = get_post_meta($post_id, '_bcv_verification_status', true) ?: 'not_verified';
?>

<!-- Header Section -->
<?php get_template_part('parts/common/header', 'section'); ?>


<!-- Registration Form -->
<div class="container mb-5">
    <div class="registration-container shadow-sm border-0 text-decoration-none rounded-4 p-4">
        <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3"><?php echo $is_editing ? 'Edit Company Details' : 'Company Registration Form'; ?></h2>
        <form class="mt-5 needs-validation" id="businessRegistrationForm" novalidate method="post" enctype="multipart/form-data">
            <!-- General Information Section -->
            <div class="section" id="general-info">
                <h3 class="fs-5 fw-bold">General Information</h3>
                <div class="form-grid">
                    <div class="row">
                        <div class="col-md-6 form-item">
                            <label for="bname" class="form-label mb-1">Business Name *</label>
                            <input type="text" class="form-control" name="bname" id="bname" value="<?php echo esc_attr($business_name); ?>"
                                placeholder="Enter your business name" required
                                <?php if ($is_editing && $verification_status == 'verified') {
                                    echo 'disabled';
                                } ?>>
                            <p class="form-note">
                                <?php if ($is_editing && $verification_status == 'verified') {
                                    echo "Verified business name can't be updated.";
                                } ?>
                            </p>
                            <div class="invalid-feedback">Please provide a business name.</div>
                        </div>
                        <div class="col-md-6 form-item">
                            <label for="_ait-item_item-data[subtitle]" class="form-label mb-1">Sub Title</label>
                            <input type="text" name="_ait-item_item-data[subtitle]" class="form-control" id="_ait-item_item-data[subtitle]" placeholder="Business subtitle" value="<?php echo esc_attr($metadata['subtitle'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-item">
                            <label for="reg_type" class="form-label mb-1">Select Category</label>
                            <div class="accordion" id="reg_type">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            Select the Business category
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#reg_type">
                                        <div class="accordion-body">
                                            <?php echo get_terms_chekboxes_model('ait-items', ['hide_empty' => false], $ait_items); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invalid-feedback">Please select a business category.</div>
                        </div>
                        <div class="col-md-6 form-item">
                            <label for="businessimage" class="form-label mb-1">Business Logo</label>
                            <img src="<?php echo esc_url($logo_url ?: ''); ?>" id="logoPreview" class="logo-preview" alt="Logo preview" style="<?php echo $logo_url ? 'display:block;' : 'display:none;'; ?>">
                            <input type="file" class="form-control" id="businessimage" name="businessimage" accept="image/png, image/jpeg" onchange="previewLogo(this)">
                            <p class="form-note">Please enter 1:1 ratio image. 500 x 500 resolution, JPG/PNG</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label for="briefIntro" class="form-label mb-1">Brief Introduction</label>
                            <?php
                            wp_editor(
                                $intro,
                                'intro',
                                array(
                                    'textarea_name' => 'intro',
                                    'media_buttons' => false,
                                    'textarea_rows' => 10,
                                    'editor_class'  => 'form-control',
                                    'teeny'         => false,
                                    'quicktags'     => true
                                )
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="section" id="address-info">
                <h3 class="fs-5 fw-bold">Address Information</h3>
                <div class="form-grid">
                    <div class="row">
                        <div class="col-md-6 form-item">
                            <label for="reg_type" class="form-label mb-1">Select Location</label>
                            <div class="accordion" id="reg_location">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Select Business Location
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#reg_location">
                                        <div class="accordion-body">
                                            <?php echo get_terms_chekboxes_model('ait-locations', ['hide_empty' => false], $ait_locations); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invalid-feedback">Please select a business Location.</div>
                        </div>
                        <div class="col-md-6 form-item">
                            <label for="_ait-item_item-data[map][address]" class="form-label mb-1">Street Address *</label>
                            <input id="_ait-item_item-data[map][address]" type="text" name="_ait-item_item-data[map][address]" class="form-control" placeholder="Enter street address" value="<?php echo esc_attr($metadata['map']['address'] ?? ''); ?>" required>
                            <div class="invalid-feedback">Please provide a street address.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-item">
                            <label for="_ait-item_item-data[postalCode]" class="form-label mb-1">Postal Code</label>
                            <input type="number" class="form-control" id="_ait-item_item-data[postalCode]" name="_ait-item_item-data[postalCode]" value="<?php echo esc_attr($metadata['postalCode'] ?? ''); ?>" placeholder="Enter postal code">
                        </div>
                        <div class="col-md-6 form-item">
                            <label for="_ait-item_item-data[map][latitude]" class="form-label mb-1">Latitude</label>
                            <input type="text" class="form-control" name="_ait-item_item-data[map][latitude]" id="_ait-item_item-data[map][latitude]" placeholder="Enter latitude" value="<?php echo esc_attr($metadata['map']['latitude'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-item">
                            <label for="_ait-item_item-data[map][longitude]" class="form-label mb-1">Longitude</label>
                            <input type="text" class="form-control" id="_ait-item_item-data[map][longitude]" name="_ait-item_item-data[map][longitude]" placeholder="Enter longitude" value="<?php echo esc_attr($metadata['map']['longitude'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="row">
                        <span class="fs-7 font-italic ps-3 mb-2">Drag and drop the marker pin to your location</span>
                        <div class="col-12 form-row mb-3">
                            <div class="form-group col-md-12">
                                <div id="map_canvas" style="height:400px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="section" id="contact-info">
                <h3 class="fs-5 fw-bold">Contact Information</h3>
                <div class="form-grid">
                    <!-- Phone Numbers -->
                    <div class="row" id="contactFields">
                        <div class="col-md-6 col-lg-4 form-item">
                            <div class="contact-item">
                                <label class="form-label mb-1" for="_ait-item_item-data[telephone]"><strong>Phone *</strong></label>
                                <input type="tel" class="form-control" id="_ait-item_item-data[telephone]" name="_ait-item_item-data[telephone]"
                                    placeholder="Enter phone number" required pattern="[0-9]{10,15}"
                                    value="<?php echo esc_attr($metadata['telephone'] ?? ''); ?>">
                                <div class="invalid-feedback">Please provide a valid phone number (10-15 digits).</div>
                            </div>
                        </div>
                        <?php
                        $additionalPhones = $metadata['telephoneAdditional'] ?? [];
                        $validPhones = array_filter($additionalPhones, function ($phone) {
                            return isset($phone['number']) && !empty($phone['number']);
                        });
                        if (!empty($validPhones)) {
                            foreach ($validPhones as $index => $phone) {
                        ?>
                                <div class="col-md-6 col-lg-4 form-item">
                                    <div class="contact-item">
                                        <button type="button" class="remove-btn" onclick="removeField(this, 'phone')" aria-label="Remove phone number">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        <label class="form-label mb-1" for="_ait-item_item-data[telephoneAdditional][<?php echo $index; ?>][number]">
                                            <strong>Phone <?php echo $index + 1; ?></strong>
                                        </label>
                                        <input type="tel" class="form-control"
                                            id="_ait-item_item-data[telephoneAdditional][<?php echo $index; ?>][number]"
                                            name="_ait-item_item-data[telephoneAdditional][<?php echo $index; ?>][number]"
                                            placeholder="Enter additional phone number" pattern="[0-9]{10,15}"
                                            value="<?php echo esc_attr($phone['number']); ?>">
                                        <div class="invalid-feedback">Please provide a valid phone number (10-15 digits).</div>
                                    </div>
                                </div>
                            <?php
                            }
                        } else {
                            // Display one empty phone field by default
                            ?>
                            <div class="col-md-6 col-lg-4 form-item" data-index="0">
                                <div class="contact-item">
                                    <button type="button" class="remove-btn" onclick="removeField(this, 'phone')" aria-label="Remove phone number">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    <label class="form-label mb-1" for="_ait-item_item-data[telephoneAdditional][0][number]">
                                        <strong>Phone 1</strong>
                                    </label>
                                    <input type="tel" class="form-control"
                                        id="_ait-item_item-data[telephoneAdditional][0][number]"
                                        name="_ait-item_item-data[telephoneAdditional][0][number]"
                                        placeholder="Enter additional phone number" pattern="[0-9]{10,15}">
                                    <div class="invalid-feedback">Please provide a valid phone number (10-15 digits).</div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <button type="button" class="btn btn-custom-red add-more-btn" onclick="addContactField()" id="addPhoneBtn">
                        <i class="bi bi-plus"></i> Add More Phone Number
                    </button>

                    <!-- Emails -->
                    <div class="row" id="email">
                        <div class="col-md-6 form-item">
                            <div>
                                <label class="form-label mb-1" for="_ait-item_item-data[email]">Email *</label>
                                <input class="form-control" id="_ait-item_item-data[email]" type="email" name="_ait-item_item-data[email]" placeholder="Enter Email" value="<?php echo esc_attr($metadata['email'] ?? ''); ?>" required>
                                <div class="invalid-feedback">Please provide a valid email address.</div>
                            </div>
                        </div>

                        <!-- Web URL -->
                        <div class="col-md-6 form-item">
                            <div>
                                <label class="form-label mb-1" for="_ait-item_item-data[web]">Web URL</label>
                                <input class="form-control" id="_ait-item_item-data[web]" type="url" name="_ait-item_item-data[web]" placeholder="Enter web URL" value="<?php echo esc_attr($metadata['web'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opening Hours -->
            <div class="section" id="opening-hours">
                <h3 class="fs-5 fw-bold mb-4">Opening Hours</h3>
                <div class="my-3">
                    <label for="_ait-item_item-data[openingHoursNote]" class="form-label">Opening Hour Note</label>
                    <input type="text" class="form-control" id="_ait-item_item-data[openingHoursNote]" name="_ait-item_item-data[openingHoursNote]" placeholder="E.g., Closed on public holidays" value="<?php echo esc_attr($metadata['openingHoursNote'] ?? ''); ?>">
                </div>
                <?php
                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                ?>
                <div class="row g-3" id="openingHoursGrid">
                    <?php foreach ($days as $day) : ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card opening-hours-card">
                                <div class="card-body">
                                    <h5 class="day-title"><?php echo esc_html($day); ?></h5>
                                    <div class="time-grid">
                                        <div class="row g-2 align-items-center">
                                            <?php
                                            $hours = $metadata['openingHours' . $day] ?? '';
                                            $from_time = '';
                                            $to_time = '';
                                            if ($hours && strpos($hours, '-') !== false) {
                                                $parts = array_map('trim', explode('-', $hours));
                                                if (count($parts) === 2 && strtotime($parts[0]) !== false && strtotime($parts[1]) !== false) {
                                                    $from_time = date('H:i', strtotime($parts[0]));
                                                    $to_time = date('H:i', strtotime($parts[1]));
                                                }
                                            }
                                            ?>
                                            <div class="col-5">
                                                <input class="form-control time-select-sm mb-0" type="time"
                                                    name="_ait-item_item-data[openingHours][<?php echo esc_attr($day); ?>][from]"
                                                    id="_ait-item_item-data[openingHours][<?php echo esc_attr($day); ?>][from]"
                                                    value="<?php echo esc_attr($from_time); ?>">
                                            </div>
                                            <div class="col-2 text-center">
                                                <span class="text-muted">to</span>
                                            </div>
                                            <div class="col-5">
                                                <input class="form-control time-select-sm mb-0" type="time"
                                                    name="_ait-item_item-data[openingHours][<?php echo esc_attr($day); ?>][to]"
                                                    id="_ait-item_item-data[openingHours][<?php echo esc_attr($day); ?>][to]"
                                                    value="<?php echo esc_attr($to_time); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Company Services -->
            <div class="section" id="company-services">
                <h3 class="fs-5 fw-bold">Company Services</h3>
                <div class="form-grid">
                    <div class="row" id="companyServices">
                        <?php
                        $companyServices = $metadata['companyServices'] ?? [];
                        $validServices = array_filter($companyServices, function ($service) {
                            return !empty($service['services']) || !empty($service['servicesDesc']) || !empty($service['servicesPrice']) || !empty($service['servicesImgId']);
                        });
                        if (!empty($validServices)) {
                            foreach ($validServices as $i => $service) {
                                $image_id = $service['servicesImgId'] ?? '';
                                $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
                        ?>
                                <div class="col-md-6 col-lg-4 form-item" data-index="<?php echo $i; ?>">
                                    <div class="services-items">
                                        <button type="button" class="remove-btn" onclick="removeField(this, 'service')" aria-label="Remove service">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        <label for="_ait-item_item-data[companyServices][<?php echo $i; ?>][services]" class="form-label">Service</label>
                                        <input type="text"
                                            name="_ait-item_item-data[companyServices][<?php echo $i; ?>][services]"
                                            id="_ait-item_item-data[companyServices][<?php echo $i; ?>][services]"
                                            class="form-control"
                                            placeholder="Enter service name"
                                            value="<?php echo esc_attr($service['services']); ?>">
                                        <label for="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesDesc]" class="form-label">Service Description</label>
                                        <textarea name="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesDesc]"
                                            id="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesDesc]"
                                            class="form-control"
                                            placeholder="Enter one line description"><?php echo esc_textarea($service['servicesDesc']); ?></textarea>
                                        <label for="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesPrice]" class="form-label">Starting From</label>
                                        <input type="number"
                                            name="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesPrice]"
                                            id="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesPrice]"
                                            class="form-control"
                                            placeholder="Your service price"
                                            value="<?php echo esc_attr($service['servicesPrice']); ?>">
                                        <label for="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesImg]" class="form-label">Service Banner</label>
                                        <img class="gallery-preview" src="<?php echo esc_url($image_url); ?>" alt="Service preview" style="<?php echo $image_url ? 'display:block;max-width:100px;max-height:100px;' : 'display:none;'; ?>">
                                        <input type="file"
                                            name="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesImg]"
                                            id="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesImg]"
                                            class="form-control"
                                            accept="image/png,image/jpeg"
                                            onchange="previewImage(this)">
                                        <input type="hidden"
                                            name="_ait-item_item-data[companyServices][<?php echo $i; ?>][servicesImgId]"
                                            value="<?php echo esc_attr($image_id); ?>">
                                        <p class="form-note">JPG/PNG</p>
                                    </div>
                                </div>
                            <?php
                            }
                        } else {
                            // Display one empty service field by default
                            ?>
                            <div class="col-md-6 col-lg-4 form-item" data-index="0">
                                <div class="services-items">
                                    <button type="button" class="remove-btn" onclick="removeField(this, 'service')" aria-label="Remove service">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    <label for="_ait-item_item-data[companyServices][0][services]" class="form-label">Service</label>
                                    <input type="text"
                                        name="_ait-item_item-data[companyServices][0][services]"
                                        id="_ait-item_item-data[companyServices][0][services]"
                                        class="form-control"
                                        placeholder="Enter service name">
                                    <label for="_ait-item_item-data[companyServices][0][servicesDesc]" class="form-label">Service Description</label>
                                    <textarea name="_ait-item_item-data[companyServices][0][servicesDesc]"
                                        id="_ait-item_item-data[companyServices][0][servicesDesc]"
                                        class="form-control"
                                        placeholder="Enter one line description"></textarea>
                                    <label for="_ait-item_item-data[companyServices][0][servicesPrice]" class="form-label">Starting From</label>
                                    <input type="number"
                                        name="_ait-item_item-data[companyServices][0][servicesPrice]"
                                        id="_ait-item_item-data[companyServices][0][servicesPrice]"
                                        class="form-control"
                                        placeholder="Your service price">
                                    <label for="_ait-item_item-data[companyServices][0][servicesImg]" class="form-label">Service Banner</label>
                                    <img class="gallery-preview" alt="Service preview" style="display:none;max-width:100px;max-height:100px;">
                                    <input type="file"
                                        name="_ait-item_item-data[companyServices][0][servicesImg]"
                                        id="_ait-item_item-data[companyServices][0][servicesImg]"
                                        class="form-control"
                                        accept="image/png,image/jpeg"
                                        onchange="previewImage(this)">
                                    <input type="hidden"
                                        name="_ait-item_item-data[companyServices][0][servicesImgId]"
                                        value="">
                                    <p class="form-note">JPG/PNG</p>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <button type="button" class="btn btn-custom-red add-more-btn" onclick="addServiceField()" id="addServiceBtn">
                        <i class="bi bi-plus"></i> Add More Service
                    </button>
                </div>
            </div>

            <!-- Social Profiles -->
            <div class="section" id="social-profiles">
                <h3 class="fs-5 fw-bold">Social Profiles</h3>
                <div class="form-grid">
                    <div class="row" id="socialFields">
                        <?php
                        $socialIcons = $metadata['socialIcons'] ?? [];
                        $validSocials = array_filter($socialIcons, function ($social) {
                            return !empty($social['icon']) || !empty($social['link']);
                        });
                        $platform_map = [
                            'fa-brands fa-facebook-f fb-color-code' => 'Facebook',
                            'fa-brands fa-x-twitter x-color-code' => 'Twitter',
                            'fa-brands fa-instagram insta-color-code' => 'Instagram',
                            'fa-brands fa-tiktok tiktok-color-code' => 'TikTok',
                            'fa-brands fa-linkedin-in linkedin-color-code' => 'LinkedIn',
                            'fa-brands fa-youtube yt-color-code' => 'YouTube'
                        ];
                        if (!empty($validSocials)) {
                            foreach ($validSocials as $i => $social) {
                        ?>
                                <div class="col-md-6 col-lg-4 form-item" data-index="<?php echo $i; ?>">
                                    <div class="social-item">
                                        <button type="button" class="remove-btn" onclick="removeField(this, 'social')" aria-label="Remove social media">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        <label class="form-label mb-1">Social Media</label>
                                        <select class="form-select mb-2" name="_ait-item_item-data[socialIcons][<?php echo $i; ?>][icon]">
                                            <option selected disabled>Select platform</option>
                                            <?php foreach ($platform_map as $value => $label) : ?>
                                                <option value="<?php echo esc_attr($value); ?>" <?php selected($social['icon'], $value); ?>><?php echo esc_html($label); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="url" class="form-control" name="_ait-item_item-data[socialIcons][<?php echo $i; ?>][link]" placeholder="Enter profile URL" pattern="https?://.+" value="<?php echo esc_attr($social['link']); ?>">
                                        <div class="invalid-feedback">Please provide a valid URL (starting with http:// or https://).</div>
                                    </div>
                                </div>
                            <?php
                            }
                        } else {
                            // Display one empty social field by default
                            ?>
                            <div class="col-md-6 col-lg-4 form-item" data-index="0">
                                <div class="social-item">
                                    <button type="button" class="remove-btn" onclick="removeField(this, 'social')" aria-label="Remove social media">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    <label class="form-label mb-1">Social Media</label>
                                    <select class="form-select mb-2" name="_ait-item_item-data[socialIcons][0][icon]">
                                        <option selected disabled>Select platform</option>
                                        <?php foreach ($platform_map as $value => $label) : ?>
                                            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="url" class="form-control" name="_ait-item_item-data[socialIcons][0][link]" placeholder="Enter profile URL" pattern="https?://.+">
                                    <div class="invalid-feedback">Please provide a valid URL (starting with http:// or https://).</div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <button type="button" class="btn btn-custom-red add-more-btn" onclick="addSocialField()" id="addSocialBtn">
                        <i class="bi bi-plus"></i> Add More Social Media
                    </button>
                </div>
            </div>

            <!-- Gallery -->
            <div class="section" id="business-gallery">
                <h3 class="fs-5 fw-bold">Gallery (Max 5 images)</h3>
                <div class="form-grid">
                    <div class="row" id="galleryFields">
                        <?php
                        $gallery = $metadata['gallery'] ?? [];
                        $validGallery = array_filter($gallery, function ($item) {
                            return !empty($item['image_id']) || !empty($item['title']);
                        });
                        if (!empty($validGallery)) {
                            foreach ($validGallery as $i => $gallery_item) {
                                $image_id = $gallery_item['image_id'] ?? '';
                                $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
                        ?>
                                <div class="col-md-6 col-lg-4 form-item" data-index="<?php echo $i; ?>">
                                    <div class="gallery-item">
                                        <button type="button" class="remove-btn" onclick="removeField(this, 'gallery')" aria-label="Remove image">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        <label class="form-label mb-1">Image Title</label>
                                        <input type="text" class="form-control mb-2" name="_ait-item_item-data[gallery][<?php echo $i; ?>][title]" placeholder="Image title" value="<?php echo esc_attr($gallery_item['title']); ?>">
                                        <img class="gallery-preview" src="<?php echo esc_url($image_url); ?>" alt="Gallery preview" style="<?php echo $image_url ? 'display:block;max-width:100px;max-height:100px;' : 'display:none;'; ?>">
                                        <input type="file" class="form-control" name="_ait-item_item-data[gallery][<?php echo $i; ?>][image]" accept="image/png, image/jpeg" onchange="previewImage(this)">
                                        <input type="hidden" name="_ait-item_item-data[gallery][<?php echo $i; ?>][image_id]" value="<?php echo esc_attr($image_id); ?>">
                                        <p class="form-note">JPG/PNG, < 500KB</p>
                                    </div>
                                </div>
                            <?php
                            }
                        } else {
                            // Display one empty gallery field by default
                            ?>
                            <div class="col-md-6 col-lg-4 form-item" data-index="0">
                                <div class="gallery-item">
                                    <button type="button" class="remove-btn" onclick="removeField(this, 'gallery')" aria-label="Remove image">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    <label class="form-label mb-1">Image Title</label>
                                    <input type="text" class="form-control mb-2" name="_ait-item_item-data[gallery][0][title]" placeholder="Image title">
                                    <img class="gallery-preview" alt="Gallery preview" style="display:none;max-width:100px;max-height:100px;">
                                    <input type="file" class="form-control" name="_ait-item_item-data[gallery][0][image]" accept="image/png, image/jpeg" onchange="previewImage(this)">
                                    <input type="hidden" name="_ait-item_item-data[gallery][0][image_id]" value="">
                                    <p class="form-note">JPG/PNG, < 500KB</p>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <button type="button" class="btn btn-custom-red add-more-btn" onclick="addGalleryField()" id="addGalleryBtn">
                        <i class="bi bi-plus"></i> Add More Image
                    </button>
                </div>
            </div>

            <!-- Terms and Submit -->
            <div class="mt-4">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="termsCheck" name="termsCheck" required <?php echo $terms_accepted ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="termsCheck">
                        I agree to the <a href="#" class="d-inline-block" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> *
                    </label>
                    <div class="invalid-feedback">You must agree to the terms and conditions.</div>
                </div>
                <button type="submit" class="btn btn-custom-red submit-btn">
                    <span id="submitText"><?php echo $is_editing ? 'Update Business' : 'Submit Business'; ?></span>
                    <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </form>
        <!-- Notification Container -->
        <div class="notification-container" id="notificationContainer"></div>
    </div>
</div>

<!-- Terms Modal -->
<?php get_template_part('parts/business-register/modal') ?>

<script type="text/javascript">
    window.addEventListener('load', function() {
        var map = new google.maps.Map(document.getElementById('map_canvas'), {
            zoom: 7,
            center: new google.maps.LatLng(<?php echo esc_js($metadata['map']['latitude'] ?? '28.165'); ?>, <?php echo esc_js($metadata['map']['longitude'] ?? '84.680'); ?>),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var myMarker = new google.maps.Marker({
            position: new google.maps.LatLng(<?php echo esc_js($metadata['map']['latitude'] ?? '28.165'); ?>, <?php echo esc_js($metadata['map']['longitude'] ?? '84.680'); ?>),
            draggable: true
        });

        google.maps.event.addListener(myMarker, 'dragend', function(evt) {
            document.getElementById('_ait-item_item-data[map][latitude]').value = evt.latLng.lat().toFixed(3);
            document.getElementById('_ait-item_item-data[map][longitude]').value = evt.latLng.lng().toFixed(3);
        });

        map.setCenter(myMarker.position);
        myMarker.setMap(map);

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    myMarker.setPosition(pos);
                    map.setCenter(pos);
                    document.getElementById('_ait-item_item-data[map][latitude]').value = position.coords.latitude.toFixed(3);
                    document.getElementById('_ait-item_item-data[map][longitude]').value = position.coords.longitude.toFixed(3);
                });
            }
        }
        getLocation();
    });
</script>

<script>
    // Initialize variables
    let phoneCount = <?php echo count($validPhones) ?: 1; ?>;
    let socialCount = <?php echo count($validSocials) ?: 1; ?>;
    let serviceCount = <?php echo count($validServices) ?: 1; ?>;
    let galleryCount = <?php echo count($validGallery) ?: 1; ?>;
    const maxPhoneItems = 3;
    const maxSocialItems = 6;
    const maxServiceItems = 5;
    const maxGalleryItems = 5;

    // Add new phone field
    function addContactField() {
        if (phoneCount >= maxPhoneItems) {
            document.getElementById('addPhoneBtn').disabled = true;
            showNotification('info', `Maximum ${maxPhoneItems} phone numbers allowed`);
            return;
        }

        const contactFields = document.getElementById('contactFields');
        const newField = document.createElement('div');
        newField.className = 'col-md-6 col-lg-4 form-item';
        newField.setAttribute('data-index', phoneCount);
        newField.innerHTML = `
            <div class="contact-item">
                <button type="button" class="remove-btn" onclick="removeField(this, 'phone')" aria-label="Remove phone number">
                    <i class="bi bi-x-circle"></i>
                </button>
                <label class="form-label mb-1" for="_ait-item_item-data[telephoneAdditional][${phoneCount}][number]">
                    <strong>Phone ${phoneCount + 1}</strong>
                </label>
                <input type="tel" class="form-control" 
                       id="_ait-item_item-data[telephoneAdditional][${phoneCount}][number]" 
                       name="_ait-item_item-data[telephoneAdditional][${phoneCount}][number]" 
                       placeholder="Enter additional phone number" pattern="[0-9]{10,15}">
                <div class="invalid-feedback">Please provide a valid phone number (10-15 digits).</div>
            </div>
        `;
        contactFields.appendChild(newField);
        phoneCount++;
        document.getElementById('addPhoneBtn').disabled = phoneCount >= maxPhoneItems;
        updateProgress();
    }

    // Add new service field
    function addServiceField() {
        if (serviceCount >= maxServiceItems) {
            document.getElementById('addServiceBtn').disabled = true;
            showNotification('info', `Maximum ${maxServiceItems} services allowed`);
            return;
        }

        const companyServices = document.getElementById('companyServices');
        const newIndex = serviceCount;
        const newField = document.createElement('div');
        newField.className = 'col-md-6 col-lg-4 form-item';
        newField.setAttribute('data-index', newIndex);
        newField.innerHTML = `
            <div class="services-items">
                <button type="button" class="remove-btn" onclick="removeField(this, 'service')" aria-label="Remove service">
                    <i class="bi bi-x-circle"></i>
                </button>
                <label for="_ait-item_item-data[companyServices][${newIndex}][services]" class="form-label">Service</label>
                <input type="text" 
                       name="_ait-item_item-data[companyServices][${newIndex}][services]" 
                       id="_ait-item_item-data[companyServices][${newIndex}][services]" 
                       class="form-control" 
                       placeholder="Enter service name">
                <label for="_ait-item_item-data[companyServices][${newIndex}][servicesDesc]" class="form-label">Service Description</label>
                <textarea name="_ait-item_item-data[companyServices][${newIndex}][servicesDesc]" 
                          id="_ait-item_item-data[companyServices][${newIndex}][servicesDesc]" 
                          class="form-control" 
                          placeholder="Enter one line description"></textarea>
                <label for="_ait-item_item-data[companyServices][${newIndex}][servicesPrice]" class="form-label">Starting From</label>
                <input type="number" 
                       name="_ait-item_item-data[companyServices][${newIndex}][servicesPrice]" 
                       id="_ait-item_item-data[companyServices][${newIndex}][servicesPrice]" 
                       class="form-control" 
                       placeholder="Your service price">
                <label for="_ait-item_item-data[companyServices][${newIndex}][servicesImg]" class="form-label">Service Banner</label>
                <img class="gallery-preview" alt="Service preview" style="display:none;max-width:100px;max-height:100px;">
                <input type="file" 
                       name="_ait-item_item-data[companyServices][${newIndex}][servicesImg]" 
                       id="_ait-item_item-data[companyServices][${newIndex}][servicesImg]" 
                       class="form-control" 
                       accept="image/png,image/jpeg" 
                       onchange="previewImage(this)">
                <input type="hidden" 
                       name="_ait-item_item-data[companyServices][${newIndex}][servicesImgId]" 
                       value="">
                <p class="form-note">JPG/PNG</p>
            </div>
        `;
        companyServices.appendChild(newField);
        serviceCount++;
        document.getElementById('addServiceBtn').disabled = serviceCount >= maxServiceItems;
        updateProgress();
    }

    // Add new social field
    function addSocialField() {
        if (socialCount >= maxSocialItems) {
            document.getElementById('addSocialBtn').disabled = true;
            showNotification('info', `Maximum ${maxSocialItems} social media links allowed`);
            return;
        }

        const socialFields = document.getElementById('socialFields');
        const newIndex = socialCount;
        const newField = document.createElement('div');
        newField.className = 'col-md-6 col-lg-4 form-item';
        newField.setAttribute('data-index', newIndex);
        newField.innerHTML = `
            <div class="social-item">
                <button type="button" class="remove-btn" onclick="removeField(this, 'social')" aria-label="Remove social media">
                    <i class="bi bi-x-circle"></i>
                </button>
                <label class="form-label mb-1">Social Media</label>
                <select class="form-select mb-2" name="_ait-item_item-data[socialIcons][${newIndex}][icon]">
                    <option selected disabled>Select platform</option>
                    <option value="fa-brands fa-facebook-f fb-color-code">Facebook</option>
                    <option value="fa-brands fa-x-twitter x-color-code">Twitter</option>
                    <option value="fa-brands fa-instagram insta-color-code">Instagram</option>
                    <option value="fa-brands fa-tiktok tiktok-color-code">TikTok</option>
                    <option value="fa-brands fa-linkedin-in linkedin-color-code">LinkedIn</option>
                    <option value="fa-brands fa-youtube yt-color-code">YouTube</option>
                </select>
                <input type="url" class="form-control" name="_ait-item_item-data[socialIcons][${newIndex}][link]" placeholder="Enter profile URL" pattern="https?://.+">
                <div class="invalid-feedback">Please provide a valid URL (starting with http:// or https://).</div>
            </div>
        `;
        socialFields.appendChild(newField);
        socialCount++;
        document.getElementById('addSocialBtn').disabled = socialCount >= maxSocialItems;
        updateProgress();
    }

    // Add new gallery field
    function addGalleryField() {
        if (galleryCount >= maxGalleryItems) {
            document.getElementById('addGalleryBtn').disabled = true;
            showNotification('info', `Maximum ${maxGalleryItems} images allowed`);
            return;
        }

        const galleryFields = document.getElementById('galleryFields');
        const existingFields = galleryFields.querySelectorAll('.form-item');
        if (existingFields.length >= maxGalleryItems) {
            document.getElementById('addGalleryBtn').disabled = true;
            showNotification('info', `Maximum ${maxGalleryItems} images allowed`);
            return;
        }

        const newIndex = existingFields.length;
        const newField = document.createElement('div');
        newField.className = 'col-md-6 col-lg-4 form-item';
        newField.setAttribute('data-index', newIndex);
        newField.innerHTML = `
            <div class="gallery-item">
                <button type="button" class="remove-btn" onclick="removeField(this, 'gallery')" aria-label="Remove image">
                    <i class="bi bi-x-circle"></i>
                </button>
                <label class="form-label mb-1">Image Title</label>
                <input type="text" class="form-control mb-2" name="_ait-item_item-data[gallery][${newIndex}][title]" placeholder="Image title">
                <img class="gallery-preview" alt="Gallery preview" style="display:none;max-width:100px;max-height:100px;">
                <input type="file" class="form-control" name="_ait-item_item-data[gallery][${newIndex}][image]" accept="image/png,image/jpeg" onchange="previewImage(this)">
                <input type="hidden" name="_ait-item_item-data[gallery][${newIndex}][image_id]" value="">
                <p class="form-note">JPG/PNG, < 500KB</p>
            </div>
        `;
        galleryFields.appendChild(newField);
        galleryCount++;
        document.getElementById('addGalleryBtn').disabled = galleryCount >= maxGalleryItems;
        updateProgress();
    }

    // Remove field
    function removeField(button, type) {
        const field = button.closest('.form-item');
        if (type === 'phone') {
            phoneCount--;
            document.getElementById('addPhoneBtn').disabled = phoneCount >= maxPhoneItems;
        } else if (type === 'service') {
            serviceCount--;
            document.getElementById('addServiceBtn').disabled = serviceCount >= maxServiceItems;
        } else if (type === 'social') {
            socialCount--;
            document.getElementById('addSocialBtn').disabled = socialCount >= maxSocialItems;
        } else if (type === 'gallery') {
            galleryCount--;
            document.getElementById('addGalleryBtn').disabled = galleryCount >= maxGalleryItems;
        }
        field.remove();
        updateIndices(type);
        updateProgress();
    }

    // Update indices for dynamic fields
    function updateIndices(type) {
        let fields, prefix, indexProp;
        if (type === 'phone') {
            fields = document.querySelectorAll('#contactFields .form-item');
            prefix = '_ait-item_item-data[telephoneAdditional]';
            indexProp = 'number';
        } else if (type === 'service') {
            fields = document.querySelectorAll('#companyServices .form-item');
            prefix = '_ait-item_item-data[companyServices]';
        } else if (type === 'social') {
            fields = document.querySelectorAll('#socialFields .form-item');
            prefix = '_ait-item_item-data[socialIcons]';
        } else if (type === 'gallery') {
            fields = document.querySelectorAll('#galleryFields .form-item');
            prefix = '_ait-item_item-data[gallery]';
        } else {
            return;
        }

        fields.forEach((field, index) => {
            field.setAttribute('data-index', index);
            if (type === 'phone') {
                const label = field.querySelector('label');
                const input = field.querySelector('input');
                if (index > 0) {
                    label.textContent = `Phone ${index + 1}`;
                    label.setAttribute('for', `${prefix}[${index}][${indexProp}]`);
                    input.id = `${prefix}[${index}][${indexProp}]`;
                    input.name = `${prefix}[${index}][${indexProp}]`;
                }
            } else if (type === 'service') {
                const inputs = field.querySelectorAll('input, textarea');
                const labels = field.querySelectorAll('label');
                const img = field.querySelector('img');
                inputs.forEach(input => {
                    if (input.name.includes('[services]')) {
                        input.name = `${prefix}[${index}][services]`;
                        input.id = `${prefix}[${index}][services]`;
                        labels[0].setAttribute('for', input.id);
                    } else if (input.name.includes('[servicesDesc]')) {
                        input.name = `${prefix}[${index}][servicesDesc]`;
                        input.id = `${prefix}[${index}][servicesDesc]`;
                        labels[1].setAttribute('for', input.id);
                    } else if (input.name.includes('[servicesPrice]')) {
                        input.name = `${prefix}[${index}][servicesPrice]`;
                        input.id = `${prefix}[${index}][servicesPrice]`;
                        labels[2].setAttribute('for', input.id);
                    } else if (input.name.includes('[servicesImg]')) {
                        input.name = `${prefix}[${index}][servicesImg]`;
                        input.id = `${prefix}[${index}][servicesImg]`;
                        labels[3].setAttribute('for', input.id);
                    } else if (input.name.includes('[servicesImgId]')) {
                        input.name = `${prefix}[${index}][servicesImgId]`;
                    }
                });
                if (img) img.alt = `Service preview ${index + 1}`;
            } else if (type === 'social') {
                const select = field.querySelector('select');
                const input = field.querySelector('input');
                select.name = `${prefix}[${index}][icon]`;
                input.name = `${prefix}[${index}][link]`;
            } else if (type === 'gallery') {
                const inputTitle = field.querySelector('input[type="text"]');
                const inputFile = field.querySelector('input[type="file"]');
                const inputHidden = field.querySelector('input[type="hidden"]');
                const img = field.querySelector('img');
                inputTitle.name = `${prefix}[${index}][title]`;
                inputFile.name = `${prefix}[${index}][image]`;
                inputHidden.name = `${prefix}[${index}][image_id]`;
                if (img) img.alt = `Gallery preview ${index + 1}`;
            }
        });
    }

    // Image preview functions
    function previewImage(input) {
        const preview = input.previousElementSibling;
        if (input.files && input.files[0]) {
            if (input.files[0].size > 500000) {
                showNotification('error', 'File size must be less than 500KB');
                input.value = '';
                preview.src = '';
                preview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewLogo(input) {
        const preview = document.getElementById('logoPreview');
        if (input.files && input.files[0]) {
            if (input.files[0].size > 500000) {
                showNotification('error', 'File size must be less than 500KB');
                input.value = '';
                preview.src = '';
                preview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Notification system
    function showNotification(type, message, duration = 5000) {
        const container = document.getElementById('notificationContainer');
        const notification = document.createElement('div');
        const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';

        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <i class="bi ${icon} icon"></i>
            <div>${message}</div>
            <button class="close-btn" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        `;

        container.appendChild(notification);
        setTimeout(() => notification.classList.add('show'), 10);

        const removalTimer = setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, duration);

        notification.addEventListener('mouseenter', () => clearTimeout(removalTimer));
        notification.addEventListener('mouseleave', () => {
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, duration);
        });
    }

    // Form submission handler
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('businessRegistrationForm');
        form.addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                showNotification('error', 'Please fill in all required fields correctly.');
            }
            this.classList.add('was-validated');

            const submitBtn = form.querySelector('button[type="submit"]');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');

            submitText.textContent = 'Processing...';
            submitSpinner.classList.remove('d-none');
            submitBtn.disabled = true;

            // Re-enable button after a short delay to prevent double submission
            setTimeout(() => {
                submitText.textContent = '<?php echo $is_editing ? 'Update Business' : 'Submit Business'; ?>';
                submitSpinner.classList.add('d-none');
                submitBtn.disabled = false;
            }, 1500);
        });

        // Initialize button states
        document.getElementById('addPhoneBtn').disabled = phoneCount >= maxPhoneItems;
        document.getElementById('addServiceBtn').disabled = serviceCount >= maxServiceItems;
        document.getElementById('addSocialBtn').disabled = socialCount >= maxSocialItems;
        document.getElementById('addGalleryBtn').disabled = galleryCount >= maxGalleryItems;

        // Update progress on input change
        document.querySelectorAll('input, select, textarea').forEach(element => {
            element.addEventListener('input', updateProgress);
            element.addEventListener('change', updateProgress);
        });

        updateProgress();
    });
</script>

<?php get_footer(); ?>