    <?php
    // Determinar ruta base según ubicación
    $base_path = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/user/')) ? '../' : '';
    ?>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Carnicería La Morgue. Todos los derechos reservados.</p>
    </footer>

</body>

</html>

