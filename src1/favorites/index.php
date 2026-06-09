<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../api/database.php';
require_once __DIR__ . '/../../api/main/CartFunctions.php';
require_once __DIR__ . '/../../api/favorites/FavoriteFunctions.php';
require_once __DIR__ . '/../../api/products/ProductImages.php';

if (!isset($_SESSION['user']) || $_SESSION['user'] === 'none') {
    header('Location: ../panel-login/index.php');
    exit();
}

$customerId = getLoggedCustomerId($db);
ensureFavoritesTable($db);

$stmt = $db->prepare("
    SELECT p.*, c.category_name
    FROM favorites f
    JOIN products p ON f.product_id = p.product_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE f.customer_id = ?
    ORDER BY f.created_at DESC
");
$stmt->bind_param("i", $customerId);
$stmt->execute();
$products = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Ulubione - SPEC.</title>
    <link href="../main/styles.css?v=product-images-2" rel="stylesheet">
    <link rel="icon" href="../img/Logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <nav>
        <div class="responsive-navbar">
            <div class="Logo-etc">
                <a href="../main/index.php"><img src="../img/Logo.png" alt="Logo SPEC"></a>
                <span>Wybór wart swojej ceny.</span>
            </div>
            <div class="nav-icons">
                <a href="../cart/index.php"><img src="../img/koszyk.png" alt="Koszyk"></a>
            </div>
        </div>
    </nav>

    <section class="products-section">
        <div class="section-header">
            <h1 class="section-title">Ulubione</h1>
            <p class="section-subtitle">Produkty zapisane na później</p>
        </div>

        <div class="products-grid">
            <?php if ($products->num_rows === 0): ?>
                <div class="no-results" style="grid-column:1/-1; text-align:center; padding:80px 20px; font-size:1.2rem; color:#555;">
                    Nie masz jeszcze ulubionych produktów.
                </div>
            <?php endif; ?>

            <?php while ($product = $products->fetch_assoc()): ?>
                <?php
                    $productId = (int)$product['product_id'];
                    $quantityInCart = getProductQuantity($productId, $db);
                    $mainImage = getProductMainImage($product['image_url']);
                ?>
                <div class="product-card">
                    <button class="favorite-btn active" data-product-id="<?= $productId ?>" title="Ulubione">♥</button>
                    <a class="product-link" href="../product/index.php?id=<?= $productId ?>">
                        <div class="product-image-frame">
                            <img src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-image">
                        </div>
                        <h3 class="product-name"><?= htmlspecialchars($product['product_name']) ?></h3>
                    </a>
                    <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
                    <div class="product-status <?= $product['stock_quantity'] > 0 ? 'status-available' : 'status-unavailable' ?>">
                        <span class="status-dot"></span>
                        <?= $product['stock_quantity'] > 0 ? 'Na stanie' : 'Brak w magazynie' ?>
                    </div>
                    <div class="product-price"><?= number_format((float)$product['price'], 2, ',', ' ') ?> zł / szt.</div>

                    <?php if ($quantityInCart > 0): ?>
                        <div class="quantity-selector" data-product-id="<?= $productId ?>">
                            <button class="decrease">-</button>
                            <span class="quantity"><?= $quantityInCart ?></span>
                            <button class="increase">+</button>
                        </div>
                    <?php elseif ((int)$product['stock_quantity'] <= 0): ?>
                        <button class="add-to-cart-btn" data-product-id="<?= $productId ?>" disabled>Brak towaru</button>
                    <?php else: ?>
                        <button class="add-to-cart-btn" data-product-id="<?= $productId ?>">Dodaj</button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <script src="../main/product-actions.js"></script>
</body>
</html>
