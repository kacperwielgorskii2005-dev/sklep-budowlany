<?php
    require ('../../api/auth/Register.php');
?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPEC. - Panel logowania!</title>
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
            <p class="panel-subtitle">Zarejestruj się w naszym sklepie:</p>
        </div>

        <?php if (!empty($message)) : ?>
            <div style="margin-top:20px; color:#000; font-weight:bold; text-align:center; margin-bottom: 20px; background-color: rgba(255, 0, 0, 0.33); border-radius: 4px; padding: 10px;">
                <?= $message ?>
            </div>
            <?php $message = '' ?>
        <?php endif; ?>
        <form id="loginForm" method="post">
            <div class="form-group">
                <label class="form-label">Adres e-mail:</label>
                <input type="text" name="email" class="form-input" placeholder="Podaj e-mail" value="<?php if(isset($_POST["email"])){echo htmlspecialchars($email);} ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Login:</label>
                <input type="text" name="login" class="form-input" placeholder="Podaj login" value="<?php if(isset($_POST["login"])){echo htmlspecialchars($login);} ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Hasło (6 do 20 znaków):</label>
                <input type="password" name="password" class="form-input" placeholder="Podaj hasło" value="<?php if(isset($_POST["password"])){echo htmlspecialchars($password);} ?>" required>
            </div>

            <button type="submit" class="login-button" id="register-submit">Zarejestruj się</button>
        </form>

        <div class="footer-links">
            <a href="../panel-login/index.php" class="footer-link register-link">
                <span class="register-text">Masz już konto?</span><br>
                <strong>Zaloguj się!</strong>
            </a>
        </div>
    </div>
</body>
</html>