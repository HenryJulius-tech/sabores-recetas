<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <h1 class="page-title mb-4"><?= $curso ? 'Editar' : 'Nuevo' ?> Curso</h1>
        <div class="card-modern">
            <div class="card-body p-4">
                <form class="form-modern" method="post" action="<?= url($curso ? 'cursos/actualizar/' . $curso['id'] : 'cursos/guardar') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" class="form-control" value="<?= e($curso['title'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= selected($curso['category_id'] ?? '', $cat['id']) ?>><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" rows="4"><?= e($curso['description'] ?? '') ?></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Precio ($)</label>
                            <input type="text" name="price" class="form-control" value="<?= number_format((float)($curso['price'] ?? 0), 0, ',', '.') ?>" required oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Duración (horas)</label>
                            <input type="text" name="duration" class="form-control" value="<?= e($curso['duration'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nivel</label>
                            <select name="level" class="form-select" required>
                                <option value="principiante" <?= selected($curso['level'] ?? '', 'principiante') ?>>Principiante</option>
                                <option value="intermedio" <?= selected($curso['level'] ?? '', 'intermedio') ?>>Intermedio</option>
                                <option value="avanzado" <?= selected($curso['level'] ?? '', 'avanzado') ?>>Avanzado</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instructor</label>
                        <input type="text" name="instructor" class="form-control" value="<?= e($curso['instructor'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="featured" class="form-check-input" value="1" id="featured" <?= !empty($curso['featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="featured">Curso destacado</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen</label>
                        <input type="file" name="image" class="form-control" accept="image/*" data-preview="preview">
                        <?php if ($curso && !empty($curso['image_url'])): ?>
                            <div class="mt-2"><img id="preview" src="<?= asset('uploads/' . $curso['image_url']) ?>" class="image-preview"></div>
                        <?php else: ?>
                            <div class="mt-2"><img id="preview" class="image-preview" style="display:none"></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-modern btn-modern-primary"><?= $curso ? 'Actualizar' : 'Guardar' ?></button>
                        <a href="<?= url('cursos') ?>" class="btn-modern btn-modern-outline">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
