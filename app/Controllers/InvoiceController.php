<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Purchase;
use App\Core\Session;
class InvoiceController extends Controller
{
    public function download($id)
    {
        $purchase = Purchase::withDetails($id);
        if (!$purchase) { Session::setFlash('error', 'Factura no encontrada'); $this->redirectBack(); }
        $role = Session::userRole();
        if ($role === 'client' && $purchase['user_id'] != Session::userId()) {
            Session::setFlash('error', 'No tienes acceso a esta factura');
            $this->redirectBack();
        }
        $html = $this->renderInvoiceHtml($purchase);
        require_once __DIR__ . '/../Services/InvoiceService.php';
        $service = new \App\Services\InvoiceService();
        $service->generate($html, "factura_{$id}.pdf");
    }
    private function renderInvoiceHtml($purchase)
    {
        ob_start();
        ?>
        <style>
            body { font-family: 'Inter', sans-serif; padding: 40px; color: #333; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #198754; padding-bottom: 20px; }
            .header h1 { color: #198754; margin: 0; font-size: 24px; }
            .header p { margin: 5px 0; color: #666; }
            .info { margin-bottom: 20px; }
            .info table { width: 100%; }
            .info td { padding: 3px 0; }
            table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            table.items th { background: #198754; color: white; padding: 10px; text-align: left; }
            table.items td { padding: 10px; border-bottom: 1px solid #eee; }
            table.items tr:nth-child(even) { background: #f9f9f9; }
            .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; }
            .footer { text-align: center; margin-top: 40px; color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
        </style>
        <div class="header">
            <h1>Finca Bananera</h1>
            <p>NIT: 123.456.789-0</p>
            <p>Factura de Venta</p>
        </div>
        <div class="info">
            <table>
                <tr><td><strong>Factura No:</strong> <?= $purchase['id'] ?></td>
                    <td><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($purchase['created_at'])) ?></td></tr>
                <tr><td><strong>Cliente:</strong> <?= $purchase['username'] ?></td>
                    <td><strong>Email:</strong> <?= $purchase['email'] ?></td></tr>
                <tr><td><strong>Forma de pago:</strong> <?= $purchase['payment']['forma_pago'] ?? 'N/A' ?></td>
                    <td><strong>Estado:</strong> <?= $purchase['status'] ?></td></tr>
            </table>
        </div>
        <table class="items">
            <tr><th>Producto</th><th>Cant.</th><th>Precio Unit.</th><th>Subtotal</th></tr>
            <?php foreach ($purchase['items'] as $item): ?>
            <tr>
                <td><?= $item['name'] ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= '$' . number_format($item['price'], 0, ',', '.') ?></td>
                <td><?= '$' . number_format($item['quantity'] * $item['price'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div class="total">
            Total: <?= '$' . number_format($purchase['total'], 0, ',', '.') ?>
        </div>
        <div class="footer">
            <p>Gracias por su compra</p>
            <p>Esta factura se genera automÃ¡ticamente al aprobar el pago</p>
        </div>
        <?php
        return ob_get_clean();
    }
}
