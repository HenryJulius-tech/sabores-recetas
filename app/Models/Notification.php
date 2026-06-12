<?php
namespace App\Models;
use App\Core\Database;

class Notification
{
    public static function create($userId, $type, $title, $message = '', $link = '')
    {
        return Database::insert(
            "INSERT INTO notificaciones (user_id, type, title, message, link) VALUES (?, ?, ?, ?, ?)",
            [$userId, $type, $title, $message, $link]
        );
    }

    public static function notifyAdmins($type, $title, $message = '', $link = '')
    {
        $admins = Database::fetchAll("SELECT id FROM usuarios WHERE role='admin'");
        foreach ($admins as $admin) {
            self::create($admin['id'], $type, $title, $message, $link);
        }
    }

    public static function unreadCount($userId)
    {
        $row = Database::fetchOne(
            "SELECT COUNT(*) as c FROM notificaciones WHERE user_id=? AND read_at IS NULL",
            [$userId]
        );
        return $row['c'] ?? 0;
    }

    public static function recent($userId, $limit = 10)
    {
        return Database::fetchAll(
            "SELECT * FROM notificaciones WHERE user_id=? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    public static function markRead($id, $userId)
    {
        Database::execute(
            "UPDATE notificaciones SET read_at=NOW() WHERE id=? AND user_id=?",
            [$id, $userId]
        );
    }

    public static function markAllRead($userId)
    {
        Database::execute(
            "UPDATE notificaciones SET read_at=NOW() WHERE user_id=? AND read_at IS NULL",
            [$userId]
        );
    }

    public static function deleteById($id, $userId)
    {
        Database::execute(
            "DELETE FROM notificaciones WHERE id=? AND user_id=?",
            [$id, $userId]
        );
    }
}
