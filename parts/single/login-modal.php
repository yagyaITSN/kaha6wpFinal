    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
             <div class="modal-header">
                <p class="modal-title text-capitalize fs-6" id="loginModalLabel">Choose your preferred
                   login
                   method</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                <div class="d-flex flex-column">
                   <a class="btn btn-custom-red" href="<?php echo home_url('/login') ?>">
                      <i class="fas fa-sign-in-alt me-2"></i>Login
                   </a>
                </div>
                <?php echo do_shortcode('[google_login]'); ?>
             </div>
          </div>
       </div>
    </div>