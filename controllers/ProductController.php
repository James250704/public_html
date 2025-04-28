<?php
class ProductController
{
    public function index()
    {
        // Load products view
        $productId = $_GET['id'] ?? '';
        $productName = '';

        if (!empty($productId)) {
            // 使用 BASE_URL 獲取商品信息
            $apiResponse = file_get_contents(BASE_URL . '/api/product_detail.php?id=' . $productId);
            $productData = json_decode($apiResponse, true);
            $productName = $productData['ProductName'] ?? '';
        }

        $pageTitle = '歐印精品-' . $productName;
        require_once 'views/products.php';
    }
}
?>