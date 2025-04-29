<?php
require_once __DIR__ . '/../header.php';
// 首頁視圖文件
?>
<div class="container-fluid px-0 mt-0">
    <!-- 輪播圖片 -->
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner" id="carousel-inner">
            <!-- 轮播图片将通过AJAX动态加载 -->
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

    <div class="container mt-3">
        <ul class="nav nav-pills mb-4 justify-content-center gap-4 fs-6" id="product-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-type="aluminum,zipper">全部行李箱</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-type="accessories">行李箱配件</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-type="travel">旅行周邊</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="#" data-type="featured">歐印精選</a>
            </li>
        </ul>


        <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
            <h2 class="text-center mb-0">行李箱系列</h2>
            <div class="d-flex gap-2">
                <select class="form-select" style="width: auto;" id="suitcase-filter">
                    <option value="all">全部行李箱</option>
                    <option value="aluminum">鋁框行李箱</option>
                    <option value="zipper">拉鍊行李箱</option>
                </select>
                <select class="form-select" style="width: auto;" id="size-filter">
                    <option value="all">全部尺寸</option>
                    <option value="large">28吋以上</option>
                    <option value="medium-large">25~28吋</option>
                    <option value="medium">21~24吋</option>
                    <option value="small">20吋以下</option>
                </select>
            </div>
        </div>
        <div class="row" id="suitcase-products">
            <!-- 商品將通過AJAX動態加載 -->
        </div>

        <div class="row" id="accessories-products">
            <!-- 商品將通過AJAX動態加載 -->
        </div>

        <div class="row" id="travel-products">
            <!-- 商品將通過AJAX動態加載 -->
        </div>

        <div class="row" id="featured-products">
            <!-- 商品將通過AJAX動態加載 -->
        </div>
    </div>

    <? require_once __DIR__ . '/../footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    console.log('<?php echo BASE_URL; ?>');
    console.log('<?php echo BASE_URL . '/images/carousel'; ?>');
    $(document).ready(function () {
        // 處理導航標籤點擊
        $('#product-tabs .nav-link').click(function (e) {
            e.preventDefault();
            $('#product-tabs .nav-link').removeClass('active');
            $(this).addClass('active');

            const type = $(this).data('type');
            loadProducts(type);
        });

        // 预加载所有商品数据
        let allProductsData = {};

        // 页面加载时预加载所有分类数据
        $(window).on('load', function () {
            const productTypes = ['aluminum', 'zipper', 'featured', 'travel', 'accessories'];

            productTypes.forEach(type => {
                $.ajax({
                    url: 'api/products.php',
                    data: { type: type },
                    method: 'GET',
                    success: function (data) {
                        allProductsData[type] = data;
                    },
                    error: function (xhr, status, error) {
                        console.error('预加载商品数据失败:', error);
                    }
                });
            });
        });

        function loadProducts(type) {
            // 隐藏所有商品区域和标题
            $('.row[id$="-products"]').hide();
            $('h2.text-center.mb-0').hide();
            $('.form-select').hide();

            // 处理行李箱类型（同时显示铝框和拉链行李箱）
            if (type === 'aluminum,zipper') {
                $('#suitcase-products').show();
                $('h2.text-center.mb-0:contains("行李箱系列")').show();
                $('.form-select').show();

                // 检查是否已预加载数据
                if (allProductsData['aluminum'] && allProductsData['zipper']) {
                    const allProducts = [...allProductsData['aluminum'], ...allProductsData['zipper']];
                    renderProducts(allProducts, '#suitcase-products');
                } else {
                    loadSuitcases();
                }
                return;
            }

            // 如果数据已预加载，直接渲染
            if (allProductsData[type]) {
                renderFromCache(type);
            } else {
                // 如果数据未预加载，则从服务器获取
                $.ajax({
                    url: 'api/products.php',
                    data: { type: type },
                    method: 'GET',
                    success: function (data) {
                        allProductsData[type] = data; // 缓存数据
                        renderFromCache(type);
                    },
                    error: function (xhr, status, error) {
                        console.error('加載商品失敗:', error);
                    }
                });
            }
        }

        function renderFromCache(type) {
            const container = `#${type}-products`;
            const titleMap = {
                'featured': '精選商品',
                'travel': '旅行系列',
                'accessories': '配件系列'
            };

            $(container).show();
            if (titleMap[type]) {
                $(`h2.text-center.mb-4.mt-5:contains("${titleMap[type]}")`).show();
            }
            renderProducts(allProductsData[type], container);
        }
        // 加載精選商品
        $.ajax({
            url: 'api/carousel.php',
            method: 'GET',
            success: function (data) {
                const carouselInner = $('#carousel-inner');
                carouselInner.empty();
                data.forEach(function (image, index) {
                    carouselInner.append(`
                        <div class="carousel-item${index === 0 ? ' active' : ''}">
                            <img src="${image.url}" class="d-block w-100" alt="${image.alt}">
                        </div>
                    `);
                });
            },
            error: function (xhr, status, error) {
                console.error('加载轮播图片失败:', error);
            }
        });

        // 加載行李箱商品
        function loadSuitcases(type = 'all', size = 'all') {
            if (type === 'all') {
                // 同時加載鋁框和拉鍊行李箱
                Promise.all([
                    $.ajax({
                        url: 'api/products.php',
                        data: { type: 'aluminum' },
                        method: 'GET'
                    }),
                    $.ajax({
                        url: 'api/products.php',
                        data: { type: 'zipper' },
                        method: 'GET'
                    })
                ]).then(function ([aluminumData, zipperData]) {
                    const allProducts = [...aluminumData, ...zipperData];
                    const filteredProducts = filterProductsBySize(allProducts, size);
                    renderProducts(filteredProducts, '#suitcase-products');
                }).catch(function (error) {
                    console.error('加載行李箱失敗:', error);
                });
            } else {
                // 加載指定類型的行李箱
                $.ajax({
                    url: 'api/products.php',
                    data: { type: type },
                    method: 'GET',
                    success: function (data) {
                        const filteredProducts = filterProductsBySize(data, size);
                        renderProducts(filteredProducts, '#suitcase-products');
                    },
                    error: function (xhr, status, error) {
                        console.error('加載行李箱失敗:', error);
                    }
                });
            }
        }

        // 根據尺寸篩選商品
        function filterProductsBySize(products, size) {
            if (size === 'all') return products;

            return products.filter(product => {
                const sizeNumber = parseInt(product.sizes);
                switch (size) {
                    case 'large': // 28吋以上
                        return sizeNumber >= 28;
                    case 'medium-large': // 25~28吋
                        return sizeNumber >= 25 && sizeNumber < 28;
                    case 'medium': // 21~24吋
                        return sizeNumber >= 21 && sizeNumber < 25;
                    case 'small': // 20吋以下
                        return sizeNumber <= 20;
                    default:
                        return true;
                }
            });
        }

        // 初始加載所有行李箱
        loadSuitcases();

        // 監聽篩選器變化
        $('#suitcase-filter, #size-filter').change(function () {
            const selectedType = $('#suitcase-filter').val();
            const selectedSize = $('#size-filter').val();
            loadSuitcases(selectedType, selectedSize);
        });


        function renderProducts(products, container) {
            // 清空容器内容
            $(container).empty();

            // 檢查是否有商品
            if (!products || products.length === 0) {
                $(container).append(`
                    <div class="col-12 text-center py-5">
                        <h4 class="text-muted">無相關商品</h4>
                    </div>
                `);
                return;
            }

            // 使用对象来跟踪已显示的商品ID
            const displayedProducts = {};
            // 先过滤掉重复商品ID
            const uniqueProducts = products.filter(product => {
                if (displayedProducts[product.id]) {
                    return false;
                }
                displayedProducts[product.id] = true;
                return true;
            });

            $.each(uniqueProducts, function (index, product) {
                $(container).append(`
                <div class="col-md-4 mb-4 d-flex justify-content-center">
                    <div class="card" style="width: 100%; max-width: 320px; border-radius: 20px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                        <a href="index.php?action=products&id=${product.id}" class="text-decoration-none text-black">
                            <img src="${product.image}" class="card-img-top" style="border-radius: 20px 20px 0 0; object-fit: cover; height: auto;" alt="${product.name}">
                            <div class="card-body pb-0 mb-2">
                                <h5 class="card-title">${product.name}</h5>
                                ${product.sizes ? `<p class="card-text mb-0">尺寸：${product.sizes}</p>` : ''}
                                <p class="mb-0"> 價格： <span class="text-decoration-line-through text-muted">$${(product.max_price * 1.2).toFixed(0)}</span></p>
                                <p class="text-danger fs-4 mt-0 strong"><strong>$${product.min_price}</strong></p>
                            </div>
                        </a>
                        <div class="card-footer bg-transparent border-0 text-center pb-3">
                            <a href="index.php?action=products&id=${product.id}" class="btn btn-success" style="border-radius: 20px; width: 80%;">查看詳情</a>
                        </div>
                    </div>
                </div>
            `);
            });
        }
    });
</script>
<?php
require_once __DIR__ . '/../footer.php';
?>