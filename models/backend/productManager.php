<?php
// 商品管理文件

// 防止直接訪問此文件
if (!defined('INCLUDED_FROM_ADMIN') && !isset($_SESSION['logged_in'])) {
    header('Location: ../../login.php');
    exit;
}

// 引入數據庫連接
require_once __DIR__ . '/../../api/db.php';

// 獲取所有商品
function getAllProducts()
{
    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("SELECT p.ProductID, p.Type, p.ProductName, p.Introdution, p.isActive, 
                              COUNT(o.OptionID) as OptionCount 
                              FROM Product p 
                              LEFT JOIN Options o ON p.ProductID = o.ProductID 
                              GROUP BY p.ProductID");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching products: " . $e->getMessage());
        return [];
    }
}

// 獲取商品選項
function getProductOptions($productId)
{
    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("SELECT * FROM Options WHERE ProductID = ?");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching product options: " . $e->getMessage());
        return [];
    }
}

// 獲取商品類型列表
function getProductTypes()
{
    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT Type FROM Product");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        error_log("Error fetching product types: " . $e->getMessage());
        return [];
    }
}

// 嘗試獲取商品列表
try {
    $products = getAllProducts();
    $productTypes = getProductTypes();
} catch (Exception $e) {
    error_log("Error in product manager: " . $e->getMessage());
    $products = [];
    $productTypes = [];
}
?>

<div class="container-fluid p-0">
    <!-- 商品管理頁面標題 -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-box-seam me-2"></i>商品管理</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-circle me-2"></i>新增商品
        </button>
    </div>

    <!-- 商品篩選區 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="filterType" class="form-label">商品類型</label>
                    <select class="form-select" id="filterType">
                        <option value="">全部類型</option>
                        <?php foreach ($productTypes as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>">
                                <?php echo htmlspecialchars($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filterStatus" class="form-label">商品狀態</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">全部狀態</option>
                        <option value="1">啟用</option>
                        <option value="0">停用</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchProduct" class="form-label">搜尋商品</label>
                    <input type="text" class="form-control" id="searchProduct" placeholder="輸入商品名稱或ID">
                </div>
            </div>
        </div>
    </div>

    <!-- 商品列表 -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>商品名稱</th>
                            <th>類型</th>
                            <th>選項數量</th>
                            <th>狀態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="6" class="text-center">暫無商品數據</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['ProductID']); ?></td>
                                    <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                                    <td><?php echo htmlspecialchars($product['Type']); ?></td>
                                    <td><?php echo htmlspecialchars($product['OptionCount']); ?></td>
                                    <td>
                                        <?php if ($product['isActive'] == 1): ?>
                                            <span class="badge bg-success">啟用</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">停用</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-product"
                                                data-id="<?php echo htmlspecialchars($product['ProductID']); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info view-options"
                                                data-id="<?php echo htmlspecialchars($product['ProductID']); ?>">
                                                <i class="bi bi-list-ul"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-product"
                                                data-id="<?php echo htmlspecialchars($product['ProductID']); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- 分頁控制 -->
            <nav aria-label="商品列表分頁">
                <ul class="pagination justify-content-center mt-4">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">上一頁</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">下一頁</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- 新增商品模態框 -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addProductModalLabel">新增商品</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                        <div class="mb-3">
                            <label for="productName" class="form-label">商品名稱</label>
                            <input type="text" class="form-control" id="productName" required>
                        </div>
                        <div class="mb-3">
                            <label for="productType" class="form-label">商品類型</label>
                            <select class="form-select" id="productType" required>
                                <option value="" selected disabled>選擇商品類型</option>
                                <?php foreach ($productTypes as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>">
                                        <?php echo htmlspecialchars($type); ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="new">新增類型...</option>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="newTypeGroup">
                            <label for="newType" class="form-label">新類型名稱</label>
                            <input type="text" class="form-control" id="newType">
                        </div>
                        <div class="mb-3">
                            <label for="productIntro" class="form-label">商品介紹</label>
                            <textarea class="form-control" id="productIntro" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="productActive" checked>
                                <label class="form-check-label" for="productActive">商品狀態（啟用/停用）</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" id="saveProduct">保存商品</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 當選擇「新增類型」時顯示新類型輸入框
    document.getElementById('productType').addEventListener('change', function () {
        const newTypeGroup = document.getElementById('newTypeGroup');
        if (this.value === 'new') {
            newTypeGroup.classList.remove('d-none');
        } else {
            newTypeGroup.classList.add('d-none');
        }
    });

    // 商品篩選功能
    document.getElementById('filterType').addEventListener('change', filterProducts);
    document.getElementById('filterStatus').addEventListener('change', filterProducts);
    document.getElementById('searchProduct').addEventListener('input', filterProducts);

    function filterProducts() {
        console.log('篩選商品功能尚未實現');
        // 實際篩選功能將在後續開發中實現
    }

    // 保存商品按鈕點擊事件
    document.getElementById('saveProduct').addEventListener('click', function () {
        console.log('保存商品功能尚未實現');
        // 實際保存功能將在後續開發中實現

        // 顯示成功提示
        const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
        modal.hide();

        // 使用 Bootstrap 的 Toast 組件顯示提示
        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '11';

        toastContainer.innerHTML = `
            <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>商品已成功保存！
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        document.body.appendChild(toastContainer);
        const toastElement = toastContainer.querySelector('.toast');
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();

        // 3秒後移除 Toast 元素
        setTimeout(() => {
            document.body.removeChild(toastContainer);
        }, 3500);
    });
</script>