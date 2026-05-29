<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\User;
use App\Models\Course;
class DashboardController extends Controller
{
    public function index()
    {
        $total_cursos = Course::count();
        $total_usuarios = User::count();
        $matriculas_pendientes = \App\Core\Database::fetchOne("SELECT COUNT(*) as c FROM matriculas WHERE status='pending'")['c'] ?? 0;
        $total_ingresos = \App\Core\Database::fetchOne("SELECT COALESCE(SUM(total),0) as s FROM matriculas WHERE status='approved'")['s'] ?? 0;
        $total_gastos = \App\Core\Database::fetchOne("SELECT COALESCE(SUM(amount),0) as s FROM movimientos WHERE type='gasto'")['s'] ?? 0;
        $balance = $total_ingresos - $total_gastos;
        $recent = \App\Core\Database::fetchAll("SELECT m.*, u.username as creador FROM movimientos m LEFT JOIN usuarios u ON m.created_by_id=u.id ORDER BY m.id DESC LIMIT 10");
        $this->view('dashboard.index', [
            'title' => 'Dashboard', 'total_ingresos' => $total_ingresos,
            'total_gastos' => $total_gastos, 'balance' => $balance,
            'compras_pendientes' => $matriculas_pendientes,
            'total_productos' => $total_cursos,
            'total_usuarios' => $total_usuarios,
            'recent_movements' => $recent
        ]);
    }
}
