<?php
/*
Plugin Name: Login Watcher
Plugin URI: https://www.brassworks.jp/
Description: A simple login history plugin.
Author: akahige
Author URI: https://www.brassworks.jp/
Version: 20180619
Text Domain: login-watcher
Domain Path: /languages/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

function login_watcher_load_textdomain() {
  load_plugin_textdomain('login-watcher', false, dirname( plugin_basename( __FILE__ )) . '/languages/');
}
add_action('plugins_loaded', 'login_watcher_load_textdomain');

class LoginWatcher {
  const LOGIN_WATCHER_TABLE_NAME = 'login_watcher_histories';

  /*
   * Create login history table on activate.
  */
  public static function activate() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . self::tableName() . " (
      ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      user_login varchar(255) NOT NULL,
      user_id bigint(20) UNSIGNED NOT NULL,
      remote_ip varchar(43),
      user_agent text,
      logged_in_at timestamp NOT NULL,
      UNIQUE KEY ID (ID)
    ) CHARACTER SET ". $charset_collate . ";";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  /*
   * Save login history to DB at user login.
   * 
   * A history contains Login ID, WP_User ID, Remote IP and UserAgent.
   * 
   * @param string $user_login Login ID.
   * @param WP_User $curernt_user WP_User Object of current user.
   */
  public static function saveLoginHistory($user_login, $current_user) {
    global $wpdb;

    $history = array(
      'user_login' => $user_login,
      'user_id' => $current_user->ID,
      'remote_ip' => $_SERVER['REMOTE_ADDR'],
      'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    );
    
    $wpdb->insert(self::tableName(), $history);
  }

  /*
   * Show menu item of login histories.
   */
  public static function showLoginHistoryMenu() {
    $title = __('Login History', 'login-watcher');
    add_menu_page($title, $title, 'manage_options', 'login_watcher_login_history', 'LoginWatcher::showLoginHistory', 'dashicons-welcome-learn-more', 81);
  }

  /*
   * Show list of login histories.
   * 
   * Output recent 25 records of login histories.
   */
  public static function showLoginHistory() {
    $template = file_get_contents(plugin_dir_path(__FILE__) . 'templates/login_history.html');

    $results = self::queryLoginHistories('desc', 25);
    $histories = '';
    foreach ($results as $history) {
      $histories .= '<tr><td>' . $history->logged_in_at. '</td><td>' . $history->user_login . '</td><td>' . $history->remote_ip . '</td><td>' . $history->user_agent . '</td></tr>';
    }

    $output = str_replace('%%page_title%%', get_admin_page_title(), $template);
    $output = str_replace('%%login_histories%%', $histories, $output);
    $output = str_replace('%%Recent%%', __('Recent', 'login-watcher'), $output);
    $output = str_replace('%%record%%', __('record', 'login-watcher'), $output);
    $output = str_replace('%%csv_download_link%%', plugin_dir_url(__FILE__) . 'download.php', $output);
    echo $output;
  }

  /*
   * Download CSV of login histories.
   * 
   * CSV contains whole login histories.
   */
  public static function downloadCSV() {
    $csv_header = 'timestamp,user_login,remote_ip,user_agent';

    $lines = array();
    $results = self::queryLoginHistories();
    foreach ($results as $history) {
      $output_values = array($history->logged_in_at, $history->user_login, $history->remote_ip, $history->user_agent);
      $lines[] = implode(',', array_map(function($col) { return '"' . $col . '"'; }, $output_values));
    }

    $csv_body = implode("\n", $lines) . "\n";

		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=login_history.csv'); 

    echo $csv_header;
    echo $csv_body;
  }

  /*
   * Return $wpdb query results of select login history. 
   * 
   * @param string $order 'asc' or 'desc'. order by logged_in_at(timestamp)
   * @param int $limit LIMIT in SQL. '0' means no limit.
   * @return array $results wpdb query results of select login history. 
  */
  private static function queryLoginHistories($order = 'asc', $limit = 0) {
    global $wpdb;

    $sql = "SELECT * FROM " . self::tableName() . " order by logged_in_at " . $order;
    if (!empty($limit)) {
      $sql .= " limit " . $limit;
    }

    return $wpdb->get_results($sql);
  }

  /*
   * Return login history table name with prefix.
   * 
   * @return string $table_name login history table name with prefix.
  */
  private static function tableName() {
    global $wpdb;
    return $wpdb->prefix . self::LOGIN_WATCHER_TABLE_NAME;
  }
}

register_activation_hook( __FILE__, 'LoginWatcher::activate');
add_action('wp_login', 'LoginWatcher::saveLoginHistory', 10, 2);
add_action('admin_menu', 'LoginWatcher::showLoginHistoryMenu');