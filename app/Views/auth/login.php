<div class="hero-panel">
  <h4><i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión</h4>
  <p class="sub">Accede a tu cuenta para continuar</p>
  <form method="post" action="<?= url('login') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Usuario</label>
      <input type="text" name="username" class="form-control" placeholder="Tu usuario" value="<?= e(old('username')) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input type="password" name="password" class="form-control" placeholder="••••••" required>
    </div>
    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i>Ingresar</button>
    <div class="divider"><hr><span>o</span><hr></div>
    <a href="<?= url('register') ?>" class="btn btn-outline-primary w-100"><i class="bi bi-person-plus"></i>Crear cuenta</a>
    <div class="mt-3 text-center">
      <a href="<?= url('recuperar-contrasena') ?>" class="text-muted small">¿Olvidaste tu contraseña?</a>
    </div>
  </form>
</div>
