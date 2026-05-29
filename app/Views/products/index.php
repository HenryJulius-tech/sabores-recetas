<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Productos</h1>
    <a href="<?= url('productos/crear') ?>" class="btn-modern btn-modern-primary"><i class="bi bi-plus-lg me-2"></i>Nuevo Producto</a>
</div>
<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern mb-0">
                <thead><tr><th>ID</th><th>Imagen</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                    <tr><td colspan="6"><div class="empty-state"><i class="bi bi-box"></i><p>No hay productos registrados</p></div></td></tr>
                    <?php else: ?>
                    <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td>
                            <?php if (!empty($p['image_url'])): ?>
                                <img src="<?= asset('uploads/' . $p['image_url']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                            <?php else: ?>
                                <div class="bg-light rounded" style="width:50px;height:50px;display:flex;align-items:center;justify-content:center"><i class="bi bi-box text-muted"></i></div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold"><?= e($p['name']) ?></td>
                        <td><?= format_cop($p['price']) ?></td>
                        <td><?= stockBadge($p['stock']) ?></td>
                        <td>
                            <a href="<?= url('productos/editar/' . $p['id']) ?>" class="btn-modern btn-modern-outline btn-modern-sm"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= url('productos/eliminar/' . $p['id']) ?>" class="d-inline" id="del-<?= $p['id'] ?>" onsubmit="return confirm('¿Eliminar producto?')">
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
