<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: admin/producto_nuevo.php
// Crear nuevo producto
// ==============================

require_once "../includes/auth.php";
requireRole('admin');
require_once "../includes/db.php";
require_once "../includes/funciones.php";

$titulo = "Nuevo Producto";
$subtitulo = "Agregar producto";
$esAdmin = true;

$mensaje = "";
$errores = [];

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
    $imagen = "";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['imagen'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed)) {
            $errores[] = "El archivo debe ser una imagen (JPEG, PNG, GIF o WebP).";
        } elseif ($file['size'] > $max_size) {
            $errores[] = "La imagen no debe superar los 5MB.";
        } else {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imagen = uniqid() . '.' . $extension;
            $upload_dir = '../uploads/productos/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (!move_uploaded_file($file['tmp_name'], $upload_dir . $imagen)) {
                $errores[] = "Error al subir la imagen.";
            }
        }
    }

    if (empty($errores)) {
        $precio = floatval($precio);
        $stock = intval($stock);
        $categoria = $categoria !== "" ? $categoria : null;

        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria, stock, imagen) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsis", $nombre, $descripcion, $precio, $categoria, $stock, $imagen);

        if ($stmt->execute()) {
            header("Location: productos.php?mensaje=Producto creado correctamente");
            exit;
        } else {
            $mensaje = "Error al crear el producto.";
        }
        $stmt->close();
    } else {
        $mensaje = implode("<br>", $errores);
    }
}

$categorias = obtenerCategorias();

include "../includes/header.php";
include "../includes/navbar_admin.php";
?>

<main class="admin-main">
    <div class="container">
        <div class="page-header">
            <h2>Nuevo Producto</h2>
            <a href="productos.php" class="btn btn-secondary">Volver</a>
        </div>

        <?php if ($mensaje != ""): ?>
            <div class="alerta"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data" class="product-form">
            <div class="form-group">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" maxlength="100" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="precio">Precio (€) *</label>
                    <input type="number" id="precio" name="precio" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock *</label>
                    <input type="number" id="stock" name="stock" min="0" required>
                </div>
            </div>

            <div class="form-group">
                <label for="categoria">Categoría</label>
                <input type="text" id="categoria" name="categoria" maxlength="50" list="categorias">
                <datalist id="categorias">
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="form-group">
                <label for="imagen">Imagen</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <small>Formatos permitidos: JPEG, PNG, GIF, WebP. Tamaño máximo: 5MB</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Crear Producto</button>
                <a href="productos.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php include "../includes/footer.php"; ?>

