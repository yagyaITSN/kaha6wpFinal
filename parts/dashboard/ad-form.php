<?php
// Retrieve messages from transients
$error_message = get_transient('ad_form_error');
$success_message = get_transient('ad_form_success');
delete_transient('ad_form_error');
delete_transient('ad_form_success');

// Check ad count for disabling form
global $wpdb;
$user_id = get_current_user_id();
$ad_count = $user_id ? $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}user_ads WHERE author_id = %d AND status = 'active'",
    $user_id
)) : 0;
$disabled = ($ad_count >= get_option('max_ads_per_user', 1)) ? "disabled='disabled'" : '';
?>

<!-- Form Start -->
<div class="row px-3 px-md-0">
    <?php if (!empty($error_message)) : ?>
        <div class="col-sm-12 col-md-6 alert alert-danger mb-4"><?php echo esc_html($error_message); ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)) : ?>
        <div class="col-sm-12 col-md-6 alert alert-success mb-4"><?php echo esc_html($success_message); ?></div>
    <?php endif; ?>
</div>
<form method="post" action="" enctype="multipart/form-data">
    <fieldset <?php echo $disabled; ?>>
        <?php if ($ad_count >= get_option('max_ads_per_user', 1)) : ?>
            <div class="badge text-bg-danger mb-3 fs-7">You have reached the maximum number of active ads allowed.</div>
        <?php endif; ?>
        <!-- Image preview container -->
        <div class="mb-3 mb-md-4 mb-lg-5">
            <img id="adImagePreview" class="img-fluid" style="display: none; max-width: 100%;" alt="Ad Image Preview">
            <div id="noImageMessage" class="text-muted fs-7">No image selected</div>
        </div>
        <div class="mb-3">
            <label for="adImage" class="form-label text-dark">Ad Image <span class="text-primary">*</span></label>
            <input class="form-control" type="file" id="adImage" name="adImage" accept=".jpg, .jpeg, .png, .gif" required <?php echo $disabled; ?>>
            <span class="fs-7 opacity-50 text-dark">Please upload only .jpg, .jpeg, .png, and .gif, Max Ad size 500KB, 1200px x 150px recommended.</span>
        </div>
        <div class="mb-3">
            <label for="adTitle" class="form-label text-dark">Ad Title <span class="text-primary">*</span></label>
            <input class="form-control" type="text" id="adTitle" name="adTitle" required <?php echo $disabled; ?>>
        </div>
        <div class="mb-3">
            <label for="adURL" class="form-label text-dark">Ad URL</label>
            <input type="url" class="form-control" id="adURL" name="adURL" placeholder="Redirect your Ad to" <?php echo $disabled; ?>>
        </div>
        <?php wp_nonce_field('ad_form_submission', 'ad_form_nonce'); ?>
        <input type="hidden" name="action" value="submit_ad_form">
        <button type="submit" class="btn btn-primary bg-primary border-0" <?php echo $disabled; ?>>Submit Ad</button>
    </fieldset>
</form>
<!-- Form End -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const adImageInput = document.getElementById('adImage');
        const adImagePreview = document.getElementById('adImagePreview');
        const noImageMessage = document.getElementById('noImageMessage');

        adImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please upload a valid image file (.jpg, .jpeg, .png, .gif).');
                    this.value = ''; // Clear the input
                    adImagePreview.style.display = 'none';
                    noImageMessage.style.display = 'block';
                    return;
                }

                // Validate file size (500KB)
                if (file.size > 500 * 1024) {
                    alert('File size exceeds 500KB limit.');
                    this.value = ''; // Clear the input
                    adImagePreview.style.display = 'none';
                    noImageMessage.style.display = 'block';
                    return;
                }

                // Display preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    adImagePreview.src = e.target.result;
                    adImagePreview.style.display = 'block';
                    noImageMessage.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                adImagePreview.style.display = 'none';
                noImageMessage.style.display = 'block';
            }
        });
    });
</script>