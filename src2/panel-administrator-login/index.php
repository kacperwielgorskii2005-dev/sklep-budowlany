<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPEC. - Panel administratora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link href="../img/Logo.png" rel="icon">
</head>
<body>
    <a href="../main/index.php" class="back-button">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        POWRÓT
    </a>

    <div class="login-container">
        <div class="logo-section">
            <div class="logo">
                <img src="../img/WBlack-Logo.png">
            </div>
        </div>
        <div class="description">
            <h1 class="panel-title">Dzień dobry!</h1>
            <p class="panel-subtitle">Zaloguj się do swojego konta administratora:</p>
        </div>
        <form id="loginForm">
            <div class="form-group">
                <label class="form-label">Login:</label>
                <input type="text" class="form-input" placeholder="Podaj login" required>
            </div>

            <div class="form-group">
                <label class="form-label">Hasło:</label>
                <input type="password" class="form-input" placeholder="Podaj hasło" required>
            </div>

            <button type="submit" class="login-button">Zaloguj się</button>
        </form>

        <div class="footer-links">
            <a href="#" class="footer-link register-link">
                <span class="register-text">Zapomniałeś hasła?</span><br>
                <strong>Logowanie tokenem</strong>
            </a>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Funkcja logowania - do podłączenia z backendem');
        });
    </script>
</body>
</html>