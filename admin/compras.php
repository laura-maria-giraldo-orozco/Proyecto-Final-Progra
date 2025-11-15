<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: admin/compras.php
// Gestión de compras
// ==============================

require_once "../includes/auth.php";
requireRole('admin');
require_once "../includes/db.php";
require_once "../includes/funciones.php";

$titulo = "Compras";
$subtitulo = "Gestión de compras";
$esAdmin = true;

// Ver detalles de una compra específica
$compra_id = isset($_GET['ver']) && is_numeric($_GET['ver']) ? intval($_GET['ver']) : null;
$detalle_compra = null;

if ($compra_id) {
    // Obtener información de la compra
    $stmt = $conn->prepare("
        SELECT c.*, u.nombre as usuario_nombre, u.email as usuario_email 
        FROM compras c 
        JOIN usuarios u ON c.usuario_id = u.id 
        WHERE c.id = ?
    ");
    $stmt->bind_param("i", $compra_id);
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

// Obtener todas las compras
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$query = "
    SELECT c.id, c.fecha, c.total, u.nombre as usuario_nombre, u.email as usuario_email,
           (SELECT COUNT(*) FROM detalle_compra WHERE compra_id = c.id) as productos_count
    FROM compras c 
    JOIN usuarios u ON c.usuario_id = u.id 
    WHERE 1=1
";

$params = [];
$types = "";

if ($busqueda !== '') {
    $query .= " AND (u.nombre LIKE ? OR u.email LIKE ?)";
    $busqueda_param = "%$busqueda%";
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $types .= "ss";
}

$query .= " ORDER BY c.fecha DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$compras = [];
while ($row = $result->fetch_assoc()) {
    $compras[] = $row;
}
$stmt->close();

include "../includes/header.php";
include "../includes/navbar_admin.php";
?>

<main class="admin-main">
    <div class="container">
        <div class="page-header">
            <h2>Gestión de Compras</h2>
        </div>

        <?php if ($detalle_compra): ?>
            <!-- Detalle de compra -->
            <div class="section">
                <div class="page-header">
                    <h3>Detalle de Compra #<?php echo $detalle_compra['id']; ?></h3>
                    <a href="compras.php" class="btn btn-secondary">Volver</a>
                </div>

                <div class="compra-info">
                    <p><strong>Usuario:</strong> <?php echo htmlspecialchars($detalle_compra['usuario_nombre']); ?> (<?php echo htmlspecialchars($detalle_compra['usuario_email']); ?>)</p>
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
                                    <?php endif; ?>
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
            <div class="filters">
                <form method="GET" action="" class="filter-form">
                    <input type="text" name="buscar" placeholder="Buscar por usuario..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit" class="btn btn-small">Buscar</button>
                    <a href="compras.php" class="btn btn-small btn-secondary">Limpiar</a>
                </form>
            </div>

            <?php if (empty($compras)): ?>
                <p>No se encontraron compras.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
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
                                <td>
                                    <?php echo htmlspecialchars($compra['usuario_nombre']); ?><br>
                                    <small><?php echo htmlspecialchars($compra['usuario_email']); ?></small>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($compra['fecha'])); ?></td>
                                <td><?php echo $compra['productos_count']; ?></td>
                                <td><?php echo formatearPrecio($compra['total']); ?></td>
                                <td>
                                    <a href="compras.php?ver=<?php echo $compra['id']; ?>" class="btn-small">Ver Detalle</a>
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

