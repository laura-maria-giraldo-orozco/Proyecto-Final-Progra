<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: includes/db.php
// Conexión básica MySQL
// ==============================

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "carniceria";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>