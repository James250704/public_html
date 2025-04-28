<?php require_once __DIR__ . '/../header.php' ?>
<div class="container">
    <h2 class="mb-4">結帳</h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>訂單明細</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>商品</th>
                                <th>規格</th>
                                <th>單價</th>
                                <th>數量</th>
                                <th>小計</th>
                            </tr>
                        </thead>
                        <tbody id="checkout-items">
                            <!-- 訂單商品將通過AJAX動態加載 -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>會員資訊</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>姓名:</strong> <span id="member-name"></span></p>
                            <p><strong>電話:</strong> <span id="member-phone"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> <span id="member-email"></span></p>
                            <p><strong>地址:</strong> <span id="member-address"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>訂單總計</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>商品總計:</span>
                        <span id="subtotal">NT$0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>運費:</span>
                        <span>NT$0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5>總計:</h5>
                        <h5 id="total">NT$0</h5>
                    </div>
                    <button class="btn btn-primary w-100 mt-3" id="place-order">確認訂單</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // 加載購物車內容
        $.ajax({
            url: 'api/cart.php',
            data: { action: 'get' },
            success: function (cartItems) {
                renderCheckoutItems(cartItems);
                calculateTotal(cartItems);
            }
        });

        // 加載會員資訊
        $.ajax({
            url: 'api/account.php',
            data: { action: 'get' },
            success: function (member) {
                $('#member-name').text(member.name);
                $('#member-phone').text(member.phone);
                $('#member-email').text(member.email);
                $('#member-address').text(member.address);
            }
        });

        function renderCheckoutItems(cartItems) {
            const container = $('#checkout-items');
            container.empty();

            $.each(cartItems, function (index, item) {
                container.append(`
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
                    <td>${item.quantity}</td>
                    <td>NT$${item.price * item.quantity}</td>
                </tr>
            `);
            });
        }

        function calculateTotal(cartItems) {
            let subtotal = 0;
            $.each(cartItems, function (index, item) {
                subtotal += item.price * item.quantity;
            });

            $('#subtotal').text('NT$' + subtotal);
            $('#total').text('NT$' + subtotal);
        }

        // 提交訂單
        $('#place-order').click(function () {
            $.ajax({
                url: 'api/order.php',
                method: 'POST',
                data: { action: 'create' },
                success: function (response) {
                    if (response.success) {
                        alert('訂單已建立，訂單編號: ' + response.order_id);
                        window.location.href = 'index.php?action=orders';
                    } else {
                        alert(response.message || '訂單建立失敗');
                    }
                }
            });
        });
    });
</script>