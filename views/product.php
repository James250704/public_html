<?php
require_once __DIR__ . '/../../header.php';
// 商品詳情頁面
if (!isset($_GET['id'])) {
    header('Location: index.php?action=404');
    exit;
}

$productId = $_GET['id'];
?>

<?php require_once __DIR__ . '/../header.php' ?>
<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <img id="product-main-image" src="images/products/<?php echo $productId; ?>/main.jpg" class="img-fluid"
                alt="商品主圖">
            <div class="row mt-3">
                <div class="col-3">
                    <img src="images/products/<?php echo $productId; ?>/detail1.jpg"
                        class="img-thumbnail product-thumbnail" alt="商品細節1">
                </div>
                <div class="col-3">
                    <img src="images/products/<?php echo $productId; ?>/detail2.jpg"
                        class="img-thumbnail product-thumbnail" alt="商品細節2">
                </div>
                <div class="col-3">
                    <img src="images/products/<?php echo $productId; ?>/detail3.jpg"
                        class="img-thumbnail product-thumbnail" alt="商品細節3">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h2 id="product-name"></h2>
            <p id="product-description"></p>

            <div class="mb-3">
                <label for="product-color" class="form-label">顏色</label>
                <select class="form-select" id="product-color">
                    <!-- 顏色選項將通過AJAX動態加載 -->
                </select>
            </div>

            <div class="mb-3">
                <label for="product-size" class="form-label">尺寸</label>
                <select class="form-select" id="product-size">
                    <!-- 尺寸選項將通過AJAX動態加載 -->
                </select>
                <small id="size-description" class="text-muted"></small>
            </div>

            <div class="mb-3">
                <label for="product-quantity" class="form-label">數量</label>
                <div class="input-group" style="width: 150px;">
                    <button class="btn btn-outline-secondary" type="button" id="decrease-quantity">-</button>
                    <input type="number" class="form-control text-center" id="product-quantity" value="1" min="1">
                    <button class="btn btn-outline-secondary" type="button" id="increase-quantity">+</button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 id="product-price"></h4>
                <button class="btn btn-primary" id="add-to-cart">加入購物車</button>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <h3>商品詳情</h3>
            <div id="product-details">
                <!-- 商品詳情將通過AJAX動態加載 -->
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const productId = <?php echo $productId; ?>;

        // 獲取商品基本資訊
        $.ajax({
            url: 'api/product.php',
            data: { id: productId },
            success: function (product) {
                $('#product-name').text(product.name);
                $('#product-description').text(product.description);
                $('#product-price').text('NT$' + product.price);

                // 加載顏色選項
                const colorSelect = $('#product-color');
                product.colors.forEach(color => {
                    colorSelect.append(`<option value="${color}">${color}</option>`);
                });

                // 加載尺寸選項
                const sizeSelect = $('#product-size');
                product.sizes.forEach(size => {
                    sizeSelect.append(`<option value="${size.size}" data-description="${size.description}">${size.size}</option>`);
                });

                // 更新尺寸說明
                updateSizeDescription();
            }
        });

        // 獲取商品詳情
        $.ajax({
            url: 'api/product_details.php',
            data: { id: productId },
            success: function (details) {
                $('#product-details').html(details);
            }
        });

        // 數量增減按鈕
        $('#increase-quantity').click(function () {
            const quantityInput = $('#product-quantity');
            quantityInput.val(parseInt(quantityInput.val()) + 1);
        });

        $('#decrease-quantity').click(function () {
            const quantityInput = $('#product-quantity');
            if (parseInt(quantityInput.val()) > 1) {
                quantityInput.val(parseInt(quantityInput.val()) - 1);
            }
        });

        // 尺寸選擇變化
        $('#product-size').change(function () {
            updateSizeDescription();
        });

        function updateSizeDescription() {
            const selectedOption = $('#product-size option:selected');
            $('#size-description').text(selectedOption.data('description'));
        }

        // 加入購物車
        $('#add-to-cart').click(function () {
            const color = $('#product-color').val();
            const size = $('#product-size').val();
            const quantity = $('#product-quantity').val();

            $.ajax({
                url: 'api/cart.php',
                method: 'POST',
                data: {
                    action: 'add',
                    product_id: productId,
                    color: color,
                    size: size,
                    quantity: quantity
                },
                success: function (response) {
                    if (response.success) {
                        alert('商品已加入購物車');
                    } else {
                        alert('請先登入');
                        window.location.href = 'index.php?action=login';
                    }
                }
            });
        });

        // 縮略圖點擊切換主圖
        $('.product-thumbnail').click(function () {
            $('#product-main-image').attr('src', $(this).attr('src'));
        });
    });
</script>
<?php
require_once __DIR__ . '/../../footer.php';
?>