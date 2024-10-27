<?php
if ( ! defined( 'ABSPATH' ) ) exit;
## BULK OPTIMIZATION PAGE
function aiit_image_seo_toolkit_bulk_optimization_page() {
    ?>
        <div class="main-container">
            <div class="column1">
                <h1>Bulk Generation</h1>
                <?php
                    if (!aiit_k_valid() || !aiit_tuning_parameters_set()){
                        echo '<h5 style="padding-bottom: 10px;">Bulk Optimization is available in Pro version after <a href="';
                        echo esc_url(admin_url('admin.php?page=ai-image-seo-toolkit-settings#key'));
                        echo '">adding keys</a> and setting up <a href="';
                        echo esc_url(admin_url('admin.php?page=ai-image-seo-toolkit-tuning'));
                        echo '">tuning parameters</a>.<br></h5>';
                    } else {
                        aiit_show_bulk_form();
                    }
                ?>
            </div>
            <div class="column2">
                <h3>Information</h3>
                <hr>
                <div class="accordion">
                <h2>Test before text bulk generation</h2>
                <div>
                    Always remember to test image text generation on the Tuning page. That's important to avoid generating texts that don't satisfy you or aren't relevant to your website's content.
                </div>
                <h2>Why some images are not being processed</h2>
                <div>
                The reason some images are not being processed can vary. Some images may be interpreted by AI as safety issues, such as captcha recognition, explicit content, or other restrictions built into the OpenAI system. Additionally, images larger than 20MB are not processed.
                </div>
                <h2>Limited image count differs from total images </h2>
                <div>
                If you set a limit on the image count, the actual number of images processed may differ from the total number calculated. For instance, if you limit the image count to '100', and then click 'Get Image Total', the number shown may be different. This discrepancy occurs because on the Tuning page you have set an image size limitation and images below this size limit are not included in the total image count that will be generated.
                </div>
                <h2>Controlling text in images</h2>
                <div>
                    On the <a href="/page=ai-image-seo-toolkit-settings">Settings page</a> you can specify what texts (ALTs, Titles, Descriptions or Captions) need to be generated. <br></br>Additionally, you can select which images to update with new texts under "Generate content for". These settings will help you to avoid overwriting existing image texts if you have specific ones you want to keep.
                </div>
                <h2>Plugin works with the texts in Media Library</h2>
                <div>
                    If you are using Gutenberg or Classic editor and you've added custom Titles, ALTs, Captions or Descriptions for images in a post or page, these posts or pages won't be affected by the text generation. Go to <a href="/page=ai-image-seo-toolkit-update-pages">Update Content</a> - it will help you to update these images that are published on older posts and pages with the newly written texts from the Media Library.
                </div>
                <h2>Generated text precision</h2>
                <div>
                    We created the plugin to ensure image texts are precise and free of errors, but occasionally some may slip through. You can re-generate texts for those you don't like or where you spot mistakes. 
                </div>
            </div>
            <?php aiit_genarate_banner_html() ?>
        </div>
    <?php
}

function aiit_show_bulk_form(){
    ?>
        <p style="padding-bottom: 10px;">Control image text generation by selecting images requiring texts and choosing content types for creation. Modify settings, then click "Get Image Total" to begin. Keep in mind, you can stop the process at any time. </p>
        <hr>
        <div class="tab-container">
            <div>
                <form id="bulkSettings" action="">
                    <div class="settings-section">
                        <div class="title-column-wide">
                            <h2>Generate content for</h2>
                            <span class="tooltip-icon" data-tooltip="This option allows you to target specific images based on their current content status. Make your selection to either focus on images needing certain SEO texts or extend the text generation to include all images on your site.">?</span>
                        </div>
                        <div class="content-column">
                            <input type="hidden" name="aiit_selection_criteria" id="aiit_selection_criteria" maxlength="200" value="<?php echo esc_attr(get_option('aiit_selection_criteria')); ?>">
            
                            <div class="custom-dropdown">
                                <?php echo wp_kses_post(aiit_show_selection_cr_dropdown()); ?>
                            </div>
                        </div>
                    </div>

                    <div class="settings-section" style="margin-bottom: 30px;">
                        <div class="title-column-wide">
                            <h2>Limit images count</h2>
                            <span class="tooltip-icon" data-tooltip="Set the number of images you wish to process. Useful for testing purposes. To process all images filtered based to previously set settings, leave the field empty.">?</span>
                        </div>
                        <div class="content-column">
                            <input type="number" class="full-width-input" id="aiit_limit_images" name="aiit_limit_images" value="<?php echo esc_attr(get_option('aiit_limit_images')); ?>" placeholder="Leave empty to process all images" maxlength="7">
                        </div>
                    </div>
                    <button id="aiit-prepare-optimization" class="button button-2 button-secondary">Get Image Total</button>
                </form>

                <div id="image-count-container" class="total-images-container">
                    Total Images to Optimize: <span id="aiit-image-count">-</span>
                </div>

                <hr>

                <div class="button-container">
                    <button id="aiit-start-optimization-button" class="button button-primary" disabled>Start Text Generation</button>
                </div>
 
            </div>
            <div id="progress-container">
                <div id="progress-bar"></div>
                <div id="percent-text"></div>
            </div>
            <div id="stop-optimization-container" style="display: none;">
                <a href="#" id="stop-optimization-link">Stop the optimization</a> 
                <span class="tooltip-icon" data-tooltip="Stop the optimization at any time and restart where you left off. You will need to start a new session to resume what was previously stopped. Optimized images up to the point of stopping are saved.">?</span>
                <div id="stop-optimization-loader"></div>               
            </div>
            <div class="optimization-table-container">
                <h3 style="padding-top: 30px;">Text generation history</h3>
                <div id="aiit-last-job-data"></div>
            </div>
        </div>
    <?php
}

## Accordion style menu
function aiit_open_specific_accordion_section() {
    
    if (is_singular() && has_shortcode(get_post()->post_content, 'accordion_shortcode')) {
      
      wp_enqueue_script('jquery');
      wp_enqueue_script('bulk-script', plugin_dir_url(__FILE__) . '../js/bulk-script.js', array('jquery'), '1.0', true);
      wp_localize_script('bulk-script', 'accordion_vars', array('section_to_open' => 1));
    }
}   


function aiit_show_selection_cr_dropdown(){
    $selection_criteria = get_option('aiit_selection_criteria');
    $selected_option = "Select an option";
    $dropdown = array(
                    array(
                        'value' => 'all-images',
                        'title' => "All images",
                        'description' => "Generate texts for all images in your library, regardless of their existing SEO texts",
                    ),
                    array(
                        'value' => 'alt-text-empty',
                        'title' => "Images without 'ALTs'",
                        'description' => "Generate texts for all images that don't have ALTs yet",
                    ),
                    array(
                        'value' => 'titles-empty',
                        'title' => "Images without 'Titles'",
                        'description' => "Generate texts for all images that don't have Titles yet",
                    ),
                    array(
                        'value' => 'captions-empty',
                        'title' => "Images without 'Captions'",
                        'description' => "Generate texts for all images that don't have Captions yet",
                    ),
                    array(
                        'value' => 'descriptions-empty',
                        'title' => "Images without 'Descriptions'",
                        'description' => "Generate texts for all images that don't have Descriptions yet",
                    )
                    );
    if ($selection_criteria){
        foreach ($dropdown as $item) {
            if ($item['value'] === $selection_criteria) {
                $selected_option = $item['title'];
            }
        }
    }
    echo    '<div class="dropdown-selected">
                <span class="selected-text">' . esc_html($selected_option) . '</span>
                <span class="dropdown-caret"></span>
            </div>
            <div class="dropdown-options">';
    
    foreach ($dropdown as $item) {
        echo    '<div class="dropdown-option" data-value="' . esc_html($item['value']) . '">
                    <div class="option-title">' . esc_html($item['title']) . '</div>
                    <div class="option-description">' . esc_html($item['description']) . '</div>
                </div>';
    }
    echo    '</div>';
}

function aiit_filter_where_empty($where, &$wp_query){
    global $wpdb;
    if ( $empty_field = $wp_query->get( 'empty_field' ) ) {
        if($empty_field === 'title'){
            $where .= ' AND (' . $wpdb->posts . '.post_title IS NULL OR ' . $wpdb->posts . ".post_title = '')";
        } else if ($empty_field === 'caption'){
            $where .= ' AND (' . $wpdb->posts . '.post_excerpt IS NULL OR ' . $wpdb->posts . ".post_excerpt = '')";
        } else if ($empty_field === 'description'){
            $where .= ' AND (' . $wpdb->posts . '.post_content IS NULL OR ' . $wpdb->posts . ".post_content = '')";
        }
    }
    return $where;
}

## Popuate optimization table
function aiit_populate_optimization_table() {
    if (!isset($_POST['security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['security'])), 'aiit_bulk_nonce')) {
        wp_send_json_error('Permission check failed');
    }
    if (!aiit_oai_k_valid() || !aiit_k_valid()) {
        wp_send_json_error('Invalid OpenAI API or License Key provided.');
    }   
    global $wpdb;

    $serializedFormData = sanitize_text_field($_POST['form_data']);
    aiit_update_bulk_settings($serializedFormData);

    $table_name = $wpdb->prefix . 'aiit_image_optimization';
    $delete = $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}aiit_image_optimization");

    $min_width = get_option('aiit_min_width');
    $min_height = get_option('aiit_min_height');

    $limit = intval(get_option('aiit_limit_images'));
    if (!$limit || $limit == 0){
        $limit = 10000;
    }
    $posts_per_page = 50;
    $search_criteria = [
                            'all-images' => [
                                'orderby' => 'date',
                                'order' => 'DESC',
                                'post_type' => 'attachment',
                                'post_mime_type' => 'image',
                                'post_status' => ['inherit'],
                                'posts_per_page' => $posts_per_page
                            ],
                            'alt-text-empty' => [
                                'orderby' => 'date',
                                'order' => 'DESC',
                                'post_type' => 'attachment',
                                'post_mime_type' => 'image',
                                'post_status' => ['inherit'],
                                'posts_per_page' => $posts_per_page,
                                'meta_query' => [
                                    'relation' => 'OR',
                                    [
                                        'key' => '_wp_attachment_image_alt',
                                        'compare' => 'NOT EXISTS' 
                                    ],
                                    [
                                        'key' => '_wp_attachment_image_alt',
                                        'value' => '',
                                        'compare' => '='
                                    ]
                                ]
                            ],
                            'titles-empty' => [
                                'orderby' => 'date',
                                'order' => 'DESC',
                                'post_type' => 'attachment',
                                'post_mime_type' => 'image',
                                'post_status' => ['inherit'],
                                'posts_per_page' => $posts_per_page,
                                'empty_field' => 'title',
                            ],
                            'captions-empty' => [
                                'orderby' => 'date',
                                'order' => 'DESC',
                                'post_type' => 'attachment',
                                'post_mime_type' => 'image',
                                'post_status' => ['inherit'],
                                'posts_per_page' => $posts_per_page,
                                'empty_field' => 'caption',
                            ],
                            'descriptions-empty' => [
                                'orderby' => 'date',
                                'order' => 'DESC',
                                'post_type' => 'attachment',
                                'post_mime_type' => 'image',
                                'post_status' => ['inherit'],
                                'posts_per_page' => $posts_per_page,
                                'empty_field' => 'description',
                            ]
    ];
    $selection_mode = get_option('aiit_selection_criteria', 'alt-text-empty');
    $args = $search_criteria[$selection_mode];
    $images_to_optimize = 0;
    $current_page = 1;

    do {
        $args['paged'] = $current_page;
        add_filter('posts_where', 'aiit_filter_where_empty', 10, 2 ); 
        $images = new WP_Query($args);
        remove_filter('posts_where', 'aiit_filter_where_empty', 10 );
        error_log('aiit_populate_optimization_table, current_page: '. print_r($current_page, true));
        if ($images->have_posts()) {
            while ($images->have_posts()) {
                $images->the_post();
                $image_id = get_the_ID();
                list($width, $height) = getimagesize(get_attached_file($image_id));
                $proceed = false;
                $error = "Unknown error";
                //error_log('aiit_populate_optimization_table, image: '. print_r($image_id, true));

                if ($width != false && $height != false && $width >= $min_width && $height >= $min_height) {
                    $file_path = get_attached_file($image_id);
                    if ($file_path) {
                        $file_size_bytes = filesize($file_path);
                        $file_size_mb = $file_size_bytes / (1024 * 1024);
                        if ($file_size_mb < 20) {
                            $image_path = wp_get_attachment_url($image_id);
                            $file_extension = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
                            $allowed_extensions = array('png', 'jpeg', 'jpg', 'webp', 'gif');
                            if (in_array($file_extension, $allowed_extensions)) {
                                $proceed = true;
                            } else {
                                $error = "File format not allowed";
                            }
                        } else {
                            $error = "20MB file size exceeded";
                        }
                    } else {
                        $error = "File path not found";
                    }
                } else {
                    $error = "Minimal size not met";
                }
                if ($proceed === true) {
                    $image_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}aiit_image_optimization WHERE `image_id` = %d", $image_id));
                    if (!$image_exists) {
                        $file_name = basename(get_attached_file($image_id));
                        $insert_result = $wpdb->insert($table_name, ['image_name' => $file_name, 'image_id' => $image_id, 'status' => 'due'], ['%s', '%d', '%s']);
                        if ($insert_result) {
                            $images_to_optimize++;
                        } else {
                            error_log("aiit_populate_optimization_table: Failed to add image - ID: {$image_id}, Name: {$file_name}");
                        }
                    }
                } else {
                    //error_log('aiit_populate_optimization_table, error?: '. print_r($error, true));
                }

                if($images_to_optimize == $limit){
                    break 2;
                }
            }
            wp_reset_postdata();
        } else {
            break;
        }
        $current_page++;
    } while ($images_to_optimize < $limit);
    wp_send_json_success($images_to_optimize);
}

function aiit_update_bulk_settings($serializedFormData){
    parse_str($serializedFormData, $formDataArray);
    $limit_images = "";
    $selection_criteria = "";
    if (array_key_exists('aiit_limit_images', $formDataArray)) {
        $limit_images = $formDataArray['aiit_limit_images'];
    }
    if (array_key_exists('aiit_selection_criteria', $formDataArray)) {
        $selection_criteria = $formDataArray['aiit_selection_criteria'];
    }
    update_option('aiit_limit_images', $limit_images);
    update_option('aiit_selection_criteria', $selection_criteria);
}

##schedule job
function aiit_schedule_optimization() {
    if (!isset($_POST['security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['security'])), 'aiit_bulk_nonce')) {
        wp_send_json_error('Permission check failed');
    }
    if (wp_schedule_single_event(time(), 'aiit_schedule_optimization_event')) {
        global $wpdb;
        $image_exists = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}aiit_image_optimization");
        wp_send_json_success(array('images' => $image_exists));
    } else {
        error_log('aiit_schedule_optimization - Failed to schedule the event.'); 
        wp_send_json_error(array('message' => 'Failed to optimize image.'));
    }
}

function aiit_stop_optimization() {
    if (!isset($_POST['security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['security'])), 'aiit_bulk_nonce')) {
        wp_send_json_error('Permission check failed');
    }
    global $wpdb;
    $table_job = $wpdb->prefix . 'aiit_bckg_processing';
    $jobs_in_progress = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aiit_bckg_processing WHERE `status` = 'In progress'");
    $delete = $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}aiit_image_optimization");
    foreach ( $jobs_in_progress as $job ) {
        $wpdb->update($table_job, array('status' => 'Canceled manually'), array('id' => $job->id));
    }
}



## Optimize image from Bulk page
function aiit_update_bulk() {
    if (!aiit_oai_k_valid() || !aiit_k_valid()) {
        wp_send_json_error('Invalid OpenAI API or License Key provided.');
    }
    global $wpdb;
    $table_job = $wpdb->prefix . 'aiit_bckg_processing';
    $image_exists = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}aiit_image_optimization");
    if ($image_exists>0){         
        //clean up existing jobs in progress
        $jobs_in_progress = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aiit_bckg_processing WHERE `status` = 'In progress' AND  `type` = 'Generate'");
        foreach ( $jobs_in_progress as $job ) {
            $wpdb->update($table_job, array('status' => 'Canceled'), array('id' => $job->id));
        }
        //add the new job
        $result = $wpdb->insert($table_job, array('initial_count' => $image_exists, 'status' => 'In progress', 'type' => 'Generate'), array('%d', '%s', '%s'));
        $job_id = null;
        if ($result !== false) {
            $job_id = $wpdb->insert_id;
            $processed_images = 0;
            $errors_in_row = 0;
            $newStatus = 'Completed';
            while ($image = aiit_fetchNextImage()) {
                $processingResult = aiit_generate_alt_text($image->image_id, $job_id);
                if($processingResult['status'] == "ok"){
                    $processed_images += 1;
                    $errors_in_row = 0;
                    $wpdb->update($table_job, array('processed_count' => $processed_images), array('id' => $job_id));
                } else if ($processingResult['status'] == "nok"){
                    $errors_in_row += 1;
                    if($errors_in_row == 15){
                        $newStatus = 'Canceled due errors';
                        break;
                    }
                }
                aiit_removeProcessedRecord($image->id);
            }
            if($processed_images == 0 && $newStatus == 'Completed'){
                $newStatus = 'Failed';
            }
            $jobRow = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}aiit_bckg_processing WHERE `id` = %d", $job_id));
            if($jobRow && $jobRow->status == 'In progress' ){
                $wpdb->update($table_job, array('status' => $newStatus), array('id' => $job_id));  
            }          
        } else {
            error_log('aiit_update_bulk, failed to insert job'); 
        }
    } else {
        error_log('aiit_update_bulk, no images to process');
    }
}

function aiit_fetchNextImage() {
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aiit_image_optimization LIMIT 1");
}

function aiit_removeProcessedRecord($id) {
    global $wpdb;
    $table_optimization = $wpdb->prefix . 'aiit_image_optimization';
    $wpdb->delete($table_optimization, array('id' => $id));
}

## Jobs information input
function aiit_job_data_callback(){
    if (!isset($_POST['security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['security'])), 'aiit_bulk_nonce')) {
        wp_send_json_error('Permission check failed');
    }
    global $wpdb;
    $last_jobs = wp_cache_get('aiit_job_data_callback_jobs');
    if ($last_jobs === false) {
        $last_jobs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aiit_bckg_processing WHERE `type` = 'Generate' ORDER BY id DESC LIMIT 3", ARRAY_A);
        wp_cache_set('aiit_job_data_callback_jobs', $last_jobs, '', 3);
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


  