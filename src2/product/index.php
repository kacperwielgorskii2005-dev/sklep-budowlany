<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../api/database.php';
require_once __DIR__ . '/../../api/main/CartFunctions.php';
require_once __DIR__ . '/../../api/favorites/FavoriteFunctions.php';
require_once __DIR__ . '/../../api/products/ProductImages.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = 'none';
}

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $db->prepare("
    SELECT p.*, c.category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.product_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    http_response_code(404);
}

$galleryImages = [];
if ($product) {
    $galleryImages = getProductImages($product['image_url']);

    while (count($galleryImages) < 4) {
        $galleryImages[] = $galleryImages[0];
    }
}

$quantityInCart = $product ? getProductQuantity($productId, $db) : 0;
$favoriteIds = getFavoriteProductIds($db, getLoggedCustomerId($db));
$isFavorite = in_array($productId, $favoriteIds, true);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= $product ? htmlspecialchars($product['product_name']) : 'Produkt nie znaleziony' ?> - SPEC.</title>
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
                <?php if ($_SESSION['user'] !== 'none'): ?>
                    <a href="../favorites/index.php"><img src="../img/serduszko.png" alt="Ulubione"></a>
                <?php endif; ?>
                <a href="../cart/index.php"><img src="../img/koszyk.png" alt="Koszyk"></a>
            </div>
        </div>
    </nav>

    <section class="products-section">
        <?php if (!$product): ?>
            <div class="section-header">
                <h1 class="section-title">Produkt nie istnieje</h1>
                <p class="section-subtitle">Wróć do sklepu i wybierz produkt z listy.</p>
            </div>
        <?php else: ?>
            <div class="product-details-page">
                <div class="product-gallery" data-gallery-images='<?= htmlspecialchars(json_encode($galleryImages), ENT_QUOTES, 'UTF-8') ?>'>
                    <div class="gallery-main">
                        <button class="gallery-arrow gallery-prev" type="button" aria-label="Poprzednie zdjecie">&#8249;</button>
                        <img id="galleryMainImage" src="<?= htmlspecialchars($galleryImages[0]) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                        <button class="gallery-arrow gallery-next" type="button" aria-label="Nastepne zdjecie">&#8250;</button>
                    </div>
                    <div class="gallery-thumbnails">
                        <?php foreach ($galleryImages as $index => $imageUrl): ?>
                            <button class="gallery-thumb<?= $index === 0 ? ' active' : '' ?>" type="button" data-gallery-index="<?= $index ?>">
                                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="product-details-content">
                    <p class="section-subtitle"><?= htmlspecialchars($product['category_name'] ?? 'Produkt') ?></p>
                    <h1 class="section-title"><?= htmlspecialchars($product['product_name']) ?></h1>
                    <p class="section-description"><?= htmlspecialchars($product['description']) ?></p>
                    <div class="product-status <?= $product['stock_quantity'] > 0 ? 'status-available' : 'status-unavailable' ?>">
                        <span class="status-dot"></span>
                        <?= $product['stock_quantity'] > 0 ? 'Na stanie' : 'Brak w magazynie' ?>
                    </div>
                    <div class="product-price"><?= number_format((float)$product['price'], 2, ',', ' ') ?> zł / szt.</div>

                    <button class="favorite-btn detail-favorite<?= $isFavorite ? ' active' : '' ?>" data-product-id="<?= $productId ?>" type="button">
                        <span class="favorite-icon">&#9825;</span>
                        <span>Ulubione</span>
                    </button>

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
            </div>
        <?php endif; ?>
    </section>

    <script src="../main/product-actions.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const gallery = document.querySelector(".product-gallery");
            if (!gallery) return;

            const images = JSON.parse(gallery.dataset.galleryImages || "[]");
            const mainImage = document.getElementById("galleryMainImage");
            const thumbs = document.querySelectorAll(".gallery-thumb");
            let currentIndex = 0;

            function showImage(index) {
                if (!images.length) return;

                currentIndex = (index + images.length) % images.length;
                mainImage.src = images[currentIndex];
                thumbs.forEach((thumb, thumbIndex) => {
                    thumb.classList.toggle("active", thumbIndex === currentIndex);
                });
            }

            document.querySelector(".gallery-prev").addEventListener("click", () => showImage(currentIndex - 1));
            document.querySelector(".gallery-next").addEventListener("click", () => showImage(currentIndex + 1));

            thumbs.forEach(thumb => {
                thumb.addEventListener("click", () => showImage(parseInt(thumb.dataset.galleryIndex, 10)));
            });
        });
    </script>
</body>
</html>
