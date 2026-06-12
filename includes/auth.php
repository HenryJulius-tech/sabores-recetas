<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/session.php';

function requireLogin() {
    if (!session_isLoggedIn()) {
        session_setFlash('error', 'Debe iniciar sesión');
        header('Location: ' . BASE_URL . 'auth/login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (session_userRole() !== 'admin') {
        session_setFlash('error', 'Acceso denegado');
        header('Location: ' . BASE_URL . 'estudiante/dashboard.php');
        exit;
    }
}

function requireEstudiante() {
    requireLogin();
    if (session_userRole() !== 'estudiante') {
        session_setFlash('error', 'Acceso denegado');
        header('Location: ' . BASE_URL . 'admin/dashboard.php');
        exit;
    }
}
