<div class="row justify-content-center">
    <div class="col-md-6">
        <h4 class="mb-4 text-center">Crear Cuenta</h4>
        <form class="form-modern" method="post" action="<?= url('register') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="username" class="form-control" required minlength="3">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input type="text" name="phone" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <input type="text" name="address" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <button type="submit" class="btn-modern btn-modern-primary w-100">Registrarse</button>
        </form>
        <p class="text-center mt-3 mb-0">
            <small>¿Ya tienes cuenta? <a href="<?= url('login') ?>">Inicia sesión</a></small>
        </p>
    </div>
</div>
