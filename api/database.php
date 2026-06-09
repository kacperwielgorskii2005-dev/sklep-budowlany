<?php
$db = mysqli_connect('localhost','root','','sklep');

if (!$db) {
    die("Błąd połączenia: ". mysqli_connect_error());
}

?>