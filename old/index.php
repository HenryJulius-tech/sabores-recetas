<?php
require_once 'config.php';
require_role('admin');

// Fetch summary metrics
$stmt = $pdo->query("SELECT SUM(amount) FROM movimientos WHERE type = 'ingreso'");
$ingresos_total = $stmt->fetchColumn() ?: 0.0;

$stmt = $pdo->query("SELECT SUM(amount) FROM movimientos WHERE type = 'gasto'");
$gastos_total = $stmt->fetchColumn() ?: 0.0;

$balance_neto = $ingresos_total - $gastos_total;

// Fetch last movements
$stmt = $pdo->query("SELECT * FROM movimientos ORDER BY date DESC, id DESC LIMIT 5");
$ultimos_movimientos = $stmt->fetchAll();

// System status counts
$productos_count = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$compras_pendientes = $pdo->query("SELECT COUNT(*) FROM compras WHERE status = 'pending'")->fetchColumn();
$usuarios_count = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

$page_title = 'Finca La Karen - Dashboard Financiero';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Librería Chart.js para visualización de gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- GRID DE METRICAS PRINCIPALES -->
<div class="metrics-grid">
    <!-- INGRESOS -->
    <div class="metric-card">
        <div class="metric-icon-wrapper success">
            <i data-lucide="trending-up"></i>
        </div>
        <div class="metric-details">
            <div class="metric-title">Ingresos Totales</div>
            <div class="metric-value" id="val-ingresos"><?= format_cop($ingresos_total) ?></div>
        </div>
    </div>

    <!-- GASTOS -->
    <div class="metric-card">
        <div class="metric-icon-wrapper danger">
            <i data-lucide="trending-down"></i>
        </div>
        <div class="metric-details">
            <div class="metric-title">Gastos Totales</div>
            <div class="metric-value" id="val-gastos"><?= format_cop($gastos_total) ?></div>
        </div>
    </div>

    <!-- NET GAIN -->
    <div class="metric-card">
        <div class="metric-icon-wrapper info">
            <i data-lucide="scale"></i>
        </div>
        <div class="metric-details">
            <div class="metric-title">Ganancia Neta</div>
            <div class="metric-value" style="color: <?= $balance_neto >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                <?= format_cop($balance_neto) ?>
            </div>
        </div>
    </div>
</div>

<!-- FILA CON GRÁFICO E INFORMACIÓN COMPLEMENTARIA -->
<div class="dashboard-row">
    <!-- PANEL DE GRÁFICO -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">
                <i data-lucide="bar-chart-3" style="color: var(--primary);"></i>
                <span>Comparativa de Flujo de Caja</span>
            </div>
            <div class="panel-actions">
                <select id="chart-filter" class="select-filter">
                    <option value="diario">Diario (Últimos 15 días)</option>
                    <option value="semanal">Semanal (Últimas 8 sem)</option>
                    <option value="mensual" selected>Mensual (Últimos 6 meses)</option>
                </select>
            </div>
        </div>
        
        <div style="position: relative; height: 320px; width: 100%;">
            <canvas id="financeChart"></canvas>
        </div>
    </div>

    <!-- RESUMEN DE ESTADO DEL SISTEMA -->
    <div class="panel" style="margin-bottom: 24px;">
        <div class="panel-header">
            <div class="panel-title">
                <i data-lucide="activity" style="color: var(--info);"></i>
                <span>Estado del Sistema</span>
            </div>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="package" style="color: var(--text-muted); width: 20px;"></i>
                    <span style="font-weight: 500;">Productos en Catálogo</span>
                </div>
                <span class="role-badge client" style="font-weight: bold;"><?= $productos_count ?></span>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="shopping-cart" style="color: var(--text-muted); width: 20px;"></i>
                    <span style="font-weight: 500;">Compras Pendientes</span>
                </div>
                <span class="role-badge worker" style="font-weight: bold;"><?= $compras_pendientes ?></span>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="users-2" style="color: var(--text-muted); width: 20px;"></i>
                    <span style="font-weight: 500;">Usuarios Registrados</span>
                </div>
                <span class="role-badge admin" style="font-weight: bold;"><?= $usuarios_count ?></span>
            </div>
            
            <?php if ($current_user['role'] == 'admin'): ?>
            <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 8px;">
                <a href="admin_compras.php" class="btn btn-primary btn-block" style="font-size: 13px; padding: 10px;">
                    <i data-lucide="check-square"></i> Ver Aprobaciones Pendientes
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- TABLA DE ULTIMOS MOVIMIENTOS -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            <i data-lucide="history" style="color: var(--primary);"></i>
            <span>Últimos Movimientos Registrados</span>
        </div>
        <a href="movimientos.php" class="btn btn-outline" style="font-size: 13px; padding: 6px 12px;">
            Ver Todos <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th style="text-align: right;">Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ultimos_movimientos): ?>
                    <?php foreach ($ultimos_movimientos as $m): ?>
                    <tr>
                        <td style="font-weight: 500; color: var(--text-muted);"><?= date('Y-m-d', strtotime($m['date'])) ?></td>
                        <td>
                            <?php if ($m['type'] == 'ingreso'): ?>
                                <span class="status-badge approved">Ingreso</span>
                            <?php else: ?>
                                <span class="status-badge rejected">Gasto</span>
                            <?php endif; ?>
                        </td>
                        <td><span style="font-weight: 600;"><?= htmlspecialchars($m['category']) ?></span></td>
                        <td><?= htmlspecialchars($m['description']) ?></td>
                        <td style="text-align: right; font-weight: 700; color: <?= $m['type'] == 'ingreso' ? 'var(--success)' : 'var(--danger)' ?>;">
                            <?= $m['type'] == 'ingreso' ? '+' : '-' ?><?= format_cop($m['amount']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            No hay movimientos registrados recientemente.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JS del Dashboard para inicializar el gráfico y filtros -->
<script src="js/dashboard.js"></script>

<?php include 'includes/footer.php'; ?>
