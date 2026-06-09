<?php
header('Content-Type: application/json');
require_once('../../api/database.php');

$conn = $db;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowa metoda żądania']);
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Brak ID produktu']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Produkt został usunięty']);
} else {
    echo json_encode(['success' => false, 'message' => 'Błąd podczas usuwania: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>