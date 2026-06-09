<?php
require_once('../../api/database.php');
require('../../api/main/MainProductsDisplay.php');


if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['user'])){
    $_SESSION['user'] = 'none';
}

$prompt = isset($_GET['prompt']) ? $_GET['prompt'] : "";

if (isset($_GET['categories']) && is_array($_GET['categories'])) {
    DisplayProducts($_GET['categories'], $prompt, $db);
} else {
    DisplayProducts("NOT NULL", $prompt, $db);
}
?>