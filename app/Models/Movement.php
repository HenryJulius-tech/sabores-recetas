<?php
namespace App\Models;
use App\Core\Model;
class Movement extends Model
{
    protected static $table = 'movimientos';
    public static function create($data)
    {
        $cols = implode(',', array_keys($data));
        $vals = implode(',', array_fill(0, count($data), '?'));
        return \App\Core\Database::insert("INSERT INTO movimientos ({$cols}) VALUES ({$vals})", array_values($data));
    }
    public static function summary()
    {
        $ingresos = \App\Core\Database::fetchOne("SELECT COALESCE(SUM(amount),0) as total FROM movimientos WHERE type='ingreso' AND status='approved'")['total'];
        $gastos = \App\Core\Database::fetchOne("SELECT COALESCE(SUM(amount),0) as total FROM movimientos WHERE type='gasto' AND status='approved'")['total'];
        return ['ingresos' => $ingresos, 'gastos' => $gastos, 'balance' => $ingresos - $gastos];
    }
    public static function chartData($filter = 'diario')
    {
        switch ($filter) {
            case 'semanal': $group = "%Y-%u"; $format = "'Semana ' . date('W', strtotime(%s))"; break;
            case 'mensual': $group = "%Y-%m"; $format = "date_format(%s, '%Y-%m')"; break;
            default: $group = "%Y-%m-%d"; $format = "date_format(%s, '%d/%m/%Y')"; break;
        }
        $ingresos = \App\Core\Database::fetchAll(
            "SELECT DATE_FORMAT(date, '{$group}') as label, SUM(amount) as total FROM movimientos WHERE type='ingreso' AND status='approved' GROUP BY label ORDER BY label DESC LIMIT 15"
        );
        $gastos = \App\Core\Database::fetchAll(
            "SELECT DATE_FORMAT(date, '{$group}') as label, SUM(amount) as total FROM movimientos WHERE type='gasto' AND status='approved' GROUP BY label ORDER BY label DESC LIMIT 15"
        );
        $labels = array_unique(array_merge(array_column($ingresos,'label'), array_column($gastos,'label')));
        sort($labels);
        $dataIngresos = []; $dataGastos = [];
        foreach ($labels as $l) {
            $i = array_values(array_filter($ingresos, function($r) use ($l) { return $r['label']===$l; }));
            $g = array_values(array_filter($gastos, function($r) use ($l) { return $r['label']===$l; }));
            $dataIngresos[] = (int)($i[0]['total'] ?? 0);
            $dataGastos[] = (int)($g[0]['total'] ?? 0);
        }
        return ['labels' => $labels, 'ingresos' => $dataIngresos, 'gastos' => $dataGastos];
    }
    public static function search($params)
    {
        $sql = "SELECT m.*, u.username as creador FROM movimientos m JOIN usuarios u ON m.created_by_id=u.id WHERE 1=1";
        $binds = [];
        if (!empty($params['search'])) { $sql .= " AND (m.description LIKE ? OR m.proveedor_beneficiario LIKE ?)"; $s="%{$params['search']}%"; $binds[]=$s; $binds[]=$s; }
        if (!empty($params['type'])) { $sql .= " AND m.type=?"; $binds[]=$params['type']; }
        if (!empty($params['status'])) { $sql .= " AND m.status=?"; $binds[]=$params['status']; }
        if (!empty($params['category'])) { $sql .= " AND m.category=?"; $binds[]=$params['category']; }
        if (!empty($params['from'])) { $sql .= " AND m.date>=?"; $binds[]=$params['from']; }
        if (!empty($params['to'])) { $sql .= " AND m.date<=?"; $binds[]=$params['to']; }
        $sql .= " ORDER BY m.id DESC";
        return \App\Core\Database::fetchAll($sql, $binds);
    }
}
