<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$modulo_id = (int)($_GET['modulo_id'] ?? 0);
$modulo = db_fetchOne("SELECT m.*, c.titulo as curso_titulo, c.id as curso_id FROM modulos m JOIN cursos c ON m.curso_id=c.id WHERE m.id=?", [$modulo_id]);
if (!$modulo) { session_setFlash('error', 'Módulo no encontrado'); redirect(BASE_URL . 'admin/cursos/index.php'); }

if (isset($_GET['eliminar'])) {
    db_execute("DELETE FROM clases WHERE id=? AND modulo_id=?", [(int)$_GET['eliminar'], $modulo_id]);
    session_setFlash('success', 'Clase eliminada');
    redirect(BASE_URL . "admin/clases/index.php?modulo_id=$modulo_id");
}

if ($_POST && isset($_POST['guardar_clase'])) {
    validate_csrf();
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    $duracion = trim($_POST['duracion'] ?? '');
    if ($titulo) {
        $maxOrd = db_fetchOne("SELECT MAX(orden) as mx FROM clases WHERE modulo_id=?", [$modulo_id]);
        db_insert("INSERT INTO clases (modulo_id, titulo, descripcion, video_url, duracion, orden) VALUES (?,?,?,?,?,?)",
            [$modulo_id, $titulo, $descripcion, $video_url, $duracion, ($maxOrd['mx'] ?? 0) + 1]);
        session_setFlash('success', 'Clase creada');
        redirect(BASE_URL . "admin/clases/index.php?modulo_id=$modulo_id");
    }
}

$clases = db_fetchAll("SELECT * FROM clases WHERE modulo_id=? ORDER BY orden", [$modulo_id]);
$titulo = 'Clases - ' . $modulo['titulo'];
include __DIR__ . '/../header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw700" style="color:#1a1a2e;">Clases</h2>
        <p class="text-muted">Módulo: <strong><?= e($modulo['titulo']) ?></strong> — Curso: <?= e($modulo['curso_titulo']) ?></p>
    </div>
    <a href="<?= BASE_URL ?>admin/modulos/index.php?curso_id=<?= $modulo['curso_id'] ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw600">Nueva Clase</div>
            <div class="card-body">
                <form method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw500">Título</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw500">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw500">URL del video</label>
                        <input type="text" name="video_url" class="form-control" placeholder="https://youtube.com/...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw500">Duración</label>
                        <input type="text" name="duracion" class="form-control" placeholder="Ej: 15 min">
                    </div>
                    <button type="submit" name="guardar_clase" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Crear Clase</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw600">Clases del Módulo</div>
            <div class="card-body p-0">
                <?php if (empty($clases)): ?>
                    <p class="text-muted p-3 mb-0">No hay clases aún.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($clases as $cl): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= e($cl['titulo']) ?></strong>
                                    <?php if ($cl['duracion']): ?><br><small class="text-muted"><i class="bi bi-clock"></i> <?= e($cl['duracion']) ?></small><?php endif; ?>
                                </div>
                                <a href="?modulo_id=<?= $modulo_id ?>&eliminar=<?= $cl['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar clase?')"><i class="bi bi-trash"></i></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
