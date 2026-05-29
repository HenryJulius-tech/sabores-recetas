<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <h4 class="fw-bold mb-4"><?= $usuario ? 'Editar' : 'Nuevo' ?> Usuario</h4>
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="post" action="<?= url($usuario ? 'usuarios/actualizar/' . $usuario['id'] : 'usuarios/guardar') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Usuario</label>
                            <input type="text" name="username" class="form-control" value="<?= e($usuario['username'] ?? '') ?>" <?= $usuario ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= e($usuario['email'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <select name="role" class="form-select">
                                <option value="client" <?= selected($usuario['role']??'', 'client') ?>>Cliente</option>
                                <option value="worker" <?= selected($usuario['role']??'', 'worker') ?>>Trabajador</option>
                                <option value="admin" <?= selected($usuario['role']??'', 'admin') ?>>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control" value="<?= e($usuario['phone'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="address" class="form-control" value="<?= e($usuario['address'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= $usuario ? 'Nueva ' : '' ?>Contraseña</label>
                            <input type="password" name="password" class="form-control" <?= $usuario ? '' : 'required' ?> minlength="6">
                            <?php if ($usuario): ?><small class="text-muted">Dejar en blanco para mantener</small><?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success"><?= $usuario ? 'Actualizar' : 'Guardar' ?></button>
                        <a href="<?= url('usuarios') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
