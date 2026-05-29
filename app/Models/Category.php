<?php
namespace App\Models;
use App\Core\Model;
class Category extends Model
{
    protected static $table = 'categorias';
    public static function create($data)
    {
        $cols = implode(',', array_keys($data));
        $vals = implode(',', array_fill(0, count($data), '?'));
        return \App\Core\Database::insert("INSERT INTO categorias ({$cols}) VALUES ({$vals})", array_values($data));
    }
    public static function withCourseCount()
    {
        return \App\Core\Database::fetchAll("SELECT cat.*, (SELECT COUNT(*) FROM cursos WHERE category_id=cat.id AND status='active') as course_count FROM categorias cat ORDER BY cat.name");
    }
}
