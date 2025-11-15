<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: user/historial.php
// Historial de compras
// ==============================

require_once "../includes/auth.php";
requireRole('usuario');
require_once "../includes/db.php";
require_once "../includes/funciones.php";

$titulo = "Historial";
$subtitulo = "Historial de compras";
$esAdmin = false;

$mensaje = "";

if (isset($_GET['compra_exitosa'])) {
    $mensaje = "Compra realizada exitosamente.";
    if (isset($_GET['compra_id'])) {
        $mensaje .= " ID de compra: #" . intval($_GET['compra_id']);
    }
}

// Ver detalles de una compra específica
$compra_id = isset($_GET['ver']) && is_numeric($_GET['ver']) ? intval($_GET['ver']) : null;
$detalle_compra = null;

if ($compra_id) {
    // Verificar que la compra pertenece al usuario
    $usuario_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM compras WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $compra_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $detalle_compra = $result->fetch_assoc();
    $stmt->close();

    if ($detalle_compra) {
        // Obtener productos de la compra
        $stmt = $conn->prepare("
            SELECT dc.*, p.nombre as producto_nombre, p.imagen 
            FROM detalle_compra dc 
            JOIN productos p ON dc.producto_id = p.id 
            WHERE dc.compra_id = ?
        ");
        $stmt->bind_param("i", $compra_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        $stmt->close();
    }
}

// Obtener todas las compras del usuario
$usuario_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT c.*, 
           (SELECT COUNT(*) FROM detalle_compra WHERE compra_id = c.id) as productos_count
    FROM compras c 
    WHERE c.usuario_id = ? 
    ORDER BY c.fecha DESC
");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$compras = [];
while ($row = $result->fetch_assoc()) {
    $compras[] = $row;
}
$stmt->close();

include "../includes/header.php";
include "../includes/navbar_user.php";
?>
<link rel="stylesheet" href="../assets/css/historial.css">
<main class="user-main">
    <div class="container">
        <h2>Historial de Compras</h2>

        <?php if ($mensaje != ""): ?>
            <div class="alerta alerta-exito"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <?php if ($detalle_compra): ?>
            <!-- Detalle de compra -->
            <div class="section">
                <div class="page-header">
                    <h3>Detalle de Compra #<?php echo $detalle_compra['id']; ?></h3>
                    <a href="historial.php" class="btn btn-secondary">Volver</a>
                </div>

                <div class="compra-info">
                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i:s', strtotime($detalle_compra['fecha'])); ?></p>
                    <p><strong>Total:</strong> <?php echo formatearPrecio($detalle_compra['total']); ?></p>
                </div>

                <h4>Productos:</h4>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td>
                                    <?php if ($producto['imagen']): ?>
                                        <img src="../uploads/productos/<?php echo htmlspecialchars($producto['imagen']); ?>"
                                            alt="<?php echo htmlspecialchars($producto['producto_nombre']); ?>"
                                            class="product-thumb">
                                    <?php endif; ?> <br>
                                    <?php echo htmlspecialchars($producto['producto_nombre']); ?>
                                </td>
                                <td><?php echo $producto['cantidad']; ?></td>
                                <td><?php echo formatearPrecio($producto['precio']); ?></td>
                                <td><?php echo formatearPrecio($producto['precio'] * $producto['cantidad']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Lista de compras -->
            <?php if (empty($compras)): ?>
                <p>No tienes compras registradas.</p>
                <a href="tienda.php" class="btn btn-primary">Ir a la Tienda</a>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compras as $compra): ?>
                            <tr>
                                <td><?php echo $compra['id']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($compra['fecha'])); ?></td>
                                <td><?php echo $compra['productos_count']; ?></td>
                                <td><?php echo formatearPrecio($compra['total']); ?></td>
                                <td>
                                    <a href="historial.php?ver=<?php echo $compra['id']; ?>" class="btn-small">Ver Detalle</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php include "../includes/footer.php"; ?>