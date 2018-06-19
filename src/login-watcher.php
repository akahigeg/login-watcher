<?php
/*
Plugin Name: Login Watcher
Plugin URI: https://www.brassworks.jp/
Description: A simple login history plugin.
Author: akahige
Author URI: https://www.brassworks.jp/
Version: 20180619
Text Domain: login-watcher
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// TODO: アクティベート時のlogin_watcher_histories テーブル作成
// TODO: ログイン履歴の記録
// TODO: ログイン履歴を管理画面で一覧表示
// TODO: アンインストール時のテーブル削除

require_once(plugin_dir_path(__FILE__) . 'constants.php');
add_option('login_watcher_table_name', LOGIN_WATCHER_TABLE_NAME);

class LoginWatcher {
  public static function activate() {
    global $wpdb;

    $table_name = $wpdb->prefix . LOGIN_WATCHER_TABLE_NAME;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . $table_name . " (
      ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      user_id bigint(20) UNSIGNED NOT NULL,
      ip varchar(43),
      user_agent text,
      logged_in_at timestamp NOT NULL,
      UNIQUE KEY ID (ID)
    ) CHARACTER SET ". $charset_collate . ";";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}
register_activation_hook( __FILE__, 'LoginWatcher::activate' );
