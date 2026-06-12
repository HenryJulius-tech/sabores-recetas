<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$totalCursos = db_fetchOne("SELECT COUNT(*) as c FROM cursos")['c'];
$totalUsuarios = db_fetchOne("SELECT COUNT(*) as c FROM usuarios")['c'];
$totalInscripciones = db_fetchOne("SELECT COUNT(*) as c FROM inscripciones")['c'];
$pendientes = db_fetchOne("SELECT COUNT(*) as c FROM pagos WHERE estado='pendiente'")['c'];
$ultimosCursos = db_fetchAll("SELECT * FROM cursos ORDER BY created_at DESC LIMIT 5");
$ultimasInscripciones = db_fetchAll("SELECT i.*, u.nombre, c.titulo FROM inscripciones i JOIN usuarios u ON i.user_id=u.id JOIN cursos c ON i.curso_id=c.id ORDER BY i.created_at DESC LIMIT 5");

$titulo = 'Dashboard';
include __DIR__ . '/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold m-0" style="color:#1a1a2e;">Panel de Administración</h2>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg,#667eea,#764ba2);">
            <div class="stat-icon"><i class="bi bi-book-fill"></i></div>
            <div class="stat-number"><?= $totalCursos ?></div>
            <div class="stat-label fw-bold">Cursos Totales</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg,#4facfe,#00f2fe);">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-number"><?= $totalUsuarios ?></div>
            <div class="stat-label fw-bold">Usuarios</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg,#f093fb,#f5576c);">
            <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
            <div class="stat-number"><?= $totalInscripciones ?></div>
            <div class="stat-label fw-bold">Inscripciones</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg,#fa709a,#fee140);">
            <div class="stat-icon"><i class="bi bi-credit-card-fill"></i></div>
            <div class="stat-number"><?= $pendientes ?></div>
            <div class="stat-label fw-bold">Pagos Pendientes</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark m-0"><i class="bi bi-collection-play text-primary me-2"></i>Últimos Cursos</h5>
                <a href="<?= BASE_URL ?>admin/cursos/index.php" class="btn btn-sm btn-outline-secondary rounded-pill">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush rounded-4 overflow-hidden">
                    <?php if (empty($ultimosCursos)): ?>
                        <li class="list-group-item p-4 text-center text-muted border-0">No hay cursos registrados.</li>
                    <?php else: ?>
                        <?php foreach ($ultimosCursos as $c): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 border-light">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= upload('cursos/' . $c['imagen']) ?>" alt="<?= e($c['titulo']) ?>" class="rounded-3" style="width:40px;height:40px;object-fit:cover;">
                                    <strong><?= e($c['titulo']) ?></strong>
                                </div>
                                <span class="badge bg-light text-dark rounded-pill px-3 py-2 border shadow-sm"><?= format_cop($c['precio']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark m-0"><i class="bi bi-person-lines-fill text-success me-2"></i>Últimas Inscripciones</h5>
                <a href="<?= BASE_URL ?>admin/pagos/index.php" class="btn btn-sm btn-outline-secondary rounded-pill">Gestionar Pagos</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush rounded-4 overflow-hidden">
                    <?php if (empty($ultimasInscripciones)): ?>
                        <li class="list-group-item p-4 text-center text-muted border-0">No hay inscripciones recientes.</li>
                    <?php else: ?>
                        <?php foreach ($ultimasInscripciones as $i): ?>
                            <li class="list-group-item px-4 py-3 border-light">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong class="text-dark"><?= e($i['nombre']) ?></strong>
                                    <?= estadoBadge($i['estado']) ?>
                                </div>
                                <div class="text-muted small"><i class="bi bi-arrow-return-right me-1"></i><?= e($i['titulo']) ?></div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card { border-radius: 16px; padding: 24px; color: #fff; position: relative; overflow: hidden; transition: transform 0.3s ease; }
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
.stat-icon { font-size: 48px; opacity: 0.2; position: absolute; right: 16px; top: 50%; transform: translateY(-50%); }
.stat-number { font-size: 36px; font-weight: 800; line-height: 1; margin-bottom: 8px; }
.stat-label { font-size: 14px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; }
</style>
<?php include __DIR__ . '/footer.php'; ?>
