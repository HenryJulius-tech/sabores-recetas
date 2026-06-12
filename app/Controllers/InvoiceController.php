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

    public function preview($id)
    {
        $enrollment = Enrollment::withDetails($id);
        if (!$enrollment) { Session::setFlash('error', 'Factura no encontrada'); $this->redirectBack(); }
        $role = Session::userRole();
        if ($role === 'client' && $enrollment['user_id'] != Session::userId()) {
            Session::setFlash('error', 'No tienes acceso a esta factura');
            $this->redirectBack();
        }
        $html = $this->renderInvoiceHtml($enrollment);
        $title = 'Factura #' . $id;
        $this->view('invoice', ['title' => $title, 'html' => $html]);
    }

    private function renderInvoiceHtml($e)
    {
        ob_start();
        $total = $e['total'] ?? 0;
        $paymentMethod = $e['payment']['payment_method'] ?? 'N/A';
        $paymentStatus = $e['payment']['status'] ?? 'pending';
        $statusMap = ['approved' => 'Pagado', 'pending' => 'Pendiente', 'rejected' => 'Rechazado'];
        $statusLabel = $statusMap[$paymentStatus] ?? $paymentStatus;
        $date = date('d/m/Y', strtotime($e['created_at']));
        $dueDate = date('d/m/Y', strtotime('+15 days', strtotime($e['created_at'])));
        ?>
        <style>
            @page { margin: 20mm 15mm; }
            body { font-family: 'Inter', 'Segoe UI', sans-serif; color: #1e293b; margin: 0; padding: 0; }
            .invoice { max-width: 800px; margin: 0 auto; padding: 0; }
            .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 3px solid #E11D48; }
            .header-left h1 { font-size: 28px; color: #E11D48; margin: 0 0 4px 0; font-weight: 800; letter-spacing: -0.5px; }
            .header-left p { margin: 2px 0; color: #64748b; font-size: 13px; }
            .header-right { text-align: right; }
            .header-right h2 { margin: 0 0 4px 0; font-size: 22px; color: #1e293b; font-weight: 700; }
            .header-right p { margin: 2px 0; color: #64748b; font-size: 13px; }
            .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-top: 8px; }
            .badge-paid { background: #dcfce7; color: #166534; }
            .badge-pending { background: #fef3c7; color: #92400e; }
            .badge-rejected { background: #fee2e2; color: #991b1b; }
            .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
            .info-box { background: #f8fafc; border-radius: 8px; padding: 16px; border: 1px solid #e2e8f0; }
            .info-box h4 { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin: 0 0 8px 0; font-weight: 600; }
            .info-box p { margin: 3px 0; font-size: 14px; color: #1e293b; }
            .info-box p small { color: #64748b; }
            table.detail { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
            table.detail th { background: #f1f5f9; text-align: left; padding: 10px 12px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
            table.detail td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
            table.detail .amount { text-align: right; font-weight: 600; }
            .totals { margin-left: auto; width: 300px; }
            .totals table { width: 100%; }
            .totals td { padding: 6px 12px; font-size: 14px; }
            .totals .total-row td { font-weight: 700; font-size: 18px; color: #E11D48; border-top: 2px solid #e2e8f0; padding-top: 10px; }
            .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 12px; }
            .footer p { margin: 2px 0; }
            .print-btn { display: block; text-align: center; margin: 24px 0; }
            .print-btn button { padding: 12px 32px; background: #E11D48; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; }
            .print-btn button:hover { background: #BE123C; }
            @media print { .print-btn { display: none; } body { padding: 0; } .invoice { max-width: 100%; } }
        </style>
        <div class="invoice">
            <div class="header">
                <div class="header-left">
                    <h1>Sabores & Recetas</h1>
                    <p><strong>NIT:</strong> 123.456.789-0</p>
                    <p><strong>Dirección:</strong> Cra 7 # 12-34, Bogotá, Colombia</p>
                    <p><strong>Teléfono:</strong> +57 (1) 234 5678</p>
                    <p><strong>Email:</strong> info@saboresyrecetas.com</p>
                </div>
                <div class="header-right">
                    <h2>FACTURA</h2>
                    <p><strong>No.</strong> <?= $e['id'] ?></p>
                    <p><strong>Fecha:</strong> <?= $date ?></p>
                    <p><strong>Vencimiento:</strong> <?= $dueDate ?></p>
                    <span class="badge badge-<?= $paymentStatus ?>"><?= $statusLabel ?></span>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <h4>Cliente</h4>
                    <p><strong><?= e($e['username']) ?></strong></p>
                    <p><small><?= e($e['email']) ?></small></p>
                    <?php if (!empty($e['curso']['instructor'])): ?>
                    <p><small>Instructor: <?= e($e['curso']['instructor']) ?></small></p>
                    <?php endif; ?>
                </div>
                <div class="info-box">
                    <h4>Detalle del Pago</h4>
                    <p><strong>Método:</strong> <?= e($paymentMethod) ?></p>
                    <p><strong>Estado:</strong> <?= $statusLabel ?></p>
                    <p><strong>Matrícula:</strong> #<?= $e['id'] ?></p>
                </div>
            </div>

            <table class="detail">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Categoría</th>
                        <th>Duración</th>
                        <th>Nivel</th>
                        <th class="amount">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?= e($e['curso']['title'] ?? 'Curso') ?></strong></td>
                        <td><?= e($e['curso']['category_name'] ?? 'General') ?></td>
                        <td><?= e($e['curso']['duration'] ?? 'N/A') ?></td>
                        <td><?= ucfirst(e($e['curso']['level'] ?? 'N/A')) ?></td>
                        <td class="amount">$<?= number_format($total, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="totals">
                <table>
                    <tr><td>Subtotal</td><td class="amount">$<?= number_format($total, 0, ',', '.') ?></td></tr>
                    <tr><td>IVA (0%)</td><td class="amount">$0</td></tr>
                    <tr class="total-row"><td>Total</td><td class="amount">$<?= number_format($total, 0, ',', '.') ?></td></tr>
                </table>
            </div>

            <div class="footer">
                <p>Sabores & Recetas — Escuela de Gastronomía</p>
                <p>¡Gracias por matricularte! Tu acceso se activará al confirmar el pago.</p>
                <p><small>Esta factura se genera automáticamente y es válida como comprobante de inscripción.</small></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
