<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
  die();
}

// Drop table.
global $wpdb;
$table_name = $wpdb->prefix . LoginWatcher::LOGIN_WATCHER_TABLE_NAME;
$wpdb->query("DROP TABLE " . $table_name . ";");