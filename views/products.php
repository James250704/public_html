<?php
$productId = $_GET['id'];
// 使用API檢查商品是否存在
$checkResponse = file_get_contents(BASE_URL . "/api/product_check.php?id=" . $productId);
$checkData = json_decode($checkResponse, true);

if (!isset($_GET['id']) || !$checkData['exists']) {
    header('Location: index.php?action=404');
    exit;
}
require_once __DIR__ . '/../config.php';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Product WHERE ProductID = :id");
    $stmt->bindParam(':id', $productId);
    $stmt->execute();

    if ($stmt->fetchColumn() == 0) {
        header('Location: index.php?action=404');
        exit;
    }
} catch (PDOException $e) {
    header('Location: index.php?action=500');
    exit;
}
?>
<?php require_once __DIR__ . '/../header.php' ?>
<div class="container text-center border-bottom border-black my-3">
    <img src="images/title.png" alt="歐印精品" class="img-fluid">
    <h1 class="h1" id="product-name"></h1>
</div>
<div class="container my-4 m-0 px-0 mx-auto">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <img id="product-main-image" src="images/Products/<?php echo $productId; ?>/main.jpg" class="img-fluid"
                    style="border-radius: 20px;" alt="商品主圖">
            </div>
            <div class="col-lg-6 col-12">
                <div class="container mb-4">
                    <p id="product-description"></p>
                </div>
                <div class="container">
                    <div class="mb-4">
                        <p class="fw-bold mb-1">尺寸</p>
                        <div id="size-options-container">
                            <!-- 尺寸选项将通过AJAX动态加载 -->
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="fw-bold mb-1">顏色</p>
                        <div id="color-options-container">
                            <!-- 颜色选项将通过AJAX动态加载 -->
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="fw-bold mb-1">數量</p>
                        <div class="input-group" style="max-width: 150px;">
                            <button class="btn btn-outline-success" type="button" id="decrease-qty">-</button>
                            <input type="number" class="form-control text-center border-success" value="1" min="1"
                                id="quantity">
                            <button class="btn btn-outline-success" type="button" id="increase-qty">+</button>
                        </div>
                    </div>

                    <!-- 新增價格區塊 -->
                    <div class="mb-4">
                        <p class="fw-bold mb-1">價格</p>
                        <div>
                            <span class="fs-6 text-decoration-line-through" id="original-price">
                                <!-- 移除直接PHP變量，改由JS動態更新 -->
                            </span>
                            <div class="h3 text-danger fw-bold" id="product-price">
                                <!-- 移除直接PHP變量，改由JS動態更新 -->
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary mt-3" id="add-to-cart">加入購物車</button>
                </div>
            </div>

            <div class="row mt-5 col-12 col-lg-9 mx-auto px-0" id="gallery-container">
                <!-- 商品圖片將通過AJAX動態加載 -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous">
    </script>
<script>
    $(document).ready(function () {
        const productId = <?php echo $productId; ?>;
        let selectedSize = null;
        let selectedColor = null;

        // 載入商品詳情
        $.ajax({
            url: 'api/product_detail.php',
            method: 'GET',
            data: { id: productId },
            success: function (response) {
                console.log(response);
                // 更新商品名稱更新：
                $('#product-name').text(response.ProductName || '商品詳情');
                // 更新商品描述
                $('#product-description').text(response.description);

                // 渲染尺寸選項
                const sizeContainer = $('#size-options-container');
                sizeContainer.empty();
                response.sizeOptions.forEach(function (sizeOption, index) {
                    const button = $(`<button class="btn btn-outline-success me-1 mb-1 size-option">${sizeOption.sizeDescription}</button>`);
                    button.data('size', sizeOption.sizeDescription);
                    button.data('price', sizeOption.price);
                    sizeContainer.append(button);

                    // 自動點擊第一個尺寸選項
                    if (index === 0) {
                        button.trigger('click');
                    }
                });

                // 渲染顏色選項
                const colorContainer = $('#color-options-container');
                colorContainer.empty();
                response.sizeOptions[0].colors.forEach(function (colorOption, index) {
                    const button = $(`<button class="btn btn-outline-success me-1 mb-1 color-option">${colorOption.color}</button>`);
                    colorContainer.append(button);

                    // 自動點擊第一個顏色選項
                    if (index === 0) {
                        button.trigger('click');
                    }
                });

                // 初始化價格
                updatePrice(response.sizeOptions[0].price);

                // 渲染商品圖片
                if (response.galleryImages && response.galleryImages.length > 0) {
                    const galleryContainer = $('#gallery-container');
                    galleryContainer.empty();
                    response.galleryImages.forEach(function (img) {
                        galleryContainer.append(`
                            <div class="col-12 col-lg-9 mx-auto mx-0">
                                <img src="${img.url}" alt="產品圖片" class="img-fluid">
                            </div>
                        `);
                    });
                }
            }
        });

        // 尺寸選擇事件
        $(document).on('click', '.size-option', function () {
            $('.size-option').removeClass('active');
            $(this).addClass('active');
            selectedSize = $(this).data('size');
            const price = $(this).data('price');
            updatePrice(price);
        });

        // 顏色選擇事件
        $(document).on('click', '.color-option', function () {
            $('.color-option').removeClass('active');
            $(this).addClass('active');
            selectedColor = $(this).text();
        });

        // 更新價格顯示
        function updatePrice(price) {
            $('#original-price').text(`NT$ ${(price * 1.2).toFixed(0)}`);
            $('#product-price').text(`NT$ ${price}`);
        }

        // 更新顏色選項
        function updateColorOptions(sizeOption) {
            const colorContainer = $('.color-option').parent();
            colorContainer.empty();
            sizeOption.colors.forEach(function (colorOption) {
                const button = $(`<button class="btn btn-outline-success me-1 mb-1 color-option">${colorOption.color}</button>`);
                colorContainer.append(button);
            });
        }

        // 數量增減按鈕
        $('#increase-qty').click(function () {
            const quantityInput = $('#quantity');
            quantityInput.val(parseInt(quantityInput.val()) + 1);
        });

        $('#decrease-qty').click(function () {
            const quantityInput = $('#quantity');
            if (parseInt(quantityInput.val()) > 1) {
                quantityInput.val(parseInt(quantityInput.val()) - 1);
            }
        });

        // 加入購物車
        $('#add-to-cart').click(function () {
            if (!selectedSize || !selectedColor) {
                alert('請選擇商品尺寸和顏色');
                return;
            }

            $.ajax({
                url: 'api/cart.php',
                method: 'POST',
                data: {
                    action: 'add',
                    product_id: productId,
                    color: selectedColor,
                    size: selectedSize,
                    quantity: $('#quantity').val()
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
    });
</script>
<?php
require_once __DIR__ . '/../footer.php';
?>