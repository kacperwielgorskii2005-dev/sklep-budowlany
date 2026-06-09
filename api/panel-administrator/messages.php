<?php
require_once('../../api/database.php');
$conn = $db;

$sql = "
    SELECT cm.*, 
           c.first_name AS c_first_name, 
           c.last_name AS c_last_name 
    FROM contact_messages cm
    LEFT JOIN customers c ON cm.customer_id = c.customer_id
    ORDER BY cm.created_at DESC
";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<p>Brak wiadomości do wyświetlenia.</p>";
    return;
}
?>