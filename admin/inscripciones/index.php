<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

if (isset($_GET['aprobar'])) {
    $id = (int)$_GET['aprobar'];
    
    $info = db_fetchOne("SELECT u.nombre, c.titulo FROM inscripciones i JOIN usuarios u ON i.user_id=u.id JOIN cursos c ON i.curso_id=c.id WHERE i.id=?", [$id]);
    
    db_execute("UPDATE inscripciones SET estado='aprobado' WHERE id=?", [$id]);
    
    if ($info) {
        registrar_log('Aprobación de inscripción', 'Aprobó la inscripción del estudiante ' . $info['nombre'] . ' para el curso: "' . $info['titulo'] . '".');
        
        $user_id = db_fetchOne("SELECT user_id FROM inscripciones WHERE id=?", [$id])['user_id'];
        crearNotificacion('aprobacion', "Tu inscripción al curso '{$info['titulo']}' ha sido aprobada.", 'estudiante', $user_id, 'estudiante/mis-cursos.php');
    }
    
    session_setFlash('success', 'Inscripción aprobada');
    redirect(BASE_URL . 'admin/inscripciones/index.php');
}

if (isset($_GET['rechazar'])) {
    $id = (int)$_GET['rechazar'];
    
    $info = db_fetchOne("SELECT u.nombre, c.titulo FROM inscripciones i JOIN usuarios u ON i.user_id=u.id JOIN cursos c ON i.curso_id=c.id WHERE i.id=?", [$id]);
    
    db_execute("UPDATE inscripciones SET estado='rechazado' WHERE id=?", [$id]);
    
    if ($info) {
        registrar_log('Rechazo de inscripción', 'Rechazó la inscripción del estudiante ' . $info['nombre'] . ' para el curso: "' . $info['titulo'] . '".');
        
        $user_id = db_fetchOne("SELECT user_id FROM inscripciones WHERE id=?", [$id])['user_id'];
        crearNotificacion('aprobacion', "Tu inscripción al curso '{$info['titulo']}' ha sido rechazada.", 'estudiante', $user_id, 'estudiante/cursos.php');
    }
    
    session_setFlash('success', 'Inscripción rechazada');
    redirect(BASE_URL . 'admin/inscripciones/index.php');
}

$inscripciones = db_fetchAll("SELECT i.*, u.nombre as usuario, c.titulo as curso FROM inscripciones i JOIN usuarios u ON i.user_id=u.id JOIN cursos c ON i.curso_id=c.id ORDER BY i.created_at DESC");
$titulo = 'Inscripciones';
include __DIR__ . '/../header.php';
?>
<div class="mb-4">
    <h2 class="fw700" style="color:#1a1a2e;">Inscripciones</h2>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Curso</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inscripciones)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No hay inscripciones registradas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inscripciones as $i): ?>
                            <tr>
                                <td><?= e($i['usuario']) ?></td>
                                <td><?= e($i['curso']) ?></td>
                                <td><?= format_date($i['created_at']) ?></td>
                                <td><?= estadoBadge($i['estado']) ?></td>
                                <td>
                                    <?php if ($i['estado'] === 'pendiente'): ?>
                                        <a href="?aprobar=<?= $i['id'] ?>" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Aprobar</a>
                                        <a href="?rechazar=<?= $i['id'] ?>" class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i> Rechazar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
