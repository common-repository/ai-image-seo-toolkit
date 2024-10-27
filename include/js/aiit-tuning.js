jQuery(document).ready(function($) {
    console.log('JS tuining file loaded and ready2'); // Confirm JS file loaded

    jQuery(".image-detail").hide();
    jQuery("#image-1-details").show();
    var categoryString = document.getElementById('categories_list').value;
    var categories = [];
    if (categoryString.trim() !== ''){
        categories = categoryString.split(', ');
        loadCatogoryList()
    }
    
    // Function to load the category list
    function loadCatogoryList() {
        var categoryList = jQuery('#category-list');
        categories.forEach(function(loadedCatogory) {
            var listItem = jQuery('<div class="category-item" style="opacity: 1;">' + loadedCatogory + '<span class="remove-category">x</span></div>');
            listItem.find('.remove-category').click(function() {
                var index = categories.indexOf(loadedCatogory);
                if (index !== -1) {
                    categories.splice(index, 1);
                    listItem.remove(); // Remove this item from DOM
                    $('#categories_list').val(categories.join(', '));
                }
            });
            categoryList.append(listItem);
        });
    }

    // Function to update the category list
    function updateCategoryList() {
        var categoryList = jQuery('#category-list');
        // Only add the new category to the DOM
        var newCategory = categories[categories.length - 1]; // Get the last item in the array
        var listItem = jQuery('<div class="category-item" style="opacity: 0;">' + newCategory + '<span class="remove-category">x</span></div>'); // Start with opacity 0
        listItem.find('.remove-category').click(function() {
            var index = categories.indexOf(newCategory);
            if (index !== -1) {
                categories.splice(index, 1);
                listItem.remove(); // Remove this item from DOM
                jQuery('#categories_list').val(categories.join(', '));
            }
        });

        categoryList.append(listItem);
        setTimeout(function() {
            listItem.css('opacity', '1'); // Fade in
        }, 50);

        // Update existing items (if they exist)
        categoryList.children().not(listItem).each(function() {
            var existingCategory = $(this).text().replace('x', '').trim();
            var index = categories.indexOf(existingCategory);
            if (index === -1) { // If category no longer exists in the array, remove it
                jQuery(this).remove();
            }
        });
        jQuery('#categories_list').val(categories.join(', '));

    }

    // Character count functionality for Type of Website and Target Audience
    jQuery('#website-type-input, #target-audience-input').on('input', function() {
        var inputText = $(this).val();
        var charCount = inputText.length;
        var maxChar = 50; // Maximum character limit

        // Update the corresponding character count element
        var charCountElementId = jQuery(this).attr('id') === 'website-type-input' ? 'website-type-char-count' : 'target-audience-char-count';
        jQuery('#' + charCountElementId).text(charCount + '/' + maxChar);
    });

    // Event listener for 'Enter' key in category input
    jQuery('#category-input').keypress(function(event) {
        var keycode = event.keyCode || event.which;
        if (keycode == '13') { // Check if 'Enter' key is pressed
            event.preventDefault(); // Prevent the default action of the enter key
            jQuery('#add-category').click(); // Trigger the click event on 'Add' button
        }
    });

    // Add category when the "Add" button is clicked
    jQuery('#add-category').click(function() {
        var categoryInput = jQuery('#category-input');
        var newCategory = categoryInput.val().trim();
    
        if (newCategory.length > 20) {
            alert('Category names must be 20 characters or less.');
            return;
        }
    
        if (categories.length >= 10) {
            alert('Maximum of 10 categories allowed.');
            return;
        }
    
        if (newCategory !== '') {
            // Capitalize the category name
            newCategory = newCategory.charAt(0).toUpperCase() + newCategory.slice(1);
    
            if (!categories.includes(newCategory)) {
                categories.push(newCategory);
                categoryInput.val('');
                updateCategoryList();
                jQuery('#char-count').text('0/20'); // Reset character count to 0/20
            }
        }
    });

    // Character count functionality
    jQuery('#category-input').on('input', function() {
        var inputText = $(this).val();
        var charCount = inputText.length;
        jQuery('#char-count').text(charCount + '/20');
    });

    // Function to check if any image is uploaded
    function checkIfImagesUploaded() {
        // Check if any of the image tabs have the 'image-uploaded' class
        return jQuery('.image-tab-list li.image-uploaded').length > 0;
    }

    // Function to enable or disable the "Optimize Images" button based on image uploads
    function toggleOptimizeButton() {
        var optimizeButton = $('#optimize-images-button');

        if (checkIfImagesUploaded()) {
            optimizeButton.prop('disabled', false);
            optimizeButton.css('opacity', '1');
        } else {
            optimizeButton.prop('disabled', true);
            optimizeButton.css('opacity', '0.5');
        }
    }

    // Event handler for image uploads
    jQuery('.image-upload-input').on('change', function() {
        // Add 'image-uploaded' class as soon as an image is selected for upload
        jQuery(this).closest('li').addClass('image-uploaded');
        toggleOptimizeButton();
    });

    // Initial call to set the button state correctly on page load
    toggleOptimizeButton();

    // Function to show image details based on the clicked tab
    function showImageDetails(imageId) {
        // Hide all image details except for the clicked tab
        $(".image-detail").hide();
        $("#" + imageId + "-details").show();

        // Remove active class from all tabs
        $(".image-tab-list li").removeClass("active");
        // Add active class to the clicked tab
        $("#" + imageId + "-tab").addClass("active");
    }

    // Initialize the active tab variable
    var activeTab = "image-1";

    // Function to handle file input change event (Merged version)
    function handleFileUpload(input, imageTab) {
        if (input.files && input.files[0]) {
            var formData = new FormData();
            var plhId = imageTab.attr('id').replace("-tab", "").replace("image-", "");
            formData.append('action', 'upload_image');
            formData.append('image', input.files[0]);
            formData.append('plhId', plhId);
            formData.append('security', aiit_ajax.nonce);
            var loadingContainer = $('<div class="loading-container"><div class="pulsating-loader"></div></div>');
            imageTab.find('.image-placeholder').append(loadingContainer);

            $.ajax({
                url: aiit_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        var imageURL = response.data.url;
                        imageTab.find('.image-placeholder').css('background-image', 'url(' + imageURL + ')');
                        imageTab.find('.remove-icon-container').addClass('visible');
                        setTimeout(function() {
                            loadingContainer.remove();
                        }, 1500);
                        var activeTabId = imageTab.attr('id').replace("-tab", "");
                        var detailsTabId = "#" + activeTabId + "-details";
                        $(detailsTabId).html(response.data.html);
                        showImageDetails(activeTabId);

                    } else {
                        alert('Error uploading the image. ' + response.data);
                        loadingContainer.remove();
                        imageTab.removeClass('image-uploaded'); // Remove the class if upload fails
                    }
                },
                error: function() {
                    alert('Error uploading the image. Please try again.');
                    loadingContainer.remove();
                    imageTab.removeClass('image-uploaded'); // Remove the class if upload fails
                }
            });
        }
    }

    // Event Handlers
    jQuery('.image-upload-input').on('change', function() {
        var imageTab = jQuery(this).closest('li');
        imageTab.addClass('image-uploaded'); // Add the class as soon as the file is selected
        handleFileUpload(this, imageTab);
        toggleOptimizeButton(); // Update the button's state
    });

    jQuery('.upload-icon-container').on('click', function() {
        var imageTab = jQuery(this).closest('li');
        var input = imageTab.find('.image-upload-input')[0];
        jQuery(input).click();
    });

    jQuery("#image-1-tab, #image-2-tab, #image-3-tab").click(function() {
        showImageDetails(this.id.replace("-tab", ""));
    });

    // Function to prevent clicking on tabs with no image
    function preventInactiveTabClick() {
        jQuery(".image-tab-list li").click(function() {
            if (!jQuery(this).hasClass("image-uploaded")) {
                showImageDetails(activeTab);
            }
        });
    }

    // Call the preventInactiveTabClick function
    preventInactiveTabClick();

    // Start regeneration
    jQuery('#optimize-images-button').on('click', function(event) {
        var button = document.getElementById('optimize-images-button');
        button.classList.add('loading');
        jQuery.ajax({
            url: aiit_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aiit_start_test_optimization',
                security: aiit_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#image-details').html(response.data.html);
                    activeTab = "image-1";
                    button.classList.remove('loading');
                    showImageDetails(activeTab);
                    console.log('Images processed successfully.');
                } else {
                    jQuery('#aiit-image-proc-status').html('Error processing image.');
                    button.classList.remove('loading');
                    console.error('Error processing image:', response);
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