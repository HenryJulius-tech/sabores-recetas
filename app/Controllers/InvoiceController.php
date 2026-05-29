<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Enrollment;
use App\Core\Session;
class InvoiceController extends Controller
{
    public function download($id)
    {
        $enrollment = Enrollment::withDetails($id);
        if (!$enrollment) { Session::setFlash('error', 'Factura no encontrada'); $this->redirectBack(); }
        $role = Session::userRole();
        if ($role === 'client' && $enrollment['user_id'] != Session::userId()) {
            Session::setFlash('error', 'No tienes acceso a esta factura');
            $this->redirectBack();
        }
        $html = $this->renderInvoiceHtml($enrollment);
        require_once __DIR__ . '/../Services/InvoiceService.php';
        $service = new \App\Services\InvoiceService();
        $service->generate($html, "factura_{$id}.pdf");
    }
    private function renderInvoiceHtml($e)
    {
        ob_start();
        ?>
        <style>
            body { font-family: 'Inter', sans-serif; padding: 40px; color: #333; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #D3545F; padding-bottom: 20px; }
            .header h1 { color: #D3545F; margin: 0; font-size: 24px; }
            .info { margin-bottom: 20px; }
            .info table { width: 100%; }
            .info td { padding: 3px 0; }
            .detail { background: #f9f5f0; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
            .detail h3 { margin: 0 0 8px 0; color: #4A2C2A; }
            .detail p { margin: 4px 0; color: #666; }
            .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; color: #D3545F; }
            .footer { text-align: center; margin-top: 40px; color: #999; font-size: 12px; }
        </style>
        <div class="header">
            <h1>Sabores & Recetas</h1>
            <p>NIT: 123.456.789-0</p>
            <p>Factura de Matrícula</p>
        </div>
        <div class="info">
            <table>
                <tr><td><strong>Factura No:</strong> <?= $e['id'] ?></td>
                    <td><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($e['created_at'])) ?></td></tr>
                <tr><td><strong>Alumno:</strong> <?= $e['username'] ?></td>
                    <td><strong>Email:</strong> <?= $e['email'] ?></td></tr>
            </table>
        </div>
        <div class="detail">
            <h3><?= $e['curso']['title'] ?? 'Curso' ?></h3>
            <p><strong>Instructor:</strong> <?= $e['curso']['instructor'] ?? 'N/A' ?></p>
            <p><strong>Modalidad:</strong> Online</p>
            <p><strong>Duración:</strong> <?= $e['curso']['duration'] ?? 'N/A' ?></p>
            <p><strong>Nivel:</strong> <?= ucfirst($e['curso']['level'] ?? 'N/A') ?></p>
        </div>
        <div class="total">Total: $<?= number_format($e['total'], 0, ',', '.') ?></div>
        <div class="footer"><p>¡Gracias por matricularte! Tu acceso al curso se activará al confirmar el pago.</p></div>
        <?php
        return ob_get_clean();
    }
}
