<?php
/**
 * estudiante/ver-clase.php
 * Vista de una clase individual con reproductor de video.
 * Incluye sistema para marcar clase como completada y recalcular progreso,
 * con protección try/catch para evitar Error 500 si faltan tablas en la BD.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
requireEstudiante();

// ── 1. Validar parámetros ──────────────────────────────────────────────────
$curso_id = (int)($_GET['id'] ?? 0);
$clase_id = (int)($_GET['clase'] ?? 0);

if ($curso_id <= 0 || $clase_id <= 0) {
    session_setFlash('error', 'Parámetros inválidos.');
    redirect(BASE_URL . 'estudiante/cursos.php');
}

// ── 2. Cargar datos de la clase y validar pertenencia al curso ────────────
$clase = db_fetchOne("SELECT cl.*, m.curso_id FROM clases cl JOIN modulos m ON cl.modulo_id=m.id WHERE cl.id=?", [$clase_id]);
if (!$clase || $clase['curso_id'] != $curso_id) {
    session_setFlash('error', 'Clase no encontrada.');
    redirect(BASE_URL . "estudiante/ver-curso.php?id=$curso_id");
}

$curso = db_fetchOne("SELECT titulo FROM cursos WHERE id=?", [$curso_id]);

// ── 3. Verificar inscripción aprobada ─────────────────────────────────────
$userId = session_userId();
$inscripcion = db_fetchOne("SELECT * FROM inscripciones WHERE user_id=? AND curso_id=? AND estado='aprobado'", [$userId, $curso_id]);
if (!$inscripcion) {
    session_setFlash('error', 'No tienes acceso a este curso.');
    redirect(BASE_URL . 'estudiante/mis-cursos.php');
}

// ── 4. Función segura para recalcular progreso (protegida con try/catch) ──
function recalcularProgreso($userId, $curso_id) {
    try {
        $total = db_fetchOne(
            "SELECT COUNT(cl.id) as c FROM clases cl JOIN modulos m ON cl.modulo_id=m.id WHERE m.curso_id=?",
            [$curso_id]
        )['c'] ?? 0;

        $completadas = db_fetchOne(
            "SELECT COUNT(*) as c FROM clases_completadas WHERE user_id=? AND curso_id=?",
            [$userId, $curso_id]
        )['c'] ?? 0;

        $porcentaje = ($total > 0) ? (int)round(($completadas / $total) * 100) : 0;
        $estado = 'Inscrito';
        if ($porcentaje > 0 && $porcentaje < 100) $estado = 'En progreso';
        if ($porcentaje === 100) $estado = 'En progreso'; // 100% habilita examen, pero no es Aprobado aún

        $actual = db_fetchOne(
            "SELECT estado_curso FROM progreso_estudiantes WHERE usuario_id=? AND curso_id=?",
            [$userId, $curso_id]
        );

        if ($actual && $actual['estado_curso'] === 'Aprobado') {
            db_execute(
                "UPDATE progreso_estudiantes SET progreso_porcentaje=? WHERE usuario_id=? AND curso_id=?",
                [$porcentaje, $userId, $curso_id]
            );
            return $porcentaje;
        }

        $existe = db_fetchOne("SELECT id FROM progreso_estudiantes WHERE usuario_id=? AND curso_id=?", [$userId, $curso_id]);
        if ($existe) {
            db_execute(
                "UPDATE progreso_estudiantes SET progreso_porcentaje=?, estado_curso=? WHERE usuario_id=? AND curso_id=?",
                [$porcentaje, $estado, $userId, $curso_id]
            );
        } else {
            db_execute(
                "INSERT INTO progreso_estudiantes (usuario_id, curso_id, progreso_porcentaje, estado_curso) VALUES (?,?,?,?)",
                [$userId, $curso_id, $porcentaje, $estado]
            );
        }
        return $porcentaje;
    } catch (Exception $e) {
        // Tablas inexistentes, retornar 0 sin romper
        return 0;
    }
}

// ── 5. Procesar acción de Marcar / Desmarcar Completada ───────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_completada'])) {
    validate_csrf();
    try {
        $ya_completada = db_fetchOne(
            "SELECT id FROM clases_completadas WHERE user_id=? AND clase_id=?",
            [$userId, $clase_id]
        );
        if ($ya_completada) {
            db_execute("DELETE FROM clases_completadas WHERE user_id=? AND clase_id=?", [$userId, $clase_id]);
        } else {
            db_execute(
                "INSERT IGNORE INTO clases_completadas (user_id, clase_id, curso_id) VALUES (?,?,?)",
                [$userId, $clase_id, $curso_id]
            );
        }
        recalcularProgreso($userId, $curso_id);
    } catch (Exception $e) {
        session_setFlash('error', 'Error al guardar el progreso. Asegúrate de haber ejecutado el setup de la base de datos.');
    }
    redirect(BASE_URL . "estudiante/ver-clase.php?id={$curso_id}&clase={$clase_id}");
}

// ── 6. Obtener progreso y estado actual ───────────────────────────────────
$completada = false;
$progreso = 0;
$completadasIds = [];

try {
    $completada = (bool)db_fetchOne(
        "SELECT id FROM clases_completadas WHERE user_id=? AND clase_id=?",
        [$userId, $clase_id]
    );

    $progresoRow = db_fetchOne(
        "SELECT progreso_porcentaje FROM progreso_estudiantes WHERE usuario_id=? AND curso_id=?",
        [$userId, $curso_id]
    );
    $progreso = $progresoRow ? (int)$progresoRow['progreso_porcentaje'] : 0;

    $completadasIds = array_column(
        db_fetchAll("SELECT clase_id FROM clases_completadas WHERE user_id=? AND curso_id=?", [$userId, $curso_id]),
        'clase_id'
    );
} catch (Exception $e) {
    // Fallback silencioso si las tablas no existen
}

// ── 7. Navegación entre clases ────────────────────────────────────────────
$todasClases = db_fetchAll(
    "SELECT cl.id, cl.titulo FROM clases cl JOIN modulos m ON cl.modulo_id=m.id WHERE m.curso_id=? ORDER BY m.orden, cl.orden",
    [$curso_id]
);
$claseIds = array_column($todasClases, 'id');
$currentPos = array_search($clase_id, $claseIds);
$prevClase  = ($currentPos !== false && $currentPos > 0) ? $claseIds[$currentPos - 1] : null;
$nextClase  = ($currentPos !== false && $currentPos < count($claseIds) - 1) ? $claseIds[$currentPos + 1] : null;

// ── 8. Lógica del Reproductor de Video ────────────────────────────────────
$videoHtml = '';
if (!empty($clase['video_url'])) {
    $url = $clase['video_url'];
    if (strpos($url, 'youtube.com/watch?v=') !== false) {
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $vid = $params['v'] ?? '';
        $videoHtml = '<iframe style="width:100%;aspect-ratio:16/9;border:none;" src="https://www.youtube.com/embed/' . $vid . '?rel=0" allowfullscreen></iframe>';
    } elseif (strpos($url, 'youtu.be/') !== false) {
        $vid = trim(parse_url($url, PHP_URL_PATH), '/');
        $videoHtml = '<iframe style="width:100%;aspect-ratio:16/9;border:none;" src="https://www.youtube.com/embed/' . $vid . '?rel=0" allowfullscreen></iframe>';
    } elseif (strpos($url, 'vimeo.com/') !== false) {
        $vid = trim(parse_url($url, PHP_URL_PATH), '/');
        $videoHtml = '<iframe style="width:100%;aspect-ratio:16/9;border:none;" src="https://player.vimeo.com/video/' . $vid . '" allowfullscreen></iframe>';
    } else {
        $videoHtml = '<video style="width:100%;aspect-ratio:16/9;" controls><source src="' . e($url) . '" type="video/mp4"></video>';
    }
}

$titulo = $clase['titulo'] ?? 'Clase';
include __DIR__ . '/header.php';
?>

<!-- ══ BREADCRUMB Y PROGRESO GLOBAL ═══════════════════════════════════════ -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <a href="<?= BASE_URL ?>estudiante/ver-curso.php?id=<?= $curso_id ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Volver al curso
    </a>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small fw-500">Progreso del curso:</span>
        <div class="progress rounded-pill" style="width:140px; height:10px;">
            <div id="barraProgresoTop" class="progress-bar <?= $progreso >= 100 ? 'bg-success' : 'bg-primary' ?> rounded-pill"
                 role="progressbar"
                 style="width:<?= $progreso ?>%; transition: width 0.8s ease;"
                 aria-valuenow="<?= $progreso ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <span id="textoProgresoTop" class="fw-bold small <?= $progreso >= 100 ? 'text-success' : 'text-primary' ?>"><?= $progreso ?>%</span>
    </div>
</div>

<div class="row g-4">

    <!-- ── Columna Principal: Video + Controles ──────────────────────────── -->
    <div class="col-lg-8">

        <!-- Reproductor -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
            <?php if ($videoHtml): ?>
                <div class="ratio ratio-16x9 bg-dark"><?= $videoHtml ?></div>
            <?php else: ?>
                <div style="height:380px;background:#1a1a2e;display:flex;align-items:center;justify-content:center;border-radius:16px;">
                    <div class="text-center text-white opacity-50">
                        <i class="bi bi-play-circle" style="font-size:72px;"></i>
                        <p class="mt-3 fw-500">Sin video disponible para esta clase</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Información y Checklist -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-3">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                <div>
                    <h4 class="fw-bold mb-1"><?= e($clase['titulo']) ?></h4>
                    <span class="badge bg-light text-secondary border">
                        <i class="bi bi-camera-video me-1"></i>Clase de video
                    </span>
                </div>
                <!-- Botón de Completada compatible con AJAX/Fetch -->
                <button type="button" id="btnMarcarCompletada"
                        class="btn <?= $completada ? 'btn-success' : 'btn-outline-success' ?> rounded-pill fw-bold px-4 transition-all"
                        data-curso-id="<?= $curso_id ?>" 
                        data-leccion-id="<?= $clase_id ?>">
                    <?php if ($completada): ?>
                        <i class="bi bi-check-circle-fill me-2"></i>¡Lección completada!
                    <?php else: ?>
                        <i class="bi bi-circle me-2"></i>Marcar como completada
                    <?php endif; ?>
                </button>
            </div>

            <?php if (!empty($clase['descripcion'])): ?>
                <hr class="my-3">
                <p class="text-muted mb-0" style="line-height:1.7;"><?= nl2br(e($clase['descripcion'])) ?></p>
            <?php endif; ?>
        </div>

        <!-- Navegación Anterior/Siguiente -->
        <div class="d-flex justify-content-between gap-3 mt-2">
            <?php if ($prevClase): ?>
                <a href="ver-clase.php?id=<?= $curso_id ?>&clase=<?= $prevClase ?>"
                   class="btn btn-outline-primary rounded-pill flex-fill text-truncate">
                    <i class="bi bi-arrow-left me-1"></i>Clase anterior
                </a>
            <?php else: ?>
                <div class="flex-fill"></div>
            <?php endif; ?>

            <?php if ($nextClase): ?>
                <a href="ver-clase.php?id=<?= $curso_id ?>&clase=<?= $nextClase ?>"
                   class="btn btn-primary rounded-pill flex-fill text-truncate">
                    Siguiente clase<i class="bi bi-arrow-right ms-1"></i>
                </a>
            <?php else: ?>
                <a href="ver-curso.php?id=<?= $curso_id ?>"
                   class="btn btn-outline-secondary rounded-pill flex-fill text-truncate">
                    <i class="bi bi-list-stars me-1"></i>Ver contenido del curso
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Columna Lateral: Info + Lista de clases ────────────────────────── -->
    <div class="col-lg-4">

        <!-- Card de Información -->
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-1">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-info-circle-fill text-primary me-2"></i>Información</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <p class="mb-2 small"><strong>Curso:</strong> <?= e($curso['titulo'] ?? '') ?></p>
                <?php if (!empty($clase['duracion'])): ?>
                    <p class="mb-2 small"><strong>Duración:</strong> <?= e($clase['duracion']) ?></p>
                <?php endif; ?>
                
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-500 text-muted">Avance del curso</small>
                        <small id="textoProgresoSide" class="fw-bold <?= $progreso >= 100 ? 'text-success' : '' ?>"><?= $progreso ?>%</small>
                    </div>
                    <div class="progress rounded-pill" style="height:8px;">
                        <div id="barraProgresoSide" class="progress-bar <?= $progreso >= 100 ? 'bg-success' : 'bg-primary' ?> rounded-pill"
                             style="width:<?= $progreso ?>%; transition: width 0.8s ease;" role="progressbar"></div>
                    </div>
                    <?php if ($progreso >= 100): ?>
                        <div class="mt-3 text-center">
                            <a href="<?= BASE_URL ?>estudiante/examen.php?id=<?= $curso_id ?>"
                               class="btn btn-warning rounded-pill w-100 fw-bold pulse-btn">
                                <i class="bi bi-trophy-fill me-2"></i>¡Presentar Examen!
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Lista de Clases -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-1">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-check text-primary me-2"></i>Clases del curso</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush rounded-4">
                    <?php foreach ($todasClases as $cl):
                        $isActive  = ($cl['id'] == $clase_id);
                        $isDone    = in_array($cl['id'], $completadasIds);
                    ?>
                    <li class="list-group-item border-0 px-4 py-3 <?= $isActive ? 'active-clase-item' : '' ?>">
                        <a href="ver-clase.php?id=<?= $curso_id ?>&clase=<?= $cl['id'] ?>"
                           class="d-flex align-items-center gap-3 text-decoration-none <?= $isActive ? 'text-white' : 'text-dark' ?>">
                            <span class="clase-status-icon">
                                <?php if ($isDone): ?>
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <?php elseif ($isActive): ?>
                                    <i class="bi bi-play-circle-fill text-white fs-5"></i>
                                <?php else: ?>
                                    <i class="bi bi-circle text-muted fs-5"></i>
                                <?php endif; ?>
                            </span>
                            <span class="small fw-500 text-truncate" style="max-width:160px;"><?= e($cl['titulo']) ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    </div>
</div>

<style>
.active-clase-item {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border-radius: 0;
}
.btn-completada-toggle { transition: all 0.3s ease; }
.btn-completada-toggle:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(25,135,84,0.25); }
.pulse-btn { animation: pulse 2s infinite; }
@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(255,193,7,0.4); }
    50%       { box-shadow: 0 0 0 8px rgba(255,193,7,0); }
}
</style>

<!-- ── SCRIPT AJAX PREMIUM PARA MARCAR COMPLETADA ── -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnCompletar = document.getElementById('btnMarcarCompletada');
    
    if (!btnCompletar) return;

    btnCompletar.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Evitar doble clic
        if (this.disabled) return;
        this.disabled = true;
        
        // Efecto visual de carga
        const originalText = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Guardando...';
        
        const cursoId = this.dataset.cursoId;
        const leccionId = this.dataset.leccionId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const formData = new FormData();
        formData.append('curso_id', cursoId);
        formData.append('leccion_id', leccionId);
        if (csrfToken) formData.append('csrf_token', csrfToken);

        fetch('guardar_progreso.php', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en el servidor');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // A) Cambiar diseño del botón
                btnCompletar.classList.remove('btn-outline-success', 'btn-primary');
                btnCompletar.classList.add('btn-success');
                btnCompletar.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>¡Lección completada!';
                
                // B) Animar las barras de progreso
                const barraTop = document.getElementById('barraProgresoTop');
                const textoTop = document.getElementById('textoProgresoTop');
                const barraSide = document.getElementById('barraProgresoSide');
                const textoSide = document.getElementById('textoProgresoSide');
                
                if (data.nuevo_porcentaje !== undefined) {
                    if (barraTop) barraTop.style.width = data.nuevo_porcentaje + '%';
                    if (barraSide) barraSide.style.width = data.nuevo_porcentaje + '%';
                    
                    if (textoTop) {
                        textoTop.innerText = data.nuevo_porcentaje + '%';
                        if (data.nuevo_porcentaje >= 100) {
                            textoTop.classList.replace('text-primary', 'text-success');
                            if(barraTop) barraTop.classList.replace('bg-primary', 'bg-success');
                        }
                    }
                    if (textoSide) {
                        textoSide.innerText = data.nuevo_porcentaje + '%';
                        if (data.nuevo_porcentaje >= 100) {
                            textoSide.classList.add('text-success');
                            if(barraSide) barraSide.classList.replace('bg-primary', 'bg-success');
                        }
                    }
                }
                
                // C) Redirección suave
                if (data.siguiente_url) {
                    setTimeout(() => {
                        document.body.style.transition = "opacity 0.4s";
                        document.body.style.opacity = 0;
                        setTimeout(() => {
                            window.location.href = data.siguiente_url;
                        }, 400);
                    }, 600); // Pequeña pausa para apreciar la animación
                } else {
                    btnCompletar.disabled = false;
                    // Forzar mostrar el botón de examen dinámicamente si llega al 100% y no recarga
                    if (data.nuevo_porcentaje >= 100) {
                        setTimeout(() => window.location.reload(), 600);
                    }
                }
            } else {
                alert(data.error || 'No se pudo registrar el avance.');
                btnCompletar.innerHTML = originalText;
                btnCompletar.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión con guardar_progreso.php');
            btnCompletar.innerHTML = originalText;
            btnCompletar.disabled = false;
        });
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
