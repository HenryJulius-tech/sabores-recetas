<?php
require_once 'config.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'create') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        
        if (!$username || !$email || !$password || !$role) {
            set_flash_message('error', 'Todos los campos son obligatorios.');
        } else {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                set_flash_message('error', 'Nombre de usuario o correo ya registrados.');
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hash, $role]);
                set_flash_message('success', "Usuario \"$username\" creado correctamente.");
            }
        }
        header("Location: usuarios.php");
        exit;
    }
    
    if ($action == 'edit') {
        $id = $_POST['id'] ?? 0;
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (!$username || !$email || !$role) {
            set_flash_message('error', 'Los campos username, email y rol son obligatorios.');
        } else {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$username, $email, $id]);
            if ($stmt->fetch()) {
                set_flash_message('error', 'Nombre de usuario o correo ya en uso por otro usuario.');
            } else {
                if ($password) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios SET username=?, email=?, role=?, password_hash=? WHERE id=?");
                    $stmt->execute([$username, $email, $role, $hash, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuarios SET username=?, email=?, role=? WHERE id=?");
                    $stmt->execute([$username, $email, $role, $id]);
                }
                set_flash_message('success', "Usuario \"$username\" actualizado correctamente.");
            }
        }
        header("Location: usuarios.php");
        exit;
    }
    
    if ($action == 'delete') {
        $id = $_POST['id'] ?? 0;
        if ($id == $current_user['id']) {
            set_flash_message('error', 'No puedes eliminar tu propio usuario.');
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE created_by_id = ?");
            $stmt->execute([$id]);
            $has_movs = $stmt->fetchColumn() > 0;
            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM compras WHERE user_id = ?");
            $stmt->execute([$id]);
            $has_compras = $stmt->fetchColumn() > 0;
            
            if ($has_movs) {
                set_flash_message('error', 'No se puede eliminar al usuario porque tiene movimientos financieros asociados.');
            } elseif ($has_compras) {
                set_flash_message('error', 'No se puede eliminar al usuario porque tiene compras registradas.');
            } else {
                $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
                set_flash_message('success', 'Usuario eliminado correctamente.');
            }
        }
        header("Location: usuarios.php");
        exit;
    }
}

$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC");
$users = $stmt->fetchAll();

$page_title = 'Gestión de Usuarios';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            <i data-lucide="users" style="color: var(--primary);"></i>
            <span>Gestión de Usuarios</span>
        </div>
        <button class="btn btn-primary" onclick="openModal('modalCreate')">
            <i data-lucide="plus"></i> Nuevo Usuario
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Fecha Registro</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td style="font-weight: 500;"><?= htmlspecialchars($u['username']) ?></td>
                    <td style="color: var(--text-muted);"><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="role-badge <?= $u['role'] ?>"><?= $u['role'] ?></span>
                    </td>
                    <td style="color: var(--text-muted);"><?= date('Y-m-d', strtotime($u['created_at'])) ?></td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <?php if ($u['role'] != 'admin' || $u['id'] == $current_user['id']): ?>
                            <button type="button" class="btn btn-outline" style="padding: 6px 10px;" 
                                onclick='openEditModal(<?= htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8') ?>)'>
                                <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i>
                            </button>
                            <?php endif; ?>
                            
                            <?php if ($u['id'] != $current_user['id'] && $u['role'] != 'admin'): ?>
                            <form action="usuarios.php" method="POST" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-outline" style="padding: 6px 10px; color: var(--danger); border-color: rgba(239, 68, 68, 0.2);">
                                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div id="modalCreate" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i data-lucide="user-plus" style="display: inline; vertical-align: middle;"></i> Registrar Nuevo Usuario</h3>
            <button class="modal-close" onclick="closeModal('modalCreate')"><i data-lucide="x"></i></button>
        </div>
        <form action="usuarios.php" method="POST">
            <input type="hidden" name="action" value="create">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nombre de Usuario</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Rol</label>
                    <select name="role" class="form-control" required>
                        <option value="">Seleccione un rol...</option>
                        <option value="admin">Administrador (Control Total)</option>
                        <option value="worker">Trabajador (Tienda y Finanzas)</option>
                        <option value="client">Cliente (Solo Compras)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalCreate')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Registrar Usuario</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div id="modalEdit" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i data-lucide="edit-3" style="display: inline; vertical-align: middle;"></i> Editar Usuario</h3>
            <button class="modal-close" onclick="closeModal('modalEdit')"><i data-lucide="x"></i></button>
        </div>
        <form action="usuarios.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nombre de Usuario</label>
                    <input type="text" name="username" id="edit_username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Rol</label>
                    <select name="role" id="edit_role" class="form-control" required>
                        <option value="admin">Administrador</option>
                        <option value="worker">Trabajador</option>
                        <option value="client">Cliente</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nueva Contraseña <small style="color: var(--text-muted); font-weight: normal;">(Dejar en blanco para mantener actual)</small></label>
                    <input type="password" name="password" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    openModal('modalEdit');
}
</script>

<?php include 'includes/footer.php'; ?>
