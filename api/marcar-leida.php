<?php
/**
 * api/marcar-leida.php
 * Marca una notificación como leída (AJAX POST).
 * Body: { id: N } o id=all para marcar todas
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/csrf.php';

header('Content-Type: application/json');

if (!session_isLoggedIn()) {
    echo json_encode(['ok' => false]); exit;
}

$id  = $_POST['id'] ?? null;
$rol = session_userRole();
$uid = session_userId();

if ($id === 'all') {
    if ($rol === 'admin') {
        db_execute("UPDATE notificaciones SET leida=1 WHERE rol_destino='admin' AND leida=0");
    } else {
        db_execute("UPDATE notificaciones SET leida=1 WHERE rol_destino='estudiante' AND user_id=? AND leida=0", [$uid]);
    }
} elseif (is_numeric($id)) {
    db_execute("UPDATE notificaciones SET leida=1 WHERE id=?", [(int)$id]);
}

echo json_encode(['ok' => true]);
