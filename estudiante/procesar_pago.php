<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

// Validar que el usuario tiene rol de estudiante
requireEstudiante();

$userId = session_userId();

// Establecer cabecera JSON
header('Content-Type: application/json; charset=UTF-8');

try {
    // Validar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método de solicitud no permitido.');
    }

    // Validar token CSRF para prevenir peticiones maliciosas externas
    validate_csrf();

    $inscripcion_id = isset($_POST['inscripcion_id']) ? (int)$_POST['inscripcion_id'] : 0;
    $metodo = isset($_POST['metodo']) ? trim($_POST['metodo']) : '';

    if ($inscripcion_id <= 0) {
        throw new Exception('ID de inscripción no válido.');
    }

    if (empty($metodo)) {
        throw new Exception('Debe seleccionar el método de pago utilizado.');
    }

    // Verificar que la inscripción exista y pertenezca al estudiante logueado
    $insc = db_fetchOne("SELECT * FROM inscripciones WHERE id=? AND user_id=?", [$inscripcion_id, $userId]);
    if (!$insc) {
        throw new Exception('Inscripción no encontrada o no tienes acceso a ella.');
    }

    // Verificar si ya se ha reportado un pago previo para esta inscripción
    $pagoExistente = db_fetchOne("SELECT id FROM pagos WHERE inscripcion_id=?", [$inscripcion_id]);
    if ($pagoExistente) {
        throw new Exception('Ya has reportado un pago para esta inscripción. Espera la aprobación.');
    }

    // Procesar la subida del comprobante
    $comprobante = '';
    if (isset($_FILES['comprobante']) && !empty($_FILES['comprobante']['name'])) {
        // Permitir imágenes y PDFs
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        $upload = validateUpload($_FILES['comprobante'], $allowedTypes);
        
        if ($upload['valid']) {
            $destDir = ROOT . '/public/uploads/pagos/';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0777, true);
            }
            if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $destDir . $upload['name'])) {
                $comprobante = $upload['name'];
            } else {
                throw new Exception('Error al mover el comprobante subido en el servidor.');
            }
        } else {
            throw new Exception($upload['error'] ?? 'El archivo no es válido.');
        }
    }

    // Obtener datos del curso para saber el monto a pagar
    $curso = db_fetchOne("SELECT titulo, precio FROM cursos WHERE id=?", [$insc['curso_id']]);
    $monto = $curso['precio'] ?? 0;

    // Registrar el pago en la base de datos
    db_insert(
        "INSERT INTO pagos (inscripcion_id, monto, metodo, comprobante, estado) VALUES (?,?,?,?,'pendiente')",
        [$inscripcion_id, $monto, $metodo, $comprobante]
    );

    // Registrar acción en la auditoría
    registrar_log('Reporte de pago', 'Subió comprobante de pago (' . format_cop($monto) . ') vía ' . ucfirst($metodo) . ' para el curso: "' . $curso['titulo'] . '".');

    // Crear notificación para el panel de administración
    $studentName = session_userNombre();
    crearNotificacion(
        'pago',
        "El estudiante {$studentName} reportó el pago para el curso: {$curso['titulo']}.",
        'admin',
        null,
        'admin/dashboard.php'
    );

    // Retornar respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Comprobante enviado exitosamente. Espera la aprobación del administrador.'
    ]);
    exit;

} catch (Exception $e) {
    // Retornar código de error y mensaje
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
