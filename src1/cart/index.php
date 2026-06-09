<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once ('../../api/database.php');
require ('../../api/cart/GetCartContent.php');

if(!isset($_SESSION['user'])){
    $_SESSION['user'] = "none";
}

$cartItems = getCartItems($db);

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$delivery = 35.00;
$discount = 50.00;

if ($subtotal >= 500) {
    $delivery = 0;
}else {
    $delivery = 35.00;
}

$total = $subtotal + $delivery - $discount;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koszyk - SPEC.</title>
    <link rel="icon" href="../img/Logo.png">
    <link rel="stylesheet" href="styles.css?v=product-images-2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href="styles.css?v=product-images-2" rel="stylesheet">
</head>
<body>
    <nav>
        <div class="responsive-navbar">
            <div class="Logo-etc">
                <img src="../img/Logo.png" alt="Logo SPEC">
                <span>Wybór wart swojej ceny.</span>
            </div>
            <div class="nav-icons">
                <a href="../main/index.php" class="back-to-shop">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Powrót do sklepu
                </a>
            </div>
        </div>
    </nav>

    <div class="cart-container">
        <div class="cart-header">
            <h1>Twój Koszyk</h1>
            <div class="cart-summary-top">
                <span class="items-count"><?= count($cartItems) ?> <?= count($cartItems) === 1 ? 'produkt' : (count($cartItems) < 5 ? 'produkty' : 'produktów') ?></span>
                <?php if (count($cartItems) > 0): ?>
                    <button class="clear-cart-btn" onclick="clearCart()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                        </svg>
                        Wyczyść koszyk
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if (count($cartItems) > 0): ?>
            <div class="cart-content">
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item" data-product-id="<?= $item['id'] ?>">
                            <div class="cart-item-image">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            </div>
                            <div class="cart-item-details">
                                <h3 class="cart-item-name"><?= htmlspecialchars($item['name']) ?></h3>
                                <p class="cart-item-description"><?= htmlspecialchars($item['description']) ?></p>
                                <div class="cart-item-status <?= $item['available'] ? 'available' : 'unavailable' ?>">
                                    <span class="status-dot <?= $item['available'] ? 'available' : 'unavailable' ?>"></span>
                                    <span><?= $item['available'] ? 'Dostępny' : 'Brak w magazynie' ?></span>
                                </div>
                            </div>
                            <div class="cart-item-quantity">
                                <button class="qty-btn" onclick="decreaseQty(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 13H5v-2h14v2z"/>
                                    </svg>
                                </button>
                                <input type="number" value="<?= $item['quantity'] ?>" min="1" max="99" class="qty-input" data-price="<?= $item['price'] ?>" readonly>
                                <button class="qty-btn" onclick="increaseQty(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="cart-item-price">
                                <span class="price-label">Cena:</span>
                                <span class="price-value"><?= number_format($item['price'], 2, ',', ' ') ?> zł</span>
                                <span class="price-total"><?= number_format($item['price'] * $item['quantity'], 2, ',', ' ') ?> zł</span>
                            </div>
                            <button class="cart-item-remove" onclick="removeItem(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-sidebar">
                    <div class="cart-summary">
                        <h3 class="summary-title">Podsumowanie</h3>
                        
                        <div class="summary-row">
                            <span>Wartość produktów:</span>
                            <span id="subtotal-value"><?= number_format($subtotal, 2, ',', ' ') ?> zł</span>
                        </div>
                        <div class="summary-row">
                            <span>Dostawa:</span>
                            <span id="delivery-cost"><?= number_format($delivery, 2, ',', ' ') ?> zł</span>
                        </div>
                        <div class="summary-row discount">
                            <span>Rabat:</span>
                            <span>-<?= number_format($discount, 2, ',', ' ') ?> zł</span>
                        </div>
                        
                        <div class="summary-divider"></div>
                        
                        <div class="summary-total">
                            <span>Do zapłaty:</span>
                            <span id="total-value"><?= number_format($total, 2, ',', ' ') ?> zł</span>
                        </div>

                        <button class="checkout-btn" onclick="goToCheckout()">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                            </svg>
                            Przejdź do płatności
                        </button>

                        <a href="../main/index.php" class="continue-shopping">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                            </svg>
                            Kontynuuj zakupy
                        </a>
                    </div>

                    <div class="cart-promo">
                        <h4>Masz kod rabatowy?</h4>
                        <div class="promo-input-group">
                            <input type="text" placeholder="Wpisz kod" class="promo-input">
                            <button class="promo-btn">Zastosuj</button>
                        </div>
                    </div>

                    <div class="cart-info">
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#4CAF50">
                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                            </svg>
                            <span>Darmowa dostawa od 500 zł</span>
                        </div>
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#2196F3">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                            </svg>
                            <span>Bezpieczne płatności</span>
                        </div>
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FF9800">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            <span>30 dni na zwrot</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ccc">
                    <path d="M15.55 13c.75 0 1.41-.41 1.75-1.03l3.58-6.49A.996.996 0 0 0 20.01 4H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2h7.45zM6.16 6h12.15l-2.76 5H8.53L6.16 6zM7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
                <h3>Twój koszyk jest pusty</h3>
                <p>Dodaj produkty do koszyka, aby kontynuować zakupy</p>
                <a href="../main/index.php" class="btn-back-shop">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z"/>
                    </svg>
                    Wróć do sklepu
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="cart.js"></script>
</body>
</html>
