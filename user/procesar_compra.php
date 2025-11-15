<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: user/procesar_compra.php
// Procesar compra
// ==============================

require_once "../includes/auth.php";
requireRole('usuario');
require_once "../includes/db.php";
require_once "../includes/funciones.php";

$titulo = "Procesar Compra";
$subtitulo = "Finalizar compra";
$esAdmin = false;

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit;
}

$mensaje = "";
$errores = [];

// Procesar compra
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar stock de todos los productos
    $carrito_valido = true;
    $productos_verificar = [];
    
    foreach ($_SESSION['carrito'] as $item) {
        $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->bind_param("i", $item['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        $stmt->close();
        
        if (!$producto || $producto['stock'] < $item['cantidad']) {
            $carrito_valido = false;
            $errores[] = "El producto " . htmlspecialchars($item['nombre']) . " no tiene stock suficiente.";
            break;
        }
        
        $productos_verificar[] = [
            'producto' => $producto,
            'cantidad' => $item['cantidad']
        ];
    }
    
    if ($carrito_valido && empty($errores)) {
        // Calcular total
        $total = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        // Iniciar transacción
        $conn->begin_transaction();
        
        try {
            // Crear compra
            $usuario_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("INSERT INTO compras (usuario_id, total) VALUES (?, ?)");
            $stmt->bind_param("id", $usuario_id, $total);
            $stmt->execute();
            $compra_id = $conn->insert_id;
            $stmt->close();
            
            // Crear detalles de compra y actualizar stock
            foreach ($productos_verificar as $item) {
                $producto = $item['producto'];
                $cantidad = $item['cantidad'];
                $precio = $producto['precio'];
                
                // Insertar detalle
                $stmt = $conn->prepare("INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $compra_id, $producto['id'], $cantidad, $precio);
                $stmt->execute();
                $stmt->close();
                
                // Actualizar stock
                $nuevo_stock = $producto['stock'] - $cantidad;
                $stmt = $conn->prepare("UPDATE productos SET stock = ? WHERE id = ?");
                $stmt->bind_param("ii", $nuevo_stock, $producto['id']);
                $stmt->execute();
                $stmt->close();
            }
            
            // Confirmar transacción
            $conn->commit();
            
            // Vaciar carrito
            $_SESSION['carrito'] = [];
            
            // Redirigir a historial
            header("Location: historial.php?compra_exitosa=1&compra_id=" . $compra_id);
            exit;
            
        } catch (Exception $e) {
            // Revertir transacción
            $conn->rollback();
            $errores[] = "Error al procesar la compra. Intente nuevamente.";
        }
    }
    
    if (!empty($errores)) {
        $mensaje = implode("<br>", $errores);
    }
}

// Calcular total actual
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

include "../includes/header.php";
include "../includes/navbar_user.php";
?>

<main class="user-main">
    <div class="container">
        <h2>Procesar Compra</h2>

        <?php if ($mensaje != ""): ?>
            <div class="alerta"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <div class="checkout-content">
            <div class="checkout-summary">
                <h3>Resumen de Compra</h3>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['carrito'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td><?php echo $item['cantidad']; ?></td>
                                <td><?php echo formatearPrecio($item['precio'] * $item['cantidad']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right"><strong>Total:</strong></td>
                            <td><strong><?php echo formatearPrecio($total); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="checkout-form">
                <h3>Confirmar Compra</h3>
                <p>Al confirmar, se procesará tu compra y se descontará el stock de los productos.</p>
                
                <form method="POST" action="" onsubmit="return confirm('¿Está seguro de confirmar esta compra?');">
                    <div class="form-actions">
                        <a href="carrito.php" class="btn btn-secondary">Volver al Carrito</a>
                        <button type="submit" class="btn btn-primary">Confirmar Compra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include "../includes/footer.php"; ?>

