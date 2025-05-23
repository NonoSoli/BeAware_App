<?php
$host = 'localhost';
$db = 'db_beaware';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>
