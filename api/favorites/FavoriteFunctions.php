<?php

function ensureFavoritesTable(mysqli $conn): void
{
    mysqli_query($conn, "
        CREATE TABLE IF NOT EXISTS favorites (
            favorite_id INT(11) NOT NULL AUTO_INCREMENT,
            customer_id INT(11) NOT NULL,
            product_id INT(11) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (favorite_id),
            UNIQUE KEY unique_customer_product (customer_id, product_id),
            KEY customer_id (customer_id),
            KEY product_id (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

function getLoggedCustomerId(mysqli $conn): ?int
{
    if (!isset($_SESSION['user']) || $_SESSION['user'] === 'none') {
        return null;
    }

    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE login = ? LIMIT 1");
    $stmt->bind_param("s", $_SESSION['user']);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    $stmt->close();

    return isset($customer['customer_id']) ? (int)$customer['customer_id'] : null;
}

function getFavoriteProductIds(mysqli $conn, ?int $customerId): array
{
    if (!$customerId) {
        return [];
    }

    ensureFavoritesTable($conn);

    $stmt = $conn->prepare("SELECT product_id FROM favorites WHERE customer_id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $ids = [];

    while ($row = $result->fetch_assoc()) {
        $ids[] = (int)$row['product_id'];
    }

    $stmt->close();
    return $ids;
}

?>
