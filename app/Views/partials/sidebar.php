<?php
$role = \App\Core\Session::userRole();
$u = \App\Core\Session::username();
$cu = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function act($p) { global $cu; return strpos($cu, $p) !== false ? 'active' : ''; }
?>
<div class="d-flex flex-column flex-shrink-0" id="sidebar">
<a href="<?= url('admin') ?>" class="brand">
    <i class="bi bi-tree-fill"></i><span>Finca Bananera</span>
</a>
<hr>
<ul class="nav nav-pills flex-column mb-auto">
    <?php if ($role === 'admin'): ?>
    <li><a href="<?= url('admin') ?>" class="nav-link <?= act('admin') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
    <li><a href="<?= url('productos') ?>" class="nav-link <?= act('productos') ?>"><i class="bi bi-box-seam"></i> Productos</a></li>
    <?php endif; ?>
    <?php if ($role === 'client'): ?>
    <li><a href="<?= url('tienda') ?>" class="nav-link <?= act('tienda') ?>"><i class="bi bi-shop"></i> Tienda</a></li>
    <li><a href="<?= url('mis-compras') ?>" class="nav-link <?= act('mis-compras') ?>"><i class="bi bi-cart-check"></i> Mis Compras</a></li>
    <?php endif; ?>
    <?php if ($role === 'admin'): ?>
    <li><a href="<?= url('admin/compras') ?>" class="nav-link <?= act('admin/compras') ?>"><i class="bi bi-inbox"></i> Aprobar Compras</a></li>
    <?php endif; ?>
    <?php if (in_array($role, ['admin','worker'])): ?>
    <li><a href="<?= url('movimientos') ?>" class="nav-link <?= act('movimientos') ?>"><i class="bi bi-cash-stack"></i> Movimientos</a></li>
    <?php endif; ?>
    <?php if ($role === 'admin'): ?>
    <li><a href="<?= url('usuarios') ?>" class="nav-link <?= act('usuarios') ?>"><i class="bi bi-people"></i> Usuarios</a></li>
    <?php endif; ?>
    <li><a href="<?= url('manual') ?>" class="nav-link <?= act('manual') ?>"><i class="bi bi-book"></i> Manual</a></li>
</ul>
<div class="dropdown mt-auto">
    <a href="#" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
        <i class="bi bi-person-circle me-2 fs-5"></i><strong><?= e($u) ?></strong>
    </a>
    <ul class="dropdown-menu dropdown-menu-dark shadow">
        <li><span class="dropdown-item-text text-muted small">Rol: <?= e($role) ?></span></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
    </ul>
</div>
</div>
