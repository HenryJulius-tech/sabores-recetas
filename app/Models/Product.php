<?php
namespace App\Models;
use App\Core\Model;
class Product extends Model
{
    protected static $table = 'productos';
    public static function create($data)
    {
        $cols = implode(',', array_keys($data));
        $vals = implode(',', array_fill(0, count($data), '?'));
        return \App\Core\Database::insert("INSERT INTO productos ({$cols}) VALUES ({$vals})", array_values($data));
    }
    public static function updateProduct($id, $data)
    {
        unset($data['id']);
        $sets = implode('=?, ', array_keys($data)) . '=?';
        \App\Core\Database::execute("UPDATE productos SET {$sets} WHERE id=?", array_merge(array_values($data), [$id]));
    }
    public static function available()
    {
        return \App\Core\Database::fetchAll("SELECT * FROM productos WHERE stock>0 ORDER BY id DESC");
    }
}
