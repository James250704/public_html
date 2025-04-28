<?php
class CheckoutController
{
    public function show()
    {
        // 檢查登入狀態
        if (!isset($_SESSION['logged_in'])) {
            echo '<script>alert("請先登入"); window.location.href = "index.php?action=login";</script>';
            exit;
        }

        // 檢查購物車是否為空
        if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
            echo '<script>alert("購物車是空的"); window.location.href = "index.php?action=cart";</script>';
            exit;
        }

        // 載入視圖
        require_once 'views/checkout.php';
    }
}
?>