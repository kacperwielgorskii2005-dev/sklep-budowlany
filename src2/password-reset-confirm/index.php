<?php
    require '../../api/auth/PasswordResetConfirm.php';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPEC. - Reset hasła</title>
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
        <?php if(!empty($message)) { echo "<p class='error-msg' style='margin-top:20px; color:#000; font-weight:bold; text-align:center; margin-bottom: 60px; background-color: rgba(255, 0, 0, 0.33); border-radius: 4px; padding: 10px;'>$message</p>"; } ?>
        <div class="description">
            <h1 class="panel-title">Ustaw nowe hasło</h1>
            <p class="panel-subtitle">Aby ustawić nowe hasło, wpisz je w polach poniżej</p>
        </div>
        <form id="loginForm" method="post">
            <div class="form-group">
                <label class="form-label">Nowe hasło:</label>
                <input type="password" name="password" class="form-input" placeholder="Podaj nowe hasło.." value="<?php if(isset($_POST["password"])){echo htmlspecialchars($password);} ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Powtórz nowe hasło:</label>
                <input type="password" name="confirm" class="form-input" placeholder="Powtórz nowe hasło.." value="<?php if(isset($_POST["confirm"])){echo htmlspecialchars($confirm);} ?>" required>
            </div>

            <button type="submit" class="login-button">Zatwierdź zmianę hasła</button>
        </form>

        <div class="footer-links">
            <a href="../panel-login/index.php" class="footer-link reset-link">
                <span class="reset-text">Przypomniałeś sobie hasło?</span><br>
                <strong>Zaloguj się!</strong>
            </a>
        </div>
    </div>
</body>
</html>