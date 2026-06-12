<?php
namespace App\Models;
use App\Core\Database;
use App\Core\Session;

class AuditLog
{
    public static function log($action, $description = '')
    {
        return Database::insert(
            "INSERT INTO auditoria (user_id, username, role, action, description, ip_address) VALUES (?, ?, ?, ?, ?, ?)",
            [
                Session::userId(),
                Session::username(),
                Session::userRole(),
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            ]
        );
    }

    public static function all($limit = 100, $offset = 0)
    {
        return Database::fetchAll(
            "SELECT * FROM auditoria ORDER BY id DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    public static function search($params = [], $limit = 100, $offset = 0)
    {
        $sql = "SELECT * FROM auditoria WHERE 1=1";
        $binds = [];
        if (!empty($params['action'])) {
            $sql .= " AND action LIKE ?";
            $binds[] = '%' . $params['action'] . '%';
        }
        if (!empty($params['username'])) {
            $sql .= " AND username LIKE ?";
            $binds[] = '%' . $params['username'] . '%';
        }
        if (!empty($params['role'])) {
            $sql .= " AND role = ?";
            $binds[] = $params['role'];
        }
        if (!empty($params['from'])) {
            $sql .= " AND created_at >= ?";
            $binds[] = $params['from'];
        }
        if (!empty($params['to'])) {
            $sql .= " AND created_at <= ?";
            $binds[] = $params['to'] . ' 23:59:59';
        }
        $sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
        $binds[] = $limit;
        $binds[] = $offset;
        return Database::fetchAll($sql, $binds);
    }

    public static function count($params = [])
    {
        $sql = "SELECT COUNT(*) as c FROM auditoria WHERE 1=1";
        $binds = [];
        if (!empty($params['action'])) {
            $sql .= " AND action LIKE ?";
            $binds[] = '%' . $params['action'] . '%';
        }
        if (!empty($params['username'])) {
            $sql .= " AND username LIKE ?";
            $binds[] = '%' . $params['username'] . '%';
        }
        if (!empty($params['role'])) {
            $sql .= " AND role = ?";
            $binds[] = $params['role'];
        }
        if (!empty($params['from'])) {
            $sql .= " AND created_at >= ?";
            $binds[] = $params['from'];
        }
        if (!empty($params['to'])) {
            $sql .= " AND created_at <= ?";
            $binds[] = $params['to'] . ' 23:59:59';
        }
        $row = Database::fetchOne($sql, $binds);
        return $row['c'] ?? 0;
    }

    public static function distinctActions()
    {
        $rows = Database::fetchAll("SELECT DISTINCT action FROM auditoria ORDER BY action");
        return array_column($rows, 'action');
    }
}
