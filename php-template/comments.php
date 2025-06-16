<div class="d-flex align-items-center justify-content-between flex-column flex-md-row gap-3">
   <div class="col-md-6 col-12">
      <h3 class="fs-5 mb-3">Leave a Comment</h3>
      <form id="commentForm">
         <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" included>
         </div>
         <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" included>
         </div>
         <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" rows="4" included></textarea>
         </div>
         <button type="submit" class="btn btn-custom-red">Submit</button>
      </form>
   </div>
   <div class="col-md-6 col-12">
      <!-- Banner -->
      <?php include 'ads/comment/comment-ad-one.php' ?>
      <!-- End Banner -->
   </div>
</div>

<!-- Comments Display -->
<div class="comments-container mt-4" id="commentsContainer" style="display: none;">
   <!-- Sample comment - this would be dynamically added -->
   <div class="comment-item mb-4">
      <div class="d-flex">
         <div class="flex-shrink-0">
            <img src="./kaha6_about.png" class="rounded-circle" width="40" height="40" alt="User">
         </div>
         <div class="flex-grow-1 ms-3">
            <div class="d-flex align-items-center mb-1">
               <h5 class="mb-0 me-2">Jane Smith</h5>
               <small class="text-muted">1 week ago</small>
            </div>
            <p class="mb-2">I had a great experience with this business. The staff was very
               helpful!</p>
            <button class="btn btn-sm btn-outline-secondary reply-comment-btn">Reply</button>

            <!-- Reply form (hidden by default) -->
            <div class="reply-comment-form mt-2">
               <textarea class="form-control mb-2" rows="2" placeholder="Write your reply..."></textarea>
               <button class="btn btn-sm btn-custom-red submit-comment-reply">Submit</button>
               <button class="btn btn-sm btn-outline-secondary cancel-comment-reply ms-2">Cancel</button>
            </div>

            <!-- Replies -->
            <div class="comment-replies mt-3 ps-3 border-start">
               <!-- Sample reply -->
               <div class="comment-reply-item mb-3">
                  <div class="d-flex">
                     <div class="flex-shrink-0">
                        <img src="./kaha6_about.png" class="rounded-circle" width="30" height="30"
                           alt="User">
                     </div>
                     <div class="flex-grow-1 ms-2">
                        <div class="d-flex align-items-center mb-1">
                           <h6 class="mb-0 me-2">Business Owner</h6>
                           <small class="text-muted">5 days ago</small>
                        </div>
                        <p class="mb-0 small">We're glad you enjoyed your visit!</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>