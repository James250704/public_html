<?php
// 資料庫配置範本
// 請將此檔案複製為config.php並修改以下設定

define('DB_HOST', 'localhost');
define('DB_NAME', 'database_name');
define('DB_USER', 'username');
define('DB_PASS', 'password');
// 自動檢測協議和主機名
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));
?>