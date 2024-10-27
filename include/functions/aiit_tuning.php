<?php
if ( ! defined( 'ABSPATH' ) ) exit;

## TUNING PAGE
function aiit_image_seo_toolkit_tuning_page() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aiit_save_tuning_param']) && isset( $_POST['aiit_tuning_nonce'] ) &&  wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['aiit_tuning_nonce'])), 'tuning_settings' )) {
        // The form is being saved
        aiit_save_tuning_parameters();
    }
    $oaiKeySet = aiit_oai_k_valid();
    $keySet = aiit_k_valid();
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if(!$oaiKeySet){
            $err = 'Enter your OpenAI API Key in <a href="';
            $err .= esc_url(admin_url('admin.php?page=ai-image-seo-toolkit-settings#key'));
            $err .= '">Settings</a> to activate the plugin';
            add_settings_error('ai-image-seo-toolkit-tuning', 'no_oia_key', $err , 'error');
        } else {
            if(!$keySet){
                $notic = 'To unlock PRO features, add your License Key in <a href="';
                $notic .= esc_url(admin_url('admin.php?page=ai-image-seo-toolkit-settings#key'));
                $notic .= '">Settings</a>';
                add_settings_error('ai-image-seo-toolkit-tuning', 'no_key', $notic, 'notice');
            } else {
                if (!aiit_tuning_parameters_set()){
                    add_settings_error('ai-image-seo-toolkit-tuning', 'not_all', 'Complete all fields and tap "Save Changes" at the bottom', 'notice');
                }
            }
        }
    }

    ?>
    <div class="main-container">
        <div class="column1">
            <h1>Tuning</h1>
            <p>An AI-powered search engine optimization WordPress plugin that streamlines image text generation by creating smart & SEO-friendly titles, ALTs, captions and descriptions.</p>
            <hr>
            <?php settings_errors('ai-image-seo-toolkit-tuning'); ?>
            <p style="padding-bottom: 10px;">Here you can adjust the AI's capabilities to fit your site's specific needs. Set your website type and define your target audience to align the AI Image SEO Toolkit with your content strategy, enhancing SEO relevance and.</p>
            <form id="tuning-settings" action="" method="post"> 
                <?php wp_nonce_field( 'tuning_settings', 'aiit_tuning_nonce' ); ?>         
                <?php settings_fields('aiit_tuning'); ?>
                <?php do_settings_sections('aiit_tuning'); ?>
                <div class="settings-section">
                    <div class="title-column">
                        <h2>Type of website</h2>
                        <span class="tooltip-icon" data-tooltip="Indicate both the nature and focus of your site, like 'educational marketing blog' or 'outdoor adventure e-commerce', to precisely align the AI's content creation with your specific niche.">?</span>
                    </div>
                    <div class="content-column">
                        <div class="input-container">
                            <input type="text" name="aiit_website_type" id="website-type-input" class="full-width-input" placeholder="e.g. Blog about the latest technologies" maxlength="50" value="<?php echo esc_attr(get_option('aiit_website_type')); ?>" required>
                            <span class="char-count" id="website-type-char-count">0/50</span>
                        </div>
                    </div>
                </div>
                <div class="settings-section">
                    <div class="title-column">
                        <h2>Target audience</h2>
                        <span class="tooltip-icon" data-tooltip="Identify your primary audience, such as 'Professionals in graphic and web design', to help the AI tailor its text generation for images to appeal directly to this group.">?</span>
                    </div>
                    <div class="content-column">
                        <div class="input-container">
                            <input type="text" name="aiit_audience" id="target-audience-input" class="full-width-input" placeholder="e.g. Tech professionals and enthusiasts" maxlength="50" value="<?php echo esc_attr(get_option('aiit_audience')); ?>" required>
                            <span class="char-count" id="target-audience-char-count">0/50</span>
                        </div>
                    </div>
                </div>

                <div class="settings-section">
                    <div class="title-column">
                        <h2>Categories</h2>
                        <span class="tooltip-icon" data-tooltip="Assign your images to relevant Post or Product categories like 'Tutorials' or 'Mobile Phones' to enhance content organization and relevance in each specific context. Filling out this field is key for generating text that best matches your specific needs. Limit to a maximum of 10 categories. If you have more, prioritize the most general ones to ensure broad coverage.">?</span>
                    </div>
                    <div class="content-column">
                        <div class="input-container">
                            <div class="input-wrapper">
                                <input type="text" id="category-input" placeholder="e.g. Computers" maxlength="20">
                                <span class="char-count" id="char-count">0/20</span>
                            </div>
                            <button id="add-category" type="button" class="add-category-link">Add Category</button>
                        </div>
                    </div>
                </div>

                <div id="category-list-container"> 
                    <input type="hidden" name="aiit_categories" id="categories_list" maxlength="200" value="<?php echo esc_attr(get_option('aiit_categories')); ?>">
                                
                    <div id="category-list" class="content-column"></div>
                </div>
                <hr>

                <div class="settings-section">
                    <div class="title-column">
                        <h2>Language</h2>
                        <span class="tooltip-icon" data-tooltip="Select your preferred language to ensure AI-generated texts match your audience's language preference. PRO feature.">?</span>
                    </div>
                    <div class="content-column">
                        <select class="full-width-input" id="language-input" name="aiit_language" required >
                            <?php
                            if($keySet){
                                $languages = array(
                                    'Afrikaans', 'Arabic', 'Armenian', 'Azerbaijani', 'Belarusian', 'Bosnian', 'Bulgarian', 'Catalan', 'Chinese', 'Croatian',
                                    'Czech', 'Danish', 'Dutch', 'English', 'Estonian', 'Finnish', 'French', 'Galician', 'German', 'Greek', 'Hebrew', 'Hindi',
                                    'Hungarian', 'Icelandic', 'Indonesian', 'Italian', 'Japanese', 'Kannada', 'Kazakh', 'Korean', 'Latvian', 'Lithuanian',
                                    'Macedonian', 'Malay', 'Marathi', 'Maori', 'Nepali', 'Norwegian', 'Persian', 'Polish', 'Portuguese', 'Romanian', 'Russian',
                                    'Serbian', 'Slovak', 'Slovenian', 'Spanish', 'Swahili', 'Swedish', 'Tagalog', 'Tamil', 'Thai', 'Turkish', 'Ukrainian', 'Urdu',
                                    'Vietnamese', 'Welsh'
                                );
                                $language_selected = get_option('aiit_language');
                                if (!in_array($language_selected, $languages)) {
                                    $language_selected = 'English';
                                }
    
                                foreach ($languages as $language) {
                                    echo '<option value="' . esc_html($language) . '" ';
                                    if ($language == $language_selected) {
                                        echo ' selected';
                                    }
                                    echo '>' . esc_html($language) . '</option>';
                                }
                            } else {
                                echo '<option value="English" selected>English</option>';
                            }

                            ?>
                        </select>
                    </div>
                </div>
                
                <hr>
                <p>Set the minimum width and height in pixels for images that will undergo SEO optimization. Images smaller than these dimensions will be excluded. Images must be at least 224 pixels on the shortest side to be processed.</p>
                <div class="settings-section">
                    <div class="title-column">
                        <h2>Min. Image Width (px)</h2>
                    </div>
                    <div class="content-column">
                        <input type="number" name="aiit_min_width" id="min-image-width-input" class="full-width-input" value="<?php echo esc_html(get_option('aiit_min_width', 1000)); ?>" required>
                    </div>
                </div>

                <div class="settings-section">
                    <div class="title-column">
                        <h2>Min. Image Height (px)</h2>
                    </div>
                    <div class="content-column">
                        <input type="number" name="aiit_min_height" id="min-image-height-input" class="full-width-input" value="<?php echo esc_html(get_option('aiit_min_height', 1000)); ?>" required>
                    </div>
                </div>
                <hr>
                <input type="submit" name="aiit_save_tuning_param" value="Save Changes" class="button button-primary" <?php if(!$oaiKeySet){echo 'disabled';} ?> >
            </form>
        </div>
        <div class="column2">
            <h3>Text Generation Testing</h3>
            <hr>
            <?php 
                if(aiit_tuning_parameters_set()){
                    aiit_provide_testing_content();
                } else {
                    echo '<h5>Save your tuning parameters to enable testing</h5>';
                }
            ?>
            <?php aiit_genarate_banner_html() ?>
        </div>
    </div>
    <?php
}

function aiit_tuning_parameters_set(){
    $parameters = array('aiit_openai_api_key', 'aiit_website_type', 'aiit_audience', 'aiit_categories', 'aiit_language', 'aiit_min_width', 'aiit_min_height', 'aiit_alt_text_prompt', 'aiit_title_prompt', 'aiit_caption_prompt', 'aiit_description_prompt');
    $result = true;
    foreach ($parameters as $parameter) {
       if(empty(get_option($parameter))){
            $result = false;
            break; 
        }
    }
    return $result;
}

function aiit_save_tuning_parameters(){
    if (isset( $_POST['aiit_tuning_nonce'] ) &&  wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['aiit_tuning_nonce'])), 'tuning_settings' )){
        $parameters = array(
            'aiit_website_type' => array(
                'value' => sanitize_text_field($_POST['aiit_website_type']),
                'error' => 'website type'),
            'aiit_audience' => array(
                'value' => sanitize_text_field($_POST['aiit_audience']),
                'error' => 'audience'),
            'aiit_categories' => array(
                'value' => sanitize_text_field($_POST['aiit_categories']),
                'error' => 'at least one category and make sure you click "Add Category" before saving'),
            'aiit_language' => array(
                'value' => sanitize_text_field($_POST['aiit_language']),
                'error' => 'language'),
            'aiit_min_width' => array(
                'value' => sanitize_text_field($_POST['aiit_min_width']),
                'error' => 'minimal width'),
            'aiit_min_height' => array(
                'value' => sanitize_text_field($_POST['aiit_min_height']),
                'error' => 'minimal height'),
        );
        $errorParams = '';
        foreach ($parameters as $parameter) {
            if($parameter['value'] === ''){
                if ($errorParams != ''){
                    $errorParams .= ', ';
                }
                $errorParams .= $parameter['error'];
            }
        }
        
        if ($errorParams === ''){
            foreach ($parameters as $key => $parameter) {
                update_option($key, $parameter['value']);
            }
            $alt_text_prompt = $GLOBALS['aiit_prompt_settings']['alt1'] . $parameters['aiit_website_type']['value'] . $GLOBALS['aiit_prompt_settings']['alt2'] . $parameters['aiit_audience']['value'] . $GLOBALS['aiit_prompt_settings']['alt3'] . $parameters['aiit_categories']['value'] . $GLOBALS['aiit_prompt_settings']['alt4'] . $parameters['aiit_language']['value'];
            $title_prompt = $GLOBALS['aiit_prompt_settings']['title1'] . $parameters['aiit_language']['value'] . $GLOBALS['aiit_prompt_settings']['title2'];
            $title_prompt2 = $GLOBALS['aiit_prompt_settings']['title_2_1'] . $parameters['aiit_language']['value'];
            $caption_prompt = $GLOBALS['aiit_prompt_settings']['caption'] . $parameters['aiit_language']['value'];
            $description_prompt = $GLOBALS['aiit_prompt_settings']['description1'] . $parameters['aiit_website_type']['value'] . $GLOBALS['aiit_prompt_settings']['description2'] . $parameters['aiit_audience']['value'] . $GLOBALS['aiit_prompt_settings']['description3'] . $parameters['aiit_language']['value'];
            update_option('aiit_alt_text_prompt', $alt_text_prompt);
            update_option('aiit_title_prompt', $title_prompt);
            update_option('aiit_title_prompt2', $title_prompt2);
            update_option('aiit_caption_prompt', $caption_prompt);
            update_option('aiit_description_prompt', $description_prompt);
            add_settings_error('ai-image-seo-toolkit-tuning', 'success', 'Settings saved successfully.', 'updated');
        } else {
            $message = 'Please provide ' . $errorParams;
            add_settings_error('ai-image-seo-toolkit-tuning', 'categories_empty', $message, 'error');
        }
    } else {
        add_settings_error('ai-image-seo-toolkit-tuning', 'not_permitted', 'Invalid data provided.', 'error');
    }
}

function aiit_provide_testing_content(){
    $image_id = aiit_tuning_get_images();
    ?>
    <h5>Remember to click 'Save Changes' to ensure your tuning settings are saved before testing</h5>

    <div class="testing-content">
        <div id="image-tabs">
            <ul class="image-tab-list">
                <?php
                    for ($i = 1; $i <= 3; $i++) {
                        if (isset($image_id[$i-1])) {
                            $image_url = wp_get_attachment_url($image_id[$i-1]);
                        } else {
                            $image_url = "";
                        }
                        echo '<li id="image-' . esc_html($i) . '-tab"';
                            if ($image_url) {echo ' class="image-uploaded';
                                if($i === 1){echo ' active';}
                                echo '"';}
                        echo '>
                                <div class="image-placeholder ';
                        if ($image_url) {echo ' background-image" style="background-image: url(' . esc_url($image_url) . ');>';}
                        echo '">
                                    <input type="file" class="image-upload-input" name="image_upload[]" accept="image/*" style="display: none;" />
                                    <div class="upload-icon-container">
                                        <span class="dashicons dashicons-plus"></span>
                                    </div>
                                </div>
                            </li>';
                    }
                ?>
            </ul>

            <div id="image-details">
            <?php
                echo wp_kses_post(aiit_tuning_get_all_details($image_id));
            ?>
            </div>
            <div class="button-container">
                <button id="optimize-images-button" class="button button-secondary">Regenerate texts</button>
            </div>
        </div>
    </div>
    <?php
}

function aiit_tuning_get_all_details($image_id) {
    $result = '';
    for ($i = 1; $i <= 3; $i++) {
        $result .= '  <div id="image-' .$i. '-details" class="image-detail">';
        $result .= aiit_provide_image_details_html($image_id[$i-1]);
        $result .= '</div>';                            
    }
    return $result;
}

function aiit_tuning_get_images() {
    global $wpdb;
    $result = array();
    for ($i = 1; $i <= 3; $i++) {
        $image = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}aiit_tuning_images WHERE `id` = %d LIMIT 1", $i));
        if ($image){     
            $result[] = $image->image_id;
        } else {
            $result[] = 0;
        }
    }
    return $result;
}

function aiit_start_test_optimization(){
    if (!isset($_POST['security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['security'])), 'aiit_tuning_nonce')) {
        wp_send_json_error('Permission check failed');
    }
    $image_id = aiit_tuning_get_images();
    $error = "";
    foreach ( $image_id as $image ) {
        if ($image !== 0){
            $processedOK = aiit_generate_alt_text($image, -2);
            //error_log('aiit_start_test_optimization, processedOK?: '. print_r($processedOK, true));
            if ($processedOK['status'] == "nok") {
                if ($error != ""){
                    $error .= "</br>";
                }
                $error .= $processedOK['error'];
            }
        }
    }
    $result = aiit_tuning_get_all_details($image_id);
    if ($error != ""){
        $error_html = '<div class="tuning-error"><p>Processing error:</br>';
        $error_html .= $error;
        $error_html .= '</p></div>';
        $result .= $error_html;
    }
    wp_send_json_success(array(
        'html'=> $result,
    ));
}

// Image file upload
function aiit_upload_tuning_image_callback() {
    if (!isset($_POST['security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['security'])), 'aiit_tuning_nonce')) {
        wp_send_json_error('Permission check failed');
    }
    $plhId = sanitize_text_field($_POST['plhId']);
    if (isset($_FILES['image'])) {
        
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error('File upload error: ' . $file['error']);
            return;
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $min_width = get_option('aiit_min_width');
        $min_height = get_option('aiit_min_height');

        // Check image dimensions
        $image_info = getimagesize($file['tmp_name']);
        if ($image_info === false) {
            wp_send_json_error('The uploaded file is not a valid image.');
            return;
        }

        $width = $image_info[0];
        $height = $image_info[1];
       
        if ($width < $min_width || $height < $min_height) {
            wp_send_json_error('Image dimensions are smaller than defined minimal values');
        }

        $attachment_id = media_handle_upload('image', 0);

        if (is_wp_error($attachment_id)) {
            wp_send_json_error(array('message' => 'Error uploading the image.'));
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . 'aiit_tuning_images';
            $existing_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}aiit_tuning_images WHERE id = %d", $plhId));
            if ($existing_record) {
                $wpdb->delete($table_name, array('id' => $plhId), array('%d'));
            } 
            $wpdb->insert($table_name, array('id' => $plhId, 'image_id' => $attachment_id), array('%d', '%d'));
            
            $generate = get_option('aiit_generate_on_upload');
            if($generate != "on"){
                aiit_generate_alt_text($attachment_id, -2);
            }
            
            // Image uploaded successfully
            $image_url = wp_get_attachment_url($attachment_id);
            $freshImageData = aiit_provide_image_details_html($attachment_id);
            wp_send_json_success(array(
                'url' => $image_url,
                'html'=> $freshImageData,
            ));
        }
    } else {
        // No file was uploaded
        wp_send_json_error('No image file received.');
    }
}

function aiit_provide_image_details_html($image_id) {
    $keySet = aiit_k_valid();
    if ($image_id > 0){
        if ($keySet){
            $title = get_the_title($image_id);
            $description = get_post_field('post_content', $image_id);
            $caption = get_post_field('post_excerpt', $image_id);
        }
        $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
    } else {
        $title = "";
        $description = "";
        $alt = "";
        $caption = "";
    }
    $freshImageData = '';
    if ($keySet){
        $freshImageData .= '<div class="image-part"><h4>Title</h4>
                                <p class="image-input">'. $title . '</p></div>';
    }
    $freshImageData .= '<div class="image-part"><h4>Alt Text</h4>
                            <p class="image-input">' .  $alt . '</p></div>';
    if ($keySet){                        
        $freshImageData .= '<div class="image-part"><h4>Caption</h4>
                                <p class="image-input">' . $caption . '</p></div>
                            <div class="image-part"><h4>Description</h4>
                                <p class="image-input">' . $description . '</p></div>';
    }   
    return $freshImageData;
}