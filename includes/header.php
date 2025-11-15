<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? $titulo . ' - ' : ''; ?>Carnicería La Morgue</title>
    <?php
    // Determinar ruta base según ubicación
    $base_path = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/user/')) ? '../' : '';
    ?>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/estilos.css">
    <?php if (isset($esAdmin) && $esAdmin): ?>
        <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/admin.css">
    <?php endif; ?>
    <link rel="icon" type="image/png" href="<?php echo $base_path; ?>assets/img/logo.png">
    <script src="<?php echo $base_path; ?>assets/js/alerts.js" defer></script>
    <script src="<?php echo $base_path; ?>assets/js/validaciones.js" defer></script>
</head>

<body>

    <header>
        <h1>CARNICERÍA LA MORGUE</h1>
        <p><?php echo isset($subtitulo) ? $subtitulo : 'Calidad, frescura y servicio al alcance de un clic'; ?></p>
    </header>

