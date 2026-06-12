<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (session_isLoggedIn()) {
    $role = session_userRole();
    if ($role === 'admin') {
        redirect(BASE_URL . 'admin/dashboard.php');
    } else {
        redirect(BASE_URL . 'estudiante/dashboard.php');
    }
}

redirect(BASE_URL . 'auth/login.php');
