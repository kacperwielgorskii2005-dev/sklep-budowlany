<?php
header('Content-Type: application/json');
require_once('../../api/database.php');

$conn = $db;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowa metoda']);
    exit;
}

$id = intval($_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Brak ID klienta']);
    exit;
}

$checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE customer_id = ?");
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result()->fetch_assoc();

if ($result['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Nie można usunąć klienta, który ma zamówienia. Zamiast tego ustaw status jako nieaktywny.']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Klient usunięty pomyślnie']);
} else {
    echo json_encode(['success' => false, 'message' => 'Błąd usuwania: ' . $conn->error]);
}
?>