<?php
require_once __DIR__ . '/CartFunctions.php';
require_once __DIR__ . '/../favorites/FavoriteFunctions.php';
require_once __DIR__ . '/../products/ProductImages.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = $db;


function DisplayProducts($filter, $prompt, $conn) {
    $limit = 9;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    $promptEscaped = mysqli_real_escape_string($conn, $prompt);

    $whereClauses = [];

    if (isset($_GET['categories']) && is_array($_GET['categories']) && count($_GET['categories']) > 0) {
        $categories = array_map(function($c) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $c) . "'";
        }, $_GET['categories']);
        $whereClauses[] = "c.category_name IN (" . implode(",", $categories) . ")";
    }

    if (!empty($promptEscaped)) {
        $whereClauses[] = "p.product_name LIKE '%$promptEscaped%'";
    }

    $whereSQL = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

    $sql = "SELECT p.*, c.category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            $whereSQL
            LIMIT $limit OFFSET $offset";

    $countSql = "SELECT COUNT(*) FROM products p
                 LEFT JOIN categories c ON p.category_id = c.category_id
                 $whereSQL";

    $result = mysqli_query($conn, $sql);
    $totalRows = mysqli_fetch_row(mysqli_query($conn, $countSql))[0];
    $totalPages = ceil($totalRows / $limit);
    $favoriteIds = getFavoriteProductIds($conn, getLoggedCustomerId($conn));

    echo '<div class="products-grid" id="products-grid">';

    if ($totalRows === 0) {
        echo '<div class="no-results" style="grid-column:1/-1; text-align:center; padding:20px; margin-top:200px; margin-bottom:200px; font-size:1.2rem; color:#555;">
                Nie znaleziono produktów spełniających kryteria
              </div>';
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $productId = $row['product_id'];
            $quantityInCart = getProductQuantity($productId, $conn);
            $isFavorite = in_array((int)$productId, $favoriteIds, true);
            $mainImage = getProductMainImage($row['image_url']);

            $statusClass = $row['stock_quantity'] > 0 ? "status-available" : "status-unavailable";
            $statusText = $row['stock_quantity'] > 0 ? "Na stanie" : "Brak w magazynie";

            echo '<div data-aos="fade-up">
                    <div class="product-card">
                        <button class="favorite-btn' . ($isFavorite ? ' active' : '') . '" data-product-id="' . $productId . '" title="Ulubione">♥</button>
                        <a class="product-link" href="../product/index.php?id=' . $productId . '">
                            <div class="product-image-frame">
                                <img src="' . htmlspecialchars($mainImage) . '" alt="Product Image" class="product-image">
                            </div>
                            <h3 class="product-name">' . htmlspecialchars($row['product_name']) . '</h3>
                        </a>
                        <p class="product-description">' . htmlspecialchars($row['description']) . '</p>
                        <div class="product-status ' . $statusClass . '">
                            <span class="status-dot"></span>
                            ' . $statusText . '
                        </div>
                        <div class="product-price">' . $row['price'] . ' zł / szt.</div>';

            if ($quantityInCart > 0) {
                echo '<div class="quantity-selector" data-product-id="' . $productId . '">
                        <button class="decrease">-</button>
                        <span class="quantity">' . $quantityInCart . '</span>
                        <button class="increase">+</button>
                      </div>';
            } else {
                if ($row['stock_quantity'] <= 0){
                    echo '<button class="add-to-cart-btn" data-product-id="' . $productId . '" disabled>Brak towaru</button>';
                }else {
                    echo '<button class="add-to-cart-btn" data-product-id="' . $productId . '">Dodaj</button>';
                }
            }

            echo '  </div>
                  </div>';
        }
    }

    echo '</div>';

    if ($totalRows > 0) {
        RenderPagination($page, $totalPages);
    }
}

function RenderPagination($page, $totalPages) {
    echo '<div data-aos="fade-up"><div class="pagination">';

    if ($page > 1) {
        echo '<a class="pagination-btn" href="?page=' . ($page - 1) . '"><span>Poprzednia</span></a>';
    } else {
        echo '<button class="pagination-btn" disabled>Poprzednia</button>';
    }

    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i == $page) ? "active" : "";
        echo '<a class="pagination-btn ' . $active . '" href="?page=' . $i . '">' . $i . '</a>';
    }

    if ($page < $totalPages) {
        echo '<a class="pagination-btn" href="?page=' . ($page + 1) . '"><span>Następna</span></a>';
    } else {
        echo '<button class="pagination-btn" disabled>Następna</button>';
    }

    echo '</div></div>';
}
?>
