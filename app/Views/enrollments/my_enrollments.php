<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Mis Matrículas</h1>
    <a href="<?= url('catalogo') ?>" class="btn-modern btn-modern-primary"><i class="bi bi-book me-1"></i>Ver Cursos</a>
</div>
<?php if (empty($matriculas)): ?>
<div class="card-modern"><div class="empty-state"><i class="bi bi-mortarboard"></i><p>No tienes matrículas activas</p></div></div>
<?php else: ?>
<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern mb-0">
                <thead><tr><th>Curso</th><th>Instructor</th><th>Nivel</th><th>Duración</th><th>Precio</th><th>Estado</th><th>Pago</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($matriculas as $m): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if (!empty($m['image_url'])): ?>
                                    <img src="<?= upload_url('courses', $m['image_url']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                                <?php endif; ?>
                                <span class="fw-bold"><?= e($m['course_title']) ?></span>
                            </div>
                        </td>
                        <td><?= e($m['instructor']) ?></td>
                        <td><span class="badge-status <?= $m['level'] ?>"><?= e($m['level']) ?></span></td>
                        <td><?= $m['duration'] ?>h</td>
                        <td class="fw-bold"><?= format_cop($m['price']) ?></td>
                        <td><?= statusBadge($m['status']) ?></td>
                        <td>
                            <?php if (!empty($m['has_pago'])): ?>
                                <span class="badge bg-info">Comprobante enviado</span>
                            <?php elseif ($m['status'] === 'pending'): ?>
                                <span class="badge-status pending">Pendiente</span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($m['status'] === 'pending'): ?>
                            <button class="btn-modern btn-modern-outline btn-modern-sm" data-bs-toggle="modal" data-bs-target="#pagoModal" onclick="setMatricula(<?= $m['id'] ?>)">
                                <i class="bi bi-upload me-1"></i>Subir Pago
                            </button>
                            <?php elseif ($m['status'] === 'approved'): ?>
                            <a href="<?= url('factura/' . $m['id'] . '/html') ?>" class="btn-modern btn-modern-outline btn-modern-sm"><i class="bi bi-file-text me-1"></i>Factura</a>
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

<div class="modal fade" id="pagoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-modern" method="post" action="<?= url('mis-matriculas/subir-pago') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="matricula_id" id="matriculaId">
                <div class="modal-header">
                    <h5 class="modal-title">Subir Comprobante de Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Método de pago</label>
                        <select name="forma_pago" class="form-select" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comprobante (imagen)</label>
                        <input type="file" name="proof" class="form-control" accept="image/*" required data-preview="proofPreview">
                        <div class="mt-2"><img id="proofPreview" class="image-preview" style="display:none"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-modern btn-modern-primary">Enviar</button>
                    <button type="button" class="btn-modern btn-modern-outline" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function setMatricula(id) { document.getElementById('matriculaId').value = id; }
</script>
