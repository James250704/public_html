<?php
class OrderController
{
    public function index()
    {
        // Check login status
        if (!isset($_SESSION['logged_in'])) {
            echo '<script>alert("請先登入"); window.location.href = "index.php?action=login";</script>';
            exit;
        }

        // Load orders view
        require_once 'views/orders.php';
    }
}
?>