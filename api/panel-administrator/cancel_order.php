<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

header('Content-Type: application/json; charset=utf-8');
ob_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Nieprawidłowa metoda żądania');
    }

    require_once('../../api/database.php');

    if (!isset($conn)) {
        $conn = $db;
    }

    $orderId = $_POST['order_id'] ?? null;

    if (!$orderId || empty($orderId)) {
        throw new Exception('Brak ID zamówienia');
    }

    $orderId = intval($orderId);

    if ($orderId <= 0) {
        throw new Exception('Nieprawidłowe ID zamówienia');
    }

    $stmt = $conn->prepare("SELECT order_id, status FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Zamówienie nie znalezione');
    }
    
    $order = $result->fetch_assoc();
    $stmt->close();

    if ($order['status'] === 'Cancelled') {
        throw new Exception('Zamówienie jest już anulowane');
    }

    $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE order_id = ?");
    $stmt->bind_param("i", $orderId);

    if (!$stmt->execute()) {
        throw new Exception('Błąd podczas anulowania zamówienia: ' . $conn->error);
    }

    $stmt->close();
    $conn->close();

    $response = [
        'success' => true,
        'message' => 'Zamówienie zostało anulowane pomyślnie'
    ];

} catch (Exception $e) {
    error_log('Cancel order error: ' . $e->getMessage());
    
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

ob_end_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>