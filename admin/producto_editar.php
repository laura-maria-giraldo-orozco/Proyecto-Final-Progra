<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: admin/producto_editar.php
// Editar producto
// ==============================

require_once "../includes/auth.php";
requireRole('admin');
require_once "../includes/db.php";
require_once "../includes/funciones.php";

$titulo = "Editar Producto";
$subtitulo = "Modificar producto";
$esAdmin = true;

$mensaje = "";
$errores = [];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: productos.php");
    exit;
}

$id = intval($_GET['id']);

// Obtener producto
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();
$stmt->close();

if (!$producto) {
    header("Location: productos.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $precio = trim($_POST["precio"]);
    $categoria = trim($_POST["categoria"]);
    $stock = trim($_POST["stock"]);

    // Validaciones
    if ($nombre === "") {
        $errores[] = "El nombre es obligatorio.";
    }
    if ($precio === "" || !is_numeric($precio) || $precio <= 0) {
        $errores[] = "El precio debe ser un número válido mayor a 0.";
    }
    if ($stock === "" || !is_numeric($stock) || $stock < 0) {
        $errores[] = "El stock debe ser un número válido mayor o igual a 0.";
    }

    // Procesar imagen
    $imagen = $producto['imagen'];
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['imagen'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed)) {
            $errores[] = "El archivo debe ser una imagen (JPEG, PNG, GIF o WebP).";
        } elseif ($file['size'] > $max_size) {
            $errores[] = "La imagen no debe superar los 5MB.";
        } else {
            // Eliminar imagen anterior si existe
            if ($imagen && file_exists("../uploads/productos/" . $imagen)) {
                unlink("../uploads/productos/" . $imagen);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imagen = uniqid() . '.' . $extension;
            $upload_dir = '../uploads/productos/';
            
            if (!move_uploaded_file($file['tmp_name'], $upload_dir . $imagen)) {
                $errores[] = "Error al subir la imagen.";
            }
        }
    }

    if (empty($errores)) {
        $precio = floatval($precio);
        $stock = intval($stock);
        $categoria = $categoria !== "" ? $categoria : null;

        $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria = ?, stock = ?, imagen = ? WHERE id = ?");
        $stmt->bind_param("ssdsisi", $nombre, $descripcion, $precio, $categoria, $stock, $imagen, $id);

        if ($stmt->execute()) {
            header("Location: productos.php?mensaje=Producto actualizado correctamente");
            exit;
        } else {
            $mensaje = "Error al actualizar el producto.";
        }
        $stmt->close();
    } else {
        $mensaje = implode("<br>", $errores);
    }

    // Actualizar datos del producto para mostrar en el formulario
    $producto['nombre'] = $nombre;
    $producto['descripcion'] = $descripcion;
    $producto['precio'] = $precio;
    $producto['categoria'] = $categoria;
    $producto['stock'] = $stock;
    $producto['imagen'] = $imagen;
}

$categorias = obtenerCategorias();

include "../includes/header.php";
include "../includes/navbar_admin.php";
?>

<main class="admin-main">
    <div class="container">
        <div class="page-header">
            <h2>Editar Producto</h2>
            <a href="productos.php" class="btn btn-secondary">Volver</a>
        </div>

        <?php if ($mensaje != ""): ?>
            <div class="alerta"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data" class="product-form">
            <div class="form-group">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" maxlength="100" 
                       value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="precio">Precio ($ COP) *</label>
                    <input type="number" id="precio" name="precio" step="10" min="0" 
                           value="<?php echo $producto['precio']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock *</label>
                    <input type="number" id="stock" name="stock" min="0" 
                           value="<?php echo $producto['stock']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="categoria">Categoría</label>
                <input type="text" id="categoria" name="categoria" maxlength="50" 
                       value="<?php echo htmlspecialchars($producto['categoria'] ?? ''); ?>" list="categorias">
                <datalist id="categorias">
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="form-group">
                <label for="imagen">Imagen</label>
                <?php if ($producto['imagen']): ?>
                    <div class="current-image">
                        <img src="../uploads/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             alt="Imagen actual" class="preview-image">
                        <p>Imagen actual</p>
                    </div>
                <?php endif; ?>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <small>Formatos permitidos: JPEG, PNG, GIF, WebP. Tamaño máximo: 5MB</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                <a href="productos.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php include "../includes/footer.php"; ?>

