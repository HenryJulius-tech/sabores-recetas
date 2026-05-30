-- Sabores & Recetas - Schema Completo
CREATE DATABASE IF NOT EXISTS sabores_recetas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sabores_recetas;

CREATE TABLE usuarios (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(80) NOT NULL,
  email varchar(120) NOT NULL,
  password_hash varchar(255) NOT NULL,
  role varchar(20) DEFAULT 'client',
  address varchar(200) DEFAULT '',
  phone varchar(20) DEFAULT '',
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY username (username),
  UNIQUE KEY email (email)
) ENGINE=InnoDB;

CREATE TABLE categorias (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL,
  description text,
  image_url varchar(255) DEFAULT '',
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE cursos (
  id int(11) NOT NULL AUTO_INCREMENT,
  category_id int(11) NOT NULL,
  title varchar(200) NOT NULL,
  description text,
  price decimal(12,2) NOT NULL,
  image_url varchar(255) DEFAULT '',
  duration varchar(50) DEFAULT '',
  level varchar(30) DEFAULT 'principiante',
  instructor varchar(100) DEFAULT '',
  featured tinyint(1) DEFAULT 0,
  status varchar(20) DEFAULT 'active',
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY category_id (category_id),
  CONSTRAINT cursos_ibfk_1 FOREIGN KEY (category_id) REFERENCES categorias (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE matriculas (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  curso_id int(11) NOT NULL,
  total decimal(12,2) NOT NULL,
  status varchar(20) DEFAULT 'pending',
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY curso_id (curso_id),
  CONSTRAINT matriculas_ibfk_1 FOREIGN KEY (user_id) REFERENCES usuarios (id) ON DELETE CASCADE,
  CONSTRAINT matriculas_ibfk_2 FOREIGN KEY (curso_id) REFERENCES cursos (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pagos (
  id int(11) NOT NULL AUTO_INCREMENT,
  matricula_id int(11) NOT NULL,
  amount decimal(12,2) NOT NULL,
  payment_method varchar(50) DEFAULT '',
  reference varchar(100) DEFAULT '',
  proof_image_url varchar(255) DEFAULT '',
  status varchar(20) DEFAULT 'pending',
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY matricula_id (matricula_id),
  CONSTRAINT pagos_ibfk_1 FOREIGN KEY (matricula_id) REFERENCES matriculas (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE movimientos (
  id int(11) NOT NULL AUTO_INCREMENT,
  matricula_id int(11) DEFAULT NULL,
  type varchar(20) NOT NULL,
  description text,
  category varchar(100) DEFAULT '',
  forma_pago varchar(50) DEFAULT '',
  proveedor_beneficiario varchar(200) DEFAULT '',
  soporte_url varchar(255) DEFAULT '',
  observaciones text,
  amount decimal(12,2) NOT NULL,
  created_by_id int(11) DEFAULT NULL,
  status varchar(20) DEFAULT 'approved',
  date datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY created_by_id (created_by_id),
  CONSTRAINT movimientos_ibfk_1 FOREIGN KEY (created_by_id) REFERENCES usuarios (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Admin por defecto: admin / admin123
INSERT IGNORE INTO usuarios (username, email, password_hash, role) VALUES
('admin', 'admin@saboresyrecetas.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('cliente', 'cliente@saboresyrecetas.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client');

-- Categorias de cursos
INSERT IGNORE INTO categorias (id, name, description) VALUES
(1, 'Cocina Italiana', 'Pastas, pizzas y risottos'),
(2, 'Cocina de mar', 'Pescados y mariscos'),
(3, 'Repostería', 'Pasteles, postres y panadería'),
(4, 'Cocina Mexicana', 'Tacos, enchiladas y más'),
(5, 'Cocina Asiática', 'Sushi, ramen y wok');

-- Cursos de ejemplo
INSERT IGNORE INTO cursos (id, category_id, title, description, price, duration, level, instructor, featured, status) VALUES
(1, 1, 'Pasta Artesanal', 'Aprende a hacer pasta fresca desde cero', 150000.00, '8 horas', 'principiante', 'Chef Mario', 1, 'active'),
(2, 2, 'Curso de cocina de mar', 'Aprende a hacer diferentes platos de mariscos y pescados', 180000.00, '10 horas', 'intermedio', 'Chef Brandon', 1, 'active');
