<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\AuditLog;
use App\Helpers\Security;
class CartController extends Controller
{
    public function shop()
    {
        $catId = $_GET['category'] ?? '';
        $cursos = $catId ? Course::findByCategory((int)$catId) : Course::available();
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
    public function showEnrollmentForm($cursoId)
    {
        $cursoId = (int)$cursoId;
        $curso = Course::findWithCategory($cursoId);
        if (!$curso) {
            Session::setFlash('error', 'Curso no encontrado');
            $this->redirect('catalogo');
        }
        $user = \App\Core\Database::fetchOne("SELECT * FROM usuarios WHERE id=?", [Session::userId()]);
        $this->view('enrollments.enrollment_form', [
            'title' => 'Inscripción: ' . $curso['title'],
            'curso' => $curso,
            'user' => $user,
            'payment_methods' => [
                'transferencia' => 'Transferencia Bancaria',
                'tarjeta' => 'Tarjeta de Crédito/Débito',
                'paypal' => 'PayPal',
                'cash' => 'Pago en Efectivo',
            ]
        ]);
    }
    public function processEnrollment()
    {
        $cursoId = (int)($_POST['curso_id'] ?? 0);
        $paymentMethod = $_POST['payment_method'] ?? '';
        $cursoId = (int)$cursoId;
        $curso = Course::findWithCategory($cursoId);
        if (!$curso) {
            Session::setFlash('error', 'Curso no encontrado');
            $this->redirect('catalogo');
        }
        try {
            \App\Core\Database::beginTransaction();
            $curso = \App\Core\Database::fetchOne("SELECT * FROM cursos WHERE id=? AND status='active' FOR UPDATE", [$cursoId]);
            if (!$curso) throw new \Exception("Curso no encontrado o no disponible");
            $matriculaId = Enrollment::create([
                'user_id' => Session::userId(),
                'curso_id' => $curso['id'],
                'total' => $curso['price'],
                'status' => 'pending',
            ]);
            $proofUrl = '';
            if ($paymentMethod === 'transferencia' || $paymentMethod === 'cash') {
                if (!empty($_FILES['proof']['name'])) {
                    $upload = Security::validateUpload($_FILES['proof']);
                    if (!$upload['valid']) throw new \Exception($upload['error']);
                    move_uploaded_file($_FILES['proof']['tmp_name'], __DIR__ . '/../../public/uploads/payments/' . $upload['name']);
                    $proofUrl = 'payments/' . $upload['name'];
                }
            }
            Payment::upsert($matriculaId, [
                'payment_method' => $paymentMethod,
                'proof_image_url' => $proofUrl,
                'status' => 'pending'
            ]);
            \App\Core\Database::commit();
            $username = Session::username();
            AuditLog::log('Nueva inscripción', $username . ' se inscribió en: ' . $curso['title'] . ' - Matrícula #' . $matriculaId);
            Notification::notifyAdmins('enrollment', 'Nueva matrícula', $username . ' se inscribió en: ' . $curso['title'], 'admin/enrollments');
            Session::setFlash('success', '¡Inscripción completada! Por favor completa el pago.');
            $this->redirect('mis-matriculas');
        } catch (\Exception $e) {
            \App\Core\Database::rollback();
            Session::setFlash('error', 'Error: ' . $e->getMessage());
            $this->redirectBack();
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
            move_uploaded_file($_FILES['proof']['tmp_name'], __DIR__ . '/../../public/uploads/payments/' . $upload['name']);
            $proofUrl = 'payments/' . $upload['name'];
        }
        Payment::upsert($matriculaId, ['payment_method' => $formaPago, 'proof_image_url' => $proofUrl, 'status' => 'pending']);
        $username = Session::username();
        AuditLog::log('Comprobante de pago', $username . ' subió comprobante para matrícula #' . $matriculaId);
        Notification::notifyAdmins('payment', 'Comprobante de pago', $username . ' subió un comprobante de pago', 'admin/enrollments');
        Session::setFlash('success', 'Comprobante enviado');
        $this->redirect('mis-matriculas');
    }
}
