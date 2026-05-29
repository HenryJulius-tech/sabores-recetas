<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Cursos</h1>
    <a href="<?= url('cursos/crear') ?>" class="btn-modern btn-modern-primary"><i class="bi bi-plus-lg me-2"></i>Nuevo Curso</a>
</div>
<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern mb-0">
                <thead><tr><th>Imagen</th><th>Título</th><th>Categoría</th><th>Precio</th><th>Instructor</th><th>Nivel</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php if (empty($cursos)): ?>
                    <tr><td colspan="7"><div class="empty-state"><i class="bi bi-book"></i><p>No hay cursos registrados</p></div></td></tr>
                    <?php else: ?>
                    <?php foreach ($cursos as $c): ?>
                    <tr>
                        <td>
                            <?php if (!empty($c['image_url'])): ?>
                                <img src="<?= asset('uploads/' . $c['image_url']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                            <?php else: ?>
                                <div class="bg-light rounded" style="width:50px;height:50px;display:flex;align-items:center;justify-content:center"><i class="bi bi-book text-muted"></i></div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold"><?= e($c['title']) ?></td>
                        <td><?= e($c['category_name']) ?></td>
                        <td><?= format_cop($c['price']) ?></td>
                        <td><?= e($c['instructor']) ?></td>
                        <td><?= levelBadge($c['level']) ?></td>
                        <td>
                            <a href="<?= url('cursos/editar/' . $c['id']) ?>" class="btn-modern btn-modern-outline btn-modern-sm"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= url('cursos/eliminar/' . $c['id']) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar curso?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-modern btn-modern-danger btn-modern-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
