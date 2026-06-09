<?php
require_once('CartFunctions.php');

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

error_log("UpdateCart.php - Before update - Cart: " . print_r($_SESSION['cart'] ?? 'NOT SET', true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    addOrUpdateCart($productId, $quantity);
    
    error_log("UpdateCart.php - After update - Cart: " . print_r($_SESSION['cart'], true));

    global $db;
    echo json_encode([
        'success' => true,
        'quantity' => getProductQuantity($productId, $db)
    ]);
}
?>
