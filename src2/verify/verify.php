<?php
require ('../../api/database.php');

$conn = $db;
$token = $_GET['token'] ?? '';

$success = false;
$message = '';

if ($token) {
    $stmt = $conn->prepare("SELECT * FROM pending_users WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $stmt2 = $conn->prepare("
            INSERT INTO customers (login, email, password_hash)
            VALUES (?, ?, ?)
        ");
        $stmt2->bind_param("sss", $user['login'], $user['email'], $user['password_hash']);
        $stmt2->execute();
        $stmt2->close();

        $stmt3 = $conn->prepare("DELETE FROM pending_users WHERE id = ?");
        $stmt3->bind_param("i", $user['id']);
        $stmt3->execute();
        $stmt3->close();

        $success = true;
        $message = "Konto zostało potwierdzone! Możesz się teraz zalogować.";
    } else {
        $success = false;
        $message = "Nieprawidłowy lub wygasły token weryfikacyjny.";
    }
} else {
    $success = false;
    $message = "Brak tokenu weryfikacyjnego!";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $success ? 'Weryfikacja udana' : 'Błąd weryfikacji' ?> - SPEC.</title>
    <link rel="icon" href="../img/Logo.png">
    <link rel="stylesheet" href="verify.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href="verify.css" rel="stylesheet">
</head>
<body>
    <div class="verify-container">
        <img class="logo" src="../img/WBlack-logo.png">

        <?php if ($success): ?>
            <div class="icon-container success">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                </svg>
            </div>
            <h1 class="message-title">Weryfikacja udana! ✅</h1>
            <p class="message-text"><?= htmlspecialchars($message) ?></p>
            <div class="countdown">
                Przekierowanie za <span id="countdown">5</span> sekund...
            </div>
            <div class="links">
                <a href="../panel-login/index.php" class="btn-primary">Zaloguj się teraz</a>
                <a href="../main/index.php" class="btn-secondary">Strona główna</a>
            </div>
        <?php else: ?>
            <div class="icon-container error">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </div>
            <h1 class="message-title">Błąd weryfikacji ❌</h1>
            <p class="message-text"><?= htmlspecialchars($message) ?></p>
            <div class="countdown">
                Przekierowanie za <span id="countdown">5</span> sekund...
            </div>
            <div class="links">
                <a href="../panel-register/index.php" class="btn-primary">Zarejestruj się ponownie</a>
                <a href="../main/index.php" class="btn-secondary">Strona główna</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        let seconds = 5;
        const countdownElement = document.getElementById('countdown');
        const redirectUrl = <?= $success ? '"../panel-login/index.php"' : '"../main/index.php"' ?>;

        const interval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = redirectUrl;
            }
        }, 1000);
    </script>
</body>
</html>