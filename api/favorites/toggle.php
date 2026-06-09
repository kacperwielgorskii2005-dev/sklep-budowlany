<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/FavoriteFunctions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Nieprawidlowa metoda']);
    exit();
}

$customerId = getLoggedCustomerId($db);
if (!$customerId) {
    echo json_encode(['success' => false, 'message' => 'Zaloguj sie, aby korzystac z ulubionych']);
    exit();
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Brak produktu']);
    exit();
}

ensureFavoritesTable($db);

$stmt = $db->prepare("SELECT favorite_id FROM favorites WHERE customer_id = ? AND product_id = ? LIMIT 1");
$stmt->bind_param("ii", $customerId, $productId);
$stmt->execute();
$result = $stmt->get_result();
$favorite = $result->fetch_assoc();
$stmt->close();

if ($favorite) {
    $stmt = $db->prepare("DELETE FROM favorites WHERE customer_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $customerId, $productId);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true, 'favorite' => false]);
    exit();
}

$stmt = $db->prepare("INSERT INTO favorites (customer_id, product_id) VALUES (?, ?)");
$stmt->bind_param("ii", $customerId, $productId);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'favorite' => true]);
?>
