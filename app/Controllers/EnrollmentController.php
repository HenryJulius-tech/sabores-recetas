<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Enrollment;
use App\Models\Movement;
class EnrollmentController extends Controller
{
    public function pending()
    {
        $matriculas = Enrollment::pending();
        $this->view('enrollments.admin_approval', ['title' => 'Matrículas Pendientes', 'matriculas' => $matriculas]);
    }
    public function approve($id)
    {
        \App\Core\Database::execute("UPDATE matriculas SET status='approved' WHERE id=?", [$id]);
        $m = \App\Core\Database::fetchOne("SELECT * FROM matriculas WHERE id=?", [$id]);
        if ($m) {
            Movement::create([
                'matricula_id' => $id,
                'type' => 'ingreso',
                'description' => 'Matrícula #' . $id,
                'amount' => $m['total'],
                'user_id' => Session::userId(),
            ]);
        }
        Session::setFlash('success', 'Matrícula aprobada');
        $this->redirect('admin/enrollments');
    }
    public function reject($id)
    {
        \App\Core\Database::execute("UPDATE matriculas SET status='rejected' WHERE id=?", [$id]);
        Session::setFlash('info', 'Matrícula rechazada');
        $this->redirect('admin/enrollments');
    }
}
