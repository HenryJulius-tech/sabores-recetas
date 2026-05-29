<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Categorías</h1>
</div>
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card-modern">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Nueva Categoría</h5>
                <form class="form-modern" method="post" action="<?= url('categorias/guardar') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn-modern btn-modern-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7 mb-4">
        <div class="card-modern">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-modern mb-0">
                        <thead><tr><th>Nombre</th><th>Cursos</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php if (empty($categorias)): ?>
                            <tr><td colspan="3"><div class="empty-state"><i class="bi bi-tags"></i><p>No hay categorías</p></div></td></tr>
                            <?php else: ?>
                            <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td class="fw-bold"><?= e($cat['name']) ?></td>
                                <td><span class="badge bg-light text-dark"><?= $cat['course_count'] ?? 0 ?> cursos</span></td>
                                <td>
                                    <form method="post" action="<?= url('categorias/eliminar/' . $cat['id']) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar categoría?')">
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
    </div>
</div>
