<div class="row g-4 mb-4 fade-in">
    <div class="col-md-3">
        <div class="metric-card d-flex align-items-center gap-3">
            <div class="metric-icon"><i class="bi bi-arrow-down-circle"></i></div>
            <div>
                <div class="metric-value"><?= format_cop($total_ingresos) ?></div>
                <div class="metric-label">Ingresos</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card d-flex align-items-center gap-3">
            <div class="metric-icon"><i class="bi bi-arrow-up-circle"></i></div>
            <div>
                <div class="metric-value"><?= format_cop($total_gastos) ?></div>
                <div class="metric-label">Gastos</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card d-flex align-items-center gap-3">
            <div class="metric-icon"><i class="bi bi-wallet2"></i></div>
            <div>
                <div class="metric-value <?= $balance >= 0 ? '' : 'text-danger' ?>"><?= format_cop($balance) ?></div>
                <div class="metric-label">Balance</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card d-flex align-items-center gap-3">
            <div class="metric-icon"><i class="bi bi-inbox"></i></div>
            <div>
                <div class="metric-value"><?= $compras_pendientes ?></div>
                <div class="metric-label">Matrículas Pendientes</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Flujo de Caja</h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn-modern btn-modern-outline btn-modern-sm chart-filter active" data-filter="diario">Diario</button>
                    <button class="btn-modern btn-modern-outline btn-modern-sm chart-filter" data-filter="semanal">Semanal</button>
                    <button class="btn-modern btn-modern-outline btn-modern-sm chart-filter" data-filter="mensual">Mensual</button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="financeChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Estado del Sistema</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <span class="fw-medium">Cursos</span>
                    <span class="badge-status active fs-6 px-3"><?= $total_productos ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <span class="fw-medium">Usuarios</span>
                    <span class="badge-status pending fs-6 px-3"><?= $total_usuarios ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">Matrículas Pendientes</span>
                    <span class="badge-status pending fs-6 px-3"><?= $compras_pendientes ?></span>
                </div>
                <hr>
                <a href="<?= url('admin/enrollments') ?>" class="btn-modern btn-modern-primary w-100 text-center d-block">
                    <i class="bi bi-inbox me-2"></i>Ir a Matrículas
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card-modern fade-in">
    <div class="card-header">
        <h5 class="mb-0 fw-bold">Últimos Movimientos</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern mb-0">
                <thead><tr><th>Fecha</th><th>Tipo</th><th>Monto</th><th>Descripción</th><th>Estado</th><th>Creado por</th></tr></thead>
                <tbody>
                    <?php if (empty($recent_movements)): ?>
                    <tr><td colspan="6"><div class="empty-state"><i class="bi bi-archive"></i><p>No hay movimientos recientes</p></div></td></tr>
                    <?php else: ?>
                    <?php foreach ($recent_movements as $m): ?>
                    <tr>
                        <td><?= format_date($m['date']) ?></td>
                        <td><span class="badge-status <?= $m['type'] === 'ingreso' ? 'approved' : 'rejected' ?>"><?= ucfirst($m['type']) ?></span></td>
                        <td class="fw-bold"><?= format_cop($m['amount']) ?></td>
                        <td><?= e($m['description']) ?></td>
                        <td><?= statusBadge($m['status']) ?></td>
                        <td><?= e($m['creador'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $scripts = '<script>var API_FINANCE_URL="' . url('api/finance-data') . '";</script><script src="' . asset('js/dashboard.js') . '"></script>'; ?>
