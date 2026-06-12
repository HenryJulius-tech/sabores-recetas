<?php
namespace App\Models;
use App\Core\Database;

class Question {
    public static function getTable() { return 'preguntas'; }

    public static function getAllByExam($examen_id) {
        return Database::fetchAll("SELECT * FROM preguntas WHERE examen_id = ? ORDER BY orden ASC", [$examen_id]);
    }

    public static function find($id) {
        return Database::fetchOne("SELECT * FROM preguntas WHERE id = ?", [$id]);
    }

    public static function create($data) {
        return Database::query("INSERT INTO preguntas (examen_id, question, type, options, correct_answer, points, orden) VALUES (?, ?, ?, ?, ?, ?, ?)", [$data['examen_id'], $data['question'], $data['type'] ?? 'multiple_choice', json_encode($data['options']), $data['correct_answer'], $data['points'] ?? 10, $data['orden'] ?? 0]);
    }

    public static function update($id, $data) {
        return Database::query("UPDATE preguntas SET question = ?, options = ?, correct_answer = ?, points = ?, orden = ? WHERE id = ?", [$data['question'], json_encode($data['options']), $data['correct_answer'], $data['points'] ?? 10, $data['orden'] ?? 0, $id]);
    }

    public static function delete($id) {
        return Database::query("DELETE FROM preguntas WHERE id = ?", [$id]);
    }

    public static function countByExam($examen_id) {
        return (int)Database::fetchOne("SELECT COUNT(*) as cnt FROM preguntas WHERE examen_id = ?", [$examen_id])['cnt'];
    }
}
