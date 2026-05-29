<?php
namespace App\Models;
use App\Core\Model;
class Payment extends Model
{
    protected static $table = 'pagos';
    public static function findByPurchase($compraId)
    {
        return \App\Core\Database::fetchOne("SELECT * FROM pagos WHERE compra_id=?", [$compraId]);
    }
    public static function upsert($compraId, $data)
    {
        $existing = self::findByPurchase($compraId);
        if ($existing) {
            $sets = implode('=?, ', array_keys($data)) . '=?';
            \App\Core\Database::execute("UPDATE pagos SET {$sets} WHERE compra_id=?", array_merge(array_values($data), [$compraId]));
        } else {
            $data['compra_id'] = $compraId;
            $cols = implode(',', array_keys($data));
            $vals = implode(',', array_fill(0, count($data), '?'));
            \App\Core\Database::insert("INSERT INTO pagos ({$cols}) VALUES ({$vals})", array_values($data));
        }
    }
}
