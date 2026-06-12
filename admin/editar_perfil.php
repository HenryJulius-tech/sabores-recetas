<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

// Validar que el usuario sea administrador
requireAdmin();

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
        
        // Registrar acción en la auditoría
        registrar_log('Actualización de perfil', 'El administrador actualizó sus datos personales (Nombre: "' . $nombre . '", Email: "' . $email . '").');
        
        session_setFlash('success', 'Perfil actualizado correctamente.');
    } else {
        session_setFlash('error', 'El nombre y correo electrónico son obligatorios.');
    }
    redirect(BASE_URL . 'admin/editar_perfil.php');
}

// ── Subir foto de perfil ───────────────────────────────────────────
if ($_POST && isset($_POST['subir_foto'])) {
    validate_csrf();
    if (!empty($_FILES['foto_perfil']['name'])) {
        // Validar subida (JPG, PNG, WEBP de hasta 2MB)
        $upload = validateUpload($_FILES['foto_perfil'], ['image/jpeg','image/png','image/webp'], 2097152);
        
        if ($upload['valid']) {
            $dir = ROOT . '/public/uploads/avatars/';
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Eliminar avatar antiguo si existe
            if (!empty($user['foto'])) {
                $oldFile = $dir . $user['foto'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            // Mover archivo e insertar en BD
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $dir . $upload['name'])) {
                db_execute("UPDATE usuarios SET foto=? WHERE id=?", [$upload['name'], $userId]);
                $user['foto'] = $upload['name'];
                $_SESSION['user'] = $user;
                
                // Registrar acción en la auditoría
                registrar_log('Cambio de foto de perfil', 'El administrador actualizó su foto de avatar.');
                
                session_setFlash('success', 'Foto de perfil actualizada con éxito.');
            } else {
                session_setFlash('error', 'Error al guardar la imagen en el servidor.');
            }
        } else {
            session_setFlash('error', $upload['error']);
        }
    } else {
        session_setFlash('error', 'Selecciona o arrastra una imagen válida.');
    }
    redirect(BASE_URL . 'admin/editar_perfil.php');
}

// ── Cambiar contraseña ─────────────────────────────────────────────
if ($_POST && isset($_POST['cambiar_password'])) {
    validate_csrf();
    $actual  = $_POST['actual']  ?? '';
    $nueva   = $_POST['nueva']   ?? '';
    $confirm = $_POST['confirm'] ?? '';
    
    if (!password_verify($actual, $user['password'])) {
        session_setFlash('error', 'La contraseña actual ingresada es incorrecta.');
    } elseif ($nueva !== $confirm) {
        session_setFlash('error', 'La nueva contraseña y su confirmación no coinciden.');
    } elseif (strlen($nueva) < 6) {
        session_setFlash('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
    } else {
        db_execute("UPDATE usuarios SET password=? WHERE id=?", [password_hash($nueva, PASSWORD_DEFAULT), $userId]);
        
        // Registrar acción en la auditoría
        registrar_log('Cambio de contraseña', 'El administrador actualizó su contraseña de acceso.');
        
        session_setFlash('success', 'Contraseña actualizada correctamente.');
    }
    redirect(BASE_URL . 'admin/editar_perfil.php');
}

$titulo = 'Editar Perfil';
include __DIR__ . '/header.php';
$avatarUrl = avatar_url($user, 120);
?>

<div class="mb-4">
    <h2 class="fw-bold" style="color:#1a1a2e;">Editar Perfil Admin</h2>
    <p class="text-muted">Actualiza tus credenciales de acceso, datos personales y foto de avatar.</p>
</div>

<div class="row g-4">

    <!-- ── Foto de Perfil (Drag & Drop) ── -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white fw-600 border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark m-0"><i class="bi bi-camera-fill me-2 text-primary"></i>Foto de Perfil</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" enctype="multipart/form-data" id="fotoForm">
                    <?= csrf_field() ?>
                    <div class="avatar-upload-section d-flex align-items-center gap-4 flex-wrap flex-md-nowrap">
                        <!-- Previsualización actual -->
                        <img src="<?= $avatarUrl ?>"
                             alt="Avatar Administrador"
                             class="avatar-preview-large rounded-circle border shadow-sm"
                             id="avatarPreviewLarge"
                             style="width:120px; height:120px; object-fit:cover;">

                        <!-- Zona drag & drop -->
                        <div class="flex-grow-1 w-100">
                            <div class="avatar-dropzone border-2 border-dashed rounded-3 p-4 text-center cursor-pointer" 
                                 id="avatarDropzoneEl"
                                 style="border-color:#cbd5e1; background-color:#f8fafc; transition: all 0.2s ease;">
                                <i class="bi bi-cloud-arrow-up-fill text-primary fs-2"></i>
                                <p class="drop-hint fw-semibold mb-1 mt-2 text-dark">Arrastra tu foto aquí</p>
                                <p class="text-muted small mb-0">o haz clic para seleccionar (JPG, PNG, WEBP · Máx. 2 MB)</p>
                            </div>
                            <div class="avatar-upload-error alert alert-danger py-2 mt-2" style="display:none;"></div>
                            <input type="file" id="avatarFileInput" name="foto_perfil" class="d-none" accept="image/jpeg,image/png,image/webp">
                        </div>
                    </div>
                    <button type="submit" name="subir_foto" class="btn btn-primary rounded-pill fw-bold mt-4 px-4">
                        <i class="bi bi-check-circle me-2"></i>Guardar Foto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Datos Personales ── -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white fw-600 border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark m-0"><i class="bi bi-person-fill me-2 text-primary"></i>Datos Personales</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control bg-light border-0" value="<?= e($user['nombre']) ?>" required style="padding: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control bg-light border-0" value="<?= e($user['email']) ?>" required style="padding: 12px;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">Rol asignado</label>
                        <input type="text" class="form-control bg-light border-0 text-muted" value="Administrador" disabled style="padding: 12px;">
                    </div>
                    <button type="submit" name="guardar_perfil" class="btn btn-primary rounded-pill fw-bold px-4">
                        <i class="bi bi-save me-2"></i>Guardar Cambios
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Cambiar Contraseña ── -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white fw-600 border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark m-0"><i class="bi bi-shield-lock-fill me-2 text-warning"></i>Cambiar Contraseña</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Contraseña Actual</label>
                        <input type="password" name="actual" class="form-control bg-light border-0" required style="padding: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Nueva Contraseña</label>
                        <input type="password" name="nueva" class="form-control bg-light border-0" required minlength="6" style="padding: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Confirmar Nueva Contraseña</label>
                        <input type="password" name="confirm" class="form-control bg-light border-0" required minlength="6" style="padding: 12px;">
                    </div>
                    <button type="submit" name="cambiar_password" class="btn btn-warning text-white rounded-pill fw-bold px-4">
                        <i class="bi bi-key me-2"></i>Cambiar Contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var avatarDropzone = document.getElementById('avatarDropzoneEl');
    var avatarInput    = document.getElementById('avatarFileInput');
    var avatarPreview  = document.getElementById('avatarPreviewLarge');
    var avatarError    = document.querySelector('.avatar-upload-error');

    if (avatarDropzone && avatarInput) {
        // Clic en dropzone abre input de archivos
        avatarDropzone.addEventListener('click', function () { 
            avatarInput.click(); 
        });
        
        avatarInput.addEventListener('click', function (e) { 
            e.stopPropagation(); 
        });

        // Cambio en el input
        avatarInput.addEventListener('change', function (e) {
            e.stopPropagation();
            handleAvatarFile(this.files[0]);
        });

        // Eventos Drag & Drop
        avatarDropzone.addEventListener('dragover', function (e) {
            e.preventDefault(); 
            e.stopPropagation();
            this.classList.add('drag-over');
            this.style.borderColor = '#0d6efd';
            this.style.backgroundColor = '#eff6ff';
        });
        
        avatarDropzone.addEventListener('dragleave', function (e) {
            e.stopPropagation(); 
            this.classList.remove('drag-over');
            this.style.borderColor = '#cbd5e1';
            this.style.backgroundColor = '#f8fafc';
        });
        
        avatarDropzone.addEventListener('drop', function (e) {
            e.preventDefault(); 
            e.stopPropagation();
            this.classList.remove('drag-over');
            this.style.borderColor = '#cbd5e1';
            this.style.backgroundColor = '#f8fafc';
            handleAvatarFile(e.dataTransfer.files[0]);
        });
    }

    function handleAvatarFile(file) {
        if (!file) return;
        var allowed = ['image/jpeg', 'image/png', 'image/webp'];
        var maxSize = 2 * 1024 * 1024; // 2MB
        
        if (avatarError) {
            avatarError.style.display = 'none';
            avatarError.textContent = '';
        }

        if (!allowed.includes(file.type)) {
            showError('Formato no permitido. Solo se aceptan imágenes JPG, PNG o WEBP.');
            return;
        }
        if (file.size > maxSize) {
            showError('El archivo es demasiado grande. El peso máximo es de 2 MB.');
            return;
        }

        // Previsualización local instantánea
        if (avatarPreview) {
            var reader = new FileReader();
            reader.onload = function (ev) {
                avatarPreview.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        }

        // Asignar archivo al input real de tipo file
        if (avatarInput) {
            var dt = new DataTransfer();
            dt.items.add(file);
            avatarInput.files = dt.files;
        }
    }

    function showError(msg) {
        if (avatarError) {
            avatarError.textContent = msg;
            avatarError.style.display = 'block';
        } else {
            alert(msg);
        }
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
