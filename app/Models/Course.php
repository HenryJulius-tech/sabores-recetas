<?php
namespace App\Models;
use App\Core\Model;
class Course extends Model
{
    protected static $table = 'cursos';
    public static function create($data)
    {
        $cols = implode(',', array_keys($data));
        $vals = implode(',', array_fill(0, count($data), '?'));
        return \App\Core\Database::insert("INSERT INTO cursos ({$cols}) VALUES ({$vals})", array_values($data));
    }
    public static function available()
    {
        return \App\Core\Database::fetchAll("SELECT c.*, cat.name as category_name FROM cursos c JOIN categorias cat ON c.category_id=cat.id WHERE c.status='active' ORDER BY c.featured DESC, c.created_at DESC");
    }
    public static function all()
    {
        return \App\Core\Database::fetchAll("SELECT c.*, cat.name as category_name FROM cursos c JOIN categorias cat ON c.category_id=cat.id ORDER BY c.created_at DESC");
    }
    public static function findWithCategory($id)
    {
        return \App\Core\Database::fetchOne("SELECT c.*, cat.name as category_name FROM cursos c JOIN categorias cat ON c.category_id=cat.id WHERE c.id=?", [$id]);
    }
    public static function findByCategory($catId)
    {
        return \App\Core\Database::fetchAll("SELECT c.*, cat.name as category_name FROM cursos c JOIN categorias cat ON c.category_id=cat.id WHERE c.category_id=? AND c.status='active' ORDER BY c.created_at DESC", [$catId]);
    }
    public static function featured()
    {
        return \App\Core\Database::fetchAll("SELECT c.*, cat.name as category_name FROM cursos c JOIN categorias cat ON c.category_id=cat.id WHERE c.featured=1 AND c.status='active' LIMIT 6");
    }
    public static function updateStock($id, $qty)
    {
        return true;
    }
    public static function byPeriod($period)
    {
        return \App\Core\Database::fetchAll("SELECT c.*, cat.name as category_name FROM cursos c JOIN categorias cat ON c.category_id=cat.id WHERE c.period=? AND c.status='active' ORDER BY c.featured DESC, c.id ASC", [$period]);
    }
    public static function periods()
    {
        return \App\Core\Database::fetchAll("SELECT DISTINCT period FROM cursos WHERE period != '' AND status='active' ORDER BY period ASC");
    }
}
