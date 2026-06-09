<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../products/ProductImages.php';

$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Brak ID produktu']);
    exit();
}

$stmt = $db->prepare("SELECT image_url FROM products WHERE product_id = ? LIMIT 1");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Produkt nie istnieje']);
    exit();
}

echo json_encode([
    'success' => true,
    'image_url' => $product['image_url'],
    'images' => getProductImages($product['image_url'])
], JSON_UNESCAPED_UNICODE);
?>
