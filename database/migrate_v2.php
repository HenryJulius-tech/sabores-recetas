<?php
require_once __DIR__ . '/../app/autoload.php';
$config = require __DIR__ . '/../config/database.php';
$pdo = new PDO("mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}", $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$queries = [
    "ALTER TABLE usuarios ADD COLUMN email_notifications TINYINT(1) DEFAULT 1",
    "ALTER TABLE usuarios ADD COLUMN newsletter TINYINT(1) DEFAULT 1",
    "CREATE TABLE IF NOT EXISTS auditoria (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT DEFAULT NULL,
        username VARCHAR(80) DEFAULT '',
        role VARCHAR(20) DEFAULT '',
        action VARCHAR(100) NOT NULL,
        description TEXT,
        ip_address VARCHAR(45) DEFAULT '',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB",
    "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        used_at DATETIME DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY token (token)
    ) ENGINE=InnoDB",
];

foreach ($queries as $sql) {
    try {
        $pdo->exec($sql);
        echo "OK: " . substr($sql, 0, 60) . "...\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "SKIP (already exists): " . substr($sql, 0, 60) . "...\n";
        } else {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }
}
echo "Migration complete.\n";
