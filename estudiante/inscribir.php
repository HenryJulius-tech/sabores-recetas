<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

// Validar rol de estudiante
requireEstudiante();

$userId = session_userId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token CSRF para seguridad
    validate_csrf();
    
    $curso_id = isset($_POST['curso_id']) ? (int)$_POST['curso_id'] : 0;
    
    if ($curso_id <= 0) {
        session_setFlash('error', 'Curso no válido.');
        redirect(BASE_URL . 'estudiante/cursos.php');
    }
    
    // Verificar que el curso exista en la base de datos
    $curso = db_fetchOne("SELECT id, titulo FROM cursos WHERE id=?", [$curso_id]);
    if (!$curso) {
        session_setFlash('error', 'El curso seleccionado no existe.');
        redirect(BASE_URL . 'estudiante/cursos.php');
    }
    
    // Verificar si el estudiante ya está inscrito en el curso
    $existe = db_fetchOne("SELECT id FROM inscripciones WHERE user_id=? AND curso_id=?", [$userId, $curso_id]);
    
    if (!$existe) {
        // Registrar inscripción con estado pendiente
        db_insert("INSERT INTO inscripciones (user_id, curso_id, estado) VALUES (?,?,?)", [$userId, $curso_id, 'pendiente']);
        
        // Registrar acción en la auditoría
        registrar_log('Inscripción a curso', 'Se inscribió en el curso: "' . $curso['titulo'] . '" (ID: ' . $curso_id . ').');
        
        // Disparar notificación para todos los administradores
        $studentName = session_userNombre();
        crearNotificacion(
            'inscripcion',
            "El estudiante {$studentName} se ha inscrito al curso: {$curso['titulo']}.",
            'admin',
            null,
            'admin/dashboard.php'
        );
        
        session_setFlash('success', '¡Inscripción realizada con éxito! Sube el comprobante de pago para activar tu curso.');
        redirect(BASE_URL . 'estudiante/mis-cursos.php');
    } else {
        session_setFlash('error', 'Ya estás inscrito en este curso.');
        redirect(BASE_URL . 'estudiante/cursos.php');
    }
} else {
    // Si no es POST, redirigir al catálogo de cursos
    redirect(BASE_URL . 'estudiante/cursos.php');
}
