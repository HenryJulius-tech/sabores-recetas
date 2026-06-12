<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
requireEstudiante();

$curso_id = (int)($_GET['id'] ?? 0);
$userId   = session_userId();

// Verificar inscripción aprobada
$inscripcion = db_fetchOne("SELECT * FROM inscripciones WHERE user_id=? AND curso_id=? AND estado='aprobado'", [$userId, $curso_id]);
if (!$inscripcion) { session_setFlash('error', 'No tienes acceso'); redirect(BASE_URL . 'estudiante/mis-cursos.php'); }

// Verificar que el progreso sea 100% o el curso ya esté aprobado
$progresoRow = db_fetchOne("SELECT progreso_porcentaje, estado_curso FROM progreso_estudiantes WHERE usuario_id=? AND curso_id=?", [$userId, $curso_id]);
$progreso    = $progresoRow ? (int)$progresoRow['progreso_porcentaje'] : 0;
$estadoCurso = $progresoRow ? $progresoRow['estado_curso'] : 'Inscrito';

if ($progreso < 100 && $estadoCurso !== 'Aprobado') {
    session_setFlash('error', 'Debes completar todas las clases antes de presentar el examen.');
    redirect(BASE_URL . "estudiante/ver-curso.php?id=$curso_id");
}

$curso = db_fetchOne("SELECT * FROM cursos WHERE id=?", [$curso_id]);
if (!$curso) { redirect(BASE_URL . 'estudiante/mis-cursos.php'); }

// ══ BANCO DE PREGUNTAS ══════════════════════════════════════════════════════
// Se pueden añadir más cursos por ID. Si el ID no coincide se usa el banco general.
$bancoPreguntas = [
    // Banco GENERAL de cocina (aplica a todos los cursos)
    'general' => [
        [
            'pregunta' => '¿Cuál es la temperatura interna segura para cocinar pollo según las normas de seguridad alimentaria?',
            'opciones' => ['60 °C', '74 °C', '85 °C', '50 °C'],
            'correcta' => 1, // índice 0-based
        ],
        [
            'pregunta' => '¿Qué técnica consiste en cocinar alimentos a temperatura muy baja durante un tiempo prolongado en agua?',
            'opciones' => ['Escalfado', 'Sous-vide', 'Salteado', 'Braseado'],
            'correcta' => 1,
        ],
        [
            'pregunta' => '¿Cuál es el espesante que se usa en la cocina oriental para dar brillo a las salsas?',
            'opciones' => ['Harina de trigo', 'Maicena (almidón de maíz)', 'Agar-agar', 'Pectina'],
            'correcta' => 1,
        ],
        [
            'pregunta' => '¿Qué método de cocción utiliza la convección de vapor para cocinar los alimentos?',
            'opciones' => ['Asado al horno', 'Freír en aceite', 'Cocción al vapor', 'Gratinar'],
            'correcta' => 2,
        ],
        [
            'pregunta' => '¿Cuál de los siguientes NO es un macronutriente?',
            'opciones' => ['Carbohidratos', 'Proteínas', 'Vitamina C', 'Grasas'],
            'correcta' => 2,
        ],
        [
            'pregunta' => '¿Qué reacción química produce el dorado y el sabor característico al sellar carnes a alta temperatura?',
            'opciones' => ['Oxidación', 'Reacción de Maillard', 'Caramelización', 'Hidrólisis'],
            'correcta' => 1,
        ],
        [
            'pregunta' => '¿Qué significa la técnica culinaria "mise en place"?',
            'opciones' => ['Presentación del plato', 'Preparar y organizar los ingredientes antes de cocinar', 'Condimentar la preparación', 'Emplatar con elegancia'],
            'correcta' => 1,
        ],
        [
            'pregunta' => '¿Cuál es la función principal de la sal en la cocción de pastas?',
            'opciones' => ['Reducir el tiempo de cocción', 'Sazonar la pasta desde dentro y elevar el punto de ebullición ligeramente', 'Evitar que se peguen', 'Endurecer la masa'],
            'correcta' => 1,
        ],
        [
            'pregunta' => '¿Qué cuchillo de chef es el más versátil y el más utilizado en cocina profesional?',
            'opciones' => ['Cuchillo de pan', 'Cuchillo de filetear', 'Cuchillo de chef (20 cm)', 'Cuchillo puntilla'],
            'correcta' => 2,
        ],
        [
            'pregunta' => '¿Qué tipo de harina tiene mayor contenido de proteína (gluten) y se usa para panes de estructura firme?',
            'opciones' => ['Harina de repostería (cake flour)', 'Harina de fuerza (bread flour)', 'Harina todo uso', 'Harina de arroz'],
            'correcta' => 1,
        ],
    ],
];

// Seleccionar banco de preguntas (específico del curso o general)
$banco = $bancoPreguntas[$curso_id] ?? $bancoPreguntas['general'];

// Mezclar y tomar 5 preguntas aleatorias con semilla fija por sesión para reproducibilidad
$semilla = $userId * 31 + $curso_id * 7;
srand($semilla);
shuffle($banco);
$preguntas = array_slice($banco, 0, 3);
srand(); // restaurar aleatoriedad

// ══ PROCESAR RESPUESTAS ════════════════════════════════════════════════════
$resultado     = null;
$puntaje       = 0;
$respuestas    = [];
$aprobado      = false;

if ($_POST && isset($_POST['enviar_examen'])) {
    validate_csrf();

    // Re-crear el mismo orden de preguntas usando la misma semilla
    srand($semilla);
    shuffle($banco);
    $preguntas = array_slice($banco, 0, 3);
    srand();

    $aciertos = 0;
    foreach ($preguntas as $i => $preg) {
        $respuestaEstudiante = isset($_POST['resp'][$i]) ? (int)$_POST['resp'][$i] : -1;
        $esCorrecta          = ($respuestaEstudiante === $preg['correcta']);
        if ($esCorrecta) $aciertos++;
        $respuestas[$i] = [
            'seleccionada' => $respuestaEstudiante,
            'correcta'     => $preg['correcta'],
            'ok'           => $esCorrecta,
        ];
    }

    $puntaje  = round(($aciertos / count($preguntas)) * 100);
    $aprobado = ($puntaje >= 80);
    $resultado = compact('aciertos', 'puntaje', 'aprobado');

    if ($aprobado && $estadoCurso !== 'Aprobado') {
        // Marcar curso como Aprobado en progreso_estudiantes
        $existe = db_fetchOne("SELECT id FROM progreso_estudiantes WHERE usuario_id=? AND curso_id=?", [$userId, $curso_id]);
        if ($existe) {
            db_execute(
                "UPDATE progreso_estudiantes SET estado_curso='Aprobado', progreso_porcentaje=100 WHERE usuario_id=? AND curso_id=?",
                [$userId, $curso_id]
            );
        } else {
            db_execute(
                "INSERT INTO progreso_estudiantes (usuario_id, curso_id, progreso_porcentaje, estado_curso) VALUES (?,?,100,'Aprobado')",
                [$userId, $curso_id]
            );
        }
        // Registrar logro en auditoría
        registrar_log(
            'Examen final aprobado',
            'Aprobó el examen del curso "' . $curso['titulo'] . '" (ID: ' . $curso_id . ') con un puntaje de ' . $puntaje . '%.'
        );
        $estadoCurso = 'Aprobado';
    }
}

$titulo = 'Examen Final – ' . $curso['titulo'];
include __DIR__ . '/header.php';
?>

<style>
/* ── Estilos del examen ── */
.exam-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    border-radius: 20px;
    padding: 2rem;
    color: #fff;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.exam-hero::before {
    content: '🏆';
    position: absolute;
    right: 2rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 5rem;
    opacity: 0.15;
}
.pregunta-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    margin-bottom: 1.5rem;
    transition: box-shadow 0.2s;
}
.pregunta-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
.opcion-label {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.85rem 1.1rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 0.5rem;
    font-weight: 500;
}
.opcion-label:hover { border-color: #667eea; background: #f5f7ff; }
.opcion-label input[type="radio"] { margin-top: 2px; accent-color: #667eea; }
/* Corrección visual post-envío */
.opcion-correcta  { border-color: #198754 !important; background: rgba(25,135,84,0.08) !important; }
.opcion-incorrecta { border-color: #dc3545 !important; background: rgba(220,53,69,0.08) !important; }
/* Resultado */
.resultado-card {
    border-radius: 20px;
    border: none;
    overflow: hidden;
}
.resultado-aprobado { background: linear-gradient(135deg, #d4edda, #f0fff4); }
.resultado-reprobado { background: linear-gradient(135deg, #f8d7da, #fff5f5); }
.puntaje-circle {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0 auto;
}
.circle-aprobado  { background: #198754; color: #fff; box-shadow: 0 4px 20px rgba(25,135,84,0.35); }
.circle-reprobado { background: #dc3545; color: #fff; box-shadow: 0 4px 20px rgba(220,53,69,0.35); }
</style>

<!-- ── Hero del examen ──────────────────────────────────────────────────── -->
<div class="exam-hero">
    <div class="d-flex align-items-center gap-2 mb-2">
        <a href="<?= BASE_URL ?>estudiante/ver-curso.php?id=<?= $curso_id ?>"
           class="btn btn-sm btn-outline-light rounded-pill">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
    <h2 class="fw-bold mb-1 mt-2">Examen Final</h2>
    <p class="mb-0 opacity-75 fs-6"><?= e($curso['titulo']) ?> · <?= count($preguntas) ?> preguntas · Aprobación ≥ 80%</p>
</div>

<?php if ($estadoCurso === 'Aprobado' && !$resultado): ?>
<!-- ── Ya aprobado anteriormente ───────────────────────────────────────── -->
<div class="card resultado-card resultado-aprobado shadow-sm p-5 text-center">
    <div class="puntaje-circle circle-aprobado mb-4">🏆</div>
    <h3 class="fw-bold text-success">¡Ya aprobaste este curso!</h3>
    <p class="text-muted">Has superado el examen final de <strong><?= e($curso['titulo']) ?></strong> con éxito. ¡Felicitaciones!</p>
    <a href="<?= BASE_URL ?>estudiante/mis-cursos.php" class="btn btn-success rounded-pill px-5 fw-bold mt-2">
        <i class="bi bi-grid-fill me-2"></i>Ir a Mis Cursos
    </a>
</div>

<?php elseif ($resultado): ?>
<!-- ── Resultado del examen ────────────────────────────────────────────── -->
<div class="card resultado-card <?= $aprobado ? 'resultado-aprobado' : 'resultado-reprobado' ?> shadow-sm mb-4">
    <div class="card-body p-5 text-center">
        <div class="puntaje-circle <?= $aprobado ? 'circle-aprobado' : 'circle-reprobado' ?> mb-4">
            <?= $puntaje ?>%
        </div>
        <?php if ($aprobado): ?>
            <h3 class="fw-bold text-success">¡Felicitaciones, aprobaste! 🎉</h3>
            <p class="text-muted mb-0">Obtuviste <strong><?= $resultado['aciertos'] ?> de <?= count($preguntas) ?></strong> respuestas correctas. Tu curso ha sido marcado como <strong>Aprobado</strong>.</p>
        <?php else: ?>
            <h3 class="fw-bold text-danger">¡No aprobaste esta vez!</h3>
            <p class="text-muted mb-0">Obtuviste <strong><?= $resultado['aciertos'] ?> de <?= count($preguntas) ?></strong> respuestas correctas. Necesitas al menos el 80% para aprobar. ¡Revisa el material y vuelve a intentarlo!</p>
        <?php endif; ?>
    </div>
</div>

<!-- ── Revisión de respuestas ─────────────────────────────────────────── -->
<h5 class="fw-bold mb-3"><i class="bi bi-clipboard2-check-fill text-primary me-2"></i>Revisión de respuestas</h5>
<?php foreach ($preguntas as $i => $preg): $r = $respuestas[$i]; ?>
<div class="pregunta-card card mb-3">
    <div class="card-body p-4">
        <div class="d-flex align-items-start gap-3 mb-3">
            <span class="badge <?= $r['ok'] ? 'bg-success' : 'bg-danger' ?> rounded-pill fs-6 px-3 py-2">
                <?= $r['ok'] ? '✓' : '✗' ?>
            </span>
            <h6 class="fw-bold mb-0"><?= e($preg['pregunta']) ?></h6>
        </div>
        <?php foreach ($preg['opciones'] as $j => $opc): ?>
        <div class="opcion-label
            <?= ($j === $preg['correcta']) ? 'opcion-correcta' : '' ?>
            <?= (!$r['ok'] && $j === $r['seleccionada']) ? 'opcion-incorrecta' : '' ?>">
            <?php if ($j === $preg['correcta']): ?>
                <i class="bi bi-check-circle-fill text-success mt-1"></i>
            <?php elseif (!$r['ok'] && $j === $r['seleccionada']): ?>
                <i class="bi bi-x-circle-fill text-danger mt-1"></i>
            <?php else: ?>
                <i class="bi bi-circle text-muted mt-1"></i>
            <?php endif; ?>
            <?= e($opc) ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<div class="d-flex gap-3 mt-4 flex-wrap">
    <?php if ($aprobado): ?>
        <a href="<?= BASE_URL ?>estudiante/mis-cursos.php" class="btn btn-success rounded-pill fw-bold px-5">
            <i class="bi bi-grid-fill me-2"></i>Ir a Mis Cursos
        </a>
    <?php else: ?>
        <a href="<?= BASE_URL ?>estudiante/ver-curso.php?id=<?= $curso_id ?>" class="btn btn-outline-primary rounded-pill fw-bold px-4">
            <i class="bi bi-book-fill me-2"></i>Repasar el Curso
        </a>
        <a href="<?= BASE_URL ?>estudiante/examen.php?id=<?= $curso_id ?>" class="btn btn-danger rounded-pill fw-bold px-4">
            <i class="bi bi-arrow-repeat me-2"></i>Volver a Intentarlo
        </a>
    <?php endif; ?>
</div>

<?php else: ?>
<!-- ── Formulario del Examen ──────────────────────────────────────────── -->
<form method="POST" id="examForm">
    <?= csrf_field() ?>

    <?php foreach ($preguntas as $i => $preg): ?>
    <div class="pregunta-card card">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">
                <span class="badge bg-primary rounded-pill me-2"><?= $i + 1 ?></span>
                <?= e($preg['pregunta']) ?>
            </h6>
            <div role="radiogroup">
                <?php foreach ($preg['opciones'] as $j => $opc): ?>
                <label class="opcion-label" for="resp_<?= $i ?>_<?= $j ?>">
                    <input type="radio"
                           name="resp[<?= $i ?>]"
                           id="resp_<?= $i ?>_<?= $j ?>"
                           value="<?= $j ?>"
                           required>
                    <?= e($opc) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Botón de envío -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mt-2">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <p class="fw-bold mb-1 text-dark">¿Listo para enviar?</p>
                <p class="text-muted small mb-0">Asegúrate de haber respondido las <?= count($preguntas) ?> preguntas antes de enviar.</p>
            </div>
            <button type="submit" name="enviar_examen"
                    class="btn btn-warning rounded-pill fw-bold px-5 py-2 fs-5"
                    onclick="return confirm('¿Confirmas el envío del examen? No podrás cambiar tus respuestas.')">
                <i class="bi bi-send-fill me-2"></i>Enviar Examen
            </button>
        </div>
    </div>
</form>

<script>
// Resaltar visualmente la opción seleccionada
document.addEventListener('change', function(e) {
    if (e.target.type === 'radio') {
        const group = document.querySelectorAll(`input[name="${e.target.name}"]`);
        group.forEach(r => r.closest('.opcion-label').style.borderColor = '');
        e.target.closest('.opcion-label').style.borderColor = '#667eea';
        e.target.closest('.opcion-label').style.background  = '#f0f2ff';
    }
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
