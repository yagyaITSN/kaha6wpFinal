<section class="contact-header text-center py-5">
   <div class="container">
      <?php if (is_page('about')): ?>
         <h1 class="fs-1 fw-bold">About Kaha6</h1>
         <p class="lead text-muted fs-6">Nepal's Premier Business Directory Platform</p>
      <?php elseif (is_page('privacy-policy')): ?>
         <h1 class="fs-1 fw-bold">Privacy Policy</h1>
         <p class="lead text-muted fs-6">Last updated: January 2025</p>
      <?php elseif (is_page('contact')): ?>
         <h1 class="fs-1 fw-bold">Contact Kaha6</h1>
         <p class="lead text-muted fs-6">Best Directory solutions in Nepal</p>
      <?php elseif (is_page('faq')): ?>
         <h1 class="fs-1 fw-bold">Frequently Asked Questions</h1>
         <p class="lead text-muted fs-6">Find answers to common questions about Kaha6 Business Directory</p>
      <?php elseif (is_page('login')): ?>
         <h1 class="fs-1 fw-bold">Join Kaha6 Business Directory</h1>
         <p class="lead text-muted fs-6">Manage your business listings and connect with customers</p>
      <?php elseif (is_page('register-to-submit-your-company')): ?>
         <h1 class="fs-1 fw-bold">Register to Submit your Business</h1>
         <p class="lead text-muted fs-6">Join Nepal's premier business directory and reach more customers</p>
      <?php endif; ?>
   </div>
</section>