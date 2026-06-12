<div class="hero-panel">
  <h4><i class="bi bi-shield-lock me-2"></i>Restablecer contraseña</h4>
  <p class="sub">Elige una nueva contraseña para tu cuenta</p>
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
  <?php else: ?>
  <form method="post" action="<?= url('restablecer-contrasena/' . e($token)) ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Nueva contraseña</label>
      <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required minlength="8">
    </div>
    <div class="mb-3">
      <label class="form-label">Confirmar contraseña</label>
      <input type="password" name="password_confirm" class="form-control" placeholder="Repite la contraseña" required minlength="8">
    </div>
    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i>Cambiar contraseña</button>
  </form>
  <?php endif; ?>
</div>
