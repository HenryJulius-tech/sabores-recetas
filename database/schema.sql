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
  topics text DEFAULT NULL,
  price decimal(12,2) NOT NULL,
  image_url varchar(255) DEFAULT '',
  duration varchar(50) DEFAULT '',
  level varchar(30) DEFAULT 'principiante',
  instructor varchar(100) DEFAULT '',
  featured tinyint(1) DEFAULT 0,
  status varchar(20) DEFAULT 'active',
  period varchar(50) DEFAULT '',
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

CREATE TABLE contactos (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(150) NOT NULL,
  email varchar(150) NOT NULL,
  message text NOT NULL,
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Admin por defecto: admin / admin123
INSERT IGNORE INTO usuarios (username, email, password_hash, role) VALUES
('admin', 'admin@saboresyrecetas.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('cliente', 'cliente@saboresyrecetas.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client');

-- Categorias de cursos
INSERT IGNORE INTO categorias (id, name, description) VALUES
(1, 'Cocina Italiana', 'Pastas, pizzas, risottos y la tradición culinaria italiana'),
(2, 'Cocina de mar', 'Pescados, mariscos y técnicas de cocina marina'),
(3, 'Repostería', 'Pasteles, postres, panadería y chocolate'),
(4, 'Cocina Mexicana', 'Tacos, enchiladas, mole y la esencia de México'),
(5, 'Cocina Asiática', 'Sushi, ramen, wok y sabores orientales'),
(6, 'Cocina Colombiana', 'Bandeja paisa, sancocho, arepas y tradición colombiana'),
(7, 'Cocina Saludable', 'Comida fitness, bowls, ensaladas y alimentación consciente'),
(8, 'Cocina Mediterránea', 'Dieta mediterránea, aceite de oliva y sabores frescos'),
(9, 'Cocina Argentina', 'Carnes, asados, empanadas y vino argentino'),
(10, 'Cocina Francesa', 'Sofisticación francesa, salsas, crepes y repostería fina');

-- Cursos de ejemplo
INSERT IGNORE INTO cursos (id, category_id, title, description, price, duration, level, instructor, featured, status) VALUES

-- Cocina Italiana (cat 1)
(1, 1, 'Pasta Artesanal', 'Aprende a hacer pasta fresca desde cero: tallarines, ravioles, tortellini y ñoquis con salsas clásicas', 150000.00, '8 horas', 'principiante', 'Chef Mario Rossi', 1, 'active'),
(2, 1, 'Pizza Napolitana', 'Domina la masa perfecta y la cocción en horno de leña para pizza napolitana auténtica', 120000.00, '6 horas', 'principiante', 'Chef Mario Rossi', 0, 'active'),
(3, 1, 'Risotto Perfecto', 'Técnicas para lograr un risotto cremoso: risotto al funghi, al limón y con mariscos', 135000.00, '5 horas', 'intermedio', 'Chef Luigi Verdi', 1, 'active'),
(4, 1, 'Salsas Italianas Clásicas', 'Prepara salsa pomodoro, boloñesa, carbonara, alfredo y pesto como un verdadero chef', 110000.00, '4 horas', 'principiante', 'Chef Luigi Verdi', 0, 'active'),
(5, 1, 'Tiramisú y Postres Italianos', 'Postres emblemáticos: tiramisú, panna cotta, cannoli y gelato artesanal', 140000.00, '6 horas', 'intermedio', 'Chef Sofia Bianchi', 0, 'active'),

-- Cocina de mar (cat 2)
(6, 2, 'Cocina de Mar Esencial', 'Limpieza y preparación de pescados, mariscos y técnicas de cocción marina', 180000.00, '10 horas', 'intermedio', 'Chef Brandon Peña', 1, 'active'),
(7, 2, 'Ceviches y Tiraditos', 'Ceviche peruano, tiraditos, aguachiles y marinados frescos con pescados del día', 160000.00, '6 horas', 'principiante', 'Chef Carmen Solano', 1, 'active'),
(8, 2, 'Mariscos al Wok', 'Salteados de camarones, calamares y pescado con verduras y salsas orientales', 145000.00, '5 horas', 'principiante', 'Chef Brandon Peña', 0, 'active'),
(9, 2, 'Parrillada de Mariscos', 'Técnicas de parrilla para langostas, camarones, pulpo y pescados enteros', 200000.00, '8 horas', 'avanzado', 'Chef Carlos Marino', 0, 'active'),
(10, 2, 'Sopas y Guisos de Mar', 'Sopa de pescado, cazuela de mariscos, bouillabaisse y caldo de camarón', 130000.00, '6 horas', 'principiante', 'Chef Carmen Solano', 0, 'active'),

-- Repostería (cat 3)
(11, 3, 'Pastelería Clásica', 'Bizcochos, cremas, merengues y la base de la pastelería tradicional', 170000.00, '10 horas', 'intermedio', 'Chef María Dulce', 1, 'active'),
(12, 3, 'Chocolate Artesanal', 'Temperado, bombones, trufas, ganaches y decoración con chocolate', 190000.00, '8 horas', 'intermedio', 'Chef Pierre Chocolat', 1, 'active'),
(13, 3, 'Panadería Casera', 'Panes artesanales, masa madre, ciabatta, baguette y bollería', 160000.00, '12 horas', 'principiante', 'Chef María Dulce', 0, 'active'),
(14, 3, 'Macarons Franceses', 'El reto de los macarons perfectos: betún, relleno y presentación profesional', 220000.00, '8 horas', 'avanzado', 'Chef Pierre Chocolat', 0, 'active'),
(15, 3, 'Postres Sin Horno', 'Cheesecake, mousses, gelatinas artísticas y postres fríos espectaculares', 120000.00, '5 horas', 'principiante', 'Chef Ana Pastel', 0, 'active'),

-- Cocina Mexicana (cat 4)
(16, 4, 'Tacos Auténticos', 'Tacos al pastor, carnitas, canasta, birria y salsas tradicionales mexicanas', 155000.00, '8 horas', 'principiante', 'Chef Juanita Martínez', 1, 'active'),
(17, 4, 'Mole Poblano y Enchiladas', 'Mole tradicional, enchiladas rojas y verdes, chiles en nogada', 175000.00, '8 horas', 'intermedio', 'Chef Juanita Martínez', 0, 'active'),
(18, 4, 'Cocina Prehispánica', 'Ingredientes ancestrales: nopal, huitlacoche, amaranto, chapulines y maíz nativo', 210000.00, '10 horas', 'avanzado', 'Chef Ernesto Maya', 1, 'active'),
(19, 4, 'Tamales y Pozole', 'Tamales de diferentes regiones, pozole rojo y verde, y sus guarniciones', 150000.00, '7 horas', 'intermedio', 'Chef Ernesto Maya', 0, 'active'),
(20, 4, 'Coctelería Mexicana', 'Margaritas, palomas, micheladas y cócteles con tequila y mezcal', 130000.00, '4 horas', 'principiante', 'Chef Juanita Martínez', 0, 'active'),

-- Cocina Asiática (cat 5)
(21, 5, 'Sushi y Nigiri', 'Preparación de arroz, cortes de pescado, nigiri, maki, rolls y presentación', 200000.00, '10 horas', 'intermedio', 'Chef Hiro Tanaka', 1, 'active'),
(22, 5, 'Ramen Auténtico', 'Caldo tonkotsu, shoyu, miso; noodles caseros, toppings y huevo marinado', 185000.00, '10 horas', 'intermedio', 'Chef Hiro Tanaka', 1, 'active'),
(23, 5, 'Wok y Salteados Asiáticos', 'Técnicas de wok, stir fry, pad thai, lo mein y arroz frito', 140000.00, '6 horas', 'principiante', 'Chef Lin Chang', 0, 'active'),
(24, 5, 'Currys Tailandeses', 'Curry verde, rojo, amarillo, massaman; pasta de curry desde cero', 165000.00, '7 horas', 'intermedio', 'Chef Lin Chang', 0, 'active'),
(25, 5, 'Dim Sum y Dumplings', 'Wontons, gyozas, siu mai, bao buns y salsas para acompañar', 175000.00, '8 horas', 'intermedio', 'Chef Hiro Tanaka', 0, 'active'),

-- Cocina Colombiana (cat 6)
(26, 6, 'Bandeja Paisa y Más', 'Preparación completa de la bandeja paisa: fríjoles, chicharrón, arepa, huevo y carne', 140000.00, '6 horas', 'principiante', 'Chef Carlos Rojas', 1, 'active'),
(27, 6, 'Sancochos Colombianos', 'Sancocho de gallina, de pescado, de carne; guisos y sopas tradicionales', 125000.00, '5 horas', 'principiante', 'Chef Lucía Restrepo', 0, 'active'),
(28, 6, 'Arepas de Colombia', 'Arepas de todo el país: paisa, santandereana, costeña, de chócolo, rellenas', 100000.00, '4 horas', 'principiante', 'Chef Lucía Restrepo', 1, 'active'),
(29, 6, 'Cocina del Pacífico', 'Platos afrocolombianos: encocado de pescado, arroz con coco, tapao, aborrajado', 160000.00, '7 horas', 'intermedio', 'Chef Carlos Rojas', 0, 'active'),
(30, 6, 'Postres Colombianos', 'Postres típicos: natilla, buñuelos, manjar blanco, bocadillo, brevas con arequipe', 120000.00, '5 horas', 'principiante', 'Chef Lucía Restrepo', 0, 'active'),

-- Cocina Saludable (cat 7)
(31, 7, 'Comida Fitness y Meal Prep', 'Planificación de comidas saludables, bowls, proteínas y vegetales para toda la semana', 145000.00, '6 horas', 'principiante', 'Chef Valentina Gómez', 1, 'active'),
(32, 7, 'Ensaladas Creativas', 'Ensaladas completas, aderezos caseros, combinaciones de ingredientes y presentación', 110000.00, '4 horas', 'principiante', 'Chef Valentina Gómez', 0, 'active'),
(33, 7, 'Cocina Vegana y Vegetariana', 'Platos veganos nutritivos, proteína vegetal, fermentos y lácteos vegetales', 170000.00, '8 horas', 'intermedio', 'Chef Diego Verde', 0, 'active'),
(34, 7, 'Snacks Saludables', 'Barras energéticas, chips horneados, hummus, rollitos y opciones saludables para toda hora', 95000.00, '3 horas', 'principiante', 'Chef Valentina Gómez', 0, 'active'),
(35, 7, 'Jugos y Smoothies', 'Green smoothies, jugos detox, leches vegetales, kombucha y bebidas fermentadas', 100000.00, '4 horas', 'principiante', 'Chef Diego Verde', 0, 'active'),

-- Cocina Mediterránea (cat 8)
(36, 8, 'Dieta Mediterránea Esencial', 'Principios de la dieta mediterránea, aceite de oliva, vegetales y pescados', 140000.00, '6 horas', 'principiante', 'Chef Elena Grecia', 1, 'active'),
(37, 8, 'Cocina Griega', 'Moussaka, gyros, tzatziki, spanakopita y ensalada griega tradicional', 150000.00, '7 horas', 'intermedio', 'Chef Elena Grecia', 0, 'active'),
(38, 8, 'Tapas Españolas', 'Tapas clásicas: patatas bravas, tortilla, croquetas, jamón, gambas al ajillo', 160000.00, '6 horas', 'principiante', 'Chef Pablo Torres', 1, 'active'),
(39, 8, 'Paella Valenciana', 'Paella mixta, de mariscos y vegetariana; socarrat y punto perfecto del arroz', 185000.00, '7 horas', 'intermedio', 'Chef Pablo Torres', 0, 'active'),
(40, 8, 'Hummus y Mezze', 'Hummus, baba ghanoush, falafel, tabbouleh y pan pita casero', 120000.00, '4 horas', 'principiante', 'Chef Elena Grecia', 0, 'active'),

-- Cocina Argentina (cat 9)
(41, 9, 'Asado Argentino', 'Cortes de carne, punto de cocción, chimichurri, fuego y técnicas de parrilla argentina', 200000.00, '10 horas', 'intermedio', 'Chef Diego Martínez', 1, 'active'),
(42, 9, 'Empanadas Argentinas', 'Empanadas de carne, pollo, jamón y queso, humita; repulgue y horneado perfecto', 130000.00, '5 horas', 'principiante', 'Chef Diego Martínez', 0, 'active'),
(43, 9, 'Dulce de Leche y Alfajores', 'Dulce de leche casero, alfajores de maicena, de chocolate y regionales', 115000.00, '4 horas', 'principiante', 'Chef Gabriela Paz', 1, 'active'),
(44, 9, 'Cocina Patagónica', 'Cordero al palo, trucha patagónica, hongos silvestres y frutos del bosque', 220000.00, '10 horas', 'avanzado', 'Chef Diego Martínez', 0, 'active'),
(45, 9, 'Vinos y Maridaje Argentino', 'Cata de vinos argentinos, maridaje con carnes, quesos y postres', 175000.00, '5 horas', 'intermedio', 'Chef Gabriela Paz', 0, 'active'),

-- Cocina Francesa (cat 10)
(46, 10, 'Salsas Madre Francesas', 'Las 5 salsas madre: bechamel, velouté, española, tomate y holandesa', 160000.00, '8 horas', 'intermedio', 'Chef Pierre Dubois', 1, 'active'),
(47, 10, 'Crepes y Galettes', 'Crepes dulces y salados, galettes de trigo sarraceno, rellenos y flambeados', 130000.00, '5 horas', 'principiante', 'Chef Pierre Dubois', 0, 'active'),
(48, 10, 'Cocina Francesa Clásica', 'Coq au vin, boeuf bourguignon, ratatouille y gratin dauphinois', 195000.00, '10 horas', 'avanzado', 'Chef Anne Laurent', 1, 'active'),
(49, 10, 'Croissants y Viennoiserie', 'Hojaldre laminado, croissants, pain au chocolat y brioche artesanal', 190000.00, '10 horas', 'avanzado', 'Chef Anne Laurent', 0, 'active'),
(50, 10, 'Quesos y Tabla Francesa', 'Selección, corte y maridaje de quesos franceses; tabla de quesos profesional', 140000.00, '4 horas', 'principiante', 'Chef Pierre Dubois', 0, 'active');
