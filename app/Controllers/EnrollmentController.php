<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Enrollment;
use App\Models\Movement;
use App\Models\Notification;
use App\Models\AuditLog;
use App\Core\Database;
class EnrollmentController extends Controller
{
    public function pending()
    {
        $matriculas = Enrollment::pending();
        $this->view('enrollments.admin_approval', ['title' => 'Matrículas Pendientes', 'matriculas' => $matriculas]);
    }
    public function approve($id)
    {
        try {
            \App\Core\Database::execute("UPDATE matriculas SET status='approved' WHERE id=?", [$id]);
            $m = \App\Core\Database::fetchOne("SELECT * FROM matriculas WHERE id=?", [$id]);
            if ($m) {
                Movement::create([
                    'matricula_id' => $id,
                    'type' => 'ingreso',
                    'description' => 'Matrícula #' . $id,
                    'amount' => $m['total'],
                    'created_by_id' => Session::userId(),
                ]);
                \App\Core\Database::execute("UPDATE pagos SET status='approved' WHERE matricula_id=?", [$id]);
                AuditLog::log('Matrícula aprobada', 'Matrícula #' . $id . ' - Usuario: ' . $m['user_id'] . ' - Curso: ' . ($m['curso_id'] ?? ''));
                Notification::create($m['user_id'], 'enrollment_approved', 'Matrícula aprobada', 'Tu matrícula ha sido aprobada. ¡Ya puedes acceder al curso!', 'mis-matriculas');
                Notification::create($m['user_id'], 'payment_approved', 'Pago aprobado', 'Tu pago por la matrícula ha sido aprobado.', 'mis-matriculas');
            }
            Session::setFlash('success', 'Matrícula aprobada');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Error al aprobar: ' . $e->getMessage());
        }
        $this->redirect('admin/enrollments');
    }
    public function reject($id)
    {
        try {
            $m = \App\Core\Database::fetchOne("SELECT * FROM matriculas WHERE id=?", [$id]);
            \App\Core\Database::execute("UPDATE matriculas SET status='rejected' WHERE id=?", [$id]);
            if ($m) {
                \App\Core\Database::execute("UPDATE pagos SET status='rejected' WHERE matricula_id=?", [$id]);
                AuditLog::log('Matrícula rechazada', 'Matrícula #' . $id . ' - Usuario: ' . $m['user_id']);
                Notification::create($m['user_id'], 'enrollment_rejected', 'Matrícula rechazada', 'Tu matrícula ha sido rechazada. Contacta al administrador.', 'catalogo');
                Notification::create($m['user_id'], 'payment_rejected', 'Pago rechazado', 'Tu pago por la matrícula ha sido rechazado.', 'catalogo');
            }
            Session::setFlash('info', 'Matrícula rechazada');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Error al rechazar: ' . $e->getMessage());
        }
        $this->redirect('admin/enrollments');
    }
    public function all()
    {
        $matriculas = Enrollment::allWithDetails();
        $this->view('enrollments.admin_approval', [
            'title' => 'Todas las Matrículas',
            'matriculas' => $matriculas,
            'showAll' => true,
        ]);
    }
    public function cancel($id)
    {
        try {
            $m = Database::fetchOne("SELECT * FROM matriculas WHERE id=?", [$id]);
            if (!$m) { Session::setFlash('error', 'Matrícula no encontrada'); $this->redirect('admin/enrollments/todas'); }
            Database::execute("UPDATE matriculas SET status='cancelled' WHERE id=?", [$id]);
            Database::execute("UPDATE pagos SET status='cancelled' WHERE matricula_id=?", [$id]);
            Database::execute("DELETE FROM clases_completadas WHERE user_id=? AND clase_id IN (SELECT id FROM clases WHERE modulo_id IN (SELECT id FROM modulos WHERE curso_id=?))", [$m['user_id'], $m['curso_id']]);
            Database::execute("DELETE FROM respuestas_alumno WHERE intento_id IN (SELECT id FROM intentos_examen WHERE user_id=? AND examen_id IN (SELECT id FROM examenes WHERE modulo_id IN (SELECT id FROM modulos WHERE curso_id=?)))", [$m['user_id'], $m['curso_id']]);
            Database::execute("DELETE FROM intentos_examen WHERE user_id=? AND examen_id IN (SELECT id FROM examenes WHERE modulo_id IN (SELECT id FROM modulos WHERE curso_id=?))", [$m['user_id'], $m['curso_id']]);
            AuditLog::log('Matrícula cancelada', 'Matrícula #' . $id . ' - Usuario: ' . $m['user_id'] . ' - Curso: ' . ($m['curso_id'] ?? ''));
            Notification::create($m['user_id'], 'enrollment_cancelled', 'Matrícula cancelada', 'Tu matrícula ha sido cancelada por el administrador.', 'catalogo');
            Session::setFlash('success', 'Matrícula #' . $id . ' cancelada. Progreso eliminado.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Error al cancelar: ' . $e->getMessage());
        }
        $this->redirect('admin/enrollments/todas');
    }
}
