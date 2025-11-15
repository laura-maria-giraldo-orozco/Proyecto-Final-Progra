    <nav class="navbar-user">
        <div class="nav-container">
            <div class="nav-links">
                <a href="tienda.php">Tienda</a>
                <a href="carrito.php">
                    Carrito
                    <?php 
                    if (!isset($totalCarrito)) {
                        require_once "../includes/funciones.php";
                        $totalCarrito = obtenerTotalCarrito();
                    }
                    if ($totalCarrito > 0) {
                        echo "<span class='badge'>$totalCarrito</span>";
                    }
                    ?>
                </a>
                <a href="historial.php">Historial</a>
            </div>
            <div class="nav-user">
                <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

