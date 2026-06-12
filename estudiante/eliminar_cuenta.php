<?php
/**
 * estudiante/eliminar_cuenta.php
 * Procesador para que el estudiante se dé de baja voluntaria.
 * Elimina registros en cascada, destruye sesión y redirige al login.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

requireEstudiante();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_cuenta'])) {
    validate_csrf();
    
    $userId = session_userId();
    
    // Loguear la baja antes de destruir los datos
    registrar_log('Auto-eliminación de cuenta', "El estudiante solicitó la baja del sistema y eliminó su cuenta ID $userId.");
    
    try {
        db_transaction();
        
        // Eliminar en cascada registros asociados
        db_execute("DELETE FROM pagos WHERE inscripcion_id IN (SELECT id FROM inscripciones WHERE user_id = ?)", [$userId]);
        db_execute("DELETE FROM inscripciones WHERE user_id = ?", [$userId]);
        db_execute("DELETE FROM notificaciones WHERE user_id = ?", [$userId]);
        db_execute("DELETE FROM progreso_estudiantes WHERE usuario_id = ?", [$userId]);
        db_execute("DELETE FROM clases_completadas WHERE user_id = ?", [$userId]);
        db_execute("DELETE FROM resultados_examenes WHERE usuario_id = ?", [$userId]);
        
        // Conservar el registro de auditoría, pero quitar el ID para que no apunte a un usuario inexistente
        db_execute("UPDATE auditoria SET usuario_id = NULL WHERE usuario_id = ?", [$userId]);
        
        // Finalmente eliminar el usuario
        db_execute("DELETE FROM usuarios WHERE id = ?", [$userId]);
        
        db_commit();
        
        // Destruir sesión completamente
        session_destroyAll();
        
        // Redirigir al login
        header('Location: ' . BASE_URL . 'auth/login.php?deleted=1');
        exit;
    } catch (Exception $e) {
        db_rollback();
        session_setFlash('error', 'Error al eliminar la cuenta: ' . $e->getMessage());
        redirect(BASE_URL . 'estudiante/perfil.php');
    }
} else {
    // Acceso no permitido por GET
    redirect(BASE_URL . 'estudiante/perfil.php');
}
