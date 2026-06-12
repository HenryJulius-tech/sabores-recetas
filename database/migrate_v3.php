<?php
require_once __DIR__ . '/../app/autoload.php';
$config = require __DIR__ . '/../config/database.php';
$pdo = new PDO("mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}", $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$queries = [
    "CREATE TABLE IF NOT EXISTS modulos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        curso_id INT NOT NULL,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        orden INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
    ) ENGINE=InnoDB",

    "CREATE TABLE IF NOT EXISTS clases (
        id INT AUTO_INCREMENT PRIMARY KEY,
        modulo_id INT NOT NULL,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        video_url VARCHAR(500) NOT NULL,
        video_type VARCHAR(20) DEFAULT 'youtube',
        duration VARCHAR(20) DEFAULT '',
        orden INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (modulo_id) REFERENCES modulos(id) ON DELETE CASCADE
    ) ENGINE=InnoDB",

    "CREATE TABLE IF NOT EXISTS clases_completadas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        clase_id INT NOT NULL,
        completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (clase_id) REFERENCES clases(id) ON DELETE CASCADE,
        UNIQUE KEY uq_user_clase (user_id, clase_id)
    ) ENGINE=InnoDB",

    "CREATE TABLE IF NOT EXISTS examenes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        modulo_id INT NOT NULL,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        passing_score INT DEFAULT 70,
        max_attempts INT DEFAULT 3,
        time_limit_min INT DEFAULT 30,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (modulo_id) REFERENCES modulos(id) ON DELETE CASCADE
    ) ENGINE=InnoDB",

    "CREATE TABLE IF NOT EXISTS preguntas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        examen_id INT NOT NULL,
        question TEXT NOT NULL,
        type VARCHAR(20) DEFAULT 'multiple_choice',
        options JSON NOT NULL,
        correct_answer VARCHAR(500) NOT NULL,
        points INT DEFAULT 10,
        orden INT DEFAULT 0,
        FOREIGN KEY (examen_id) REFERENCES examenes(id) ON DELETE CASCADE
    ) ENGINE=InnoDB",

    "CREATE TABLE IF NOT EXISTS intentos_examen (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        examen_id INT NOT NULL,
        score INT DEFAULT 0,
        passed TINYINT(1) DEFAULT 0,
        started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        completed_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (examen_id) REFERENCES examenes(id) ON DELETE CASCADE
    ) ENGINE=InnoDB",

    "CREATE TABLE IF NOT EXISTS respuestas_alumno (
        id INT AUTO_INCREMENT PRIMARY KEY,
        intento_id INT NOT NULL,
        pregunta_id INT NOT NULL,
        answer TEXT,
        is_correct TINYINT(1) DEFAULT 0,
        points_earned INT DEFAULT 0,
        FOREIGN KEY (intento_id) REFERENCES intentos_examen(id) ON DELETE CASCADE,
        FOREIGN KEY (pregunta_id) REFERENCES preguntas(id) ON DELETE CASCADE
    ) ENGINE=InnoDB",
];

foreach ($queries as $sql) {
    try {
        $pdo->exec($sql);
        echo "OK: " . strtok($sql, "\n") . "\n";
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}
echo "Migration v3 complete.\n";
