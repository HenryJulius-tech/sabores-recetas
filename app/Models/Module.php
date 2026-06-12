<?php
namespace App\Models;
use App\Core\Database;

class Module {
    public static function getTable() { return 'modulos'; }

    public static function getAllByCourse($curso_id) {
        return Database::fetchAll("SELECT * FROM modulos WHERE curso_id = ? ORDER BY orden ASC", [$curso_id]);
    }

    public static function find($id) {
        return Database::fetchOne("SELECT * FROM modulos WHERE id = ?", [$id]);
    }

    public static function create($data) {
        return Database::query("INSERT INTO modulos (curso_id, title, description, orden) VALUES (?, ?, ?, ?)", [$data['curso_id'], $data['title'], $data['description'] ?? '', $data['orden'] ?? 0]);
    }

    public static function update($id, $data) {
        return Database::query("UPDATE modulos SET title = ?, description = ?, orden = ? WHERE id = ?", [$data['title'], $data['description'] ?? '', $data['orden'] ?? 0, $id]);
    }

    public static function delete($id) {
        return Database::query("DELETE FROM modulos WHERE id = ?", [$id]);
    }

    public static function getMaxOrden($curso_id) {
        return (int)Database::fetchOne("SELECT COALESCE(MAX(orden), 0) + 1 as cnt FROM modulos WHERE curso_id = ?", [$curso_id])['cnt'];
    }
}
