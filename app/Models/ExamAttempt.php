<?php
namespace App\Models;
use App\Core\Database;

class ExamAttempt {
    public static function getTable() { return 'intentos_examen'; }

    public static function find($id) {
        return Database::fetchOne("SELECT * FROM intentos_examen WHERE id = ?", [$id]);
    }

    public static function create($data) {
        Database::query("INSERT INTO intentos_examen (user_id, examen_id) VALUES (?, ?)", [$data['user_id'], $data['examen_id']]);
        return Database::getInstance()->getConnection()->lastInsertId();
    }

    public static function complete($id, $score, $passed) {
        return Database::query("UPDATE intentos_examen SET score = ?, passed = ?, completed_at = NOW() WHERE id = ?", [$score, $passed, $id]);
    }

    public static function countByUserAndExam($user_id, $examen_id) {
        return (int)Database::fetchOne("SELECT COUNT(*) as cnt FROM intentos_examen WHERE user_id = ? AND examen_id = ?", [$user_id, $examen_id])['cnt'];
    }

    public static function getLastByUserAndExam($user_id, $examen_id) {
        return Database::fetchOne("SELECT * FROM intentos_examen WHERE user_id = ? AND examen_id = ? ORDER BY started_at DESC LIMIT 1", [$user_id, $examen_id]);
    }

    public static function hasPassed($user_id, $examen_id) {
        return (int)Database::fetchOne("SELECT COUNT(*) as cnt FROM intentos_examen WHERE user_id = ? AND examen_id = ? AND passed = 1", [$user_id, $examen_id])['cnt'] > 0;
    }
}
