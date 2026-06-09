<?php
require_once('../../api/database.php');
require_once('../../api/main/CartFunctions.php');

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if (!$productId && $action !== 'clear') {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

try {
    switch ($action) {
        case 'update':
            if ($quantity < 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
                exit;
            }
            addOrUpdateCart($productId, $quantity);
            $newQuantity = getProductQuantity($productId, $db);
            echo json_encode([
                'success' => true,
                'message' => 'Cart updated',
                'quantity' => $newQuantity
            ]);
            break;
            
        case 'remove':
            addOrUpdateCart($productId, 0);
            echo json_encode([
                'success' => true,
                'message' => 'Item removed from cart'
            ]);
            break;
            
        case 'clear':
            if (!isset($_SESSION['user']) || $_SESSION['user'] == 'none') {
                $_SESSION['cart'] = [];
            } else {
                $customerId = getCustomerId($db);
                if ($customerId) {
                    $cartResult = mysqli_query($db, "SELECT cart_id FROM carts WHERE customer_id = $customerId LIMIT 1");
                    if ($cartResult && mysqli_num_rows($cartResult) > 0) {
                        $cartId = mysqli_fetch_assoc($cartResult)['cart_id'];
                        mysqli_query($db, "DELETE FROM cart_items WHERE cart_id = $cartId");
                    }
                }
            }
            echo json_encode([
                'success' => true,
                'message' => 'Cart cleared'
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Cart update error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred'
    ]);
}
?>