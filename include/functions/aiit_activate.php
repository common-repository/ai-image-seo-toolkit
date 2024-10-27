<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function aiit_create_tables() {
    aiit_create_optimization_table();
    aiit_create_log_table();
    aiit_create_processing_table();
    aiit_tuning_images_table();
    aiit_posts_update_table();
}


## Optimization table ##
function aiit_create_optimization_table() {
    global $wpdb;
    $table_optimization = $wpdb->prefix . 'aiit_image_optimization';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_optimization (
        `id` int NOT NULL AUTO_INCREMENT,
        `image_name` varchar(255) NOT NULL,
        `image_id` int NOT NULL,
        `status` varchar(20) NOT NULL,
        `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}aiit_image_optimization'") != $table_optimization) {
        error_log('aiit_create_optimization_table: Failed to create table');
    }
}

function aiit_posts_update_table() {
    global $wpdb;
    $table_p_update = $wpdb->prefix . 'aiit_posts_update';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_p_update (
        `id` int NOT NULL AUTO_INCREMENT,
        `post_id` int NOT NULL,
        `image_id` int NOT NULL,
        `old_alt` varchar(255),
        `old_caption` varchar(255),
        `old_title` varchar(255),
        `ml_alt` varchar(255) NOT NULL,
        `ml_caption` varchar(255),   
        `ml_title` varchar(255),       
        `status` varchar(20) NOT NULL,
        `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}aiit_posts_update'") != $table_p_update) {
        error_log('aiit_posts_update_table: Failed to create table');
    }
}

function aiit_create_log_table() {
    global $wpdb;
    $table_log = $wpdb->prefix . 'aiit_processed_images';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_log (
        `id` int NOT NULL AUTO_INCREMENT,
        `image_name` varchar(255) NOT NULL,
        `image_id` int NOT NULL,
        `post_id` int,
        `job_id` int,
        `status` varchar(20) NOT NULL,
        `reason` varchar(1000),
        `old_alt` varchar(255),
        `old_caption` varchar(255),
        `old_title` varchar(255),
        `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}aiit_processed_images'") != $table_log) {
        error_log('aiit_create_log_table: Failed to create table');
    }
}

function aiit_create_processing_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'aiit_bckg_processing';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int NOT NULL AUTO_INCREMENT,
        `type` varchar(20) NOT NULL,
        `initial_count` int NOT NULL,
        `processed_count` int,
        `status` varchar(20) NOT NULL,
        `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `completed_on` DATETIME,        
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}aiit_bckg_processing'") != $table_name) {
        error_log('aiit_create_optimization_table: Failed to create table');
    }
}

function aiit_tuning_images_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'aiit_tuning_images';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int NOT NULL,
        `image_id` int NOT NULL,
        `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,       
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Check for table creation
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}aiit_tuning_images'") != $table_name) {
        error_log('aiit_tuning_images_table: Failed to create table');
    }
}