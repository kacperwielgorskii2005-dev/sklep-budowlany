<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require '../../api/database.php';

$conn = $db;
$message = "";
$loginValue = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $loginValue = htmlspecialchars($login);

    if (empty($login) || empty($password)) {
        $message = "❌ Wprowadź login i hasło!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM administrators WHERE username = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $adminResult = $stmt->get_result();
        $stmt->close();

        if ($adminResult->num_rows > 0) {
            $admin = $adminResult->fetch_assoc();
            if (password_verify($password, $admin['password_hash'])) {
                $_SESSION['isadmin'] = true;
                $_SESSION['user'] = $admin['username'];
                header("Location: ../../src/panel-administrator/index.php");
                exit;
            } else {
                $message = "❌ Nieprawidłowe hasło!";
            }
        } else {
            $stmt = $conn->prepare("SELECT * FROM customers WHERE login = ?");
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $custResult = $stmt->get_result();
            $stmt->close();

            if ($custResult->num_rows > 0) {
                $customer = $custResult->fetch_assoc();
                if (password_verify($password, $customer['password_hash'])) {
                    $_SESSION['user'] = $customer['login'];
                    header("Location: ../../src/main/index.php");
                    exit;
                } else {
                    $message = "❌ Nieprawidłowe hasło!";
                }
            } else {
                $message = "❌ Nie znaleziono użytkownika!";
            }
        }
    }
}
?>