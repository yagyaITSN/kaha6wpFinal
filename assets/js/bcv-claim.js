jQuery(document).ready(function ($) {
    // Business claimes
    jQuery(document).ready(function ($) {
        $('.bcv-claim-button').on('click', function () {
            var button = $(this);
            var post_id = button.data('post-id');

            $.ajax({
                url: bcv_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'bcv_claim_business',
                    post_id: post_id,
                    nonce: bcv_ajax.nonce
                },
                beforeSend: function () {
                    button.prop('disabled', true).text('Submitting...');
                },
                success: function (response) {
                    if (response.success) {
                        button.replaceWith('<button class="btn btn-warning text-capitalize mt-3 bcv-pending" style="padding:6px 12px">Claim on Pending!</button>');
                        alert('Claim request submitted successfully.');
                    } else {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            alert('Error: ' + response.data);
                            button.prop('disabled', false).text('Claim Your Business');
                        }
                    }
                },
                error: function () {
                    alert('An error occurred. Please try again.');
                    button.prop('disabled', false).text('Claim Your Business');
                }
            });
        });
    });

   
});