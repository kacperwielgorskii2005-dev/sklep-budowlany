<?php
require_once ('../../api/database.php');
require_once __DIR__ . '/../products/ProductImages.php';

$conn = $db;

function getOrders($conn, $status = null, $limit = null) {
    $sql = "
        SELECT 
            o.order_id,
            o.customer_id,
            o.order_date,
            GROUP_CONCAT(p.product_name SEPARATOR ', ') AS product_name,
            o.total_amount,
            o.status,
            CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
            (SELECT SUM(quantity) FROM order_items WHERE order_id = o.order_id) AS items_count
        FROM orders o
        LEFT JOIN customers c ON c.customer_id = o.customer_id
        LEFT JOIN order_items oi ON oi.order_id = o.order_id
        LEFT JOIN products p ON p.product_id = oi.product_id
    ";

    if ($status !== null) {
        $sql .= " WHERE o.status = '" . $conn->real_escape_string($status) . "'";
    }

    $sql .= " GROUP BY o.order_id";

    $sql .= " ORDER BY o.order_date DESC";

    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$limit;
    }

    return $conn->query($sql);
}


function statusBadge($status) {
    $map = [
        "New"        => ["status-new", "Nowe"],
        "Pending"    => ["status-pending", "Oczekujące"],
        "Processing" => ["status-processing", "W realizacji"],
        "Completed"  => ["status-completed", "Zrealizowane"],
        "Cancelled"  => ["status-cancelled", "Anulowane"],
    ];

    if (!isset($map[$status])) return $status;

    return "<span class='status-badge {$map[$status][0]}'>{$map[$status][1]}</span>";
}

function getCategoriesWithProducts($conn) {
    return $conn->query("
        SELECT 
            c.category_id,
            c.category_name,
            COUNT(p.product_id) AS products_count
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.category_id
        GROUP BY c.category_id
        ORDER BY c.category_name ASC
    ");
}

function getProductsByCategory($conn, $catId) {
    $catId = (int)$catId;
    return $conn->query("
        SELECT 
            product_id,
            product_name,
            description,
            image_url,
            price,
            stock_quantity
        FROM products
        WHERE category_id = $catId
        ORDER BY product_name ASC
    ");
}

function getCustomers($conn) {
    $query = "
        SELECT 
            c.customer_id,
            c.login,
            c.first_name,
            c.last_name,
            c.email,
            c.phone,
            c.address,
            c.city,
            c.postal_code,
            c.country,
            c.created_at,
            (
                SELECT o.notes
                FROM orders o
                WHERE o.customer_id = c.customer_id AND o.notes IS NOT NULL AND o.notes != ''
                ORDER BY o.order_date DESC
                LIMIT 1
            ) AS latest_order_notes,
            COUNT(o.order_id) AS orders_count,
            COALESCE(SUM(o.total_amount), 0) AS total_spent
        FROM customers c
        LEFT JOIN orders o ON c.customer_id = o.customer_id
        GROUP BY 
            c.customer_id,
            c.login,
            c.first_name,
            c.last_name,
            c.email,
            c.phone,
            c.address,
            c.city,
            c.postal_code,
            c.country,
            c.created_at
        ORDER BY c.created_at DESC
    ";

    $result = $conn->query($query);

    if (!$result) {
        error_log("Błąd zapytania getCustomers: " . $conn->error);
        return null;
    }

    return $result;
}

function getDashboardStats($conn) {
    $periods = [
        'today' => [
            'label' => 'dzisiaj',
            'orders_where' => 'DATE(o.order_date) = CURDATE()',
            'customers_where' => 'DATE(c.created_at) = CURDATE()'
        ],
        'week' => [
            'label' => 'w tym tygodniu',
            'orders_where' => 'YEARWEEK(o.order_date, 1) = YEARWEEK(CURDATE(), 1)',
            'customers_where' => 'YEARWEEK(c.created_at, 1) = YEARWEEK(CURDATE(), 1)'
        ],
        'month' => [
            'label' => 'w tym miesiącu',
            'orders_where' => 'YEAR(o.order_date) = YEAR(CURDATE()) AND MONTH(o.order_date) = MONTH(CURDATE())',
            'customers_where' => 'YEAR(c.created_at) = YEAR(CURDATE()) AND MONTH(c.created_at) = MONTH(CURDATE())'
        ],
        'year' => [
            'label' => 'w tym roku',
            'orders_where' => 'YEAR(o.order_date) = YEAR(CURDATE())',
            'customers_where' => 'YEAR(c.created_at) = YEAR(CURDATE())'
        ],
    ];

    $stats = [];

    foreach ($periods as $key => $config) {
        $ordersSql = "
            SELECT
                COUNT(*) AS orders_count,
                COALESCE(SUM(CASE WHEN status = 'New' THEN 1 ELSE 0 END), 0) AS new_orders,
                COALESCE(SUM(total_amount), 0) AS revenue
            FROM orders o
            WHERE {$config['orders_where']}
        ";
        $customersSql = "
            SELECT COUNT(*) AS customers_count
            FROM customers c
            WHERE {$config['customers_where']}
        ";

        $ordersResult = $conn->query($ordersSql);
        $customersResult = $conn->query($customersSql);
        $ordersRow = $ordersResult ? $ordersResult->fetch_assoc() : [];
        $customersRow = $customersResult ? $customersResult->fetch_assoc() : [];

        $stats[$key] = [
            'orders' => (int)($ordersRow['orders_count'] ?? 0),
            'newOrders' => (int)($ordersRow['new_orders'] ?? 0),
            'revenue' => number_format((float)($ordersRow['revenue'] ?? 0), 2, ',', ' ') . ' zł',
            'customers' => (int)($customersRow['customers_count'] ?? 0),
            'changeOrders' => 'Zamówienia ' . $config['label'],
            'changeNewOrders' => 'Nowe zamówienia ' . $config['label'],
            'changeRevenue' => 'Wartość zamówień ' . $config['label'],
            'changeCustomers' => 'Nowi klienci ' . $config['label']
        ];
    }

    return $stats;
}

?>
