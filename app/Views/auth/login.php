<div class="row justify-content-center">
    <div class="col-md-6">
        <h4 class="mb-4 text-center">Iniciar Sesión</h4>
        <form class="form-modern" method="post" action="<?= url('login') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn-modern btn-modern-primary w-100">Ingresar</button>
        </form>
        <p class="text-center mt-3 mb-0">
            <small>¿No tienes cuenta? <a href="<?= url('register') ?>">Regístrate aquí</a></small>
        </p>
    </div>
</div>
