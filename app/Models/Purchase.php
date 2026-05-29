<?php
namespace App\Models;
use App\Core\Model;
class Purchase extends Model
{
    protected static $table = 'compras';
    public static function create($data)
    {
        $cols = implode(',', array_keys($data));
        $vals = implode(',', array_fill(0, count($data), '?'));
        return \App\Core\Database::insert("INSERT INTO compras ({$cols}) VALUES ({$vals})", array_values($data));
    }
    public static function withDetails($id)
    {
        $purchase = \App\Core\Database::fetchOne("SELECT c.*, u.username, u.email FROM compras c JOIN usuarios u ON c.user_id=u.id WHERE c.id=?", [$id]);
        if (!$purchase) return null;
        $purchase['items'] = \App\Core\Database::fetchAll(
            "SELECT d.*, p.name, p.image_url FROM detalle_compras d JOIN productos p ON d.product_id=p.id WHERE d.compra_id=?", [$id]
        );
        $purchase['payment'] = \App\Core\Database::fetchOne("SELECT * FROM pagos WHERE compra_id=?", [$id]);
        return $purchase;
    }
    public static function pending()
    {
        return \App\Core\Database::fetchAll("SELECT c.*, u.username FROM compras c JOIN usuarios u ON c.user_id=u.id WHERE c.status='pending' ORDER BY c.id DESC");
    }
    public static function byUser($userId)
    {
        return \App\Core\Database::fetchAll("SELECT c.*, (SELECT COUNT(*) FROM pagos WHERE compra_id=c.id) as has_pago FROM compras c WHERE c.user_id=? ORDER BY c.id DESC", [$userId]);
    }
}
