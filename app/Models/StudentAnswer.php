<?php
namespace App\Models;
use App\Core\Database;

class StudentAnswer {
    public static function getTable() { return 'respuestas_alumno'; }

    public static function getAllByAttempt($intento_id) {
        return Database::fetchAll("SELECT * FROM respuestas_alumno WHERE intento_id = ?", [$intento_id]);
    }

    public static function save($data) {
        return Database::query("INSERT INTO respuestas_alumno (intento_id, pregunta_id, answer, is_correct, points_earned) VALUES (?, ?, ?, ?, ?)", [$data['intento_id'], $data['pregunta_id'], $data['answer'], $data['is_correct'] ? 1 : 0, $data['points_earned'] ?? 0]);
    }

    public static function countCorrectByAttempt($intento_id) {
        return (int)Database::fetchOne("SELECT COUNT(*) as cnt FROM respuestas_alumno WHERE intento_id = ? AND is_correct = 1", [$intento_id])['cnt'];
    }
}
