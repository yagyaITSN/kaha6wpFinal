<!-- Sidebar -->
<div class="col-lg-3 border-end d-none d-lg-block">
    <div class="p-md-4">
        <div class="nav flex-column nav-pills">
            <a class="nav-link <?php echo (is_page('dashboard') ? 'active' : ''); ?>" href="<?php echo home_url('/dashboard'); ?>"><i class="fa-solid fa-square-poll-horizontal">&nbsp;&nbsp;</i>Dashboard
                Info</a>
            <a class="nav-link <?php echo (is_page('register-to-submit-your-company') ? 'active' : ''); ?>" href="<?php echo home_url('/register-to-submit-your-company'); ?>"><i class="fa-solid fa-plus"></i>&nbsp;&nbsp;List Company</a>
            <a class="nav-link <?php echo (is_page('dashboard-allcompany') ? 'active' : ''); ?>" href="<?php echo home_url('/dashboard-allcompany'); ?>"><i class="fa-solid fa-table-list"></i>&nbsp;&nbsp;My Listing</a>
            <a class="nav-link <?php echo (is_page('ads-management') ? 'active' : ''); ?>" href="<?php echo home_url('/ads-management'); ?>"><i class="fa-solid fa-rectangle-ad"></i>&nbsp;&nbsp;Ad Management</a>
            <a class="nav-link <?php echo (is_page('dashboard-profile') ? 'active' : ''); ?>" href="<?php echo home_url('/dashboard-profile'); ?>"><i class="fa-solid fa-user"></i>&nbsp;&nbsp;My Profile</a>
            <a class="nav-link" href="<?php echo wp_logout_url(home_url('/')); ?>"><i
                    class="fa-solid fa-arrow-right-from-bracket"></i>&nbsp;&nbsp;Logout</a>
        </div>
    </div>
</div>

<div class="accordion d-block d-lg-none" id="offcanvas_menu">
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Dashboard Navigation
            </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#offcanvas_menu">
            <div class="accordion-body">
                <div class="p-md-4">
                    <div class="nav flex-column nav-pills">
                        <a class="nav-link <?php echo (is_page('dashboard') ? 'active' : ''); ?>" href="<?php echo home_url('/dashboard'); ?>"><i class="fa-solid fa-square-poll-horizontal">&nbsp;&nbsp;</i>Dashboard
                            Info</a>
                        <a class="nav-link <?php echo (is_page('register-to-submit-your-company') ? 'active' : ''); ?>" href="<?php echo home_url('/register-to-submit-your-company'); ?>"><i class="fa-solid fa-plus"></i>&nbsp;&nbsp;List Company</a>
                        <a class="nav-link <?php echo (is_page('dashboard-allcompany') ? 'active' : ''); ?>" href="<?php echo home_url('/dashboard-allcompany'); ?>"><i class="fa-solid fa-table-list"></i>&nbsp;&nbsp;My Listing</a>
                        <a class="nav-link <?php echo (is_page('ads-management') ? 'active' : ''); ?>" href="<?php echo home_url('/ads-management'); ?>"><i class="fa-solid fa-rectangle-ad"></i>&nbsp;&nbsp;Ad Management</a>
                        <a class="nav-link <?php echo (is_page('dashboard-profile') ? 'active' : ''); ?>" href="<?php echo home_url('/dashboard-profile'); ?>"><i class="fa-solid fa-user"></i>&nbsp;&nbsp;My Profile</a>
                        <a class="nav-link" href="<?php echo wp_logout_url(home_url('/')); ?>"><i
                                class="fa-solid fa-arrow-right-from-bracket"></i>&nbsp;&nbsp;Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>