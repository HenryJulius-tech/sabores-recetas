<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title"><?= e($title ?? 'Matrículas Pendientes') ?></h1>
    <div class="btn-group">
        <a href="<?= url('admin/enrollments') ?>" class="btn-modern btn-modern-sm <?= empty($showAll) ? 'btn-modern-primary' : 'btn-modern-outline' ?>">Pendientes</a>
        <a href="<?= url('admin/enrollments/todas') ?>" class="btn-modern btn-modern-sm <?= !empty($showAll) ? 'btn-modern-primary' : 'btn-modern-outline' ?>">Todas</a>
    </div>
</div>
<?php if (empty($matriculas)): ?>
<div class="card-modern"><div class="empty-state"><i class="bi bi-check2-all"></i><p>No hay matrículas</p></div></div>
<?php else: ?>
<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern mb-0">
                <thead><tr><th>Usuario</th><th>Curso</th><th>Monto</th><th>Pago</th><th>Estado</th><th>Fecha</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($matriculas as $m): ?>
                    <?php
                    $statusClass = 'inactive';
                    $statusLabel = 'Pendiente';
                    if ($m['status'] === 'approved') { $statusClass = 'active'; $statusLabel = 'Activa'; }
                    elseif ($m['status'] === 'rejected') { $statusClass = 'inactive'; $statusLabel = 'Rechazada'; }
                    elseif ($m['status'] === 'cancelled') { $statusClass = 'inactive'; $statusLabel = 'Cancelada'; }
                    ?>
                    <tr>
                        <td class="fw-bold"><?= e($m['username']) ?></td>
                        <td><?= e($m['course_title']) ?></td>
                        <td><?= format_cop($m['amount']) ?></td>
                        <td>
                            <?php if (empty($m['proof_image_url'])): ?>
                                <span class="badge-status inactive">Sin comprobante</span>
                            <?php elseif ($m['pago_status'] === 'approved'): ?>
                                <span class="badge-status active">Pagado</span>
                            <?php else: ?>
                                <a href="<?= upload_url('payments', $m['proof_image_url']) ?>" target="_blank" class="badge-status pending text-decoration-none">Ver comprobante</a>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge-status <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                        <td class="small text-muted"><?= format_datetime($m['created_at']) ?></td>
                        <td>
                            <?php if ($m['status'] === 'pending'): ?>
                            <form method="post" action="<?= url('admin/enrollments/aprobar/' . $m['id']) ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-modern btn-modern-primary btn-modern-sm" onclick="return confirm('¿Aprobar esta matrícula?')"><i class="bi bi-check-lg me-1"></i>Aprobar</button>
                            </form>
                            <form method="post" action="<?= url('admin/enrollments/rechazar/' . $m['id']) ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-modern btn-modern-danger btn-modern-sm" onclick="return confirm('¿Rechazar esta matrícula?')"><i class="bi bi-x-lg me-1"></i>Rechazar</button>
                            </form>
                            <?php elseif ($m['status'] === 'approved'): ?>
                            <form method="post" action="<?= url('admin/enrollments/cancelar/' . $m['id']) ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-modern btn-modern-danger btn-modern-sm" onclick="return confirm('¿Cancelar esta matrícula? Se eliminará el progreso del alumno.')"><i class="bi bi-x-lg me-1"></i>Cancelar</button>
                            </form>
                            <?php else: ?>
                            <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
