jQuery(document).ready(function($) {
    console.log('JS file loaded and ready'); // Confirm JS file loaded

    // Single image optimization
    jQuery('#aiit-process-single').on('click', function(event) {
        event.preventDefault();
        var button = document.getElementById('aiit-process-single');
        button.classList.add('loading');
        let postid = button.getAttribute('postid');
        console.log('Optimizing image with post id: ', postid);

        jQuery.ajax({
            url: aiit_ajax.ajax_url,
            type: 'POST',
            data: {
                postid: postid,
                action: 'aiit_optimize_image',
                security: aiit_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    jQuery('#aiit-image-proc-status').html('Image processed successfully. Reloading');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    jQuery('#aiit-image-proc-status').html('Error processing image. </br>' + response.data.message);
                    button.classList.remove('loading');
                    console.error('Error processing image:', response.data.message);
                }
            },
            error: function() {
                jQuery('#aiit-image-proc-status').html('Error occurred.');
                button.classList.remove('loading');
                console.error('AJAX request failed');
            }
        });
    });
});