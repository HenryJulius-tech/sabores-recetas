-- Finca Bananera v2 - Schema Completo
CREATE DATABASE IF NOT EXISTS finca_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE finca_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'client',
    address VARCHAR(200),
    phone VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    description TEXT,
    image_url VARCHAR(200),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    forma_pago VARCHAR(50) DEFAULT '',
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS detalle_compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    proof_image_url VARCHAR(200) DEFAULT '',
    forma_pago VARCHAR(50) DEFAULT '',
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NULL,
    type VARCHAR(10) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    forma_pago VARCHAR(50) DEFAULT '',
    soporte_url VARCHAR(200) DEFAULT '',
    observaciones TEXT,
    status ENUM('pending','approved','rejected') DEFAULT 'approved',
    description VARCHAR(200) NOT NULL,
    category VARCHAR(50) DEFAULT '',
    proveedor_beneficiario VARCHAR(100) DEFAULT '',
    date DATE NOT NULL,
    created_by_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Admin por defecto (password: admin123)
INSERT IGNORE INTO usuarios (username, email, password_hash, role) VALUES
('admin', 'admin@fincalakaren.com', '$2y$10$wT6qL/A19S99c1zWlqjLVu.P3BvS/G.R.M9c.Z2V8G6vF1G9k2D2i', 'admin');
