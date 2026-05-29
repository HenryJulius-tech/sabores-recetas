<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Movement;
class DashboardController extends Controller
{
    public function index()
    {
        $summary = Movement::summary();
        $data = [
            'title' => 'Dashboard',
            'total_ingresos' => $summary['ingresos'],
            'total_gastos' => $summary['gastos'],
            'balance' => $summary['balance'],
            'total_productos' => Product::count(),
            'total_usuarios' => User::count(),
            'compras_pendientes' => \App\Core\Database::fetchOne("SELECT COUNT(*) as c FROM compras WHERE status='pending'")['c'],
            'recent_movements' => \App\Core\Database::fetchAll(
                "SELECT m.*, u.username as creador FROM movimientos m JOIN usuarios u ON m.created_by_id=u.id ORDER BY m.id DESC LIMIT 5"
            ),
        ];
        $this->view('dashboard.index', $data);
    }
}
