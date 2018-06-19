<?php
require_once(plugin_dir_path(__FILE__) . 'constants.php');

if (!defined('WP_UNINSTALL_PLUGIN')) {
  add_option('login_watcher_no_uninstall', '1');
  die();
}

if (!defined('LOGIN_WATCHER_TABLE_NAME')) {
  add_option('login_watcher_undefined_table_name', '1');
  die();
}

// TODO: delete plugin options
add_option('login_watcher_year', '1');

// drop table
global $wpdb;

$table_name = $wpdb->prefix . LOGIN_WATCHER_TABLE_NAME;

add_option('login_watcher_table_name', $table_name);
$wpdb->query("DROP TABLE " . $table_name . ";");