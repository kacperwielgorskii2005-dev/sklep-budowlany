<?php
header('Content-Type: application/json');
require_once('../../api/database.php');

$conn = $db;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowa metoda żądania']);
    exit;
}

$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? null;
$description = $_POST['description'] ?? '';

if (!$id || !$name) {
    echo json_encode(['success' => false, 'message' => 'Brak wymaganych danych']);
    exit;
}

$stmt = $conn->prepare("UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?");
$stmt->bind_param("ssi", $name, $description, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Kategoria została zaktualizowana']);
} else {
    echo json_encode(['success' => false, 'message' => 'Błąd podczas aktualizacji: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>