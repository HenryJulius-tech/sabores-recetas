<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
function asset_url($path) {
    return BASE_URL . 'public/assets/' . ltrim($path, '/');
}

function asset($path) { return asset_url($path); }

function upload_url($path) {
    if (empty(trim($path ?? '', '/'))) return asset_url('img/default-course.jpg');
    $file = ROOT . '/public/uploads/' . ltrim($path, '/');
    if (!file_exists($file) || is_dir($file)) {
        return asset_url('img/default-course.jpg');
    }
    return BASE_URL . 'public/uploads/' . ltrim($path, '/');
}

function upload($path) { return upload_url($path); }

function upload_path($subdir, $file) {
    if (!$file) return '';
    return ROOT . '/public/uploads/' . $subdir . '/' . $file;
}

function e($v) {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function redirectBack() {
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL));
    exit;
}

function format_cop($a) {
    return '$' . number_format((float)$a, 0, ',', '.');
}

function format_date($d) {
    return $d ? date('d/m/Y', strtotime($d)) : '';
}

function old($k, $d = '') {
    return $_POST[$k] ?? $d;
}

function selected($v, $c) {
    return $v == $c ? 'selected' : '';
}

function truncate($t, $l = 100) {
    if (mb_strlen($t) <= $l) return $t;
    return mb_substr($t, 0, $l) . '...';
}

function validateUpload($file, $allowed = ['image/jpeg','image/png','image/gif','image/webp'], $maxSize = 2097152) {
    if ($file['error'] !== UPLOAD_ERR_OK) return ['valid' => false, 'error' => 'Error al subir el archivo'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $extMap = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp'];
    if (!isset($extMap[$ext]) || !in_array($extMap[$ext], $allowed)) return ['valid' => false, 'error' => 'Tipo de archivo no permitido'];
    if ($file['size'] > $maxSize) return ['valid' => false, 'error' => 'El archivo excede el tamaño máximo (2MB)'];
    $name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    return ['valid' => true, 'name' => $name];
}

function nivelBadge($nivel) {
    $map = ['principiante' => 'badge bg-success', 'intermedio' => 'badge bg-warning text-dark', 'avanzado' => 'badge bg-danger'];
    return '<span class="' . ($map[$nivel] ?? 'badge bg-secondary') . '">' . ucfirst($nivel) . '</span>';
}

function estadoBadge($estado) {
    $map = ['pendiente' => 'badge bg-warning text-dark', 'aprobado' => 'badge bg-success', 'rechazado' => 'badge bg-danger'];
    return '<span class="' . ($map[$estado] ?? 'badge bg-secondary') . '">' . ucfirst($estado) . '</span>';
}

// ─── Sistema de Notificaciones ───────────────────────────────────
/**
 * Crea una notificación en la BD.
 * @param string $tipo        'inscripcion'|'pago'|'aprobacion'
 * @param string $mensaje     Texto corto visible al usuario
 * @param string $rol_destino 'admin' (broadcast) o 'estudiante' (user_id específico)
 * @param int|null $user_id   NULL para broadcast admin, ID del estudiante si aplica
 * @param string|null $url    URL relativa a la que apunta la notificación
 */
function crearNotificacion($tipo, $mensaje, $rol_destino, $user_id = null, $url = null) {
    db_insert(
        "INSERT INTO notificaciones (user_id, rol_destino, tipo, mensaje, url) VALUES (?,?,?,?,?)",
        [$user_id, $rol_destino, $tipo, $mensaje, $url]
    );
}

/**
 * Retorna la URL del avatar del usuario.
 * Usa la foto subida si existe, o genera un avatar con UI-Avatars como fallback.
 */
function avatar_url($user, $size = 40) {
    if (!empty($user['foto'])) {
        $file = ROOT . '/public/uploads/avatars/' . $user['foto'];
        if (file_exists($file)) {
            return BASE_URL . 'public/uploads/avatars/' . $user['foto'];
        }
    }
    $bg = ($user['role'] ?? 'estudiante') === 'admin' ? '1E293B' : 'F43F5E';
    return 'https://ui-avatars.com/api/?name=' . urlencode($user['nombre'] ?? 'U') . '&background=' . $bg . '&color=fff&size=' . $size;
}

/**
 * Registra una acción relevante en el sistema dentro del log de auditoría.
 * Captura automáticamente el usuario de la sesión y la IP de origen.
 * 
 * @param string $accion    Nombre corto de la acción realizada.
 * @param string $detalles  Detalles descriptivos opcionales del log.
 */
function registrar_log($accion, $detalles = '') {
    // Capturar la IP del usuario de manera segura
    $ip = '0.0.0.0';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipParts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ipParts[0]);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Obtener información de usuario de la sesión actual
    $usuario_id = null;
    $nombre = 'Sistema/Invitado';
    $rol = 'invitado';

    if (function_exists('session_userId') && session_isLoggedIn()) {
        $usuario_id = session_userId();
        $nombre = session_userNombre() ?: (session_userEmail() ?: 'Desconocido');
        $rol = session_userRole() ?: 'estudiante';
    }

    // Asegurar que db.php esté cargado si se llama antes en el ciclo de vida de la página
    if (!function_exists('db_insert')) {
        require_once __DIR__ . '/../config/db.php';
    }

    // Insertar el registro en la base de datos
    db_insert(
        "INSERT INTO `auditoria` (`usuario_id`, `nombre_usuario`, `rol`, `accion`, `detalles`, `direccion_ip`) VALUES (?, ?, ?, ?, ?, ?)",
        [$usuario_id, $nombre, $rol, $accion, $detalles, $ip]
    );
}
