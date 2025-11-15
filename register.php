<?php
// ==============================
// CARNICERÍA LA MORGUE
// Archivo: register.php
// Registro de nuevos usuarios
// ==============================

session_start();
require_once "includes/db.php";

// Si el usuario ya inició sesión, redirigir
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    } elseif ($_SESSION['rol'] === 'usuario') {
        header("Location: user/tienda.php");
        exit;
    }
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $password_confirm = trim($_POST["password_confirm"]);

    // Validaciones
    if ($nombre === "" || $email === "" || $password === "" || $password_confirm === "") {
        $mensaje = "Por favor, complete todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El correo electrónico no es válido.";
    } elseif (strlen($password) < 4) {
        $mensaje = "La contraseña debe tener al menos 4 caracteres.";
    } elseif ($password !== $password_confirm) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el email ya existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "Este correo electrónico ya está registrado.";
        } else {
            // Registrar nuevo usuario
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'usuario')");
            $stmt->bind_param("sss", $nombre, $email, $hash);

            if ($stmt->execute()) {
                $mensaje = "Registro exitoso. Redirigiendo...";
                header("refresh:2;url=login.php");
            } else {
                $mensaje = "Error al registrar. Intente nuevamente.";
            }
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
    <title>Registro - Carnicería La Morgue</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
    <script src="assets/js/alerts.js" defer></script>
    <script src="assets/js/validaciones.js" defer></script>
</head>

<body>

    <header>
        <h1>CARNICERÍA LA MORGUE</h1>
        <p>Crear nueva cuenta</p>
    </header>

    <main>
        <section class="formulario">
            <h2>Registrarse</h2>

            <?php if ($mensaje != ""): ?>
                <div class="alerta <?php echo strpos($mensaje, 'exitoso') !== false ? 'alerta-exito' : ''; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="nombre">Nombre completo:</label><br>
                <input type="text" id="nombre" name="nombre" maxlength="100" required><br>

                <label for="email">Correo electrónico:</label><br>
                <input type="email" id="email" name="email" maxlength="100" required><br>

                <label for="password">Contraseña:</label><br>
                <input type="password" id="password" name="password" minlength="4" maxlength="50" required><br>

                <label for="password_confirm">Confirmar contraseña:</label><br>
                <input type="password" id="password_confirm" name="password_confirm" minlength="4" maxlength="50" required><br>

                <button type="submit">Registrarse</button>
            </form>

            <p class="enlace">¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            <p class="enlace"><a href="index.php">← Volver al inicio</a></p>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Carnicería La Morgue. Todos los derechos reservados.</p>
    </footer>

</body>

</html>

