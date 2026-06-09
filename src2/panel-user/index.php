<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once ('../../api/database.php');

if(!isset($_SESSION['user'])){
    $_SESSION['user'] = "none";
}

$openCart = false;

if (isset($_GET['from']) && $_GET['from'] === 'cart') {
    $openCart = true;
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPEC. - Panel użytkownika</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../panel-administrator/styles.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link href="../img/Logo.png" rel="icon">
</head>
<body>
    <button class="hamburger" id="hamburgerBtn">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <div class="container">
            <aside class="sidebar">
                <div class="logo">
                    <img src="../img/WBlack-Logo.png"alt="Logo">
                </div>
                <div class="menu-section-title">POWRÓT</div>
                <div class="main-page-link">
                    <a href="../main/index.php" class="menu-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                        </svg>
                        Strona główna
                    </a>
                </div>
                
                <?php
                    if($_SESSION['user'] != "none"){
                        echo '<div class="menu-section-title">DZIAŁANIA</div>
                            <a href="#" class="menu-item active" onclick="showSection(\'dashboard\')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l-5.5 9h11z"/>
                                    <circle cx="17.5" cy="17.5" r="4.5"/>
                                    <path d="M3 13.5h8v8H3z"/>
                                </svg>
                                Panel zarządzania
                            </a>

                            <a href="#" class="menu-item" onclick="showSection(\'client-zone\')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                </svg>
                                Strefa Klienta
                            </a>';
                    }
                ?>
                <!--
                <a href="#" class="menu-item" id="cart-select" onclick="showSection('cart')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.55 13c.75 0 1.41-.41 1.75-1.03l3.58-6.49A.996.996 0 0 0 20.01 4H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2h7.45zM6.16 6h12.15l-2.76 5H8.53L6.16 6zM7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                    Koszyk
                </a>
                -->
                <!-- <a style="text-decoration: none" href="../panel-login/index.php">*/ -->
                <?php
                    if($_SESSION['user'] != "none"){
                        echo '<form method="POST" action="../../src/main/index.php" class="dropdown-item" style="text-decoration: none">
                        <button class="logout-btn" type="submit" name="logout" id="logout-button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="white">
                                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                            </svg>
                            Wyloguj się
                        </button>
                    </form>
                    
                    </a>';
                    }
                ?>
            </aside>
            <main class="main-content">
                <div class="top-bar">
                    <h3>Jesteś zalogowany/a jako klient</h3>
                    <div class="user-status">
                        <?php
                            if($_SESSION['user'] != "none"){
                                echo $_SESSION['user'];
                            }else{
                                echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                    </svg>';
                                echo 'Konto nieaktywne';
                            }
                        ?>
                    </div>
                </div>

                <div id="dashboard" class="content-section active">
                    <div class="welcome-card">
                        <div class="welcome-header">
                            <div class="avatar">
                                <?php
                                    if($_SESSION['user'] != "none"){
                                        echo strtoupper(substr($_SESSION['user'], 0, 1));
                                    }
                                ?>
                            </div>
                            <div class="welcome-text">
                                <h1>
                                    <?php
                                        if($_SESSION['user'] != "none"){
                                            echo $_SESSION['user'];
                                        }
                                    ?>
                                </h1>
                                <p>Dane użytkownika</p>
                            </div>
                            <button class="edit-btn" onclick="toggleEdit()">Edytuj</button>
                        </div>

                        <div class="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#2196F3">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                            <span>Twoje konto wymaga aktywacji <a href="#" id="activate-admin-account">kliknij tutaj</a>, aby wysłać prośbę do administratora.</span>
                        </div>

                        <div class="info-grid" id="user-info-grid">
                            <div class="info-item">
                                <label>Login:</label>
                                <input type="text" id="u_login" disabled>
                            </div>
                            <div class="info-item">
                                <label>Hasło:</label>
                                <input type="password" id="u_password" disabled placeholder="Nie pokazujemy hasła">
                            </div>
                            <div class="info-item">
                                <label>E-mail:</label>
                                <input type="email" id="u_email" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3 class="section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#FFB400">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/>
                            </svg>
                            Informacje rozliczeniowe
                        </h3>
                        
                        <div class="info-grid">
                            <div class="info-grid" id="billing-grid">
                                <div class="info-item">
                                    <label>Imię:</label>
                                    <input type="text" id="u_first_name" name="first_name" disabled>
                                </div>
                                <div class="info-item">
                                    <label>Nazwisko:</label>
                                    <input type="text" id="u_last_name" name="last_name"disabled>
                                </div>
                                <div class="info-item">
                                    <label>Telefon:</label>
                                    <input type="tel" id="u_phone" name="phone" disabled>
                                </div>
                                <div class="info-item">
                                    <label>Ulica i numer:</label>
                                    <input type="text" id="u_address" name="address" disabled>
                                </div>
                                <div class="info-item">
                                    <label>Kod pocztowy:</label>
                                    <input type="text" id="u_postal" name="postal" disabled>
                                </div>
                                <div class="info-item">
                                    <label>Miasto:</label>
                                    <input type="text" id="u_city" name="city" disabled>
                                </div>
                                <div class="info-item">
                                    <label>Kraj:</label>
                                    <input type="text" id="u_country" name="country" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="contact-section">
                        <h3 class="section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#FFB400">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/>
                            </svg>
                            Formularz kontaktowy
                        </h3>
                        
                        <form method="post" class="contact-form">
                            <div class="form-layout">
                                <div class="form-left">
                                    <div class="form-section-title">Twoje dane</div>
                                    <div class="form-item">
                                        <label>Imię:</label>
                                        <input type="text" name="c_first_name" id="c_first_name" required>
                                    </div>
                                    <div class="form-item">
                                        <label>Nazwisko:</label>
                                        <input type="text" name="c_last_name" id="c_last_name" required>
                                    </div>
                                    <div class="form-item">
                                        <label>E-mail kontaktowy:</label>
                                        <input type="email" name="c_email" id="c_email" required>
                                    </div>
                                    <div class="form-item">
                                        <label>Telefon:</label>
                                        <input type="tel" name="c_phone" id="c_phone">
                                    </div>
                                </div>

                                <div class="form-right">
                                    <div class="form-section-title">Treść wiadomości</div>
                                    <div class="form-item">
                                        <label>Temat wiadomości:</label>
                                        <input type="text" id="c_subject" placeholder="Wpisz temat wiadomości" required>
                                    </div>
                                    <div class="form-item">
                                        <label>Twoja wiadomość:</label>
                                        <textarea id="c_message" placeholder="Opisz swoją sprawę..." required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-footer">
                                <div class="checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" required>
                                        <span>Zapoznałem/am się z treścią <a href="#">regulaminu</a> <span style="color: #FFB400;">*</span></span>
                                    </label>
                                    <label class="checkbox-label">
                                        <input type="checkbox" required>
                                        <span>Wyrażam zgodę na przetwarzanie moich danych osobowych przez <strong>SPEC. MARKET SP. Z O. O.</strong> zgodnie z RODO <span style="color: #FFB400;">*</span></span>
                                    </label>
                                    <p class="required"><span style="color: #FFB400;">*</span> oznacza pola wymagane</p>
                                </div>
                                <button type="submit" class="send-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="white">
                                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                                    </svg>
                                    Wyślij wiadomość
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="client-zone" class="content-section">
                    <h2>Strefa Klienta</h2>
                    <p>Twoje zamówienia:</p>
                    <div class="info-section">
                        <h3 class="section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#FFB400">
                                <path d="M15.55 13c.75 0 1.41-.41 1.75-1.03l3.58-6.49A.996.996 0 0 0 20.01 4H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2h7.45zM6.16 6h12.15l-2.76 5H8.53L6.16 6zM7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                            Status twoich zamówień
                        </h3>
                        
                        <div class="orders-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID Zamówienia</th>
                                        <th>Data</th>
                                        <th>Nazwa produktu</th>
                                        <th>Ilość</th>
                                        <th>Wartość</th>
                                        <th>Status</th>
                                        <th>Szczegóły</th>
                                    </tr>
                                </thead>
                                <tbody id="orders-tbody">
                                    <!-- Dynamic rows will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="cart" class="content-section">
                    <h2>Twój Koszyk</h2>
                    <p>Produkty, które dodałeś do koszyka:</p>
                </div>
            </main>
        </div>

    <div class="order-popup-overlay" id="orderPopup">
        <div class="order-popup">
            <div class="popup-header">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/>
                    </svg>
                    Szczegóły zamówienia <span id="popupOrderId">#12345</span>
                </h2>
                <button class="popup-close" onclick="closeOrderPopup()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>

            <div class="popup-content">
                <div class="order-info-grid">
                    <div class="info-box">
                        <div class="info-label">Klient</div>
                        <div class="info-value" id="popupCustomer">Jan Kowalski</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Data zamówienia</div>
                        <div class="info-value" id="popupDate">21.10.2025 14:32</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Status</div>
                        <div class="info-value" id="popupStatus">
                            <span class="status-badge status-new">Nowe</span>
                        </div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Email</div>
                        <div class="info-value" id="popupEmail">jan.kowalski@email.com</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Telefon</div>
                        <div class="info-value" id="popupPhone">+48 500 600 700</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Metoda płatności</div>
                        <div class="info-value" id="popupPayment">PayU</div>
                    </div>
                </div>

                <h3 class="section-title-popup">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z"/>
                    </svg>
                    Zamówione produkty
                </h3>

                <div class="products-list" id="popupProducts">
                    <div class="product-row">
                        <div class="product-info">
                            <div class="product-name-popup">Wiertarka udarowa Bosch PSB 500 RE</div>
                            <div class="product-details">2 szt. × 450,00 zł</div>
                        </div>
                        <div class="product-price">900,00 zł</div>
                    </div>
                </div>

                <h3 class="section-title-popup">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    Adres dostawy
                </h3>

                <div class="delivery-info">
                    <div class="delivery-row">
                        <span class="delivery-label">Ulica:</span>
                        <span class="delivery-value" id="popupStreet">ul. Konarskiego 11</span>
                    </div>
                    <div class="delivery-row">
                        <span class="delivery-label">Miasto:</span>
                        <span class="delivery-value" id="popupCity">08-110 Siedlce</span>
                    </div>
                    <div class="delivery-row">
                        <span class="delivery-label">Dodatkowe info:</span>
                        <span class="delivery-value" id="popupNotes">—</span>
                    </div>
                </div>

                <h3 class="section-title-popup">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                    </svg>
                    Podsumowanie
                </h3>

                <div class="total-section-popup">
                    <div class="total-row-popup">
                        <span>Wartość produktów:</span>
                        <span id="popupSubtotal">1025,00 zł</span>
                    </div>
                    <div class="total-row-popup">
                        <span>Dostawa:</span>
                        <span id="popupShipping">35,00 zł</span>
                    </div>
                    <div class="total-row-popup final">
                        <span>Do zapłaty:</span>
                        <span id="popupTotal">1060,00 zł</span>
                    </div>
                </div>

                <div class="popup-actions">
                    <button class="popup-action-btn btn-primary" onclick="printOrder()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                            <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
                        </svg>
                        Drukuj zamówienie
                    </button>
                    <button class="popup-action-btn btn-danger-popup" id="btnCancelOrder" onclick="cancelOrder()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                        Anuluj zamówienie
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="show-order.js"></script>
    <script src="edit-data.js"></script>
    <script src="mobile-menu.js"></script>
    <script src="section-swap.js"></script>
    <script src="send-message.js"></script>

    <?php if ($openCart): ?>
        <script>
        document.addEventListener("DOMContentLoaded", function() {

            showSection('cart');

            document.querySelectorAll('.menu-item.active').forEach(item => {
                item.classList.remove("active");
            });

            const cartBtn = document.getElementById('cart-select');
            if (cartBtn) cartBtn.classList.add("active");
        });
        </script>
    <?php endif; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const activateAdminLink = document.getElementById('activate-admin-account');
            if (!activateAdminLink) return;

            activateAdminLink.addEventListener('click', function(event) {
                event.preventDefault();

                fetch('../../api/user/ActivateAdminAccount.php', { method: 'POST' })
                    .then(res => res.json())
                    .then(res => {
                        alert(res.message || (res.success ? 'Prosba wyslana.' : 'Nie udalo sie wyslac prosby.'));
                    })
                    .catch(() => alert('Nie udalo sie wyslac prosby.'));
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const tbody = document.getElementById('orders-tbody');

            fetch('../../api/user/LoadOrders.php')
                .then(res => res.json())
                .then(res => {
                    if (!res.success) {
                        tbody.innerHTML = `<tr><td colspan="7">${res.message}</td></tr>`;
                        return;
                    }

                    const orders = res.data;

                    if (orders.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="7">Brak zamówień</td></tr>`;
                        return;
                    }

                    tbody.innerHTML = orders.map(order => `
                        <tr>
                            <td>#${order.order_id}</td>
                            <td>${order.order_date}</td>
                            <td>${order.product_name}</td>
                            <td>${order.items_count}</td>
                            <td>${order.total_amount}</td>
                            <td>${order.status}</td>
                            <td><a href="#" class="view-details" onclick="event.preventDefault(); openOrderPopup('${order.order_id}')">Zobacz</a></td>\n
                        </tr>
                    `).join('');
                });
        });
    </script>

    <script>

        function getStatusBadge(status) {
            const statusMap = {
                'Pending': { class: 'status-new', text: 'Oczekujące' },
                'Processing': { class: 'status-processing', text: 'W realizacji' },
                'Completed': { class: 'status-completed', text: 'Zrealizowane' },
                'Cancelled': { class: 'status-cancelled', text: 'Anulowane' }
            };
            
            const statusInfo = statusMap[status] || { class: 'status-new', text: status };
            return `<span class="status-badge ${statusInfo.class}">${statusInfo.text}</span>`;
        }

        function closeOrderPopup() {
            document.getElementById('orderPopup').classList.remove('active');
            document.body.style.overflow = '';
        }

        document.getElementById('orderPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderPopup();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('orderPopup').classList.contains('active')) {
                closeOrderPopup();
            }
        });

    </script>
</body>
</html>
