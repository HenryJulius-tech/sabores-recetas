<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $curso = db_fetchOne("SELECT imagen FROM cursos WHERE id=?", [$id]);
    if ($curso && $curso['imagen']) {
        $file = ROOT . '/public/uploads/cursos/' . $curso['imagen'];
        if (file_exists($file)) unlink($file);
    }
    db_execute("DELETE FROM cursos WHERE id=?", [$id]);
    session_setFlash('success', 'Curso eliminado');
    redirect(BASE_URL . 'admin/cursos/index.php');
}

$cursos = db_fetchAll("SELECT c.*, (SELECT COUNT(*) FROM inscripciones WHERE curso_id=c.id) as inscritos FROM cursos c ORDER BY c.created_at DESC");
$titulo = 'Cursos';
include __DIR__ . '/../header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw700" style="color:#1a1a2e;">Cursos</h2>
    <a href="crear.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nuevo Curso</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Curso</th>
                    <th>Precio</th>
                    <th>Nivel</th>
                    <th>Inscritos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cursos as $c): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= upload('cursos/' . $c['imagen']) ?>" style="width:48px;height:48px;border-radius:8px;object-fit:cover;">
                                <div>
                                    <strong><?= e($c['titulo']) ?></strong>
                                </div>
                            </div>
                        </td>
                        <td>$<?= number_format($c['precio'], 0) ?></td>
                        <td><?= nivelBadge($c['nivel']) ?></td>
                        <td><?= $c['inscritos'] ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="editar.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <a href="<?= BASE_URL ?>admin/modulos/index.php?curso_id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-list-ul"></i></a>
                                <a href="?eliminar=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar curso?')"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
