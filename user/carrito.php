<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: user/carrito.php
// Carrito de compras
// ==============================

require_once "../includes/auth.php";
requireRole('usuario');
require_once "../includes/db.php";
require_once "../includes/funciones.php";

$titulo = "Carrito";
$subtitulo = "Tu carrito de compras";
$esAdmin = false;

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$mensaje = "";

// Actualizar cantidad
if (isset($_POST['actualizar'])) {
    $producto_id = intval($_POST['producto_id']);
    $cantidad = intval($_POST['cantidad']);
    
    // Verificar stock disponible
    $stmt = $conn->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();
    $stmt->close();
    
    if ($producto && $cantidad > 0 && $cantidad <= $producto['stock']) {
        foreach ($_SESSION['carrito'] as $key => $item) {
            if ($item['id'] == $producto_id) {
                $_SESSION['carrito'][$key]['cantidad'] = $cantidad;
                break;
            }
        }
        $mensaje = "Carrito actualizado.";
    } else {
        $mensaje = "Cantidad inválida o sin stock suficiente.";
    }
}

// Eliminar producto del carrito
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $producto_id = intval($_GET['eliminar']);
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['id'] == $producto_id) {
            unset($_SESSION['carrito'][$key]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar
            $mensaje = "Producto eliminado del carrito.";
            break;
        }
    }
}

// Vaciar carrito
if (isset($_GET['vaciar'])) {
    $_SESSION['carrito'] = [];
    $mensaje = "Carrito vaciado.";
}

// Actualizar precios del carrito con datos de la BD
$carrito_actualizado = [];
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ? AND stock > 0");
    $stmt->bind_param("i", $item['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();
    $stmt->close();
    
    if ($producto) {
        // Actualizar precio y ajustar cantidad si es necesario
        $cantidad = min($item['cantidad'], $producto['stock']);
        $precio = $producto['precio'];
        
        $carrito_actualizado[] = [
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'precio' => $precio,
            'cantidad' => $cantidad,
            'imagen' => $producto['imagen'],
            'stock' => $producto['stock']
        ];
        
        $total += $precio * $cantidad;
    }
}

$_SESSION['carrito'] = $carrito_actualizado;

include "../includes/header.php";
include "../includes/navbar_user.php";
?>

<script src="../assets/js/carrito.js" defer></script>

<main class="user-main">
    <div class="container">
        <h2>Tu Carrito de Compras</h2>

        <?php if ($mensaje != ""): ?>
            <div class="alerta alerta-exito"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <?php if (empty($_SESSION['carrito'])): ?>
            <div class="carrito-vacio">
                <p>Tu carrito está vacío.</p>
                <a href="tienda.php" class="btn btn-primary">Ir a la Tienda</a>
            </div>
        <?php else: ?>
            <div class="carrito-contenido">
                <table class="carrito-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['carrito'] as $item): ?>
                            <tr>
                                <td>
                                    <div class="producto-carrito">
                                        <?php if ($item['imagen']): ?>
                                            <img src="../uploads/productos/<?php echo htmlspecialchars($item['imagen']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['nombre']); ?>" 
                                                 class="producto-mini">
                                        <?php endif; ?>
                                        <span><?php echo htmlspecialchars($item['nombre']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo formatearPrecio($item['precio']); ?></td>
                                <td>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" 
                                               name="cantidad" 
                                               min="1" 
                                               max="<?php echo $item['stock']; ?>" 
                                               value="<?php echo $item['cantidad']; ?>" 
                                               style="width: 60px;">
                                        <button type="submit" name="actualizar" class="btn-small">Actualizar</button>
                                    </form>
                                </td>
                                <td><?php echo formatearPrecio($item['precio'] * $item['cantidad']); ?></td>
                                <td>
                                    <a href="carrito.php?eliminar=<?php echo $item['id']; ?>" 
                                       class="btn-small btn-delete"
                                       onclick="return confirm('¿Eliminar este producto del carrito?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td><strong><?php echo formatearPrecio($total); ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="carrito-acciones">
                    <a href="carrito.php?vaciar=1" 
                       class="btn btn-secondary"
                       onclick="return confirm('¿Vaciar todo el carrito?');">Vaciar Carrito</a>
                    <a href="procesar_compra.php" class="btn btn-primary">Proceder al Pago</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include "../includes/footer.php"; ?>

