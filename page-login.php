<?php get_header(); ?>

<!-- Login/Register Header Section -->
<?php include 'parts/common/header-section.php' ?>
<!-- End Login/Register Header Section -->

<!-- Login/Register Form Section -->
<section class="container my-5">
    <div class="row row-cols-1 row-cols-lg-3 justi">
        <div class="col d-none d-lg-block">
            <!-- Banner 1 -->
            <?php get_template_part('ads/login/lg/lg', 'ad-one'); ?>
            <!-- End Banner 1 -->

            <!-- Banner 2 -->
            <?php get_template_part('ads/login/lg/lg', 'ad-two'); ?>

            <!-- End Banner 2 -->

        </div>

        <!-- Main Content -->
        <div class="col">
            <div class="login-register-container shadow-sm border-0 text-decoration-none rounded-4">
                <!-- Tabs Navigation -->
                <ul class="nav login-register-tabs" id="authTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="btn btn-custom-red active" id="login-tab" data-bs-toggle="tab"
                            data-bs-target="#login" type="button" role="tab">Login</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="btn btn-custom-red" id="register-tab" data-bs-toggle="tab" data-bs-target="#register"
                            type="button" role="tab">Register</button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="authTabsContent">
                    <!-- Login Tab -->
                    <div class="tab-pane fade show active login-register-content p-4" id="login" role="tabpanel">
                        <h2 class="fs-2 fw-bold text-center login-register-title my-5">Welcome Back</h2>

                        <?php if (isset($_GET['login'])) : ?>
                            <div class="login-error-message alert alert-<?php echo $_GET['login'] === 'false' ? 'success' : 'danger'; ?> mb-3">
                                <?php
                                switch ($_GET['login']) {
                                    case 'failed':
                                        echo '⚠️ Invalid username or password.';
                                        break;
                                    case 'empty':
                                        echo '⚠️ Please enter both username and password.';
                                        break;
                                    case 'false':
                                        echo '✅ You have logged out successfully.';
                                        break;
                                    default:
                                        echo '⚠️ An unknown error occurred.';
                                        break;
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        <form id="loginForm" action="<?php echo esc_url(wp_login_url()); ?>" method="post">
                            <div class="mb-3">
                                <label for="loginUsername" class="form-label">Username/Email:</label>
                                <input type="text" class="form-control" name="log" id="loginUsername" value="<?php echo isset($_GET['user']) ? esc_attr($_GET['user']) : ''; ?>"
                                    placeholder="Enter your username or email" required autofocus included>
                            </div>

                            <div class="mb-3 password-toggle">
                                <label for="pwd" class="form-label">Password:</label>
                                <input type="password" name="pwd" class="form-control" id="pwd"
                                    placeholder="Enter your password" included>
                                <i class="bi bi-eye-slash password-toggle-icon" id="toggleLoginPassword"></i>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="rememberme" value="forever">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>

                            <button type="submit" class="btn btn-custom-red login-register-btn" name="wp-submit">Login</button>
                            <?php echo do_shortcode('[google_login]'); ?>
                            <div class="text-center mt-3">
                                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="text-decoration-none">Lost Your Password?</a>
                            </div>
                            <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url()); ?>">

                        </form>
                    </div>

                    <!-- Register Tab -->
                    <div class="tab-pane fade login-register-content p-4" id="register" role="tabpanel">
                        <h2 class="fs-2 fw-bold text-center login-register-title my-5">Create Account</h2>
                        <?php
                        // Check if we are in the verification step
                        if (isset($_GET['verify']) && $_GET['verify'] === 'code' && isset($_SESSION['registration_data'])) {
                            // Verification Form
                        ?>
                            <form method="POST" action="">
                                <?php
                                if (isset($GLOBALS['verification_errors']) && !empty($GLOBALS['verification_errors'])) {
                                    foreach ($GLOBALS['verification_errors'] as $error) {
                                        echo '<div class="alert alert-danger mb-3">';
                                        echo '⚠️ ' . esc_html($error);
                                        echo '</div>';
                                    }
                                }
                                ?>
                                <div class="mb-3">
                                    <label for="verification_code" class="form-label">Enter 6-Digit Code:</label>
                                    <input type="text" class="form-control" id="verification_code" name="verification_code" maxlength="6" required autofocus>
                                </div>
                                <?php wp_nonce_field('verify_code_nonce', 'verify_code_nonce'); ?>
                                <?php
                                // Show Verify Code button unless code has expired
                                if (!isset($GLOBALS['verification_errors']) || !in_array('Verification code has expired. Please resend the code.', $GLOBALS['verification_errors'])) {
                                ?>
                                    <button type="submit" name="verify_code" id="verify_button" class="btn btn-custom-red">Verify Code</button>
                                <?php
                                }
                                ?>
                            </form>
                            <?php
                            // Separate form for Resend Code, shown only when code has expired
                            if (isset($GLOBALS['verification_errors']) && in_array('Verification code has expired. Please resend the code.', $GLOBALS['verification_errors'])) {
                            ?>
                                <form method="POST" action="">
                                    <?php wp_nonce_field('verify_code_nonce', 'verify_code_nonce'); ?>
                                    <button type="submit" name="resend_code" id="resend_button" class="btn btn-custom-red">Resend Code</button>
                                </form>
                            <?php
                            }
                            ?>
                        <?php
                        } else {
                            // Registration Form
                        ?>
                            <form id="registerForm" method="POST" action="">
                                <?php
                                if (isset($GLOBALS['registration_errors']) && !empty($GLOBALS['registration_errors'])) {
                                    foreach ($GLOBALS['registration_errors'] as $error) {
                                        echo '<div class="alert alert-danger mb-3">';
                                        echo '⚠️ ' . esc_html($error);
                                        echo '</div>';
                                    }
                                }
                                ?>
                                <div class="mb-3">
                                    <label for="uname" class="form-label">Username:</label>
                                    <input type="text" class="form-control" id="uname" name="uname" value="<?php echo isset($_POST['uname']) ? esc_attr($_POST['uname']) : ''; ?>" placeholder="Enter username"
                                        required autofocus included>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>" placeholder="Enter your email"
                                        required included>
                                </div>

                                <div class="mb-3 password-toggle">
                                    <label for="password" class="form-label">Password:</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter your password" required included>
                                    <i class="bi bi-eye-slash password-toggle-icon" id="toggleRegisterPassword"></i>
                                </div>

                                <div class="mb-3 password-toggle">
                                    <label for="repeat_password" class="form-label">Repeat Password:</label>
                                    <input type="password" class="form-control" id="repeat_password" name="repeat_password"
                                        placeholder="Repeat your password" required included>
                                    <i class="bi bi-eye-slash password-toggle-icon" id="toggleRegisterPasswordConfirm"></i>
                                </div>

                                <?php wp_nonce_field('custom_register_nonce', 'custom_register_nonce'); ?>
                                <button type="submit" name="custom_register" class="btn btn-custom-red login-register-btn">Register</button>

                                <?php echo do_shortcode('[google_login]'); ?>

                                <div class="divider text-muted">or</div>

                                <div class="switch-login-register mt-3 text-center">
                                    <p>Already have an account? <a href="#" class="d-inline-block text-decoration-none fw-bold" id="switchToLogin">Login Here</a></p>
                                </div>
                            </form>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Main Content -->

        <div class="col d-none d-lg-block">

            <!-- Banner 3 -->
            <?php get_template_part('ads/login/lg/lg ', 'ad-three'); ?>
            <!-- End Banner 3 -->

            <!-- Banner 4 -->
            <?php get_template_part('ads/login/lg/lg ', 'ad-four'); ?>
            <!-- End Banner 4 -->

        </div>
    </div>
</section>
<!-- End Login/Register Form Section -->

<!-- Banner -->
<?php get_template_part('ads/login/commom/common', 'ad-one'); ?>
<!-- End Banner -->

<?php get_footer(); ?>