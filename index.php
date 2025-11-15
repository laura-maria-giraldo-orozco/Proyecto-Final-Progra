<?php
// Iniciar sesión
session_start();

// Si el usuario ya inició sesión, redirigir según su rol
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    } elseif ($_SESSION['rol'] === 'usuario') {
        header("Location: user/tienda.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnicería La Morgue</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
</head>

<body>

    <header>
        <h1>CARNICERÍA LA MORGUE</h1>
        <p>Calidad, frescura y servicio al alcance de un clic</p>
    </header>

    <main>
        <section class="bienvenida">
            <img src="assets/img/logo.png" alt="Logo Carnicería" class="logo">
            <h2>Bienvenido a nuestra tienda virtual</h2>
            <p>Compra los mejores productos cárnicos directamente desde tu hogar o administra tu inventario si eres
                parte del equipo.</p>

            <div class="botones">
                <a href="login.php" class="btn rojo">Iniciar Sesión</a>
                <a href="register.php" class="btn gris">Registrarse</a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Carnicería La Morgue. Todos los derechos reservados.</p>
    </footer>

</body>

</html>