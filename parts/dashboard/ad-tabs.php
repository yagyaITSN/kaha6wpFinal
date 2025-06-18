<div class="row row-cols-1 g-3 px-2 justify-content-center mb-5">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark active" id="add-ads" data-bs-toggle="tab" data-bs-target="#add-ads-pane" type="button"
                role="tab" aria-controls="add-ads-pane" aria-selected="true"><i class="fa-regular fa-square-plus"></i> Add your Ads</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="all-ads" data-bs-toggle="tab" data-bs-target="#all-ads-pane" type="button" role="tab" aria-controls="all-ads-pane" aria-selected="false"><i class="fa-solid fa-table-list"></i> Your Ads</button>
        </li>

    </ul>
    <div class="tab-content py-4" id="myTabContent">
        <!-- Add Ads Start -->
        <div class="tab-pane fade show active" id="add-ads-pane" role="tabpanel" aria-labelledby="add-ads" tabindex="0">
            <div class="row row-cols-1 g-3 px-2 justify-content-center mb-5">
                <?php get_template_part('parts/dashboard/ad', 'form');
                ?>
            </div>
        </div>
        <!-- Add Ads End -->

        <!-- All Start -->
        <div class="tab-pane fade" id="all-ads-pane" role="tabpanel" aria-labelledby="all-ads" tabindex="0">


            <?php
            // Ensure user is logged in
            if (!is_user_logged_in()) {
                echo '<div class="alert alert-warning">Please log in to view your ads.</div>';
                return;
            }

            global $wpdb;
            $user_id = get_current_user_id();
            $table_name = $wpdb->prefix . 'user_ads';

            // Update expired ads
            $result = $wpdb->query("UPDATE $table_name SET status = 'expired' WHERE expire_time < NOW() AND status = 'active'");
            if ($result === false) {
                error_log('Dashboard: Failed to update expired ads - ' . $wpdb->last_error);
            }

            // Fetch user ads
            $ads = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE author_id = %d ORDER BY ad_id DESC",
                $user_id
            ));
            ?>

            <div class="container">
                <h3 class="mb-4">Your Ads</h3>
                <?php if (empty($ads)) : ?>
                    <div class="alert alert-info">You have not added any ads yet.</div>
                <?php else : ?>
                    <?php foreach ($ads as $ad) : ?>
                        <div class="row px-2 justify-content-center mb-4">
                            <div class="col-12 itsn-shadow rounded py-3">
                                <div class="d-flex gap-2 justify-content-end mb-2">
                                    <div class="text-muted opacity-80 fs-7">
                                        <i class="fa-solid fa-eye text-success"></i> <?php echo esc_html($ad->displayed ?? 0); ?>
                                    </div>
                                    <div class="text-muted opacity-80 fs-7">
                                        <i class="fa-solid fa-hand-pointer text-success"></i> <?php echo esc_html($ad->clicks ?? 0); ?>
                                    </div>
                                    <?php if ($ad->status === 'active') : ?>
                                        <div class="d-flex justify-content-center">
                                            <p class="badge fw-normal bg-success border-0 fs-7 text-decoration-none mb-0">Active</p>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($ad->status === 'expired') : ?>
                                        <div class="d-flex justify-content-center">
                                            <p class="badge fw-normal bg-primary border-0 fs-7 text-decoration-none mb-0">Expired</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Display ad image -->
                                <div class="mb-3">
                                    <img class="img-fluid rounded" src="<?php echo esc_url($ad->image_path); ?>" alt="<?php echo esc_attr($ad->title); ?>">
                                </div>

                                <div class="row d-flex justify-content-between align-items-center">
                                    <div class="col-12 col-md-10">
                                        <h6 class="lh-base mb-1 fs-6"><?php echo esc_html($ad->title); ?></h6>
                                        <p class="text-dark lh-base fs-7 mb-0">
                                            Redirected to: <a class="text-primary" target="_blank" href="<?php echo esc_url($ad->redirect_url); ?>"><?php echo esc_html($ad->redirect_url ?: 'No URL'); ?></a>
                                        </p>
                                    </div>
                                    <div class="col-12 col-md-2 text-end justify-content-end d-flex">
                                        <?php if ($ad->status === 'expired') : ?>
                                            <a href="#" class="btn text-success border-0 reactivate-ad" data-ad-id="<?php echo esc_attr($ad->ad_id); ?>" data-nonce="<?php echo wp_create_nonce('reactivate_ad_' . $ad->ad_id); ?>" data-bs-toggle="tooltip" data-bs-title="Re-Activate">
                                                <i class="fa-solid fa-retweet"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="#" class="btn text-danger border-0 delete-ad" data-ad-id="<?php echo esc_attr($ad->ad_id); ?>" data-nonce="<?php echo wp_create_nonce('delete_ad_' . $ad->ad_id); ?>" data-bs-toggle="tooltip" data-bs-title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- JavaScript for AJAX and tooltips -->
            <script>
                jQuery(document).ready(function($) {
                    // Initialize Bootstrap tooltips
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });

                    // Handle reactivation
                    $('.reactivate-ad').on('click', function(e) {
                        e.preventDefault();
                        var adId = $(this).data('ad-id');
                        var nonce = $(this).data('nonce');
                        console.log('Reactivating ad_id: ' + adId); // Debug

                        if (confirm('Are you sure you want to reactivate this ad?')) {
                            $.ajax({
                                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                type: 'POST',
                                data: {
                                    action: 'reactivate_ad',
                                    ad_id: adId,
                                    nonce: nonce
                                },
                                success: function(response) {
                                    console.log('Reactivate response:', response); // Debug
                                    if (response.success) {
                                        alert(response.data.message);
                                        location.reload();
                                    } else {
                                        alert(response.data.message || 'An error occurred.');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Reactivate AJAX error:', status, error); // Debug
                                    alert('An error occurred. Please try again.');
                                }
                            });
                        }
                    });

                    // Handle deletion
                    $('.delete-ad').on('click', function(e) {
                        e.preventDefault();
                        var adId = $(this).data('ad-id');
                        var nonce = $(this).data('nonce');
                        console.log('Deleting ad_id: ' + adId); // Debug

                        if (confirm('Are you sure you want to delete this ad? This action cannot be undone.')) {
                            $.ajax({
                                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                type: 'POST',
                                data: {
                                    action: 'delete_ad',
                                    ad_id: adId,
                                    nonce: nonce
                                },
                                success: function(response) {
                                    console.log('Delete response:', response); // Debug
                                    if (response.success) {
                                        alert(response.data.message);
                                        location.reload();
                                    } else {
                                        alert(response.data.message || 'An error occurred.');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Delete AJAX error:', status, error); // Debug
                                    alert('An error occurred. Please try again.');
                                }
                            });
                        }
                    });
                });
            </script>

            <!-- All End -->
        </div>

    </div>