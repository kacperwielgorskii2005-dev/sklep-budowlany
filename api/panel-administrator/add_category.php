<?php
header('Content-Type: application/json');
require_once('../../api/database.php');

$conn = $db;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowa metoda żądania']);
    exit;
}

$name = $_POST['name'] ?? null;
$description = $_POST['description'] ?? '';

if (!$name) {
    echo json_encode(['success' => false, 'message' => 'Nazwa kategorii jest wymagana']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $description);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Kategoria została dodana']);
} else {
    echo json_encode(['success' => false, 'message' => 'Błąd podczas dodawania: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>