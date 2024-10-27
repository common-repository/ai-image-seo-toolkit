<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function aiit_add_bulk_action( $bulk_actions ) {
    if (aiit_k_valid() && aiit_tuning_parameters_set()){
        $bulk_actions['optimize_seo'] = 'Optimize SEO';
    }
        return $bulk_actions;
}

function aiit_handle_bulk_action( $redirect_to, $doaction, $post_ids ) {
    if ($doaction !== 'optimize_seo' || !aiit_k_valid() || !aiit_tuning_parameters_set()) {
        return $redirect_to;
    }
    global $wpdb;
    $table_job = $wpdb->prefix . 'aiit_bckg_processing';
    $postCount = count($post_ids);
    if($postCount > 0){
        $wpdb->insert($table_job, array('initial_count' => $postCount, 'status' => 'In progress', 'type' => 'Generate'), array('%d', '%s', '%s'));
        $job_id = $wpdb->insert_id;
        $errors_in_row = 0;
        $processed_images = 0;
        $newStatus = 'Completed';
        foreach ($post_ids as $post_id) {
            if (wp_attachment_is_image($post_id)) {
                $processingResult = aiit_generate_alt_text($post_id, $job_id);
                if($processingResult['status'] == "ok"){
                    $processed_images += 1;
                    $errors_in_row = 0;
                } else if ($processingResult['status'] == "nok"){
                    $errors_in_row += 1;
                    if($errors_in_row == 5){
                        $newStatus = 'Cancelled due errors';
                        break;
                    }
                }
            }
        }
        $wpdb->update($table_job, array('status' => $newStatus, 'processed_count' => $processed_images), array('id' => $job_id)); 
    }
    if ($processed_images > 0){
        $message = $processed_images; 
        /* translators: %s is the number of optimized images */
        $message .= esc_html(_n(' image', ' images', esc_html($processed_images), 'text-domain'));
        $message .= ' optimized';
        set_transient('aiit_handle_bulk_action_notice_message', array('message' => $message, 'type' => 'success'), 3); // 3 seconds expiration
    } else {
        $message = '0 images optimized';
        set_transient('aiit_handle_bulk_action_notice_message', array('message' => $message, 'type' => 'error'), 3);
    }


    aiit_bulk_action_admin_notice($processed_images);
    return $redirect_to;
}

function aiit_bulk_action_admin_notice() {
    $notice = get_transient('aiit_handle_bulk_action_notice_message');
    if ($notice && $notice != "") {
        $class = ($notice['type'] === 'error') ? 'notice-error' : 'notice-success';
        echo "<div class='notice fade " . esc_html($class) . "'><p>" . esc_html($notice['message']) . "</p></div>";
    }
}

## Gallery upper corner
function aiit_add_meta_box() {
    add_meta_box(
        'aiit-seo-optimizer',
        'AI Image SEO Toolkit',
        'aiit_seo_optimizer_callback',
        'attachment',
        'side',
        'high'
    );
}

function aiit_seo_optimizer_callback($post) {
    if (aiit_tuning_parameters_set()){
        echo '<button id="aiit-process-single" class="button aiit-button" postid="' . esc_html($post->ID) . '">Generate SEO texts for the image</button><p id="aiit-image-proc-status"></p>';
        echo '<p>For text tuning go to <a href="' . esc_url(admin_url('admin.php?page=ai-image-seo-toolkit-tuning')) . '">Tuning page</a></p>';
    } else {
        echo '<p>Image metadata generation is available after <a href="';
        echo esc_url(admin_url('admin.php?page=ai-image-seo-toolkit-settings#key'));
        echo '">adding keys</a> and setting up <a href="';
        echo esc_url(admin_url('admin.php?page=ai-image-seo-toolkit-tuning'));
        echo '">tuning parameters</a>.</p>';
    }
}

function aiit_ajax_optimize_image() {
    if (!isset($_POST['security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['security'])), 'aiit_general_nonce')) {
        wp_send_json_error('Permission check failed');
    }
    $post_id = sanitize_text_field($_POST['postid']);
    if (!$post_id) {
        error_log('aiit_ajax_optimize_image, Invalid Post ID');
        wp_send_json_error(array('message' => 'Invalid post ID.'));
        return;
    }

    $processedOK = aiit_generate_alt_text($post_id, -1);

    if ($processedOK['status'] == "ok") {
        wp_send_json_success($processedOK);

    } else {
        error_log('aiit_ajax_optimize_image, Failed to optimize image');
        wp_send_json_error(array('message' => $processedOK['error']));
    }
}

function aiit_new_image_uploaded($attachment_id){
    $generate = get_option('aiit_generate_on_upload');
    if ($generate == "on"){
        if (aiit_tuning_parameters_set()){
            aiit_generate_alt_text($attachment_id, 0);
        }
    }

}
