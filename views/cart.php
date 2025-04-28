<?php
// 購物車頁面
if (!isset($_SESSION['logged_in'])) {
    echo '<script>alert("請先登入"); window.location.href = "index.php?action=login";</script>';
    exit;
}
?>
<?php require_once __DIR__ . '/../header.php' ?>
<div class="container my-5">
    <h2 class="mb-4">我的購物車</h2>

    <div class="d-none d-md-block">
        <!-- 桌面版購物車表格 -->
        <table class="table">
            <thead>
                <tr>
                    <th>商品</th>
                    <th>規格</th>
                    <th>單價</th>
                    <th>數量</th>
                    <th>小計</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="cart-items-desktop">
                <!-- 購物車商品將通過AJAX動態加載 -->
            </tbody>
        </table>
    </div>

    <div class="d-md-none">
        <!-- 手機版購物車列表 -->
        <div id="cart-items-mobile">
            <!-- 購物車商品將通過AJAX動態加載 -->
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <a href="index.php?action=products" class="btn btn-outline-secondary">繼續購物</a>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-inline-block text-end">
                <h4>總計: <span id="cart-total">NT$0</span></h4>
                <a href="index.php?action=checkout" class="btn btn-primary">前往結帳</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // 加載購物車內容
        loadCart();

        function loadCart() {
            $.ajax({
                url: 'api/cart.php',
                data: { action: 'get' },
                success: function (cartItems) {
                    renderCartItems(cartItems);
                    calculateTotal(cartItems);
                }
            });
        }

        function renderCartItems(cartItems) {
            // 清空現有內容
            $('#cart-items-desktop').empty();
            $('#cart-items-mobile').empty();

            if (cartItems.length === 0) {
                $('#cart-items-desktop').append('<tr><td colspan="6" class="text-center">購物車是空的</td></tr>');
                $('#cart-items-mobile').append('<div class="alert alert-info">購物車是空的</div>');
                return;
            }

            // 渲染桌面版購物車
            $.each(cartItems, function (index, item) {
                $('#cart-items-desktop').append(`
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="${item.image}" alt="${item.name}" width="60" class="me-3">
                            <div>
                                <h6 class="mb-0">${item.name}</h6>
                            </div>
                        </div>
                    </td>
                    <td>${item.color} / ${item.size}</td>
                    <td>NT$${item.price}</td>
                    <td>
                        <div class="input-group" style="width: 120px;">
                            <button class="btn btn-outline-secondary update-quantity" data-id="${item.id}" data-change="-1">-</button>
                            <input type="number" class="form-control text-center" value="${item.quantity}" min="1" readonly>
                            <button class="btn btn-outline-secondary update-quantity" data-id="${item.id}" data-change="1">+</button>
                        </div>
                    </td>
                    <td>NT$${item.price * item.quantity}</td>
                    <td>
                        <button class="btn btn-danger btn-sm remove-item" data-id="${item.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
            });

            // 渲染手機版購物車
            $.each(cartItems, function (index, item) {
                $('#cart-items-mobile').append(`
                <div class="card mb-3">
                    <div class="row g-0">
                        <div class="col-4">
                            <img src="${item.image}" class="img-fluid rounded-start" alt="${item.name}">
                        </div>
                        <div class="col-8">
                            <div class="card-body">
                                <h5 class="card-title">${item.name}</h5>
                                <p class="card-text">${item.color} / ${item.size}</p>
                                <p class="card-text">NT$${item.price} x ${item.quantity} = NT$${item.price * item.quantity}</p>
                                <div class="d-flex justify-content-between">
                                    <div class="input-group" style="width: 120px;">
                                        <button class="btn btn-outline-secondary update-quantity" data-id="${item.id}" data-change="-1">-</button>
                                        <input type="number" class="form-control text-center" value="${item.quantity}" min="1" readonly>
                                        <button class="btn btn-outline-secondary update-quantity" data-id="${item.id}" data-change="1">+</button>
                                    </div>
                                    <button class="btn btn-danger btn-sm remove-item" data-id="${item.id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            });

            // 綁定事件
            $('.update-quantity').click(function () {
                const itemId = $(this).data('id');
                const change = $(this).data('change');

                $.ajax({
                    url: 'api/cart.php',
                    method: 'POST',
                    data: {
                        action: 'update',
                        id: itemId,
                        change: change
                    },
                    success: function () {
                        loadCart();
                    }
                });
            });

            $('.remove-item').click(function () {
                const itemId = $(this).data('id');

                if (confirm('確定要移除這個商品嗎？')) {
                    $.ajax({
                        url: 'api/cart.php',
                        method: 'POST',
                        data: {
                            action: 'remove',
                            id: itemId
                        },
                        success: function () {
                            loadCart();
                        }
                    });
                }
            });
        }

        function calculateTotal(cartItems) {
            let total = 0;
            $.each(cartItems, function (index, item) {
                total += item.price * item.quantity;
            });
            $('#cart-total').text('NT$' + total);
        }
    });
</script>