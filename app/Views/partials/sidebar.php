<?php
$role = \App\Core\Session::userRole();
$u = \App\Core\Session::username();
$cu = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function act($p) { global $cu; return strpos($cu, $p) !== false ? 'active' : ''; }
?>
<div class="d-flex flex-column flex-shrink-0" id="sidebar">
<a href="<?= url('admin') ?>" class="brand">
    <i class="bi bi-book"></i><span>Sabores & Recetas</span>
</a>
<hr>
<ul class="nav nav-pills flex-column mb-auto">
    <?php if ($role === 'admin'): ?>
    <li><a href="<?= url('admin') ?>" class="nav-link <?= act('admin') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
    <li><a href="<?= url('cursos') ?>" class="nav-link <?= act('cursos') ?>"><i class="bi bi-mortarboard"></i> Cursos</a></li>
    <li><a href="<?= url('categorias') ?>" class="nav-link <?= act('categorias') ?>"><i class="bi bi-tags"></i> Categorías</a></li>
    <?php endif; ?>
    <?php if ($role === 'client'): ?>
    <li><a href="<?= url('catalogo') ?>" class="nav-link <?= act('catalogo') ?>"><i class="bi bi-shop"></i> Catálogo</a></li>
    <li><a href="<?= url('mis-matriculas') ?>" class="nav-link <?= act('mis-matriculas') ?>"><i class="bi bi-journal-check"></i> Mis Matrículas</a></li>
    <?php endif; ?>
    <?php if ($role === 'admin'): ?>
    <li><a href="<?= url('admin/enrollments') ?>" class="nav-link <?= act('enrollments') ?>"><i class="bi bi-inbox"></i> Matrículas Pendientes</a></li>
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
