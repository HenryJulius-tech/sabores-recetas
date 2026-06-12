<?php
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
$_currentUser = db_fetchOne("SELECT id, nombre, email, role, foto FROM usuarios WHERE id=?", [session_userId()]);
$_avatarUrl   = avatar_url($_currentUser, 80);
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'Inicio') ?> - Sabores &amp; Recetas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset_url('css/style.css') ?>" rel="stylesheet">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta name="base-url" content="<?= BASE_URL ?>">
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- ═══ SIDEBAR ESTUDIANTE (Con Estilo Admin) ═══ -->
    <div class="sidebar sidebar-admin" id="sidebar">
        <a href="<?= BASE_URL ?>estudiante/dashboard.php" class="brand" style="text-decoration:none;">
            <i class="bi bi-mortarboard-fill"></i>
            <span>Sabores &amp; Recetas</span>
        </a>
        <div class="nav flex-column mt-1">
            <a href="<?= BASE_URL ?>estudiante/dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                <i class="bi bi-house-fill"></i> <span>Inicio</span>
            </a>
            <a href="<?= BASE_URL ?>estudiante/cursos.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'cursos.php' ? 'active' : '' ?>">
                <i class="bi bi-compass"></i> <span>Explorar Cursos</span>
            </a>
            <a href="<?= BASE_URL ?>estudiante/mis-cursos.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'mis-cursos.php' ? 'active' : '' ?>">
                <i class="bi bi-journal-bookmark-fill"></i> <span>Mis Cursos</span>
            </a>
            <a href="<?= BASE_URL ?>manual.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'manual.php' ? 'active' : '' ?>">
                <i class="bi bi-question-circle-fill"></i> <span>Ayuda / Manual</span>
            </a>
        </div>
    </div>

    <!-- ═══ CONTENIDO PRINCIPAL ═══ -->
    <div id="page-content-wrapper" class="flex-grow-1 d-flex flex-column">

        <!-- ═══ TOPBAR ═══ -->
        <nav class="navbar navbar-top">
            <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

                <button class="topbar-toggle d-md-none" id="menu-toggle">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <span class="page-breadcrumb d-none d-md-block">
                    <i class="bi bi-mortarboard me-2 text-muted"></i>
                    <span class="text-muted"><?= e($titulo ?? 'Portal Estudiante') ?></span>
                </span>

                <div class="topbar-right">
                    <!-- ── Campana ── -->
                    <div class="notif-wrapper" id="notifWrapper">
                        <button class="topbar-icon-btn" id="notifBtn" aria-label="Notificaciones">
                            <i class="bi bi-bell-fill"></i>
                            <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
                        </button>
                        <div class="notif-panel" id="notifPanel">
                            <div class="notif-panel-header">
                                <span class="fw-600">Notificaciones</span>
                                <button class="btn-text-sm" id="markAllRead">Marcar todas</button>
                            </div>
                            <div class="notif-list" id="notifList">
                                <div class="notif-empty">
                                    <i class="bi bi-bell-slash"></i>
                                    <p>Sin notificaciones nuevas</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Perfil dropdown ── -->
                    <div class="profile-wrapper" id="profileWrapper">
                        <button class="topbar-avatar-btn" id="profileBtn">
                            <img src="<?= $_avatarUrl ?>" alt="<?= e($_currentUser['nombre']) ?>" class="topbar-avatar" id="topbarAvatar">
                            <span class="topbar-username d-none d-lg-inline"><?= e($_currentUser['nombre']) ?></span>
                            <i class="bi bi-chevron-down topbar-chevron"></i>
                        </button>
                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="profile-dropdown-header">
                                <img src="<?= $_avatarUrl ?>" alt="Avatar" class="profile-dropdown-avatar" id="dropdownAvatar">
                                <div>
                                    <div class="profile-dropdown-name"><?= e($_currentUser['nombre']) ?></div>
                                    <div class="profile-dropdown-role">
                                        <span class="role-badge-student">Estudiante</span>
                                    </div>
                                </div>
                            </div>
                            <hr class="dropdown-divider-custom">
                            <a href="<?= BASE_URL ?>estudiante/perfil.php" class="profile-dropdown-item">
                                <i class="bi bi-person-gear"></i> Editar Perfil
                            </a>
                            <a href="<?= BASE_URL ?>auth/logout.php" class="profile-dropdown-item profile-dropdown-logout">
                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow-1 p-4" style="background:#f8fafc;">
            <?php $flashes = session_allFlashes(); if (!empty($flashes)): foreach ($flashes as $k => $m): ?>
                <div class="alert alert-<?= $k === 'error' ? 'danger' : ($k === 'success' ? 'success' : 'info') ?> alert-dismissible fade show">
                    <?= e($m) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; endif; ?>
