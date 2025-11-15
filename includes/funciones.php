<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: includes/funciones.php
// Funciones auxiliares
// ==============================

require_once "db.php";

/**
 * Sanitizar entrada de datos
 */
function sanitizar($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Formatear precio
 */
function formatearPrecio($precio)
{
    return "$" . number_format($precio, 0, ',', '.');
}

/**
 * Validar email
 */
function validarEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Obtener categorías de productos
 */
function obtenerCategorias()
{
    global $conn;
    $stmt = $conn->prepare("SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria");
    $stmt->execute();
    $result = $stmt->get_result();
    $categorias = [];
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row['categoria'];
    }
    $stmt->close();
    return $categorias;
}

/**
 * Obtener total de productos en carrito
 */
function obtenerTotalCarrito()
{
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return 0;
    }
    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['cantidad'];
    }
    return $total;
}

/**
 * Calcular total del carrito
 */
function calcularTotalCarrito()
{
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return 0;
    }
    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }
    return $total;
}
