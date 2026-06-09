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
    $newStatus = $_POST['status'] ?? null;

    if (!$orderId || empty($orderId)) {
        throw new Exception('Brak ID zamówienia');
    }

    if (!$newStatus || empty($newStatus)) {
        throw new Exception('Brak nowego statusu');
    }

    $orderId = intval($orderId);

    if ($orderId <= 0) {
        throw new Exception('Nieprawidłowe ID zamówienia');
    }

    $allowedStatuses = ['New', 'Processing', 'Pending', 'Completed', 'Cancelled'];
    if (!in_array($newStatus, $allowedStatuses)) {
        throw new Exception('Nieprawidłowy status');
    }

    $stmt = $conn->prepare("SELECT order_id, status FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Zamówienie nie znalezione');
    }
    
    $order = $result->fetch_assoc();
    $oldStatus = $order['status'];
    $stmt->close();

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $newStatus, $orderId);

    if (!$stmt->execute()) {
        throw new Exception('Błąd podczas aktualizacji statusu: ' . $conn->error);
    }

    $stmt->close();

    $stmt = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $items = $stmt->get_result();
    $stmt->close();

    if ($items->num_rows === 0) {
        throw new Exception("Brak produktów w zamówieniu");
    }

    if ($newStatus === 'Completed' && $oldStatus !== 'Completed') {

        $conn->begin_transaction();

        try {
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $qty = $item['quantity'];

                $stmt = $conn->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $stockRes = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$stockRes) {
                    throw new Exception("Produkt ID $productId nie istnieje");
                }

                if ($stockRes['stock_quantity'] < $qty) {
                    throw new Exception("Brak wystarczającego stanu dla produktu ID $productId");
                }

                $stmt = $conn->prepare(
                    "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?"
                );
                $stmt->bind_param("ii", $qty, $productId);
                $stmt->execute();
                $stmt->close();
            }

            $conn->commit();

        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }

    if ($oldStatus === 'Completed' && $newStatus !== 'Completed') {

        $conn->begin_transaction();

        try {
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $qty = $item['quantity'];

                $stmt = $conn->prepare(
                    "UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id = ?"
                );
                $stmt->bind_param("ii", $qty, $productId);
                $stmt->execute();
                $stmt->close();
            }

            $conn->commit();

        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }

    $conn->close();

    $statusTranslations = [
        'New' => 'Nowe',
        'Processing' => 'W realizacji',
        'Pending' => 'Oczekujące',
        'Completed' => 'Zrealizowane',
        'Cancelled' => 'Anulowane'
    ];

    $response = [
        'success' => true,
        'message' => 'Status zamówienia został zmieniony na: ' . ($statusTranslations[$newStatus] ?? $newStatus),
        'old_status' => $oldStatus,
        'new_status' => $newStatus
    ];

} catch (Exception $e) {
    error_log('Update order status error: ' . $e->getMessage());
    
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

ob_end_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>