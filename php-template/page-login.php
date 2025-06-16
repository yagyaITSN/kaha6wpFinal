<?php include 'header.php'; ?>

<!-- Login/Register Header Section -->
<?php include 'parts/common/header-section.php' ?>
<!-- End Login/Register Header Section -->

<!-- Login/Register Form Section -->
<section class="container my-5">
    <div class="row row-cols-1 row-cols-lg-3 justi">
        <div class="col d-none d-lg-block">
            <!-- Banner 1 -->
            <?php include 'ads/login/lg/lg-ad-one.php' ?>
            <!-- End Banner 1 -->

            <!-- Banner 2 -->
            <?php include 'ads/login/lg/lg-ad-two.php' ?>

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

                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="loginUsername" class="form-label">Username/Email:</label>
                                <input type="text" class="form-control" id="loginUsername"
                                    placeholder="Enter your username or email" included>
                            </div>

                            <div class="mb-3 password-toggle">
                                <label for="loginPassword" class="form-label">Password:</label>
                                <input type="password" class="form-control" id="loginPassword"
                                    placeholder="Enter your password" included>
                                <i class="bi bi-eye-slash password-toggle-icon" id="toggleLoginPassword"></i>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>

                            <button type="submit" class="btn btn-custom-red login-register-btn">Login</button>

                            <div class="text-center mt-3">
                                <a href="/forgot-password" class="text-decoration-none">Lost Your Password?</a>
                            </div>
                        </form>
                    </div>

                    <!-- Register Tab -->
                    <div class="tab-pane fade login-register-content p-4" id="register" role="tabpanel">
                        <h2 class="fs-2 fw-bold text-center login-register-title my-5">Create Account</h2>
                        <form id="registerForm">
                            <div class="mb-3">
                                <label for="registerUsername" class="form-label">Username:</label>
                                <input type="text" class="form-control" id="registerUsername" placeholder="Enter username"
                                    included>
                            </div>

                            <div class="mb-3">
                                <label for="registerEmail" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="registerEmail" placeholder="Enter your email"
                                    included>
                            </div>

                            <div class="mb-3 password-toggle">
                                <label for="registerPassword" class="form-label">Password:</label>
                                <input type="password" class="form-control" id="registerPassword"
                                    placeholder="Enter your password" included>
                                <i class="bi bi-eye-slash password-toggle-icon" id="toggleRegisterPassword"></i>
                            </div>

                            <div class="mb-3 password-toggle">
                                <label for="registerPasswordConfirm" class="form-label">Repeat Password:</label>
                                <input type="password" class="form-control" id="registerPasswordConfirm"
                                    placeholder="Repeat your password" included>
                                <i class="bi bi-eye-slash password-toggle-icon" id="toggleRegisterPasswordConfirm"></i>
                            </div>

                            <button type="submit" class="btn btn-custom-red login-register-btn">Register</button>

                            <div class="divider text-muted">or</div>

                            <div class="switch-login-register mt-3 text-center">
                                <p>Already have an account? <a href="#" class="d-inline-block text-decoration-none fw-bold"
                                        id="switchToLogin">Login Here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Main Content -->

        <div class="col d-none d-lg-block">

            <!-- Banner 3 -->
            <?php include 'ads/login/lg/lg-ad-three.php' ?>
            <!-- End Banner 3 -->

            <!-- Banner 4 -->
            <?php include 'ads/login/lg/lg-ad-four.php' ?>
            <!-- End Banner 4 -->

        </div>
    </div>
</section>
<!-- End Login/Register Form Section -->

<!-- Banner -->
<?php include 'ads/login/commom/common-ad-one.php' ?>
<!-- End Banner -->

<?php include 'footer.php'; ?>