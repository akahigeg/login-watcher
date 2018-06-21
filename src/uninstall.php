<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
  die();
}

// Drop table.
require_once(plugin_dir_path( __FILE__ ) . 'index.php');
global $wpdb;
$table_name = $wpdb->prefix . SimpleLoginHistory::SIMPLE_LOGIN_HISTORY_TABLE_NAME;
$wpdb->query("DROP TABLE IF EXISTS " . $table_name . ";");