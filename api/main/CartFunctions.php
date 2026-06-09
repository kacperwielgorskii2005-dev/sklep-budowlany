<?php
require_once('../../api/database.php');

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['user'])){
    $_SESSION['user'] = 'none';
}
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

$conn = $db;

function getCustomerId($conn) {
    if (!isset($_SESSION['user']) || $_SESSION['user'] == 'none') return null;
    $userLogin = $_SESSION['user'];
    $userLoginEscaped = mysqli_real_escape_string($conn, $userLogin);
    $res = mysqli_query($conn, "SELECT customer_id FROM customers WHERE login = '$userLoginEscaped' LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) {
        return (int)mysqli_fetch_assoc($res)['customer_id'];
    }
    return null;
}

function getUserCart($conn) {
    $customerId = getCustomerId($conn);
    if (!$customerId) return null;

    $cartRes = mysqli_query($conn, "SELECT * FROM carts WHERE customer_id = $customerId LIMIT 1");
    if ($cartRes && mysqli_num_rows($cartRes) > 0) {
        return mysqli_fetch_assoc($cartRes);
    } else {
        mysqli_query($conn, "INSERT INTO carts (customer_id) VALUES ($customerId)");
        return getUserCart($conn);
    }
}

function addOrUpdateCart($productId, $quantity) {
    global $db;

    $productId = (int)$productId;
    $quantity = (int)$quantity;

    error_log("addOrUpdateCart called - Product: $productId, Qty: $quantity, User: " . $_SESSION['user']);
    error_log("Cart BEFORE modification: " . print_r($_SESSION['cart'] ?? 'NOT SET', true));

    if (!isset($_SESSION['user']) || $_SESSION['user'] == 'none') {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        error_log("Guest cart AFTER modification: " . print_r($_SESSION['cart'], true));
    } else {
        $cart = getUserCart($db);
        if (!$cart) return;
        $cartId = (int)$cart['cart_id'];

        if ($quantity <= 0) {
            mysqli_query($db, "DELETE FROM cart_items WHERE cart_id = $cartId AND product_id = $productId");
        } else {
            $check = mysqli_query($db, "SELECT * FROM cart_items WHERE cart_id = $cartId AND product_id = $productId");
            if ($check && mysqli_num_rows($check) > 0) {
                mysqli_query($db, "UPDATE cart_items SET quantity = $quantity WHERE cart_id = $cartId AND product_id = $productId");
            } else {
                mysqli_query($db, "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES ($cartId, $productId, $quantity)");
            }
        }
    }
}

function getProductQuantity($productId, $conn = null) {
    $productId = (int)$productId;
    if (!isset($_SESSION['user']) || $_SESSION['user'] == 'none') {
        return isset($_SESSION['cart'][$productId]) ? (int)$_SESSION['cart'][$productId] : 0;
    } else {
        if (!$conn) return 0;
        $cart = getUserCart($conn);
        if (!$cart) return 0;
        $cartId = (int)$cart['cart_id'];
        $res = mysqli_query($conn, "SELECT quantity FROM cart_items WHERE cart_id = $cartId AND product_id = $productId LIMIT 1");
        if ($res && mysqli_num_rows($res) > 0) {
            return (int)mysqli_fetch_assoc($res)['quantity'];
        }
        return 0;
    }
}
?>
