<?php
// 資料庫配置
define('DB_HOST', 'localhost');
define('DB_NAME', 'D1256977');
define('DB_USER', 'root');
define('DB_PASS', '');

// 自動檢測協議和主機名
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/new_test');
?>