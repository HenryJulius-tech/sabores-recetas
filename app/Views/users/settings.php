<div class="settings-container">
    <!-- Header -->
    <div class="settings-header fade-in">
        <h1><i class="bi bi-gear-fill me-2"></i>Configuración</h1>
        <p>Personaliza tu experiencia en Sabores & Recetas</p>
    </div>

    <!-- Settings Grid -->
    <div class="settings-grid">
        <!-- Account Settings -->
        <div class="settings-card">
            <div class="card-header">
                <i class="bi bi-person-lock"></i>
                <h3>Cuenta</h3>
            </div>
            <div class="card-content">
                <p>Gestiona tu información personal y seguridad</p>
                <a href="<?= url('perfil/editar') ?>" class="btn btn-settings-primary">
                    <i class="bi bi-pencil me-1"></i>Editar Perfil
                </a>
            </div>
        </div>

        <!-- Notifications Settings -->
        <div class="settings-card">
            <div class="card-header">
                <i class="bi bi-bell"></i>
                <h3>Notificaciones</h3>
            </div>
            <div class="card-content">
                <div class="toggle-item">
                    <div>
                        <h5>Notificaciones por email</h5>
                        <p>Recibe actualizaciones de tus cursos</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" data-field="email_notifications">
                        <span></span>
                    </label>
                </div>
                <div class="toggle-item">
                    <div>
                        <h5>Newsletter</h5>
                        <p>Información sobre nuevos cursos y ofertas</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" data-field="newsletter">
                        <span></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Privacy Settings -->
        <div class="settings-card">
            <div class="card-header">
                <i class="bi bi-shield-check"></i>
                <h3>Privacidad</h3>
            </div>
            <div class="card-content">
                <div class="toggle-item">
                    <div>
                        <h5>Perfil público</h5>
                        <p>Permite que otros vean tu perfil</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span></span>
                    </label>
                </div>
                <div class="toggle-item">
                    <div>
                        <h5>Mostrar progreso</h5>
                        <p>Comparte tu progreso en cursos</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span></span>
                    </label>
                </div>
            </div>
        </div>

        <?php if (\App\Core\Session::userRole() !== 'admin'): ?>
        <!-- Learning Preferences -->
        <div class="settings-card">
            <div class="card-header">
                <i class="bi bi-book"></i>
                <h3>Preferencias de Aprendizaje</h3>
            </div>
            <div class="card-content">
                <div class="option-group">
                    <label class="option-item">
                        <input type="radio" name="difficulty" value="beginner" checked>
                        <span class="option-label">
                            <strong>Principiante</strong>
                            <p>Prefiero cursos básicos y fundamentales</p>
                        </span>
                    </label>
                </div>
                <div class="option-group">
                    <label class="option-item">
                        <input type="radio" name="difficulty" value="intermediate">
                        <span class="option-label">
                            <strong>Intermedio</strong>
                            <p>Tengo experiencia previa</p>
                        </span>
                    </label>
                </div>
                <div class="option-group">
                    <label class="option-item">
                        <input type="radio" name="difficulty" value="advanced">
                        <span class="option-label">
                            <strong>Avanzado</strong>
                            <p>Busco desafíos técnicos</p>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Download Data -->
        <div class="settings-card">
            <div class="card-header">
                <i class="bi bi-download"></i>
                <h3>Mis Datos</h3>
            </div>
            <div class="card-content">
                <p>Descarga una copia de tus datos personales</p>
                <button class="btn btn-settings-outline" onclick="downloadData()">
                    <i class="bi bi-download me-1"></i>Descargar Datos
                </button>
            </div>
        </div>

        <!-- Help & Support -->
        <div class="settings-card">
            <div class="card-header">
                <i class="bi bi-question-circle"></i>
                <h3>Ayuda & Soporte</h3>
            </div>
            <div class="card-content">
                <p>¿Necesitas ayuda?</p>
                <a href="<?= url('contacto') ?>" class="btn btn-settings-outline">
                    <i class="bi bi-chat-dots me-1"></i>Contactar Soporte
                </a>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="danger-zone">
        <h2><i class="bi bi-exclamation-triangle me-2"></i>Zona de Riesgo</h2>
        <p>Estas acciones no se pueden deshacer</p>
        
        <button class="btn btn-danger-outline" onclick="confirmDeleteAccount()">
            <i class="bi bi-trash me-1"></i>Eliminar Cuenta
        </button>
    </div>
</div>

<style>
.settings-container {
    max-width: 1000px;
    margin: 0 auto;
}

.settings-header {
    margin-bottom: 40px;
    padding-bottom: 24px;
    border-bottom: 2px solid var(--border);
}

.settings-header h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.settings-header p {
    color: var(--text-muted);
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.settings-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: var(--transition);
}

.settings-card:hover {
    border-color: var(--primary-light);
    box-shadow: var(--shadow-md);
}

.settings-card .card-header {
    background: linear-gradient(135deg, #FEF2F2, #FEF7EE);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid var(--border);
}

.settings-card .card-header i {
    font-size: 1.4rem;
    color: var(--primary);
}

.settings-card .card-header h3 {
    font-weight: 700;
    margin: 0;
    font-size: 1.05rem;
}

.settings-card .card-content {
    padding: 20px;
}

.settings-card p {
    color: var(--text-muted);
    margin-bottom: 16px;
}

.toggle-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 16px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border);
}

.toggle-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.toggle-item h5 {
    font-weight: 600;
    margin-bottom: 4px;
}

.toggle-item p {
    font-size: 0.85rem;
    margin: 0;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
    flex-shrink: 0;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-switch span {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: var(--transition);
    border-radius: 28px;
}

.toggle-switch span:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: var(--transition);
    border-radius: 50%;
}

.toggle-switch input:checked + span {
    background-color: var(--primary);
}

.toggle-switch input:checked + span:before {
    transform: translateX(22px);
}

.option-group {
    margin-bottom: 16px;
}

.option-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius-xs);
    cursor: pointer;
    transition: var(--transition);
}

.option-item:hover {
    border-color: var(--primary-light);
    background: #FEF2F2;
}

.option-item input[type="radio"] {
    width: 18px;
    height: 18px;
    margin-top: 2px;
    cursor: pointer;
    accent-color: var(--primary);
    flex-shrink: 0;
}

.option-label {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.option-label strong {
    color: var(--text);
}

.option-label p {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin: 0;
}

.btn-settings-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: linear-gradient(135deg, var(--primary), #BE123C);
    color: #fff;
    border: none;
    border-radius: var(--radius-xs);
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition);
    font-size: 0.9rem;
}

.btn-settings-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3);
    color: #fff;
    text-decoration: none;
}

.btn-settings-outline {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #fff;
    color: var(--primary);
    border: 1px solid var(--primary-light);
    border-radius: var(--radius-xs);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    font-size: 0.9rem;
    text-decoration: none;
}

.btn-settings-outline:hover {
    background: #FEF2F2;
}

.danger-zone {
    background: #FEF2F2;
    border: 2px solid #FECACA;
    border-radius: var(--radius);
    padding: 24px;
    margin-top: 40px;
}

.danger-zone h2 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #991b1b;
    margin-bottom: 8px;
}

.danger-zone p {
    color: #7f1d1d;
    margin-bottom: 16px;
}

.btn-danger-outline {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    background: #fff;
    color: #991b1b;
    border: 1px solid #fecaca;
    border-radius: var(--radius-xs);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    font-size: 0.9rem;
}

.btn-danger-outline:hover {
    background: #FEE2E2;
    border-color: #dc2626;
}

@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .toggle-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
}
</style>

<?php
$notifEmail = $user['email_notifications'] ?? 1;
$notifNews = $user['newsletter'] ?? 1;
$apiUrl = url('api/configuracion/notificaciones');
$scripts = <<<SCRIPT
<script>
var notifPrefs = { email_notifications: {$notifEmail}, newsletter: {$notifNews} };

document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(function(cb) {
    var field = cb.getAttribute('data-field');
    if (field && notifPrefs[field] !== undefined) {
        cb.checked = notifPrefs[field] == 1;
    }
    cb.addEventListener('change', function() {
        var f = this.getAttribute('data-field');
        if (!f) return;
        var formData = new FormData();
        formData.append('field', f);
        formData.append('value', this.checked ? '1' : '0');
        fetch('{$apiUrl}', { method: 'POST', body: formData })
        .then(function(r){ return r.json() })
        .then(function(d){ if (!d.success) alert('Error al guardar preferencia'); })
        .catch(function(){});
    });
});
</script>
SCRIPT;
?>
