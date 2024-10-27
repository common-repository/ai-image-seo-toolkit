document.addEventListener('DOMContentLoaded', function () {
    console.log('Preparation - addEventListener');
    const dropdown = document.querySelector('.custom-dropdown');
    const selected = dropdown.querySelector('.dropdown-selected');
    const selectedText = selected.querySelector('.selected-text');
    const options = dropdown.querySelector('.dropdown-options');

    // Toggle dropdown
    selected.addEventListener('click', function() {
        console.log('Preparation - click');
        options.style.display = options.style.display === 'block' ? 'none' : 'block';
        dropdown.classList.toggle('open'); // Toggle 'open' class
    });

    // Option clicked
    options.addEventListener('click', function(event) {
        let target = event.target;
        if (target.classList.contains('option-description') || target.classList.contains('option-title')) {
            target = target.parentNode;
        }
        if (target.classList.contains('dropdown-option')) {
            selectedText.textContent = target.querySelector('.option-title').textContent;
            selectedText.classList.add('selected'); // Add 'selected' class
            var selectedValue = target.getAttribute('data-value');
            options.style.display = 'none';
            jQuery('#aiit_post_type').val(selectedValue);
            dropdown.classList.remove('open'); // Remove 'open' class
        }
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', function(event) {
        if (!dropdown.contains(event.target)) {
            options.style.display = 'none';
            dropdown.classList.remove('open'); // Remove 'open' class
        }
    });
});


jQuery(document).ready(function($) {
    console.log('JS bulk file loaded and ready' ); // Confirm JS file loaded
    // Prepare optimization
    jQuery('#updateSettings').submit(function(e) {
        var button1 = document.getElementById('aiit-prepare-update');
        var errNotific = document.getElementById('posts-error-notification');
        var table = document.getElementById('posts-to-be-updated');
        var startOptimizationButton = document.getElementById('aiit-start-update-button');
        errNotific.style.display = 'none';
        table.style.display = 'none';
        if (!startOptimizationButton.disabled) {
            startOptimizationButton.disabled = true;
        }
        button1.classList.add('loading');
        e.preventDefault();
        var formData = jQuery(this).serialize();
        jQuery.ajax({
            url: aiit_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aiit_populate_update_table',
                form_data: formData,
                security: aiit_ajax.nonce
            },
            success: function(response) {
                if (response.data && response.data.message) {
                    jQuery('#posts-error-notification').html(response.data.message);
                    jQuery('#posts-to-be-updated').html("");
                    errNotific.style.display = 'block';
                } else {
                    startOptimizationButton.disabled = false;
                    startOptimizationButton.style.display = 'block';
                    jQuery('#posts-error-notification').html("");
                    jQuery('#posts-to-be-updated').html(response);
                    table.style.display = 'block';
                }
                console.log(response);

                button1.classList.remove('loading');

            },
            error: function(response) {
                jQuery('#posts-to-be-updated').html(response.data);
                button1.classList.remove('loading');
                console.error(jsonResponse);
            }
        });
    });

    // Accordion style menu
    jQuery(document).ready(function($) {
        $('.accordion div').addClass('accordion-content').hide();
        $('.accordion h2').click(function() {
            $(this).toggleClass('active').next().slideToggle(140);
    
            $('.accordion h2').not(this).removeClass('active').next('.accordion-content').slideUp(140);
        });
    });

    // Start bulk update
    jQuery('#aiit-start-update-button').on('click', function(event) {
        var button = document.getElementById('aiit-start-update-button');
        var errNotific = document.getElementById('posts-error-notification');
        errNotific.style.display = 'none';
        button.disabled = true;
        button.classList.add('loading');
        jQuery('#posts-to-be-updated').html('');
        jQuery.ajax({
            url: aiit_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aiit_start_bulk_update',
                security: aiit_ajax.nonce
            },
            success: function(response) {
                if (response.data.images) {
                    imageOptimizationProgress('In progress', response.data.images, 0);
                    button.classList.remove('loading');
                } else {
                    jQuery('#posts-error-notification').html(response.data.message);
                    errNotific.style.display = 'block';
                    button.classList.remove('loading');
                }
            },
            error: function() {
                if (response.data.message) {
                    jQuery('#posts-error-notification').html(response.data.message);
                }
                button.classList.remove('loading');
                console.error('AJAX request failed');
            }
        });
    });

    // Show Job data
    jQuery('#aiit-last-update-data').ready(function($) {
        // Load data immediately
        showLastJobs();
        // Periodically update data every 2 seconds (adjust as needed)
        setInterval(showLastJobs, 2000);
    });

    // Stop bulk update
    jQuery('#stop-update-link').on('click', function(event) {
        var div = document.getElementById('stop-update-loader');
        div.classList.add('loading-container');
        jQuery('#posts-to-be-updated').html('');
        jQuery.ajax({
            url: aiit_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aiit_stop_bulk_update',
                security: aiit_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showLastJobs();
                    div.classList.remove('loading-container');
                }
            },
            error: function() {
                showLastJobs();
                div.classList.remove('loading-container');
                console.error('AJAX request failed');
            }
        });
    });

    function showLastJobs() {
        jQuery.ajax({
            url: aiit_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aiit_update_load_data',
                security: aiit_ajax.nonce
            },
            success: function(response) {
                // Update the HTML with the new data
                var table = $('#aiit-last-update-data').html(response);
                if (table) {
                    console.log(table);
                    var firstRow = table.find('tbody tr:first');
                    if (firstRow.length > 0) {
                        var total = firstRow.find('td:eq(1)').text();
                        var processed = firstRow.find('td:eq(2)').text();
                        var status = firstRow.find('td:eq(3)').text();
                        imageOptimizationProgress(status, total, processed)
                    }
                }
            },
            error: function(error) {
                console.error('Error updating data:', error);
            }
        });
    }

    function imageOptimizationProgress(status, totalImages, processedImages) {
        var fadeElements = [
            document.getElementById('aiit-prepare-update'), 
            document.getElementById('posts-to-be-updated'), 
            document.getElementById('aiit-start-update-button'),
        ];
        console.log("imageOptimizationProgress");
        if (status === 'In progress') {

            fadeElements.forEach(function(elem) {
                elem.classList.add('fade-out', 'smooth-height-transition');
                var elemHeight = elem.offsetHeight; // Get current height
                elem.style.height = elemHeight + 'px'; // Set height explicitly
        
                setTimeout(function() {
                    elem.style.height = '0'; // Reduce height to 0 for collapse
                }, 10); // Start height transition after a very short delay
        
                setTimeout(function() {
                    elem.style.display = 'none';
                }, 500); // Match this with the CSS transition time
            });
        
            var progressContainer = document.getElementById('progress-container');
            progressContainer.style.display = 'none';
        
            var progressBar = document.getElementById('progress-bar');
            if (!progressBar) {
                progressBar = document.createElement('div');
                progressBar.id = 'progress-bar';
                progressBar.style.backgroundColor = 'green';
                progressBar.style.width = '0%';
                progressBar.style.height = '100%';
                progressContainer.appendChild(progressBar);
            }
    
            var percentText = document.getElementById('percent-text');
            if (!percentText) {
                percentText = document.createElement('div');
                percentText.id = 'percent-text';
                percentText.style.color = 'white';
                percentText.style.fontWeight = 'bold';
                percentText.style.padding = '5px 0'; // Adjust padding as needed
                progressContainer.appendChild(percentText);
            }
        
        
            // Show the stop optimization container
            var stopOptimizationContainer = document.getElementById('stop-update-container');
            stopOptimizationContainer.style.display = 'block';
        
            var percent = Math.round((processedImages / totalImages)*100);
            progressBar.style.width = percent + '%';
            percentText.textContent = percent + '% (' + processedImages + ' of ' + totalImages + ' Images)';

            var progressContainer = document.getElementById('progress-container');
            progressContainer.classList.add('fade-in');
            progressContainer.style.display = 'block';
        } else {
            var progressContainer = document.getElementById('progress-container');
            progressContainer.style.display = 'none';
            var stopOptimizationContainer = document.getElementById('stop-update-container');
            stopOptimizationContainer.style.display = 'none';
            fadeElements.forEach(function(elem) {
                elem.classList.add('fade-in');
                elem.style.display = 'block';
                elem.style.height = '';
            });

        }
    }
});