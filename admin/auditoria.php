<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

// Enforce admin role
requireAdmin();

// Obtener filtros de la URL
$search = trim($_GET['search'] ?? '');
$rol = trim($_GET['rol'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Construir la consulta SQL
$whereClauses = [];
$params = [];

if ($search !== '') {
    $whereClauses[] = "(nombre_usuario LIKE ? OR accion LIKE ? OR detalles LIKE ? OR direccion_ip LIKE ?)";
    $searchParam = "%{$search}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if ($rol !== '') {
    $whereClauses[] = "rol = ?";
    $params[] = $rol;
}

$whereString = '';
if (!empty($whereClauses)) {
    $whereString = " WHERE " . implode(" AND ", $whereClauses);
}

// Obtener el conteo total de registros coincidentes para la paginación
$countQuery = "SELECT COUNT(*) as total FROM `auditoria`" . $whereString;
$totalRecords = db_fetchOne($countQuery, $params)['total'] ?? 0;
$totalPages = max(1, ceil($totalRecords / $limit));
$page = min($page, $totalPages);
$offset = ($page - 1) * $limit;

// Obtener los registros paginados y ordenados por fecha descendente
$selectQuery = "SELECT * FROM `auditoria`" . $whereString . " ORDER BY fecha_registro DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
$logs = db_fetchAll($selectQuery, $params);

// Título de la página e inclusión del header
$titulo = 'Registro de Auditoría';
include __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold m-0" style="color:#1a1a2e;">Registro de Auditoría</h2>
        <p class="text-muted">Historial completo de acciones y eventos de seguridad en la plataforma.</p>
    </div>
</div>

<!-- Tarjeta de Filtros de Búsqueda -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="searchInput" class="form-label fw-bold text-dark small">Buscar acción, usuario o detalles</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="searchInput" class="form-control bg-light border-start-0" placeholder="Escribe para buscar..." value="<?= e($search) ?>">
                </div>
            </div>
            
            <div class="col-md-3">
                <label for="rolSelect" class="form-label fw-bold text-dark small">Filtrar por Rol</label>
                <select name="rol" id="rolSelect" class="form-select bg-light">
                    <option value="">Todos los roles</option>
                    <option value="admin" <?= selected($rol, 'admin') ?>>Administrador</option>
                    <option value="estudiante" <?= selected($rol, 'estudiante') ?>>Estudiante</option>
                    <option value="invitado" <?= selected($rol, 'invitado') ?>>Invitado</option>
                </select>
            </div>
            
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill fw-bold w-100 py-2"><i class="bi bi-funnel-fill me-1"></i> Filtrar</button>
                <?php if ($search !== '' || $rol !== ''): ?>
                    <a href="auditoria.php" class="btn btn-outline-secondary rounded-pill fw-bold w-100 py-2 d-flex align-items-center justify-content-center"><i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Listado de Auditoría -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-uppercase text-muted fs-7 border-bottom">
                        <th class="ps-4 py-3">Fecha y Hora</th>
                        <th class="py-3">Usuario</th>
                        <th class="py-3">Rol</th>
                        <th class="py-3">Acción Realizada</th>
                        <th class="py-3">Detalles</th>
                        <th class="py-3">Dirección IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-shield-slash fs-1 d-block mb-3 text-secondary"></i>
                                No se encontraron registros de auditoría coincidentes.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="ps-4 text-dark font-monospace py-3">
                                    <?= date('d/m/Y H:i:s', strtotime($log['fecha_registro'])) ?>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= e($log['nombre_usuario']) ?></div>
                                </td>
                                <td>
                                    <?php if ($log['rol'] === 'admin'): ?>
                                        <span class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill fw-bold" style="background-color: rgba(220,53,69,0.1);">
                                            <i class="bi bi-shield-fill-exclamation me-1"></i>Admin
                                        </span>
                                    <?php elseif ($log['rol'] === 'estudiante'): ?>
                                        <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-bold" style="background-color: rgba(13,110,253,0.1);">
                                            <i class="bi bi-mortarboard-fill me-1"></i>Estudiante
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-soft text-secondary px-3 py-2 rounded-pill fw-bold" style="background-color: rgba(108,117,125,0.1);">
                                            <i class="bi bi-eye-fill me-1"></i>Invitado
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-dark"><?= e($log['accion']) ?></strong>
                                </td>
                                <td class="text-muted text-wrap" style="max-width: 300px;">
                                    <?= e($log['detalles'] ?: 'Sin detalles adicionales') ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2 font-monospace">
                                        <i class="bi bi-pc-display me-1 text-muted"></i><?= e($log['direccion_ip']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Paginación -->
<?php if ($totalPages > 1): ?>
    <nav aria-label="Navegación de auditoría" class="d-flex justify-content-between align-items-center px-2">
        <div class="text-muted small">
            Mostrando registros del <strong><?= $offset + 1 ?></strong> al <strong><?= min($offset + $limit, $totalRecords) ?></strong> de un total de <strong><?= $totalRecords ?></strong>.
        </div>
        <ul class="pagination pagination-rounded mb-0">
            <!-- Página anterior -->
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?search=<?= urlencode($search) ?>&rol=<?= urlencode($rol) ?>&page=<?= $page - 1 ?>" aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            
            <!-- Páginas numéricas -->
            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            for ($p = $startPage; $p <= $endPage; $p++):
            ?>
                <li class="page-item <?= ($p === $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?search=<?= urlencode($search) ?>&rol=<?= urlencode($rol) ?>&page=<?= $p ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
            
            <!-- Página siguiente -->
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?search=<?= urlencode($search) ?>&rol=<?= urlencode($rol) ?>&page=<?= $page + 1 ?>" aria-label="Siguiente">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<style>
.pagination-rounded .page-link {
    border-radius: 50% !important;
    margin: 0 3px;
    border: none;
    color: #495057;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pagination-rounded .page-item.active .page-link {
    background-color: #0d6efd;
    color: #fff;
}
.fs-7 {
    font-size: 0.8rem;
}
</style>

<?php include __DIR__ . '/footer.php'; ?>
