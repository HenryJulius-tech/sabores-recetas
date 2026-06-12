<?php
require_once __DIR__ . '/../app/autoload.php';
use App\Core\Database;
use App\Models\Module;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\Question;

function getVideosByCategory($catName) {
    $videos = [
        'Cocina Italiana' => [
            ['Pasta fresca casera', 'https://www.youtube.com/watch?v=4C9Gr3Qz9Rk', '12:30', 'Aprende a hacer pasta fresca desde cero'],
            ['Salsa Carbonara clásica', 'https://www.youtube.com/watch?v=3AAdKl1UYZs', '15:00', 'La auténtica carbonara italiana'],
            ['Risotto perfecto', 'https://www.youtube.com/watch?v=3bp0wEjgnYk', '18:20', 'Técnica para un risotto cremoso'],
            ['Pizza napolitana', 'https://www.youtube.com/watch?v=1HpE0dbRUdE', '20:15', 'Masa y toppings tradicionales'],
            ['Tiramisú tradicional', 'https://www.youtube.com/watch?v=4IeJ4JZ7s9s', '14:45', 'Postre italiano sin horno'],
            ['Bruschetta clásica', 'https://www.youtube.com/watch?v=6f5j3E0G6FY', '8:30', 'Antipasto italiano fácil'],
            ['Lasagna boloñesa', 'https://www.youtube.com/watch?v=7G5J7qzKg6E', '25:00', 'Capas de pasta con ragú'],
            ['Ossobuco alla Milanese', 'https://www.youtube.com/watch?v=8HxYF0zH1p4', '35:00', 'Estofado de ternera tradicional'],
            ['Gnocchi de papa', 'https://www.youtube.com/watch?v=9JkR5pVq4sY', '16:45', 'Ñoquis caseros'],
            ['Pesto alla Genovese', 'https://www.youtube.com/watch?v=2cF5q5gH6kE', '10:00', 'Salsa de albahaca fresca'],
        ],
        'Cocina Mexicana' => [
            ['Tacos al pastor', 'https://www.youtube.com/watch?v=8qYLMozw7jM', '22:00', 'Tacos con adobo tradicional'],
            ['Guacamole auténtico', 'https://www.youtube.com/watch?v=9V-QgTYM6wc', '10:15', 'Guacamole con receta original'],
            [' Mole poblano', 'https://www.youtube.com/watch?v=3zRg5bYbHdI', '40:00', 'El mole de las fiestas'],
            ['Chiles en nogada', 'https://www.youtube.com/watch?v=7G5J7qzKg6E', '35:00', 'Plato patrio mexicano'],
            ['Tamales caseros', 'https://www.youtube.com/watch?v=4C9Gr3Qz9Rk', '28:30', 'Tamales de masa tradicional'],
            ['Enchiladas suizas', 'https://www.youtube.com/watch?v=1HpE0dbRUdE', '18:45', 'Enchiladas con salsa verde'],
            ['Pozole rojo', 'https://www.youtube.com/watch?v=6f5j3E0G6FY', '45:00', 'Caldo tradicional de maíz'],
            ['Quesadillas de huitlacoche', 'https://www.youtube.com/watch?v=2cF5q5gH6kE', '12:20', 'Quesadillas con relleno prehispánico'],
            ['Ceviche de camarón', 'https://www.youtube.com/watch?v=9JkR5pVq4sY', '15:30', 'Fresco y picante'],
            ['Flan de caramelo', 'https://www.youtube.com/watch?v=5B4zPZ5J6kE', '20:00', 'Postre cremoso de leche'],
        ],
        'Cocina Japonesa' => [
            ['Sushi básico: Maki', 'https://www.youtube.com/watch?v=3AAdKl1UYZs', '25:00', 'Rollos de sushi con arroz'],
            ['Ramen tonkotsu', 'https://www.youtube.com/watch?v=8qYLMozw7jM', '50:00', 'Caldo de cerdo fermentado'],
            ['Tempura crujiente', 'https://www.youtube.com/watch?v=9V-QgTYM6wc', '20:30', 'Técnica de fritura ligera'],
            ['Teriyaki de pollo', 'https://www.youtube.com/watch?v=7G5J7qzKg6E', '15:00', 'Salsa agridulce japonesa'],
            ['Gyoza (empanadillas)', 'https://www.youtube.com/watch?v=4C9Gr3Qz9Rk', '22:15', 'Dumplings japoneses'],
            ['Miso soup tradicional', 'https://www.youtube.com/watch?v=1HpE0dbRUdE', '10:00', 'Sopa de miso con tofu'],
            ['Okonomiyaki', 'https://www.youtube.com/watch?v=6f5j3E0G6FY', '18:00', 'Pizza japonesa salteada'],
            ['Onigiri (bolas de arroz)', 'https://www.youtube.com/watch?v=2cF5q5gH6kE', '12:00', 'Snack japonés portátil'],
            ['Matcha tiramisú', 'https://www.youtube.com/watch?v=9JkR5pVq4sY', '16:30', 'Fusión japonesa-italiana'],
            ['Udon noodle soup', 'https://www.youtube.com/watch?v=5B4zPZ5J6kE', '14:45', 'Fideos gruesos en caldo'],
        ],
        'Cocina Saludable' => [
            ['Bowl de quinoa y vegetales', 'https://www.youtube.com/watch?v=3bp0wEjgnYk', '15:00', 'Bowl nutritivo y colorido'],
            ['Ensalada César light', 'https://www.youtube.com/watch?v=8qYLMozw7jM', '12:30', 'Aderezo bajo en calorías'],
            ['Smoothie verde detox', 'https://www.youtube.com/watch?v=9V-QgTYM6wc', '8:00', 'Bebida verde nutritiva'],
            ['Pollo al horno con hierbas', 'https://www.youtube.com/watch?v=4C9Gr3Qz9Rk', '30:00', 'Pechuga jugosa sin aceite'],
            [' Buddha bowl', 'https://www.youtube.com/watch?v=1HpE0dbRUdE', '18:00', 'Cuenco equilibrado de superfoods'],
            ['Wrap de lechuga', 'https://www.youtube.com/watch?v=6f5j3E0G6FY', '10:00', 'Low carb wrap alternativo'],
            ['Sopa de lentejas', 'https://www.youtube.com/watch?v=2cF5q5gH6kE', '25:00', 'Proteína vegetal reconfortante'],
            ['Salteado de tofu y verduras', 'https://www.youtube.com/watch?v=9JkR5pVq4sY', '15:45', 'Salteado rápido y proteico'],
            ['Hummus de garbanzos', 'https://www.youtube.com/watch?v=5B4zPZ5J6kE', '12:00', 'Dip saludable casero'],
            ['Pescado al vapor con jengibre', 'https://www.youtube.com/watch?v=7G5J7qzKg6E', '20:00', 'Cocción suave y sabrosa'],
        ],
        'Repostería' => [
            ['Pastel de chocolate húmedo', 'https://www.youtube.com/watch?v=3AAdKl1UYZs', '35:00', 'Bizcocho de chocolate intenso'],
            ['Galletas chunky cookies', 'https://www.youtube.com/watch?v=8qYLMozw7jM', '22:00', 'Cookies con trozos de chocolate'],
            ['Cheesecake neoyorquino', 'https://www.youtube.com/watch?v=9V-QgTYM6wc', '45:00', 'Tarta de queso cremosa'],
            ['Macarons franceses', 'https://www.youtube.com/watch?v=4C9Gr3Qz9Rk', '50:00', 'Almendrados franceses delicados'],
            ['Brownies de chocolate', 'https://www.youtube.com/watch?v=1HpE0dbRUdE', '25:00', 'Brownies densos y chocolatosos'],
            ['Panna cotta con frutos rojos', 'https://www.youtube.com/watch?v=6f5j3E0G6FY', '20:00', 'Postre italiano cremoso'],
            ['Cupcakes decorados', 'https://www.youtube.com/watch?v=2cF5q5gH6kE', '30:00', 'Técnica de decoración con manga'],
            ['Hoja de hojaldre casero', 'https://www.youtube.com/watch?v=3bp0wEjgnYk', '40:00', 'Masa laminada desde cero'],
            ['Crème brûlée', 'https://www.youtube.com/watch?v=9JkR5pVq4sY', '28:00', 'Natillas con caramelo crujiente'],
            ['Tarta de manzana', 'https://www.youtube.com/watch?v=5B4zPZ5J6kE', '35:00', 'Apple pie tradicional'],
        ],
        'Cocina Colombiana' => [
            ['Bandeja paisa completa', 'https://www.youtube.com/watch?v=7G5J7qzKg6E', '45:00', 'Plato típico antioqueño'],
            ['Ajiaco santafereño', 'https://www.youtube.com/watch?v=4C9Gr3Qz9Rk', '40:00', 'Sopa bogotana con pollo'],
            ['Arepas de queso', 'https://www.youtube.com/watch?v=8qYLMozw7jM', '15:00', 'Arepas rellenas de queso'],
            ['Sancocho de gallina', 'https://www.youtube.com/watch?v=9V-QgTYM6wc', '55:00', 'Caldo tradicional costeño'],
            ['Empanadas colombianas', 'https://www.youtube.com/watch?v=1HpE0dbRUdE', '25:00', 'Empanadas de carne y papa'],
            ['Lechona tolimense', 'https://www.youtube.com/watch?v=6f5j3E0G6FY', '60:00', 'Cerdo relleno horneado'],
            ['Patacones con hogao', 'https://www.youtube.com/watch?v=2cF5q5gH6kE', '18:00', 'Plátano verde frito con salsa'],
            ['Mazamorra con leche', 'https://www.youtube.com/watch?v=9JkR5pVq4sY', '20:00', 'Postre de maíz tradicional'],
            ['Cazuela de frijoles', 'https://www.youtube.com/watch?v=5B4zPZ5J6kE', '30:00', 'Frijoles cremosos colombianos'],
            ['Tamales tolimenses', 'https://www.youtube.com/watch?v=3bp0wEjgnYk', '50:00', 'Tamales envueltos en hoja'],
        ],
        'Cocina Internacional' => [
            ['Paella valenciana', 'https://www.youtube.com/watch?v=3AAdKl1UYZs', '40:00', 'Arroz con mariscos y pollo'],
            ['Curry tailandés verde', 'https://www.youtube.com/watch?v=8qYLMozw7jM', '25:00', 'Curry picante con leche de coco'],
            ['Falafel con tahini', 'https://www.youtube.com/watch?v=9V-QgTYM6wc', '22:00', 'Albóndigas de garbanzo fritas'],
            ['Coq au vin', 'https://www.youtube.com/watch?v=4C9Gr3Qz9Rk', '50:00', 'Pollo al vino tinto francés'],
            ['Pad Thai', 'https://www.youtube.com/watch?v=1HpE0dbRUdE', '20:00', 'Fideos tailandeses salteados'],
            ['Shepherd s Pie', 'https://www.youtube.com/watch?v=6f5j3E0G6FY', '35:00', 'Pastel de carne con puré británico'],
            ['Tagine de cordero', 'https://www.youtube.com/watch?v=2cF5q5gH6kE', '45:00', 'Estofado marroquí especiado'],
            ['Moussaka griega', 'https://www.youtube.com/watch?v=9JkR5pVq4sY', '40:00', 'Lasaña de berenjena'],
            ['Pho vietnamita', 'https://www.youtube.com/watch?v=5B4zPZ5J6kE', '35:00', 'Sopa de fideos de arroz'],
            ['Schnitzel vienés', 'https://www.youtube.com/watch?v=7G5J7qzKg6E', '20:00', 'Milanesa empanizada alemana'],
        ],
        'Cocina Rápida' => [
            ['Hamburguesa smash', 'https://www.youtube.com/watch?v=3AAdKl1UYZs', '15:00', 'Hamburguesa con queso crujiente'],
            ['Pollo frito crujiente', 'https://www.youtube.com/watch?v=8qYLMozw7jM', '30:00', 'Buttermilk fried chicken'],
            ['Nachos con queso', 'https://www.youtube.com/watch?v=9V-QgTYM6wc', '10:00', 'Totopos con queso fundido'],
            ['Sándwich cubano', 'https://www.youtube.com/watch?v=4C9Gr3Qz9Rk', '12:00', 'Sandwich prensado cubano'],
            ['Mac and cheese', 'https://www.youtube.com/watch?v=1HpE0dbRUdE', '18:00', 'Pasta con queso cremosa'],
            ['Fish and chips', 'https://www.youtube.com/watch?v=6f5j3E0G6FY', '22:00', 'Pescado empanizado con papas'],
            ['Hot dog completo', 'https://www.youtube.com/watch?v=2cF5q5gH6kE', '8:00', 'Perro caliente con toppings'],
            ['Pita gyro', 'https://www.youtube.com/watch?v=9JkR5pVq4sY', '14:00', 'Pan pita relleno de carne'],
            ['Loaded fries', 'https://www.youtube.com/watch?v=5B4zPZ5J6kE', '12:00', 'Papas fritas con coberturas'],
            ['Burrito rápido', 'https://www.youtube.com/watch?v=7G5J7qzKg6E', '16:00', 'Burrito exprés de pollo'],
        ],
        'Cocina Vegetariana' => [
            ['Lasaña de verduras', 'https://www.youtube.com/watch?v=3bp0wEjgnYk', '35:00', 'Lasaña sin carne con espinaca'],
            ['Curry de garbanzos', 'https://www.youtube.com/watch?v=8qYLMozw7jM', '20:00', 'Chana masala vegetariana'],
            ['Tacos de coliflor', 'https://www.youtube.com/watch?v=9V-QgTYM6wc', '18:00', 'Tacos veganos de coliflor'],
            ['Hamburguesa de lentejas', 'https://www.youtube.com/watch?v=4C9Gr3Qz9Rk', '22:00', 'Burger vegetal proteica'],
            ['Pizza vegetariana', 'https://www.youtube.com/watch?v=1HpE0dbRUdE', '20:00', 'Pizza cargada de vegetales'],
            ['Stir fry de tofu', 'https://www.youtube.com/watch?v=6f5j3E0G6FY', '12:00', 'Salteado rápido de tofu'],
            ['Risotto de hongos', 'https://www.youtube.com/watch?v=2cF5q5gH6kE', '25:00', 'Risotto cremoso sin carne'],
            ['Falafel bowl', 'https://www.youtube.com/watch?v=9JkR5pVq4sY', '20:00', 'Bowl mediterráneo vegetal'],
            ['Chili sin carne', 'https://www.youtube.com/watch?v=5B4zPZ5J6kE', '28:00', 'Chili vegetariano especiado'],
            ['Sushi vegano', 'https://www.youtube.com/watch?v=7G5J7qzKg6E', '22:00', 'Rollos de sushi sin pescado'],
        ],
        'Cocina de Temporada' => [
            ['Ensalada de verano', 'https://www.youtube.com/watch?v=3AAdKl1UYZs', '10:00', 'Ensalada fresca con frutas'],
            ['Sopa de calabaza otoñal', 'https://www.youtube.com/watch?v=8qYLMozw7jM', '25:00', 'Crema de calabaza con especias'],
            ['Pavo navideño', 'https://www.youtube.com/watch?v=9V-QgTYM6wc', '90:00', 'Pavo horneado jugoso'],
            ['Cazuela de invierno', 'https://www.youtube.com/watch?v=4C9Gr3Qz9Rk', '35:00', 'Guiso caliente de temporada'],
            ['Ensalada primaveral', 'https://www.youtube.com/watch?v=1HpE0dbRUdE', '12:00', 'Ensalada con vegetales de primavera'],
            ['Ponche navideño', 'https://www.youtube.com/watch?v=6f5j3E0G6FY', '15:00', 'Bebida caliente de frutas'],
            ['Gazpacho andaluz', 'https://www.youtube.com/watch?v=2cF5q5gH6kE', '15:00', 'Sopa fría de tomate veraniega'],
            ['Hallacas venezolanas', 'https://www.youtube.com/watch?v=9JkR5pVq4sY', '60:00', 'Plato navideño envuelto en hoja'],
            ['Pan de muerto', 'https://www.youtube.com/watch?v=5B4zPZ5J6kE', '30:00', 'Pan tradicional de día de muertos'],
            ['Roscón de reyes', 'https://www.youtube.com/watch?v=7G5J7qzKg6E', '35:00', 'Pan dulce de epifanía'],
        ],
    ];
    return $videos[$catName] ?? $videos['Cocina Internacional'];
}

function getExamQuestions($catName, $moduleNum) {
    $questions = [];
    if ($catName === 'Cocina Italiana') {
        if ($moduleNum === 1) {
            $questions = [
                ['¿Cuál es el ingrediente principal de la pasta fresca?', ['Harina de trigo', 'Harina de maíz', 'Harina de arroz', 'Harina de almendra'], 'Harina de trigo'],
                ['¿Qué tipo de queso se usa en la Carbonara tradicional?', ['Parmesano y Pecorino', 'Mozzarella', 'Cheddar', 'Gouda'], 'Parmesano y Pecorino'],
                ['¿Qué arroz se usa para el risotto?', ['Arborio', 'Jazmín', 'Basmati', 'Integral'], 'Arborio'],
                ['¿Cuánto tiempo debe reposar la masa de pizza?', ['24 horas', '1 hora', '5 minutos', '1 semana'], '24 horas'],
                ['¿Qué vino se usa en la lasaña boloñesa?', ['Vino tinto', 'Vino blanco', 'Marsala', 'No lleva vino'], 'Vino tinto'],
                ['¿Cuál es la base del pesto genovés?', ['Albahaca fresca', 'Perejil', 'Cilantro', 'Espinaca'], 'Albahaca fresca'],
                ['¿Cómo se llama la pasta rellena de papa?', ['Gnocchi', 'Ravioli', 'Tortellini', 'Fettuccine'], 'Gnocchi'],
                ['¿Qué corte de carne usa el Ossobuco?', ['Ossobuco de ternera', 'Pechuga de pollo', 'Lomo de cerdo', 'Costilla de res'], 'Ossobuco de ternera'],
            ];
        } else {
            $questions = [
                ['¿Qué tipo de queso lleva el Tiramisú?', ['Mascarpone', 'Ricotta', 'Mozzarella', 'Parmesano'], 'Mascarpone'],
                ['¿Cuántas capas tiene una lasagna clásica?', ['3-4 capas', '1 capa', '7-8 capas', '10 capas'], '3-4 capas'],
                ['¿Qué ingrediente da el color verde al pesto?', ['Albahaca', 'Espinaca', 'Perejil', 'Rúcula'], 'Albahaca'],
                ['¿Cuál es el acompañante clásico del ossobuco?', ['Risotto alla Milanese', 'Papas fritas', 'Ensalada', 'Pan'], 'Risotto alla Milanese'],
                ['¿Qué tipo de vino se usa en el risotto?', ['Vino blanco seco', 'Vino tinto', 'Prosecco', 'No lleva vino'], 'Vino blanco seco'],
                ['¿De qué está hecha la Bruschetta?', ['Pan tostado con tomate', 'Pasta con salsa', 'Arroz con vegetales', 'Pizza con queso'], 'Pan tostado con tomate'],
            ];
        }
    } elseif ($catName === 'Cocina Mexicana') {
        if ($moduleNum === 1) {
            $questions = [
                ['¿Qué carne se usa tradicionalmente en los tacos al pastor?', ['Cerdo', 'Res', 'Pollo', 'Cordero'], 'Cerdo'],
                ['¿Qué ingrediente NO lleva el guacamole auténtico?', ['Crema', 'Aguacate', 'Cilantro', 'Limón'], 'Crema'],
                ['¿Cuántos tipos de chile lleva el mole poblano?', ['Más de 20', '3', '5', '10'], 'Más de 20'],
                ['¿Qué fruta rellena los chiles en nogada?', ['Nuez', 'Fresa', 'Durazno', 'Mango'], 'Nuez'],
                ['¿Cómo se envuelven los tamales?', ['En hojas de maíz', 'En papel aluminio', 'En hojas de plátano', 'En plástico'], 'En hojas de maíz'],
            ];
        } else {
            $questions = [
                ['¿Qué salsa llevan las enchiladas suizas?', ['Salsa verde', 'Salsa roja', 'Salsa de mole', 'Salsa de queso'], 'Salsa verde'],
                ['¿Qué tipo de maíz se usa para el pozole?', ['Maíz cacahuazintle', 'Maíz dulce', 'Maíz reventado', 'Maíz azul'], 'Maíz cacahuazintle'],
                ['¿Qué es el huitlacoche?', ['Un hongo del maíz', 'Una hierba', 'Un tipo de queso', 'Una salsa'], 'Un hongo del maíz'],
                ['¿Cómo se sirve el ceviche?', ['FrÍo con limón', 'Caliente', 'A la parrilla', 'Horneado'], 'FrÍo con limón'],
                ['¿Qué ingrediente principal da textura al flan?', ['Leche condensada', 'Harina', 'Almidón', 'Gelatina'], 'Leche condensada'],
            ];
        }
    } else {
        if ($moduleNum === 1) {
            $questions = [
                ['¿Cuál es el ingrediente principal de esta receta?', ['Vegetales frescos', 'Carne', 'Pescado', 'Granos'], 'Vegetales frescos'],
                ['¿Qué técnica de cocción se recomienda?', ['Salteado', 'Hervido', 'Frito', 'Horneado'], 'Salteado'],
                ['¿Cuánto tiempo aproximado lleva la preparación?', ['15-30 minutos', '1-2 horas', 'MÃ¡s de 3 horas', '5 minutos'], '15-30 minutos'],
                ['¿Qué acompañamiento sugiere el chef?', ['Arroz blanco', 'Papas fritas', 'Ensalada verde', 'Pan tostado'], 'Arroz blanco'],
                ['¿Qué nivel de dificultad tiene esta receta?', ['Principiante', 'Intermedio', 'Avanzado', 'Experto'], 'Principiante'],
            ];
        } else {
            $questions = [
                ['¿Qué variante se puede hacer de este plato?', ['Versión vegana', 'Versión con pollo', 'Versión dulce', 'Todas las anteriores'], 'Todas las anteriores'],
                ['¿Cuál es el error más común al preparar esta receta?', ['Exceso de cocción', 'Poca sal', 'No reposar los ingredientes', 'Usar ingredientes fríos'], 'Exceso de cocción'],
                ['¿Qué utensilio es esencial para esta receta?', ['Sartén antiadherente', 'Olla de presión', 'Batidora', 'Cuchillo de chef'], 'Sartén antiadherente'],
                ['¿Cómo se puede almacenar este plato?', ['En refrigeración hasta 3 días', 'Congelado hasta 1 mes', 'A temperatura ambiente', 'No se puede almacenar'], 'En refrigeración hasta 3 días'],
                ['¿Qué vino marida mejor con este plato?', ['Vino blanco seco', 'Vino tinto robusto', 'Cerveza clara', 'Agua mineral'], 'Vino blanco seco'],
            ];
        }
    }
    return $questions;
}

function createModuleWithContent($cursoId, $moduleTitle, $catName, $moduleNum, $courseOffset) {
    $videos = getVideosByCategory($catName);
    $totalVideos = count($videos);
    $startIdx = ($courseOffset + $moduleNum * 3) % max(1, $totalVideos - 4);

    Module::create([
        'curso_id' => $cursoId,
        'title' => $moduleTitle,
        'description' => 'Módulo ' . $moduleNum . ' de ' . $catName . ': domina las técnicas esenciales.',
        'orden' => $moduleNum,
    ]);
    $modId = Database::getInstance()->getConnection()->lastInsertId();

    $numClasses = min(4, $totalVideos - $startIdx);
    for ($i = 0; $i < $numClasses; $i++) {
        $vi = $videos[($startIdx + $i) % $totalVideos];
        ClassModel::create([
            'modulo_id' => $modId,
            'title' => $vi[0],
            'description' => $vi[3] ?? 'Aprende a preparar ' . $vi[0] . ' paso a paso.',
            'video_url' => $vi[1],
            'duration' => $vi[2],
            'orden' => $i + 1,
        ]);
    }

    $examData = [
        'modulo_id' => $modId,
        'title' => 'Examen: ' . $moduleTitle,
        'description' => 'Demuestra lo que aprendiste en ' . $moduleTitle,
        'passing_score' => 70,
        'max_attempts' => 3,
        'time_limit_min' => 20,
    ];
    Exam::create($examData);
    $examId = Database::getInstance()->getConnection()->lastInsertId();

    $questions = getExamQuestions($catName, $moduleNum);
    foreach ($questions as $qIdx => $q) {
        Question::create([
            'examen_id' => $examId,
            'question' => $q[0],
            'options' => $q[1],
            'correct_answer' => $q[2],
            'points' => $numClasses > 0 ? round(100 / count($questions)) : 10,
            'orden' => $qIdx + 1,
        ]);
    }
}

try {
    $cursos = Database::fetchAll("SELECT c.id, c.title, cat.name as category FROM cursos c JOIN categorias cat ON c.category_id = cat.id ORDER BY c.id");

    Database::getInstance()->getConnection()->exec("SET FOREIGN_KEY_CHECKS = 0");
    Database::getInstance()->getConnection()->exec("TRUNCATE TABLE respuestas_alumno");
    Database::getInstance()->getConnection()->exec("TRUNCATE TABLE intentos_examen");
    Database::getInstance()->getConnection()->exec("TRUNCATE TABLE preguntas");
    Database::getInstance()->getConnection()->exec("TRUNCATE TABLE examenes");
    Database::getInstance()->getConnection()->exec("TRUNCATE TABLE clases_completadas");
    Database::getInstance()->getConnection()->exec("TRUNCATE TABLE clases");
    Database::getInstance()->getConnection()->exec("TRUNCATE TABLE modulos");
    Database::getInstance()->getConnection()->exec("SET FOREIGN_KEY_CHECKS = 1");

    $moduleTemplates = [
        ['Fundamentos de ', 'Introducción, ingredientes y técnicas base.'],
        ['Técnicas Intermedias de ', 'Perfecciona tus habilidades con recetas más complejas.'],
        ['Recetas Avanzadas de ', 'Domina las preparaciones profesionales.'],
    ];

    $courseNum = 0;
    foreach ($cursos as $curso) {
        $courseNum++;
        $catName = $curso['category'];
        $numModules = min(3, max(2, $courseNum % 3 + 2));

        for ($m = 1; $m <= $numModules; $m++) {
            $tpl = $moduleTemplates[($m - 1) % count($moduleTemplates)];
            $moduleTitle = $tpl[0] . $catName;
            createModuleWithContent($curso['id'], $moduleTitle, $catName, $m, $courseNum);
        }
        echo "OK: {$curso['title']} ({$catName}) - {$numModules} módulos\n";
    }

    echo "Seed complete: " . count($cursos) . " cursos con contenido.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
