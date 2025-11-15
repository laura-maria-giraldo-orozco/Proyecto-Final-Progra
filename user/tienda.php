<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: user/tienda.php
// Tienda de productos
// ==============================

require_once "../includes/auth.php";
requireRole('usuario');
require_once "../includes/db.php";
require_once "../includes/funciones.php";

$titulo = "Tienda";
$subtitulo = "Nuestros productos";
$esAdmin = false;

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto al carrito
if (isset($_POST['agregar_carrito'])) {
    $producto_id = intval($_POST['producto_id']);
    $cantidad = intval($_POST['cantidad']);
    
    if ($cantidad > 0) {
        // Obtener producto
        $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ? AND stock > 0");
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        $stmt->close();
        
        if ($producto && $cantidad <= $producto['stock']) {
            // Verificar si ya está en el carrito
            $existe = false;
            foreach ($_SESSION['carrito'] as $key => $item) {
                if ($item['id'] == $producto_id) {
                    $_SESSION['carrito'][$key]['cantidad'] += $cantidad;
                    if ($_SESSION['carrito'][$key]['cantidad'] > $producto['stock']) {
                        $_SESSION['carrito'][$key]['cantidad'] = $producto['stock'];
                    }
                    $existe = true;
                    break;
                }
            }
            
            if (!$existe) {
                $_SESSION['carrito'][] = [
                    'id' => $producto['id'],
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'cantidad' => $cantidad,
                    'imagen' => $producto['imagen']
                ];
            }
            
            header("Location: tienda.php?agregado=1");
            exit;
        }
    }
}

// Obtener productos
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$categoria_filtro = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

$query = "SELECT * FROM productos WHERE stock > 0";
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

$query .= " ORDER BY nombre ASC";

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
include "../includes/navbar_user.php";
?>

<script src="../assets/js/carrito.js" defer></script>

<main class="user-main">
    <div class="container">
        <h2>Nuestros Productos</h2>

        <?php if (isset($_GET['agregado'])): ?>
            <div class="alerta alerta-exito">Producto agregado al carrito.</div>
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
                <button type="submit" class="btn btn-primary">Buscar</button>
                <a href="tienda.php" class="btn btn-secondary">Limpiar</a>
            </form>
        </div>

        <?php if (empty($productos)): ?>
            <p>No se encontraron productos disponibles.</p>
        <?php else: ?>
            <div class="productos-grid">
                <?php foreach ($productos as $producto): ?>
                    <div class="producto-card">
                        <?php if ($producto['imagen']): ?>
                            <img src="../uploads/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                                 class="producto-imagen">
                        <?php else: ?>
                            <div class="producto-sin-imagen">Sin imagen</div>
                        <?php endif; ?>
                        
                        <div class="producto-info">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <?php if ($producto['categoria']): ?>
                                <span class="categoria"><?php echo htmlspecialchars($producto['categoria']); ?></span>
                            <?php endif; ?>
                            
                            <?php if ($producto['descripcion']): ?>
                                <p class="descripcion"><?php echo htmlspecialchars(substr($producto['descripcion'], 0, 100)); ?><?php echo strlen($producto['descripcion']) > 100 ? '...' : ''; ?></p>
                            <?php endif; ?>
                            
                            <div class="producto-precio">
                                <strong><?php echo formatearPrecio($producto['precio']); ?></strong>
                                <span class="stock">Stock: <?php echo $producto['stock']; ?></span>
                            </div>
                            
                            <form method="POST" action="" class="form-carrito">
                                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                <div class="cantidad-input">
                                    <label for="cantidad_<?php echo $producto['id']; ?>">Cantidad:</label>
                                    <input type="number" 
                                           id="cantidad_<?php echo $producto['id']; ?>" 
                                           name="cantidad" 
                                           min="1" 
                                           max="<?php echo $producto['stock']; ?>" 
                                           value="1" 
                                           required>
                                </div>
                                <button type="submit" name="agregar_carrito" class="btn btn-primary btn-block">
                                    Agregar al Carrito
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include "../includes/footer.php"; ?>

