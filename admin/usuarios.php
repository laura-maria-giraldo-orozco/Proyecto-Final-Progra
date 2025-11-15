<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: admin/usuarios.php
// Gestión de usuarios
// ==============================

require_once "../includes/auth.php";
requireRole('admin');
require_once "../includes/db.php";
require_once "../includes/funciones.php";

$titulo = "Usuarios";
$subtitulo = "Gestión de usuarios";
$esAdmin = true;

$mensaje = "";

// Cambiar rol de usuario
if (isset($_POST['cambiar_rol'])) {
    $user_id = intval($_POST['user_id']);
    $nuevo_rol = $_POST['nuevo_rol'];

    if ($nuevo_rol === 'admin' || $nuevo_rol === 'usuario') {
        $stmt = $conn->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_rol, $user_id);
        if ($stmt->execute()) {
            $mensaje = "Rol actualizado correctamente.";
        } else {
            $mensaje = "Error al actualizar el rol.";
        }
        $stmt->close();
    }
}

// Eliminar usuario
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    // No permitir eliminar el propio usuario
    if ($id != $_SESSION['user_id']) {
        // NUEVO: Primero eliminar los detalles de las compras del usuario
        $stmt = $conn->prepare("DELETE dc FROM detalle_compra dc 
                               INNER JOIN compras c ON dc.compra_id = c.id 
                               WHERE c.usuario_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // NUEVO: Luego eliminar las compras del usuario
        $stmt = $conn->prepare("DELETE FROM compras WHERE usuario_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Finalmente eliminar el usuario
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "Usuario eliminado correctamente.";
        } else {
            $mensaje = "Error al eliminar el usuario.";
        }
        $stmt->close();
    } else {
        $mensaje = "No puedes eliminar tu propio usuario.";
    }
}

// Obtener usuarios
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$rol_filtro = isset($_GET['rol']) ? trim($_GET['rol']) : '';

$query = "SELECT id, nombre, email, rol, 
          (SELECT COUNT(*) FROM compras WHERE usuario_id = usuarios.id) as total_compras
          FROM usuarios WHERE 1=1";
$params = [];
$types = "";

if ($busqueda !== '') {
    $query .= " AND (nombre LIKE ? OR email LIKE ?)";
    $busqueda_param = "%$busqueda%";
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $types .= "ss";
}

if ($rol_filtro !== '') {
    $query .= " AND rol = ?";
    $params[] = $rol_filtro;
    $types .= "s";
}

$query .= " ORDER BY nombre ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}
$stmt->close();

include "../includes/header.php";
include "../includes/navbar_admin.php";
?>

<main class="admin-main">
    <div class="container">
        <div class="page-header">
            <h2>Gestión de Usuarios</h2>
        </div>

        <?php if ($mensaje != ""): ?>
            <div class="alerta alerta-exito"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <div class="filters">
            <form method="GET" action="" class="filter-form">
                <input type="text" name="buscar" placeholder="Buscar usuario..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <select name="rol">
                    <option value="">Todos los roles</option>
                    <option value="admin" <?php echo $rol_filtro === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="usuario" <?php echo $rol_filtro === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                </select>
                <button type="submit" class="btn btn-small">Buscar</button>
                <a href="usuarios.php" class="btn btn-small btn-secondary">Limpiar</a>
            </form>
        </div>

        <?php if (empty($usuarios)): ?>
            <p>No se encontraron usuarios.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Compras</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $usuario['id']; ?>">
                                    <select name="nuevo_rol" onchange="this.form.submit()">
                                        <option value="admin" <?php echo $usuario['rol'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        <option value="usuario" <?php echo $usuario['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                                    </select>
                                    <input type="hidden" name="cambiar_rol" value="1">
                                </form>
                            </td>
                            <td><?php echo $usuario['total_compras']; ?></td>
                            <td>
                                <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                    <a href="usuarios.php?eliminar=<?php echo $usuario['id']; ?>"
                                        class="btn-small btn-delete"
                                        onclick="return confirm('¿Está seguro de eliminar este usuario? Se eliminarán también todas sus compras.');">Eliminar</a>
                                <?php else: ?>
                                    <span class="text-muted">Tu usuario</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>

<?php include "../includes/footer.php"; ?>