<?php
function LogoutUser(){
    $_SESSION = array();
    
    session_destroy();
    
    session_start();
    $_SESSION['user'] = 'none';
    $_SESSION['cart'] = [];
    
    header("Location: index.php");
    exit();
}
?>