<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Mis Compras</h1>
    <a href="<?= url('tienda') ?>" class="btn-modern btn-modern-primary"><i class="bi bi-shop me-1"></i>Seguir Comprando</a>
</div>
<?php if (empty($compras)): ?>
<div class="card-modern"><div class="empty-state"><i class="bi bi-cart"></i><p>No has realizado compras aún</p></div></div>
<?php else: ?>
<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern mb-0">
                <thead><tr><th>#</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Pago</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($compras as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= format_datetime($c['created_at']) ?></td>
                        <td class="fw-bold"><?= format_cop($c['total']) ?></td>
                        <td><?= statusBadge($c['status']) ?></td>
                        <td>
                            <?php if ($c['has_pago'] > 0): ?>
                                <span class="badge bg-info">Comprobante enviado</span>
                            <?php elseif ($c['status'] === 'pending'): ?>
                                <span class="badge-status pending">Pendiente</span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($c['status'] === 'pending'): ?>
                            <button class="btn-modern btn-modern-outline btn-modern-sm" data-bs-toggle="modal" data-bs-target="#pagoModal" onclick="setCompra(<?= $c['id'] ?>)">
                                <i class="bi bi-upload me-1"></i>Subir Pago
                            </button>
                            <?php elseif ($c['status'] === 'approved'): ?>
                            <a href="<?= url('factura/' . $c['id']) ?>" class="btn-modern btn-modern-outline btn-modern-sm"><i class="bi bi-file-pdf me-1"></i>Factura</a>
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
            <form class="form-modern" method="post" action="<?= url('mis-compras/subir-pago') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="compra_id" id="compraId">
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
function setCompra(id) { document.getElementById('compraId').value = id; }
</script>
