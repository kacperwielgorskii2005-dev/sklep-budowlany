<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../api/database.php';
require 'RegisterConfirm.php';

$conn = $db;
$message = "";
$emailValue = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $emailValue = htmlspecialchars($email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Nieprawidłowy format adresu e-mail!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 0) {
            $message = "❌ Nie znaleziono użytkownika z tym adresem e-mail!";
        } else {
            $user = $result->fetch_assoc();

            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime('+30 minutes'));

            $stmt = $conn->prepare("INSERT INTO password_resets (customer_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user['customer_id'], $token, $expiry);
            $stmt->execute();
            $stmt->close();

            $resetLink = "http://localhost/sklep_budowlany/src/password-reset-confirm/index.php?token=$token";

            $mailBody = "
                <h2>Witaj, {$user['login']}!</h2>
                <p>Kliknij przycisk poniżej, aby zresetować swoje hasło:</p>
                <a href='$resetLink' style='
                    display:inline-block;
                    padding:10px 20px;
                    background:#007bff;
                    color:white;
                    text-decoration:none;
                    border-radius:5px;
                '>Zresetuj hasło</a>
                <p>Jeśli nie prosiłeś o reset hasła, zignoruj tę wiadomość.</p>
            ";

            $emailSent = sendAccountCreationEmail($email, $user['login'], $mailBody);

            if ($emailSent) {
                $message = "✅ Sprawdź swoją skrzynkę e-mail i kliknij przycisk, aby zresetować hasło.";
            } else {
                $message = "❌ Nie udało się wysłać wiadomości e-mail. Spróbuj ponownie!";
            }
        }
    }
}
?>
