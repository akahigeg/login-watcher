<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
  die();
}

// Drop table.
global $wpdb;
$table_name = $wpdb->prefix . SimpleLoginHistory::SIMPLE_LOGIN_HISTORY_TABLE_NAME;
$wpdb->query("DROP TABLE " . $table_name . ";");