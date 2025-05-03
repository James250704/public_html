<?php
require_once __DIR__ . '/../../../api/db.php';
require_once __DIR__ . '/../../../api/product.php';

function handleAddProduct()
{
    try {
        $db = getDBConnection();

        // 驗證必填欄位
        $requiredFields = ['productName', 'productType', 'productIntro'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception('請填寫所有必填欄位');
            }
        }

        // 準備商品基本資料
        $productData = [
            'ProductName' => $_POST['productName'],
            'Type' => $_POST['productType'],
            'Introdution' => $_POST['productIntro'],
            'isActive' => isset($_POST['productActive']) ? 1 : 0
        ];

        // 插入商品資料
        $productId = insertProduct($db, $productData);

        // 處理主圖片上傳
        if (!empty($_FILES['mainImage']['name'])) {
            $mainImagePath = uploadImage($_FILES['mainImage']);
            // 不儲存到資料庫，直接移動到產品目錄
            $productData['MainImage'] = 'imgs/products/' . $productId . '/main.jpg';

            // 移動主圖片到產品目錄
            $targetDir = __DIR__ . '/../../../imgs/products/' . $productId . '/';
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            rename(__DIR__ . '/../../../' . $mainImagePath, $targetDir . 'main.jpg');
        }

        // 插入商品資料
        $productId = insertProduct($db, $productData);

        // 處理詳細圖片
        if (!empty($_FILES['galleryImages']['name'][0])) {
            foreach ($_FILES['galleryImages']['tmp_name'] as $index => $tmpName) {
                if ($_FILES['galleryImages']['error'][$index] === UPLOAD_ERR_OK) {
                    $galleryImagePath = uploadImage([
                        'name' => $_FILES['galleryImages']['name'][$index],
                        'type' => $_FILES['galleryImages']['type'][$index],
                        'tmp_name' => $tmpName,
                        'error' => $_FILES['galleryImages']['error'][$index],
                        'size' => $_FILES['galleryImages']['size'][$index]
                    ]);

                    insertProductImage($db, $productId, $galleryImagePath, $index + 1);
                }
            }
        }

        // 處理尺寸與價格選項
        if (!empty($_POST['sizes']) && is_array($_POST['sizes'])) {
            foreach ($_POST['sizes'] as $sizeData) {
                if (is_array($sizeData) && !empty($sizeData['colors']) && is_array($sizeData['colors'])) {
                    $optionId = insertProductOption($db, $productId, $sizeData);

                    // 顏色和庫存已在 insertProductOption 中處理
                }
            }
        }

        return ['success' => true, 'message' => '商品新增成功', 'productId' => $productId];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function uploadImage($file)
{
    $uploadDir = __DIR__ . '/../../../imgs/products/';
    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('圖片上傳失敗');
    }

    return 'imgs/products/' . $fileName;
}

function insertProduct($db, $data)
{
    // 獲取當前最大ProductID
    $maxIdStmt = $db->query("SELECT MAX(ProductID) as maxId FROM Product");
    $maxId = $maxIdStmt->fetch(PDO::FETCH_ASSOC)['maxId'];
    $newId = $maxId ? $maxId + 1 : 1;

    $stmt = $db->prepare("INSERT INTO Product (ProductID, ProductName, Type, Introdution, isActive) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $newId,
        $data['ProductName'],
        $data['Type'],
        $data['Introdution'],
        $data['isActive']
    ]);

    return $newId;
}

function insertProductImage($db, $productId, $imagePath, $order)
{
    // 直接儲存圖片到指定目錄，不存入資料庫
    $targetDir = __DIR__ . '/../../../imgs/products/' . $productId . '/';
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // 主圖片命名為main.jpg，詳細圖片命名為gallery-{number}.jpg
    $targetFile = $order === 1
        ? $targetDir . 'main.jpg'
        : $targetDir . 'gallery-' . ($order - 1) . '.jpg';

    rename(__DIR__ . '/../../../' . $imagePath, $targetFile);
}

function insertProductOption($db, $productId, $sizeData)
{
    $stmt = $db->prepare("INSERT INTO Options (ProductID, Size, SizeDescription, Price, Color, Stock) VALUES (?, ?, ?, ?, ?, ?)");

    // 處理每個顏色選項
    foreach ($sizeData['colors'] as $colorData) {
        $stmt->execute([
            $productId,
            $sizeData['size'],
            $sizeData['sizeDescription'],
            $sizeData['price'],
            $colorData['color'],
            $colorData['stock']
        ]);
    }

    return $db->lastInsertId();
}

// 已將顏色和庫存直接整合到insertProductOption中，此函數可刪除

// 處理AJAX請求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addProduct') {
    header('Content-Type: application/json');
    echo json_encode(handleAddProduct());
    exit;
}
?>