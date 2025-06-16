<section class="popup-section">
   <!-- Modal -->
   <div class="modal fade custom-modal" id="homepageModal" ttabindex="-1" role="dialog"
      aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content p-4 ">
            <div class="modal-header">
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-center align-items-center">
               <div>
                  <h4 class="fs-4 text-center text-dark mb-4 text-capitalize">Find trusted businesses near you
                     with ease.</h4>
                  <!-- Search Form -->
                  <?php include 'parts/common/search-form.php' ?>
               </div>
            </div>
            <div class="modal-footer">
               <!-- Premium Cards -->
               <?php include 'parts/home/modal-slider.php' ?>
               <!-- End Premium Cards -->
            </div>
         </div>
      </div>
   </div>
</section>