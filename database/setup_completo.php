<?php
/**
 * SETUP COMPLETO — Sabores & Recetas
 * Ejecuta este archivo UNA sola vez desde el navegador:
 *   http://localhost/sabores-recetas/database/setup_completo.php
 * Luego elimínalo o restringe su acceso.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

$log = [];

function run($sql, &$log, $label) {
    try {
        db_execute($sql);
        $log[] = ['ok', "✅ $label"];
    } catch (Exception $e) {
        $log[] = ['err', "❌ $label → " . $e->getMessage()];
    }
}

// ══════════════════════════════════════════════════════════════════════════
//  1. TABLA: usuarios  (base, con columna foto)
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `nombre`     VARCHAR(150) NOT NULL,
  `email`      VARCHAR(120) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('admin','estudiante') DEFAULT 'estudiante',
  `foto`       VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `usuarios`");

// Agregar columna foto si faltara
try {
    $col = db_fetchOne("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='usuarios' AND COLUMN_NAME='foto'");
    if (!$col) {
        db_execute("ALTER TABLE `usuarios` ADD COLUMN `foto` VARCHAR(255) DEFAULT NULL AFTER `role`");
        $log[] = ['ok', '✅ Columna `foto` agregada a `usuarios`'];
    } else {
        $log[] = ['ok', 'ℹ️  Columna `foto` ya existía'];
    }
} catch (Exception $e) {
    $log[] = ['err', '❌ Columna foto → ' . $e->getMessage()];
}

// ══════════════════════════════════════════════════════════════════════════
//  2. TABLA: cursos
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `cursos` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `titulo`      VARCHAR(200) NOT NULL,
  `descripcion` TEXT,
  `precio`      DECIMAL(12,2) NOT NULL DEFAULT 0,
  `imagen`      VARCHAR(255) DEFAULT NULL,
  `nivel`       ENUM('principiante','intermedio','avanzado') DEFAULT 'principiante',
  `duracion`    VARCHAR(50) DEFAULT '',
  `instructor`  VARCHAR(100) DEFAULT '',
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `cursos`");

// ══════════════════════════════════════════════════════════════════════════
//  3. TABLA: modulos
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `modulos` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `curso_id`    INT NOT NULL,
  `titulo`      VARCHAR(200) NOT NULL,
  `descripcion` TEXT,
  `orden`       INT DEFAULT 0,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_mod_curso` (`curso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `modulos`");

// ══════════════════════════════════════════════════════════════════════════
//  4. TABLA: clases
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `clases` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `modulo_id`   INT NOT NULL,
  `titulo`      VARCHAR(200) NOT NULL,
  `descripcion` TEXT,
  `video_url`   VARCHAR(500) DEFAULT '',
  `duracion`    VARCHAR(20)  DEFAULT '',
  `orden`       INT DEFAULT 0,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_clase_modulo` (`modulo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `clases`");

// ══════════════════════════════════════════════════════════════════════════
//  5. TABLA: inscripciones
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `inscripciones` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT NOT NULL,
  `curso_id`   INT NOT NULL,
  `estado`     ENUM('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_insc_user`  (`user_id`),
  KEY `idx_insc_curso` (`curso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `inscripciones`");

// ══════════════════════════════════════════════════════════════════════════
//  6. TABLA: pagos
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `pagos` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `inscripcion_id` INT NOT NULL,
  `monto`          DECIMAL(12,2) NOT NULL DEFAULT 0,
  `metodo`         VARCHAR(50) DEFAULT '',
  `referencia`     VARCHAR(100) DEFAULT '',
  `comprobante`    VARCHAR(255) DEFAULT NULL,
  `estado`         ENUM('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_pago_insc` (`inscripcion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `pagos`");

// ══════════════════════════════════════════════════════════════════════════
//  7. TABLA: notificaciones
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT DEFAULT NULL,
  `rol_destino` ENUM('admin','estudiante') NOT NULL,
  `tipo`        VARCHAR(50) NOT NULL,
  `mensaje`     VARCHAR(255) NOT NULL,
  `url`         VARCHAR(255) DEFAULT NULL,
  `leida`       TINYINT(1) DEFAULT 0,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_notif_user`  (`user_id`),
  KEY `idx_notif_rol`   (`rol_destino`),
  KEY `idx_notif_leida` (`leida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `notificaciones`");

// ══════════════════════════════════════════════════════════════════════════
//  8. TABLA: auditoria
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `auditoria` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id`      INT DEFAULT NULL,
  `nombre_usuario`  VARCHAR(150) NOT NULL DEFAULT 'Sistema',
  `rol`             VARCHAR(50)  NOT NULL DEFAULT 'invitado',
  `accion`          VARCHAR(100) NOT NULL,
  `detalles`        TEXT DEFAULT NULL,
  `direccion_ip`    VARCHAR(45)  NOT NULL DEFAULT '0.0.0.0',
  `fecha_registro`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_aud_usuario` (`usuario_id`),
  KEY `idx_aud_fecha`   (`fecha_registro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `auditoria`");

// ══════════════════════════════════════════════════════════════════════════
//  9. TABLA: progreso_estudiantes  ← CLAVE para ver-curso.php
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `progreso_estudiantes` (
  `id`                  INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id`          INT NOT NULL,
  `curso_id`            INT NOT NULL,
  `progreso_porcentaje` INT DEFAULT 0,
  `estado_curso`        ENUM('Inscrito','En progreso','Aprobado') DEFAULT 'Inscrito',
  `updated_at`          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_prog_user_curso` (`usuario_id`, `curso_id`),
  KEY `idx_prog_user`  (`usuario_id`),
  KEY `idx_prog_curso` (`curso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `progreso_estudiantes`");

// ══════════════════════════════════════════════════════════════════════════
//  10. TABLA: clases_completadas  ← CLAVE para el sistema de progreso
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `clases_completadas` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`       INT NOT NULL,
  `clase_id`      INT NOT NULL,
  `curso_id`      INT NOT NULL,
  `completada_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_cc_user_clase` (`user_id`, `clase_id`),
  KEY `idx_cc_curso_user` (`curso_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `clases_completadas`");

// ══════════════════════════════════════════════════════════════════════════
//  11. TABLA: examenes (preguntas) — estructura de apoyo
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `examenes` (
  `id`        INT AUTO_INCREMENT PRIMARY KEY,
  `curso_id`  INT NOT NULL,
  `pregunta`  TEXT NOT NULL,
  `opcion_a`  VARCHAR(255) NOT NULL,
  `opcion_b`  VARCHAR(255) NOT NULL,
  `opcion_c`  VARCHAR(255) NOT NULL,
  `opcion_d`  VARCHAR(255) NOT NULL,
  `correcta`  ENUM('a','b','c','d') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_exam_curso` (`curso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `examenes`");

// ══════════════════════════════════════════════════════════════════════════
//  12. TABLA: resultados_examenes
// ══════════════════════════════════════════════════════════════════════════
run("CREATE TABLE IF NOT EXISTS `resultados_examenes` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id`     INT NOT NULL,
  `curso_id`       INT NOT NULL,
  `nota_obtenida`  INT NOT NULL DEFAULT 0 COMMENT 'Porcentaje 0-100',
  `estado`         ENUM('Aprobado','Reprobado') NOT NULL,
  `intento`        INT NOT NULL DEFAULT 1,
  `fecha`          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_res_user`  (`usuario_id`),
  KEY `idx_res_curso` (`curso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $log, "Tabla `resultados_examenes`");

// ══════════════════════════════════════════════════════════════════════════
//  13. Usuario administrador por defecto
//      Contraseña: admin123
// ══════════════════════════════════════════════════════════════════════════
run("INSERT INTO `usuarios` (`nombre`, `email`, `password`, `role`)
     VALUES ('Administrador', 'admin@admin.com',
             '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
     ON DUPLICATE KEY UPDATE id=id", $log, "Admin por defecto (admin@admin.com / admin123)");

// ══════════════════════════════════════════════════════════════════════════
//  OUTPUT
// ══════════════════════════════════════════════════════════════════════════
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Setup BD — Sabores & Recetas</title>
<style>
body { font-family: monospace; background:#0f1117; color:#e2e8f0; padding:2rem; }
h1   { color:#f43f5e; margin-bottom:1.5rem; }
.ok  { color:#4ade80; }
.err { color:#f87171; }
.box { background:#1e293b; border-radius:12px; padding:1.5rem; max-width:760px; }
.done{ margin-top:1.5rem; padding:1rem; background:#14532d; border-radius:8px; color:#86efac; font-size:1.1rem; }
a { color:#38bdf8; }
</style>
</head>
<body>
<div class="box">
<h1>🍳 Sabores & Recetas — Setup de Base de Datos</h1>
<?php foreach ($log as [$type, $msg]): ?>
  <div class="<?= $type ?>"><?= htmlspecialchars($msg) ?></div>
<?php endforeach; ?>
<div class="done">
    ✅ Setup completado. <strong>Elimina o renombra este archivo ahora.</strong><br>
    <a href="<?= BASE_URL ?>auth/login.php">→ Ir al Login</a>
</div>
</div>
</body>
</html>
