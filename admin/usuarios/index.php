<?php
/**
 * admin/usuarios/index.php
 * CRUD Completo de Usuarios
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/functions.php';

// Validar privilegios de administrador
requireAdmin();
$userId = session_userId();

// ── Crear Usuario ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    validate_csrf();
    $nombre   = trim($_POST['nombre'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'estudiante';
    
    if ($nombre && $email && $password) {
        $exists = db_fetchOne("SELECT id FROM usuarios WHERE email=?", [$email]);
        if ($exists) {
            session_setFlash('error', 'El correo electrónico ya está registrado.');
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            db_insert(
                "INSERT INTO usuarios (nombre, email, password, role) VALUES (?,?,?,?)",
                [$nombre, $email, $hashedPassword, $role]
            );
            
            // Loguear acción en auditoría
            registrar_log('Creación de usuario', "Creó al usuario '$nombre' ($email) con rol: $role.");
            session_setFlash('success', 'Usuario creado correctamente.');
        }
    } else {
        session_setFlash('error', 'Todos los campos son obligatorios.');
    }
    redirect(BASE_URL . 'admin/usuarios/index.php');
}

// ── Eliminar Usuario ──
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    
    // Evitar auto-eliminación del administrador actual
    if ($id === $userId) {
        session_setFlash('error', 'No puedes eliminar tu propia cuenta de administrador por seguridad.');
        redirect(BASE_URL . 'admin/usuarios/index.php');
    }
    
    $targetUser = db_fetchOne("SELECT nombre, email FROM usuarios WHERE id=?", [$id]);
    if ($targetUser) {
        try {
            db_transaction();
            // Eliminar en cascada manualmente para evitar errores de claves foráneas
            // 1. Pagos relacionados a las inscripciones del usuario
            db_execute("DELETE FROM pagos WHERE inscripcion_id IN (SELECT id FROM inscripciones WHERE user_id = ?)", [$id]);
            // 2. Inscripciones
            db_execute("DELETE FROM inscripciones WHERE user_id = ?", [$id]);
            // 3. Notificaciones
            db_execute("DELETE FROM notificaciones WHERE user_id = ?", [$id]);
            // 4. Progreso de Estudiantes
            db_execute("DELETE FROM progreso_estudiantes WHERE usuario_id = ?", [$id]);
            // 5. Clases Completadas
            db_execute("DELETE FROM clases_completadas WHERE user_id = ?", [$id]);
            // 6. Resultados de Exámenes
            db_execute("DELETE FROM resultados_examenes WHERE usuario_id = ?", [$id]);
            // 7. Desvincular de Auditoría (mantener historial pero poner usuario_id a NULL)
            db_execute("UPDATE auditoria SET usuario_id = NULL WHERE usuario_id = ?", [$id]);
            
            // 8. Finalmente eliminar el usuario
            db_execute("DELETE FROM usuarios WHERE id = ?", [$id]);
            
            db_commit();
            
            // Loguear acción en auditoría (después del commit para no perderla si falla la transacción)
            registrar_log('Eliminación de usuario', "Eliminó al usuario '{$targetUser['nombre']}' ({$targetUser['email']}).");
            session_setFlash('success', 'Usuario eliminado correctamente junto con todos sus registros.');
        } catch (Exception $e) {
            db_rollback();
            session_setFlash('error', 'Error al eliminar usuario: ' . $e->getMessage());
        }
    } else {
        session_setFlash('error', 'Usuario no encontrado.');
    }
    redirect(BASE_URL . 'admin/usuarios/index.php');
}

// Obtener listado de usuarios
$usuarios = db_fetchAll("SELECT u.*, (SELECT COUNT(*) FROM inscripciones WHERE user_id=u.id) as cursos FROM usuarios u ORDER BY u.created_at DESC");

$titulo = 'Usuarios';
include __DIR__ . '/../header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold m-0" style="color:#1a1a2e;">Gestión de Usuarios</h2>
        <p class="text-muted">Administra el acceso de estudiantes y administradores en la plataforma.</p>
    </div>
    <button class="btn btn-primary rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
        <i class="bi bi-person-plus-fill me-1"></i> Agregar Usuario
    </button>
</div>

<!-- Listado de Usuarios -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-uppercase text-muted fs-7 border-bottom">
                        <th class="ps-4 py-3">Nombre</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Rol</th>
                        <th class="py-3">Cursos Inscritos</th>
                        <th class="py-3">Fecha Registro</th>
                        <th class="py-3 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($usuarios)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No hay usuarios registrados.</td></tr>
                    <?php else: foreach ($usuarios as $u): ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= avatar_url($u, 40) ?>" class="rounded-circle shadow-sm" style="width:36px; height:36px; object-fit:cover;" alt="Avatar">
                                    <strong class="text-dark"><?= e($u['nombre']) ?></strong>
                                </div>
                            </td>
                            <td><?= e($u['email']) ?></td>
                            <td>
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill fw-bold" style="background-color: rgba(220,53,69,0.1);">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-bold" style="background-color: rgba(13,110,253,0.1);">Estudiante</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill"><?= $u['cursos'] ?> cursos</span>
                            </td>
                            <td class="text-muted small"><?= format_date($u['created_at']) ?></td>
                            <td class="text-end pe-4">
                                <?php if ($u['id'] !== $userId): ?>
                                    <a href="?eliminar=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario y toda su información relacionada? Esta acción es irreversible.');">
                                        <i class="bi bi-trash3-fill me-1"></i>Eliminar
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill fw-bold">Tú</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Crear Usuario -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="crearUsuarioModalLabel">Registrar Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="regNombre" class="form-label fw-bold text-dark small">Nombre Completo</label>
                        <input type="text" name="nombre" id="regNombre" class="form-control bg-light border-0" placeholder="Nombre" required style="padding: 12px;">
                    </div>
                    <div class="mb-3">
                        <label for="regEmail" class="form-label fw-bold text-dark small">Correo Electrónico</label>
                        <input type="email" name="email" id="regEmail" class="form-control bg-light border-0" placeholder="correo@ejemplo.com" required style="padding: 12px;">
                    </div>
                    <div class="mb-3">
                        <label for="regPassword" class="form-label fw-bold text-dark small">Contraseña</label>
                        <input type="password" name="password" id="regPassword" class="form-control bg-light border-0" placeholder="••••••••" required minlength="6" style="padding: 12px;">
                    </div>
                    <div class="mb-3">
                        <label for="regRole" class="form-label fw-bold text-dark small">Rol de Usuario</label>
                        <select name="role" id="regRole" class="form-select bg-light border-0" required style="padding: 12px;">
                            <option value="estudiante">Estudiante</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="submit" name="crear_usuario" class="btn btn-primary rounded-pill w-100 fw-bold py-2.5">Crear Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.fs-7 { font-size: 0.8rem; }
</style>

<?php include __DIR__ . '/../footer.php'; ?>
