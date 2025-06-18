<?php get_header(); ?>

<section class="container py-5">
	<div class="col-12">
		<div class="card border-0 itsn-shadow">
			<div class="card-body p-0">
				<div class="row g-0">

					<!-- Business Details Start -->
					<?php get_template_part('parts/dashboard/pg', 'offcanvas'); ?>
					<!-- Business Details End -->

					<!-- Content Area -->
					<div class="col-lg-9 p-4 mb-">
						<div class="py-4 d-md-flex justify-content-md-between align-items-md-center">
							<h3 class="fw-bold">My Listing</h3>
							<div>
								<nav aria-label="breadcrumb">
									<ol class="breadcrumb">
										<li class="breadcrumb-item"><a href="<?php echo home_url('/dashboard'); ?>">Dashboard</a></li>
										<li class="breadcrumb-item active" aria-current="page">My Listing</li>
									</ol>
								</nav>
							</div>
						</div>

						<!-- Tabs Start -->
						<?php get_template_part('parts/dashboard/comp', 'tabs'); ?>
						<!-- Tabs End -->

					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer() ?>