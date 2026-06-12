<div class="hero-panel">
  <h4><i class="bi bi-key me-2"></i>Recuperar contraseña</h4>
  <p class="sub">Ingresa tu correo para recuperar tu acceso</p>
  <form method="post" action="<?= url('recuperar-contrasena') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Correo electrónico</label>
      <input type="email" name="email" class="form-control" placeholder="tu@correo.com" required>
    </div>
    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send"></i>Enviar enlace</button>
    <div class="mt-3 text-center">
      <a href="<?= url('login') ?>" class="text-muted small"><i class="bi bi-arrow-left"></i> Volver al inicio de sesión</a>
    </div>
  </form>
</div>
