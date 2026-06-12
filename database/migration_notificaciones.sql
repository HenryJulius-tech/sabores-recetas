-- ================================================================
-- Migración: Sistema de Notificaciones + Foto de Perfil
-- Compatible con MySQL 5.x (AppServ)
-- ================================================================

-- Crear tabla de notificaciones
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT DEFAULT NULL COMMENT 'NULL = broadcast a todos los admin',
  `rol_destino` ENUM('admin','estudiante') NOT NULL,
  `tipo`        VARCHAR(50) NOT NULL COMMENT 'inscripcion|pago|aprobacion',
  `mensaje`     VARCHAR(255) NOT NULL,
  `url`         VARCHAR(255) DEFAULT NULL,
  `leida`       TINYINT(1) DEFAULT 0,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_user`  (`user_id`),
  KEY `idx_rol`   (`rol_destino`),
  KEY `idx_leida` (`leida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
