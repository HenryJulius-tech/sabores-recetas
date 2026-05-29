<?php
namespace App\Models;
use App\Core\Model;
class Payment extends Model
{
    protected static $table = 'pagos';
    public static function findByMatricula($matriculaId)
    {
        return \App\Core\Database::fetchOne("SELECT * FROM pagos WHERE matricula_id=?", [$matriculaId]);
    }
    public static function upsert($matriculaId, $data)
    {
        $existing = self::findByMatricula($matriculaId);
        if ($existing) {
            $sets = implode('=?, ', array_keys($data)) . '=?';
            \App\Core\Database::execute("UPDATE pagos SET {$sets} WHERE matricula_id=?", array_merge(array_values($data), [$matriculaId]));
        } else {
            $data['matricula_id'] = $matriculaId;
            $cols = implode(',', array_keys($data));
            $vals = implode(',', array_fill(0, count($data), '?'));
            \App\Core\Database::insert("INSERT INTO pagos ({$cols}) VALUES ({$vals})", array_values($data));
        }
    }
}
