<?php
require_once __DIR__ . '/../products/ProductImages.php';

function getCartItems($conn) {
    $cartItems = [];
    
    if (!isset($_SESSION['user']) || $_SESSION['user'] == 'none') {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return $cartItems;
        }
        
        $productIds = array_keys($_SESSION['cart']);
        $productIdsEscaped = array_map(function($id) use ($conn) {
            return (int)$id;
        }, $productIds);
        
        if (empty($productIdsEscaped)) {
            return $cartItems;
        }
        
        $idsString = implode(',', $productIdsEscaped);
        $sql = "SELECT product_id, product_name, description, price, stock_quantity, image_url 
                FROM products 
                WHERE product_id IN ($idsString)";
        
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $productId = $row['product_id'];
                $quantity = $_SESSION['cart'][$productId];
                
                $cartItems[] = [
                    'id' => $productId,
                    'name' => $row['product_name'],
                    'description' => $row['description'],
                    'price' => (float)$row['price'],
                    'quantity' => $quantity,
                    'image' => getProductMainImage($row['image_url']),
                    'available' => $row['stock_quantity'] > 0
                ];
            }
        }
        
    } else {
        $userLogin = $_SESSION['user'];
        $userLoginEscaped = mysqli_real_escape_string($conn, $userLogin);
        
        $customerQuery = "SELECT customer_id FROM customers WHERE login = '$userLoginEscaped' LIMIT 1";
        $customerResult = mysqli_query($conn, $customerQuery);
        
        if ($customerResult && mysqli_num_rows($customerResult) > 0) {
            $customerId = mysqli_fetch_assoc($customerResult)['customer_id'];
            
            $cartQuery = "SELECT cart_id FROM carts WHERE customer_id = $customerId LIMIT 1";
            $cartResult = mysqli_query($conn, $cartQuery);
            
            if ($cartResult && mysqli_num_rows($cartResult) > 0) {
                $cartId = mysqli_fetch_assoc($cartResult)['cart_id'];
                
                $sql = "SELECT 
                            p.product_id,
                            p.product_name,
                            p.description,
                            p.price,
                            p.stock_quantity,
                            p.image_url,
                            ci.quantity
                        FROM cart_items ci
                        JOIN products p ON ci.product_id = p.product_id
                        WHERE ci.cart_id = $cartId";
                
                $result = mysqli_query($conn, $sql);
                
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $cartItems[] = [
                            'id' => $row['product_id'],
                            'name' => $row['product_name'],
                            'description' => $row['description'],
                            'price' => (float)$row['price'],
                            'quantity' => (int)$row['quantity'],
                            'image' => getProductMainImage($row['image_url']),
                            'available' => $row['stock_quantity'] > 0
                        ];
                    }
                }
            }
        }
    }
    
    return $cartItems;
}

?>
