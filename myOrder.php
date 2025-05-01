<?php
require_once 'fixedFile/header.php';

// 檢查會員是否登入
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 取得會員訂單
$orders = getMemberOrders($_SESSION['member_id']);
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">我的訂單</h1>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>訂單編號</th>
                            <th>日期</th>
                            <th>狀態</th>
                            <th>總金額</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= $order['OrderID'] ?></td>
                                <td><?= $order['OrderDate'] ?></td>
                                <td><?= $order['Status'] ?></td>
                                <td>$<?= number_format($order['TotalAmount']) ?></td>
                                <td><a href="orderDetail.php?id=<?= $order['OrderID'] ?>"
                                        class="btn btn-sm btn-primary">詳細</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'fixedFile/footer.php'; ?>