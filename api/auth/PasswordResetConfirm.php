<?php
require '../../api/database.php';

$conn = $db;
$message = "";
$passwordValue = "";
$confirmValue = "";
$showForm = false;

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $message = "❌ Link do resetu hasła jest nieprawidłowy!";
} else {
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows === 0) {
        $message = "❌ Token nie istnieje lub wygasł!";
    } else {
        $showForm = true;
        $resetData = $result->fetch_assoc();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $showForm) {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    $passwordValue = htmlspecialchars($password);
    $confirmValue = htmlspecialchars($confirm);

    if (strlen($password) < 6) {
        $message = "❌ Hasło jest za krótkie!";
    } elseif (strlen($password) > 20) {
        $message = "❌ Hasło jest za długie!";
    } elseif ($password !== $confirm) {
        $message = "❌ Hasła nie są identyczne!";
    } else {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE customers SET password_hash = ? WHERE customer_id = ?");
        $stmt->bind_param("si", $password_hashed, $resetData['customer_id']);
        if ($stmt->execute()) {
            $stmt->close();
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->close();

            $message = "✅ Hasło zostało zmienione! Możesz teraz się zalogować.";
            $showForm = false;
        } else {
            $message = "❌ Wystąpił błąd przy zmianie hasła!";
        }
    }
}
?>
