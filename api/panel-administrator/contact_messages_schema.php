<?php
function ensureContactMessagesSchema(mysqli $conn): bool
{
    $readColumn = $conn->query("SHOW COLUMNS FROM contact_messages LIKE 'is_read'");
    if ($readColumn && $readColumn->num_rows === 0) {
        if (!$conn->query("ALTER TABLE contact_messages ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0")) {
            return false;
        }
    }

    $primaryKey = $conn->query("SHOW INDEX FROM contact_messages WHERE Key_name = 'PRIMARY'");
    if ($primaryKey && $primaryKey->num_rows === 0) {
        if (!$conn->query("SET @contact_message_id := 0")) {
            return false;
        }

        if (!$conn->query("UPDATE contact_messages SET message_id = (@contact_message_id := @contact_message_id + 1) ORDER BY created_at ASC, email ASC, subject ASC")) {
            return false;
        }

        if (!$conn->query("ALTER TABLE contact_messages ADD PRIMARY KEY (message_id)")) {
            return false;
        }
    }

    $messageIdColumn = $conn->query("SHOW COLUMNS FROM contact_messages LIKE 'message_id'");
    $messageIdData = $messageIdColumn ? $messageIdColumn->fetch_assoc() : null;
    $extra = strtolower($messageIdData['Extra'] ?? '');

    if (strpos($extra, 'auto_increment') === false) {
        if (!$conn->query("ALTER TABLE contact_messages MODIFY message_id INT(11) NOT NULL AUTO_INCREMENT")) {
            return false;
        }
    }

    return true;
}
?>
