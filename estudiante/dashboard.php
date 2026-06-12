<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireEstudiante();

$userId = session_userId();
$totalCursos = db_fetchOne("SELECT COUNT(*) as c FROM cursos")['c'];
$misCursos = db_fetchOne("SELECT COUNT(*) as c FROM inscripciones WHERE user_id=? AND estado='aprobado'", [$userId])['c'];
$pendientes = db_fetchOne("SELECT COUNT(*) as c FROM inscripciones WHERE user_id=? AND estado='pendiente'", [$userId])['c'];
$ultimosCursos = db_fetchAll("SELECT * FROM cursos ORDER BY created_at DESC LIMIT 3");

$titulo = 'Inicio';
include __DIR__ . '/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color:#1a1a2e;">¡Bienvenido, <?= e(session_userNombre()) ?>!</h2>
        <p class="text-muted">Explora los cursos y aprende nuevas recetas</p>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg,#e91e63,#c2185b);">
            <div class="stat-icon"><i class="bi bi-book-fill"></i></div>
            <div class="stat-number"><?= $totalCursos ?></div>
            <div class="stat-label fw-bold">Cursos Disponibles</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg,#4facfe,#00f2fe);">
            <div class="stat-icon"><i class="bi bi-journal-text"></i></div>
            <div class="stat-number"><?= $misCursos ?></div>
            <div class="stat-label fw-bold">Mis Cursos Activos</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg,#f093fb,#f5576c);">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-number"><?= $pendientes ?></div>
            <div class="stat-label fw-bold">Inscripciones Pendientes</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-end mb-4">
    <h4 class="fw-bold m-0" style="color:#1a1a2e;">Últimos Cursos Añadidos</h4>
    <a href="cursos.php" class="btn btn-outline-primary rounded-pill btn-sm fw-bold px-3">Ver catálogo</a>
</div>

<div class="row g-4">
    <?php foreach ($ultimosCursos as $c): ?>
        <div class="col-md-4">
            <div class="card course-card h-100 shadow-sm border-0">
                <div class="course-img-wrapper">
                    <div style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                        <?= nivelBadge($c['nivel']) ?>
                    </div>
                    <img src="<?= upload('cursos/' . $c['imagen']) ?>" alt="<?= e($c['titulo']) ?>">
                </div>
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="fw-bold mb-2" style="color:#1a1a2e;"><?= e($c['titulo']) ?></h5>
                    <p class="text-muted small flex-grow-1 mb-3"><?= truncate($c['descripcion'] ?? '', 80) ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                        <strong style="color:#e91e63; font-size: 1.1rem;"><?= format_cop($c['precio']) ?></strong>
                        <a href="cursos.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3">Ver detalles</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
.stat-card { border-radius: 16px; padding: 24px; color: #fff; position: relative; overflow: hidden; transition: transform 0.3s ease; }
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
.stat-icon { font-size: 42px; opacity: 0.2; position: absolute; right: 20px; top: 50%; transform: translateY(-50%); }
.stat-number { font-size: 36px; font-weight: 800; line-height: 1; margin-bottom: 8px; }
.stat-label { font-size: 15px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; }

.course-card { border-radius: 16px; overflow: hidden; transition: all 0.3s ease; }
.course-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important; }
.course-img-wrapper { position: relative; height: 180px; overflow: hidden; }
.course-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.course-card:hover .course-img-wrapper img { transform: scale(1.05); }
</style>

<?php include __DIR__ . '/footer.php'; ?>
