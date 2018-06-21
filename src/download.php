<?php
$this_plugin_dir = dirname( __FILE__ );
$wordpress_base_dir = $this_plugin_dir. '/../../..';
require_once($wordpress_base_dir . '/wp-load.php');

SimpleLoginHistory::downloadCSV();