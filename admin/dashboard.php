<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: admin/dashboard.php
// Dashboard del administrador
// ==============================

require_once "../includes/auth.php";
requireRole('admin');
require_once "../includes/db.php";
require_once "../includes/funciones.php";

$titulo = "Dashboard";
$subtitulo = "Panel de administración";
$esAdmin = true;

// Obtener estadísticas
$stats = [];

// Total de productos
$result = $conn->query("SELECT COUNT(*) as total FROM productos");
$stats['productos'] = $result->fetch_assoc()['total'];

// Total de usuarios
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'usuario'");
$stats['usuarios'] = $result->fetch_assoc()['total'];

// Total de compras
$result = $conn->query("SELECT COUNT(*) as total FROM compras");
$stats['compras'] = $result->fetch_assoc()['total'];

// Total de ventas
$result = $conn->query("SELECT SUM(total) as total FROM compras");
$stats['ventas'] = $result->fetch_assoc()['total'] ?? 0;

// Productos con bajo stock (menos de 10 unidades)
$result = $conn->query("SELECT COUNT(*) as total FROM productos WHERE stock < 10");
$stats['bajo_stock'] = $result->fetch_assoc()['total'];

// Últimas compras
$result = $conn->query("
    SELECT c.id, c.fecha, c.total, u.nombre as usuario 
    FROM compras c 
    JOIN usuarios u ON c.usuario_id = u.id 
    ORDER BY c.fecha DESC 
    LIMIT 5
");
$ultimas_compras = [];
while ($row = $result->fetch_assoc()) {
    $ultimas_compras[] = $row;
}

include "../includes/header.php";
include "../includes/navbar_admin.php";
?>

<main class="admin-main">
    <div class="container">
        <h2>Dashboard</h2>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Productos</h3>
                <p class="stat-number"><?php echo $stats['productos']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Usuarios</h3>
                <p class="stat-number"><?php echo $stats['usuarios']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Compras</h3>
                <p class="stat-number"><?php echo $stats['compras']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Ventas Total</h3>
                <p class="stat-number"><?php echo formatearPrecio($stats['ventas']); ?></p>
            </div>
            <div class="stat-card warning">
                <h3>Bajo Stock</h3>
                <p class="stat-number"><?php echo $stats['bajo_stock']; ?></p>
            </div>
        </div>

        <div class="section">
            <h3>Últimas Compras</h3>
            <?php if (empty($ultimas_compras)): ?>
                <p>No hay compras registradas.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimas_compras as $compra): ?>
                            <tr>
                                <td><?php echo $compra['id']; ?></td>
                                <td><?php echo htmlspecialchars($compra['usuario']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($compra['fecha'])); ?></td>
                                <td><?php echo formatearPrecio($compra['total']); ?></td>
                                <td>
                                    <a href="compras.php?ver=<?php echo $compra['id']; ?>" class="btn-small">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include "../includes/footer.php"; ?>

