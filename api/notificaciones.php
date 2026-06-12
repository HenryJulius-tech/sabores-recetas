<?php
/**
 * api/notificaciones.php
 * Retorna el conteo y lista de notificaciones no leídas del usuario actual.
 * Uso: GET /api/notificaciones.php
 * Responde JSON: { count: N, items: [...] }
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

header('Content-Type: application/json');

if (!session_isLoggedIn()) {
    echo json_encode(['count' => 0, 'items' => []]);
    exit;
}

$rol  = session_userRole();
$uid  = session_userId();

if ($rol === 'admin') {
    // Admin ve notificaciones broadcast (user_id=NULL) y las dirigidas a él
    $items = db_fetchAll(
        "SELECT id, tipo, mensaje, url, created_at FROM notificaciones
         WHERE rol_destino='admin' AND leida=0
         ORDER BY created_at DESC LIMIT 15"
    );
} else {
    // Estudiante ve solo las suyas
    $items = db_fetchAll(
        "SELECT id, tipo, mensaje, url, created_at FROM notificaciones
         WHERE rol_destino='estudiante' AND user_id=? AND leida=0
         ORDER BY created_at DESC LIMIT 15",
        [$uid]
    );
}

// Formatear tiempo relativo
foreach ($items as &$n) {
    $diff = time() - strtotime($n['created_at']);
    if ($diff < 60)        $n['tiempo'] = 'Hace un momento';
    elseif ($diff < 3600)  $n['tiempo'] = 'Hace ' . floor($diff/60) . ' min';
    elseif ($diff < 86400) $n['tiempo'] = 'Hace ' . floor($diff/3600) . ' h';
    else                   $n['tiempo'] = date('d/m/Y', strtotime($n['created_at']));
}

echo json_encode(['count' => count($items), 'items' => $items]);
