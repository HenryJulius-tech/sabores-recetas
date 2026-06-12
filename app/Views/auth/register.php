<div class="hero-panel">
  <h4><i class="bi bi-person-plus me-2"></i>Crear cuenta</h4>
  <p class="sub">Regístrate y accede a todos los cursos</p>
  <form method="post" action="<?= url('register') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Usuario</label>
      <input type="text" name="username" class="form-control" placeholder="Elige un usuario" value="<?= e(old('username')) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" placeholder="tu@correo.com" value="<?= e(old('email')) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6">
    </div>
    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-person-plus"></i>Crear cuenta</button>
    <div class="divider"><hr><span>o</span><hr></div>
    <a href="<?= url('login') ?>" class="btn btn-outline-primary w-100"><i class="bi bi-box-arrow-in-right"></i>Ya tengo cuenta</a>
  </form>
</div>
