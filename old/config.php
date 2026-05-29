<?php
session_start();

// Configuración por defecto para AppServ
$host = 'localhost';
$db   = 'finca_db';
$user = 'root'; 
$pass = '12345678'; // Cámbiala si tu AppServ tiene otra contraseña
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    if ($e->getCode() == 1049) {
        die("<h3>Error: La base de datos 'finca_db' no existe.</h3><p>Por favor, crea la base de datos en phpMyAdmin e importa el archivo <code>database/schema.sql</code>.</p>");
    } elseif ($e->getCode() == 1045) {
        die("<h3>Error de conexión a la Base de Datos.</h3><p>Usuario o contraseña incorrectos. Por favor, edita <code>config.php</code> y asegúrate de que la contraseña ($pass) sea la que usaste al instalar AppServ.</p>");
    }
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Funciones globales útiles
function format_cop($value) {
    return 'COP $' . number_format($value, 2, ',', '.');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_current_user_role() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_role($roles) {
    require_login();
    $current_role = get_current_user_role();
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    if (!in_array($current_role, $roles)) {
        die("Acceso denegado. No tienes permisos para ver esta página.");
    }
}

function get_flash_message($key) {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function set_flash_message($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

// Lógica base para inyectar datos del usuario logueado en las vistas
$current_user = null;
if (is_logged_in()) {
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $current_user = $stmt->fetch();
}
?>
