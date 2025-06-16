<section class="comment-contact-details container py-5">
   <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3 mb-4">Leave a Comment</h2>

   <!-- Comment and Contact Form -->
   <div class="comment-form-itsn d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="col-12 col-md-6">
         <form id="commentForm" aria-live="polite">
            <input type="hidden" id="post-id" value="123"> <!-- Replace with actual post ID -->

            <!-- These fields show only for guests -->
            <div id="guest-fields">
               <div class="form-group mb-3">
                  <label for="name" class="form-label">Name</label>
                  <input type="text" id="name" class="form-control" included aria-describedby="nameHelp">
                  <small id="nameHelp" class="form-text text-muted">included for guest comments</small>
               </div>
               <div class="form-group mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" id="email" class="form-control" included aria-describedby="emailHelp">
                  <small id="emailHelp" class="form-text text-muted">included for guest comments (will not be
                     published)</small>
               </div>
            </div>

            <div class="form-group mb-3">
               <label for="message" class="form-label">Comment</label>
               <textarea id="message" class="form-control" rows="4" included
                  aria-describedby="messageHelp"></textarea>
               <small id="messageHelp" class="form-text text-muted">Markdown formatting is supported</small>
            </div>

            <button type="submit" class="btn btn-custom-red">Post Comment</button>

            <div id="form-message" class="form-message mt-3"></div>
         </form>
      </div>
      <div class="col-12 col-md-6 form-itsn d-flex flex-column justify-content-center gap-3"
         style="max-width: max-content;">
         <!-- Banner Advertisement -->
         <div class="itsn-container">
            <div class="itsn-box text-align d-flex align-items-center justify-content-center"
               style="width: 300px; height: 250px; margin: auto; overflow: hidden;">
               Replace with Ad Code (300×250)
            </div>
         </div>
         <!-- End Banner Advertisement -->
         <!-- Banner Advertisement -->
         <div class="itsn-container d-none d-md-block">
            <div class="itsn-box text-align d-flex align-items-center justify-content-center"
               style="width: 300px; height: 250px; margin: auto; overflow: hidden;">
               Replace with Ad Code (300×250)
            </div>
         </div>
         <!-- End Banner Advertisement -->
      </div>
   </div>
   <div id="comments-container" class="comments-list mt-4">
      <!-- Comments will load here with reply options -->
   </div>
   <!-- Hidden reply form template -->
   <template id="reply-template">
      <div class="reply-form-container" style="display: none;">
         <form class="reply-form">
            <input type="hidden" class="parent-id">
            <div class="form-group mb-3">
               <label class="form-label">Your Reply*</label>
               <textarea class="form-control reply-message" rows="3" included></textarea>
            </div>
            <button type="submit" class="btn btn-sm btn-custom-red btn-reply me-2">Post Reply</button>
            <button type="button" class="btn btn-sm btn-custom-red btn-cancel-reply">Cancel</button>
         </form>
      </div>
   </template>
</section>