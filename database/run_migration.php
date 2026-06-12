<?php
// Script de migración temporal - ejecutar UNA sola vez y eliminar
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

$results = [];

// 1. Agregar columna foto a usuarios si no existe
$col = db_fetchOne(
    "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='usuarios' AND COLUMN_NAME='foto'"
);
if (!$col) {
    db_execute("ALTER TABLE `usuarios` ADD COLUMN `foto` VARCHAR(255) DEFAULT NULL AFTER `role`");
    $results[] = "✅ Columna `foto` agregada a `usuarios`";
} else {
    $results[] = "ℹ️ Columna `foto` ya existía en `usuarios`";
}

// 2. Crear tabla notificaciones si no existe
db_execute("CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT DEFAULT NULL COMMENT 'NULL = broadcast a todos los admin',
  `rol_destino` ENUM('admin','estudiante') NOT NULL,
  `tipo`        VARCHAR(50) NOT NULL,
  `mensaje`     VARCHAR(255) NOT NULL,
  `url`         VARCHAR(255) DEFAULT NULL,
  `leida`       TINYINT(1) DEFAULT 0,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_user`  (`user_id`),
  KEY `idx_rol`   (`rol_destino`),
  KEY `idx_leida` (`leida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$results[] = "✅ Tabla `notificaciones` verificada/creada";

// 3. Crear tabla auditoria si no existe
db_execute("CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT DEFAULT NULL COMMENT 'NULL para acciones de invitados o del sistema',
  `nombre_usuario` VARCHAR(150) NOT NULL DEFAULT 'Sistema/Invitado',
  `rol` VARCHAR(50) NOT NULL DEFAULT 'invitado',
  `accion` VARCHAR(100) NOT NULL,
  `detalles` TEXT DEFAULT NULL,
  `direccion_ip` VARCHAR(45) NOT NULL DEFAULT '0.0.0.0',
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_rol` (`rol`),
  KEY `idx_fecha` (`fecha_registro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$results[] = "✅ Tabla `auditoria` verificada/creada";

// 4. Crear tabla progreso_estudiantes si no existe
db_execute("CREATE TABLE IF NOT EXISTS `progreso_estudiantes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `curso_id` INT NOT NULL,
  `progreso_porcentaje` INT DEFAULT 0,
  `estado_curso` ENUM('Inscrito', 'En progreso', 'Aprobado') DEFAULT 'Inscrito',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_pe_user` (`usuario_id`),
  KEY `idx_pe_curso` (`curso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$results[] = "✅ Tabla `progreso_estudiantes` verificada/creada";

// 5. Crear tabla clases_completadas si no existe
db_execute("CREATE TABLE IF NOT EXISTS `clases_completadas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `clase_id` INT NOT NULL,
  `curso_id` INT NOT NULL,
  `completada_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_cc_user_clase` (`user_id`, `clase_id`),
  KEY `idx_cc_curso_user` (`curso_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$results[] = "✅ Tabla `clases_completadas` verificada/creada";

echo '<pre style="font-family:monospace;padding:20px;">';
echo "=== Migración Sabores & Recetas ===\n\n";
foreach ($results as $r) echo $r . "\n";
echo "\n✅ Migración completada. Puedes eliminar este archivo.\n";
echo '</pre>';
