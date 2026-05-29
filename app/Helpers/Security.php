<?php
namespace App\Helpers;
class Security
{
    public static function generateCsrf()
    {
        if (empty($_SESSION['_csrf_token'])) $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['_csrf_token'];
    }
    public static function csrfField()
    {
        return '<input type="hidden" name="_token" value="' . self::generateCsrf() . '">';
    }
    public static function sanitize($input) { return htmlspecialchars($input, ENT_QUOTES, 'UTF-8'); }
    public static function validateUpload($file, $allowed = ['image/jpeg','image/png','image/gif','image/webp'], $maxSize = 2097152)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return ['valid' => false, 'error' => 'Error al subir'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extMap = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp'];
        if (!isset($extMap[$ext]) || !in_array($extMap[$ext], $allowed)) return ['valid' => false, 'error' => 'Tipo no permitido'];
        if ($file['size'] > $maxSize) return ['valid' => false, 'error' => 'Excede tamaño máximo (2MB)'];
        return ['valid' => true, 'name' => time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext];
    }
    public static function hashPassword($p) { return password_hash($p, PASSWORD_DEFAULT); }
    public static function verifyPassword($p, $h) { return password_verify($p, $h); }
    public static function rateLimit($key, $max = 5, $window = 900)
    {
        $now = time();
        $attempts = $_SESSION['_rate_limit'][$key] ?? [];
        $attempts = array_values(array_filter($attempts, function($t) use ($now, $window) { return $t > ($now - $window); }));
        if (count($attempts) >= $max) return false;
        $attempts[] = $now;
        $_SESSION['_rate_limit'][$key] = $attempts;
        return true;
    }
}
