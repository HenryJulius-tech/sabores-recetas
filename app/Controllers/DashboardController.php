<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Database;
use App\Models\User;
use App\Models\Course;
class DashboardController extends Controller
{
    public function index()
    {
        $total_cursos = Course::count();
        $total_usuarios = User::count();
        $matriculas_pendientes = Database::fetchOne("SELECT COUNT(*) as c FROM matriculas WHERE status='pending'")['c'] ?? 0;
        $total_ingresos = Database::fetchOne("SELECT COALESCE(SUM(total),0) as s FROM matriculas WHERE status='approved'")['s'] ?? 0;
        $total_gastos = Database::fetchOne("SELECT COALESCE(SUM(amount),0) as s FROM movimientos WHERE type='gasto'")['s'] ?? 0;
        $balance = $total_ingresos - $total_gastos;

        $recent = Database::fetchAll("SELECT m.*, u.username as creador FROM movimientos m LEFT JOIN usuarios u ON m.created_by_id=u.id ORDER BY m.id DESC LIMIT 10");

        // Cursos activos / inactivos
        $cursos_activos = Database::fetchOne("SELECT COUNT(*) as c FROM cursos WHERE status='active'")['c'] ?? 0;
        $cursos_inactivos = Database::fetchOne("SELECT COUNT(*) as c FROM cursos WHERE status!='active'")['c'] ?? 0;
        $cursos_destacados = Database::fetchOne("SELECT COUNT(*) as c FROM cursos WHERE featured=1 AND status='active'")['c'] ?? 0;

        // Total matriculas aprobadas y rechazadas
        $matriculas_aprobadas = Database::fetchOne("SELECT COUNT(*) as c FROM matriculas WHERE status='approved'")['c'] ?? 0;
        $matriculas_rechazadas = Database::fetchOne("SELECT COUNT(*) as c FROM matriculas WHERE status='rejected'")['c'] ?? 0;

        // Cursos por categoria (para gráfico)
        $cursos_por_categoria = Database::fetchAll("SELECT cat.name, COUNT(c.id) as total FROM categorias cat LEFT JOIN cursos c ON c.category_id=cat.id AND c.status='active' GROUP BY cat.id, cat.name ORDER BY total DESC");

        // Top 5 cursos mas vendidos (por matrículas aprobadas)
        $top_cursos = Database::fetchAll("SELECT c.id, c.title, c.price, c.image_url, c.level, c.instructor, COUNT(m.id) as matriculas, COALESCE(SUM(m.total),0) as total_vendido FROM cursos c LEFT JOIN matriculas m ON m.curso_id=c.id AND m.status='approved' GROUP BY c.id ORDER BY matriculas DESC LIMIT 5");

        // Últimas 5 matrículas
        $recent_enrollments = Database::fetchAll("SELECT m.*, u.username, c.title as course_title FROM matriculas m JOIN usuarios u ON m.user_id=u.id JOIN cursos c ON m.curso_id=c.id ORDER BY m.id DESC LIMIT 5");

        $this->view('dashboard.index', [
            'title' => 'Inicio',
            'total_ingresos' => $total_ingresos,
            'total_gastos' => $total_gastos,
            'balance' => $balance,
            'compras_pendientes' => $matriculas_pendientes,
            'total_productos' => $total_cursos,
            'total_usuarios' => $total_usuarios,
            'recent_movements' => $recent,
            'cursos_activos' => $cursos_activos,
            'cursos_inactivos' => $cursos_inactivos,
            'cursos_destacados' => $cursos_destacados,
            'matriculas_aprobadas' => $matriculas_aprobadas,
            'matriculas_rechazadas' => $matriculas_rechazadas,
            'cursos_por_categoria' => $cursos_por_categoria,
            'top_cursos' => $top_cursos,
            'recent_enrollments' => $recent_enrollments,
        ]);
    }
}
