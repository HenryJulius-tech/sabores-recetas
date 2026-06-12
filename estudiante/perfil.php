<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
requireEstudiante();

$userId = session_userId();
$user   = db_fetchOne("SELECT * FROM usuarios WHERE id=?", [$userId]);

// ── Guardar datos personales ───────────────────────────────────────
if ($_POST && isset($_POST['guardar_perfil'])) {
    validate_csrf();
    $nombre = trim($_POST['nombre'] ?? '');
    $email  = trim($_POST['email']  ?? '');
    if ($nombre && $email) {
        db_execute("UPDATE usuarios SET nombre=?, email=? WHERE id=?", [$nombre, $email, $userId]);
        $user['nombre'] = $nombre;
        $user['email']  = $email;
        $_SESSION['user'] = $user;
        registrar_log('Actualización de perfil', 'Actualizó sus datos personales (Nombre: "' . $nombre . '", Email: "' . $email . '").');
        session_setFlash('success', 'Perfil actualizado correctamente.');
    } else {
        session_setFlash('error', 'Nombre y email son requeridos.');
    }
    redirect(BASE_URL . 'estudiante/perfil.php');
}

// ── Subir foto de perfil ───────────────────────────────────────────
if ($_POST && isset($_POST['subir_foto'])) {
    validate_csrf();
    if (!empty($_FILES['foto_perfil']['name'])) {
        $upload = validateUpload($_FILES['foto_perfil'], ['image/jpeg','image/png','image/webp'], 2097152);
        if ($upload['valid']) {
            // Crear directorio si no existe
            $dir = ROOT . '/public/uploads/avatars/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            // Eliminar foto anterior si existe
            if (!empty($user['foto'])) {
                $old = $dir . $user['foto'];
                if (file_exists($old)) unlink($old);
            }

            move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $dir . $upload['name']);
            db_execute("UPDATE usuarios SET foto=? WHERE id=?", [$upload['name'], $userId]);
            $user['foto'] = $upload['name'];
            $_SESSION['user'] = $user;
            registrar_log('Cambio de foto de perfil', 'Actualizó su foto de avatar.');
            session_setFlash('success', 'Foto de perfil actualizada.');
        } else {
            session_setFlash('error', $upload['error']);
        }
    } else {
        session_setFlash('error', 'Selecciona o arrastra una imagen.');
    }
    redirect(BASE_URL . 'estudiante/perfil.php');
}

// ── Cambiar contraseña ─────────────────────────────────────────────
if ($_POST && isset($_POST['cambiar_password'])) {
    validate_csrf();
    $actual  = $_POST['actual']  ?? '';
    $nueva   = $_POST['nueva']   ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (!password_verify($actual, $user['password'])) {
        session_setFlash('error', 'La contraseña actual no es correcta.');
    } elseif ($nueva !== $confirm) {
        session_setFlash('error', 'Las contraseñas nuevas no coinciden.');
    } elseif (strlen($nueva) < 6) {
        session_setFlash('error', 'Mínimo 6 caracteres para la nueva contraseña.');
    } else {
        db_execute("UPDATE usuarios SET password=? WHERE id=?", [password_hash($nueva, PASSWORD_DEFAULT), $userId]);
        registrar_log('Cambio de contraseña', 'Actualizó su contraseña de acceso.');
        session_setFlash('success', 'Contraseña actualizada correctamente.');
    }
    redirect(BASE_URL . 'estudiante/perfil.php');
}

// Lógica de eliminación movida a eliminar_cuenta.php
$titulo = 'Mi Perfil';
include __DIR__ . '/header.php';
$avatarUrl = avatar_url($user, 120);
?>

<div class="mb-4">
    <h2 class="fw-bold" style="color:#1a1a2e;">Mi Perfil</h2>
    <p class="text-muted">Gestiona tu información personal y foto de perfil.</p>
</div>

<div class="row g-4">

    <!-- ── Foto de Perfil ─────────────────────────────── -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-600">
                <i class="bi bi-camera-fill me-2 text-primary"></i>Foto de Perfil
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="fotoForm">
                    <?= csrf_field() ?>
                    <div class="avatar-upload-section">
                        <!-- Previsualización actual -->
                        <img src="<?= $avatarUrl ?>"
                             alt="Avatar"
                             class="avatar-preview-large"
                             id="avatarPreviewLarge">

                        <!-- Zona drag & drop -->
                        <div>
                            <div class="avatar-dropzone" id="avatarDropzoneEl">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <p class="drop-hint">Arrastra tu foto aquí</p>
                                <p>o haz clic para seleccionar</p>
                                <p><small>JPG, PNG, WEBP · Máx. 2 MB</small></p>
                            </div>
                            <div class="avatar-upload-error"></div>
                            <input type="file" id="avatarFileInput" name="foto_perfil" accept="image/jpeg,image/png,image/webp">
                        </div>
                    </div>
                    <button type="submit" name="subir_foto" class="btn btn-primary mt-3">
                        <i class="bi bi-check-circle me-2"></i>Guardar foto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Datos Personales ──────────────────────────── -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-600">
                <i class="bi bi-person-fill me-2 text-primary"></i>Datos Personales
            </div>
            <div class="card-body">
                <form method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-500">Nombre completo</label>
                        <input type="text" name="nombre" class="form-control" value="<?= e($user['nombre']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500">Correo electrónico</label>
                        <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500">Rol</label>
                        <input type="text" class="form-control bg-light" value="<?= ucfirst($user['role']) ?>" disabled>
                    </div>
                    <button type="submit" name="guardar_perfil" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Guardar cambios
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Cambiar Contraseña ────────────────────────── -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-600">
                <i class="bi bi-shield-lock-fill me-2 text-warning"></i>Cambiar Contraseña
            </div>
            <div class="card-body">
                <form method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-500">Contraseña actual</label>
                        <input type="password" name="actual" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500">Nueva contraseña</label>
                        <input type="password" name="nueva" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500">Confirmar nueva contraseña</label>
                        <input type="password" name="confirm" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" name="cambiar_password" class="btn btn-warning">
                        <i class="bi bi-key me-2"></i>Cambiar contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Darse de Baja (Zona de Peligro) ── -->
    <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545;">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold text-danger m-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Zona de Peligro</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted mb-4">Si decides darte de baja de la plataforma, tu cuenta, inscripciones, progreso y comprobantes serán eliminados de forma definitiva. Esta acción no se puede deshacer.</p>
                <button type="button" class="btn btn-danger rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#eliminarCuentaModal">
                    <i class="bi bi-trash-fill me-1"></i>Eliminar mi cuenta permanentemente
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Modal para Confirmar Eliminación de Cuenta -->
<div class="modal fade" id="eliminarCuentaModal" tabindex="-1" aria-labelledby="eliminarCuentaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="eliminar_cuenta.php">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="eliminarCuentaModalLabel">¿Confirmas darte de baja?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="alert alert-danger rounded-3 d-flex align-items-center mb-3">
                        <i class="bi bi-exclamation-octagon-fill me-2 fs-5 text-danger"></i>
                        <strong class="text-danger">¡Advertencia importante!</strong>
                    </div>
                    <p class="text-dark mb-0">Al confirmar esta acción, perderás acceso inmediato a todos tus cursos activos, progresos y certificados. Toda tu información será borrada del servidor permanentemente.</p>
                </div>
                <div class="modal-footer border-top-0 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill w-100 fw-bold py-2" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="eliminar_cuenta" class="btn btn-danger rounded-pill w-100 fw-bold py-2">Eliminar Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
