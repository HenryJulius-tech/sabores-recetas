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
    db_execute("UPDATE pagos SET estado='aprobado' WHERE id=?", [$id]);
    $pago = db_fetchOne("SELECT inscripcion_id FROM pagos WHERE id=?", [$id]);
    if ($pago) db_execute("UPDATE inscripciones SET estado='aprobado' WHERE id=?", [$pago['inscripcion_id']]);
    
    // Obtener detalles del pago para registrar en auditoría
    $info = db_fetchOne("SELECT u.nombre, c.titulo FROM pagos p JOIN inscripciones i ON p.inscripcion_id=i.id JOIN usuarios u ON i.user_id=u.id JOIN cursos c ON i.curso_id=c.id WHERE p.id=?", [$id]);
    if ($info) {
        registrar_log('Aprobación de pago', 'Aprobó el pago del estudiante ' . $info['nombre'] . ' para el curso: "' . $info['titulo'] . '".');
    }
    
    session_setFlash('success', 'Pago aprobado');
    redirect(BASE_URL . 'admin/pagos/index.php');
}

if (isset($_GET['rechazar'])) {
    $id = (int)$_GET['rechazar'];
    
    // Obtener detalles del pago para registrar en auditoría antes de actualizar
    $info = db_fetchOne("SELECT u.nombre, c.titulo FROM pagos p JOIN inscripciones i ON p.inscripcion_id=i.id JOIN usuarios u ON i.user_id=u.id JOIN cursos c ON i.curso_id=c.id WHERE p.id=?", [$id]);
    
    db_execute("UPDATE pagos SET estado='rechazado' WHERE id=?", [$id]);
    
    if ($info) {
        registrar_log('Rechazo de pago', 'Rechazó el pago del estudiante ' . $info['nombre'] . ' para el curso: "' . $info['titulo'] . '".');
    }
    
    session_setFlash('success', 'Pago rechazado');
    redirect(BASE_URL . 'admin/pagos/index.php');
}

$pagos = db_fetchAll("SELECT p.*, u.nombre as usuario, c.titulo as curso FROM pagos p JOIN inscripciones i ON p.inscripcion_id=i.id JOIN usuarios u ON i.user_id=u.id JOIN cursos c ON i.curso_id=c.id ORDER BY p.created_at DESC");
$titulo = 'Pagos';
include __DIR__ . '/../header.php';
?>
<div class="mb-4">
    <h2 class="fw700" style="color:#1a1a2e;">Pagos</h2>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Usuario</th>
                    <th>Curso</th>
                    <th>Monto</th>
                    <th>Método</th>
                    <th>Comprobante</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $p): ?>
                    <tr>
                        <td><?= e($p['usuario']) ?></td>
                        <td><?= e($p['curso']) ?></td>
                        <td>$<?= number_format($p['monto'], 0) ?></td>
                        <td><?= e($p['metodo']) ?></td>
                        <td>
                            <?php if ($p['comprobante']): ?>
                                <a href="<?= upload_url('pagos/' . $p['comprobante']) ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Ver</a>
                            <?php else: ?>
                                <span class="text-muted">Sin comprobante</span>
                            <?php endif; ?>
                        </td>
                        <td><?= estadoBadge($p['estado']) ?></td>
                        <td>
                            <?php if ($p['estado'] === 'pendiente'): ?>
                                <a href="?aprobar=<?= $p['id'] ?>" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></a>
                                <a href="?rechazar=<?= $p['id'] ?>" class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
