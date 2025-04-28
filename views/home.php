<?php
require_once __DIR__ . '/../header.php';
// 首頁視圖文件
?>
<div class="container-fluid px-0 mt-0">
    <!-- 輪播圖片 -->
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $carouselDir = __DIR__ . '/../images/carousel';
            if (is_dir($carouselDir)) {
                $images = array_diff(scandir($carouselDir), array('.', '..', '.gitkeep'));
                $first = true;
                foreach ($images as $image) {
                    echo '<div class="carousel-item' . ($first ? ' active' : '') . '">';
                    echo '<img src="images/carousel/' . $image . '" class="d-block w-100" alt="' . pathinfo($image, PATHINFO_FILENAME) . '">';
                    echo '</div>';
                    $first = false;
                }
            }
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container mb-4">
        <h2 class="text-center mb-4 mt-3">精選商品</h2>
        <div class="row" id="featured-products">
            <!-- 商品將通過AJAX動態加載 -->
        </div>

        <h2 class="text-center mb-4 mt-5">旅行系列</h2>
        <div class="row" id="travel-products">
            <!-- 商品將通過AJAX動態加載 -->
        </div>

        <h2 class="text-center mb-4 mt-5">配件系列</h2>
        <div class="row" id="accessories-products">
            <!-- 商品將通過AJAX動態加載 -->
        </div>
    </div>

    <? require_once __DIR__ . '/../footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    console.log('<?php echo BASE_URL; ?>');
    $(document).ready(function () {
        // 加載精選商品
        $.ajax({
            url: 'api/products.php',
            data: { type: 'featured' },
            method: 'GET',
            success: function (data) {
                renderProducts(data, '#featured-products');
            },
            error: function (xhr, status, error) {
                console.error('加載精選商品失敗:', error);
            }
        });

        // 加載旅行系列商品
        $.ajax({
            url: 'api/products.php',
            data: { type: 'travel' },
            method: 'GET',
            success: function (data) {
                renderProducts(data, '#travel-products');
            },
            error: function (xhr, status, error) {
                console.error('加載旅行系列商品失敗:', error);
            }
        });

        // 加載配件系列商品
        $.ajax({
            url: 'api/products.php',
            data: { type: 'accessories' },
            method: 'GET',
            success: function (data) {
                renderProducts(data, '#accessories-products');
            },
            error: function (xhr, status, error) {
                console.error('加載配件系列商品失敗:', error);
            }
        });

        function renderProducts(products, container) {
            $.each(products, function (index, product) {
                $(container).append(`
                <div class="col-md-4 mb-4">
                    <a href="index.php?action=products&id=${product.id}" class="text-decoration-none" style="color: inherit;">
                        <div class="card col-lg-8" style="border-radius: 20px; transition: transform 0.2s; position: relative;" class="border-muted" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                            <div style="position: relative;">
                                <img src="${product.image}" class="card-img-top" style="border-radius: 20px 20px 0 0; object-fit: cover; height: auto; width: 100%;" alt="${product.name}">
                                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); border-radius: 20px 20px 0 0; opacity: 0; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'"></div>
                            </div>
                            <div class="card-body pb-0 mb-2">
                                <h5 class="card-title">${product.name}</h5>
                                <p class="card-text">${product.description}</p>
                                <p class="mb-0"> 價格： <span class="text-decoration-line-through text-muted">$${(product.max_price * 1.2).toFixed(0)}</span></p>
                                <p class="text-danger fs-4 mt-0 strong">$${product.min_price}</p>
                            </div>
                            <div style="position: absolute; top: 100%; left: 50%; transform: translate(-50%, -50%); z-index: 2; width: 80%;">
                                <span class="btn btn-success" style="border-radius: 20px; width: 100%; display: block;">查看詳情</span>
                            </div>
                        </div>
                    </a>
                </div>
            `);
            });
        }
    });
</script>
<?php
require_once __DIR__ . '/../footer.php';
?>