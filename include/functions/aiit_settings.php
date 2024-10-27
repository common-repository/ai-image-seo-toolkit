<?php
if ( ! defined( 'ABSPATH' ) ) exit;


function aiit_enqueue_admin_styles($hook) {
    // Enqueue the style only on your plugin's settings page
    if (strpos($hook, 'post.php') !== false && $post_id = get_the_ID()) {
        wp_register_style( 'aiit_post_styles', plugins_url('../css/aiit-post.css',  __FILE__) );
        wp_enqueue_style( 'aiit_post_styles' );
    }
    if (strpos($hook, 'ai-image-seo-toolkit') !== false) {
        wp_register_style( 'aiit_shared_styles', plugins_url('../css/aiit-shared.css',  __FILE__) );
        wp_enqueue_style( 'aiit_shared_styles' );
    }
    if ('ai-image-seo-toolkit_page_ai-image-seo-toolkit-tuning' === $hook) {
        wp_register_style( 'aiit_tuning_styles', plugins_url('../css/aiit-tuning.css',  __FILE__) );
        wp_enqueue_style( 'aiit_tuning_styles' );
    }
    if ('ai-image-seo-toolkit_page_ai-image-seo-toolkit-bulk-optimization' === $hook) {
        wp_register_style( 'aiit_bulk_styles', plugins_url('../css/aiit-bulk.css',  __FILE__) );
        wp_enqueue_style( 'aiit_bulk_styles' );
    }
    if ('ai-image-seo-toolkit_page_ai-image-seo-toolkit-update-pages' === $hook) {
        wp_register_style( 'aiit_update_styles', plugins_url('../css/aiit-update.css',  __FILE__) );
        wp_enqueue_style( 'aiit_update_styles' );
    }
}

function aiit_enqueue_admin_scripts($hook) {
    if ('post.php' === $hook) {
        wp_register_script('aiit_admin_script', plugins_url('../js/aiit-admin.js',  __FILE__), null, true);
        $translation_array = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aiit_general_nonce')
        );
        wp_localize_script('aiit_admin_script', 'aiit_ajax', $translation_array);
        wp_enqueue_script( 'aiit_admin_script' );
    }
    if ('ai-image-seo-toolkit_page_ai-image-seo-toolkit-tuning' === $hook) {
        wp_register_script('aiit_tuning_script', plugins_url('../js/aiit-tuning.js',  __FILE__), null, true);
        $translation_array = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aiit_tuning_nonce')
        );
        wp_localize_script('aiit_tuning_script', 'aiit_ajax', $translation_array);
        wp_enqueue_script( 'aiit_tuning_script' );
    }
    if ('ai-image-seo-toolkit_page_ai-image-seo-toolkit-bulk-optimization' === $hook) {
        wp_register_script('aiit_bulk_script', plugins_url('../js/aiit-bulk.js',  __FILE__), null, true);
        $translation_array = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aiit_bulk_nonce')
        );
        wp_localize_script('aiit_bulk_script', 'aiit_ajax', $translation_array);
        wp_enqueue_script( 'aiit_bulk_script' );
    }
    if ('ai-image-seo-toolkit_page_ai-image-seo-toolkit-settings' === $hook) {
        wp_register_script('aiit_settings_script', plugins_url('../js/aiit-settings.js',  __FILE__), null, true);
        $translation_array = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aiit_settings_nonce')
        );
        wp_localize_script('aiit_settings_script', 'aiit_ajax', $translation_array);
        wp_enqueue_script( 'aiit_settings_script' );
    }
    if ('ai-image-seo-toolkit_page_ai-image-seo-toolkit-update-pages' === $hook) {
        wp_register_script('aiit_update_script', plugins_url('../js/aiit-update.js',  __FILE__), null, true);
        $translation_array = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aiit_update_nonce')
        );
        wp_localize_script('aiit_update_script', 'aiit_ajax', $translation_array);
        wp_enqueue_script( 'aiit_update_script' );
    }
}

## Bulk optimize page ##
function aiit_add_admin_page() {
    $menu_icon_url = 'data:image/svg+xml;base64,CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgMjAwIDIwMCI+CiAgICA8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJtMTY0LjA3LDE2OS41OWwtMTkuMS0xMDMuNTItMTYuMTQsMi45OGMtMTAuMTMsMS44Ny0xNy42NC05LjIxLTEyLjE2LTE3LjkzbDE3LjQ3LTI3Ljc2TDk3LjAxLDAsMCwxNTQuMTlsMzcuMTMsMjMuMzYsNDguNjUtNzcuMzNjNS43NC05LjEyLDE5LjY5LTYuNDQsMjEuNjQsNC4xNWwxMy41LDczLjE4LDQzLjE0LTcuOTZaIi8+Cjwvc3ZnPgo=';

    add_menu_page(
        'AI Image SEO Toolkit',
        'AI Image SEO Toolkit',
        'manage_options',
        'ai-image-seo-toolkit',
        '',
        $menu_icon_url
    );
    add_submenu_page('ai-image-seo-toolkit', 'Tuning', 'Tuning', 'manage_options', 'ai-image-seo-toolkit-tuning', 'aiit_image_seo_toolkit_tuning_page');
    add_submenu_page('ai-image-seo-toolkit', 'Generate image metadata', 'Bulk generation', 'manage_options', 'ai-image-seo-toolkit-bulk-optimization', 'aiit_image_seo_toolkit_bulk_optimization_page');
    add_submenu_page('ai-image-seo-toolkit', 'Update pages', 'Update content', 'manage_options', 'ai-image-seo-toolkit-update-pages', 'aiit_image_seo_toolkit_update_pages_page');
    add_submenu_page('ai-image-seo-toolkit', 'Settings', 'Settings', 'manage_options', 'ai-image-seo-toolkit-settings', 'aiit_image_seo_toolkit_settings_page');
    remove_submenu_page('ai-image-seo-toolkit', 'ai-image-seo-toolkit');
}


## SETTINGS PAGE
function aiit_image_seo_toolkit_settings_page() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['aiit_save_settings']) && isset( $_POST['aiit_settings_keys_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['aiit_settings_keys_nonce'])), 'settings_keys' )) {
            aiit_save_settings();
        } else if (isset($_POST['aiit_save_setting_flags']) && isset( $_POST['aiit_settings_general_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['aiit_settings_general_nonce'])), 'settings_general' )){
            aiit_save_setting_flags();
        }
    }
    $key = aiit_k_valid();
    $domain = wp_parse_url(get_home_url(), PHP_URL_HOST);
    ?>
        <div class="main-container">
            <div class="column1">
                <h1>Settings</h1>
                <p>An AI-powered search engine optimization WordPress plugin that streamlines image text generation by creating smart & SEO-friendly titles, ALTs, captions and descriptions.</p>
                <hr>

                <?php settings_errors('ai-image-seo-toolkit-settings'); ?>

                <div class="nav-tab-wrapper">
                    <div class="nav-tab nav-tab-active" onclick="openTab(event, 'general')">General</div>
                    <div class="nav-tab" onclick="openTab(event, 'key')">Keys</div>
                </div>

                <div id="general" class="tab-content">
                    <form action="" method="post">
                        <?php wp_nonce_field( 'settings_general', 'aiit_settings_general_nonce' ); ?> 
                        <?php settings_fields('aiit_setting_flags'); ?>
                        <?php do_settings_sections('aiaiit_setting_flagsit'); ?>
                        <div style="margin-top: 20px;" class="settings-section-start-flex">
                                <div class="title-column-wide">
                                    <h2 style="margin-top: 0px;">Text generation</h2>
                                    <span style="margin-top: -12px;" class="tooltip-icon" data-tooltip="Disable this option if you don't want to generate texts for every uploaded image.">?</span>
                                </div>
                                <div class="content-column">
                                    <div class="toggle-switches">
                                        <div class="toggle-switch-container">
                                            <label class="toggle-switch">
                                            <input type="checkbox" id="aiit_generate_on_upload" name="aiit_generate_on_upload" <?php checked('on', get_option('aiit_generate_on_upload')); ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <label for="aiit_generate_on_upload" class="toggle-switch-label">During image upload</label>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <hr style="margin-top: 0px;">
                        <div style="margin-top: 10px;" class="settings-section-start-flex">
                            <div class="title-column-wide">
                                <h2 style="margin-top: 5px;">Need to generate</h2>
                                <span style="margin-top: -9px;" class="tooltip-icon" data-tooltip="Select the elements you want to generate for your images. Note that ALT text generation is automatically included and essential, as it forms the basis for generating the other content elements.">?</span>
                            </div>                
                            <div class="content-column">
                                <div class="checkbox-container">
                                    <input type="checkbox" id="generate-alt-text" name="generate-alt-text" checked disabled>
                                    <label for="generate-alt-text">ALTs</label><br>

                                    <input type="checkbox" id="generate_titles" name="generate_titles" <?php checked('on', get_option('aiit_generate_title')); ?> <?php if(!$key){echo 'disabled';} ?>>
                                    <label for="generate-titles">Titles</label><br>

                                    <input type="checkbox" id="generate_captions" name="generate_captions" <?php checked('on', get_option('aiit_generate_caption')); ?> <?php if(!$key){echo 'disabled';} ?>>
                                    <label for="generate-captions">Captions</label><br>

                                    <input type="checkbox" id="generate_descriptions" name="generate_descriptions" <?php checked('on', get_option('aiit_generate_description')); ?> <?php if(!$key){echo 'disabled';} ?>>
                                    <label for="generate-descriptions">Descriptions</label><br>
                                </div>
                            </div>
                        </div>
                        <hr style="margin-top: 0px;">
                        <div style="margin-top: 20px;" class="settings-section-start-flex">
                                <div class="title-column-wide">
                                    <h2 style="margin-top: 0px;">Replace text</h2>
                                    <span style="margin-top: -12px;" class="tooltip-icon" data-tooltip="Disable this option if you don't want to replace image information with the one from media library on content save.">?</span>
                                </div>
                                <div class="content-column">
                                    <div class="toggle-switches">
                                        <div class="toggle-switch-container">
                                            <label class="toggle-switch">
                                            <input type="checkbox" id="aiit_replace_on_save" name="aiit_replace_on_save" <?php checked('on', get_option('aiit_replace_on_save')); ?> <?php if(!$key){echo 'disabled';} ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <label for="aiit_replace_on_save" class="toggle-switch-label">During content save</label>
                                        </div>
                                    </div>
                                </div>
                        </div>       
                        <hr>
                        <input type="submit" name="aiit_save_setting_flags" value="Save Changes" class="button button-primary">
                    </form>        
                </div>

                <div id="key" class="tab-content" style="display:none;">
                    <p style="padding-bottom: 10px;">This plugin works with OpenAI Vision API technology. Get your OpenAI API key <a href="https://platform.openai.com/api-keys" target="_blank">here</a>.</p>
                    <h5 style="margin-top: -10px; margin-bottom: 16px;">You need an OpenAI account to use the API, which is a paid service. Purchase credits on the <a href="https://platform.openai.com/account/billing/overview" target="_blank">OpenAI billing page</a> in advance and control your costs effectively. Typically, optimizing one picture costs between $0.01 and $0.03, depending on the size of the image.</h5>
                
                    <form action="" method="post">
                        <?php wp_nonce_field( 'settings_keys', 'aiit_settings_keys_nonce' ); ?> 
                        <?php settings_fields('aiit'); ?>
                        <?php do_settings_sections('aiit'); ?>
                        <div class="settings-section">
                            <div class="title-column">
                                <h2>OpenAI API Key</h2>
                            </div>
                            <div class="content-column">
                                <div class="input-container">
                                    <input type="text" name="aiit_openai_api_key" id="aiit_openai_api_key" class="full-width-input" placeholder="" value="<?php echo esc_attr(get_option('aiit_openai_api_key')); ?>">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <?php
                            if(!$key){
                                echo '<p style="padding-bottom: 10px;">';
                                echo "To activate the PRO version of the AI Image SEO Toolkit plugin, you need to enter an active License Key. If you've already purchased the key, you can find it in ";
                                echo '<a href="https://fingerscrossed.dev/my-account/orders/" target="_blank">your account</a>. To get a key, you can <a href="https://fingerscrossed.dev/" target="_blank">purchase one here</a> and use <b>';
                                echo esc_html($domain);
                                echo "</b> as a domain.</p>";
                            } 
                        ?>
                        <div class="settings-section">
                            <div class="title-column">
                                <h2>License Key</h2>
                            </div>
                            <div class="content-column">
                                <div class="input-container">
                                    <input type="text" name="aiit_license_key" id="aiit_license_key" class="full-width-input" placeholder="" value="<?php echo esc_attr(get_option('aiit_license_key')); ?>">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <?php submit_button('Save Changes', 'primary', 'aiit_save_settings'); ?>
                    </form>
                </div>
                
            </div>
            <div class="column2">
                <div class="column-2-logo">
                    <img src="<?php echo esc_url(plugins_url( '../images/aiit-admin.svg', __FILE__ )); ?>" alt="Visit Fingers Crossed website">
                </div>
                <hr>
                <p>Check out what else we have created at <a href="https://fingerscrossed.dev/" target="_blank"> our website</a></p>
                <hr>
                <p>Need help? Go to our <a href="https://fingerscrossed.dev/documentation/" target="_blank">Documentation page</a></p>
                <hr>
                <p><a href="<?php 
                    $url = is_ssl() ? 'https://' : 'http://';
                    $url .= sanitize_text_field($_SERVER['HTTP_HOST']) . esc_url_raw($_SERVER['REQUEST_URI']);
                    $nonce = wp_create_nonce( 'export_xls_file' );
                    echo esc_url( add_query_arg( array('export' => 'log', 'n' => $nonce), $url ) );
                    ?>">Export log</a></p>
                <?php aiit_genarate_banner_html() ?>
            </div>
        </div>
    <?php 
}

/* ## Image logs input
function aiit_image_logs_data_callback(){
    global $wpdb;
    $log_records = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aiit_processed_images ORDER BY id DESC LIMIT 100", ARRAY_A);
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>ID</th><th>Image ID</th><th>Image name</th><th>Status</th><th>Reason</th><th>Created on</th></tr></thead>';
    echo '<tbody>';
    foreach ($log_records as $row) {
        echo '<tr>';
        echo '<td>' . esc_html($row['id']) . '</td>';
        echo '<td>' . esc_html($row['image_id']) . '</td>';
        echo '<td>' . esc_html($row['image_name']) . '</td>';
        echo '<td>' . esc_html($row['status']) . '</td>';
        echo '<td>' . esc_html($row['reason']) . '</td>';
        echo '<td>' . esc_html($row['created_on']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    wp_die();
} */

## Settings definition ##
function aiit_settings_init() {
    register_setting('aiit', 'aiit_openai_api_key');
    register_setting('aiit', 'aiit_license_key');
    register_setting('aiit_setting_flags', 'aiit_generate_on_upload');
    register_setting('aiit_setting_flags', 'aiit_replace_on_save');
    register_setting('aiit_setting_flags', 'aiit_generate_title');
    register_setting('aiit_setting_flags', 'aiit_generate_caption');
    register_setting('aiit_setting_flags', 'aiit_generate_description');
    register_setting('aiit_setting_flags', 'aiit_force_seo_title');
    register_setting('aiit_setting_flags', 'aiit_force_seo_caption');
    register_setting('aiit_setting_flags', 'aiit_force_seo_alt');   
    aiit_tuning_settings_init();
    aiit_bulk_settings_init();
    aiit_update_settings_init();
    aiit_log_export();
}

function aiit_tuning_settings_init() {
    register_setting('aiit_tuning', 'aiit_website_type');
    register_setting('aiit_tuning', 'aiit_audience');
    register_setting('aiit_tuning', 'aiit_categories');
    register_setting('aiit_tuning', 'aiit_language');
    register_setting('aiit_tuning', 'aiit_min_width');
    register_setting('aiit_tuning', 'aiit_min_height');
    register_setting('aiit_prompts', 'aiit_alt_text_prompt');
    register_setting('aiit_prompts', 'aiit_title_prompt');
    register_setting('aiit_prompts', 'aiit_caption_prompt');
    register_setting('aiit_prompts', 'aiit_description_prompt');
}

function aiit_bulk_settings_init() {
    register_setting('aiit_bulk', 'aiit_limit_images');
    register_setting('aiit_bulk', 'aiit_selection_criteria');
}

function aiit_update_settings_init() {
    register_setting('aiit_update', 'aiit_limit_posts');
    register_setting('aiit_update', 'aiit_post_type');
}


function aiit_k_valid($key = '', $force = false) {
    if (!$key){
        $key = get_option('aiit_license_key');
    }
    $validation = false;
    $counter = get_transient('aiit_k_counter');
    if (empty($counter) || $force == true){
        $endpoint_url = 'https://fingerscrossed.dev/wp-json/fclm/v1/validate';
        $version = $GLOBALS['aiit_version'];
        $domain = wp_parse_url(get_home_url(), PHP_URL_HOST);
        Global $wpdb;
        $image_count = wp_cache_get('aiit_k_valid_image_count');
        if ($image_count === false) {
            $image_count = $wpdb->get_var("SELECT MAX(id) FROM {$wpdb->prefix}aiit_processed_images");
            wp_cache_set('aiit_k_valid_image_count', $image_count, '', 1800);
        }
        $json_data = wp_json_encode(array(
            "product"  => "ai-image-seo-toolkit",
            "domain"   => $domain,
            "key"      => $key,
            "version"  => $version,
            "count"  => $image_count
        ));
        $args = array(
            'body'        => $json_data,
            'headers'     => array(
                'Content-Type' => 'application/json',
            ),
            'timeout'     => 15,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.0',
            'sslverify'   => true,
            'data_format' => 'body',
        );

        $response = wp_remote_post($endpoint_url, $args);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log('aiit_k_valid, Something went wrong: ' . print_r($response, true));
        } else {
            $response_code = wp_remote_retrieve_response_code($response);
            if($response_code == 200){
                $response_body = wp_remote_retrieve_body($response);
                $data = json_decode($response_body, true);
                if ($data['key'] == hash('sha256', 'fc' . $key . $domain)){
                    $validation = true;
                } else {
                    error_log('aiit_k_valid, Something went wrong');
                }
            }
        }
    } else {
        if (strlen($key) == 24) {
            $validation = true;
        } else {
            error_log('aiit_k_valid, wrong key');
        }
    }
    if ($validation === true){
        $counter = empty($counter) ? 1 : $counter + 1;
        set_transient('aiit_k_counter', $counter, 1800);
    }
    return $validation;
}


function aiit_oai_k_valid($key = '', $force = false){
    if (!$key){
        $key = get_option('aiit_openai_api_key');
    }
    $validation = false;
    $counter = get_transient('aiit_oai_k_counter');
    $keyLength = strlen($key);

    $found = false;
    if ($keyLength > 20) {
        $found = true;
    }

    if ($found) {
        if (empty($counter) || $force == true){
            $api_url = 'https://api.openai.com/v1/engines';
            $args = array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $key,
                    'Content-Type' => 'application/json',
                ),
            );
            $response = wp_remote_get($api_url, $args);
            if (!is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                $http_code = wp_remote_retrieve_response_code($response);
                if ($http_code == 200) {
                    $validation = true;
                }
            } else {
                // Handle the case where the request failed
                $error_message = $response->get_error_message();
                error_log('aiit_oai_k_valid, Something went wrong: ' . print_r($error_message, true));
            }
        } else {
            $validation = true;
        }
    } else {
        error_log('The AI key format is not valid.');
    }
    if ($validation === true){
        $counter = empty($counter) ? 1 : $counter + 1;
        set_transient('aiit_oai_k_counter', $counter, 1800);
    }
    return $validation; 

}

function aiit_save_settings(){
    if(isset( $_POST['aiit_settings_keys_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['aiit_settings_keys_nonce'])), 'settings_keys' )){
        $keyOui = sanitize_text_field($_POST['aiit_openai_api_key']);
        $key = sanitize_text_field($_POST['aiit_license_key']);

        if (aiit_oai_k_valid($keyOui, true)) {
            update_option('aiit_openai_api_key', $keyOui);
            if (aiit_k_valid($key, true)){
                delete_transient('aiit_banner_req');
                update_option('aiit_license_key', $key);
                update_option('aiit_generate_title', 'on');
                update_option('aiit_generate_caption', 'on');
                add_settings_error('ai-image-seo-toolkit-settings', 'success', 'Settings saved successfully.', 'updated');
            } else if ($key != ""){
                add_settings_error('ai-image-seo-toolkit-settings', 'invalid_ai_key', 'Invalid License Key provided.', 'error');
            } else {
                update_option('aiit_license_key', '');
                update_option('aiit_generate_title', '');
                update_option('aiit_generate_caption', '');          
                add_settings_error('ai-image-seo-toolkit-settings', 'success', 'OpenAI API Key saved successfully.', 'updated');
            }
        } else {
            add_settings_error('ai-image-seo-toolkit-settings', 'invalid_ai_key', 'Invalid OpenAI API Key provided.', 'error');
        }
    } else {
        add_settings_error('ai-image-seo-toolkit-settings', 'not_permitted', 'Invalid data provided.', 'error');
    }
}

function aiit_save_setting_flags(){
    if (isset( $_POST['aiit_settings_general_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['aiit_settings_general_nonce'])), 'settings_general' )){
        if (aiit_oai_k_valid()) {
            $generate = sanitize_text_field($_POST['aiit_generate_on_upload'] ?? '');
            update_option('aiit_generate_on_upload', $generate);
            if (aiit_k_valid()) {
                $replace = sanitize_text_field($_POST['aiit_replace_on_save'] ?? '');
                $generate_title = sanitize_text_field($_POST['generate_titles'] ?? '');
                $generate_caption = sanitize_text_field($_POST['generate_captions'] ?? '');
                $generate_description = sanitize_text_field($_POST['generate_descriptions'] ?? '');
                $force_seo_title = sanitize_text_field($_POST['aiit_force_seo_title'] ?? '');
                $force_seo_caption = sanitize_text_field($_POST['aiit_force_seo_caption'] ?? '');
                $force_seo_alt = sanitize_text_field($_POST['aiit_force_seo_alt'] ?? '');  
                update_option('aiit_generate_title', $generate_title);
                update_option('aiit_generate_caption', $generate_caption);
                update_option('aiit_generate_description', $generate_description);
                update_option('aiit_replace_on_save', $replace);
                update_option('aiit_force_seo_title', $force_seo_title);
                update_option('aiit_force_seo_caption', $force_seo_caption);
                update_option('aiit_force_seo_alt', $force_seo_alt);
            }
            add_settings_error('ai-image-seo-toolkit-settings', 'success', 'Settings saved successfully.', 'updated');
        } else {
            add_settings_error('ai-image-seo-toolkit-settings', 'invalid_keys', 'Invalid OpenAI API or License Key provided.', 'error');
        }
    } else {
        add_settings_error('ai-image-seo-toolkit-settings', 'not_permitted', 'Invalid data provided.', 'error');
    }
}

function aiit_log_export() {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset( $_GET['n'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash($_GET['n'])), 'export_xls_file')){
            if (isset($_GET['page']) && sanitize_text_field($_GET['page'])=='ai-image-seo-toolkit-settings') {
                if (isset($_GET['export']) && sanitize_text_field($_GET['export'])=='log') {
                    global $wpdb;
                    $limit = 1000;
                    $table_data = wp_cache_get('aiit_log_export_log');
                    if ($table_data === false) {
                        $table_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}aiit_processed_images ORDER BY id DESC LIMIT %d", $limit), ARRAY_A);
                        wp_cache_set('aiit_log_export_log', $table_data, '', 300);
                    }
                    if ( ! function_exists( 'WP_Filesystem' ) ) {
                        require_once ABSPATH . 'wp-admin/includes/file.php';
                    }
                    if ( ! WP_Filesystem() ) {
                        error_log('Failed to initialize the WordPress Filesystem');
                        return;
                    }
                    global $wp_filesystem;
                    $csv_data = '';
                    $csv_data .= implode( ',', array_keys( $table_data[0] ) ) . "\n";
                    foreach ( $table_data as $row ) {
                        $csv_data .= implode( ',', $row ) . "\n";
                    }
                    $file_name = 'table_export.csv';
                    $file_path = trailingslashit( WP_CONTENT_DIR ) . $file_name;
                    
                    if ( ! $wp_filesystem->put_contents( $file_path, $csv_data, FS_CHMOD_FILE ) ) {
                        error_log('Failed to write to the file');
                        return;
                    }
                    
                    header( 'Content-Type: text/csv' );
                    header( 'Content-Disposition: attachment; filename="' . $file_name . '"' );
                    header( 'Pragma: no-cache' );
                    header( 'Expires: 0' );
                    $file_contents = $wp_filesystem->get_contents( $file_path );
                    echo esc_html( $file_contents );
                    $wp_filesystem->delete( $file_path );
                    die();
                }
            }
        }
    }
}

           
function aiit_get_banner() {
    $req = false;
    $product = "ai-image-seo-toolkit";
    if (!aiit_k_valid()){
        $product .= "-free";
    }
    //error_log('aiit_get_banner, product: ' . print_r($product, true));
    $banner = get_transient('aiit_banner_req');
    
    //error_log('aiit_get_banner, banner: ' . print_r($banner, true));
    if (empty($banner)){
        $endpoint_url = 'https://fingerscrossed.dev/wp-json/fclm/v1/banner';
        $domain = wp_parse_url(get_home_url(), PHP_URL_HOST);
        $json_data = wp_json_encode(array(
            "product"  => $product,
            "domain"   => $domain
        ));
        $args = array(
            'body'        => $json_data,
            'headers'     => array(
                'Content-Type' => 'application/json',
            ),
            'timeout'     => 15,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.0',
            'sslverify'   => true,
            'data_format' => 'body',
        );

        $response = wp_remote_post($endpoint_url, $args);
        //error_log('aiit_get_banner, response: ' . print_r($response, true));
        if (is_wp_error($response)) {
            //something went wrong
        } else {
            $response_code = wp_remote_retrieve_response_code($response);
            if($response_code == 200){
                $req = true;
                $response_body = wp_remote_retrieve_body($response);
                $data = json_decode($response_body, true);

                $banner_image_data = wp_remote_get($data['banner'], array('sslverify' => false));
                if (!is_wp_error($banner_image_data) && wp_remote_retrieve_response_code($banner_image_data) === 200) {
                    $image_data = base64_encode(wp_remote_retrieve_body($banner_image_data));
                    // Set the transient to cache the image data for a specific duration (e.g., 1 day)
                    set_transient('aiit_banner', $image_data, 1800);
                    set_transient('aiit_banner_req', 'yes', 1800);
                    set_transient('aiit_banner_url', $data['url'], 1800);
                    set_transient('aiit_banner_alt', $data['alt'], 1800);
                }
            } else {
                $req = true;
                set_transient('aiit_banner_req', $req, 1800);
            }
        }
    } else {
        $req = true;
    }
    return $req;
}

function aiit_genarate_banner_html() {
    $banner_req = aiit_get_banner();
    error_log('aiit_get_banner, banner_req: ' . print_r($banner_req, true));
    if ($banner_req === true ){
        error_log('aiit_get_banner, banner_req === true: ' . print_r($banner_req, true));
        $url = get_transient('aiit_banner_url');
        $alt = get_transient('aiit_banner_alt');
        $banner = get_transient('aiit_banner');
        //error_log('aiit_get_banner, banner: ' . print_r($banner, true));
        if(!empty($banner) && !empty($alt)){
            echo  '<div class="banner-image"><hr>';
            if(!empty($url)){
                echo  '<a href="' . esc_url($url) . '" target="_blank">';
            }
            echo  '<img src="data:image/jpeg;base64,' . esc_attr($banner) . '" alt="' . esc_html($alt) . '">';
            if(!empty($url)){
                echo  '</a>';
            }
            echo  '</a></div>';
        }
    }
}