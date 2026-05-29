<?php
namespace App\Core;
class Middleware
{
    public static function auth()
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Debe iniciar sesión');
            header('Location: ' . url('login'));
            exit;
        }
    }
    public static function role($roles)
    {
        self::auth();
        $roles = array_map('trim', explode(',', $roles));
        if (!in_array(Session::userRole(), $roles)) {
            Session::setFlash('error', 'No tiene permisos');
            header('Location: ' . url('login'));
            exit;
        }
    }
    public static function csrf()
    {
        $token = $_POST['_token'] ?? '';
        if (empty($_SESSION['_csrf_token']) || !hash_equals($_SESSION['_csrf_token'], $token)) {
            Session::setFlash('error', 'Token inválido');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }
    }
}
