<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/session.php';
function csrf_token() {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field() {
    return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}

function validate_csrf() {
    $token = $_POST['_csrf'] ?? '';
    if (empty($_SESSION['_csrf']) || !hash_equals($_SESSION['_csrf'], $token)) {
        session_setFlash('error', 'Token de seguridad inválido');
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL));
        exit;
    }
}
