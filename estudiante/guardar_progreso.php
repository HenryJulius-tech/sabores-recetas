<?php
/**
 * estudiante/guardar_progreso.php
 * Endpoint AJAX (Fetch) para registrar que una lección fue completada.
 * Retorna JSON con el nuevo porcentaje y la URL de la siguiente clase.
 */
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php'; 

if (!session_isLoggedIn() || session_userRole() !== 'estudiante') {
    echo json_encode(['success' => false, 'error' => 'No estás autorizado o tu sesión ha caducado.']);
    exit;
}

$userId = session_userId();
$curso_id = (int)($_POST['curso_id'] ?? 0);
$clase_id = (int)($_POST['leccion_id'] ?? 0);

if ($curso_id <= 0 || $clase_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos.']);
    exit;
}

try {
    // 1. Guardar o ignorar si ya existe
    $ya_completada = db_fetchOne("SELECT id FROM clases_completadas WHERE user_id=? AND clase_id=?", [$userId, $clase_id]);
    if (!$ya_completada) {
        db_execute("INSERT IGNORE INTO clases_completadas (user_id, clase_id, curso_id) VALUES (?,?,?)", [$userId, $clase_id, $curso_id]);
    }

    // 2. Recalcular progreso actual
    $total = db_fetchOne(
        "SELECT COUNT(cl.id) as c FROM clases cl JOIN modulos m ON cl.modulo_id=m.id WHERE m.curso_id=?", 
        [$curso_id]
    )['c'] ?? 0;

    $completadas = db_fetchOne(
        "SELECT COUNT(*) as c FROM clases_completadas WHERE user_id=? AND curso_id=?", 
        [$userId, $curso_id]
    )['c'] ?? 0;

    $porcentaje = ($total > 0) ? (int)round(($completadas / $total) * 100) : 0;
    
    // 3. Determinar estado e insertar o actualizar en progreso_estudiantes
    $estado = 'Inscrito';
    if ($porcentaje > 0 && $porcentaje < 100) $estado = 'En progreso';
    if ($porcentaje === 100) $estado = 'En progreso'; // Habilita examen, el estado Aprobado se da tras el examen

    $actual = db_fetchOne("SELECT estado_curso FROM progreso_estudiantes WHERE usuario_id=? AND curso_id=?", [$userId, $curso_id]);
    
    if ($actual && $actual['estado_curso'] === 'Aprobado') {
        db_execute("UPDATE progreso_estudiantes SET progreso_porcentaje=? WHERE usuario_id=? AND curso_id=?", [$porcentaje, $userId, $curso_id]);
    } else {
        $existe = db_fetchOne("SELECT id FROM progreso_estudiantes WHERE usuario_id=? AND curso_id=?", [$userId, $curso_id]);
        if ($existe) {
            db_execute("UPDATE progreso_estudiantes SET progreso_porcentaje=?, estado_curso=? WHERE usuario_id=? AND curso_id=?", [$porcentaje, $estado, $userId, $curso_id]);
        } else {
            db_execute("INSERT INTO progreso_estudiantes (usuario_id, curso_id, progreso_porcentaje, estado_curso) VALUES (?,?,?,?)", [$userId, $curso_id, $porcentaje, $estado]);
        }
    }

    // 4. Determinar la siguiente clase para redirigir
    $todasClases = db_fetchAll(
        "SELECT cl.id FROM clases cl JOIN modulos m ON cl.modulo_id=m.id WHERE m.curso_id=? ORDER BY m.orden, cl.orden", 
        [$curso_id]
    );
    $claseIds = array_column($todasClases, 'id');
    $currentPos = array_search($clase_id, $claseIds);
    $nextClase = ($currentPos !== false && $currentPos < count($claseIds) - 1) ? $claseIds[$currentPos + 1] : null;

    $siguiente_url = null;
    if ($nextClase) {
        $siguiente_url = BASE_URL . "estudiante/ver-clase.php?id=$curso_id&clase=$nextClase";
    }

    // 5. Devolver JSON estructurado
    echo json_encode([
        'success' => true,
        'nuevo_porcentaje' => $porcentaje,
        'siguiente_url' => $siguiente_url
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'SQL Error: ' . $e->getMessage()]);
    exit;
}
