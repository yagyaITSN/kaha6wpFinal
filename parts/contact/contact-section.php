<?php
$error_message = '';
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_contact_form') {
   $name = isset($_POST['fname']) ? sanitize_text_field($_POST['fname']) : '';
   $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
   $message = isset($_POST['msg']) ? sanitize_textarea_field($_POST['msg']) : '';

   if (empty($name) || empty($email) || empty($message)) {
      $error_message = 'All fields are required.';
   } else {
      // Prepare email
      $to = 'info.kaha6@gmail.com';
      $subject = 'Mail From Kaha6.com';
      $body = "Name: $name\nEmail: $email\nMessage:\n$message";
      $headers = array(
         'Content-Type: text/plain; charset=UTF-8',
         "From: $name <$email>",
      );

      $mail_sent = wp_mail($to, $subject, $body, $headers);

      if ($mail_sent) {
         $success_message = 'Message sent successfully!';
         // Clear form fields after successful submission
         $_POST['fname'] = '';
         $_POST['email'] = '';
         $_POST['msg'] = '';
      } else {
         $error_message = 'Failed to send email. Please check server email settings or try again later.';
      }
   }
}

?>

<section class="container">
   <div class="row">
      <div class="col-lg-6 mb-5 mb-lg-0">
         <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3">Get In Touch</h2>
         <p>Collaboratively administrate channels whereas virtual. Objectively seize scalable
            metrics whereas proactive e-services.</p>
         <!-- Contact Form -->
         <div class="contact-form mt-5">
            <div class="row px-3">
               <?php if (!empty($error_message)) : ?>
                  <div class="col-12 alert alert-danger mb-4"><?php echo esc_html($error_message); ?></div>
               <?php endif; ?>
               <?php if (!empty($success_message)) : ?>
                  <div class="col-12 alert alert-success mb-4"><?php echo esc_html($success_message); ?></div>
               <?php endif; ?>
            </div>
            <form id="contact-form" method="post" action="">
               <div class="mb-3">
                  <label for="fname" class="form-label">Name *</label>
                  <input type="text" name="fname" class="form-control" id="fname" value="<?php echo isset($_POST['fname']) ? esc_attr($_POST['fname']) : ''; ?>" included required>
               </div>
               <div class="mb-3">
                  <label for="email" class="form-label">Email address *</label>
                  <input type="email" name="email" class="form-control" id="email" value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>" included required>
               </div>
               <div class="mb-3">
                  <label for="msg" class="form-label">Message *</label>
                  <textarea class="form-control" name="msg" id="msg" rows="5" included required><?php echo isset($_POST['msg']) ? esc_textarea($_POST['msg']) : ''; ?></textarea>
               </div>
               <button type="submit" class="btn btn-custom-red submit-btn" id="submit-btn">Send Message</button>
               <input type="hidden" name="action" value="submit_contact_form">
            </form>
         </div>
      </div>
      <div class="col-lg-6">
         <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3">Our Information</h2>
         <!-- Contact Cards -->
         <div class="row mt-5">
            <div class="col-md-6 mb-3">
               <div class="contact-card card h-100 text-center p-4">
                  <div class="contact-icon">
                     <i class="bi bi-geo-alt-fill"></i>
                  </div>
                  <h4>Address</h4>
                  <p><?php echo esc_html(get_theme_mod('setting_site_details3')); ?></p>
               </div>
            </div>
            <div class="col-md-6 mb-3">
               <div class="contact-card card h-100 text-center p-4">
                  <div class="contact-icon">
                     <i class="bi bi-telephone-fill"></i>
                  </div>
                  <h4>Phone</h4>
                  <p><?php echo esc_html(get_theme_mod('setting_site_details4')); ?></p>
               </div>
            </div>
            <div class="col-md-6 mb-3">
               <div class="contact-card card h-100 text-center p-4">
                  <div class="contact-icon">
                     <i class="bi bi-envelope-fill"></i>
                  </div>
                  <h4>Email</h4>
                  <p><?php echo esc_html(get_theme_mod('setting_site_details5')); ?></p>
               </div>
            </div>
            <div class="col-md-6 mb-3">
               <div class="contact-card card h-100 text-center p-4">
                  <div class="contact-icon">
                     <i class="bi bi-clock-fill"></i>
                  </div>
                  <h4>Hours</h4>
                  <p>Sunday-Friday<br>10:00 AM - 6:00 PM</p>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
   document.addEventListener('DOMContentLoaded', function() {
      // Disable button on click to prevent multiple submissions
      const form = document.getElementById('contact-form');
      const submitBtn = document.getElementById('submit-btn');

      form.addEventListener('submit', function() {
         submitBtn.disabled = true;
         submitBtn.textContent = 'Sending...';
      });
   });
</script>