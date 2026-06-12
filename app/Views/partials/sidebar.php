<?php
$role = \App\Core\Session::userRole();
$u = \App\Core\Session::username();
$cu = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$route = trim(str_replace($base, '', $cu), '/');
$route = preg_replace('#^index\.php/?#', '', $route);
function act($p) { global $route; return $route === $p || strpos($route, $p . '/') === 0 ? 'active' : ''; }
function act_exact($p) { global $route; return $route === $p ? 'active' : ''; }
$cu = null; $base = null;
?>
<div class="d-flex flex-column flex-shrink-0" id="sidebar">
<a href="<?= url('admin') ?>" class="brand">
    <i class="bi bi-book"></i><span>Sabores & Recetas</span>
</a>
<hr>
<ul class="nav nav-pills flex-column mb-auto">
    <?php if ($role === 'admin'): ?>
    <li><a href="<?= url('admin') ?>" class="nav-link <?= act_exact('admin') ?>"><i class="bi bi-speedometer2"></i> Inicio</a></li>
    <li><a href="<?= url('cursos') ?>" class="nav-link <?= act('cursos') ?>"><i class="bi bi-mortarboard"></i> Cursos</a></li>
    <li><a href="<?= url('categorias') ?>" class="nav-link <?= act('categorias') ?>"><i class="bi bi-tags"></i> Categorías</a></li>
    <?php endif; ?>
    <?php if ($role === 'client'): ?>
    <li><a href="<?= url('catalogo') ?>" class="nav-link <?= act('catalogo') ?>"><i class="bi bi-shop"></i> Catálogo</a></li>
    <li><a href="<?= url('mis-cursos') ?>" class="nav-link <?= act('mis-cursos') ?>"><i class="bi bi-play-circle"></i> Mis Cursos</a></li>
    <li><a href="<?= url('mis-matriculas') ?>" class="nav-link <?= act('mis-matriculas') ?>"><i class="bi bi-journal-check"></i> Mis Matrículas</a></li>
    <?php endif; ?>
    <?php if ($role === 'admin'): ?>
    <li><a href="<?= url('admin/enrollments/todas') ?>" class="nav-link <?= act('admin/enrollments') ?>"><i class="bi bi-inbox"></i> Matrículas</a></li>
    <?php endif; ?>
    <?php if (in_array($role, ['admin','worker'])): ?>
    <li><a href="<?= url('movimientos') ?>" class="nav-link <?= act('movimientos') ?>"><i class="bi bi-cash-stack"></i> Movimientos</a></li>
    <?php endif; ?>
    <?php if ($role === 'admin'): ?>
    <li><a href="<?= url('usuarios') ?>" class="nav-link <?= act('usuarios') ?>"><i class="bi bi-people"></i> Usuarios</a></li>
    <li><a href="<?= url('contactos') ?>" class="nav-link <?= act('contactos') ?>"><i class="bi bi-chat-dots"></i> Contacto</a></li>
    <li><a href="<?= url('auditoria') ?>" class="nav-link <?= act('auditoria') ?>"><i class="bi bi-shield-exclamation"></i> Auditoría</a></li>
    <li><a href="<?= url('admin/progreso') ?>" class="nav-link <?= act('admin/progreso') ?>"><i class="bi bi-graph-up-arrow"></i> Progreso</a></li>
    <?php endif; ?>
    <li><a href="<?= url('manual') ?>" class="nav-link <?= act('manual') ?>"><i class="bi bi-book"></i> Manual</a></li>
</ul>
<div class="sidebar-footer text-center py-3">
    <small class="text-white-50"><i class="bi bi-book me-1"></i>Sabores & Recetas</small>
</div>
</div>
