<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
requireEstudiante();

$userId = session_userId();
$misInscripciones = db_fetchAll("SELECT curso_id, estado FROM inscripciones WHERE user_id=?", [$userId]);
$inscrito = [];
foreach ($misInscripciones as $i) $inscrito[$i['curso_id']] = $i['estado'];

$cursos = db_fetchAll("SELECT * FROM cursos ORDER BY created_at DESC");

// La lógica de inscripción ahora se procesa de forma segura en inscribir.php

$titulo = 'Catálogo de Cursos';
include __DIR__ . '/header.php';
?>
<div class="mb-5">
    <h2 class="fw-bold" style="color:#1a1a2e;">Catálogo de Cursos</h2>
    <p class="text-muted fs-5">Explora nuestra selección premium y comienza a aprender hoy.</p>
</div>

<div class="row g-4">
    <?php foreach ($cursos as $c): ?>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card course-card shadow-sm h-100 border-0">
                <div class="course-img-wrapper">
                    <div style="position: absolute; top: 15px; left: 15px; z-index: 10;">
                        <?= nivelBadge($c['nivel']) ?>
                    </div>
                    <img src="<?= upload('cursos/' . $c['imagen']) ?>" alt="<?= e($c['titulo']) ?>">
                </div>
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="fw-bold mb-3" style="color:#1a1a2e;"><?= e($c['titulo']) ?></h5>
                    <p class="text-muted small flex-grow-1 mb-4"><?= truncate($c['descripcion'] ?? '', 100) ?></p>
                    
                    <div class="course-meta mb-3">
                        <span><i class="bi bi-clock-history text-primary"></i> <?= e($c['duracion'] ?: 'Flexible') ?></span>
                        <span class="ms-3"><i class="bi bi-person-badge text-primary"></i> <?= e($c['instructor'] ?: 'Experto') ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                        <strong style="color:#e91e63; font-size:1.25rem; font-weight: 800;"><?= format_cop($c['precio']) ?></strong>
                        
                        <?php if (isset($inscrito[$c['id']])): ?>
                            <?php if ($inscrito[$c['id']] === 'aprobado'): ?>
                                <a href="ver-curso.php?id=<?= $c['id'] ?>" class="btn btn-success rounded-pill fw-bold px-3">Ir al Curso</a>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-hourglass-split"></i> Pendiente</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <form method="POST" action="<?= BASE_URL ?>estudiante/inscribir.php" class="m-0">
                                <?= csrf_field() ?>
                                <input type="hidden" name="curso_id" value="<?= $c['id'] ?>">
                                <button type="submit" name="inscribirse" class="btn btn-primary rounded-pill fw-bold px-3"><i class="bi bi-cart-plus me-1"></i> Comprar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
.course-card { border-radius: 16px; overflow: hidden; transition: all 0.3s ease; background: #fff; }
.course-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important; }
.course-img-wrapper { position: relative; height: 200px; overflow: hidden; }
.course-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.course-card:hover .course-img-wrapper img { transform: scale(1.05); }
.course-meta { font-size: 13px; color: #6c757d; font-weight: 500; }
</style>
<?php include __DIR__ . '/footer.php'; ?>
