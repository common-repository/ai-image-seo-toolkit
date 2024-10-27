<?php
/**
 * Plugin Name: AI Image SEO Toolkit
 * Plugin URI: https://fingerscrossed.dev/documentation/
 * Description: An AI-powered SEO plugin that streamlines image text generation by creating smart and SEO-friendly titles, ALTs, captions, and descriptions
 * Version: 1.0.8
 * Requires at least: 6.0
 * Requires PHP: 7.0
 * Author: Fingers Crossed
 * Author URI: https://fingerscrossed.dev/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit;
define ( 'AIIT_FILE_PATH', plugin_dir_path( __FILE__ ) );
include AIIT_FILE_PATH.'include/aiit_config.php';
include AIIT_FILE_PATH.'include/functions/aiit_activate.php';
include AIIT_FILE_PATH.'include/functions/aiit_settings.php';
include AIIT_FILE_PATH.'include/functions/aiit_media.php';
include AIIT_FILE_PATH.'include/functions/aiit_generate.php';
include AIIT_FILE_PATH.'include/functions/aiit_tuning.php';
include AIIT_FILE_PATH.'include/functions/aiit_bulk.php';
include AIIT_FILE_PATH.'include/functions/aiit_update.php';

register_activation_hook(__FILE__, 'aiit_create_tables');

if (!function_exists('add_action')) {
    echo 'Hi there! I\'m just a plugin, not much I can do when called directly.';
    exit;
}
add_action('admin_enqueue_scripts', 'aiit_enqueue_admin_styles');
add_action('admin_enqueue_scripts', 'aiit_enqueue_admin_scripts');
add_filter('bulk_actions-upload', 'aiit_add_bulk_action');
add_filter('handle_bulk_actions-upload', 'aiit_handle_bulk_action', 10, 3);
add_action('admin_notices', 'aiit_bulk_action_admin_notice');
add_action('admin_init', 'aiit_settings_init');
add_action('admin_menu', 'aiit_add_admin_page');
add_action('add_meta_boxes', 'aiit_add_meta_box');
add_action('wp_ajax_aiit_optimize_image', 'aiit_ajax_optimize_image');
add_action('wp_ajax_aiit_populate_optimization_table', 'aiit_populate_optimization_table');
add_action('wp_ajax_aiit_populate_update_table', 'aiit_populate_update_table');
add_action('wp_ajax_aiit_start_bulk_optimization', 'aiit_schedule_optimization');
add_action('wp_ajax_aiit_start_bulk_update', 'aiit_schedule_update');
add_action('wp_ajax_aiit_stop_bulk_optimization', 'aiit_stop_optimization');
add_action('wp_ajax_aiit_stop_bulk_update', 'aiit_stop_update');
add_action('aiit_schedule_optimization_event', 'aiit_update_bulk');
add_action('wp_ajax_aiit_job_load_data', 'aiit_job_data_callback');
add_action('aiit_schedule_update_event', 'aiit_update_posts');
add_action('wp_ajax_aiit_update_load_data', 'aiit_update_data_callback');
add_action('add_attachment', 'aiit_new_image_uploaded');
add_action('wp_ajax_upload_image', 'aiit_upload_tuning_image_callback');
add_action('wp_ajax_aiit_start_test_optimization', 'aiit_start_test_optimization');
add_action('wp_enqueue_scripts', 'aiit_open_specific_accordion_section');
add_action('save_post', 'aiit_update_on_save_post', 10, 3);


// Add Settings in plugin list view
function aiit_add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=ai-image-seo-toolkit-settings">Settings</a>';
    array_push($links, $settings_link);
    return $links;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'aiit_add_settings_link');
