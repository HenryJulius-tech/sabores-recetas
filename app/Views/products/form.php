<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <h1 class="page-title mb-4"><?= $producto ? 'Editar' : 'Nuevo' ?> Producto</h1>
        <div class="card-modern">
            <div class="card-body p-4">
                <form class="form-modern" method="post" action="<?= url($producto ? 'productos/actualizar/' . $producto['id'] : 'productos/guardar') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" value="<?= e($producto['name'] ?? '') ?>" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Precio ($)</label>
                            <input type="text" name="price" class="form-control" value="<?= number_format((float)($producto['price'] ?? 0), 0, ',', '.') ?>" required oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" value="<?= (int)($producto['stock'] ?? 0) ?>" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" rows="3"><?= e($producto['description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen</label>
                        <input type="file" name="image" class="form-control" accept="image/*" data-preview="preview">
                        <?php if ($producto && !empty($producto['image_url'])): ?>
                            <div class="mt-2"><img id="preview" src="<?= asset('uploads/' . $producto['image_url']) ?>" class="image-preview"></div>
                        <?php else: ?>
                            <div class="mt-2"><img id="preview" class="image-preview" style="display:none"></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-modern btn-modern-primary"><?= $producto ? 'Actualizar' : 'Guardar' ?></button>
                        <a href="<?= url('productos') ?>" class="btn-modern btn-modern-outline">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
