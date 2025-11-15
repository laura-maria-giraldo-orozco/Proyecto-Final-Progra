CREATE DATABASE carniceria;

USE carniceria;

CREATE TABLE
    usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        rol ENUM ('admin', 'usuario') DEFAULT 'usuario'
    );

CREATE TABLE
    productos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        precio DECIMAL(10, 2) NOT NULL,
        categoria VARCHAR(50),
        stock INT NOT NULL CHECK (stock >= 0),
        imagen VARCHAR(255)
    );

CREATE TABLE
    compras (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
        total DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    );

CREATE TABLE
    detalle_compra (
        id INT AUTO_INCREMENT PRIMARY KEY,
        compra_id INT NOT NULL,
        producto_id INT NOT NULL,
        cantidad INT NOT NULL,
        precio DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (compra_id) REFERENCES compras (id),
        FOREIGN KEY (producto_id) REFERENCES productos (id)
    );