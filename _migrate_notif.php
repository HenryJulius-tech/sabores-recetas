<?php
require_once __DIR__ . '/app/autoload.php';
use App\Core\Database;

$existing = [];
$stmt = Database::query("SHOW TABLES LIKE 'notificaciones'");
$tableExists = $stmt->fetch();

if (!$tableExists) {
    Database::execute("
        CREATE TABLE notificaciones (
            id int(11) NOT NULL AUTO_INCREMENT,
            user_id int(11) NOT NULL,
            type varchar(50) NOT NULL,
            title varchar(200) NOT NULL,
            message text,
            link varchar(255) DEFAULT '',
            read_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY read_at (read_at),
            CONSTRAINT notificaciones_ibfk_1 FOREIGN KEY (user_id) REFERENCES usuarios (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "CREATED: notificaciones table\n";
} else {
    echo "SKIP: notificaciones table already exists\n";
}

echo "\nOK\n";
