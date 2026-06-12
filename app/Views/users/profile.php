<div class="container-fluid">
  <div class="row">
    <!-- Tarjeta de Perfil / Foto -->
    <div class="col-lg-4 mb-4">
      <div class="card fade-in">
        <div class="card-body text-center p-4">
          <div class="profile-photo-container mb-3" style="position: relative; display: inline-block;">
            <div class="profile-photo" style="width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--accent)); display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.2);">
              <?php if (!empty($usuario['profile_photo'])): ?>
              <img src="<?= upload_url('profiles', $usuario['profile_photo']) ?>" alt="<?= e($usuario['fullname']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
              <?php else: ?>
              <i class="bi bi-person" style="font-size: 4rem; color: #fff; opacity: 0.7;"></i>
              <?php endif; ?>
            </div>
            <button class="btn btn-sm btn-primary" style="position: absolute; bottom: 0; right: 0; border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;" data-bs-toggle="modal" data-bs-target="#photoModal" title="Cambiar foto">
              <i class="bi bi-camera-fill"></i>
            </button>
          </div>
          
          <h5 style="font-weight: 700; margin-bottom: 4px;"><?= e($usuario['fullname'] ?? $usuario['username']) ?></h5>
          <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 12px;">@<?= e($usuario['username']) ?></p>
          
          <div style="background: #F1F5F9; border-radius: var(--radius-xs); padding: 12px; margin-bottom: 16px;">
            <small style="color: var(--text-muted); display: block; margin-bottom: 4px;">Rol:</small>
            <strong style="text-transform: capitalize;"><?= ucfirst($usuario['role']) ?></strong>
          </div>
          
          <div style="border-top: 1px solid var(--border); padding-top: 16px;">
            <small style="color: var(--text-muted);">Miembro desde</small>
            <p style="font-size: 0.9rem; font-weight: 600; margin: 0;">
              <?= date('d \d\e F \d\e Y', strtotime($usuario['created_at'])) ?>
            </p>
          </div>
        </div>
      </div>

      <?php if (\App\Core\Session::userRole() !== 'admin'): ?>
      <!-- Estadísticas Rápidas -->
      <div class="card fade-in">
        <div class="card-header">
          <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Mi Actividad</h6>
        </div>
        <div class="card-body" style="font-size: 0.9rem;">
          <?php 
          $enrollments = \App\Models\Enrollment::byUser(\App\Core\Session::userId());
          $completedCount = count(array_filter($enrollments, function($e) { return $e['status'] === 'approved'; }));
          ?>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span>Inscripciones</span>
              <strong><?= count($enrollments) ?></strong>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar" style="width: 100%;"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span>Aprobadas</span>
              <strong><?= $completedCount ?></strong>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-success" style="width: <?= count($enrollments) > 0 ? ($completedCount / count($enrollments) * 100) : 0 ?>%;"></div>
            </div>
          </div>
          <a href="<?= url('mis-matriculas') ?>" class="btn btn-sm btn-outline-primary w-100">
            <i class="bi bi-mortarboard me-1"></i>Ver mis cursos
          </a>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Formulario de Edición -->
    <div class="col-lg-8">
      <div class="card fade-in">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Editar Perfil</h5>
        </div>
        <div class="card-body">
          <form method="post" action="<?= url('perfil/actualizar') ?>" id="profileForm">
            <?= csrf_field() ?>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Nombre de usuario</label>
                <input type="text" class="form-control" value="<?= e($usuario['username']) ?>" disabled>
                <small class="text-muted">No se puede cambiar</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Nombre completo *</label>
                <input type="text" name="fullname" class="form-control" value="<?= e($usuario['fullname'] ?? '') ?>" required>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" value="<?= e($usuario['email']) ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="tel" name="phone" class="form-control" value="<?= e($usuario['phone'] ?? '') ?>" placeholder="+57 300 000 0000">
              </div>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="card" style="background: #F8FAFC; border: 1px solid var(--border); margin-bottom: 24px;">
              <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border);">
                <h6 class="mb-0"><i class="bi bi-lock me-2"></i>Cambiar Contraseña</h6>
              </div>
              <div class="card-body">
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 16px;">
                  Dejar vacío si no deseas cambiar tu contraseña
                </p>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Nueva contraseña</label>
                    <div class="input-group">
                      <input type="password" name="password" class="form-control" id="password" placeholder="Mínimo 8 caracteres" minlength="8">
                      <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password')">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Confirmar contraseña</label>
                    <div class="input-group">
                      <input type="password" name="password_confirm" class="form-control" id="password_confirm" placeholder="Confirma tu contraseña">
                      <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password_confirm')">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-2"></i>Guardar cambios
              </button>
              <a href="<?= url('perfil') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-2"></i>Cancelar
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- Información de Seguridad -->
      <div class="card fade-in mt-4">
        <div class="card-header">
          <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Seguridad</h6>
        </div>
        <div class="card-body" style="font-size: 0.9rem;">
          <div class="mb-3">
            <strong>Último acceso</strong>
            <p class="text-muted mb-0">Hoy a las <?= date('H:i') ?></p>
          </div>
          <div class="mb-3">
            <strong>Sesiones activas</strong>
            <p class="text-muted mb-0">1 sesión en este navegador</p>
          </div>
          <div class="alert alert-info" style="font-size: 0.85rem; margin-bottom: 0;">
            <i class="bi bi-info-circle me-2"></i>
            Por seguridad, tu contraseña nunca es compartida. Recibimos notificaciones de accesos sospechosos.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para cambiar foto de perfil -->
<div class="modal fade" id="photoModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-camera me-2"></i>Cambiar Foto de Perfil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="<?= url('perfil/foto') ?>" enctype="multipart/form-data" id="photoForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Selecciona una imagen</label>
            <div class="file-upload-wrapper" style="border: 2px dashed var(--border); border-radius: var(--radius); padding: 24px; text-align: center; cursor: pointer; transition: var(--transition);" id="dropZone">
              <i class="bi bi-cloud-upload" style="font-size: 2rem; color: var(--text-muted); display: block; margin-bottom: 8px;"></i>
              <p style="margin-bottom: 4px; font-weight: 500;">Arrastra tu imagen aquí</p>
              <small style="color: var(--text-muted);">o haz clic para seleccionar</small>
              <input type="file" name="photo" class="form-control d-none" id="photoInput" accept="image/jpeg,image/png,image/gif" required>
            </div>
            <small class="text-muted" style="display: block; margin-top: 8px;">
              Formatos: JPG, PNG o GIF. Máximo 2MB. Se recomienda imagen cuadrada.
            </small>
          </div>
          <div id="imagePreview" style="display: none; text-align: center; margin-bottom: 16px;">
            <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: var(--radius); object-fit: cover;">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-2"></i>Guardar foto
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .profile-photo-container {
    position: relative;
  }
  .profile-photo {
    transition: transform 0.3s ease;
  }
  .profile-photo-container:hover .profile-photo {
    transform: scale(1.05);
  }
  .file-upload-wrapper:hover {
    border-color: var(--primary);
    background: rgba(225, 29, 72, 0.02);
  }
</style>

<script>
function togglePasswordVisibility(inputId) {
  const input = document.getElementById(inputId);
  const btn = event.target.closest('button');
  
  if (input.type === 'password') {
    input.type = 'text';
    btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
  } else {
    input.type = 'password';
    btn.innerHTML = '<i class="bi bi-eye"></i>';
  }
}

// Drag and drop para foto
const dropZone = document.getElementById('dropZone');
const photoInput = document.getElementById('photoInput');

dropZone.addEventListener('click', () => photoInput.click());

dropZone.addEventListener('dragover', (e) => {
  e.preventDefault();
  dropZone.style.borderColor = 'var(--primary)';
  dropZone.style.background = 'rgba(225, 29, 72, 0.05)';
});

dropZone.addEventListener('dragleave', () => {
  dropZone.style.borderColor = 'var(--border)';
  dropZone.style.background = 'transparent';
});

dropZone.addEventListener('drop', (e) => {
  e.preventDefault();
  dropZone.style.borderColor = 'var(--border)';
  dropZone.style.background = 'transparent';
  
  const files = e.dataTransfer.files;
  if (files.length > 0) {
    photoInput.files = files;
    showPreview(files[0]);
  }
});

photoInput.addEventListener('change', (e) => {
  if (e.target.files.length > 0) {
    showPreview(e.target.files[0]);
  }
});

function showPreview(file) {
  const reader = new FileReader();
  reader.onload = (e) => {
    document.getElementById('previewImg').src = e.target.result;
    document.getElementById('imagePreview').style.display = 'block';
  };
  reader.readAsDataURL(file);
}
</script>
