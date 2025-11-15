    <nav class="navbar-admin">
        <div class="nav-container">
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="productos.php">Productos</a>
                <a href="usuarios.php">Usuarios</a>
                <a href="compras.php">Compras</a>
            </div>
            <div class="nav-user">
                <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

