<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$productId = $_GET['id'] ?? '';

// 获取产品基本信息
$productQuery = "SELECT * FROM Product WHERE ProductID = :id";
$productStmt = $pdo->prepare($productQuery);
$productStmt->bindParam(':id', $productId);
$productStmt->execute();
$product = $productStmt->fetch(PDO::FETCH_ASSOC);

// 获取产品选项
$optionsQuery = "SELECT SizeDescription as sizeDescription , Color as color, Price as price FROM Options WHERE ProductID = :id";
$optionsStmt = $pdo->prepare($optionsQuery);
$optionsStmt->bindParam(':id', $productId);
$optionsStmt->execute();
$options = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);

// 构建响应数据
$response = [
    'description' => $product['Introdution'] ?? '',
    'ProductName' => $product['ProductName'] ?? '',
    'sizeOptions' => [],
    'galleryImages' => []
];

// 处理尺寸选项
$sizes = array_unique(array_column($options, 'sizeDescription'));
foreach ($sizes as $size) {
    $sizeOption = [
        'sizeDescription' => $size,
        'price' => 0,
        'colors' => []
    ];

    // 获取该尺寸下的颜色选项和价格
    foreach ($options as $option) {
        if ($option['sizeDescription'] === $size) {
            $sizeOption['colors'][] = ['color' => $option['color']];
            $sizeOption['price'] = $option['price']; // 更新该尺寸对应的价格
        }
    }

    $response['sizeOptions'][] = $sizeOption;
}

// 处理图库图片
$imageDir = __DIR__ . '/../images/products/' . $productId;
if (is_dir($imageDir)) {
    $images = array_diff(scandir($imageDir), array('.', '..'));
    foreach ($images as $image) {
        if ($image !== 'main.jpg' && strpos($image, 'gallery-') === 0) {
            $response['galleryImages'][] = [
                'url' => BASE_URL . '/images/products/' . $productId . '/' . $image
            ];
        }
    }
}

echo json_encode($response);
?>