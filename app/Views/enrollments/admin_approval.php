<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Matrículas Pendientes</h1>
</div>
<?php if (empty($matriculas)): ?>
<div class="card-modern"><div class="empty-state"><i class="bi bi-check2-all"></i><p>No hay matrículas pendientes</p></div></div>
<?php else: ?>
<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern mb-0">
                <thead><tr><th>Usuario</th><th>Curso</th><th>Monto</th><th>Fecha</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($matriculas as $m): ?>
                    <tr>
                        <td class="fw-bold"><?= e($m['username']) ?></td>
                        <td><?= e($m['course_title']) ?></td>
                        <td><?= format_cop($m['amount']) ?></td>
                        <td class="small text-muted"><?= format_datetime($m['created_at']) ?></td>
                        <td>
                            <form method="post" action="<?= url('admin/matriculas/aprobar/' . $m['id']) ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-modern btn-modern-primary btn-modern-sm" onclick="return confirm('¿Aprobar esta matrícula?')"><i class="bi bi-check-lg me-1"></i>Aprobar</button>
                            </form>
                            <form method="post" action="<?= url('admin/matriculas/rechazar/' . $m['id']) ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-modern btn-modern-danger btn-modern-sm" onclick="return confirm('¿Rechazar esta matrícula?')"><i class="bi bi-x-lg me-1"></i>Rechazar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
