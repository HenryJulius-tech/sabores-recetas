<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
if (session_isLoggedIn()) {
    registrar_log('Cierre de sesión', 'El usuario cerró sesión voluntariamente.');
}
session_destroyAll();
redirect(BASE_URL . 'auth/login.php');
