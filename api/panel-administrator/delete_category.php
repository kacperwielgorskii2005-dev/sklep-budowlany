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
    echo json_encode(['success' => false, 'message' => 'Brak ID kategorii']);
    exit;
}

$stmt = $conn->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Kategoria została usunięta']);
} else {
    echo json_encode(['success' => false, 'message' => 'Błąd podczas usuwania: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>