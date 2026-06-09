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

$first_name  = trim($_POST['first_name'] ?? '');
$last_name   = trim($_POST['last_name'] ?? '');
$email       = trim($_POST['email'] ?? '');
$phone       = trim($_POST['phone'] ?? '');
$address     = trim($_POST['address'] ?? '');
$city        = trim($_POST['city'] ?? '');
$postal_code = trim($_POST['postal_code'] ?? '');

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email nie może być pusty']);
    exit;
}

$checkEmail = $conn->prepare("SELECT customer_id FROM customers WHERE email = ? AND customer_id != ?");
$checkEmail->bind_param("si", $email, $id);
$checkEmail->execute();
$res = $checkEmail->get_result();

if ($res->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Ten email jest już używany przez innego klienta']);
    exit;
}

$stmt = $conn->prepare("
    UPDATE customers 
    SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, city = ?, postal_code = ?
    WHERE customer_id = ?
");

$stmt->bind_param(
    "sssssssi",
    $first_name,
    $last_name,
    $email,
    $phone,
    $address,
    $city,
    $postal_code,
    $id
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Dane klienta zaktualizowane pomyślnie']);
} else {
    echo json_encode(['success' => false, 'message' => 'Błąd aktualizacji: ' . $conn->error]);
}
?>
