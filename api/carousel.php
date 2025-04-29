<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

// 實體路徑
$carouselDir = __DIR__ . '/../images/carousel';
// 網址路徑
$carouselUrl = BASE_URL . '/../images/carousel';

$response = [];

if (is_dir($carouselDir)) {
    $images = array_diff(scandir($carouselDir), array('.', '..', '.gitkeep'));
    foreach ($images as $image) {
        $response[] = [
            'url' => $carouselUrl . '/' . $image,
            'alt' => pathinfo($image, PATHINFO_FILENAME)
        ];
    }
}

echo json_encode($response);
?>