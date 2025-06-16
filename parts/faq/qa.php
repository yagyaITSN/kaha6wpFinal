<section class="container py-5">
   <h2 class="fs-2 fw-bold border-start border-4 border-danger ps-3">General Questions</h2>
   <!-- FAQ Accordion -->
   <div class="accordion mt-5" id="faqAccordion">
      <?php
      $faqs = [
         [
            'id' => 'One',
            'question' => 'Is listing my business on KAHA6 web directory free?',
            'answer' => 'Yes, listing your business/company to our web directory is 100% free. There may be premium features in the future, but adding your business is and will remain free. <strong>Note:</strong> Your business needs to be authorized.',
            'expanded' => true
         ],
         [
            'id' => 'Two',
            'question' => 'How to add my business/company to kaha cha?',
            'answer' => 'First, you need to register to kaha cha, which is 100% free. After you register, find your login information in your email. Then log in to Kaha6. You are all done; now you can add your business/company by clicking the add button on the top bar or by clicking <a href="https://kaha6.com/register-to-submit-your-company/" class="d-inline-block text-primary">here</a>.<div class="mt-3"><a href="https://kaha6.com/login" class="btn btn-custom-red">Register Now</a></div>',
            'expanded' => false
         ],
         [
            'id' => 'Three',
            'question' => 'How do I edit my business information after listing?',
            'answer' => 'After logging in to your Kaha6 account, navigate to "My Listings" where you\'ll see all your registered businesses. Click on the business you want to edit and select "Edit Information." Make your changes and save them. Changes may take up to 24 hours to appear on the directory.',
            'expanded' => false
         ],
         [
            'id' => 'Four',
            'question' => 'What information should I include in my business listing?',
            'answer' => 'For best results, include:<ul><li>Complete business name and description</li><li>Accurate physical address</li><li>Phone number and email</li><li>Business hours</li><li>Website and social media links</li><li>High-quality photos of your business</li><li>Products or services offered</li></ul><p>Complete listings receive more views and customer inquiries.</p>',
            'expanded' => false
         ]
      ];

      foreach ($faqs as $faq) {
      ?>
         <div class="shadow-sm border-0 text-decoration-none rounded-4 overflow-hidden mb-3">
            <div class="faq-question <?php echo $faq['expanded'] ? '' : 'collapsed'; ?>"
               id="heading<?php echo esc_attr($faq['id']); ?>"
               data-bs-toggle="collapse"
               data-bs-target="#collapse<?php echo esc_attr($faq['id']); ?>"
               aria-expanded="<?php echo $faq['expanded'] ? 'true' : 'false'; ?>"
               aria-controls="collapse<?php echo esc_attr($faq['id']); ?>">
               <?php echo esc_html($faq['question']); ?>
            </div>
            <div id="collapse<?php echo esc_attr($faq['id']); ?>"
               class="collapse <?php echo $faq['expanded'] ? 'show' : ''; ?>"
               aria-labelledby="heading<?php echo esc_attr($faq['id']); ?>"
               data-bs-parent="#faqAccordion">
               <div class="faq-answer">
                  <?php echo $faq['answer']; ?>
               </div>
            </div>
         </div>
      <?php
      }
      ?>
   </div>
</section>