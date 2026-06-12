<?php
namespace App\Core;
class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }
    public static function set($key, $value) { $_SESSION[$key] = $value; }
    public static function get($key, $default = null) { return $_SESSION[$key] ?? $default; }
    public static function has($key) { return isset($_SESSION[$key]); }
    public static function remove($key) { unset($_SESSION[$key]); }
    public static function destroy() { session_destroy(); $_SESSION = []; }
    public static function regenerate() { session_regenerate_id(true); }
    public static function isLoggedIn() { return self::has('user_id'); }
    public static function userId() { return self::get('user_id'); }
    public static function userRole() { return self::get('role'); }
    public static function username() { return self::get('username'); }
    public static function userAttribute($key, $default = null)
    {
        if ($key === 'full_name' && isset($_SESSION['user']['fullname'])) {
            return $_SESSION['user']['fullname'];
        }
        return $_SESSION['user'][$key] ?? $default;
    }
    public static function setUser($userId)
    {
        $user = Database::fetchOne("SELECT * FROM usuarios WHERE id=?", [$userId]);
        if (!$user) return false;

        self::set('user_id', $user['id']);
        self::set('username', $user['username']);
        self::set('role', $user['role']);
        self::set('user', $user);
        return true;
    }
    public static function setFlash($key, $message) { $_SESSION['flash'][$key] = $message; }
    public static function getFlash($key)
    {
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    public static function allFlashes()
    {
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flashes;
    }
}
