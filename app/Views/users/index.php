<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Usuarios</h1>
    <a href="<?= url('usuarios/crear') ?>" class="btn-modern btn-modern-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo Usuario</a>
</div>
<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern mb-0">
                <thead><tr><th>ID</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Teléfono</th><th>Registro</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                    <tr><td colspan="7"><div class="empty-state"><i class="bi bi-people"></i><p>No hay usuarios</p></div></td></tr>
                    <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td class="fw-bold"><?= e($u['username']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><span class="badge bg-<?= $u['role']==='admin'?'danger':($u['role']==='worker'?'info':'secondary') ?>"><?= ucfirst($u['role']) ?></span></td>
                        <td><?= e($u['phone'] ?? '-') ?></td>
                        <td class="small text-muted"><?= format_date($u['created_at']) ?></td>
                        <td>
                            <a href="<?= url('usuarios/editar/' . $u['id']) ?>" class="btn-modern btn-modern-outline btn-modern-sm"><i class="bi bi-pencil"></i></a>
                            <button class="btn-modern btn-modern-primary btn-modern-sm" onclick="resetUserPassword(<?= $u['id'] ?>)" title="Resetear contraseña"><i class="bi bi-key"></i></button>
                            <?php if ($u['id'] != \App\Core\Session::userId()): ?>
                            <form method="post" action="<?= url('usuarios/eliminar/' . $u['id']) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar usuario?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-modern btn-modern-danger btn-modern-sm"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $scripts = '
<script>
function resetUserPassword(id) {
    if (!confirm("¿Generar enlace de restablecimiento de contraseña para este usuario?")) return;
    fetch("' . url('api/usuarios/reset-password') . '/" + id, { method: "POST" })
    .then(function(r){ return r.json() })
    .then(function(d){
        if (d.success) {
            alert("Enlace generado:\\n\\n" + d.link + "\\n\\nCopia este enlace y compártelo con el usuario.");
        } else {
            alert("Error: " + (d.error || "Desconocido"));
        }
    })
    .catch(function(){ alert("Error de red"); });
}
</script>
'; ?>
