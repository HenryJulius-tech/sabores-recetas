<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Movimientos Financieros</h1>
    <div>
        <a href="<?= url('movimientos/exportar') ?>?<?= $_SERVER['QUERY_STRING'] ?>" class="btn-modern btn-modern-outline btn-modern-sm me-2"><i class="bi bi-download me-1"></i>Exportar CSV</a>
        <button class="btn-modern btn-modern-primary btn-modern-sm" data-bs-toggle="modal" data-bs-target="#movModal"><i class="bi bi-plus-lg me-1"></i>Nuevo Movimiento</button>
    </div>
</div>
<div class="card-modern mb-4">
    <div class="card-body filter-bar">
        <form method="get" class="form-modern row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Buscar</label>
                <input type="text" name="search" class="form-control form-control-sm" value="<?= e($_GET['search'] ?? '') ?>" placeholder="Descripción, proveedor...">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Tipo</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="ingreso" <?= selected($_GET['type']??'', 'ingreso') ?>>Ingreso</option>
                    <option value="gasto" <?= selected($_GET['type']??'', 'gasto') ?>>Gasto</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Estado</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="approved" <?= selected($_GET['status']??'', 'approved') ?>>Aprobado</option>
                    <option value="pending" <?= selected($_GET['status']??'', 'pending') ?>>Pendiente</option>
                    <option value="rejected" <?= selected($_GET['status']??'', 'rejected') ?>>Rechazado</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Categoría</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat['category']) ?>" <?= selected($_GET['category']??'', $cat['category']) ?>><?= e($cat['category']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn-modern btn-modern-primary btn-modern-sm w-100"><i class="bi bi-search"></i></button>
            </div>
            <div class="col-md-1">
                <a href="<?= url('movimientos') ?>" class="btn-modern btn-modern-outline btn-modern-sm w-100"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>
<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern mb-0">
                <thead><tr><th>ID</th><th>Fecha</th><th>Tipo</th><th>Monto</th><th>Descripción</th><th>Categoría</th><th>Proveedor</th><th>Estado</th><th>Creado</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php if (empty($movements)): ?>
                    <tr><td colspan="10"><div class="empty-state"><i class="bi bi-arrow-left-right"></i><p>No hay movimientos</p></div></td></tr>
                    <?php else: ?>
                    <?php foreach ($movements as $m): ?>
                    <tr>
                        <td><?= $m['id'] ?></td>
                        <td><?= format_date($m['date']) ?></td>
                        <td><span class="badge bg-<?= $m['type']==='ingreso'?'success':'danger' ?>"><?= ucfirst($m['type']) ?></span></td>
                        <td class="fw-bold"><?= format_cop($m['amount']) ?></td>
                        <td><?= e($m['description']) ?></td>
                        <td><?= e($m['category']) ?></td>
                        <td><?= e($m['proveedor_beneficiario'] ?? '') ?></td>
                        <td><?= statusBadge($m['status']) ?></td>
                        <td class="small text-muted"><?= e($m['creador'] ?? '') ?></td>
                        <td>
                            <?php if (\App\Core\Session::userRole() === 'admin'): ?>
                                <?php if ($m['status'] === 'pending'): ?>
                                <form method="post" action="<?= url('movimientos/aprobar/' . $m['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn-modern btn-modern-outline btn-modern-sm" title="Aprobar"><i class="bi bi-check-lg"></i></button>
                                </form>
                                <form method="post" action="<?= url('movimientos/rechazar/' . $m['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn-modern btn-modern-outline btn-modern-sm" title="Rechazar"><i class="bi bi-x-lg"></i></button>
                                </form>
                                <?php endif; ?>
                                <form method="post" action="<?= url('movimientos/eliminar/' . $m['id']) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar movimiento?')">
                                    <?= csrf_field() ?>
                                    <button class="btn-modern btn-modern-danger btn-modern-sm" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            <?php endif; ?>
                            <?php if (!empty($m['soporte_url'])): ?>
                            <a href="<?= upload_url('documents', $m['soporte_url']) ?>" target="_blank" class="btn-modern btn-modern-outline btn-modern-sm" title="Ver soporte"><i class="bi bi-paperclip"></i></a>
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

<div class="modal fade" id="movModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-modern" method="post" action="<?= url('movimientos/crear') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header"><h5 class="modal-title">Nuevo Movimiento</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select name="type" class="form-select" required>
                                <option value="ingreso">Ingreso</option>
                                <option value="gasto">Gasto</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monto ($)</label>
                            <input type="text" name="amount" class="form-control" required oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="description" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoría</label>
                            <input type="text" name="category" class="form-control" list="catList">
                            <datalist id="catList">
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= e($cat['category']) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Forma de Pago</label>
                            <select name="forma_pago" class="form-select">
                                <option value="">Seleccione</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Transferencia">Transferencia</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Proveedor/Beneficiario</label>
                            <input type="text" name="proveedor_beneficiario" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Soporte</label>
                            <input type="file" name="soporte" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-modern btn-modern-primary">Guardar</button>
                    <button type="button" class="btn-modern btn-modern-outline" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
