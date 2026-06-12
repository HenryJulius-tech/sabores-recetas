<?php
namespace App\Models;
use App\Core\Model;
class Enrollment extends Model
{
    protected static $table = 'matriculas';
    public static function create($data)
    {
        $cols = implode(',', array_keys($data));
        $vals = implode(',', array_fill(0, count($data), '?'));
        return \App\Core\Database::insert("INSERT INTO matriculas ({$cols}) VALUES ({$vals})", array_values($data));
    }
    public static function withDetails($id)
    {
        $e = \App\Core\Database::fetchOne("SELECT m.*, u.username, u.email FROM matriculas m JOIN usuarios u ON m.user_id=u.id WHERE m.id=?", [$id]);
        if (!$e) return null;
        $e['curso'] = \App\Core\Database::fetchOne("SELECT c.*, cat.name as category_name FROM cursos c JOIN categorias cat ON c.category_id=cat.id WHERE c.id=?", [$e['curso_id']]);
        $e['payment'] = \App\Core\Database::fetchOne("SELECT * FROM pagos WHERE matricula_id=?", [$id]);
        return $e;
    }
    public static function pending()
    {
        return \App\Core\Database::fetchAll("SELECT m.*, u.username, c.title as course_title, p.status as pago_status, p.proof_image_url FROM matriculas m JOIN usuarios u ON m.user_id=u.id JOIN cursos c ON m.curso_id=c.id LEFT JOIN pagos p ON p.matricula_id=m.id WHERE m.status='pending' ORDER BY m.id DESC");
    }
    public static function allWithDetails()
    {
        return \App\Core\Database::fetchAll("SELECT m.*, u.username, c.title as course_title, p.status as pago_status, p.proof_image_url FROM matriculas m JOIN usuarios u ON m.user_id=u.id JOIN cursos c ON m.curso_id=c.id LEFT JOIN pagos p ON p.matricula_id=m.id ORDER BY m.id DESC");
    }
    public static function byUser($userId)
    {
        return \App\Core\Database::fetchAll("SELECT m.*, c.title as course_title, c.image_url, c.duration, c.level, c.instructor, (SELECT COUNT(*) FROM pagos WHERE matricula_id=m.id) as has_pago FROM matriculas m JOIN cursos c ON m.curso_id=c.id WHERE m.user_id=? ORDER BY m.id DESC", [$userId]);
    }
}
