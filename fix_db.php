<?php
// fix_db.php - Script temporal para actualizar la base de datos en Hostinger
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';

try {
    echo "Iniciando actualización de la base de datos...<br><br>";
    
    // Eliminamos la tabla notificaciones antigua que tiene la estructura incorrecta (columnas en inglés)
    db_execute("DROP TABLE IF EXISTS `notificaciones`");
    echo "✅ Tabla 'notificaciones' antigua eliminada con éxito.<br>";
    
    // Llamamos al archivo de migración que recreará la tabla con las nuevas columnas (mensaje, rol_destino, etc.)
    require_once __DIR__ . '/database/run_migration.php';
    
    echo "<br><b>¡Todo listo! Ya puedes probar la plataforma.</b>";
} catch (Exception $e) {
    echo "<br>❌ Error: " . $e->getMessage();
}
