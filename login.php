<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: login.php
// Desarrollado en PHP puro
// ==============================

session_start();
require_once "includes/db.php"; // Archivo de conexión a la BD

// Variables de mensaje
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($email === "" || $password === "") {
        $mensaje = "Por favor, complete todos los campos.";
    } else {
        // Buscar usuario por email
        $stmt = $conn->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $nombre, $hash, $rol);
            $stmt->fetch();

            if (password_verify($password, $hash)) {
                // Iniciar sesión
                $_SESSION["user_id"] = $id;
                $_SESSION["nombre"] = $nombre;
                $_SESSION["rol"] = $rol;

                // Redirigir según rol
                if ($rol === "admin") {
                    header("Location: admin/dashboard.php");
                    exit;
                } else {
                    header("Location: user/tienda.php");
                    exit;
                }
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "No existe una cuenta con ese correo.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Carnicería La Morgue</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
    <script src="assets/js/alerts.js" defer></script>
</head>

<body>

    <header>
        <h1>CARNICERÍA LA MORGUE</h1>
        <p>Inicio de sesión</p>
    </header>

    <main>
        <section class="formulario">
            <h2>Iniciar Sesión</h2>

            <?php if ($mensaje != ""): ?>
                <div class="alerta"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="email">Correo electrónico:</label><br>
                <input type="email" id="email" name="email" maxlength="100" required><br>

                <label for="password">Contraseña:</label><br>
                <input type="password" id="password" name="password" minlength="4" maxlength="50" required><br>

                <button type="submit">Ingresar</button>
            </form>

            <p class="enlace">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
            <p class="enlace"><a href="index.php">← Volver al inicio</a></p>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Carnicería La Morgue. Todos los derechos reservados.</p>
    </footer>

</body>

</html>