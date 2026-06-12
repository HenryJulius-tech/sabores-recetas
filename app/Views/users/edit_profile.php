<div class="edit-profile-container">
    <!-- Header Section -->
    <div class="profile-header fade-in">
        <div class="header-content">
            <h1><i class="bi bi-person-fill me-2"></i>Mi Perfil</h1>
            <p>Actualiza tu información personal y preferencias de cuenta</p>
        </div>
        <div class="header-badge">
            <i class="bi bi-shield-check"></i> Información protegida
        </div>
    </div>

    <!-- Alert Messages -->
    <div id="alertContainer"></div>

    <form id="profileForm" class="profile-form" enctype="multipart/form-data">
        <?= csrf_field() ?>
        
        <div class="form-sections">
            <!-- Section 1: Photo & Basic Info -->
            <div class="form-section">
                <div class="section-header">
                    <h2><i class="bi bi-image me-2"></i>Foto de Perfil</h2>
                    <span class="section-subtitle">Sube una foto profesional</span>
                </div>

                <div class="photo-upload-section">
                    <div class="photo-preview-container">
                        <?php 
                            $userPhoto = \App\Core\Session::userAttribute('profile_photo');
                            $photoUrl = $userPhoto ? upload_url('profiles', $userPhoto) : asset('images/default-avatar.png');
                        ?>
                        <img id="photoPreview" src="<?= $photoUrl ?>" alt="Foto de perfil" class="photo-preview">
                        <div class="photo-overlay">
                            <i class="bi bi-camera"></i>
                        </div>
                    </div>
                    
                    <div class="photo-upload-info">
                        <h3>Actualizar foto</h3>
                        <p>Formatos: JPG, PNG, GIF (Máx: 2MB)</p>
                        <div class="upload-zone" id="uploadZone">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <p>Arrastra tu imagen aquí</p>
                            <input type="file" id="photoInput" name="profile_photo" accept="image/*" hidden>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="document.getElementById('photoInput').click()">
                            <i class="bi bi-folder me-1"></i> Seleccionar archivo
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section 2: Personal Info -->
            <div class="form-section">
                <div class="section-header">
                    <h2><i class="bi bi-person me-2"></i>Información Personal</h2>
                    <span class="section-subtitle">Datos de tu cuenta</span>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fullName" class="form-label">Nombre Completo *</label>
                            <input type="text" id="fullName" name="full_name" class="form-control form-control-modern" 
                                   placeholder="Juan García López" 
                                   value="<?= e(\App\Core\Session::userAttribute('full_name') ?? '') ?>" required>
                            <span class="form-error" id="fullNameError"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username" class="form-label">Nombre de Usuario</label>
                            <input type="text" id="username" name="username" class="form-control form-control-modern" 
                                   placeholder="juangarcia" 
                                   value="<?= e(\App\Core\Session::username()) ?>" disabled>
                            <span class="form-hint">No se puede cambiar</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="email" class="form-label">Correo Electrónico *</label>
                            <input type="email" id="email" name="email" class="form-control form-control-modern" 
                                   placeholder="juan@ejemplo.com" 
                                   value="<?= e(\App\Core\Session::userAttribute('email') ?? '') ?>" required>
                            <span class="form-error" id="emailError"></span>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="bio" class="form-label">Biografía</label>
                            <textarea id="bio" name="bio" class="form-control form-control-modern" 
                                      placeholder="Cuéntanos sobre ti..." rows="4"><?= e(\App\Core\Session::userAttribute('bio') ?? '') ?></textarea>
                            <span class="form-hint"><span id="bioCount">0</span>/200 caracteres</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Security -->
            <div class="form-section">
                <div class="section-header">
                    <h2><i class="bi bi-shield-lock me-2"></i>Seguridad</h2>
                    <span class="section-subtitle">Actualiza tu contraseña</span>
                </div>

                <div class="security-toggle">
                    <input type="checkbox" id="changePasswordToggle" class="form-check-input">
                    <label for="changePasswordToggle" class="form-check-label">
                        Cambiar contraseña
                    </label>
                </div>

                <div id="passwordSection" class="password-section" style="display: none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="currentPassword" class="form-label">Contraseña Actual *</label>
                                <div class="password-input-group">
                                    <input type="password" id="currentPassword" name="current_password" 
                                           class="form-control form-control-modern" placeholder="••••••••">
                                    <button type="button" class="btn-toggle-password" onclick="togglePassword('currentPassword')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <span class="form-error" id="currentPasswordError"></span>
                            </div>
                        </div>
                    </div>

                    <div class="password-policy">
                        <h6>Requisitos de contraseña:</h6>
                        <ul>
                            <li><i class="bi bi-dash-circle"></i> Mínimo 8 caracteres</li>
                            <li><i class="bi bi-dash-circle"></i> Una letra mayúscula</li>
                            <li><i class="bi bi-dash-circle"></i> Una letra minúscula</li>
                            <li><i class="bi bi-dash-circle"></i> Un número</li>
                        </ul>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="newPassword" class="form-label">Nueva Contraseña *</label>
                                <div class="password-input-group">
                                    <input type="password" id="newPassword" name="new_password" 
                                           class="form-control form-control-modern" placeholder="••••••••">
                                    <button type="button" class="btn-toggle-password" onclick="togglePassword('newPassword')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <span class="form-error" id="newPasswordError"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirmPassword" class="form-label">Confirmar Contraseña *</label>
                                <div class="password-input-group">
                                    <input type="password" id="confirmPassword" name="confirm_password" 
                                           class="form-control form-control-modern" placeholder="••••••••">
                                    <button type="button" class="btn-toggle-password" onclick="togglePassword('confirmPassword')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <span class="form-error" id="confirmPasswordError"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="security-info">
                    <i class="bi bi-info-circle"></i>
                    <p>Última sesión: Hace 2 horas desde Chrome en Windows 10</p>
                </div>
            </div>

            <!-- Section 4: Preferences -->
            <div class="form-section">
                <div class="section-header">
                    <h2><i class="bi bi-sliders me-2"></i>Preferencias</h2>
                    <span class="section-subtitle">Personaliza tu experiencia</span>
                </div>

                <div class="preferences-grid">
                    <label class="preference-item">
                        <input type="checkbox" name="newsletter" checked>
                        <div class="preference-content">
                            <h4>Recibir newsletter</h4>
                            <p>Notificaciones sobre nuevos cursos y ofertas</p>
                        </div>
                    </label>

                    <label class="preference-item">
                        <input type="checkbox" name="notifications" checked>
                        <div class="preference-content">
                            <h4>Notificaciones de cursos</h4>
                            <p>Avisos sobre actualizaciones de tus cursos</p>
                        </div>
                    </label>

                    <label class="preference-item">
                        <input type="checkbox" name="messages">
                        <div class="preference-content">
                            <h4>Permitir mensajes</h4>
                            <p>Mensajes de instructores y otros usuarios</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-circle me-2"></i>Guardar Cambios
            </button>
            <a href="<?= url('perfil') ?>" class="btn btn-secondary btn-lg">
                <i class="bi bi-x-circle me-2"></i>Cancelar
            </a>
        </div>
    </form>
</div>

<style>
/* Edit Profile Page Styles */
.edit-profile-container {
    max-width: 900px;
    margin: 0 auto;
}

.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 2px solid var(--border);
}

.profile-header .header-content h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--text);
}

.profile-header .header-content p {
    color: var(--text-muted);
    font-size: 0.95rem;
}

.profile-header .header-badge {
    background: linear-gradient(135deg, #ECFDF5, #F0FDF4);
    color: #047857;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-sections {
    display: flex;
    flex-direction: column;
    gap: 32px;
    margin-bottom: 32px;
}

.form-section {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 28px;
    transition: var(--transition);
}

.form-section:hover {
    border-color: var(--primary-light);
    box-shadow: var(--shadow-md);
}

.section-header {
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}

.section-header h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 8px;
}

.section-subtitle {
    font-size: 0.85rem;
    color: var(--text-muted);
}

/* Photo Upload Section */
.photo-upload-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 32px;
    align-items: center;
}

.photo-preview-container {
    position: relative;
    width: 160px;
    height: 160px;
    margin: 0 auto;
}

.photo-preview {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary-light);
    box-shadow: var(--shadow-lg);
    transition: var(--transition);
}

.photo-preview-container:hover .photo-preview {
    transform: scale(1.05);
}

.photo-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #fff;
    opacity: 0;
    transition: var(--transition);
    cursor: pointer;
}

.photo-preview-container:hover .photo-overlay {
    opacity: 1;
}

.photo-upload-info h3 {
    font-weight: 600;
    margin-bottom: 8px;
}

.photo-upload-info p {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-bottom: 16px;
}

.upload-zone {
    border: 2px dashed var(--border);
    border-radius: var(--radius-xs);
    padding: 24px;
    text-align: center;
    cursor: pointer;
    transition: var(--transition);
    background: #F8FAFC;
}

.upload-zone:hover {
    border-color: var(--primary-light);
    background: #FEF2F2;
}

.upload-zone i {
    font-size: 2rem;
    color: var(--primary);
    display: block;
    margin-bottom: 8px;
}

.upload-zone p {
    margin: 0;
    color: var(--text-muted);
}

/* Form Controls */
.form-control-modern {
    border: 1px solid var(--border) !important;
    border-radius: var(--radius-xs) !important;
    padding: 10px 14px !important;
    font-size: 0.95rem;
    transition: var(--transition);
    background: #fff;
}

.form-control-modern:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.05) !important;
}

.form-control-modern::placeholder {
    color: #cbd5e1;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    color: var(--text);
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-error {
    color: #dc2626;
    font-size: 0.8rem;
    margin-top: 4px;
    display: none;
}

.form-error.active {
    display: block;
}

.form-hint {
    color: var(--text-muted);
    font-size: 0.8rem;
    margin-top: 4px;
}

/* Password Section */
.security-toggle {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border);
}

.security-toggle .form-check-label {
    font-weight: 600;
    cursor: pointer;
    margin-left: 8px;
}

.password-section {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.password-input-group {
    position: relative;
    display: flex;
}

.password-input-group .form-control-modern {
    flex: 1;
    padding-right: 40px !important;
}

.btn-toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 1rem;
    padding: 0;
    transition: var(--transition);
}

.btn-toggle-password:hover {
    color: var(--primary);
}

.password-policy {
    background: #F8FAFC;
    border-left: 3px solid var(--primary);
    padding: 16px;
    border-radius: var(--radius-xs);
    margin-bottom: 20px;
}

.password-policy h6 {
    font-weight: 600;
    margin-bottom: 12px;
    font-size: 0.9rem;
}

.password-policy ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.password-policy li {
    font-size: 0.85rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.password-policy i {
    color: #64748B;
    font-size: 0.8rem;
}

.security-info {
    background: #ECFDF5;
    border: 1px solid #d1fae5;
    border-radius: var(--radius-xs);
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #047857;
    font-size: 0.9rem;
    margin-top: 20px;
}

.security-info i {
    font-size: 1.1rem;
    flex-shrink: 0;
}

/* Preferences */
.preferences-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
}

.preference-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 16px;
    border: 1px solid var(--border);
    border-radius: var(--radius-xs);
    cursor: pointer;
    transition: var(--transition);
    background: #fff;
}

.preference-item:hover {
    border-color: var(--primary-light);
    background: #FEF2F2;
}

.preference-item input[type="checkbox"] {
    width: 20px;
    height: 20px;
    min-width: 20px;
    cursor: pointer;
    accent-color: var(--primary);
    margin-top: 2px;
}

.preference-content h4 {
    font-weight: 600;
    margin-bottom: 4px;
    font-size: 0.95rem;
}

.preference-content p {
    color: var(--text-muted);
    font-size: 0.85rem;
    margin: 0;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-top: 40px;
    padding-top: 40px;
    border-top: 2px solid var(--border);
}

.btn-primary, .btn-secondary {
    border: none;
    border-radius: var(--radius-xs);
    font-weight: 600;
    transition: var(--transition);
    min-width: 180px;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), #BE123C);
    color: #fff;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(225, 29, 72, 0.3);
    color: #fff;
    text-decoration: none;
}

.btn-secondary {
    background: #E2E8F0;
    color: var(--text);
}

.btn-secondary:hover {
    background: #CBD5E1;
    color: var(--text);
    text-decoration: none;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        gap: 16px;
    }

    .photo-upload-section {
        grid-template-columns: 1fr;
    }

    .form-section {
        padding: 20px;
    }

    .form-actions {
        flex-direction: column;
    }

    .preferences-grid {
        grid-template-columns: 1fr;
    }
}

/* Alert Messages */
#alertContainer {
    margin-bottom: 24px;
}

.alert-message {
    padding: 16px;
    border-radius: var(--radius-xs);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideDown 0.3s ease;
}

.alert-message.success {
    background: #ECFDF5;
    color: #047857;
    border: 1px solid #d1fae5;
}

.alert-message.error {
    background: #FEF2F2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-message i {
    font-size: 1.2rem;
}

.alert-message .close-alert {
    margin-left: auto;
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 0;
}
</style>

<?php $scripts = '
<script>
// Photo Upload
const photoInput = document.getElementById("photoInput");
const photoPreview = document.getElementById("photoPreview");
const uploadZone = document.getElementById("uploadZone");

// Handle file selection
photoInput.addEventListener("change", function(e) {
    handleFileUpload(e.target.files[0]);
});

// Drag and drop
uploadZone.addEventListener("dragover", (e) => {
    e.preventDefault();
    uploadZone.style.borderColor = "var(--primary)";
    uploadZone.style.background = "#FEF2F2";
});

uploadZone.addEventListener("dragleave", (e) => {
    uploadZone.style.borderColor = "var(--border)";
    uploadZone.style.background = "#F8FAFC";
});

uploadZone.addEventListener("drop", (e) => {
    e.preventDefault();
    uploadZone.style.borderColor = "var(--border)";
    uploadZone.style.background = "#F8FAFC";
    const file = e.dataTransfer.files[0];
    if (file) {
        photoInput.files = e.dataTransfer.files;
        handleFileUpload(file);
    }
});

uploadZone.addEventListener("click", () => {
    photoInput.click();
});

function handleFileUpload(file) {
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === "password" ? "text" : "password";
}

// Toggle password section
document.getElementById("changePasswordToggle").addEventListener("change", function() {
    const section = document.getElementById("passwordSection");
    section.style.display = this.checked ? "block" : "none";
});

// Bio counter
const bioTextarea = document.getElementById("bio");
const bioCount = document.getElementById("bioCount");
bioTextarea.addEventListener("input", function() {
    bioCount.textContent = this.value.length;
});

// Form validation
function validateForm() {
    let isValid = true;
    const fullName = document.getElementById("fullName");
    const email = document.getElementById("email");
    
    // Reset errors
    document.querySelectorAll(".form-error").forEach(el => el.classList.remove("active"));

    // Full name validation
    if (!fullName.value.trim()) {
        showError("fullNameError", "El nombre es obligatorio");
        isValid = false;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim() || !emailRegex.test(email.value)) {
        showError("emailError", "Ingresa un correo válido");
        isValid = false;
    }

    // Password validation (if changing password)
    if (document.getElementById("changePasswordToggle").checked) {
        const currentPassword = document.getElementById("currentPassword");
        const newPassword = document.getElementById("newPassword");
        const confirmPassword = document.getElementById("confirmPassword");

        if (!currentPassword.value) {
            showError("currentPasswordError", "Ingresa tu contraseña actual");
            isValid = false;
        }

        if (!newPassword.value || newPassword.value.length < 8) {
            showError("newPasswordError", "Mínimo 8 caracteres");
            isValid = false;
        }

        if (newPassword.value !== confirmPassword.value) {
            showError("confirmPasswordError", "Las contraseñas no coinciden");
            isValid = false;
        }
    }

    return isValid;
}

function showError(elementId, message) {
    const errorEl = document.getElementById(elementId);
    errorEl.textContent = message;
    errorEl.classList.add("active");
}

function showAlert(message, type) {
    const alertContainer = document.getElementById("alertContainer");
    const alert = document.createElement("div");
    alert.className = `alert-message ${type}`;
    alert.innerHTML = `
        <i class="bi bi-${type === "success" ? "check-circle" : "exclamation-circle"}"></i>
        <span>${message}</span>
        <button class="close-alert" onclick="this.parentElement.remove()">
            <i class="bi bi-x"></i>
        </button>
    `;
    alertContainer.appendChild(alert);
    setTimeout(() => alert.remove(), 5000);
}

// Form submission
document.getElementById("profileForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    if (!validateForm()) {
        showAlert("Por favor completa todos los campos correctamente", "error");
        return;
    }

    const formData = new FormData(this);
    
    try {
        const response = await fetch("' . url('perfil/actualizar') . '", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-Token": document.querySelector("[name=\"_token\"]").value
            }
        });

        const data = await response.json();

        if (data.success) {
            showAlert("¡Perfil actualizado correctamente!", "success");
            setTimeout(() => window.location.href = "' . url('perfil') . '", 2000);
        } else {
            showAlert(data.message || "Error al actualizar perfil", "error");
        }
    } catch (error) {
        showAlert("Error de conexión", "error");
    }
});
</script>
'; ?>
