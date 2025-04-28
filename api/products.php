<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

// 連接資料庫
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// 獲取商品類型
$type = $_GET['type'] ?? '';

// 根據類型查詢商品
$query = "SELECT p.ProductID as id, p.ProductName as name, p.Introdution as description, 
          MIN(o.Price) as price, MAX(o.Price * 1.2) as original_price, 
          CONCAT('images/products/', p.ProductID, '/main.jpg') as image
          FROM Product p
          JOIN Options o ON p.ProductID = o.ProductID
          WHERE p.Type = :type
          GROUP BY p.ProductID";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':type', $type);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
?>