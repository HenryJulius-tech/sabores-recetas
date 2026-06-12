<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$curso = db_fetchOne("SELECT * FROM cursos WHERE id=?", [$id]);
if (!$curso) { session_setFlash('error', 'Curso no encontrado'); redirect(BASE_URL . 'admin/cursos/index.php'); }

$error = '';

if ($_POST) {
    validate_csrf();
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $nivel = $_POST['nivel'] ?? 'principiante';
    $duracion = trim($_POST['duracion'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $imagen = $curso['imagen'];

    if (!$titulo) $error = 'El título es obligatorio';

    if (!$error) {
        if (!empty($_FILES['imagen']['name'])) {
            $upload = validateUpload($_FILES['imagen']);
            if ($upload['valid']) {
                if ($curso['imagen']) {
                    $old = ROOT . '/public/uploads/cursos/' . $curso['imagen'];
                    if (file_exists($old)) unlink($old);
                }
                move_uploaded_file($_FILES['imagen']['tmp_name'], ROOT . '/public/uploads/cursos/' . $upload['name']);
                $imagen = $upload['name'];
            } else {
                $error = $upload['error'];
            }
        }

        if (!$error) {
            db_execute("UPDATE cursos SET titulo=?, descripcion=?, precio=?, imagen=?, nivel=?, duracion=?, instructor=? WHERE id=?",
                [$titulo, $descripcion, $precio, $imagen, $nivel, $duracion, $instructor, $id]);
            session_setFlash('success', 'Curso actualizado');
            redirect(BASE_URL . 'admin/cursos/index.php');
        }
    }
}

$titulo = 'Editar Curso';
include __DIR__ . '/../header.php';
?>
<div class="mb-4">
    <h2 class="fw700" style="color:#1a1a2e;">Editar Curso</h2>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw500">Título</label>
                    <input type="text" name="titulo" class="form-control" value="<?= e($curso['titulo']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw500">Precio ($)</label>
                    <input type="number" step="0.01" min="0" name="precio" class="form-control" value="<?= $curso['precio'] ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw500">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="4"><?= e($curso['descripcion']) ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw500">Nivel</label>
                    <select name="nivel" class="form-select">
                        <option value="principiante" <?= selected('principiante', $curso['nivel']) ?>>Principiante</option>
                        <option value="intermedio" <?= selected('intermedio', $curso['nivel']) ?>>Intermedio</option>
                        <option value="avanzado" <?= selected('avanzado', $curso['nivel']) ?>>Avanzado</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw500">Duración</label>
                    <input type="text" name="duracion" class="form-control" value="<?= e($curso['duracion']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw500">Instructor</label>
                    <input type="text" name="instructor" class="form-control" value="<?= e($curso['instructor']) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw500">Imagen</label>
                    <?php if ($curso['imagen']): ?>
                        <div class="mb-2"><img src="<?= upload_url('cursos/' . $curso['imagen']) ?>" style="max-height:120px;border-radius:8px;"></div>
                    <?php endif; ?>
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Actualizar</button>
                    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
