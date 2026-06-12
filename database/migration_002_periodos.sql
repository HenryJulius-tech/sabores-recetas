-- Migration: Agregar periodos, temas a cursos y tabla de contactos

CREATE TABLE IF NOT EXISTS contactos (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(150) NOT NULL,
  email varchar(150) NOT NULL,
  message text NOT NULL,
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;
ALTER TABLE cursos
  ADD COLUMN period VARCHAR(50) DEFAULT '' AFTER status,
  ADD COLUMN topics TEXT DEFAULT NULL AFTER description;

-- Asignar periodos
UPDATE cursos SET period = 'Enero - Junio 2025' WHERE id BETWEEN 1 AND 25;
UPDATE cursos SET period = 'Julio - Diciembre 2025' WHERE id BETWEEN 26 AND 50;

-- Temas para cada curso (separados por coma)
UPDATE cursos SET topics = 'Tipos de harina, Amasado manual, Laminado, Corte de pasta, Salsas base, Cocción perfecta' WHERE id = 1;
UPDATE cursos SET topics = 'Masa madre, Fermentación, Estirado manual, Salsa de tomate, Mozzarella fresca, Hornear en leña' WHERE id = 2;
UPDATE cursos SET topics = 'Tipos de arroz, Sofrito base, Técnica del risotto, Risotto al funghi, Risotto al limón, Risotto de mariscos' WHERE id = 3;
UPDATE cursos SET topics = 'Pomodoro clásico, Boloñesa tradicional, Carbonara auténtica, Alfredo cremosa, Pesto genovés' WHERE id = 4;
UPDATE cursos SET topics = 'Mascarpone casero, Café y bizcochos, Panna cotta, Cannoli sicilianos, Gelato artesanal' WHERE id = 5;
UPDATE cursos SET topics = 'Limpieza de pescados, Descamado y fileteado, Cocción al vapor, Sellado perfecto, Mariscos frescos, Fondos de pescado' WHERE id = 6;
UPDATE cursos SET topics = 'Corte de pescado, Marinado cítrico, Ceviche clásico, Tiraditos nikkei, Aguachiles, Leche de tigre' WHERE id = 7;
UPDATE cursos SET topics = 'Preparación del wok, Camarones salteados, Calamares tiernos, Salsa agridulce, Vegetales crujientes, Arroz frito' WHERE id = 8;
UPDATE cursos SET topics = 'Limpieza de langosta, Pulpo tierno, Camarones a la parrilla, Pescado entero, Salsas para mariscos, Puntos de cocción' WHERE id = 9;
UPDATE cursos SET topics = 'Base de sopas, Sopa de pescado tradicional, Cazuela de mariscos, Bouillabaisse, Caldo de camarón, Guarniciones' WHERE id = 10;
UPDATE cursos SET topics = 'Bizcocho básico, Crema pastelera, Merengues, Batidos y emulsiones, Horneado perfecto' WHERE id = 11;
UPDATE cursos SET topics = 'Temperado del chocolate, Bombones rellenos, Trufas clásicas, Ganaches, Decoración profesional' WHERE id = 12;
UPDATE cursos SET topics = 'Masa madre, Amasado y reposo, Ciabatta, Baguette crujiente, Bollos dulces' WHERE id = 13;
UPDATE cursos SET topics = 'Harina de almendra, Macaronage, Betún francés, Rellenos, Horneado y presentación' WHERE id = 14;
UPDATE cursos SET topics = 'Cheesecake neoyorquino, Mousse de chocolate, Gelatinas artísticas, Postres fríos, Decoración' WHERE id = 15;
UPDATE cursos SET topics = 'Marinado de carnes, Tortillas de maíz, Salsa verde, Guacamole, Cebolla encurtida' WHERE id = 16;
UPDATE cursos SET topics = 'Chiles secos, Mole poblano, Enchiladas rojas, Enchiladas verdes, Chiles en nogada' WHERE id = 17;
UPDATE cursos SET topics = 'Nopal asado, Huitlacoche, Amaranto, Chapulines, Maíz nativo, Salsas ancestrales' WHERE id = 18;
UPDATE cursos SET topics = 'Masa para tamales, Tamales oaxaqueños, Tamales verdes, Pozole rojo, Pozole verde, Guarniciones' WHERE id = 19;
UPDATE cursos SET topics = 'Tequila y mezcal, Margarita clásica, Paloma, Michelada, Cócteles de autor' WHERE id = 20;
UPDATE cursos SET topics = 'Arroz para sushi, Cortes de pescado, Nigiri, Maki rolls, Uramaki, Presentación' WHERE id = 21;
UPDATE cursos SET topics = 'Caldo tonkotsu, Caldo shoyu, Caldo miso, Noodles caseros, Huevo marinado, Toppings' WHERE id = 22;
UPDATE cursos SET topics = 'Técnicas de wok, Stir fry, Pad thai, Lo mein, Arroz frito, Salsas asiáticas' WHERE id = 23;
UPDATE cursos SET topics = 'Pasta de curry verde, Curry rojo, Curry amarillo, Curry massaman, Arroz jazmín' WHERE id = 24;
UPDATE cursos SET topics = 'Masa wonton, Gyozas, Siu mai, Bao buns, Salsas dipping' WHERE id = 25;
UPDATE cursos SET topics = 'Fríjoles antioqueños, Chicharrón crujiente, Arepa paisa, Huevo frito, Carne molida, Hogao' WHERE id = 26;
UPDATE cursos SET topics = 'Sancocho de gallina, Sancocho de pescado, Sancocho de carne, Guarniciones, Ají criollo' WHERE id = 27;
UPDATE cursos SET topics = 'Masa de arepa, Arepa paisa, Arepa santandereana, Arepa costeña, Arepa de chócolo, Arepas rellenas' WHERE id = 28;
UPDATE cursos SET topics = 'Coco y leche de coco, Encocado de pescado, Arroz con coco, Tapao, Aborrajado' WHERE id = 29;
UPDATE cursos SET topics = 'Natilla tradicional, Buñuelos, Manjar blanco, Bocadillo, Brevas con arequipe' WHERE id = 30;
UPDATE cursos SET topics = 'Planificación semanal, Bowls nutritivos, Proteínas magras, Vegetales salteados, Aderezos saludables' WHERE id = 31;
UPDATE cursos SET topics = 'Base de ensaladas, Aderezos caseros, Combinaciones creativas, Presentación, Ensaladas completas' WHERE id = 32;
UPDATE cursos SET topics = 'Proteína vegetal, Fermentos, Lácteos vegetales, Platos veganos, Nutrición balanceada' WHERE id = 33;
UPDATE cursos SET topics = 'Barras energéticas, Chips horneados, Hummus, Rollitos, Snacks saludables' WHERE id = 34;
UPDATE cursos SET topics = 'Green smoothies, Jugos detox, Leches vegetales, Kombucha, Bebidas fermentadas' WHERE id = 35;
UPDATE cursos SET topics = 'Aceite de oliva, Vegetales asados, Pescados a la plancha, Hierbas mediterráneas, Plato equilibrado' WHERE id = 36;
UPDATE cursos SET topics = 'Moussaka, Gyros caseros, Tzatziki, Spanakopita, Ensalada griega' WHERE id = 37;
UPDATE cursos SET topics = 'Patatas bravas, Tortilla española, Croquetas, Gambas al ajillo, Jamón ibérico' WHERE id = 38;
UPDATE cursos SET topics = 'Sofrito base, Arroz bomba, Paella mixta, Paella de mariscos, Socarrat, Paella vegetariana' WHERE id = 39;
UPDATE cursos SET topics = 'Hummus clásico, Baba ghanoush, Falafel, Tabbouleh, Pan pita casero' WHERE id = 40;
UPDATE cursos SET topics = 'Cortes de carne, Fuego y brasas, Chimichurri, Punto de cocción, Parrilla argentina' WHERE id = 41;
UPDATE cursos SET topics = 'Masa de empanada, Repulgue, Empanada de carne, Empanada de pollo, Horneado perfecto' WHERE id = 42;
UPDATE cursos SET topics = 'Dulce de leche casero, Alfajores de maicena, Alfajores de chocolate, Alfajores regionales' WHERE id = 43;
UPDATE cursos SET topics = 'Cordero al palo, Trucha patagónica, Hongos silvestres, Frutos del bosque, Hierbas patagónicas' WHERE id = 44;
UPDATE cursos SET topics = 'Cata de vinos, Varietales argentinos, Maridaje con carnes, Maridaje con quesos, Maridaje con postres' WHERE id = 45;
UPDATE cursos SET topics = 'Bechamel, Velouté, Española, Tomate, Holandesa, Usos y variaciones' WHERE id = 46;
UPDATE cursos SET topics = 'Crepes dulces, Crepes salados, Galettes, Rellenos, Flambeado' WHERE id = 47;
UPDATE cursos SET topics = 'Coq au vin, Boeuf bourguignon, Ratatouille, Gratin dauphinois, Técnicas clásicas' WHERE id = 48;
UPDATE cursos SET topics = 'Hojaldre laminado, Croissants, Pain au chocolat, Brioche, Viennoiserie' WHERE id = 49;
UPDATE cursos SET topics = 'Tipos de queso, Corte profesional, Tabla de quesos, Maridaje con vinos, Presentación' WHERE id = 50;
