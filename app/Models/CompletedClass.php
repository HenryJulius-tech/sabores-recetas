<?php
namespace App\Models;
use App\Core\Database;

class CompletedClass {
    public static function getTable() { return 'clases_completadas'; }

    public static function isCompleted($user_id, $clase_id) {
        return (int)Database::fetchOne("SELECT COUNT(*) as cnt FROM clases_completadas WHERE user_id = ? AND clase_id = ?", [$user_id, $clase_id])['cnt'] > 0;
    }

    public static function mark($user_id, $clase_id) {
        return Database::query("INSERT IGNORE INTO clases_completadas (user_id, clase_id) VALUES (?, ?)", [$user_id, $clase_id]);
    }

    public static function countByUserAndCourse($user_id, $curso_id) {
        return (int)Database::fetchOne("SELECT COUNT(*) as cnt FROM clases_completadas cc
                JOIN clases c ON cc.clase_id = c.id
                JOIN modulos m ON c.modulo_id = m.id
                WHERE cc.user_id = ? AND m.curso_id = ?", [$user_id, $curso_id])['cnt'];
    }

    public static function countByModule($user_id, $modulo_id) {
        return (int)Database::fetchOne("SELECT COUNT(*) as cnt FROM clases_completadas cc
                JOIN clases c ON cc.clase_id = c.id
                WHERE cc.user_id = ? AND c.modulo_id = ?", [$user_id, $modulo_id])['cnt'];
    }

    public static function getCompletedIdsByUserAndCourse($user_id, $curso_id) {
        $rows = Database::fetchAll("SELECT cc.clase_id FROM clases_completadas cc
                JOIN clases c ON cc.clase_id = c.id
                JOIN modulos m ON c.modulo_id = m.id
                WHERE cc.user_id = ? AND m.curso_id = ?", [$user_id, $curso_id]);
        $ids = [];
        foreach ($rows as $r) { $ids[] = $r['clase_id']; }
        return $ids;
    }

    public static function getAllByCourse($user_id, $curso_id) {
        return Database::fetchAll("SELECT cc.*, c.modulo_id, c.orden, c.title FROM clases_completadas cc
                JOIN clases c ON cc.clase_id = c.id
                JOIN modulos m ON c.modulo_id = m.id
                WHERE cc.user_id = ? AND m.curso_id = ?
                ORDER BY c.modulo_id, c.orden", [$user_id, $curso_id]);
    }
}
