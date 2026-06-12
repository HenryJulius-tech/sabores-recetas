<?php
namespace App\Models;
use App\Core\Database;

class ClassModel {
    public static function getTable() { return 'clases'; }

    public static function getAllByModule($modulo_id) {
        return Database::fetchAll("SELECT * FROM clases WHERE modulo_id = ? ORDER BY orden ASC", [$modulo_id]);
    }

    public static function find($id) {
        return Database::fetchOne("SELECT * FROM clases WHERE id = ?", [$id]);
    }

    public static function create($data) {
        return Database::query("INSERT INTO clases (modulo_id, title, description, video_url, video_type, duration, orden) VALUES (?, ?, ?, ?, ?, ?, ?)", [$data['modulo_id'], $data['title'], $data['description'] ?? '', $data['video_url'], $data['video_type'] ?? 'youtube', $data['duration'] ?? '', $data['orden'] ?? 0]);
    }

    public static function update($id, $data) {
        return Database::query("UPDATE clases SET title = ?, description = ?, video_url = ?, video_type = ?, duration = ?, orden = ? WHERE id = ?", [$data['title'], $data['description'] ?? '', $data['video_url'], $data['video_type'] ?? 'youtube', $data['duration'] ?? '', $data['orden'] ?? 0, $id]);
    }

    public static function delete($id) {
        return Database::query("DELETE FROM clases WHERE id = ?", [$id]);
    }

    public static function getMaxOrden($modulo_id) {
        return (int)Database::fetchOne("SELECT COALESCE(MAX(orden), 0) + 1 as cnt FROM clases WHERE modulo_id = ?", [$modulo_id])['cnt'];
    }

    public static function countByModule($modulo_id) {
        return (int)Database::fetchOne("SELECT COUNT(*) as cnt FROM clases WHERE modulo_id = ?", [$modulo_id])['cnt'];
    }

    public static function getPreviousClass($modulo_id, $orden) {
        return Database::fetchOne("SELECT * FROM clases WHERE modulo_id = ? AND orden < ? ORDER BY orden DESC LIMIT 1", [$modulo_id, $orden]);
    }

    public static function getFirstByModule($modulo_id) {
        return Database::fetchOne("SELECT * FROM clases WHERE modulo_id = ? ORDER BY orden ASC LIMIT 1", [$modulo_id]);
    }
}
