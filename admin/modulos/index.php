<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$curso_id = (int)($_GET['curso_id'] ?? 0);
$curso = db_fetchOne("SELECT * FROM cursos WHERE id=?", [$curso_id]);
if (!$curso) { session_setFlash('error', 'Curso no encontrado'); redirect(BASE_URL . 'admin/cursos/index.php'); }

if (isset($_GET['eliminar'])) {
    db_execute("DELETE FROM modulos WHERE id=? AND curso_id=?", [(int)$_GET['eliminar'], $curso_id]);
    session_setFlash('success', 'Módulo eliminado');
    redirect(BASE_URL . "admin/modulos/index.php?curso_id=$curso_id");
}

if ($_POST && isset($_POST['guardar_modulo'])) {
    validate_csrf();
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    if ($titulo) {
        $maxOrd = db_fetchOne("SELECT MAX(orden) as mx FROM modulos WHERE curso_id=?", [$curso_id]);
        db_insert("INSERT INTO modulos (curso_id, titulo, descripcion, orden) VALUES (?,?,?,?)",
            [$curso_id, $titulo, $descripcion, ($maxOrd['mx'] ?? 0) + 1]);
        session_setFlash('success', 'Módulo creado');
        redirect(BASE_URL . "admin/modulos/index.php?curso_id=$curso_id");
    }
}

$modulos = db_fetchAll("SELECT * FROM modulos WHERE curso_id=? ORDER BY orden", [$curso_id]);
$titulo = 'Módulos de ' . $curso['titulo'];
include __DIR__ . '/../header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw700" style="color:#1a1a2e;">Módulos</h2>
        <p class="text-muted">Curso: <strong><?= e($curso['titulo']) ?></strong></p>
    </div>
    <a href="<?= BASE_URL ?>admin/cursos/index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw600">Nuevo Módulo</div>
            <div class="card-body">
                <form method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw500">Título del módulo</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw500">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" name="guardar_modulo" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Crear Módulo</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw600">Módulos del Curso</div>
            <div class="card-body p-0">
                <?php if (empty($modulos)): ?>
                    <p class="text-muted p-3 mb-0">No hay módulos aún.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($modulos as $m): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= e($m['titulo']) ?></strong>
                                    <?php if ($m['descripcion']): ?><br><small class="text-muted"><?= e($m['descripcion']) ?></small><?php endif; ?>
                                    <div class="mt-1">
                                        <a href="<?= BASE_URL ?>admin/clases/index.php?modulo_id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-play-circle"></i> Clases</a>
                                    </div>
                                </div>
                                <a href="?curso_id=<?= $curso_id ?>&eliminar=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar módulo?')"><i class="bi bi-trash"></i></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
