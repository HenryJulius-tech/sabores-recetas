<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Movement;
use App\Helpers\Security;
class MovementController extends Controller
{
    public function index()
    {
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $status = $_GET['status'] ?? '';
        $category = $_GET['category'] ?? '';
        $movements = Movement::search(compact('search','type','status','category'));
        $categories = \App\Core\Database::fetchAll("SELECT DISTINCT category FROM movimientos WHERE category!='' ORDER BY category");
        $this->view('movements.index', ['title' => 'Movimientos', 'movements' => $movements, 'categories' => $categories]);
    }
    public function store()
    {
        $data = [
            'type' => $_POST['type'],
            'amount' => (float)str_replace(['.', ','], ['', '.'], $_POST['amount']),
            'description' => $_POST['description'],
            'category' => $_POST['category'] ?? '',
            'forma_pago' => $_POST['forma_pago'] ?? '',
            'proveedor_beneficiario' => $_POST['proveedor_beneficiario'] ?? '',
            'date' => $_POST['date'] ?? date('Y-m-d'),
            'observaciones' => $_POST['observaciones'] ?? '',
            'created_by_id' => Session::userId(),
            'status' => Session::userRole() === 'admin' ? 'approved' : 'pending',
        ];
        if (!empty($_FILES['soporte']['name'])) {
            $upload = Security::validateUpload($_FILES['soporte']);
            if ($upload['valid']) {
                move_uploaded_file($_FILES['soporte']['tmp_name'], __DIR__ . '/../../uploads/' . $upload['name']);
                $data['soporte_url'] = $upload['name'];
            }
        }
        Movement::create($data);
        Session::setFlash('success', 'Movimiento creado');
        $this->redirect('movimientos');
    }
    public function approve($id)
    {
        \App\Core\Database::execute("UPDATE movimientos SET status='approved' WHERE id=?", [$id]);
        Session::setFlash('success', 'Movimiento aprobado');
        $this->redirect('movimientos');
    }
    public function reject($id)
    {
        \App\Core\Database::execute("UPDATE movimientos SET status='rejected' WHERE id=?", [$id]);
        Session::setFlash('success', 'Movimiento rechazado');
        $this->redirect('movimientos');
    }
    public function destroy($id)
    {
        Movement::delete($id);
        Session::setFlash('success', 'Movimiento eliminado');
        $this->redirect('movimientos');
    }
    public function export()
    {
        $params = array_intersect_key($_GET, array_flip(['search','type','status','category','from','to']));
        $movements = Movement::search($params);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=movimientos_' . date('Y-m-d') . '.csv');
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Tipo','Monto','Descripción','Categoría','Proveedor','Forma Pago','Fecha','Estado','Creado por']);
        foreach ($movements as $m) {
            fputcsv($out, [
                $m['id'], $m['type'], $m['amount'], $m['description'], $m['category'],
                $m['proveedor_beneficiario'], $m['forma_pago'], $m['date'], $m['status'], $m['creador']
            ]);
        }
        fclose($out);
        exit;
    }
    public function chartData()
    {
        $filter = $_GET['filter'] ?? 'diario';
        $this->json(Movement::chartData($filter));
    }
}
