<?php
require_once __DIR__ . '/db.php';

// 只在直接訪問時設置 JSON 頭，而不是在被引入時
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('Content-Type: application/json');
}

function getProducts()
{
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("
            SELECT 
                p.*, 
                MIN(o.Price) AS MinPrice, 
                MAX(o.Price) AS MaxPrice 
            FROM Product p 
            LEFT JOIN Options o 
                ON p.ProductID = o.ProductID 
            WHERE p.isActive = 1 
            GROUP BY p.ProductID
        ");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $products;
    } catch (PDOException $e) {
        return [];
    }
}

function getProductById(int $productId): ?array
{
    $db = getDBConnection();

    // 1. 取得基本商品欄位
    $stmt = $db->prepare("SELECT * FROM Product WHERE ProductID = :id");
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        return null;
    }

    // 2. 取得價格範圍
    $stmt = $db->prepare("
        SELECT
            MIN(Price) AS MinPrice,
            MAX(Price) AS MaxPrice
        FROM Options
        WHERE ProductID = :id
    ");
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $priceRange = $stmt->fetch(PDO::FETCH_ASSOC);
    $product['MinPrice'] = $priceRange['MinPrice'] ?? 0;
    $product['MaxPrice'] = $priceRange['MaxPrice'] ?? 0;

    // 3. 聚合尺寸選項與顏色（不包含 Stock 在 GROUP BY）
    $stmt = $db->prepare("
        SELECT
            o.Size,
            o.SizeDescription,
            o.Price,
            GROUP_CONCAT(DISTINCT o.Color ORDER BY o.Color SEPARATOR ',') AS Colors
        FROM Options o
        WHERE o.ProductID = :id
        GROUP BY o.Size, o.SizeDescription, o.Price
        ORDER BY o.Size ASC
    ");
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $sizeOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. 針對每個顏色，查出對應的 OptionID 與 Stock
    foreach ($sizeOptions as &$opt) {
        $colors = explode(',', $opt['Colors'] ?? '');
        $opt['colors'] = array_map(function ($color) use ($db, $productId, $opt) {
            $s = $db->prepare("
                SELECT OptionID, Stock
                FROM Options
                WHERE ProductID = :pid
                  AND Size = :size
                  AND Color = :color
                LIMIT 1
            ");
            $s->bindParam(':pid', $productId, PDO::PARAM_INT);
            $s->bindParam(':size', $opt['Size'], PDO::PARAM_STR);
            $s->bindParam(':color', $color, PDO::PARAM_STR);
            $s->execute();
            return $s->fetch(PDO::FETCH_ASSOC);
        }, $colors);
    }
    unset($opt);
    $product['sizeOptions'] = $sizeOptions;

    // 5. 設定主圖（mainImage）欄位，若檔案不存在則 fallback default.jpg
    $defaultMain = "imgs/products/{$productId}/main.jpg";
    $product['mainImage'] = file_exists(__DIR__ . "/../{$defaultMain}")
        ? $defaultMain
        : 'imgs/products/default.jpg';

    // 6. 掃描並收集畫廊圖片
    $galleryImages = [];
    $i = 1;
    while (file_exists(__DIR__ . "/../imgs/products/{$productId}/gallery-{$i}.jpg")) {
        $galleryImages[] = [
            'url' => "imgs/products/{$productId}/gallery-{$i}.jpg"
        ];
        $i++;
    }
    $product['galleryImages'] = $galleryImages;

    return $product;
}

function getProductsByType($type)
{
    $allProducts = getProducts();
    $result = [];

    foreach ($allProducts as $p) {
        $details = getProductById($p['ProductID']);
        $p['details'] = $details;

        if (!isset($p['Type'])) {
            continue;
        }

        switch ($type) {
            case 'luggage':
                if (in_array(strtolower($p['Type']), ['aluminum', 'zipper'])) {
                    $result[] = $p;
                }
                break;
            case 'accessories':
                if (strtolower($p['Type']) === 'accessories') {
                    $result[] = $p;
                }
                break;
            case 'travel':
                if (strtolower($p['Type']) === 'travel') {
                    $result[] = $p;
                }
                break;
            case 'featured':
                if (strtolower($p['Type']) === 'featured') {
                    $result[] = $p;
                }
                break;
        }
    }

    return $result;
}

function renderProductCard(array $product)
{
    $details = $product['details'] ?? $product;

    $minPrice = $details['MinPrice'] ?? 0;
    $maxPrice = $details['MaxPrice'] ?? 0;

    $sizes = array_column($details['sizeOptions'] ?? [], 'Size');
    $filteredSizes = array_filter($sizes, fn($s) => $s != -1);

    $sizeClass = '';
    if (!empty($filteredSizes)) {
        $max = max($filteredSizes);
        if ($max > 28) {
            $sizeClass = 'large';
        } elseif ($max >= 25) {
            $sizeClass = 'medium';
        } elseif ($max >= 21) {
            $sizeClass = 'small';
        } else {
            $sizeClass = 'cabin';
        }
    }

    if (!isset($product['ProductID'])) {
        return;
    }

    // 直接使用 mainImage 欄位，無則 fallback 預設
    $img = $details['mainImage']
        ?? "imgs/products/{$product['ProductID']}/main.jpg";
    // 如果實際檔案不存在，再用 default.jpg
    if (!file_exists(__DIR__ . '/../' . $img)) {
        $img = 'imgs/products/default.jpg';
    }

    $type = htmlspecialchars($product['Type'] ?? 'unknown', ENT_QUOTES);
    $prodId = (int) $product['ProductID'];
    $prodName = htmlspecialchars($product['ProductName'] ?? '商品名稱未設定', ENT_QUOTES);

    ob_start();
    ?>
    <div class="col-lg-3 col-md-4 mb-3 d-flex justify-content-center product-item" data-size="<?= $sizeClass ?>"
        data-material="<?= $type ?>" data-sizes="<?= htmlspecialchars(implode(',', $filteredSizes), ENT_QUOTES) ?>">
        <div class="card" style="width:100%; max-width:320px; border-radius:20px; transition:transform .2s;"
            onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
            <a href="productDetail.php?product_id=<?= $prodId ?>" class="text-decoration-none text-black">
                <img src="<?= htmlspecialchars($img, ENT_QUOTES) ?>" class="card-img-top"
                    style="border-radius:20px 20px 0 0; object-fit:cover;" alt="<?= $prodName ?>">
                <div class="card-body pb-0 mb-2">
                    <h5 class="card-title mb-1"><?= $prodName ?></h5>
                    <?php if (!empty($filteredSizes)): ?>
                        <p class="card-text mb-0">
                            尺寸：<?= implode(', ', $filteredSizes) ?> 吋
                        </p>
                    <?php endif; ?>
                    <p class="mb-0">
                        價格：
                        <span class="text-decoration-line-through text-muted">
                            $<?= number_format(round(($maxPrice ?: 1000) * 1.2), 0) ?>
                        </span>
                    </p>
                    <p class="text-danger fs-4 mt-0">
                        <strong>$<?= number_format($minPrice ?: 800, 0) ?></strong>
                    </p>
                </div>
            </a>
            <div class="card-footer bg-transparent border-0 text-center pb-3">
                <a href="productDetail.php?product_id=<?= $prodId ?>" class="btn btn-success"
                    style="border-radius:20px; width:80%;">
                    查看詳情
                </a>
            </div>
        </div>
    </div>
    <?php
    $output = ob_get_clean();

    if (empty(trim($output))) {
        return '<div class="col-lg-3 col-md-4 mb-3">
                  <div class="alert alert-warning">商品資料載入中...</div>
                </div>';
    }
    return $output;
}

// API 端點處理
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {

    try {
        switch ($_GET['action']) {
            case 'getProducts':
                $type = $_GET['type'] ?? '';
                $products = getProductsByType($type);
                echo json_encode(['success' => true, 'data' => $products]);
                break;

            case 'getProductById':
                if (!isset($_GET['product_id'])) {
                    throw new Exception('Missing product_id parameter');
                }
                $product = getProductById($_GET['product_id']);
                if (!$product) {
                    throw new Exception('Product not found');
                }
                echo json_encode([
                    'success' => true,
                    'data' => $product
                ]);
                break;

            case 'renderProductCard':
                if (!isset($_GET['productId'])) {
                    throw new Exception('Missing productId parameter');
                }
                $product = getProductById($_GET['productId']);
                if (!$product) {
                    throw new Exception('Product not found');
                }
                echo json_encode([
                    'success' => true,
                    'html' => renderProductCard($product)
                ]);
                break;

            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
?>