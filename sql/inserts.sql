-- ==============================
-- CARNICERÍA LA MORGUE
-- Archivo: sql/inserts.sql
-- Inserts de datos de ejemplo
-- ==============================

USE carniceria;

-- ======== USUARIOS ========
-- Nota: Las contraseñas están hasheadas con password_hash()
-- Admin: contraseña = "admin123"
-- Usuarios: contraseña = "1234"

INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Administrador', 'admin@carniceria.com', '$2y$10$TNAJjDoQbMiCpqu/tiUKReHgW/h3dXtR4HBS2GjnsRewykyl6nKMC', 'admin'),
('Juan Pérez', 'juan.perez@email.com', '$2y$10$MhXdeMDjmS.dijwUnfqqW.w8/PPjSUjSinhWT6cRHjzUwFf3Yz.YW', 'usuario'),
('María García', 'maria.garcia@email.com', '$2y$10$MhXdeMDjmS.dijwUnfqqW.w8/PPjSUjSinhWT6cRHjzUwFf3Yz.YW', 'usuario'),
('Carlos López', 'carlos.lopez@email.com', '$2y$10$MhXdeMDjmS.dijwUnfqqW.w8/PPjSUjSinhWT6cRHjzUwFf3Yz.YW', 'usuario'),
('Ana Martínez', 'ana.martinez@email.com', '$2y$10$MhXdeMDjmS.dijwUnfqqW.w8/PPjSUjSinhWT6cRHjzUwFf3Yz.YW', 'usuario');

-- ======== PRODUCTOS ========
-- Categorías: Res, Cerdo, Pollo, Embutidos, Otros

INSERT INTO productos (nombre, descripcion, precio, categoria, stock, imagen) VALUES
('Filete de Res Premium', 'Filete de res de primera calidad, tierno y jugoso. Ideal para asar o freír. Origen: Ganado nacional.', 15.99, 'Res', 25, NULL),
('Costilla de Res', 'Costilla de res fresca, perfecta para parrilla o estofado. Corte premium con buen marmoleo.', 12.50, 'Res', 18, NULL),
('Carne Molida', 'Carne molida de res 80/20, fresca del día. Ideal para hamburguesas, albóndigas y pastas.', 8.75, 'Res', 30, NULL),
('Bistec de Res', 'Bistec de res cortado fino, tierno y sabroso. Perfecto para freír o saltear.', 11.25, 'Res', 22, NULL),
('Chuleta de Cerdo', 'Chuleta de cerdo gruesa y jugosa, ideal para asar o freír. Origen nacional.', 9.50, 'Cerdo', 28, NULL),
('Costilla de Cerdo', 'Costilla de cerdo tierna, perfecta para barbacoa o parrilla. Con hueso.', 10.75, 'Cerdo', 20, NULL),
('Lomo de Cerdo', 'Lomo de cerdo premium, tierno y sin grasa. Ideal para hornear o asar.', 13.50, 'Cerdo', 15, NULL),
('Carne Molida de Cerdo', 'Carne molida de cerdo fresca, perfecta para albóndigas y empanadas.', 7.99, 'Cerdo', 25, NULL),
('Pechuga de Pollo', 'Pechuga de pollo deshuesada, tierna y jugosa. Ideal para freír, asar o cocinar a la plancha.', 6.50, 'Pollo', 35, NULL),
('Muslo de Pollo', 'Muslo de pollo con hueso, sabroso y económico. Perfecto para estofados y guisos.', 5.25, 'Pollo', 40, NULL),
('Ala de Pollo', 'Alas de pollo frescas, ideales para asar o freír. Perfectas para aperitivos.', 4.99, 'Pollo', 45, NULL),
('Pollo Entero', 'Pollo entero fresco, limpio y listo para cocinar. Peso aproximado: 1.5 - 2 kg.', 8.50, 'Pollo', 12, NULL),
('Chorizo Español', 'Chorizo español artesanal, curado y con especias tradicionales. Listo para consumir.', 14.50, 'Embutidos', 20, NULL),
('Jamón Serrano', 'Jamón serrano curado de primera calidad, cortado fino. Origen español.', 18.99, 'Embutidos', 15, NULL),
('Salchichas', 'Salchichas frescas tipo frankfurt, ideales para perros calientes y asados.', 5.75, 'Embutidos', 30, NULL),
('Morcilla', 'Morcilla tradicional, con arroz y especias. Ideal para cocidos y guisos.', 6.50, 'Embutidos', 18, NULL),
('Longaniza', 'Longaniza fresca, perfecta para asar o freír. Tradicional y sabrosa.', 7.25, 'Embutidos', 22, NULL),
('Hígado de Res', 'Hígado de res fresco, rico en hierro. Ideal para freír o guisar.', 6.99, 'Otros', 10, NULL),
('Riñones de Res', 'Riñones de res frescos, limpios y listos para cocinar. Tradicional en guisos.', 7.50, 'Otros', 8, NULL),
('Lengua de Res', 'Lengua de res tierna, perfecta para cocidos y guisos tradicionales.', 9.75, 'Otros', 6, NULL),
('Mollejas', 'Mollejas de pollo frescas, tiernas y delicadas. Ideal para asar o freír.', 8.25, 'Otros', 12, NULL);

-- ======== COMPRAS DE EJEMPLO ========
-- Nota: Estas compras se relacionan con usuarios existentes
-- Se insertan después de los usuarios y productos

INSERT INTO compras (usuario_id, fecha, total) VALUES
(2, '2024-01-15 10:30:00', 45.73),
(2, '2024-01-20 14:15:00', 28.50),
(3, '2024-01-18 11:00:00', 67.25),
(3, '2024-01-22 16:45:00', 35.99),
(4, '2024-01-19 09:30:00', 52.75),
(4, '2024-01-21 13:20:00', 41.50);

-- ======== DETALLES DE COMPRA ========
-- Detalles de las compras anteriores

INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio) VALUES
-- Compra 1 (Juan Pérez - 45.73€)
(1, 1, 1, 15.99),  -- Filete de Res Premium
(1, 5, 2, 9.50),   -- Chuleta de Cerdo (x2)
(1, 9, 2, 6.50),   -- Pechuga de Pollo (x2)

-- Compra 2 (Juan Pérez - 28.50€)
(2, 3, 2, 8.75),   -- Carne Molida (x2)
(2, 11, 2, 4.99), -- Ala de Pollo (x2)

-- Compra 3 (María García - 67.25€)
(3, 1, 2, 15.99),  -- Filete de Res Premium (x2)
(3, 7, 1, 13.50), -- Lomo de Cerdo
(3, 14, 1, 18.99), -- Jamón Serrano
(3, 18, 1, 7.25), -- Longaniza

-- Compra 4 (María García - 35.99€)
(4, 4, 2, 11.25),  -- Bistec de Res (x2)
(4, 10, 2, 5.25), -- Muslo de Pollo (x2)

-- Compra 5 (Carlos López - 52.75€)
(5, 2, 2, 12.50), -- Costilla de Res (x2)
(5, 6, 1, 10.75), -- Costilla de Cerdo
(5, 13, 1, 14.50), -- Chorizo Español

-- Compra 6 (Carlos López - 41.50€)
(6, 9, 3, 6.50),  -- Pechuga de Pollo (x3)
(6, 15, 2, 5.75), -- Salchichas (x2)
(6, 22, 1, 8.25); -- Mollejas

-- ======== NOTAS ========
-- Contraseñas:
-- - Admin: admin123
-- - Usuarios: 1234
--
-- Para actualizar las contraseñas, puedes usar este script PHP:
-- <?php
-- echo password_hash('tu_contraseña', PASSWORD_DEFAULT);
-- ?>
--
-- Los productos incluyen diversas categorías típicas de una carnicería
-- Las compras son ejemplos para probar el historial y la gestión de compras

