<?php
session_start();
require_once 'config.php';

// 簡單路由功能
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

// 路由映射
$routes = [
    'home' => ['controller' => 'HomeController', 'method' => 'index'],
    'products' => ['controller' => 'ProductController', 'method' => 'index'],
    'cart' => ['controller' => 'CartController', 'method' => 'index'],
    'checkout' => ['controller' => 'CheckoutController', 'method' => 'show'],
    'login' => ['controller' => 'AuthController', 'method' => 'login'],
    'register' => ['controller' => 'AuthController', 'method' => 'register'],
    'orders' => ['controller' => 'OrderController', 'method' => 'index'],
    'brand' => ['controller' => 'PageController', 'method' => 'brand'],
    'shopping-guide' => ['controller' => 'PageController', 'method' => 'shoppingGuide'],
    'warranty' => ['controller' => 'PageController', 'method' => 'warranty'],
    'about' => ['controller' => 'PageController', 'method' => 'about']
];

// 檢查路由是否存在
if (array_key_exists($action, $routes)) {
    $controllerName = $routes[$action]['controller'];
    $methodName = $routes[$action]['method'];

    // 載入控制器文件
    $controllerFile = 'controllers/' . $controllerName . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;

        // 實例化控制器並調用方法
        $controller = new $controllerName();
        $controller->$methodName();
    } else {
        // 控制器文件不存在
        header('HTTP/1.0 404 Not Found');
        echo 'Page not found';
        exit;
    }
} else {
    // 路由不存在
    header('HTTP/1.0 404 Not Found');
    echo 'Page not found';
    exit;
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ"
    crossorigin="anonymous"></script>