<?php
namespace App\Models;
use App\Core\Database;

class PasswordReset
{
    public static function createToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        Database::execute(
            "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))",
            [$userId, $token]
        );
        return $token;
    }

    public static function findByToken($token)
    {
        return Database::fetchOne(
            "SELECT * FROM password_resets WHERE token=? AND expires_at > NOW() AND used_at IS NULL",
            [$token]
        );
    }

    public static function markUsed($token)
    {
        Database::execute("UPDATE password_resets SET used_at=NOW() WHERE token=?", [$token]);
    }
}
