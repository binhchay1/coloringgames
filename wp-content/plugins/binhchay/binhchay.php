<?php if (!defined('ABSPATH')) die;
/*
Plugin Name: Config SEO
Description: All config SEO for this domain only
Author: binhchay
Version: 1.0
License: GPLv2 or later
*/

define('BINHCHAY_ADMIN_VERSION', '1.0.0');
define('BINHCHAY_ADMIN_DIR', 'binhchay');

require plugin_dir_path(__FILE__) . 'admin-form.php';
function run_ct_wp_admin_form()
{
    $plugin = new Admin_Form();
    $plugin->init();
}
run_ct_wp_admin_form();

function create_category_custom_table()
{
    global $wpdb;
    $db_table_name = $wpdb->prefix . 'category_custom';
    $db_version = '1.0.0';
    $charset_collate = $wpdb->get_charset_collate();

    if ($wpdb->get_var("show tables like '$db_table_name'") != $db_table_name) {
        $sql = "CREATE TABLE $db_table_name (
                id int(11) NOT NULL auto_increment,
                category_id varchar(15) NOT NULL,
                title text NOT NULL,
                description text NOT NULL,
                content text NOT NULL,
                UNIQUE KEY id (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        add_option('my_db_version', $db_version);
        dbDelta($sql);
    }

    $listColumnsUpdate = ['title', 'description'];

    foreach ($listColumnsUpdate as $column) {
        $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = " . $db_table_name . " AND column_name = '" . $column . "'");

        if (empty($row)) {
            if ($column == 'title') {
                $wpdb->query("ALTER TABLE " . $db_table_name . " ADD " . $column . " VARCHAR (1000) NULL");
            }

            if ($column == 'description') {
                $wpdb->query("ALTER TABLE " . $db_table_name . " ADD " . $column . " VARCHAR (1000) NULL");
            }
        }
    }
}

register_activation_hook(__FILE__, 'create_category_custom_table');
