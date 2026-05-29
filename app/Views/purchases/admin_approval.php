<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Compras Pendientes</h1>
</div>
<?php if (empty($compras)): ?>
<div class="card-modern"><div class="empty-state"><i class="bi bi-check2-all"></i><p>No hay compras pendientes</p></div></div>
<?php else: ?>
<?php foreach ($compras as $c):
    $detalles = \App\Core\Database::fetchAll("SELECT d.*, p.name, p.image_url FROM detalle_compras d JOIN productos p ON d.product_id=p.id WHERE d.compra_id=?", [$c['id']]);
    $pago = \App\Core\Database::fetchOne("SELECT * FROM pagos WHERE compra_id=?", [$c['id']]);
?>
<div class="card-modern mb-3 fade-in">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <strong>Compra #<?= $c['id'] ?></strong>
            <span class="text-muted ms-2">por <?= e($c['username']) ?></span>
        </div>
        <span class="text-muted small"><?= format_datetime($c['created_at']) ?></span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-7">
                <table class="table-modern table-sm">
                    <thead><tr><th>Producto</th><th>Cant</th><th>Precio</th><th>Subtotal</th></tr></thead>
                    <tbody>
                        <?php foreach ($detalles as $d): ?>
                        <tr>
                            <td><?= e($d['name']) ?></td>
                            <td><?= $d['quantity'] ?></td>
                            <td><?= format_cop($d['price']) ?></td>
                            <td><?= format_cop($d['quantity'] * $d['price']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot><tr><th colspan="3" class="text-end">Total:</th><th class="text-success"><?= format_cop($c['total']) ?></th></tr></tfoot>
                </table>
            </div>
            <div class="col-md-5">
                <?php if ($pago && !empty($pago['proof_image_url'])): ?>
                <div class="mb-2">
                    <label class="form-label small text-muted">Comprobante:</label>
                    <a href="<?= asset('uploads/' . $pago['proof_image_url']) ?>" target="_blank">
                        <img src="<?= asset('uploads/' . $pago['proof_image_url']) ?>" class="img-fluid rounded" style="max-height:150px">
                    </a>
                </div>
                <?php endif; ?>
                <p class="mb-1 small"><strong>Forma de pago:</strong> <?= e($c['forma_pago'] ?: 'No especificado') ?></p>
                <div class="d-flex gap-2 mt-3">
                    <form method="post" action="<?= url('admin/compras/aprobar/' . $c['id']) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-modern btn-modern-primary btn-modern-sm" onclick="return confirm('¿Aprobar esta compra?')"><i class="bi bi-check-lg me-1"></i>Aprobar</button>
                    </form>
                    <form method="post" action="<?= url('admin/compras/rechazar/' . $c['id']) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-modern btn-modern-danger btn-modern-sm" onclick="return confirm('¿Rechazar esta compra? Se devolverá el stock.')"><i class="bi bi-x-lg me-1"></i>Rechazar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
