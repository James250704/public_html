<?php
require_once 'fixedFile/header.php';

// 檢查是否為管理員
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action active">儀表板</a>
                <a href="#" class="list-group-item list-group-item-action">商品管理</a>
                <a href="#" class="list-group-item list-group-item-action">訂單管理</a>
                <a href="#" class="list-group-item list-group-item-action">會員管理</a>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">管理後台</h4>
                    // ... 後台內容 ...
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'fixedFile/footer.php'; ?>