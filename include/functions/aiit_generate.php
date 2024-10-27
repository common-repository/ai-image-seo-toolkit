<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function aiit_generate_alt_text($post_ID, $job_id) {
    if (!aiit_tuning_parameters_set()){
        error_log('aiit_generate_alt_text, tuning parameters not set.');
        exit;
    }
    $processingResult = array(
        'status'  => "ok",
        'error'   => ""
    );
    $processed = "ok";
    error_log('aiit_generate_alt_text, start.');
    $reason = "";
    $alt_returned = false;
    Global $wpdb;
    $table_optimization = $wpdb->prefix . 'aiit_image_optimization';
    $table_log = $wpdb->prefix . 'aiit_processed_images';
    $file_name = basename(get_attached_file($post_ID));
    $current_caption = wp_get_attachment_caption($post_ID);
    $current_alt = get_post_meta($post_ID, '_wp_attachment_image_alt', true);
    $current_title = get_the_title($post_ID);

    if (wp_attachment_is_image($post_ID)) {
        list($width, $height) = getimagesize(get_attached_file($post_ID));
        $min_width = get_option('aiit_min_width');
        $min_height = get_option('aiit_min_height');

        if ($width != false && $height != false && $width >= $min_width && $height >= $min_height){
             $keySet = aiit_k_valid();
             $alt_text_prompt = get_option('aiit_alt_text_prompt');
            if (!empty($alt_text_prompt)) {
                $image_logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}aiit_image_optimization WHERE `image_id` = %d", $post_ID));
                foreach ( $image_logs as $image_log ) {
                    $wpdb->delete($table_optimization, array('id' => $image_log->id), array('%d'));
                }

                $alt_text = aiit_call_api($post_ID);
                if ($alt_text && $alt_text['data'] != "") {
                    $alt_returned = true;
                    $updateTitle = (get_option('aiit_generate_title') === 'on') ? "true" : "false";
                    $updateCaption = (get_option('aiit_generate_caption') === 'on') ? "true" : "false";
                    $updateDescription = (get_option('aiit_generate_description') === 'on') ? "true" : "false";  

                    if ($updateTitle === "true" && $keySet) {
                        $title_prompt = get_option('aiit_title_prompt');
                        $image_title = aiit_generate_title($alt_text['data'], $title_prompt);
                        if ($image_title['data'] && strlen($image_title['data']) > 60){
                            $title_prompt2 = get_option('aiit_title_prompt2');
                            $image_title = aiit_generate_title($image_title['data'], $title_prompt2);
                        }
                        if (!$image_title['data'] || $image_title['data'] == "") {
                            If ($image_title['error'] != ""){
                                $reason .= 'Title:' . $image_title['error'];
                            } else {
                                $reason .= "Title not received";
                            }
                            $processed = "nok";
                        }
                    }

                    if ($updateCaption === "true" && $keySet && $processed === "ok") {
                        $caption_prompt = get_option('aiit_caption_prompt');
                        $caption = aiit_generate_caption_description($alt_text['data'], $caption_prompt);
                        if (!$caption['data'] || $caption['data']  =="") {
                            If ($caption['error'] != ""){
                                $reason .= 'Caption:' . $caption['error'];
                            } else {
                                $reason .= "Caption not received";
                            }
                            $processed = "nok";
                        }
                    }

                    if ($updateDescription === "true" && $keySet && $processed === "ok") {
                        $description_prompt = get_option('aiit_description_prompt');
                        $description = aiit_generate_caption_description($alt_text['data'], $description_prompt);
                        if (!$description['data'] || $description['data']  =="") {
                            If ($description['error'] != ""){
                                $reason .= 'Description:' . $description['error'];
                            } else {
                                $reason .= "Description not received";
                            }
                            $processed = "nok";
                        }
                    }

                    if ($keySet && $processed === "ok"){
                        update_post_meta($post_ID, '_wp_attachment_image_alt', sanitize_text_field($alt_text['data']));
                        if ($updateTitle === "true" && $image_title['data']) {
                            wp_update_post(array(
                                'ID' => $post_ID,
                                'post_title' => sanitize_text_field($image_title['data'])
                            ));
                        }
                        if ($updateCaption === "true" && $caption['data']) {
                            wp_update_post(array(
                                'ID' => $post_ID,
                                'post_excerpt' => sanitize_text_field($caption['data'])
                            ));
                        }
                        if ($updateDescription === "true" && $description['data']) {
                            wp_update_post(array(
                                'ID' => $post_ID,
                                'post_content' => sanitize_text_field($description['data'])
                            ));
                        }
                    } else {
                        update_post_meta($post_ID, '_wp_attachment_image_alt', sanitize_text_field($alt_text['data']));
                        $processed = "ok";
                    }

                    if ($processed === "ok"){
                        $wpdb->insert($table_log, array('image_name' => $file_name, 'image_id' => $post_ID, 'job_id' => $job_id, 'old_alt' => $current_alt, 'old_caption' => $current_caption, 'old_title' => $current_title, 'status' => 'ok'), array('%s', '%d', '%d', '%s', '%s', '%s', '%s'));
                    }

                } else {
                    If ($alt_text['error'] != ""){
                        $reason = $alt_text['error'];
                        $error1 = "content that is not allowed";
                        $error2 = "uploaded an unsupported image";
                        if (strpos($reason, $error1) !== false || strpos($reason, $error2) !== false){
                            //skip treating as error
                        } else {
                            $processed = "nok";
                        }
                    } else {
                        $reason = "ALT text not received";
                        $processed = "nok";
                    }
                }
            } else {
                $reason = "Prompt is not defined";
                $processed = "nok";
            }
        } else {
            $reason = "Minimal size not met";
            $processed = "nok";
        }
    } else {
        $reason = "Attachment is not an image";
        $processed = "nok";
    }
    $processingResult['status'] = $processed;
    if($processed == "nok" || $alt_returned == false){
        $processingResult['error'] = $reason;
        $wpdb->insert($table_log, array('image_name' => $file_name, 'image_id' => $post_ID, 'job_id' => $job_id, 'status' => 'failed', 'reason' => $reason), array('%s', '%d', '%d', '%s', '%s'));
    }
    return $processingResult;
}

function aiit_generate_title($alt_text, $title_prompt) {
    $api_key = get_option('aiit_openai_api_key'); 
    $body = wp_json_encode(array(
        "model" => "gpt-3.5-turbo",
        "messages" => array(
            array("role" => "system", "content" => "You are a helpful assistant."),
            array("role" => "user", "content" => $title_prompt . ": " . $alt_text) 
        )
    ));

    // Make the API call
    if($GLOBALS['aiit_dev_mode'] == "true"){
        $responseAPI = "";
        error_log('aiit_generate_title:, aiit_dev_mode enabled, call skipped');
    } else {
        $responseAPI = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ),
            'body' => $body,
            'method' => 'POST',
            'data_format' => 'body',
            'timeout' => 30 
        )); 
    }

    // Handle the response
    if (is_wp_error($responseAPI)) {
        $err = substr($responseAPI->get_error_message(), 0, 100);
        $response = array(
            "data" => "", 
            "error" => $err
        );
        return $response;
    }

    $response_body = wp_remote_retrieve_body($responseAPI);
    $result = json_decode($response_body, true);

    if (!empty($result['choices'][0]['message']['content'])) {
        // Remove trailing periods from the generated title
        $clean_title = rtrim($result['choices'][0]['message']['content'], '.');
        $response = array(
            "data" => $clean_title, // Use the sanitized title
            "error" => ""
        );
        return $response;
    } else {
        $response = array(
            "data" => "", 
            "error" => print_r($responseAPI, true),
        );
        return $response;
    }
}

function aiit_generate_caption_description($alt_text, $prompt) {
    $api_key = get_option('aiit_openai_api_key');

    // Construct the body for the API request
    $body = wp_json_encode(array(
        "model" => "gpt-3.5-turbo",
        "messages" => array(
            array("role" => "system", "content" => "You are a SEO text writer for images. Tailor answers to given prompts."),
            array("role" => "user", "content" => $prompt . ": " . $alt_text)
        )
    ));

    // Make the API call
    if($GLOBALS['aiit_dev_mode'] == "true"){
        $responseAPI = "";
        error_log('aiit_generate_caption_description:, aiit_dev_mode enabled, call skipped');
    } else {
        $responseAPI = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ),
            'body' => $body,
            'method' => 'POST',
            'data_format' => 'body',
            'timeout' => 30 // Set a timeout
        ));
    }

    // Handle the response
    if (is_wp_error($responseAPI)) {
        $err = substr($responseAPI->get_error_message(), 0, 100);
        $response = array(
            "data" => "", 
            "error" => $err
        );
        return $response;
    }

    $response_body = wp_remote_retrieve_body($responseAPI);
    $result = json_decode($response_body, true);

    if (!empty($result['choices'][0]['message']['content'])) {
        $response = array(
            "data" => $result['choices'][0]['message']['content'], 
            "error" => ""
        );
        return $response;
    } else {
        error_log('aiit_generate_caption_description:, result empty');
        $response = array(
            "data" => "", 
            "error" => print_r($responseAPI, true),
        );
        return $response;
    }
}

function aiit_call_api($post_ID) {
    $image_path = wp_get_attachment_url($post_ID);

    $responseImg = wp_remote_get($image_path);
    $proceed = false;
    $error = "Unknown error";

    if (!is_wp_error($responseImg) && wp_remote_retrieve_response_code($responseImg) === 200) {
        $file_path = get_attached_file($post_ID);
        if ($file_path) {
            $file_size_bytes = filesize($file_path);
            $file_size_mb = $file_size_bytes / (1024 * 1024);
            //error_log('aiit_call_api, image_path: '. print_r($image_path, true) . ' file_size_mb: '. print_r($file_size_mb, true));
            if ($file_size_mb < 20) {
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
        $error = is_wp_error($responseImg) ? $responseImg->get_error_message() : 'Unknown error';
    }

    if ($proceed === true) {
        $image_data = base64_encode(wp_remote_retrieve_body($responseImg));

        $api_key = get_option('aiit_openai_api_key');
        $prompt = get_option('aiit_alt_text_prompt');
    
        $body = wp_json_encode(array(
            "model" => "gpt-4o",
            "messages" => array(
                array(
                    "role" => "user",
                    "content" => array(
                        array(
                            "type" => "text",
                            "text" => $prompt
                        ),
                        array(
                            "type" => "image_url",
                            "image_url" => array(
                                "url" => "data:image/jpeg;base64," . $image_data
                            )
                        )
                    )
                )
            ),
            "max_tokens" => 300
        ));
    
        // Make the API call
        if($GLOBALS['aiit_dev_mode'] == "true"){
            $responseAPI = "";
            error_log('aiit_generate_title:, aiit_dev_mode enabled, call skipped');
        } else {
            $responseAPI = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $api_key,
                ),
                'body' => $body,
                'method' => 'POST',
                'data_format' => 'body',
                'timeout' => 30 // Set the timeout to 30 seconds
            ));
        }

        if (is_wp_error($responseAPI)) {
            $err = substr($responseAPI->get_error_message(), 0, 100);
            $response = array(
                "data" => "", 
                "error" => $err
            );
            return $response ;
        }
        $response_body = wp_remote_retrieve_body($responseAPI);
        $result = json_decode($response_body, true);    
        if (!empty($result['choices'][0]['message']['content'])) {
            $response = array(
                "data" => $result['choices'][0]['message']['content'], 
                "error" => ""
            );
            return $response;
        } else {
            if (!empty($result['error']['message'])){
                $error_message = $result['error']['message'];
                error_log('aiit_call_api, error_message: '. print_r($error_message, true));
                $response = array(
                    "data" => "", 
                    "error" => print_r($error_message, true),
                );
            } else {
                if($GLOBALS['aiit_dev_mode'] == "true"){
                    $response = array(
                        "data" => "aiit_dev_mode request skipperd, simulate alt text", 
                        "error" => "",
                    );
                } else {
                    error_log('aiit_call_api:, result empty');
                    $response = array(
                        "data" => "", 
                        "error" => print_r($responseAPI, true),
                    );
                }
            }
            return $response;  
        }

    } else {
        $response = array(
            "data" => "", 
            "error" => $error,
        );
        return $response; 
    }
}