<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['isadmin']) || $_SESSION['isadmin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Brak dostepu.']);
    exit;
}

require_once(__DIR__ . '/../database.php');
require_once(__DIR__ . '/contact_messages_schema.php');

$conn = $db;
$id = (int)($_POST['id'] ?? 0);
$action = trim($_POST['action'] ?? '');

if ($id < 0 || $action === '') {
    echo json_encode(['success' => false, 'message' => 'Brak wymaganych danych.']);
    exit;
}

if (!ensureContactMessagesSchema($conn)) {
    echo json_encode(['success' => false, 'message' => 'Nie udalo sie przygotowac tabeli wiadomosci.']);
    exit;
}

if ($action === 'read') {
    $stmt = $conn->prepare("UPDATE contact_messages SET is_read = 1 WHERE message_id = ?");
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => $ok, 'message' => $ok ? 'Oznaczono jako przeczytana.' : 'Nie udalo sie oznaczyc wiadomosci.']);
    exit;
}

if ($action === 'delete') {
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE message_id = ?");
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => $ok, 'message' => $ok ? 'Wiadomosc usunieta.' : 'Nie udalo sie usunac wiadomosci.']);
    exit;
}

if ($action === 'approve_admin' || $action === 'reject_admin') {
    $stmt = $conn->prepare("
        SELECT cm.message_id, cm.subject, c.login, c.email, c.password_hash
        FROM contact_messages cm
        LEFT JOIN customers c ON cm.customer_id = c.customer_id
        WHERE cm.message_id = ?
        LIMIT 1
    ");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$request || trim($request['subject'] ?? '') !== 'Prosba o aktywacje konta administratora') {
        echo json_encode(['success' => false, 'message' => 'Nie znaleziono prosby o aktywacje admina.']);
        exit;
    }

    if ($action === 'reject_admin') {
        $stmt = $conn->prepare("DELETE FROM contact_messages WHERE message_id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => $ok, 'message' => $ok ? 'Prosba zostala odrzucona.' : 'Nie udalo sie odrzucic prosby.']);
        exit;
    }

    if (empty($request['login']) || empty($request['email']) || empty($request['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Brakuje danych klienta do utworzenia administratora.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT admin_id FROM administrators WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param('ss', $request['login'], $request['email']);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$existing) {
        $stmt = $conn->prepare("INSERT INTO administrators (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param('sss', $request['login'], $request['email'], $request['password_hash']);
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) {
            echo json_encode(['success' => false, 'message' => 'Nie udalo sie nadac uprawnien administratora.']);
            exit;
        }
    }

    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE message_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Konto administratora zostalo zatwierdzone.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Nieznana akcja.']);
?>
