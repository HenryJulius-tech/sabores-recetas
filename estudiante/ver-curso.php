<?php
/**
 * estudiante/ver-curso.php
 * Vista principal de un curso con:
 *  - Validación segura del ID
 *  - Barra de progreso dinámica
 *  - Acordeón de módulos/clases con indicador de completadas
 *  - Botón de Examen Final cuando progreso = 100%
 *  - Protección try/catch para tablas opcionales inexistentes
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
requireEstudiante();

// ── 1. Validar ID ─────────────────────────────────────────────────────────
$curso_id = (int)($_GET['id'] ?? 0);
if ($curso_id <= 0) {
    session_setFlash('error', 'ID de curso inválido.');
    redirect(BASE_URL . 'estudiante/cursos.php');
}

// ── 2. Cargar curso ────────────────────────────────────────────────────────
$curso = db_fetchOne("SELECT * FROM cursos WHERE id=?", [$curso_id]);
if (!$curso) {
    session_setFlash('error', 'El curso solicitado no existe.');
    redirect(BASE_URL . 'estudiante/cursos.php');
}

// ── 3. Verificar inscripción aprobada ─────────────────────────────────────
$userId      = session_userId();
$inscripcion = db_fetchOne(
    "SELECT * FROM inscripciones WHERE user_id=? AND curso_id=?",
    [$userId, $curso_id]
);
if (!$inscripcion || $inscripcion['estado'] !== 'aprobado') {
    session_setFlash('error', 'No tienes acceso a este curso. Verifica el estado de tu inscripción.');
    redirect(BASE_URL . 'estudiante/mis-cursos.php');
}

// ── 4. Progreso (tabla opcional — protegido con try/catch) ────────────────
$progreso    = 0;
$estadoCurso = 'Inscrito';
try {
    $progresoRow = db_fetchOne(
        "SELECT progreso_porcentaje, estado_curso
         FROM progreso_estudiantes
         WHERE usuario_id=? AND curso_id=?",
        [$userId, $curso_id]
    );
    if ($progresoRow) {
        $progreso    = (int)$progresoRow['progreso_porcentaje'];
        $estadoCurso = $progresoRow['estado_curso'];
    }
} catch (Exception $e) {
    // La tabla aún no existe: ignorar, el usuario verá 0%
    // → Ejecuta database/setup_completo.php para crearla
}

// ── 5. IDs de clases completadas (tabla opcional) ─────────────────────────
$completadasIds = [];
try {
    $completadasIds = array_column(
        db_fetchAll(
            "SELECT clase_id FROM clases_completadas WHERE user_id=? AND curso_id=?",
            [$userId, $curso_id]
        ),
        'clase_id'
    );
} catch (Exception $e) {
    // Ignorar si la tabla no existe aún
}

// ── 6. Módulos y clases ───────────────────────────────────────────────────
$modulos     = db_fetchAll("SELECT * FROM modulos WHERE curso_id=? ORDER BY orden", [$curso_id]);
$moduloData  = [];
$totalClases = 0;
foreach ($modulos as $m) {
    $clases       = db_fetchAll("SELECT * FROM clases WHERE modulo_id=? ORDER BY orden", [$m['id']]);
    $totalClases += count($clases);
    $moduloData[] = ['modulo' => $m, 'clases' => $clases];
}
$totalCompletadas = count($completadasIds);

// ── 7. Primera clase pendiente para botón "Continuar" ─────────────────────
$primeraClasePendiente = null;
foreach ($moduloData as $md) {
    foreach ($md['clases'] as $cl) {
        if (!in_array($cl['id'], $completadasIds)) {
            $primeraClasePendiente = $cl;
            break 2;
        }
    }
}

$titulo = $curso['titulo'];
include __DIR__ . '/header.php';
?>

<!-- ══ Cabecera ═══════════════════════════════════════════════════════════ -->
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="<?= BASE_URL ?>estudiante/mis-cursos.php"
       class="btn btn-outline-secondary rounded-circle"
       style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;"
       title="Volver a Mis Cursos">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="fw-bold mb-1" style="color:#1a1a2e;"><?= e($curso['titulo']) ?></h2>
        <div class="d-flex align-items-center gap-3 mt-1 flex-wrap">
            <?= nivelBadge($curso['nivel'] ?? 'principiante') ?>
            <span class="text-muted small">
                <i class="bi bi-clock-history text-primary me-1"></i>
                <?= e($curso['duracion'] ?: 'Variable') ?>
            </span>
            <span class="text-muted small">
                <i class="bi bi-person-badge text-primary me-1"></i>
                <?= e($curso['instructor'] ?: 'Sin instructor') ?>
            </span>
        </div>
    </div>
</div>

<div class="row g-4">

    <!-- ══ Columna Lateral ═══════════════════════════════════════════════ -->
    <div class="col-lg-4 order-lg-2">
        <div class="card border-0 shadow-sm rounded-4 sticky-lg-top" style="top:20px;">
            <div class="card-body p-0">

                <!-- Imagen del curso -->
                <img src="<?= upload('cursos/' . ($curso['imagen'] ?? '')) ?>"
                     alt="<?= e($curso['titulo']) ?>"
                     class="img-fluid rounded-top-4 w-100"
                     style="object-fit:cover;height:200px;">

                <div class="p-4">

                    <!-- Badge aprobado -->
                    <?php if ($estadoCurso === 'Aprobado'): ?>
                    <div class="alert alert-success rounded-3 d-flex align-items-center gap-2 mb-3 py-2">
                        <i class="bi bi-patch-check-fill fs-4"></i>
                        <div>
                            <div class="fw-bold">¡Curso Aprobado! 🎉</div>
                            <small class="text-muted">Has superado el examen final</small>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Barra de progreso -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-600 small text-dark">Tu progreso</span>
                            <span class="fw-bold small <?= $progreso >= 100 ? 'text-success' : 'text-primary' ?>">
                                <?= $progreso ?>%
                            </span>
                        </div>
                        <div class="progress rounded-pill" style="height:12px;">
                            <div class="progress-bar <?= $progreso >= 100 ? 'bg-success' : 'bg-primary' ?> rounded-pill"
                                 role="progressbar"
                                 style="width:<?= $progreso ?>%;transition:width 1.2s ease;"
                                 aria-valuenow="<?= $progreso ?>"
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            <?= $totalCompletadas ?> de <?= $totalClases ?> clases completadas
                        </small>
                    </div>

                    <!-- Botón CTA dinámico -->
                    <div class="d-grid">
                        <?php if ($estadoCurso === 'Aprobado'): ?>
                            <span class="btn btn-success rounded-pill fw-bold disabled">
                                <i class="bi bi-trophy-fill me-2"></i>Curso Completado ✓
                            </span>
                        <?php elseif ($progreso >= 100): ?>
                            <a href="<?= BASE_URL ?>estudiante/examen.php?id=<?= $curso_id ?>"
                               class="btn btn-warning rounded-pill fw-bold pulse-exam-btn">
                                <i class="bi bi-trophy-fill me-2"></i>¡Presentar Examen Final!
                            </a>
                            <p class="text-center text-muted small mt-2 mb-0">
                                Has completado todas las clases 🎉
                            </p>
                        <?php elseif ($primeraClasePendiente): ?>
                            <a href="<?= BASE_URL ?>estudiante/ver-clase.php?id=<?= $curso_id ?>&clase=<?= $primeraClasePendiente['id'] ?>"
                               class="btn btn-primary rounded-pill fw-bold">
                                <i class="bi bi-play-fill me-2"></i>
                                <?= $progreso > 0 ? 'Continuar' : 'Comenzar' ?> aprendiendo
                            </a>
                        <?php else: ?>
                            <span class="btn btn-secondary rounded-pill fw-bold disabled">
                                Sin clases disponibles aún
                            </span>
                        <?php endif; ?>
                    </div>

                    <hr class="my-3">

                    <!-- Estadísticas -->
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Módulos</span>
                        <span class="fw-bold text-dark"><?= count($moduloData) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Clases totales</span>
                        <span class="fw-bold text-dark"><?= $totalClases ?></span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ══ Contenido: Módulos y Clases ═══════════════════════════════════ -->
    <div class="col-lg-8 order-lg-1">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h4 class="fw-bold text-dark">
                    <i class="bi bi-list-stars text-primary me-2"></i>Contenido del Curso
                </h4>
            </div>
            <div class="card-body p-4">

                <?php if (empty($moduloData)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-cone-striped fs-1 text-warning d-block mb-3"></i>
                        <p class="mb-0">Este curso aún no tiene módulos publicados.</p>
                        <small>El instructor está preparando el contenido.</small>
                    </div>
                <?php else: ?>
                <div class="accordion accordion-flush" id="modulosAccordion">
                    <?php $idx = 0; foreach ($moduloData as $md): $idx++;
                        $doneEnModulo  = count(array_filter($md['clases'], function($cl) use ($completadasIds) { return in_array($cl['id'], $completadasIds); }));
                        $totalEnModulo = count($md['clases']);
                        $moduloCompleto = ($totalEnModulo > 0 && $doneEnModulo === $totalEnModulo);
                    ?>
                    <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $idx > 1 ? 'collapsed' : '' ?> fw-bold"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#modulo<?= $idx ?>"
                                    style="background:#f8f9fa;color:#1a1a2e;box-shadow:none;">
                                <span class="badge <?= $moduloCompleto ? 'bg-success' : 'bg-primary' ?> text-white rounded-pill me-3">
                                    <?= $moduloCompleto
                                        ? '<i class="bi bi-check-lg"></i>'
                                        : 'M' . $idx ?>
                                </span>
                                <?= e($md['modulo']['titulo']) ?>
                                <span class="ms-auto text-muted small fw-normal d-none d-sm-flex align-items-center gap-1 me-2">
                                    <span class="<?= $moduloCompleto ? 'text-success fw-bold' : '' ?>">
                                        <?= $doneEnModulo ?>/<?= $totalEnModulo ?>
                                    </span>
                                    clases
                                </span>
                            </button>
                        </h2>
                        <div id="modulo<?= $idx ?>"
                             class="accordion-collapse collapse <?= $idx === 1 ? 'show' : '' ?>"
                             data-bs-parent="#modulosAccordion">
                            <div class="accordion-body p-0">
                                <?php if (empty($md['clases'])): ?>
                                    <div class="p-4 text-center text-muted">
                                        <i class="bi bi-cone-striped fs-3 d-block mb-2 text-warning"></i>
                                        Aún no hay clases publicadas en este módulo.
                                    </div>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($md['clases'] as $cl):
                                            $isDone = in_array($cl['id'], $completadasIds);
                                        ?>
                                        <a href="<?= BASE_URL ?>estudiante/ver-clase.php?id=<?= $curso_id ?>&clase=<?= $cl['id'] ?>"
                                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 border-bottom-0 <?= $isDone ? 'clase-done' : '' ?>">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="play-btn-wrapper">
                                                    <?php if ($isDone): ?>
                                                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-play-circle-fill text-danger fs-4"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <strong class="d-block text-dark"><?= e($cl['titulo']) ?></strong>
                                                    <small class="text-muted">
                                                        <i class="bi bi-camera-video me-1"></i>Video lección
                                                        <?php if ($isDone): ?>
                                                            · <span class="text-success fw-500">Completada</span>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <?php if (!empty($cl['duracion'])): ?>
                                                <span class="badge bg-light text-secondary rounded-pill py-2 px-3 border flex-shrink-0">
                                                    <i class="bi bi-clock me-1"></i><?= e($cl['duracion']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</div><!-- /row -->

<style>
/* ── Estilos de la vista de curso ── */
.play-btn-wrapper { transition: transform 0.2s ease; }
.list-group-item-action:hover .play-btn-wrapper { transform: scale(1.15); }
.accordion-button:not(.collapsed) { background-color: #e9ecef !important; }
.clase-done { background-color: rgba(25, 135, 84, 0.05); }

/* Animación del botón de examen */
.pulse-exam-btn {
    animation: examPulse 2s infinite;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.35);
}
@keyframes examPulse {
    0%, 100% { box-shadow: 0 4px 15px rgba(255,193,7,0.35); transform: scale(1); }
    50%       { box-shadow: 0 6px 25px rgba(255,193,7,0.55); transform: scale(1.02); }
}
</style>

<?php include __DIR__ . '/footer.php'; ?>
