<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

$response = ['exists' => false];

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $productId = $_GET['id'] ?? '';
    if (empty($productId)) {
        echo json_encode(['error' => 'Product ID is required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Product WHERE ProductID = :id");
    $stmt->bindParam(':id', $productId);
    $stmt->execute();

    $response['exists'] = ($stmt->fetchColumn() > 0);
} catch (PDOException $e) {
    $response['error'] = 'Database connection failed';
}

echo json_encode($response);
?>