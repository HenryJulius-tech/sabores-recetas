<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Helpers\Security;
class CartController extends Controller
{
    public function shop()
    {
        $cursos = Course::available();
        $categorias = \App\Models\Category::withCourseCount();
        $this->view('courses.shop', ['title' => 'Catálogo de Cursos', 'cursos' => $cursos, 'categorias' => $categorias]);
    }
    public function checkout()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['curso_id'])) {
            $this->json(['error' => 'Curso no especificado'], 400);
        }
        try {
            \App\Core\Database::beginTransaction();
            $curso = \App\Core\Database::fetchOne("SELECT * FROM cursos WHERE id=? AND status='active' FOR UPDATE", [(int)$input['curso_id']]);
            if (!$curso) throw new \Exception("Curso no encontrado");
            $matriculaId = Enrollment::create([
                'user_id' => Session::userId(),
                'curso_id' => $curso['id'],
                'total' => $curso['price'],
                'status' => 'pending',
            ]);
            \App\Core\Database::commit();
            $this->json(['success' => true, 'matricula_id' => $matriculaId]);
        } catch (\Exception $e) {
            \App\Core\Database::rollback();
            $this->json(['error' => $e->getMessage()], 400);
        }
    }
    public function myEnrollments()
    {
        $matriculas = Enrollment::byUser(Session::userId());
        $this->view('enrollments.my_enrollments', ['title' => 'Mis Matrículas', 'matriculas' => $matriculas]);
    }
    public function uploadPayment()
    {
        $matriculaId = (int)$_POST['matricula_id'];
        $formaPago = $_POST['forma_pago'] ?? '';
        $proofUrl = '';
        if (!empty($_FILES['proof']['name'])) {
            $upload = Security::validateUpload($_FILES['proof']);
            if (!$upload['valid']) { Session::setFlash('error', $upload['error']); $this->redirectBack(); }
            move_uploaded_file($_FILES['proof']['tmp_name'], __DIR__ . '/../../uploads/' . $upload['name']);
            $proofUrl = $upload['name'];
        }
        Payment::upsert($matriculaId, ['payment_method' => $formaPago, 'proof_image_url' => $proofUrl, 'status' => 'pending']);
        Session::setFlash('success', 'Comprobante enviado');
        $this->redirect('mis-matriculas');
    }
}
