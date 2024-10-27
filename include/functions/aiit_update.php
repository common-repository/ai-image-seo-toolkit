<?php
if ( ! defined( 'ABSPATH' ) ) exit;
## BULK OPTIMIZATION PAGE
function aiit_image_seo_toolkit_update_pages_page() {
    ?>
        <div class="main-container">
            <div class="column1">
                <h1>Update content</h1>
                <?php
                    if (!aiit_k_valid() || !aiit_tuning_parameters_set()){
                        echo '<h5 style="padding-bottom: 10px;">Bulk content update is available in Pro version after <a href="';
                        echo esc_url(admin_url('admin.php?page=ai-image-seo-toolkit-settings#key'));
                        echo '">adding keys</a> and setting up <a href="';
                        echo esc_url(admin_url('admin.php?page=ai-image-seo-toolkit-tuning'));
                        echo '">tuning parameters</a>.<br></h5>';
                    } else {
                        aiit_show_update_form();
                    }
                ?>
            </div>
            <div class="column2">
                <h3>Information</h3>
                <hr>
                <div class="accordion">
                <h2>What this function is for</h2>
                <div>
                WordPress images have four text fields: Titles, ALTs, Captions, and Descriptions. These texts are visible in the Media Library, where all your site's images are stored. Our plugin automatically writes new text to these fields. However, both the Gutenberg and Classic editors offer a feature that allows you to have custom Titles, ALTs, Captions, and Descriptions specific to the images on a particular page or post. The "Update content" feature enables you to refresh these images in your posts or pages with the newly generated text from the Media Library source.
                </div>
                <h2>If you use another Wordpress builder</h2>
                <div>
                If you're using a different WordPress builder and have added custom titles, ALTs, or captions to your posts and images, updating these images with new text from the Media Library may require manual intervention. To check the text status of your images, use the developer tool "Inspect" on any of your images to see what text is displayed.
                </div>
                </div>
                <?php aiit_genarate_banner_html() ?>
        </div>
    <?php
}

function aiit_show_update_form(){
    ?>
        <p style="padding-bottom: 10px;">If you use the Gutenberg or Classic editor, you might have already published posts, pages, or products with custom image titles, ALTs, or captions. This function helps you to update these images that are published on older posts and pages with the newly written texts from the Media Library. If you're unsure whether you need this, you likely don't.</p>
        <hr>
        <div class="tab-container">
            <div>
                <form id="updateSettings" action="">
                    <div class="settings-section">
                        <div class="title-column-wide">
                            <h2>Update content for</h2>
                        </div>
                        <div class="content-column">
                            <input type="hidden" name="aiit_post_type" id="aiit_post_type" maxlength="200" value="<?php echo esc_attr(get_option('aiit_post_type')); ?>">
            
                            <div class="custom-dropdown">
                                <?php echo wp_kses_post(aiit_show_post_type_selection_dropdown()); ?>
                            </div>
                        </div>
                    </div>

                    <div class="settings-section" style="margin-bottom: 30px;">
                        <div class="title-column-wide">
                            <h2>Limit the count</h2>
                            <span class="tooltip-icon" data-tooltip="Set the number of posts/pages/products you wish to process. Useful for testing purposes. To process all based to the previously set settings, leave the field empty.">?</span>
                        </div>
                        <div class="content-column">
                            <input type="number" class="full-width-input" id="aiit_limit_posts" name="aiit_limit_posts" value="<?php echo esc_attr(get_option('aiit_limit_posts')); ?>" placeholder="Leave empty to process all" maxlength="7">
                        </div>
                    </div>
                    <button id="aiit-prepare-update" class="button button-2 button-secondary">Get Total</button>
                </form>

                <div id="posts-to-be-updated"></div>
                <div id="posts-error-notification" class="posts-error-notification"  style="display: none;"></div>
                <hr>

                <div class="button-container">
                    <button id="aiit-start-update-button" class="button button-primary" disabled>Start the Update</button>
                </div>
 
            </div>
            <div id="progress-container">
                <div id="progress-bar"></div>
                <div id="percent-text"></div>
            </div>
            <div id="stop-update-container" style="display: none; margin: 1em;">
                <a href="#" id="stop-update-link">Stop post update</a> 
                <span class="tooltip-icon" data-tooltip="Stop the optimization at any time and restart where you left off. You will need to start a new session to resume what was previously stopped. Optimized images up to the point of stopping are saved.">?</span>
                <div id="stop-update-loader"></div>               
            </div>
            <div class="optimization-table-container">
                <h3 style="padding-top: 30px;">Post update history</h3>
                <div id="aiit-last-update-data"></div>
            </div>
        </div>
    <?php
}

function aiit_show_post_type_selection_dropdown(){
    $selection_criteria = get_option('aiit_post_type');
    $selected_option = "Select an option";
    $dropdown = array(
                    array(
                        'value' => 'post',
                        'title' => "Posts",
                        'description' => "",
                    ),
                    array(
                        'value' => 'page',
                        'title' => "Pages",
                        'description' => '',
                    ),
                    array(
                        'value' => 'product',
                        'title' => "Products",
                        'description' => "WooCommerce product pages",
                    )
                );
    if ($selection_criteria){
        foreach ($dropdown as $item) {
            if ($item['value'] === $selection_criteria) {
                $selected_option = $item['title'];
            }
        }
    }
    echo   '<div class="dropdown-selected">
                <span class="selected-text">' . esc_html($selected_option) . '</span>
                <span class="dropdown-caret"></span>
            </div>
            <div class="dropdown-options">';
    
    foreach ($dropdown as $item) {
        echo '<div class="dropdown-option" data-value="' . esc_html($item['value']) . '">
                    <div class="option-title">' . esc_html($item['title']) . '</div>
                    <div class="option-description">' . esc_html($item['description']) . '</div>
                </div>';
    }
    echo    '</div>';
}

## Popuate update table
function aiit_populate_update_table() {
    if (!isset($_POST['security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['security'])), 'aiit_update_nonce')) {
        wp_send_json_error('Permission check failed');
    }
    if (!aiit_k_valid()) {
        wp_send_json_error('Invalid License Key provided.');
    }
    $serializedFormData = sanitize_text_field($_POST['form_data']);
    aiit_update_update_settings($serializedFormData);
    $limit = get_option('aiit_limit_posts');
    if(!$limit){
        $limit = -1;
    }
    $post_type = get_option('aiit_post_type');
    $args = array(
        'post_type'      => $post_type, 
        'posts_per_page' => $limit,
        's'              => '<img',
    );
    $images_to_optimize = 0;

    global $wpdb;
    $delete = $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}aiit_posts_update");
    $posts = new WP_Query($args);
    
    if ($posts->have_posts()) {
        while ($posts->have_posts()) {
            $posts->the_post();
            $content = get_the_content();
            $postID = get_the_ID();
            $imgFound = aiit_populate_update_figures($content, $postID);
            if ($imgFound == 0){
                $imgFound = aiit_populate_update_captions($content, $postID);
            }
            if ($imgFound > 0){
                $images_to_optimize++;
            }
        }
        wp_reset_postdata();
        if ($images_to_optimize>0){
            aiit_post_update_table();
        }
    } else {
        wp_send_json_error(array('message' => 'No corresponding results found.'));  
    }
    if ($images_to_optimize>0){
        wp_send_json_success(array('table' => $images_to_optimize));
    } else {
        wp_send_json_error(array('message' => 'Nothing to update.'));      
    }
    
}

function aiit_update_update_settings($serializedFormData){
    parse_str($serializedFormData, $formDataArray);
    $limit_posts = "";
    $update_criteria = "";
    if (array_key_exists('aiit_limit_posts', $formDataArray)) {
        $limit_posts = $formDataArray['aiit_limit_posts'];
    }
    if (array_key_exists('aiit_post_type', $formDataArray)) {
        $update_criteria = $formDataArray['aiit_post_type'];
    }
    update_option('aiit_limit_posts', $limit_posts);
    update_option('aiit_post_type', $update_criteria);
}

function aiit_populate_update_figures($content, $postID) {
    if (!aiit_k_valid()) {
        wp_send_json_error('Invalid License Key provided.');
    }
    $min_width = get_option('aiit_min_width', 1000);
    $min_height = get_option('aiit_min_height', 1000);
    $images_to_optimize = 0;
    global $wpdb;
    $table_p_update = $wpdb->prefix . 'aiit_posts_update';
    $doc = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8">' . $content);
    $xpath = new DOMXPath($doc);
    $figures = $xpath->query('//figure[contains(@class, "wp-block-image")]');
    foreach ($figures as $figure) {
        $img = $xpath->query('.//img', $figure)->item(0);
        if($img){
            $altText = $img ? $img->getAttribute('alt') : '';
            $imgClass = $img ? $img->getAttribute('class') : '';
            $imgTitle = $img ? $img->getAttribute('title') : '';
            $figcaption = $xpath->query('.//figcaption[@class="wp-element-caption"]', $figure)->item(0);
            $figcaptionText = $figcaption ? $figcaption->nodeValue : '';
            if (preg_match('/wp-image-(\d+)/', $imgClass, $matches)) {
                $imgID = $matches[1];
                list($width, $height) = getimagesize(get_attached_file($imgID));
                if ($width >= $min_width && $height >= $min_height) {
                    $altTextML = get_post_meta($imgID, '_wp_attachment_image_alt', true);
                    $captionML = get_post_field('post_excerpt', $imgID);
                    $titleML = get_post_field('post_title', $imgID);
                    $updateTitle = (get_option('aiit_generate_title') === 'on') ? "true" : "false";
                    $updateCaption = (get_option('aiit_generate_caption') === 'on') ? "true" : "false";
                    $updateMD = 'false';
                    if($updateCaption == 'true' && $captionML != ''){
                        if(rtrim($captionML) != rtrim($figcaptionText)){
                            $updateMD = 'true';
                        }
                    }
                    if($updateTitle == 'true' && $titleML != ''){
                        if(rtrim($titleML) != rtrim($imgTitle)){
                            $updateMD = 'true';
                        }
                    }
                    if(rtrim($altTextML) != rtrim($altText)){
                        $updateMD = 'true';
                    }

                    if ($updateMD == 'true'){
                        $image_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}aiit_posts_update WHERE `post_id` = %d AND `image_id` = %d", $postID, $imgID));
                        if (!$image_exists) {
                            $insert_result = $wpdb->insert($table_p_update, [
                                'post_id' => $postID, 
                                'image_id' => $imgID, 
                                'old_alt' => $altText, 
                                'old_caption' => $figcaptionText,
                                'old_title' => $imgTitle,
                                'ml_alt' => $altTextML, 
                                'ml_caption' => $captionML,
                                'ml_title' => $titleML,
                                'status' => 'due'], ['%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
                            if (!$insert_result) {
                                error_log("aiit_populate_update_figures: Failed to add Post - ID: {$postID}, Name: {$imgID}");
                            }
                        }
                        $images_to_optimize++;
                    }
                }
            }
        }
    }
    return $images_to_optimize;
}

function aiit_populate_update_captions($content, $postID) {
    if (!aiit_k_valid()) {
        wp_send_json_error('Invalid License Key provided.');
    }
    $min_width = get_option('aiit_min_width', 1000);
    $min_height = get_option('aiit_min_height', 1000);
    $images_to_optimize = 0;
    global $wpdb;
    $table_p_update = $wpdb->prefix . 'aiit_posts_update';
    $pattern = '/\[caption\s*.*?\](<img.*?>)(.*?)\[\/caption\]/i';
    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $imgTag = $match[1];
        $figcaptionText = $match[2];
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->loadHTML('<?xml encoding="UTF-8">' . $imgTag);
        $img = $doc->getElementsByTagName('img')->item(0);
        if($img){
            $altText = $img ? $img->getAttribute('alt') : '';
            $imgClass = $img ? $img->getAttribute('class') : '';
            $imgTitle = $img ? $img->getAttribute('title') : '';
            if (preg_match('/wp-image-(\d+)/', $imgClass, $matches)) {
                $imgID = $matches[1];
                list($width, $height) = getimagesize(get_attached_file($imgID));
                if ($width >= $min_width && $height >= $min_height) {
                    $altTextML = get_post_meta($imgID, '_wp_attachment_image_alt', true);
                    $captionML = get_post_field('post_excerpt', $imgID);
                    $titleML = get_post_field('post_title', $imgID);
                    $updateTitle = (get_option('aiit_generate_title') === 'on') ? "true" : "false";
                    $updateCaption = (get_option('aiit_generate_caption') === 'on') ? "true" : "false";
                    $updateMD = 'false';
                    if($updateCaption == 'true' && $captionML != ''){
                        if(rtrim($captionML) != rtrim($figcaptionText)){
                            $updateMD = 'true';
                        }
                    }
                    if($updateTitle == 'true' && $titleML != ''){
                        if(rtrim($titleML) != rtrim($imgTitle)){
                            $updateMD = 'true';
                        }
                    }
                    if(rtrim($altTextML) != rtrim($altText)){
                        $updateMD = 'true';
                    }

                    if ($updateMD == 'true'){
                        $image_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}aiit_posts_update WHERE `post_id` = %d AND `image_id` = %d", $postID, $imgID));
                        if (!$image_exists) {
                            $insert_result = $wpdb->insert($table_p_update, [
                                'post_id' => $postID, 
                                'image_id' => $imgID, 
                                'old_alt' => $altText, 
                                'old_caption' => $figcaptionText, 
                                'old_title' => $imgTitle,
                                'ml_alt' => $altTextML, 
                                'ml_caption' => $captionML,
                                'ml_title' => $titleML,
                                'ml_caption' => $captionML, 
                                'status' => 'due'], ['%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
                            if (!$insert_result) {
                                error_log("aiit_populate_update_captions: Failed to add Post - ID: {$postID}, Name: {$imgID}");
                            }
                        }
                        $images_to_optimize++;
                    }
                }                       
            }
        }
    }
    return $images_to_optimize;
}

function aiit_post_update_table(){
    global $wpdb;
    $update_table = wp_cache_get('aiit_post_update_table');
    if ($update_table === false) {
        $update_table = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aiit_posts_update ORDER BY id DESC LIMIT 500", ARRAY_A);
        wp_cache_set('aiit_post_update_table', $update_table, '', 3);
    }
    echo '<table class="optimization-table">';
    echo '<thead><tr><th>Post ID</th><th>Image ID</th><th>Existing content</th><th>Media Library</th></tr></thead>';
    echo '<tbody>';
    foreach ($update_table as $row) {
        echo '<tr>';
        echo '<td>' . esc_html($row['post_id']) . '</td>';
        echo '<td>' . esc_html($row['image_id']) . '</td>';
        echo '<td><b>ALT text:</b> ' . esc_html($row['old_alt']) . '<br><b>Caption:</b> ' . esc_html($row['old_caption']) . '<br><b>Title:</b> ' . esc_html($row['old_title']) . '</td>';
        echo '<td><b>ALT text:</b> ' . esc_html($row['ml_alt']) . '<br><b>Caption:</b> ' . esc_html($row['ml_caption']) . '<br><b>Title:</b> ' . esc_html($row['ml_title']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    wp_die();
}

function aiit_update_data_callback(){
    if (!isset($_POST['security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['security'])), 'aiit_update_nonce')) {
        wp_send_json_error('Permission check failed');
    }
    global $wpdb;
    $last_jobs = wp_cache_get('aiit_update_data_callback');
    if ($last_jobs === false) {
        $last_jobs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aiit_bckg_processing WHERE `type` = 'Update' ORDER BY id DESC LIMIT 3", ARRAY_A);
        wp_cache_set('aiit_update_data_callback', $last_jobs, '', 3);
    }
    echo '<table class="optimization-table">';
    echo '<thead><tr><th>ID</th><th>Total</th><th>Processed</th><th>Status</th><th>Started</th></tr></thead>';
    echo '<tbody>';
    foreach ($last_jobs as $row) {
        echo '<tr>';
        echo '<td>' . esc_html($row['id']) . '</td>';
        echo '<td>' . esc_html($row['initial_count']) . '</td>';
        echo '<td>' . esc_html($row['processed_count']) . '</td>';
        echo '<td>' . esc_html($row['status']) . '</td>';
        echo '<td>' . esc_html($row['created_on']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    wp_die();
}

##schedule job
function aiit_schedule_update() {
    if (!isset($_POST['security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['security'])), 'aiit_update_nonce')) {
        wp_send_json_error('Permission check failed');
    }
    if (wp_schedule_single_event(time(), 'aiit_schedule_update_event')) {
        global $wpdb;
        $image_exists = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}aiit_posts_update");
        wp_send_json_success(array('images' => $image_exists));
    } else {
        error_log('aiit_schedule_update - Failed to schedule the event.');
        wp_send_json_error(array('message' => 'Failed to optimize image.'));
    }
}

function aiit_update_posts() {
    if (!aiit_oai_k_valid() || !aiit_k_valid()) {
        wp_send_json_error('Invalid OpenAI API or License Key provided.');
    }
    global $wpdb;
    $table_job = $wpdb->prefix . 'aiit_bckg_processing';
    $image_exists = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}aiit_posts_update");
    if ($image_exists>0){
        //clean up existing jobs in progress
        $jobs_in_progress = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aiit_bckg_processing WHERE `status` = 'In progress' AND  `type` = 'Update'");
        foreach ( $jobs_in_progress as $job ) {
            $wpdb->update($table_job, array('status' => 'Canceled'), array('id' => $job->id));
        }
        //add the new job
        $result = $wpdb->insert($table_job, array('initial_count' => $image_exists, 'status' => 'In progress', 'type' => 'Update'), array('%d', '%s', '%s'));
        $job_id = null;
        if ($result !== false) {
            $job_id = $wpdb->insert_id;
            $processed_images = 0;
            $errors_in_row = 0;
            $newStatus = 'Completed';
            while ($imageForUpdate = aiit_fetchNextImageInPost()) {
                $post_type = get_post_type($imageForUpdate->post_id);
                $processingResult = aiit_update_figures($imageForUpdate, $job_id);
                $processingResult2 = aiit_update_captions($imageForUpdate, $job_id);
                if($processingResult == "ok" || $processingResult2 == "ok"){
                    $processingResult == "ok";
                    $processed_images += 1;
                    $errors_in_row = 0;
                    $wpdb->update($table_job, array('processed_count' => $processed_images), array('id' => $job_id));
                } else if ($processingResult == "nok"){
                    $errors_in_row += 1;
                    if($errors_in_row == 5){
                        $newStatus = 'Canceled due errors';
                        break;
                    }
                }
                aiit_removeProcessedRecordInPost($imageForUpdate->id);
            }
            if($processed_images == 0 && $newStatus == 'Completed'){
                $newStatus = 'Failed';
            }
            $jobRow = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}aiit_bckg_processing WHERE `id` = %d", $job_id));
            if($jobRow && $jobRow->status == 'In progress' ){
                $wpdb->update($table_job, array('status' => $newStatus), array('id' => $job_id));  
            }          
        } else {
            error_log('aiit_update_posts, failed to insert job');
        }
    }
}

function aiit_fetchNextImageInPost() {
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aiit_posts_update LIMIT 1");
}

function aiit_removeProcessedRecordInPost($id) {
    global $wpdb;
    $table_optimization = $wpdb->prefix . 'aiit_posts_update';
    $wpdb->delete($table_optimization, array('id' => $id));
}

function aiit_update_figures($imageForUpdate, $job_id){
    $post_content = get_post_field('post_content', $imageForUpdate->post_id);
    $processingResult = 'nok';
    $reason = '';
    if (!empty($post_content)) {
        $new_content = aiit_replace_figure_content($post_content, $imageForUpdate->image_id, $imageForUpdate->post_id, $job_id);
        if ($new_content != ''){
            wp_update_post(array('ID' => $imageForUpdate->post_id, 'post_content' => $new_content));
            $processingResult = 'ok';
        } else {
            $reason = 'Failed to get result';
        }
    } else {
        $reason = 'Failed to load post content';
    }
    return $processingResult;
}

function aiit_replace_figure_content($post_content, $image_id, $post_id, $job_id){
    $try_update_all = 'false';
    if($image_id == -1){
        $try_update_all = 'true';
    } else {
        $altTextML = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        $captionML = get_post_field('post_excerpt', $image_id);
        $titleML = get_post_field('post_title', $image_id);
    }
    $images_updated = 0;
    $updateTitle = (get_option('aiit_generate_title') === 'on') ? "true" : "false";
    $updateCaption = (get_option('aiit_generate_caption') === 'on') ? "true" : "false";
    Global $wpdb;
    $table_log = $wpdb->prefix . 'aiit_processed_images';
    $doc = new DOMDocument('1.0', 'UTF-8');
    $doc->formatOutput = true; 
    $content_new = '<?xml encoding="UTF-8">' . '<div>' . $post_content . '</div>';
    libxml_use_internal_errors(true);
    $doc->loadHTML($content_new);
    $xpath = new DOMXPath($doc);
    $figures = $xpath->query('//figure[contains(@class, "wp-block-image")]');
    foreach ($figures as $figure) {
        $img = $xpath->query('.//img', $figure)->item(0);
        if($img){
            $imgClass = $img ? $img->getAttribute('class') : '';       
            if (preg_match('/wp-image-(\d+)/', $imgClass, $matches)) {
                $imgID = $matches[1];
                if($imgID == $image_id || $try_update_all == 'true'){
                    $updated = 'false';
                    if($try_update_all == 'true'){
                        $altTextML = get_post_meta($imgID, '_wp_attachment_image_alt', true);
                        $captionML = get_post_field('post_excerpt', $imgID);
                        $titleML = get_post_field('post_title', $imgID);
                    }
                    $current_alt = $img ? $img->getAttribute('alt') : '';
                    $current_title = $img ? $img->getAttribute('title') : '';
                    $figcaption = $xpath->query('.//figcaption', $figure)->item(0);
                    $current_caption = $figcaption ? $figcaption->nodeValue : '';//old caption
                    if($updateCaption == "true" && $current_caption != $captionML){
                        if(!$figcaption){
                            $newFigcaption = $doc->createElement('figcaption', $captionML);
                            $newFigcaption->setAttribute('class', 'wp-element-caption');
                            $figure->appendChild($newFigcaption);
                        } else {
                            $figcaption->nodeValue = $captionML;
                        }
                        $updated = 'true';
                    }
                    if ($current_alt != $altTextML){
                        $img->setAttribute('alt', $altTextML);
                        $updated = 'true';
                    }
                    
                    if ($updateTitle === "true" && $titleML != $current_title) {
                        $img->setAttribute('title', $titleML);
                        $updated = 'true';
                    }

                    if ($updated == 'true'){
                        $wpdb->insert($table_log, array('image_name' => basename(get_attached_file($imgID)), 'image_id' => $imgID, 'post_id' => $post_id, 'job_id' => $job_id, 'old_alt' => $current_alt, 'old_caption' => $current_caption, 'old_title' => $current_title, 'status' => 'ok'), array('%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s'));
                        $images_updated++;
                    }
                }
            }
        }
    }
    if($images_updated > 0){
        $firstDivElement = $xpath->query('//div[1]')->item(0);
        $post_content = $doc->saveHTML($firstDivElement);
        $post_content = substr($post_content, 5, -6);
    } else {
        $post_content = "";
    }
    return $post_content;
}

function aiit_update_captions($imageForUpdate, $job_id){
    $post_content = get_post_field('post_content', $imageForUpdate->post_id);
    $processingResult = 'nok';
    $reason = '';
    if (!empty($post_content)) {
        $new_content = aiit_replace_caption_content($post_content, $imageForUpdate->image_id, $imageForUpdate->post_id, $job_id);
        if ($new_content != ''){
            wp_update_post(array('ID' => $imageForUpdate->post_id, 'post_content' => $new_content));
            $processingResult = 'ok';
        } else {
            $reason = 'Failed to get result';
        }
    } else {
        $reason = 'Failed to load post content';
    }
    if($processingResult == "nok"){
        Global $wpdb;
        $table_log = $wpdb->prefix . 'aiit_processed_images';       
        $wpdb->insert($table_log, array('image_name' => basename(get_attached_file($imageForUpdate->image_id)), 'image_id' => $imageForUpdate->image_id, 'post_id' => $imageForUpdate->post_id, 'job_id' => $job_id, 'status' => 'failed', 'reason' => $reason), array('%s', '%d', '%d', '%d', '%s', '%s'));
    }
    return $processingResult;
}

function aiit_replace_caption_content($post_content, $image_id, $post_id, $job_id){
    $content = $post_content;
    $try_update_all = 'false';
    if($image_id == -1){
        $try_update_all = 'true';
    } else {
        $altTextML = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        $captionML = get_post_field('post_excerpt', $image_id);
        $titleML = get_post_field('post_title', $image_id);
    }
    $images_updated = 0;
    $updateTitle = (get_option('aiit_generate_title') === 'on') ? "true" : "false";
    $updateCaption = (get_option('aiit_generate_caption') === 'on') ? "true" : "false";
    Global $wpdb;
    $table_log = $wpdb->prefix . 'aiit_processed_images';
    $pattern = '/(\[caption\s*.*?\])(<img.*?>)(.*?)\[\/caption\]/i';
    preg_match_all($pattern, $post_content, $captions, PREG_SET_ORDER);

    foreach ($captions as $caption) {

        $imgTag = $caption[2];
        $current_caption = $caption[3];
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;
        $doc->loadHTML('<?xml encoding="UTF-8">' . $imgTag);
        $img = $doc->getElementsByTagName('img')->item(0);
        if($img){
            $imgClass = $img ? $img->getAttribute('class') : '';
            if (preg_match('/wp-image-(\d+)/', $imgClass, $captions)) {
                $updated = 'false';
                $imgID = $captions[1];
                if($imgID == $image_id || $try_update_all == 'true'){
                    if($try_update_all == 'true'){
                        $altTextML = get_post_meta($imgID, '_wp_attachment_image_alt', true);
                        $captionML = get_post_field('post_excerpt', $imgID);
                        $titleML = get_post_field('post_title', $imgID);
                    }
                    $current_alt = $img ? $img->getAttribute('alt') : '';
                    $current_title = $img ? $img->getAttribute('title') : '';
                    if ($altTextML != $current_alt){
                        $img->setAttribute('alt', $altTextML);
                        $updated = 'true';
                    }
                    
                    if ($updateTitle === "true" && $titleML != $current_title) {
                        $img->setAttribute('title', $titleML);
                        $updated = 'true';
                    }
                    $captionTxt = $current_caption;
                    if ($updateCaption === "true" && $captionML != $captionTxt){
                        $captionTxt = $captionML;
                        $updated = 'true';
                    }

                    if ($updated == 'true'){
                        $imgTagNew = $doc->saveHTML($img);
                        $contentNew = $caption[1] . $imgTagNew . $captionTxt . "[/caption]";
                        $content = str_replace($caption[0], $contentNew, $content);
                        $wpdb->insert($table_log, array('image_name' => basename(get_attached_file($imgID)), 'image_id' => $imgID, 'post_id' => $post_id, 'job_id' => $job_id, 'old_alt' => $current_alt, 'old_caption' => $current_caption, 'old_title' => $current_title, 'status' => 'ok'), array('%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s'));
                        $images_updated++;
                    }
                }
            }
        }
    }
    if($images_updated == 0){
        $content = '';
    }
    return $content;
}

function aiit_stop_update() {
    if (!isset($_POST['security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['security'])), 'aiit_update_nonce')) {
        wp_send_json_error('Permission check failed');
    }
    global $wpdb;
    $table_job = $wpdb->prefix . 'aiit_bckg_processing';
    $jobs_in_progress = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aiit_bckg_processing WHERE `status` = 'In progress'");
    $table_name = $wpdb->prefix . 'aiit_posts_update';
    $delete = $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}aiit_posts_update");
    foreach ( $jobs_in_progress as $job ) {
        $wpdb->update($table_job, array('status' => 'Canceled manually'), array('id' => $job->id));
    }
}

function aiit_update_on_save_post($post_id, $post, $update){
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (get_option('aiit_replace_on_save') != 'on'){
        return;
    }   

    if (!aiit_oai_k_valid() || !aiit_k_valid()) {
        return;
    }    
    Global $wpdb;
    $latest_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}aiit_processed_images WHERE post_id = %s ORDER BY id DESC LIMIT 1", $post_id));    
    $currentTimestamp = current_time('timestamp');
    $diff = 100;
    if ($latest_record && $latest_record->created_on){
        $dateTime = new DateTime($latest_record->created_on);
        $timestamp = $dateTime->getTimestamp();
        $diff = $currentTimestamp - $timestamp;
    }
    if ($diff > 3) {
        if (!empty($post->post_content)) {
            $postsUpdated = 'false';
            $new_content = aiit_replace_figure_content($post->post_content, -1, $post_id, -1);
            if ($new_content != ''){
                $postsUpdated = 'true';
            } else {
                $new_content = $post->post_content;
            }
            $new_content2 = aiit_replace_caption_content($new_content, -1, $post_id, -1);
            if ($new_content2 != ''){
                $postsUpdated = 'true';
                $new_content = $new_content2;
            }

            if ($postsUpdated == 'true'){
                wp_update_post(array('ID' => $post_id, 'post_content' => $new_content));
            }
        }
    }
}

