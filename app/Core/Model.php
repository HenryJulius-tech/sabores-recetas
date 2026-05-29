<?php
namespace App\Core;
abstract class Model
{
    protected static $table = '';
    protected static $pk = 'id';
    public static function all() { return Database::fetchAll("SELECT * FROM " . static::$table . " ORDER BY id DESC"); }
    public static function find($id) { return Database::fetchOne("SELECT * FROM " . static::$table . " WHERE " . static::$pk . " = ?", [$id]); }
    public static function where($col, $val) { return Database::fetchAll("SELECT * FROM " . static::$table . " WHERE {$col} = ? ORDER BY id DESC", [$val]); }
    public static function whereFirst($col, $val) { return Database::fetchOne("SELECT * FROM " . static::$table . " WHERE {$col} = ?", [$val]); }
    public static function count() { return Database::fetchOne("SELECT COUNT(*) as c FROM " . static::$table)['c'] ?? 0; }
    public static function delete($id) { return Database::execute("DELETE FROM " . static::$table . " WHERE " . static::$pk . " = ?", [$id]); }
    public static function paginate($page = 1, $perPage = 20, $where = '', $params = [])
    {
        $offset = ($page - 1) * $perPage;
        $w = $where ? "WHERE {$where}" : '';
        $data = Database::fetchAll("SELECT * FROM " . static::$table . " {$w} ORDER BY id DESC LIMIT {$perPage} OFFSET {$offset}", $params);
        $total = Database::fetchOne("SELECT COUNT(*) as c FROM " . static::$table . " {$w}", $params)['c'] ?? 0;
        return ['data' => $data, 'total' => $total, 'page' => $page, 'perPage' => $perPage, 'lastPage' => max(1, ceil($total / $perPage))];
    }
}
