<?php
require ('../../api/database.php');
require ('RegisterConfirm.php');

$conn = $db;
$message = "";
$redirectAfter = 30;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $login = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Nieprawidłowy format adresu e-mail!";
        $email = "";

    }elseif (strlen($login) < 5 || strlen($login) > 30) {
        $message = "❌ Login musi mieć od 5 do 30 znaków!";
        $login = "";

    }elseif (!preg_match("/^[A-Za-z0-9_-]+$/", $login)) {
        $message = "❌ Login zawiera niedozwolone znaki!";
        $login = "";

    }elseif (strlen($password) < 6){
        $message = "❌ Hasło jest zbyt krótkie!";
        $password = "";

    }elseif (strlen($password) > 20){
        $message = "❌ Hasło jest zbyt długie!";
        $password = "";

    }else {
        $stmt = $conn->prepare("SELECT * FROM customers WHERE login = ? OR email = ?");
        $stmt->bind_param("ss", $login, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $message = "⚠️ Login lub adres e-mail jest już zajęty!";
        } else {
            $token = bin2hex(random_bytes(32));
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO pending_users (login, email, password_hash, token)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("ssss", $login, $email, $password_hashed, $token);

            if ($stmt->execute()) {
                $stmt->close();

                $verifyLink = "http://localhost/sklep_budowlany/src/verify/verify.php?token=$token";
                $mailBody = "
                    <h2>Witaj, {$login}!</h2>
                    <p>Kliknij przycisk poniżej, aby potwierdzić rejestrację:</p>
                    <a href='$verifyLink' style='
                        display:inline-block;
                        padding:10px 20px;
                        background:#007bff;
                        color:white;
                        text-decoration:none;
                        border-radius:5px;
                    '>Potwierdź rejestrację</a>
                    <p>Jeśli nie rejestrowałeś się w naszym sklepie, zignoruj tę wiadomość.</p>
                ";

                $emailSent = sendAccountCreationEmail($email, $login, $mailBody);

                if ($emailSent) {
                    $message = "✅ Sprawdź swoją skrzynkę e-mail i kliknij przycisk, aby potwierdzić rejestrację.";
                    echo "<script>
                        getElementById('register-submit').disabled = true;
                        setTimeout(function() {
                            window.location.href = '../../src/panel-login/index.php';
                        }, " . ($redirectAfter * 1000) . ");
                    </script>";
                } else {
                    $message = "❌ Nie udało się wysłać e-maila potwierdzającego. Spróbuj ponownie!";
                }

            } else {
                $message = "❌ Wystąpił błąd podczas rejestracji!";
                $stmt->close();
            }
        }
    }
}
?>
