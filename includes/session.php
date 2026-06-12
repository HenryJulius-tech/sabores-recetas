<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();

function session_userId() { return $_SESSION['user']['id'] ?? null; }
function session_userRole() { return $_SESSION['user']['role'] ?? null; }
function session_userNombre() { return $_SESSION['user']['nombre'] ?? null; }
function session_userEmail() { return $_SESSION['user']['email'] ?? null; }
function session_user() { return $_SESSION['user'] ?? null; }
function session_isLoggedIn() { return isset($_SESSION['user']); }

function session_setUser($user) {
    $_SESSION['user'] = $user;
}

function session_destroyAll() {
    session_destroy();
    $_SESSION = [];
}

function session_setFlash($key, $msg) {
    $_SESSION['flash'][$key] = $msg;
}

function session_getFlash($key) {
    $v = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $v;
}

function session_allFlashes() {
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}
