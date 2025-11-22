<?php
$host = "db";
$user = "usuario"; 
$pass = "usuario"; 
$db   = "TiendaOnline"; 

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>