<?php
namespace App\Models;
use App\Core\Database;

class Exam {
    public static function getTable() { return 'examenes'; }

    public static function findByModule($modulo_id) {
        return Database::fetchOne("SELECT * FROM examenes WHERE modulo_id = ? LIMIT 1", [$modulo_id]);
    }

    public static function find($id) {
        return Database::fetchOne("SELECT * FROM examenes WHERE id = ?", [$id]);
    }

    public static function create($data) {
        return Database::query("INSERT INTO examenes (modulo_id, title, description, passing_score, max_attempts, time_limit_min) VALUES (?, ?, ?, ?, ?, ?)", [$data['modulo_id'], $data['title'], $data['description'] ?? '', $data['passing_score'] ?? 70, $data['max_attempts'] ?? 3, $data['time_limit_min'] ?? 30]);
    }

    public static function update($id, $data) {
        return Database::query("UPDATE examenes SET title = ?, description = ?, passing_score = ?, max_attempts = ?, time_limit_min = ? WHERE id = ?", [$data['title'], $data['description'] ?? '', $data['passing_score'] ?? 70, $data['max_attempts'] ?? 3, $data['time_limit_min'] ?? 30, $id]);
    }

    public static function delete($id) {
        return Database::query("DELETE FROM examenes WHERE id = ?", [$id]);
    }

    public static function getUserAttempts($user_id, $examen_id) {
        return Database::fetchAll("SELECT * FROM intentos_examen WHERE user_id = ? AND examen_id = ? ORDER BY started_at DESC", [$user_id, $examen_id]);
    }

    public static function getUserPassedAttempts($user_id, $examen_id) {
        return (int)Database::fetchOne("SELECT COUNT(*) as cnt FROM intentos_examen WHERE user_id = ? AND examen_id = ? AND passed = 1", [$user_id, $examen_id])['cnt'];
    }
}
