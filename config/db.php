<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        putenv(trim($line));
    }
}

$host   = getenv('DB_HOST') ?: 'localhost';
$port   = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'sabores_recetas';
$user   = getenv('DB_USER') ?: 'root';
$pass   = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}

function db_query($sql, $params = []) { global $pdo; $s = $pdo->prepare($sql); $s->execute($params); return $s; }
function db_fetchAll($sql, $params = []) { return db_query($sql, $params)->fetchAll(); }
function db_fetchOne($sql, $params = []) { return db_query($sql, $params)->fetch(); }
function db_insert($sql, $params = []) { global $pdo; db_query($sql, $params); return $pdo->lastInsertId(); }
function db_execute($sql, $params = []) { return db_query($sql, $params)->rowCount(); }
function db_transaction() { global $pdo; $pdo->beginTransaction(); }
function db_commit() { global $pdo; $pdo->commit(); }
function db_rollback() { global $pdo; if ($pdo->inTransaction()) $pdo->rollBack(); }
