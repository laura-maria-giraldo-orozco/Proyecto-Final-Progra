<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: admin/producto_eliminar.php
// Eliminar producto
// ==============================

require_once "../includes/auth.php";
requireRole('admin');
require_once "../includes/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: productos.php");
    exit;
}

$id = intval($_GET['id']);

// Obtener producto para eliminar imagen
$stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();
$stmt->close();

if ($producto) {
    // Eliminar imagen si existe
    if ($producto['imagen'] && file_exists("../uploads/productos/" . $producto['imagen'])) {
        unlink("../uploads/productos/" . $producto['imagen']);
    }

    // NUEVO: Eliminar primero los registros relacionados en detalle_compra
    $stmt = $conn->prepare("DELETE FROM detalle_compra WHERE producto_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Ahora sí eliminar el producto
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: productos.php?mensaje=Producto eliminado correctamente");
exit;
