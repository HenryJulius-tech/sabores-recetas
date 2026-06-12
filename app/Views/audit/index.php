<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title"><i class="bi bi-shield-exclamation me-2"></i>Auditoría</h1>
    <span class="text-muted small"><?= $total ?> registros</span>
</div>

<!-- Filters -->
<div class="card-modern mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Acción</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php foreach ($actions as $a): ?>
                    <option value="<?= e($a) ?>" <?= ($filters['action'] ?? '') === $a ? 'selected' : '' ?>><?= e($a) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Usuario</label>
                <input type="text" name="username" class="form-control form-control-sm" placeholder="Usuario" value="<?= e($filters['username'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Rol</label>
                <select name="role" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="client" <?= ($filters['role'] ?? '') === 'client' ? 'selected' : '' ?>>Cliente</option>
                    <option value="worker" <?= ($filters['role'] ?? '') === 'worker' ? 'selected' : '' ?>>Trabajador</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Desde</label>
                <input type="date" name="from" class="form-control form-control-sm" value="<?= e($filters['from'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Hasta</label>
                <input type="date" name="to" class="form-control form-control-sm" value="<?= e($filters['to'] ?? '') ?>">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-modern btn-modern-primary btn-modern-sm w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<!-- Logs -->
<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay registros de auditoría</td></tr>
                    <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="small text-muted"><?= format_datetime($log['created_at']) ?></td>
                        <td class="fw-bold"><?= e($log['username']) ?></td>
                        <td><span class="badge-status <?= $log['role'] === 'admin' ? 'active' : ($log['role'] === 'worker' ? 'pending' : 'inactive') ?>"><?= e($log['role']) ?></span></td>
                        <td><?= e($log['action']) ?></td>
                        <td class="small"><?= e($log['description']) ?></td>
                        <td class="small text-muted"><?= e($log['ip_address']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<nav class="mt-4">
    <ul class="pagination pagination-sm justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query($filters) ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
