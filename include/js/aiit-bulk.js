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
            jQuery('#aiit_selection_criteria').val(selectedValue);
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
    console.log('JS bulk file loaded and ready 2' ); // Confirm JS file loaded
    var lastExecutionTimestamp = 0;
    // Prepare optimization
    jQuery('#bulkSettings').submit(function(e) {
        var button1 = document.getElementById('aiit-prepare-optimization');
        button1.classList.add('loading');
        e.preventDefault();
        var formData = jQuery(this).serialize();
        jQuery.ajax({
            url: aiit_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aiit_populate_optimization_table',
                form_data: formData,
                security: aiit_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    var totalImagesToOptimize = response.data;
                    jQuery('#aiit-image-count').html(totalImagesToOptimize);
                    console.log('Preparation completed. Total Images to Optimize:', totalImagesToOptimize);
                    if(totalImagesToOptimize > 0){
                        jQuery('#aiit-start-optimization-button').prop('disabled', false);

                        // Enable and show the "Start Optimization" button
                        var startOptimizationButton = document.getElementById('aiit-start-optimization-button');
                        startOptimizationButton.removeAttribute('disabled');
                        startOptimizationButton.style.display = 'block';
                        button1.classList.remove('loading');
                    } else {
                        button1.classList.remove('loading');
                    }
                } else {
                    button1.classList.remove('loading');
                    jQuery('#aiit-image-count').html('Error - ' + response.data);
                    console.error('Error populating table:', response);
                }
            },
            error: function() {
                jQuery('#aiit-image-count').html('Error occurred while making the AJAX request.');
                button1.classList.remove('loading');
                console.error('AJAX request failed');
            }
        });
    });


    // Start bulk optimization
    jQuery('#aiit-start-optimization-button').on('click', function(event) {
        var button = document.getElementById('aiit-start-optimization-button');
        button.classList.add('loading');
        button.disabled = true;
        jQuery.ajax({
            url: aiit_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aiit_start_bulk_optimization',
                security: aiit_ajax.nonce
            },
            success: function(response) {
                if (response.data.images) {
                    imageOptimizationProgress('In progress', response.data.images, 0);
                    lastExecutionTimestamp = new Date().getTime();
                    button.classList.remove('loading');
                } else {
                    button.classList.remove('loading');
                    console.error('Error processing image:', response.data.message);
                }
            },
            error: function() {
                button.classList.remove('loading');
                console.error('AJAX request failed');
            }
        });
    });

    
    // Stop bulk optimization
    jQuery('#stop-optimization-link').on('click', function(event) {
        var div = document.getElementById('stop-optimization-loader');
        div.classList.add('loading-container');
        jQuery.ajax({
            url: aiit_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aiit_stop_bulk_optimization',
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

    // Show Job data
    jQuery('#aiit-last-job-data').ready(function($) {
        // Load data immediately
        showLastJobs();
        // Periodically update data every 2 seconds (adjust as needed)
        setInterval(showLastJobs, 4000);
    });

    // Accordion style menu
    jQuery(document).ready(function($) {
        $('.accordion div').addClass('accordion-content').hide();
        $('.accordion h2').click(function() {
            $(this).toggleClass('active').next().slideToggle(140);
    
            $('.accordion h2').not(this).removeClass('active').next('.accordion-content').slideUp(140);
        });
    });

    function imageOptimizationProgress(status, totalImages, processedImages) {
        var fadeElements = [
            document.getElementById('aiit-prepare-optimization'), 
            document.getElementById('image-count-container'), 
            document.getElementById('aiit-start-optimization-button'),
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
            var stopOptimizationContainer = document.getElementById('stop-optimization-container');
            stopOptimizationContainer.style.display = 'block';
        
            var percent = Math.round((processedImages / totalImages)*100);
            progressBar.style.width = percent + '%';
            percentText.textContent = percent + '% (' + processedImages + ' of ' + totalImages + ' Images)';

            var progressContainer = document.getElementById('progress-container');
            progressContainer.classList.add('fade-in');
            progressContainer.style.display = 'block';
        } else {
            var currentTime = new Date().getTime();
            console.log("currentTime: " + currentTime + ", lastExecutionTimestamp: " + lastExecutionTimestamp);

            if (currentTime - lastExecutionTimestamp > 5000){
                var progressContainer = document.getElementById('progress-container');
                progressContainer.style.display = 'none';
                var stopOptimizationContainer = document.getElementById('stop-optimization-container');
                stopOptimizationContainer.style.display = 'none';
                fadeElements.forEach(function(elem) {
                    elem.classList.add('fade-in');
                    elem.style.display = 'block';
                    elem.style.height = '';
                });
            } else {
                console.log("skipping update ....");
            }
        }
    }
    
    function showLastJobs() {
        jQuery.ajax({
            url: aiit_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aiit_job_load_data',
                security: aiit_ajax.nonce
            },
            success: function(response) {
                // Update the HTML with the new data
                var table = $('#aiit-last-job-data').html(response);
                if (table) {
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
});