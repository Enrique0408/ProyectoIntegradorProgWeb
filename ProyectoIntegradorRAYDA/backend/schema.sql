-- Esquema para Inventario-RAYDA
-- Ejecutar con setup_database.php o manualmente en MySQL

CREATE TABLE IF NOT EXISTS inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    lugar VARCHAR(255) DEFAULT NULL
    imagen VARCHAR(255) DEFAULT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS registros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT,
    tipo_movimiento VARCHAR(20) NOT NULL,
    cantidad INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES inventario(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de ejemplo
INSERT INTO inventario (nombre, cantidad, lugar) VALUES
('Martillo', 10, 'Bodega A'),
('Taladro', 5, 'Bodega B'),
('Clavos (1kg)', 50, 'Bodega A');

-- Nota: las cuentas de usuario pueden ser creadas mediante el endpoint de login (si la tabla está vacía se siembran 'admin' y 'user').
