 <?php
    if (!is_user_logged_in()) {
        $contrast = "itsn-contrast";
    }

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $gravatar_url = get_avatar_url($user_id, array('size' => 96));
    $profile_photo_id = get_user_meta($user_id, 'profile_photo', true);
    $profile_photo_url = $profile_photo_id ? wp_get_attachment_url($profile_photo_id) : '';
    $default_image = get_template_directory_uri() . '/assets/images/profile.png';
    $has_gravatar = false;
    if (!empty($current_user->user_email)) {
        $hash = md5(strtolower(trim($current_user->user_email)));
        $uri = 'https://www.gravatar.com/avatar/' . $hash . '?d=404';
        $headers = @get_headers($uri);
        $has_gravatar = (is_array($headers) && strpos($headers[0], '200') !== false);
    }

    if ($has_gravatar) {
        $image_url = $gravatar_url;
    } elseif ($profile_photo_url) {
        $image_url = $profile_photo_url;
    } else {
        $image_url = $default_image;
    }
    ?>
 <?php if (is_user_logged_in()): ?>
     <img class="dropdown-toggle itsn-avatar <?php echo $contrast; ?> rounded-circle pe-auto" height="35" src="<?php echo esc_url($image_url); ?>" alt="Avatar" data-bs-toggle="dropdown" style="cursor: pointer; border:2px solid #C1272D;">
 <?php else: ?>
     <i class="fas fa-user-circle fa-2x dropdown-toggle" role="button" data-bs-toggle="dropdown"
         aria-expanded="false" style="cursor: pointer;"></i>
 <?php endif; ?>
 <ul class="dropdown-menu dropdown-menu-end">
     <?php
        if (is_user_logged_in()) {
        ?>
         <?php if (current_user_can('administrator')): ?>

             <li>
                 <a class="dropdown-item" href="<?php echo esc_url(admin_url()); ?>">
                     <i class="fa-solid fa-screwdriver-wrench"></i> wp-admin
                 </a>
             </li>
         <?php endif; ?>

         <li>
             <a class="dropdown-item" href="<?php echo esc_url(home_url('/dashboard')); ?>">
                 <i class="fa-solid fa-square-poll-horizontal"></i> Dashboard
             </a>
         </li>

         <li>
             <a class="dropdown-item" href="<?php echo wp_logout_url(home_url('/')); ?>">
                 <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
             </a>
         </li>
     <?php
        } else {
        ?>
         <li>
             <a class="dropdown-item" href="<?php echo home_url('/login'); ?>">
                 <i class="fas fa-sign-in-alt me-2"></i> Login/Register
             </a>
         </li>
         <?php /* 
         <li>
             <a class="dropdown-item" href="<?php echo home_url('/register-user'); ?>">
                 <i class="fas fa-user-plus me-2"></i> Register
             </a>
         </li>
         */ ?>
     <?php
        }
        ?>
 </ul>