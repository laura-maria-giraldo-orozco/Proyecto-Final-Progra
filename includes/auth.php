<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: includes/auth.php
// Verificación de autenticación
// ==============================

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Verificar rol si es necesario (se puede pasar como parámetro)
function requireRole($role) {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $role) {
        header("Location: ../index.php");
        exit;
    }
}
?>

