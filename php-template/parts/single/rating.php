    <div class="d-flex mb-4 flex-wrap flex-md-nowrap gap-3 justify-content-between">
       <div class="col-12 col-md-6 mb-4 mb-md-0">
          <h3 class="fs-5 mb-3">Rate Us</h3>
          <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
             <span class="text-dark fs-4 fw-bold" id="averageRating">4.8</span>
             <div class="star-rating-display">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-half"></i>
             </div>
             <span class="text-muted" id="reviewCount">(234 reviews)</span>
          </div>

          <!-- Review Text Section -->
          <div class="review-section mt-3">
             <button class="btn btn-custom-red btn-sm" id="toggleReviewBtn">
                <i class="bi bi-pencil-square"></i> Add Review
             </button>
             <div class="review-textarea mt-2" id="reviewTextarea" style="display: none;">
                <div class="star-rating mb-2">
                   <input type="radio" id="new-star5" name="new-rating" value="5">
                   <label for="new-star5"><i class="bi bi-star-fill"></i></label>
                   <input type="radio" id="new-star4" name="new-rating" value="4">
                   <label for="new-star4"><i class="bi bi-star-fill"></i></label>
                   <input type="radio" id="new-star3" name="new-rating" value="3">
                   <label for="new-star3"><i class="bi bi-star-fill"></i></label>
                   <input type="radio" id="new-star2" name="new-rating" value="2">
                   <label for="new-star2"><i class="bi bi-star-fill"></i></label>
                   <input type="radio" id="new-star1" name="new-rating" value="1">
                   <label for="new-star1"><i class="bi bi-star-fill"></i></label>
                </div>
                <textarea class="form-control" rows="3" placeholder="Share your experience..."
                   id="reviewText"></textarea>
                <div id="reviewTextError" class="text-danger small mt-1" style="display: none;">
                   Please enter your review
                </div>
                <div id="ratingError" class="text-danger small mb-2" style="display: none;">
                   Please select a rating
                </div>
                <button class="btn btn-custom-red btn-sm mt-2" id="submitReviewBtn">Submit Review</button>
             </div>
          </div>

          <!-- Reviews Display -->
          <div class="reviews-container mt-3 p-3 rounded-2" id="reviewsContainer">
             <!-- Sample user review -->
             <div class="review-item mb-4" data-user-id="user123" data-review-id="1">
                <div class="d-flex">
                   <div class="flex-shrink-0">
                      <img src="./kaha6_about.png" class="rounded-circle" width="40" height="40" alt="User">
                   </div>
                   <div class="flex-grow-1 ms-3">
                      <div class="d-flex align-items-center mb-1">
                         <h5 class="mb-0 me-2 fs-6">John Doe</h5>
                         <small class="text-muted">2 days ago</small>
                      </div>
                      <div class="star-rating-display mb-2">
                         <i class="bi bi-star-fill"></i>
                         <i class="bi bi-star-fill"></i>
                         <i class="bi bi-star-fill"></i>
                         <i class="bi bi-star-fill"></i>
                         <i class="bi bi-star-fill"></i>
                      </div>
                      <p class="mb-2">Great service! Would definitely recommend.</p>
                      <div class="review-actions mt-2" style="display: none;">
                         <div class="edit-review-form">
                            <div class="star-rating-edit mb-2">
                               <input type="radio" id="edit-star5-1" name="edit-rating-1" value="5">
                               <label for="edit-star5-1"><i class="bi bi-star-fill"></i></label>
                               <input type="radio" id="edit-star4-1" name="edit-rating-1" value="4">
                               <label for="edit-star4-1"><i class="bi bi-star-fill"></i></label>
                               <input type="radio" id="edit-star3-1" name="edit-rating-1" value="3">
                               <label for="edit-star3-1"><i class="bi bi-star-fill"></i></label>
                               <input type="radio" id="edit-star2-1" name="edit-rating-1" value="2">
                               <label for="edit-star2-1"><i class="bi bi-star-fill"></i></label>
                               <input type="radio" id="edit-star1-1" name="edit-rating-1" value="1">
                               <label for="edit-star1-1"><i class="bi bi-star-fill"></i></label>
                            </div>
                            <textarea class="form-control mb-2"
                               rows="3">Great service! Would definitely recommend.</textarea>
                            <button class="btn btn-sm btn-custom-red save-edit-btn">Save Changes</button>
                            <button
                               class="btn btn-sm btn-outline-secondary cancel-edit-btn ms-2">Cancel</button>
                         </div>
                      </div>
                   </div>
                </div>
             </div>

             <!-- Sample business owner reply -->
             <div class="reply-item mb-3" data-user-id="owner456" data-reply-id="1">
                <div class="d-flex">
                   <div class="flex-shrink-0">
                      <img src="./kaha6_about.png" class="rounded-circle" width="30" height="30" alt="User">
                   </div>
                   <div class="flex-grow-1 ms-2">
                      <div class="d-flex align-items-center mb-1">
                         <h6 class="mb-0 me-2">Business Owner</h6>
                         <small class="text-muted">1 day ago</small>
                      </div>
                      <p class="mb-0 small">Thank you for your feedback!</p>
                      <div class="reply-actions mt-1" style="display: none;">
                         <textarea class="form-control mb-2 mt-2"
                            rows="2">Thank you for your feedback!</textarea>
                         <button class="btn btn-sm btn-custom-red save-reply-btn">Save</button>
                         <button class="btn btn-sm btn-outline-danger delete-reply-btn ms-2">Delete</button>
                         <button
                            class="btn btn-sm btn-outline-secondary cancel-reply-btn ms-2">Cancel</button>
                      </div>
                   </div>
                </div>
             </div>
          </div>
       </div>

       <div class="col-12 col-md-6">
          <h4 class="fs-5 mb-3">Rating Breakdown</h4>
          <div class="rating-progress">
             <div class="d-flex align-items-center mb-2">
                <span class="text-dark me-2" style="width: 20px;">5</span>
                <i class="bi bi-star-fill text-warning me-2"></i>
                <div class="progress flex-grow-1" style="height: 10px;">
                   <div class="progress-bar" role="progressbar" style="width: 85%" aria-valuenow="85"
                      aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span class="text-muted ms-2">85%</span>
             </div>
             <div class="d-flex align-items-center mb-2">
                <span class="text-dark me-2" style="width: 20px;">4</span>
                <i class="bi bi-star-fill text-warning me-2"></i>
                <div class="progress flex-grow-1" style="height: 10px;">
                   <div class="progress-bar" role="progressbar" style="width: 10%" aria-valuenow="10"
                      aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span class="text-muted ms-2">10%</span>
             </div>
             <div class="d-flex align-items-center mb-2">
                <span class="text-dark me-2" style="width: 20px;">3</span>
                <i class="bi bi-star-fill text-warning me-2"></i>
                <div class="progress flex-grow-1" style="height: 10px;">
                   <div class="progress-bar" role="progressbar" style="width: 3%" aria-valuenow="3"
                      aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span class="text-muted ms-2">3%</span>
             </div>
             <div class="d-flex align-items-center mb-2">
                <span class="text-dark me-2" style="width: 20px;">2</span>
                <i class="bi bi-star-fill text-warning me-2"></i>
                <div class="progress flex-grow-1" style="height: 10px;">
                   <div class="progress-bar" role="progressbar" style="width: 1%" aria-valuenow="1"
                      aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span class="text-muted ms-2">1%</span>
             </div>
             <div class="d-flex align-items-center">
                <span class="text-dark me-2" style="width: 20px;">1</span>
                <i class="bi bi-star-fill text-warning me-2"></i>
                <div class="progress flex-grow-1" style="height: 10px;">
                   <div class="progress-bar" role="progressbar" style="width: 1%" aria-valuenow="1"
                      aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span class="text-muted ms-2">1%</span>
             </div>
          </div>
       </div>
    </div>