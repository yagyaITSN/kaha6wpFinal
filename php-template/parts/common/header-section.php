<section class="contact-header text-center py-5">
   <div class="container">
      <?php
      $page = basename($_SERVER['REQUEST_URI']);
      if ($page == 'page-about.php'): ?>
         <h1 class="fs-1 fw-bold">About Kaha6</h1>
         <p class="lead text-muted fs-6">Nepal's Premier Business Directory Platform</p>
      <?php elseif ($page == 'page-privacy-policy.php'): ?>
         <h1 class="fs-1 fw-bold">Privacy Policy</h1>
         <p class="lead text-muted fs-6">Last updated: January 2025</p>
      <?php elseif ($page == 'page-contact.php'): ?>
         <h1 class="fs-1 fw-bold">Contact Kaha6</h1>
         <p class="lead text-muted fs-6">Best Directory solutions in Nepal</p>
      <?php elseif ($page == 'page-faq.php'): ?>
         <h1 class="fs-1 fw-bold">Frequently Asked Questions</h1>
         <p class="lead text-muted fs-6">Find answers to common questions about Kaha6 Business Directory</p>
      <?php elseif ($page == 'search.php'): ?>
         <h1 class="fs-1 fw-bold">Search Reults for "IT Service Nepal"</h1>
         <p class="lead text-muted fs-6">Find answers to common questions about Kaha6 Business Directory</p>
      <?php elseif ($page == 'login.php'): ?>
         <h1 class="fs-1 fw-bold">Join Kaha6 Business Directory</h1>
         <p class="lead text-muted fs-6">Manage your business listings and connect with customers</p>
      <?php elseif ($page == 'page-register.php'): ?>
         <h1 class="fs-1 fw-bold">Register to Submit your Business</h1>
         <p class="lead text-muted fs-6">Join Nepal's premier business directory and reach more customers</p>
      <?php endif; ?>
   </div>
</section>