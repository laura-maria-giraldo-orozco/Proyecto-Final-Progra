<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: admin/productos.php
// Gestión de productos
// ==============================

require_once "../includes/auth.php";
requireRole('admin');
require_once "../includes/db.php";
require_once "../includes/funciones.php";

$titulo = "Productos";
$subtitulo = "Gestión de productos";
$esAdmin = true;

$mensaje = "";

// Eliminar producto
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $mensaje = "Producto eliminado correctamente.";
    } else {
        $mensaje = "Error al eliminar el producto.";
    }
    $stmt->close();
}

// Obtener productos
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$categoria_filtro = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

$query = "SELECT * FROM productos WHERE 1=1";
$params = [];
$types = "";

if ($busqueda !== '') {
    $query .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
    $busqueda_param = "%$busqueda%";
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $types .= "ss";
}

if ($categoria_filtro !== '') {
    $query .= " AND categoria = ?";
    $params[] = $categoria_filtro;
    $types .= "s";
}

$query .= " ORDER BY id ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}
$stmt->close();

$categorias = obtenerCategorias();

include "../includes/header.php";
include "../includes/navbar_admin.php";
?>

<main class="admin-main">
    <div class="container">
        <div class="page-header">
            <h2>Gestión de Productos</h2>
            <a href="producto_nuevo.php" class="btn btn-primary">Nuevo Producto</a>
        </div>

        <?php if ($mensaje != ""): ?>
            <div class="alerta alerta-exito"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <div class="filters">
            <form method="GET" action="" class="filter-form">
                <input type="text" name="buscar" placeholder="Buscar producto..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <select name="categoria">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $categoria_filtro === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-small">Buscar</button>
                <a href="productos.php" class="btn btn-small btn-secondary">Limpiar</a>
            </form>
        </div>

        <?php if (empty($productos)): ?>
            <p>No se encontraron productos.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr class="<?php echo $producto['stock'] < 10 ? 'low-stock' : ''; ?>">
                            <td><?php echo $producto['id']; ?></td>
                            <td>
                                <?php if ($producto['imagen']): ?>
                                    <img src="../uploads/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                                         class="product-thumb">
                                <?php else: ?>
                                    <span class="no-image">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['categoria'] ?? 'Sin categoría'); ?></td>
                            <td><?php echo formatearPrecio($producto['precio']); ?></td>
                            <td>
                                <span class="<?php echo $producto['stock'] < 10 ? 'stock-low' : 'stock-ok'; ?>">
                                    <?php echo $producto['stock']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="producto_editar.php?id=<?php echo $producto['id']; ?>" class="btn-small btn-edit">Editar</a>
                                <a href="producto_eliminar.php?id=<?php echo $producto['id']; ?>" 
                                   class="btn-small btn-delete"
                                   onclick="return confirm('¿Está seguro de eliminar este producto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>

<?php include "../includes/footer.php"; ?>

